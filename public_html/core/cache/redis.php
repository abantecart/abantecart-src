<?php

/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2026 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

//include abstract cache storage driver class
include_once('driver.php');

/**
 * Memcached driver
 *
 * NOTE: to use this driver, put lines belong into your system/config.php
 * NOTE: Redis php-extension required!
 * const CACHE_DRIVER = 'redis';
 * const CACHE_HOST = 'localhost';
 * const CACHE_PORT = 6379;
 * const CACHE_PASSWORD = 'redis-password';
 * const CACHE_CONNECT_TIMEOUT = 10;
 * const CACHE_SECRET = 'your_secret_key';
 * const CACHE_PERSISTENT_ID = 'some-unique-id';
 *
 * @since  1.3.3
 */
class ACacheDriverRedis extends ACacheDriver
{

    protected $hostname = CACHE_HOST;
    protected $port = CACHE_PORT;
    protected $password = CACHE_PASSWORD;
    protected $timeout = CACHE_CONNECT_TIMEOUT;
    protected $secret = CACHE_SECRET;
    protected $persistentId = CACHE_PERSISTENT_ID;

    /**
     * @var $connect - Redis connection object
     */
    protected $connect;

    /**
     * Locks currently held by this process, keyed by a Redis lock key.
     * Each entry is ['token' => string, 'expires' => float].
     *
     * @var array
     */
    protected $lock_tokens = [];

    /**
     * Constructor
     *
     * @param int $expiration
     * @param int $lock_time
     *
     * @throws AException
     * @since   1.3.3
     */
    public function __construct($expiration, $lock_time = 0)
    {
        if (!$lock_time) {
            $lock_time = 10;
        }
        parent::__construct($expiration, $lock_time);

        // Create the memcache connection
        if (!class_exists('\Redis')) {
            throw new AException(AC_ERR_LOAD, 'Error: Redis php library not installed on server.');
        }
        $this->connect = new \Redis();

        $test = $this->connect->pconnect($this->hostname, $this->port, $this->timeout, $this->persistentId);
        if (!$test) {
            throw new AException(AC_ERR_LOAD, 'Error: Could not connect to Redis server.');
        }
        // AUTH against a server without "requirepass" makes "phpredis" throw, so only
        // authenticate when a password is actually configured.
        if ((string) $this->password !== '') {
            $this->connect->auth($this->password);
        }
    }

    /**
     * @return  boolean
     * @since   1.2.7
     */
    public function isSupported()
    {
        if (!(extension_loaded('redis') && class_exists('Redis'))) {
            return false;
        }

        return true;
    }

    /**
     * Get cached data by key and group
     *
     * @param string $key The cache data key
     * @param string $group The cache data group
     * @param boolean $check_expire True to verify cache time expiration
     *
     * @return  mixed|false Boolean false on failure or a cached data string
     *
     * @since   1.3.3
     */
    public function get($key, $group, $check_expire = true)
    {
        $cache_id = $this->_getCacheId($key, $group);
        $data = $this->connect->get($cache_id);
        // phpredis returns false for a missing key; json_decode(false) becomes null.
        // ACache::lock() treats "not false" as "lock held" and busy-waits the full timeout.
        if ($data === false) {
            return false;
        }
        return json_decode($data, true);
    }

    /**
     * Save data to a file by key and group
     *
     * @param string $key The cache data key
     * @param string $group The cache data group
     * @param string $data The data to store in cache
     *
     * @return  boolean
     *
     * @since   1.2.7
     */
    public function put($key, $group, $data)
    {
        $cache_id = $this->_getCacheId($key, $group);
        $ttl = $this->expire;
        // Set the value and its TTL in a single command. SET followed by EXPIRE left
        // the key without expiration in between, so a crash in that window turned the
        // entry into a permanent one.
        $options = $ttl > 0 ? ['ex' => $ttl] : [];
        return (bool) $this->connect->set($cache_id, json_encode($data), $options);
    }

    /**
     * Remove a cached data file by key and group
     *
     * @param string $key The cache data key
     * @param string $group The cache data group
     *
     * @return  boolean
     * @since   1.2.7
     */
    public function remove($key, $group)
    {
        $cache_id = $this->_getCacheId($key, $group);
        return $this->connect->del($cache_id);
    }

