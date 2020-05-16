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

/**
 * Class ACache API
 *
 * @updated    1.2.7
 *
 * @link       http://docs.abantecart.com/pages/developer/cache.html
 *
 * @package    AbanteCart
 * @subpackage ACache
 *
 * Core class that implements an object cache.
 *
 * The Object ACache is used to save on reads from the database. The
 * Object Cache stores all of the cache data to memory and makes the cache
 * contents available by using a cache key, which is used to name and later retrieve
 * the cache contents.
 *
 * The Object Cache can be replaced by other caching mechanisms by updating CACHE_DRIVER in config.php file
 *
 */
class ACache
{

    /**
     * Default Expiration time set to 1 day
     */
    private $expire = 86400;

    /**
     * Cache storage status
     */
    private $enabled = false;

    /**
     * Cache lock time, 0 - no cache locking
     */
    private $locktime = 10;

    /**
     * Holds the cached data.
     */
    private $cache = array();

    /**
     * Holds cache storage driver object
     *
     * @var ACacheDriverFile |
     */
    private $cache_driver;

    /**
     * Number of times the cache data was saved/updated.
     */
    private $cache_saves = array();

    /**
     * Number of times the cache data was accessed.
     */
    private $cache_hits = array();

    /**
     * Number of times the cache was loaded from storage. Ideally, should be 1 for any key.
     */
    private $cache_loads = array();

    /**
     * Number of times the cache did not have data present. Ideally, should be 0 for any key.
     */
    private $cache_misses = array();

    /**
     * Called upon object declaration, should be in INIT.
     */
    public function __construct()
    {
    }

    /**
     * Called upon object destruction, should be when PHP ends.
     *
     * @return true.
     */
    public function __destruct()
    {
        return true;
    }

    /**
     * Enable caching is storage. Note, persistent in memory cache is always enabled
     *
     * @return  void
     *
     * @since  1.2.7
     */
    public function enableCache()
    {
        $this->enabled = true;
    }

    /**
     *Disable caching is storage. Note, persistent in memory cache is always enabled
     *
     *
     * @return  void
     *
     * @since  1.2.7
     */
    public function disableCache()
    {
        $this->enabled = false;
    }

    /**
     * Check if cache storage is enabled
     *
     * @return  boolean  Caching state
     *
     * @since  1.2.7
     */
    public function isCacheEnabled()
    {
        return $this->enabled;
    }

    /**
     * Function returns string based on multidimensional array. Used for cache key building.
     *
     * @param array $data
     *
     * @return string
     * @since 1.2.7
     */
    public function paramsToString($data = array())
    {
        $output = '';
        if (empty($data)) {
            return '';
        }
        asort($data);
        foreach ($data as $key => $val) {
            if (is_array($val)) {
                $output .= $this->paramsToString($val);
            } else {
                $output .= '.'.$key."=".$val;
            }
        }
        return $output;
    }

    /**
     * Set cache expiration to custom value
     *
     * @param int $expiration in seconds
     *
     * @return void
     *
     * @since  1.2.7
     */
    public function setExpiration($expiration = 86400)
    {
        $this->expire = $expiration;
    }

    /**
     * Set and load cache storage drivers.
     *
     * @param string $driver
     *
     * @return bool
     * @since 1.2.7
     */
    public function setCacheStorageDriver($driver)
    {
        //get and validate driver for availability
        $drv = $this->getCacheStorageDriver($driver);
        if (isset($drv) && is_file($drv['file'])) {
            //try to load driver class
            include_once($drv['file']);

            // If the class doesn't exist we have nothing else to do here.
            if (!class_exists($drv['class'])) {
                return false;
            }

            //instantiate storage driver class
            $this->cache_driver = new $drv['class']($this->expire, $this->locktime);
            return true;
        }
        return false;
    }

    /**
     * Saves the data contents into the cache.
     *
     * @param string $key
     *
     * @param mixed  $data
     *
     * @return bool
     */
    public function push($key, $data)
    {
        $ret = false;
        if (!$key) {
            return $ret;
        }

        //get group name from the key Example: key=[group].text
        $group = $this->_get_group($key);
        $this->cache[$group][$key] = $data;
        $this->cache_saves[$group][$key] += 1;

        if (!is_null($data) && $this->enabled && $this->cache_driver && $this->cache_driver->isSupported()) {
            $data = serialize($data);

            $lock = $this->lock($key, $group);
            if ($lock['locked'] == false && $lock['waited'] == true) {
                //cache is released, try locking again. 
                $lock = $this->lock($key, $group);
            }

            $ret = $this->cache_driver->put($key, $group, $data);

            if ($lock['locked'] == true) {
                //unlock if cache was locked
                $this->unlock($key, $group);
            }
        }
        return $ret;
    }

