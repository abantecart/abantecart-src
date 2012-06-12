<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

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

final class AConfig {
	private $data = array();

	/**
	 * get data from config
	 *
	 * @param $key - data key
	 * @return requested data; null if no data wit such key
	 */
  	public function get($key) {
    	return (isset($this->data[$key]) ? $this->data[$key] : NULL);
  	}	

	/**
	 * add data to config.
	 *
	 * @param $key - access key
	 * @param $value - data to store in config
	 * @return void
	 */
	public function set($key, $value) {
    	$this->data[$key] = $value;
  	}

	/**
	 * check if data exist in config
	 *
	 * @param $key
	 * @return bool
	 */
	public function has($key) {
    	return isset($this->data[$key]);
  	}

	/**
	 * load data from config and merge with current data set
	 *
	 * @throws AException
	 * @param $filename
	 * @return void
	 */
  	public function load($filename) {
		$file = DIR_CONFIG . $filename . '.php';
		
    	if (file_exists($file)) { 
	  		$cfg = array();
	  
	  		require($file);
	  
	  		$this->data = array_merge($this->data, $cfg);
		} else {
			throw new AException(AC_ERR_LOAD, 'Error: Could not load config ' . $filename . '!');
		}
  	}

}
?>