<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2013, 2012 Belavier Commerce LLC

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

final class ACache { 
	private $expire = 86400; //one day
	private $registry;
	private $empties = array();
	private $exists = array();
  	public function __construct() {
		$this->registry = Registry::getInstance ();		
  	}

	//force to get cache data based on params and ignore disable cache setting
	public function force_get($key, $language_id = '', $store_id = '' ) {
		return $this->get($key, $language_id, $store_id, true );
	}
	
	//get cache data based on params. 
	public function get($key, $language_id = '', $store_id = '', $disabled_override = false) {
		//clean up if disabled cache
		if (!$disabled_override && !$this->registry->get('config')->get('config_cache_enable')){
			$this->delete($key, $language_id, $store_id );
			unset($this->empties[$key.'_'.$language_id.'_'.$store_id]);
			return null;
		}
		//load all cache file names for the section (key)
		$cache_filename =  $this->_build_name($key, $language_id, $store_id);
		$cache_files = glob( $cache_filename. '.*', GLOB_NOSORT);
	
		if ($cache_files) {
    		foreach ($cache_files as $file) {
    			//Check if file expired and deleted it
    			$file_time = substr(strrchr($file, '.'), 1);
      			if ($file_time < time()) {
					if (file_exists($file)) {
						unlink($file);
						continue;
					}
      			}
      			    			
    			// remove timestamp with dot from the end of file name TODO: check this spike
				$ch_base = substr($file,0,-11); 
				if(strlen($cache_filename) == strlen($ch_base)){
					$handle = fopen($file, 'r');
					$cache = fread($handle, filesize($file));
					fclose($handle);
					$output = unserialize($cache);
					$this->empties[$key.'_'.$language_id.'_'.$store_id] = !empty($output); // if not empty
					$this->exists[$key.'_'.$language_id.'_'.$store_id] = true;
					return $output;
				}
   		 	}
		}
		unset($this->empties[$key.'_'.$language_id.'_'.$store_id]);
		return null;
  	}

	//force to set cache data based on params and ignore disable cache setting
	public function force_set( $key, $value, $language_id = '', $store_id = '' ) {
		return $this->set($key, $value, $language_id, $store_id, true );
	}

	//set cache parameter
  	public function set($key, $value, $language_id = '', $store_id = '', $create_override = false) {

    	$this->delete($key, $language_id, $store_id );

    	if($this->registry->get('request')->get['rt']=='tool/cache'){
    		return null;
    	}
    			
		if ($create_override || $this->registry->get('config')->get('config_cache_enable')){	
			//build new cache file name
			$file = $this->_build_name($key, $language_id, $store_id) . '.' . (time() + $this->expire);
			//create subdirectory if needed
			$this->_test_create_directory($key);
			$handle = fopen($file, 'w');		
		   	fwrite($handle, serialize($value));				
		   	fclose($handle);
		}
  	}
	
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
      			if (file_exists($file)) {      				
					unlink($file);
				}
    		}
		}
  	}
  	
	/**
	 * funtion check is empty cache data. Look php empty() function for details
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


	private function _test_create_directory ($key) {
		//get section by first part of the key
		$section = substr($key, 0,strpos($key, '.'));
		//if no match use key as seciton 
		if ( !$section ) {
			$section = $key;
		}
		if (!is_file(DIR_CACHE . $section) && !is_dir(DIR_CACHE . $section)) {
			mkdir(DIR_CACHE . $section, 0777);
		}

	}

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

}