    /**
     * Clean cache for a group provided.
     *
     * @param string $group The cache data group, passed '*' indicate all cache removal
     *
     * @return  boolean
     *
     * @since   1.2.7
     */
    public function clean($group)
    {
        $group = trim($group);
        if (!$group) {
            return false;
        }

        if ($group == '*') {
            // flushDb, not flushAll: flushAll wipes every database of the instance,
            // including keys that belong to other applications. It also used to run
            // once per matched key instead of once in total.
            return (bool) $this->connect->flushDb();
        }

        // SCAN instead of KEYS - KEYS walks the whole keyspace and blocks the server.
        // Cache ids are built as "<secret>.<group>.<hash>" by _getCacheId(), so the
        // group prefix selects the group's entries and its "_lock" keys.
        $pattern = $this->secret . '.' . $group . '.*';
        $this->connect->setOption(\Redis::OPT_SCAN, \Redis::SCAN_RETRY);
        $iterator = null;
        while ($keys = $this->connect->scan($iterator, $pattern, 500)) {
            $this->connect->del($keys);
        }

        return true;
    }

    /**
     * Delete expired cache data
     *
     * @return  boolean  True on success, false otherwise.
     *
     * @since   1.3.3
     */
    public function gc()
    {
        return false;
    }

    /**
     * Lock the cached item with atomic SET NX PX (do not GET + sleep).
     *
     * @param string $key The cache data key
     * @param string $group The cache data group
     * @param int $locktime Cached item max lock time in seconds
     *
     * @return array
     *
     * @since 1.3.3
     */
    public function lock($key, $group, $locktime)
    {
        $output = [];
        $output['waited'] = false;

        $lock_id = $this->_getCacheId($key, $group) . '_lock';
        $ttl_ms = max(1, (int) $locktime) * 1000;

        // Re-entrant for the holder. ACache::pull() takes the lock on a cache miss and
        // leaves releasing it to the ACache::push() that follows, so without this the
        // process would block on a lock it owns itself and time out.
        if (isset($this->lock_tokens[$lock_id])
            && $this->lock_tokens[$lock_id]['expires'] > microtime(true)
        ) {
            $output['locked'] = true;
            return $output;
        }
        unset($this->lock_tokens[$lock_id]);

        $token = $this->_lockToken();
        $data_lock = $this->connect->set($lock_id, $token, ['nx', 'px' => $ttl_ms]);

        if (!$data_lock) {
            /* Another process holds the lock. Report the wait right away: ACache::pull()
             re-reads the cache only when lock() comes back both locked and waited, so
             setting 'waited' just on timeout made that re-read unreachable.*/
            $output['waited'] = true;

            /* Poll roughly every 50-200ms (with jitter) instead of hammering Redis
             every ~0.1ms (the previous usleep(100) was 100 *microseconds*, 1000x
             shorter than the 100ms interval that $loops = $locktime*10 was
             designed around). The total wait is capped at $locktime seconds - the
             same window the lock itself is valid for.*/
            $base_interval_us = 50000; // 50ms
            $max_interval_us = 200000; // 200ms cap after backoff
            $deadline = microtime(true) + max(1, (int) $locktime);
            $interval_us = $base_interval_us;

            while (!$data_lock) {
                if (microtime(true) >= $deadline) {
                    break;
                }
                // +/-20% jitter so concurrent waiters don't retry in lockstep
                $jitter = (int) ($interval_us * (mt_rand(-20, 20) / 100));
                usleep(max(1000, $interval_us + $jitter));

                $data_lock = $this->connect->set($lock_id, $token, ['nx', 'px' => $ttl_ms]);

                // gentle exponential backoff, capped
                $interval_us = min($max_interval_us, (int) ($interval_us * 1.5));
            }
        }

        if ($data_lock) {
            $this->lock_tokens[$lock_id] = [
                'token'   => $token,
                'expires' => microtime(true) + $ttl_ms / 1000,
            ];
        }

        $output['locked'] = (bool) $data_lock;
        return $output;
    }

    /**
     * Unlock cached item
     *
     * @param string $key The cache data key
     * @param string $group The cache data group
     *
     * @return boolean
     * @since 1.3.3
     */
    public function unlock($key, $group = null)
    {
        $lock_id = $this->_getCacheId($key, $group) . '_lock';
        $held = $this->lock_tokens[$lock_id] ?? null;
        unset($this->lock_tokens[$lock_id]);

        if ($held) {
            // Compare-and-delete. A bare DEL would drop the lock of another process
            // that acquired it after ours had already expired by TTL.
            $script = "if redis.call('get', KEYS[1]) == ARGV[1]"
                . " then return redis.call('del', KEYS[1]) else return 0 end";
            $this->connect->eval($script, [$lock_id, $held['token']], 1);
        }

        // true means "handled by this driver", see ACache::unlock()
        return true;
    }

    /**
     * Build a value that identifies this process as the owner of a lock.
     *
     * @return string
     */
    protected function _lockToken()
    {
        try {
            $random = bin2hex(random_bytes(8));
        } catch (Exception) {
            $random = dechex(mt_rand()) . dechex(mt_rand());
        }
        return getmypid() . '-' . $random;
    }

    protected function _getCacheId($key, $group)
    {
        return $this->secret . '.' . $group . '.' . $this->_hashCacheKey($key, $group);
    }
}
