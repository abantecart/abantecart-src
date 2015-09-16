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
if (!defined('DIR_CORE')) {
	header('Location: static_pages/');
}

class AdminCommands {
	protected $registry;
	public $errors = 0;
	public $commands = array();
	public $action_list = array(
			'category'	=> 'catalog/category/insert',
			'product'	=> 'catalog/product/insert',
			'brand'	=>	'catalog/manufacturer/insert',			
			'manufacturer'	=>	'catalog/manufacturer/insert',			
			'download'	=>	'catalog/download',			
			'review'	=>	'catalog/review/insert',			
			'attribute'	=>	'catalog/attribute/insert',			
			'customer'	=>	'sale/customer/insert',			
			'coupon'	=>	'sale/coupon/insert',			
			'discount'	=>	'sale/coupon/insert',			
			'block'	=>	'design/blocks/insert',			
			'menu'	=>	'design/menu/insert',			
			'content'	=>	'design/content/insert',			
			'page'	=>	'design/content/insert',			
			'banner'	=>	'extension/banner_manager/insert',			
			'store'	=>	'setting/store/insert',			
			'language'	=>	'localisation/language/insert',			
			'currency'	=>	'localisation/currency/insert',			
			'location'	=>	'localisation/location/insert',			
			'tax'	=>	'localisation/tax_class/insert',			
	);

	public function __construct() {
		if (!IS_ADMIN) {
			throw new AException (AC_ERR_LOAD, 'Error: permission denied to access class AdminCommands');
		}
		$this->registry = Registry::getInstance();

		$result = array();
		$text_data = $this->language->getASet('common/action_commands');	
		$keys = preg_grep("/^command.*/", array_keys($text_data));
		foreach($keys as $key) {
			$this->commands[$key] = $text_data[$key];
		}
		unset($text_data);
		unset($keys);
	}

	public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

	public function getCommands($keyword) {
		if(!$keyword){
			return array();
		}

		//search for possible commands
		foreach($this->commands as $key => $command){
			$variations = explode(',', $command);
			//loop for command in the term
			foreach ($variations as $test) {
				$test = trim($test);
				//check exact match first
				if(strtolower($test) == strtolower($keyword)){
					$result['command'] = $test;
					$result['key'] = $key;
					$result['request'] = '';
				} 
				preg_match("/^$test\s+(.*)/iu", $keyword, $matches);
				if (count($matches)) {
					$result['command'] = $test;
					$result['key'] = $key;
					$result['request'] = $matches[1];
					//no breack. Take last matching command
				}
			}			
		} 
		
		if ( !$result ) {			
			//nothing found
			return array();
		} else {
			//call method to perform action on the request in the command
			$function = "_".$result['key'];
			if( method_exists( $this, $function )){
				//fillter duplicates and empty
				$result['found_actions'] = $this->_filter_result( $this->$function($result['request']) );
			} else {
				//no right method to process found
				return array();
			}
		}
		
		return $result;
	}

	private function _command_open( $request ) {
		//some menu text
		$this->load->language('common/header');
		
		//return format (array): url =>, title =>, confirmation => (true, false)
		$result = array();
		//remove junk words 
		$request = preg_replace('/menu|tab|page/', '', $request);
		$request = trim($request);
		//look for page in the menu matching 
		$menu = new AMenu('admin');
		$menu_arr = $menu->getMenuItems();
		if (count($menu_arr)) {
			foreach($menu_arr as $section_menu){
				$sub_res = array();
				if(is_array($section_menu)) {
					foreach($section_menu as $menu){
						//load language for prospect controller
						//Check that filename has proper name with no other special characters. 
						if ( preg_match("/[\W]+/", str_replace('/', '_', $menu['item_url'])) ) {
							$title = $this->language->get($menu['item_text']);
						} else {
							$this->load->language($menu['item_url'], 'silent');
							$title = $this->language->get($menu['item_text']) . " / " . $this->language->get('heading_title');
						}
						if (preg_match("/$request/iu", $title)){
							$sub_res["title"] = $title;
							$sub_res["url"] = $this->html->getSecureURL($menu['item_url']);
							$sub_res["confirmation"] = false;
							$result[] = $sub_res;
						}					
					}		
				}
			}
		}
		
		return $result;
	}

	private function _command_find( $request ) {
		//return format (array): url =>, title =>, confirmation => (true, false)
		$result = array();
		$request = trim($request);
		
		//future!!! check for second level request and do specific area search
		
		//return global search result
		$result[0]["url"] = $this->html->getSecureURL('tool/global_search', '&search='.$request);
		$result[0]["confirmation"] = false;
		
		return $result;
	}

	private function _command_clear_cache( $request ) {
		$result = array();
		$result[0]["url"] = $this->html->getSecureURL('tool/cache/delete', '&clear_all=all');
		$result[0]["confirmation"] = true;		
		return $result;
	}

	private function _command_view_log( $request ) {
		$result = array();
		$request = trim($request);
		
		$result[0]["url"] = $this->html->getSecureURL('tool/error_log');
				
		return $result;
	}

	private function _command_clear_log( $request ) {
		$result = array();
		$request = trim($request);
		
		$result[0]["url"] = $this->html->getSecureURL('tool/error_log/clearlog');
		$result[0]["confirmation"] = true;
				
		return $result;
	}

	private function _command_view_product( $request ) {
		$result = array();
		$request = trim($request);
		
		if (is_numeric($request)) {
			$result[0]["url"] = $this->html->getSecureURL('catalog/product/update', '&product_id='.$request);
		} else {
			$result[0]["url"] = $this->html->getSecureURL('catalog/product');		
		}
		return $result;
	}

	private function _command_view_order( $request ) {
		$result = array();
		$request = trim($request);
		
		if (is_numeric($request)) {
			$result[0]["url"] = $this->html->getSecureURL('sale/order/details', '&order_id='.$request);
		} else {
			$result[0]["url"] = $this->html->getSecureURL('sale/order');		
		}
		return $result;
	}

	private function _command_create_new( $request ) {
		//return format (array): url =>, title =>, confirmation => (true, false)
		$result = array();
		$request = trim($request);
		foreach ($this->action_list as $key => $rt) {
			if (preg_match("/$request/iu",  $this->language->get($key))){
			    $sub_res["title"] = $key;
			    $sub_res["url"] = $this->html->getSecureURL($rt);
			    $sub_res["confirmation"] = false;
			    $result[] = $sub_res;
			}
		}
		return $result;	
	}	

	private function _filter_result($data) {
		if ( empty($data)) {
			return array();
		}
		$ret = array();
	
		foreach ($data as $record) {
			if ( !empty($record["url"]) && preg_match("/rt=/", $record["url"]) ) {
				//check if already present in result
				$skip = false;
				foreach($ret as $value) {
					if ( $value["url"] ==  $record["url"]) {
						$skip = true;
						break;
					}
				}
				if ( !$skip ) {
					$ret[] = $record;
				}
			}
		}
		
		return $ret;
	}

}

