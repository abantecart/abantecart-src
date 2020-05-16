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
 * APCu cache driver
 *
 * NOTE: to use this driver put lines belong into your system/config.php
 *
 * define('CACHE_DRIVER', 'apcu');
 * define('CACHE_SECRET', 'your_secret_key');
 *
 * @since  1.2.7
 */
class ACacheDriverAPCu extends ACacheDriver
{

    protected $secret = CACHE_SECRET;

    /**
     * Constructor
     *
     * @param int $expiration
     * @param int $lock_time
     *
     * @since   1.2.7
     */
    public function __construct($expiration, $lock_time = 0)
    {
        if (!$lock_time) {
            $lock_time = 10;
        }

        parent::__construct($expiration, $lock_time);
    }

    protected function _getCacheId($key, $group)
    {
        return $this->secret.'-cache-'.$group.'.'.$this->_hashCacheKey($key, $group);
    }

    /**
     * Test to see if the cache directory is writable.
     *
     * @return  boolean
     *
     * @since   1.2.7
     */
    public function isSupported()
    {
        $supported = extension_loaded('apcu') && ini_get('apc.enabled');
        // If on the CLI interface, the `apc.enable_cli` option must also be enabled
        if ($supported && php_sapi_name() === 'cli') {
            $supported = ini_get('apc.enable_cli');
        }
        return (bool)$supported;
    }

    /**
     * Get cached data from a file by key and group
     *
     * @param   string  $key          The cache data key
     * @param   string  $group        The cache data group
     * @param   boolean $check_expire True to verify cache time expiration
     *
     * @return  mixed  Boolean false on failure or a cached data string
     *
     * @since   1.2.7
     */
    public function get($key, $group, $check_expire = true)
    {
        $cache_key = $this->_getCacheId($key, $group);
        return apcu_fetch($cache_key);
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
        $cache_key = $this->_getCacheId($key, $group);
        return apcu_store($cache_key, $data, $this->expire);
    }

    /**
     * Remove a cached data file by key and group
     *
     * @param   string $key   The cache data key
     * @param   string $group The cache data group
     *
     * @return  boolean
     *
     * @since   1.2.7
     */
    public function remove($key, $group)
    {
        $cache_key = $this->_getCacheId($key, $group);
        // The apcu_delete function returns false if the ID does not exist
        if (apcu_exists($cache_key)) {
            return apcu_delete($cache_key);
        }
        return true;
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

        $cache_info = apcu_cache_info();
        $keys = $cache_info['cache_list'];

        foreach ($keys as $key) {
            // If APCu is being used for this adapter, the internal key name changed with APCu 4.0.7 from key to info
            $internalKey = isset($key['info']) ? $key['info'] : $key['key'];
            if ($group == '*' || strpos($internalKey, $this->secret.'-cache-'.$group.'.') === 0) {
                apcu_delete($internalKey);
            }
        }
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
        $cache_info = apcu_cache_info();
        $keys = $cache_info['cache_list'];
        foreach ($keys as $key) {
            // If APCu is being used for this adapter, the internal key name changed with APCu 4.0.7 from key to info
            $internalKey = isset($key['info']) ? $key['info'] : $key['key'];
            if (strpos($internalKey, $this->secret.'-cache-')) {
                apcu_fetch($internalKey);
            }
        }
        return true;
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

        $output = array();
        $output['waited'] = false;

        $looptime = $locktime * 10;

        $cache_key = $this->_getCacheId($key, $group).'_lock';

        $data_lock = apcu_add($cache_key, 1, $locktime);

        if ($data_lock === false) {
            $lock_counter = 0;
            // Loop until you find that the lock has been released. That implies that data get from other thread has finished
            while ($data_lock === false) {
                if ($lock_counter > $looptime) {
                    $output['locked'] = false;
                    $output['waited'] = true;
                    break;
                }

                usleep(100);
                $data_lock = apcu_add($cache_key, 1, $locktime);
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
     *
     * @since   1.2.7
     */
    public function unlock($key, $group = null)
    {
        $cache_key = $this->_getCacheId($key, $group).'_lock';
        // The apcu_delete function returns false if the ID does not exist
        if (apcu_exists($cache_key)) {
            return apcu_delete($cache_key);
        }
        return true;
    }
}