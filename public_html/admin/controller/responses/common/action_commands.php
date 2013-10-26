<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2013 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>
  
 UPGRADE NOTE: 
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.  
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE') || !IS_ADMIN) {
	header('Location: static_pages/');
}
class ControllerResponsesCommonActionCommands extends AController {
	private $error = array();
	public $commands = array();

	//main method to load commands 
	public function main() {
		$result = array();
		$text_data = $this->language->getASet('common/action_commands');	
		$keys = preg_grep("/^command.*/", array_keys($text_data));
		foreach($keys as $key) {
			$this->commands[$key] = $text_data[$key];
		}
		unset($text_data);
		unset($keys);

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		//load all commands from languages. 
		$term = $this->request->get['term'];
		if ( !$term ) {			
			$this->extensions->hk_UpdateData($this, __FUNCTION__);
			return $this->_no_match();
		}

		//search for possible commands
		foreach($this->commands as $key => $command){
			$variations = explode(',', $command);
			//loor for command in the term
			foreach ($variations as $test) {
				$test = trim($test);
				preg_match("/^$test\s*(.*)/i", $term, $matches);
				if (count($matches)) {
					$result['command'] = $test;
					$result['key'] = $key;
					$result['request'] = $matches[1];
					//no breack. Take last matching command
				}
			}			
		} 
		
		if ( !$result ) {			
			$this->extensions->hk_UpdateData($this, __FUNCTION__);
			return $this->_no_match();
		}else{
			//call method to perform action on the request in the command
			$function = "_".$result['key'];
			if( method_exists( $this, $function )){
				$result['found_actions'] = $this->$function($result['request']);
			} else {
				$this->extensions->hk_UpdateData($this, __FUNCTION__);
				return $this->_no_match();			
			}
		}

		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($result));		
	}

	private function _no_match() {
		$result = array();
		$result['message'] = $this->language->get('text_possible_commands');
		//load all possible commands from language definitions.		
		foreach($this->commands as $command){
			$result['commands'][] = $command;
		}
		$this->load->library('json');
		$this->response->setOutput(AJson::encode($result));
		return null;
	}

	private function _command_open( $request ) {
		//return format (array): url =>, title =>, confirmation => (true, false)
		$result = array();
		//remove junk words 
		$request = preg_replace('/menu|tab|page/', '', $request);
		$request = trim($request);
		//look for page in the menu matching 
		//assume we have cache
		$menu_arr = $this->cache->get('admin_menu');
		foreach($menu_arr as $menu){
			$sub_res = array();
			if (preg_match("/$request/i", $menu['item_id'])){
				$sub_res["title"] = $this->language->get($menu['item_text']);
				$sub_res["url"] = $this->html->getSecureURL($menu['item_url']);
				$sub_res["confirmation"] = false;
				$result[] = $sub_res;
			}
		}
		
		return $result;
	}

	private function _command_find( $request ) {
		//return format (array): url =>, title =>, confirmation => (true, false)
		$result = array();
		$request = trim($request);
		
		//look return search result
		$result[0]["url"] = $this->html->getSecureURL('tool/global_search', '&search='.$request);
		$result[0]["confirmation"] = false;
		
		return $result;
	}


	private function _command_clear_cache( $request ) {
		//return format (array): url =>, title =>, confirmation => (true, false)
		$result = array();
		$request = trim($request);
		
		if ( empty($request) ) {
			$result[0]["url"] = $this->html->getSecureURL('tool/cache');
			$result[0]["confirmation"] = false;
		} else {
			$result[0]["url"] = $this->html->getSecureURL('tool/cache/delete', '&selected='.preg_replace('/\s+/',',',$request));
			$result[0]["confirmation"] = true;		
		}
		
		
		return $result;
	}

}

?>