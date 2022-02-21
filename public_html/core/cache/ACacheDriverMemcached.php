<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

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
 *
 * define('CACHE_DRIVER', 'memcached');
 * define('CACHE_HOST', 'localhost');
 * define('CACHE_PORT', 11211);
 * define('CACHE_SECRET', 'your_secret_key');
 * define('CACHE_PERSISTENT', false);
 * define('CACHE_COMPRESS_LEVEL', 0);
 *
 * @since  1.2.7
 */
class ACacheDriverMemcached extends ACacheDriver
{

    protected $hostname = CACHE_HOST;
    protected $port = CACHE_PORT;
    protected $secret = CACHE_SECRET;
    protected $persistent = CACHE_PERSISTENT;
    protected $compress_level = CACHE_COMPRESS_LEVEL;

    /**
     * @var $connect - Memcached connection object
     */
    protected $connect;

    /**
     * Constructor
     *
     * @param int $expiration
     * @param int $lock_time
     *
     * @throws AException
     * @since   1.2.7
     */
    public function __construct($expiration, $lock_time = 0)
    {

        if (!$lock_time) {
            $lock_time = 10;
        }
        parent::__construct($expiration, $lock_time);

        // Create the memcache connection
        if ($this->persistent) {
            $this->connect = new Memcached(session_id());
        } else {
            $this->connect = new Memcached;
        }

        $test = $this->connect->addServer($this->hostname, $this->port);

        if ($test == false) {
            throw new AException(AC_ERR_LOAD, 'Error: Could not connect to memcached server.');
        }
        $this->connect->setOption(Memcached::OPT_COMPRESSION, $this->compress_level);
        // Memcached has no list keys, we do our own accounting, initialise key index
        if ($this->connect->get($this->secret.'-index') === false) {
            $empty = array();
            $this->connect->set($this->secret.'-index', $empty, 0);
        }

    }

    /**
     * @return  boolean
     * @since   1.2.7
     */
    public function isSupported()
    {
        if ((extension_loaded('memcached') && class_exists('Memcached')) != true) {
            return false;
        }

        // Now check if we can connect to the specified Memcached server
        $memcached = new Memcached;
        return @$memcached->addServer(CACHE_HOST, CACHE_PORT);
    }

    /**
     * Get cached data by key and group
     *
     * @param   string  $key          The cache data key
     * @param   string  $group        The cache data group
     * @param   boolean $check_expire True to verify cache time expiration
     *
     * @return  mixed|false Boolean false on failure or a cached data string
     *
     * @since   1.2.7
     */
    public function get($key, $group, $check_expire = true)
    {

        $cache_id = $this->_getCacheId($key, $group);
        $data = $this->connect->get($cache_id);
        return $data;
    }

    /**
     * Save data to a file by key and group
     *
     * @param   string $key   The cache data key
     * @param   string $group The cache data group
     * @param   string $data  The data to store in cache
     *
     * @return  boolean
     *
     * @since   1.2.7
     */
    public function put($key, $group, $data)
    {

        $cache_id = $this->_getCacheId($key, $group);
        if (!$this->_lock_index()) {
            return false;
        }

        $index = $this->connect->get($this->secret.'-index');

        if ($index === false) {
            $index = array();
        }

        $temp_array = new stdClass;
        $temp_array->name = $cache_id;
        $temp_array->size = strlen($data);

        $index[] = $temp_array;
        $this->connect->replace($this->secret.'-index', $index, 0);
        $this->_unlock_index();

        // Prevent double writes, write only if it doesn't exist else replace
        if (!$this->connect->replace($cache_id, $data, $this->expire)) {
            $this->connect->set($cache_id, $data, $this->expire);
        }
        return true;
    }

    /**
     * Remove a cached data file by key and group
     *
     * @param   string $key   The cache data key
     * @param   string $group The cache data group
     *
     * @return  boolean
     * @since   1.2.7
     */
    public function remove($key, $group)
    {

        $cache_id = $this->_getCacheId($key, $group);
        if (!$this->_lock_index()) {
            return false;
        }

        $index = $this->connect->get($this->secret.'-index');

        if ($index === false) {
            $index = array();
        }

        foreach ($index as $key => $value) {
            if ($value->name == $cache_id) {
                unset($index[$key]);
            }
            break;
        }

        $this->connect->replace($this->secret.'-index', $index, 0);
        $this->_unlock_index();

        return $this->connect->delete($cache_id);
    }

