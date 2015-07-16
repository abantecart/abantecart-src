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

class ControllerResponsesIndexHome extends AController {
	private $error = array();
	public $data = array();

	public function main() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		//temporаry solution for home page - top category list
		if($this->config->get('embed_mode') == true){
			$continue_url = $this->html->getURL('product/category');
			$this->redirect($continue_url);
		}

		$this->addChild('responses/embed/head', 'head');
		$this->addChild('responses/embed/footer', 'footer');
		$this->processTemplate('embed/index/home.tpl');

		$this->extensions->hk_UpdateData($this,__FUNCTION__);

	}	

}