    /**
     * Deprecated. Old cache compatibility. Will be removed in 1.3
     *
     * @deprecated
     *
     * @param     $key
     * @param     $data
     * @param int $language_id
     * @param int $store_id
     *
     * @return bool
     */
    public function set($key, $data, $language_id = 0, $store_id = 0)
    {
        if ($language_id || $store_id) {
            if ($language_id && $store_id) {
                $key = $key.".store_".$store_id."_lang_".$language_id;
            } elseif ($store_id) {
                $key = $key.".store_".$store_id;
            } else {
                $key = $key.".lang_".$language_id;
            }
        }
        return $this->push($key, $data);
    }

    /**
     * Retrieves the cache contents.
     *
     * The contents will be first attempted to be retrieved by the key from the cache in memory data structure.
     * If the cache exists the content is returned
     *
     * On failure false is returned & the number of cache misses will be incremented for stats
     *
     * @param string $key
     *
     * @return mixed|false
     */
    public function pull($key)
    {
        if (!$key) {
            return false;
        }

        $group = $this->_get_group($key);
        if ($this->_exists($key, $group)) {
            $this->cache_hits[$group][$key] += 1;
            return $this->cache[$group][$key];
        }

        if ($this->enabled && $this->cache_driver && $this->cache_driver->isSupported()) {
            //load cache from storage		
            $data = $this->cache_driver->get($key, $group);
            if ($data === false) {
                //check if cache is locked
                $lock = $this->lock($key, $group);
                if ($lock['locked'] == true && $lock['waited'] == true) {
                    //try to get cache again 
                    $data = $this->cache_driver->get($key, $group);
                    $this->unlock($key, $group);
                }
            }

            if ($data !== false) {
                $data = unserialize($data);
                $this->cache[$group][$key] = $data;
                $this->cache_loads[$group][$key] += 1;
                return $data;
            }
        }

        $this->cache_misses[$group][$key] += 1;
        return false;
    }

    /**
     * Deprecated. Old cache compatibility. Will be removed in 1.3
     *
     * @deprecated
     *
     * @param     $key
     * @param int $language_id
     * @param int $store_id
     *
     * @return false|mixed|null
     */
    public function get($key, $language_id = 0, $store_id = 0)
    {
        if ($language_id || $store_id) {
            if ($language_id && $store_id) {
                $key = $key.".store_".$store_id."_lang_".$language_id;
            } elseif ($store_id) {
                $key = $key.".store_".$store_id;
            } else {
                $key = $key.".lang_".$language_id;
            }
        }
        $return = $this->pull($key);
        if ($return === false) {
            //Should return false if no cache present.
            //for legacy support we return NULL. Starting v1.3 FALSE will be returned. 
            return null;
        } else {
            return $return;
        }
    }

    /**
     * Removes the contents of the cache key in the group.
     *
     * If the cache key does not exist in the group, then nothing will happen.
     *
     * @param string $key
     *
     * @return bool False if the contents weren't deleted and true on success.
     */
    public function remove($key)
    {

        $group = $this->_get_group($key);
        if (trim($key) == '*') {
            // clean all
            $this->flush();
            if ($this->enabled && $this->cache_driver && $this->cache_driver->isSupported()) {
                if (!$this->cache_driver->clean('*')) {
                    return false;
                }
            }
        } else {
            if ($group == $key) {
                //if group and key match, assume we remove whole group 
                unset($this->cache[$group]);
                if ($this->enabled && $this->cache_driver && $this->cache_driver->isSupported()) {
                    if (!$this->cache_driver->clean($group)) {
                        return false;
                    }
                }
            } else {
                unset($this->cache[$group][$key]);
                if ($this->enabled && $this->cache_driver && $this->cache_driver->isSupported()) {
                    if (!$this->cache_driver->remove($key, $group)) {
                        //can not delete this key, delete entire group (backwards compatibility)
                        if (!$this->cache_driver->clean($group)) {
                            return false;
                        }
                    }
                }
            }
        }

        if (trim($key) != '*' && $group != 'html_cache') {
            //remove HTML cache on any other cache clean up as data changed or expired
            unset($this->cache['html_cache']);
            $this->cache_driver->clean('html_cache');
        }

        return true;
    }

    /**
     *  Old cache compatibility. Will be removed in 1.3
     *
     * @deprecated
     *
     * @param            $key
     * @param int|string $language_id
     * @param int|string $store_id
     *
     * @return bool
     */
    public function delete($key, $language_id = 0, $store_id = 0)
    {
        if ($language_id || $store_id) {
            if ($language_id && $store_id) {
                $key = $key.".store_".$store_id."_lang_".$language_id;
            } elseif ($store_id) {
                $key = $key.".store_".$store_id;
            } else {
                $key = $key.".lang_".$language_id;
            }
        }

        return $this->remove($key);
    }

