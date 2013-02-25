<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011, 2012 Belavier Commerce LLC

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
  	public function __construct() {
		$files = glob(DIR_CACHE . 'cache.*', GLOB_NOSORT);
		$this->registry = Registry::getInstance ();		
		if ($files) {
			foreach ($files as $file) {
				$time = substr(strrchr($file, '.'), 1);

      			if ($time < time()) {
					if (file_exists($file)) {
						unlink($file);
					}
      			}
    		}
		}
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
			return null;
		}

		$suffix = $this->_build_sufix($language_id, $store_id);
		$cache_filename = DIR_CACHE . 'cache.' . $key . ($suffix ? '.'.$suffix : '');
		$cache_files = glob( $cache_filename. '.*', GLOB_NOSORT);

		if ($cache_files) {
    		foreach ($cache_files as $file) {
				$ch_base = substr($file,0,-11); // remove timestamp with dot from the end of file name TODO: check this spike
				if(strlen($cache_filename) == strlen($ch_base)){
					$handle = fopen($file, 'r');
					$cache = fread($handle, filesize($file));
					fclose($handle);
					return unserialize($cache);
				}
   		 	}
		}else{
			return null;
		}	
  	}

	//force to set cache data based on params and ignore disable cache setting
	public function force_set( $key, $value, $language_id = '', $store_id = '' ) {
		return $this->set($key, $value, $language_id, $store_id, true );
	}

	//set cache parameter
  	public function set($key, $value, $language_id = '', $store_id = '', $create_override = false) {

		$suffix = $this->_build_sufix($language_id, $store_id);

    	$this->delete($key, $language_id, $store_id );

    	if($this->registry->get('request')->get['rt']=='tool/cache'){
    		return null;
    	}
    			
		if ($create_override || $this->registry->get('config')->get('config_cache_enable')){	
			$file = DIR_CACHE . 'cache.' . $key . ($suffix ? '.'.$suffix : '') . '.' . (time() + $this->expire);
			$handle = fopen($file, 'w');		
		   	fwrite($handle, serialize($value));				
		   	fclose($handle);
		}
  	}
	
  	public function delete($key, $language_id = '', $store_id = '') {
		$suffix = '';
		$suffix = $this->_build_sufix($language_id, $store_id);

		$files = glob(DIR_CACHE . 'cache.' . $key . ($suffix ? '.'.$suffix : '') . '.*', GLOB_NOSORT);
		
		if ($files) {
    		foreach ($files as $file) {
      			if (file_exists($file)) {      				
					unlink($file);
				}
    		}
		}
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
?>