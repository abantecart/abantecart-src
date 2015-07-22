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
class ControllerResponsesContentContent extends AController {

	public function main() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		try{
			$this->config->set('embed_mode', true);
			$cntr = $this->dispatch('pages/content/content');
			$html_out = $cntr->dispatchGetOutput();
		}catch(AException $e){	}
	
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->response->setOutput($html_out);
	}	

	public function loadInfo() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadModel('catalog/content');
		if (isset($this->request->get['content_id'])) {
			$content_id = $this->request->get['content_id'];
		} else {
			if (isset($this->request->get['create'])) {
				$content_id = $this->config->get('config_account_id');
			} else {
				$content_id = $this->config->get('config_checkout_id');
			}
		}      
		$content_info = $this->model_catalog_content->getContent($content_id);

		$this->view->assign('title', $content_info['title'] );
		$this->view->assign('description', html_entity_decode($content_info['description']) );
		$this->view->assign('content', html_entity_decode($content_info['content']) );

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->processTemplate('responses/content/content.tpl' );
	}
}