    /**
     * Clean cache for a group provided.
     *
     * @param   string $group The cache data group, passed '*' indicate all cache removal
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

        if (!$this->_lock_index()) {
            return false;
        }

        $index = $this->connect->get($this->secret.'-index');
        if ($index === false) {
            $index = array();
        }

        foreach ($index as $key => $value) {
            if ($group == '*' || strpos($value->name, $group.'.') === 0) {
                $this->connect->delete($value->name, 0);
                unset($index[$key]);
            }
        }

        $this->connect->replace($this->secret.'-index', $index, 0);
        $this->_unlock_index();
        return true;
    }

    /**
     * Delete expired cache data
     *
     * @return  boolean  True on success, false otherwise.
     *
     * @since   1.2.7
     */
    public function gc()
    {
        return null;
    }

    /**
     * Lock cached item
     *
     * @param   string  $key      The cache data key
     * @param   string  $group    The cache data group
     * @param   integer $locktime Cached item max lock time
     *
     * @return  boolean
     *
     * @since   1.2.7
     */
    public function lock($key, $group, $locktime)
    {

        $cache_id = $this->_getCacheId($key, $group);

        $output = array();
        $output['waited'] = false;

        $loops = $locktime * 10;

        if (!$this->_lock_index()) {
            return false;
        }

        $index = $this->connect->get($this->secret.'-index');

        if ($index === false) {
            $index = array();
        }

        $temp_array = new stdClass;
        $temp_array->name = $cache_id;
        $temp_array->size = 1;
        $index[] = $temp_array;
        $this->connect->replace($this->secret.'-index', $index, 0);
        $this->_unlock_index();

        $data_lock = $this->connect->add($cache_id.'_lock', 1, $locktime);

        if ($data_lock === false) {
            $lock_counter = 0;

            // Loop until you find that the lock has been released.
            // That implies that data get from other thread has finished

            while ($data_lock === false) {
                if ($lock_counter > $loops) {
                    $output['locked'] = false;
                    $output['waited'] = true;
                    break;
                }
                usleep(100);
                $data_lock = $this->connect->add($cache_id.'_lock', 1, $locktime);
                $lock_counter++;
            }
        }

        $output['locked'] = $data_lock;
        return $output;
    }

    /**
     * Unlock cached item
     *
     * @param   string $key   The cache data key
     * @param   string $group The cache data group
     *
     * @return  boolean
     * @since   1.2.7
     */
    public function unlock($key, $group = null)
    {

        $cache_id = $this->_getCacheId($key, $group).'_lock';
        if (!$this->_lock_index()) {
            return false;
        }

        $index = $this->connect->get($this->secret.'-index');

        if ($index === false) {
            $index = array();
        }

        foreach ($index as $key => $value) {
            if ($value->name == $cache_id) {
                unset($index[$key]);
            }
            break;
        }

        $this->connect->replace($this->secret.'-index', $index, 0);
        $this->_unlock_index();
        return $this->connect->delete($cache_id);
    }

    protected function _getCacheId($key, $group)
    {
        return $group.'.'.$this->_hashCacheKey($key, $group);
    }

    /**
     * Lock cache index
     *
     * @return  boolean  True on success, false otherwise.
     *
     * @since   1.2.7
     */
    protected function _lock_index()
    {

        $loops = 300;
        $data_lock = $this->connect->add($this->secret.'-index_lock', 1, 30);

        if ($data_lock === false) {
            $lock_counter = 0;

            // Loop until you find that the lock has been released.  that implies that data get from other thread has finished
            while ($data_lock === false) {
                if ($lock_counter > $loops) {
                    return false;
                    break;
                }

                usleep(100);
                $data_lock = $this->connect->add($this->secret.'-index_lock', 1, 30);
                $lock_counter++;
            }
        }
        return true;
    }

    /**
     *Unlock cache index
     *
     * @return  boolean  True on success, false otherwise.
     * @since 1.2.7
     */
    protected function _unlock_index()
    {
        $result = $this->connect->delete($this->secret.'-index_lock');
        return $result;
    }
}