    /**
     * Serves as a utility function to determine whether a key exists in the memory cache.
     *
     * @param string $key   Cache key to check for existence.
     * @param string $group Cache group.
     *
     * @return bool, Whether the key exists in the cache for given group.
     */
    protected function _exists($key, $group)
    {
        return isset($this->cache[$group])
        && (isset($this->cache[$group][$key])
            || array_key_exists($key, $this->cache[$group])
        );
    }

    /**
     * Clears the object cache's all data.
     *
     * @return true.
     */
    public function flush()
    {
        $this->cache = array();
        return true;
    }

    /**
     * Set lock on cached item to prevent data clash
     *
     * @param   string $key      The cache data key
     * @param   string $group    The cache data group
     * @param   string $locktime The default locktime for locking the cache.
     *
     * @return  object  Properties are lock and locklooped
     *
     * @since   1.2.7
     */
    public function lock($key, $group, $locktime = null)
    {
        $ret = array();
        $ret['waited'] = false;

        $locktime = ($locktime) ? $locktime : $this->locktime;

        //process lock in the cache driver
        if ($this->enabled && $this->cache_driver && $this->cache_driver->isSupported() && $locktime > 0) {
            $locked = $this->cache_driver->lock($key, $group, $locktime);
            //false will be returned only if lock is not supported by driver (base class). 
            if ($locked !== false) {
                return $locked;
            }
        } else {
            $ret['locked'] = false;
            return $ret;
        }

        //Not supported in selected driver. Process lock generic way.  

        //set short expiration time based on $locktime for the lock 
        $this->cache_driver->setExpiration($locktime);

        $looptime = $locktime * 10;
        $lock_key = $key.'_lc';
        $data_lock = $this->cache_driver->get($lock_key, $group);
        //do we have existing lock?
        if ($data_lock !== false) {
            $lock_counter = 0;
            // Loop till lock has been released.
            // Once pull from other thread has been finished
            while ($data_lock !== false) {
                if ($lock_counter > $looptime) {
                    $ret['locked'] = false;
                    $ret['waited'] = true;
                    break;
                }
                usleep(100);
                $data_lock = $this->cache_driver->get($lock_key, $group);
                $lock_counter++;
            }
        }

        $ret['locked'] = $this->cache_driver->put(1, $lock_key, $group);

        //reset cache expiration time back 
        $this->cache_driver->setExpiration($this->expire);

        return $ret;
    }

    /**
     * Unset lock cached item
     *
     * @param   string $key   The cache data key
     * @param   string $group The cache data group
     *
     * @return  boolean
     *
     * @since   1.2.7
     */
    public function unlock($key, $group)
    {

        if ($this->enabled && $this->cache_driver && $this->cache_driver->isSupported()) {
            $unlocked = $this->cache_driver->unlock($key, $group);
            if ($unlocked !== false) {
                return $unlocked;
            }
        } else {
            return false;
        }

        //cleanup after cache unlock
        $unlock = $this->cache_driver->remove($key.'_lc', $group);
        return $unlock;
    }

    /**
     * Print the stats of the caching.
     *
     * Gives the cache hits, and cache misses. Also prints every cached group,
     * key and the data.
     *
     * @since 1.2.7
     */
    public function stats()
    {
        $kb_in_bytes = 1024;
        $total_size = 0;
        $stats = "<p>";
        $stats .= "<strong>Cache usage report:</strong>";
        $stats .= "</p>";
        $stats .= '<table>';
        $stats .= '<tr><td></td><td width="9%"></td><td width="9%"></td><td width="9%"></td><td width="9%"></td><td width="9%"></td></tr>';
        foreach ($this->cache as $group => $cache) {
            $stats .= "<tr><td colspan=6>";
            $stats .= "<strong>Cache group: $group</strong>";
            $stats .= "</td></tr>";
            foreach ($cache as $key => $data) {
                $size_in_bytes = strlen(serialize($data));
                $total_size += $size_in_bytes;
                $text = '';
                if ($this->cache_saves[$group][$key] > 1) {
                    $text .= "<td><b>--> Saves: ".$this->cache_saves[$group][$key]."</b></td>";
                } else {
                    if ($this->cache_saves[$group][$key]) {
                        $text .= "<td>Saves: ".$this->cache_saves[$group][$key]."</td>";
                    } else {
                        $text .= "<td>No saves</td>";
                    }
                }
                if ($this->cache_loads[$group][$key] > 1) {
                    $text .= "<td><b>--> Loads: ".$this->cache_loads[$group][$key]."</b></td>";
                } else {
                    if ($this->cache_loads[$group][$key]) {
                        $text .= "<td>Loads: ".$this->cache_loads[$group][$key]."</td>";
                    } else {
                        $text .= "<td>No loads</td>";
                    }
                }
                if ($this->cache_hits[$group][$key]) {
                    $text .= "<td>Hits: ".$this->cache_hits[$group][$key]."</td>";
                } else {
                    $text .= "<td>No Hits</td>";
                }
                if ($this->cache_misses[$group][$key] > 1) {
                    $text .= "<td><b>--> Misses: ".$this->cache_misses[$group][$key]."</b></td>";
                } else {
                    if ($this->cache_misses[$group][$key]) {
                        $text .= "<td>Misses: <b class=\"danger\">".$this->cache_misses[$group][$key]."</b></td>";
                    } else {
                        $text .= "<td>No Misses</td>";
                    }
                }

                $stats .= '<tr>
						<td style="text-align:left; padding-left: 20px;">'.$key.'</td><td>'.number_format($size_in_bytes / $kb_in_bytes, 2).'k</td> '.$text.'
					</tr>';
            }
        }
        $stats .= "</table>";
        $stats .= "<p>";
        $stats .= "<strong>Total cache memory size: ".number_format($total_size / $kb_in_bytes, 2)."k</strong>";
        $stats .= "</p>";
        return $stats;
    }

