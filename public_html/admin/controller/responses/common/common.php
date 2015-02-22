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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ControllerResponsesCommonCommon extends AController {
	private $error = array(); 
	    
  	public function main() {
          //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}

	/**
	 * function for getting auto-generated unique seo keyword
	 */
	public function getSeoKeyword(){
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);
		$seo_key = SEOEncode($this->request->get['seo_name'],
							$this->request->get['object_key_name'],
							(int)$this->request->get['id'],
							(int)$this->language->getContentLanguageID());

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->response->setOutput($seo_key);
	}

	/**
	 * function to mark ANT message read
	 */
	public function antMessageRead(){
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$message_id = $this->request->get['message_id'];

		$result = array();
		if( has_value($message_id) && $this->messages->markViewedANT($message_id, '*')) {
			$result['success'] = true;
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($result));		
	}
	/**
	 * void function run server-server update check procedure
	 */
	public function checkUpdates(){
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadModel('tool/updater');
		$this->model_tool_updater->check4Updates();
		unset($this->session->data['checkupdates']); // was set in index/login

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}
}