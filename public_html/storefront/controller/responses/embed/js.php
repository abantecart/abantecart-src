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
class ControllerResponsesEmbedJS extends AController {

	public $data = array();

	/**
	 * NOTE: main() is bootup method
	 */
	public function main() {
		$this->extensions->hk_InitData($this, __FUNCTION__);


		//check is third-party cookie allowed
		if(!isset($this->request->cookie[SESSION_ID])){
			$this->data['test_cookie'] = true;
		}

		$this->view->setTemplate( 'embed/js.tpl' );
		$this->view->batchAssign($this->data);
        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);		
	}

	public function product() {
		$this->extensions->hk_InitData($this, __FUNCTION__);

		//check is third-party cookie allowed
		if(!isset($this->request->cookie[SESSION_ID])){
			$this->data['test_cookie'] = true;
		}

		$product_id = (int)$this->request->get['product_id'];
		if(!$product_id){
			return;
		}
		
		$this->data['abc_embed_product_url'] = $this->html->getURL('r/product/product','&product_id=' . $product_id);
		$this->data['abc_embed_test_cookie_url'] = $this->html->getURL('r/embed/js/testcookie','&timestamp='.time());

		$this->loadModel('catalog/product');
		$product_info = $this->model_catalog_product->getProduct($product_id);
		//can not locate product? get out
		if (!$product_info) { 
			return null;
		}
		//optimize to pass only needed data
		$this->data['product'] = $product_info;

		$this->view->setTemplate( 'embed/js.tpl' );
		$this->view->batchAssign($this->data);
		$this->response->addHeader('Content-Type: text/javascript; charset=UTF-8');

        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);		
	}

	public function testCookie() {
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->data['allowed'] = $this->request->cookie[SESSION_ID] ? true : false;
		$this->data['abc_token'] = session_id();


		$this->view->setTemplate( 'embed/js_cookie_check.tpl' );
		$this->response->addHeader('Content-Type: text/javascript; charset=UTF-8'); //needed for debug in firebug
		$this->view->batchAssign($this->data);
        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
  	
}