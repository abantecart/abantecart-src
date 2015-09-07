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
if (!defined('DIR_CORE') || !IS_ADMIN) {
	header('Location: static_pages/');
}
class ControllerResponsesCommonActionCommands extends AController {
	private $error = array();
	public $commands = array();

	//main method to load commands 
	public function main() {
		$result = array();
		
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		//load all commands from languages. 
		$term = $this->request->get['term'];
		if ( !$term ) {			
			$this->extensions->hk_UpdateData($this, __FUNCTION__);
			return $this->_no_match();
		}
		
		$comds_obj = new AdminCommands();
		$this->commands = $comds_obj->commands;
		$result = $comds_obj->getCommands($term);
		
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
		if ( !$result ) {			
			return $this->_no_match();
		}
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
}

?>