<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2022 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

//include abstract cache storage driver class
include_once('driver.php');

/**
 * Memcached driver
 *
 * NOTE: to use this driver put lines belong into your system/config.php
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
     * Constructor
     *
     * @param int $expiration
     * @param int $lock_time
     *
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
        $this->connect->auth($this->password);

        if (!$test) {
            throw new AException(AC_ERR_LOAD, 'Error: Could not connect to Redis server.');
        }
        // Memcached has no list keys, we do our own accounting, initialise key index
        if ($this->connect->get($this->secret . '-index') === false) {
            $empty = [];
            $this->connect->set($this->secret . '-index', $empty, 0);
        }
    }

    /**
     * @return  boolean
     * @since   1.2.7
     */
    public function isSupported()
    {
        if ((extension_loaded('redis') && class_exists('Redis')) != true) {
            return false;
        }

        // Now check if we can connect to the specified Memcached server
        $redis = new \Redis;
        return @$redis->connect(CACHE_HOST, CACHE_PORT);
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
        return json_decode($this->connect->get($cache_id), true);
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
        $status = $this->connect->set($cache_id, json_encode($data));
        if ($status) {
            $this->connect->expire($cache_id, $this->expire);
        }
        return (bool)$status;
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

        $indexes = (array)$this->connect->keys($this->secret . '*');

        foreach ($indexes as $keyName) {
            if ($group == '*') {
                $this->connect->flushAll();
            } elseif (is_int(strpos($keyName, $group . '.'))) {
                $this->connect->del($keyName);
            }
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
        return null;
    }

    protected function _getCacheId($key, $group)
    {
        return $this->secret . '.' . $group . '.' . $this->_hashCacheKey($key, $group);
    }


}