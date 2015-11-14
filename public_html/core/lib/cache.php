<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
/**
 * Class ACache
 */

final class ACache {
	/**
	 * @var int
	 */
	private $expire = 86400; //one day
	/**
	 * @var Registry
	 */
	private $registry;
	/**
	 * @var array
	 */
	private $empties = array();
	/**
	 * @var array
	 */
	private $exists = array();
	/**
	 * @var array
	 */
	private $cache_map = array();

	public function __construct(){
		$this->registry = Registry::getInstance();

		$cache_files = $this->_get_cache_files();

		if(!is_array($cache_files) || !is_writeable(DIR_CACHE)){
			$log = $this->registry->get('log');
			if(!is_object($log) || !method_exists($log, 'write')){
				$error_text = 'Error: Unable to access or write to cache directory ' . DIR_CACHE;
				$log = new ALog(DIR_SYSTEM . 'logs/error.txt');
				$this->registry->set('log', $log);
			}
			$log->write($error_text);
			//try to add message for admin (check if for install-process too)
			$db = $this->registry->get('db');

			if(is_object($db) && method_exists($db, 'query')){
				$error_text .= ' Cache feature was disabled. Check permissions on directory and enable setting back.';
				$m = new AMessage();
				$m->saveError('AbanteCart Warning', $error_text);
				//also disable caching in config
				$sql = "UPDATE ".$db->table('settings')."
						SET `value` = '0'
						WHERE `key` = 'config_cache_enable'";
				$db->query($sql);
			}

		} else{
			foreach($cache_files as $file){
				//first of all check if file expired. delete it if needed
				$file_time = filemtime($file);
				if((time() - $file_time) > $this->expire){
					if(file_exists($file)){
						$this->_remove($file);
						continue;
					}
				}
				//build cache map as array {cache_file_name_without_timestamp=>expire_time}
				$ch_base = substr($file, 0, -11);
				$this->cache_map[$ch_base] = $file_time + $this->expire;
			}
		}
	}

	/**
	 * returns array of full pathes of cache files
	 * @return array
	 */
	private function _get_cache_files(){
		$output = glob(DIR_CACHE . '*/*', GLOB_NOSORT);
		//do the trick for php v5.3.3. (php-bug in glob(). It returns false for empty folder).
		if(is_writable(DIR_CACHE) && $output===false){
			$output = array();
		}
		return $output;
	}

	/**
	 * force to get cache data based on params and ignore disable cache setting
	 * @param string $key
	 * @param mixed $language_id
	 * @param mixed $store_id
	 * @return mixed|null
	 */
	public function force_get($key, $language_id = '', $store_id = '' ) {
		return $this->get($key, $language_id, $store_id, true );
	}

	/**
	 * get cache data based on params.
	 * @param string $key
	 * @param mixed $language_id
	 * @param mixed $store_id
	 * @param bool $disabled_override
	 * @return mixed|null
	 */
	public function get($key, $language_id = '', $store_id = '', $disabled_override = false) {
		//clean up if disabled cache
		if (!$disabled_override && !$this->registry->get('config')->get('config_cache_enable')){
			$this->delete($key, $language_id, $store_id );
			unset($this->empties[$key.'_'.$language_id.'_'.$store_id]);
			return null;
		}
		//load cache file name for the section (key)
		$cache_filename =  $this->_build_name($key, $language_id, $store_id);
		$cache_file_full_name = $cache_filename.'.'.$this->cache_map[$cache_filename];

		//if file expired or not exists
		if (!isset($this->cache_map[$cache_filename]) || $this->cache_map[$cache_filename] < time()) {
			if (file_exists($cache_file_full_name)) {
				$this->_remove($cache_file_full_name);
				unset($this->cache_map[$cache_filename]);
				unset($this->empties[$key.'_'.$language_id.'_'.$store_id]);
			}
			return null;
		}else{ // if all good
			if(file_exists($cache_file_full_name)){
				if(filesize($cache_file_full_name)>0){
					$handle = fopen($cache_file_full_name, 'r');
					$cache = fread($handle, filesize($cache_file_full_name));
					fclose($handle);
					$output = unserialize($cache);
				}else{
					$output = '';
				}
				$this->empties[$key.'_'.$language_id.'_'.$store_id] = !empty($output); // if not empty
				$this->exists[$key.'_'.$language_id.'_'.$store_id] = true;
				return $output;
			}
		}

		unset($this->empties[$key.'_'.$language_id.'_'.$store_id]);
		return null;
  	}

	/**
	 * force to set cache data based on params and ignore disable cache setting
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param mixed $language_id
	 * @param mixed $store_id
	 * @return null
	 */
	public function force_set( $key, $value, $language_id = '', $store_id = '' ) {
		return $this->set($key, $value, $language_id, $store_id, true );
	}

