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
 * File cache driver (default)
 *
 * @since  1.2.7
 */
class ACacheDriverFile extends ACacheDriver
{
    /**
     * Cache directory path
     *
     * @var    string
     * @since  1.2.7
     */
    protected $path;

    /**
     * Cache security code
     *
     * @var    string
     * @since  1.2.7
     */
    protected $security_code = "<?php die('Restricted Access!'); ?>#AbanteCart#";

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
        // note: path with slash at the end!
        $this->path = DIR_CACHE;
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
        return is_writable($this->path);
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
        $data = false;
        $path = $this->_buildFilePath($key, $group);

        if ($check_expire === false || ($check_expire === true && $this->_checkExpire($key, $group) === true)) {
            if (file_exists($path)) {
                $data = @file_get_contents($path);
                if ($data) {
                    // Remove security code line
                    $data = str_replace($this->security_code, '', $data);
                }
            }
            return $data;
        } else {
            return false;
        }
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
        $path = $this->_buildFilePath($key, $group);
        $saved = false;

        $data = $this->security_code.$data;
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path));
        }
        @touch($path);
        $fileopen = @fopen($path, "wb");
        if ($fileopen) {
            $len = strlen($data);
            if (@fwrite($fileopen, $data, $len) !== false) {
                $saved = true;
                //update modification time
                touch($path);
            }
            @fclose($fileopen);
        }

        if ($saved) {
            return true;
        } else {
            //something happen and data was not saved completely, need to remove file and fail.
            if (file_exists($path)) {
                unlink($path);
            }
            return false;
        }
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
        $path = $this->_buildFilePath($key, $group);
        if ($path && is_file($path) && !unlink($path)) {
            return false;
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
        $return = true;

        if (trim($group) == '*') {
            $dirs = $this->_get_directories($this->path);
            for ($i = 0, $n = count($dirs); $i < $n; $i++) {
                $return |= $this->_delete_directory($dirs[$i]);
            }
        } else {
            if ($group) {
                if (is_dir($this->path.$group)) {
                    $return = $this->_delete_directory($this->path.$group);
                }
            }
        }

        return $return;
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
        $result = true;

        // Files older than lifeTime get deleted from cache
        $files = $this->_get_files($this->path, true, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.php'));
        foreach ($files as $file) {
            $time = @filemtime($file);
            if (($time + $this->expire) < $this->now || empty($time)) {
                if (file_exists($file)) {
                    $result |= @unlink($file);
                }
            }
        }
        return $result;
    }

    /**
     * Lock cached item
     *
     * @param   string  $key      The cache data key
     * @param   string  $group    The cache data group
     * @param   integer $locktime Cached item max lock time
     *
     * @return  array
     *
     * @since   1.2.7
     */
    public function lock($key, $group, $locktime)
    {
        $ret = array();
        $ret['waited'] = false;

        $loops = $this->lock_time * 10;
        if ($locktime) {
            $loops = $locktime * 10;
        }

        $path = $this->_buildFilePath($key, $group);
        //consider locked if file does not exists yet 
        if (!file_exists($path)) {
            $ret['locked'] = true;
            return $ret;
        }

        $fileopen = @fopen($path, "r+b");
        if ($fileopen) {
            $data_lock = @flock($fileopen, LOCK_EX);
        } else {
            $data_lock = false;
        }

        if ($data_lock === false) {
            $lock_counter = 0;
            // Loop until lock has been released. Limit is set to lock time * 10
            while ($data_lock === false) {
                if ($lock_counter > $loops) {
                    $ret['locked'] = false;
                    $ret['waited'] = true;
                    break;
                }
                usleep(100);
                $data_lock = @flock($fileopen, LOCK_EX);
                $lock_counter++;
            }
        }

        $ret['locked'] = $data_lock;
        return $ret;
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
        $path = $this->_buildFilePath($key, $group);
        if (!is_file($path)) {
            return true;
        }
        $fileopen = @fopen($path, "r+b");
        if ($fileopen) {
            $ret = @flock($fileopen, LOCK_UN);
        } else {
            // Expect true if $fileopen is false.
            $ret = true;
        }
        return $ret;
    }

    /**
     * Check to make sure cache is still valid, if not, delete it.
     *
     * @param   string $key   Cache key to expire.
     * @param   string $group The cache data group.
     *
     * @return  boolean
     *
     * @since   1.2.7
     */
    protected function _checkExpire($key, $group)
    {
        $path = $this->_buildFilePath($key, $group);

        if (file_exists($path)) {
            $time = @filemtime($path);
            if (($time + $this->expire) < $this->now || empty($time)) {
                @unlink($path);
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Get a cache file path from an key/group pair
     *
     * @param   string $key   The cache data key
     * @param   string $group The cache data group
     *
     * @return  mixed   False / The cache file path
     *
     * @since   1.2.7
     */
    protected function _buildFilePath($key, $group)
    {
        $name = $this->_hashCacheKey($key, $group);
        $dir = $this->path.$group;

        // If the folder doesn't exist try to create it
        if (!is_dir($dir)) {
            // Make sure the index file is there
            $indexFile = $dir.'/index.php';
            if (mkdir($dir, 0777, true)) {
                file_put_contents($indexFile, "<?php die('Restricted Access!'); ?>");
            }
        }

        // Double check that folder now exists
        if (!is_dir($dir)) {
            return false;
        }

        return $dir.'/'.$name.'.php';
    }

    /**
     * Fast delete of a folder with content files
     *
     * @param   string $path Full path to the folder to delete.
     *
     * @return  boolean
     * @since   1.2.7
     */
    protected function _delete_directory($path)
    {
        if (!$path || !is_dir($path) || empty($this->path)) {
            $err_text = sprintf('Error: Cannot delete cache folder: %s! Specified folder does not exist.', $path);
            $error = new AError($err_text);
            $error->toLog()->toDebug();
            return false;
        }

        // Check to make sure path is inside cache folder
        $match = strpos($path, $this->path);
        if ($match === false || $match > 0) {
            $err_text = sprintf('Error: Cannot delete cache folder: %s! Specified path in not within cache folder.', $path);
            $error = new AError($err_text);
            $error->toLog()->toDebug();
            return false;
        }

        //check permissions before rename
        if (!is_writable_dir($path)) {
            $err_text = sprintf('Error: Cannot delete cache folder: %s! Permission denied.', $path);
            $error = new AError($err_text);
            $error->toLog()->toDebug();
            return false;
        }
        //rename folder to prevent recreation by other process
        $new_path = $path.'_trash';
        $renamed = false;
        if (!is_dir($new_path)) {
            if (rename($path, $new_path)) {
                $path = $new_path;
                $renamed = true;
            }
        }

        // Remove all the files in folder if they exist; disable all filtering
        $files = $this->_get_files($path, false, array(), array());
        if (!empty($files) && !is_array($files)) {
            if (@unlink($files) !== true) {
                return false;
            }
        } else {
            if (!empty($files) && is_array($files)) {
                foreach ($files as $file) {
                    if (@unlink($file) !== true) {
                        //no permissions to delete	
                        $filename = basename($file);
                        $err_text = sprintf('Error: Cannot delete cache file: %s! No permissions to delete.', $filename);
                        $error = new AError($err_text);
                        $error->toLog()->toDebug();
                        return false;
                    }
                }
            }
        }

        //one level directories
        $folders = $this->_get_directories($path, false);
        foreach ($folders as $folder) {
            if (is_link($folder)) {
                //Delete links
                if (@unlink($folder) !== true) {
                    return false;
                }
                //Remove inner folders with recursion
            } else {
                if ($this->_delete_directory($folder) !== true) {
                    return false;
                }
            }
        }
        $ret = true;
        if ($renamed) {
            if (@rmdir($path)) {
                $ret = true;
            } else {
                $err_text = sprintf('Error: Cannot delete cache directory: %s! No permissions to delete.', $path);
                $error = new AError($err_text);
                $error->toLog()->toDebug();
                $ret = false;
            }
        }
        return $ret;
    }

    /**
     * Fast files read in provided directory.
     *
     * @param   string $path             The path of the folder to read.
     * @param   mixed  $recurse          True to recursively search into sub-folders, or an
     *                                   integer to specify the maximum depth.
     * @param   array  $exclude          Array with names of files which should be skipped
     * @param   array  $exclude_filter   Array of folder names to skip
     *
     * @return  array|false    Files in the given folder.
     *
     * @since   1.2.7
     */
    protected function _get_files($path, $recurse = false, $exclude = array('.svn', 'CVS', '.DS_Store', '__MACOSX'), $exclude_filter = array('^\..*', '.*~'))
    {
        $ret_arr = array();
        if (!is_dir($path)) {
            return false;
        }

        if (!($handle = @opendir($path))) {
            //return nothing
            return $ret_arr;
        }

        if (count($exclude_filter)) {
            $exclude_filter = '/('.implode('|', $exclude_filter).')/';
        } else {
            $exclude_filter = '';
        }

        while (($file = readdir($handle)) !== false) {
            if (($file != '.') && ($file != '..')
                && (!in_array($file, $exclude))
                && (!$exclude_filter || !preg_match($exclude_filter, $file))
            ) {
                $dir = $path.'/'.$file;
                if (is_dir($dir)) {
                    //process directory
                    if ($recurse) {
                        if (is_int($recurse)) {
                            $arr = $this->_get_files($dir, $recurse - 1);
                        } else {
                            $arr = $this->_get_files($dir, $recurse);
                        }
                        $ret_arr = array_merge($ret_arr, $arr);
                    }
                } else {
                    $ret_arr[] = $path.'/'.$file;
                }
            }
        }
        closedir($handle);
        return $ret_arr;
    }

    /**
     * Read the folders in a directory path.
     *
     * @param   string $path           The path to directory.
     * @param   mixed  $recurse        True to recursively search into sub-folders, or an integer to specify the maximum depth.
     * @param   array  $exclude        Array with names of folders which should not be shown in the result.
     * @param   array  $exclude_filter Array with regular expressions matching folders which should not be shown in the result.
     *
     * @return  array|false  with full path sub-directories.
     *
     * @since   1.2.7
     */
    protected function _get_directories($path, $recurse = false, $exclude = array('.svn', 'CVS', '.DS_Store', '__MACOSX'), $exclude_filter = array('^\..*'))
    {
        $ret_arr = array();

        if (!is_dir($path)) {
            return false;
        }

        if (!($handle = @opendir($path))) {
            //return nothing
            return $ret_arr;
        }

        if (count($exclude_filter)) {
            $excludefilter_string = '/('.implode('|', $exclude_filter).')/';
        } else {
            $excludefilter_string = '';
        }

        while (($file = readdir($handle)) !== false) {
            if (($file != '.') && ($file != '..')
                && (!in_array($file, $exclude))
                && (empty($excludefilter_string) || !preg_match($excludefilter_string, $file))
            ) {
                $dir = $path.'/'.$file;
                if (is_dir($dir)) {
                    $ret_arr[] = $dir;
                    //recurse if needed
                    if ($recurse) {
                        if (is_int($recurse)) {
                            $arr = $this->_get_directories($dir, $recurse - 1, $exclude, $exclude_filter);
                        } else {
                            $arr = $this->_get_directories($dir, $recurse, $exclude, $exclude_filter);
                        }
                        $ret_arr = array_merge($ret_arr, $arr);
                    }
                }
            }
        }
        closedir($handle);
        return $ret_arr;
    }
}