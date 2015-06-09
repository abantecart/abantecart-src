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

		$this->data['abc_embed_test_cookie_url'] = $this->html->getURL('r/embed/js/testcookie','&timestamp='.time());

		$this->view->setTemplate( 'embed/js.tpl' );
		$this->view->batchAssign($this->data);
		$this->response->addHeader('Content-Type: text/javascript; charset=UTF-8');
        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);		
	}

	/**
	 * Method fill data into embedded block with single product
	 */
	public function product() {
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$product_id = (int)$this->request->get['product_id'];
		if(!$product_id){
			return null;
		}

		$target = $this->request->get['target'];
		if(!$target){
			return null;
		}

		$this->loadModel('catalog/product');
		$product_info = $this->model_catalog_product->getProduct($product_id);
		//can not locate product? get out
		if (!$product_info) { 
			return null;
		}


		$resource = new AResource('image');
		$product_info['thumbnail'] =  $resource->getMainThumb('products',
				$product_id,
			(int)$this->config->get('config_image_grid_width'),
			(int)$this->config->get('config_image_grid_height'),
		    true);

		$product_info['price'] = $this->currency->format($product_info['price']);
		$product_info['button_addtocart'] = $this->html->buildElement(
				array(
						'type' => 'button',
						'name' => 'addtocart',
						'text' => $this->language->get('button_add_to_cart'),

						'attr' => 'data-product-id="'.$product_id.'" data-href = "'. $this->html->getURL('r/embed/js/addtocart', '&product_id='.$product_id).'"'
					)
		);


		$this->data['product'] = $product_info;
		/*$this->data['product_details_url'] = $this->html->getURL(
													'r/product/product',
													'&product_id=' . $product_id);*/
		$this->data['product_details_url'] = $this->html->getURL(
															'product/product',
															'&product_id=' . $product_id);

		$this->view->setTemplate( 'embed/js_product.tpl' );
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
		$this->response->addHeader('Content-Type: text/javascript; charset=UTF-8'); 
		$this->view->batchAssign($this->data);
        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
	
	public function cart() {
		$this->extensions->hk_InitData($this, __FUNCTION__);

		//????  temporary part of html for floating cart block
		if($this->cart->getProducts()){
			$d = $this->dispatch('responses/product/product/getCartContent');
			$this->load->library('json');
			$this->data['cart_html'] = AJson::decode($d->dispatchGetOutput(), true);
			$this->data['cart_html'] = $this->data['cart_html']['cart_details'];
		}




		$this->view->setTemplate( 'embed/js_cart.tpl' );
		$this->response->addHeader('Content-Type: text/javascript; charset=UTF-8'); 
		$this->view->batchAssign($this->data);
        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
	public function addtocart() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadModel('catalog/product');
		$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
		if($product_info){
			$this->cart->add($this->request->get['product_id'], ($product_info['minimum'] ? $product_info['minimum'] : 1));
		}

		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}
  	
}