    /**
     * Get/validate storage driver details.
     *
     * @param string $driver_name
     *
     * @return array An array with storage driver details. No validation if class DO exist in the file!
     *
     * @since 1.2.7
     */
    public function getCacheStorageDriver($driver_name)
    {
        $driver = array();
        $file_path = DIR_CORE.'cache/'.$driver_name.'.php';
        if (file_exists($file_path)) {
            $class = 'ACacheDriver'.ucfirst($driver_name);
            $driver = array('class' => $class, 'file' => $file_path, 'driver_name' => $driver_name);
        }
        return $driver;
    }

    /**
     * Get all available cache storage drivers.
     *
     * @return  array An array of available storage drivers. No validation if classes DO exist!
     *
     * @since 1.2.7
     */
    public function getCacheStorageDrivers()
    {
        $drivers = array();

        // Get an iterator and loop trough the driver php files.
        $files = new DirectoryIterator(DIR_CORE.'cache');
        foreach ($files as $file) {
            //we need only php files.
            $file_name = $file->getFilename();
            if (!$file->isFile() || $file->getExtension() != 'php' || $file_name == 'index.php' || $file_name == 'driver.php') {
                continue;
            }
            //Build class name from the file name.
            $driver_name = substr($file_name, 0, (strrpos($file_name, ".")));
            $class = 'ACacheDriver'.ucfirst($driver_name);
            $drivers[$driver_name] = array('class' => $class, 'file' => $file->getPathname(), 'driver_name' => $driver_name);
        }

        return $drivers;
    }

    private function _get_group($key)
    {
        if (!$key) {
            return false;
        }
        //match first word before dot 
        $split_key = explode('.', $key);
        $group = $split_key[0];
        if (empty($group)) {
            //nothing found, make key as a group 
            $group = $key;
        }
        return $group;
    }

    // Special Case of HTML Cache handling

    /**
     * Read HTML cache file
     *
     * @param string $key
     *
     * @return string
     */
    public function get_html_cache($key)
    {
        if (!$key) {
            return '';
        }
        $group = $this->_get_group($key);
        if ($this->enabled && $this->cache_driver && $this->cache_driver->isSupported()) {
            //load cache from storage		
            $data = $this->cache_driver->get($key, $group);
            if ($data === false) {
                //check if cache is locked
                $lock = $this->lock($key, $group);
                if ($lock['locked'] == true && $lock['waited'] == true) {
                    //try to get cache again 
                    $data = $this->cache_driver->get($key, $group);
                    $this->unlock($key, $group);
                }
            }

            if ($data !== false) {
                $this->cache_loads[$group][$key] += 1;
                return $data;
            }
        }

        $this->cache_misses[$group][$key] += 1;
        return '';
    }

    /**
     * Write HTML Cache file
     *
     * @param string $key
     * @param string $data
     *
     * @return bool
     */
    public function save_html_cache($key, $data)
    {
        $ret = false;
        if (!$key) {
            return false;
        }

        $group = $this->_get_group($key);
        if (!is_null($data) && $this->enabled && $this->cache_driver && $this->cache_driver->isSupported()) {

            $lock = $this->lock($key, $group);
            if ($lock['locked'] == false && $lock['waited'] == true) {
                //cache is released, try locking again. 
                $lock = $this->lock($key, $group);
            }
            //Minify HTML before saving to cache
            require_once(DIR_CORE.'helper/html-css-js-minifier.php');
            $data = minify_html($data);
            $ret = $this->cache_driver->put($key, $group, $data);

            if ($lock['locked'] == true) {
                //unlock if cache was locked
                $this->unlock($key, $group);
            }
        }
        return $ret;
    }

}