	/**
	 * set cache parameter
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param mixed $language_id
	 * @param mixed $store_id
	 * @param bool $create_override
	 * @return null
	 */
	public function set($key, $value, $language_id = '', $store_id = '', $create_override = false) {

    	$this->delete($key, $language_id, $store_id );
    	if($this->registry->get('request')->get['rt']=='tool/cache'){
    		return null;
    	}
    	//validate key for / and \ 
    	if(strstr($key, '/') || strstr($key, '\\') ){
    		return null;    	
    	}
		if ($create_override || $this->registry->get('config')->get('config_cache_enable')){	
			//build new cache file name
			$ch_base = $this->_build_name($key, $language_id, $store_id);
			$timestamp = (time() + $this->expire);
			$file =  $ch_base . '.' . $timestamp;
			// write into cache map
			$this->cache_map[$ch_base] = $timestamp;
			//create subdirectory if needed
			$this->_test_create_directory($key);
			$handle = fopen($file, 'w');
		   	fwrite($handle, serialize($value));				
		   	fclose($handle);
		}
  	}

	/**
	 * @param string $key
	 * @param mixed $language_id
	 * @param mixed $store_id
	 */
	public function delete($key, $language_id = '', $store_id = '') {
  		$section = substr($key, 0,strpos($key, '.'));
  		if ($section) {
  			//delete match within directory
			$files = glob($this->_build_name($key, $language_id, $store_id) . '.*', GLOB_NOSORT);
  		} else {
  			//delete whole content of directory for section/key
  			$files = glob(DIR_CACHE . $key . '/*', GLOB_NOSORT);
  		}
		if ($files) {
    		foreach ($files as $file) {
			if(pathinfo($file,PATHINFO_FILENAME) == 'index.html'){ continue; }
      				if (file_exists($file)) {      				
					$this->_remove($file);
					//clear cache map
					$ch_base = substr($file,0,-11);
					unset($this->cache_map[$ch_base]);
				}
    			}
		}
  	}
  	
	/**
	 * function check is empty cache data. Look php empty() function for details
	 * @param string $key
	 * @param string $language_id
	 * @param string $store_id
	 * @return bool
	 */
	public function isEmpty($key, $language_id = '', $store_id = ''){
		if(isset($this->empties[$key.'_'.$language_id.'_'.$store_id])){
			return $this->empties[$key.'_'.$language_id.'_'.$store_id];
		}else{
			return false;
		}
	}

	/**
	 * function checks is cache data exists. NOTE: Check will need run after get() method run!!!
	 * @param string $key
	 * @param string $language_id
	 * @param string $store_id
	 * @return bool
	 */
	public function exists($key, $language_id = '', $store_id = ''){
		return isset($this->exists[$key.'_'.$language_id.'_'.$store_id]);
	}

	/**
	 * @param string $key
	 * @void
	 */
	private function _test_create_directory ($key) {
		//get section by first part of the key
		$section = substr($key, 0,strpos($key, '.'));
		//if no match use key as seciton 
		if ( !$section ) {
			$section = $key;
		}
		if (!is_file(DIR_CACHE . $section) && !is_dir(DIR_CACHE . $section)) {
			mkdir(DIR_CACHE . $section, 0777, true);
			$this->_chmod_dir(DIR_CACHE . $section, 0777); //change mode for nested directories
		}
	}

	/**
	 * @param string $key
	 * @param mixed $language_id
	 * @param mixed $store_id
	 * @return string
	 */
	private function _build_name($key, $language_id = '', $store_id = ''){
		//get section by first part of the key
		$section = substr($key, 0,strpos($key, '.'));
		$suffix = $this->_build_sufix($language_id, $store_id);
		//if no match use key as seciton 
		if ( !$section ) {
			$section = $key;	
		}
		return DIR_CACHE . $section . '/' . $key . ($suffix ? '.'.$suffix : '');
	}

	/**
	 * @param mixed $language_id
	 * @param mixed $store_id
	 * @return string
	 */
	private function _build_sufix($language_id = '', $store_id = ''){
		$suffix = '';
		if($language_id){
			$language_id = (int)$language_id;
		}
		if($store_id){
			$store_id = (int)$store_id;
		}
		if($language_id || $store_id){
			$suffix = $language_id.'_'.(int)$store_id;
		}
		return $suffix;
  	}


	/**
	 * @param string $file
	 * @return null
	 * @void
	 */
	private function _remove($file){
		if(empty($file)){
			return null;
		}

		unlink($file);
		//double check that the cache file to be removed
		if (file_exists($file)){
			$err_text = sprintf('Error: Cannot delete cache file: %s! Check file or directory permissions.', $file);
			$error = new AError($err_text);
			$error->toLog()->toDebug();
		}
		return null;
	}

	/**
	 * Change mode recursive
	 *
	 * @param string $path
	 * @param int $dirmode
	 * @return bool
	 */
	private function _chmod_dir($path, $dirmode) {
	    if (is_dir($path) ) {
	        if (!chmod($path, $dirmode)) {
	            $dirmode_str=decoct($dirmode);
				$this->registry->get('log')->write("Failed applying filemode '".$dirmode_str."' on directory '".$path."'\n  -> the directory '".$path."' will be skipped from recursive chmod\n");
	            return false;
	        }
	    } elseif (is_link($path)) {
			$this->registry->get('log')->write("link '".$path."' is skipped\n");
			return false;
	    }
		return true;
	}
}
