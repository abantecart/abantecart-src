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
	
	//get cache data based on params. 
	public function get($key, $language_id = '', $store_id = '') {
		//clean up if disabled cache
		if (!$this->registry->get('config')->get('config_cache_enable')){
			$this->delete($key, $language_id, $store_id );
			return null;
		}
		
		$suffix = '';
		$suffix = $this->_build_sufix($language_id, $store_id);
		$cache_files = glob(DIR_CACHE . 'cache.' . $key . ($suffix ? '.'.$suffix : '') . '.*', GLOB_NOSORT);

		if ($cache_files) {
    		foreach ($cache_files as $file) {
      			$handle = fopen($file, 'r');
      			$cache = fread($handle, filesize($file));	  
      			fclose($handle);

	      		return unserialize($cache);
   		 	}
		}else{
			return null;
		}	
  	}

  	public function set($key, $value, $language_id = '', $store_id = '') {
		$suffix = '';
		$suffix = $this->_build_sufix($language_id, $store_id);

    	$this->delete($key, $language_id, $store_id );

    	if($this->registry->get('request')->get['rt']=='tool/cache'){
    		return;
    	}
    			
		if ($this->registry->get('config')->get('config_cache_enable')){	
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