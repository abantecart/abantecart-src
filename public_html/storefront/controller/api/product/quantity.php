<?php  
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright 2011-2015 Belavier Commerce LLC

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
class ControllerApiProductQuantity extends AControllerAPI {
	
	public function get() {
        $this->extensions->hk_InitData($this,__FUNCTION__);
        $request = $this->rest->getRequestParams();
		$responce_arr = array();

		$product_id = $request['product_id'];
		$opt_val_id = $request['option_value_id'];
		
		if (empty($product_id) || !is_numeric($product_id)) {
			$this->rest->setResponseData( array('Error' => 'Missing or incorrect format product ID') );
			$this->rest->sendResponse(200);
			return null;
		}

		if ( !$this->config->get('config_storefront_api_stock_check') ) {
			$this->rest->setResponseData( array('Error' => 'Restricted access to stock check ') );
			$this->rest->sendResponse(200);
			return null;
		}

		//Load all the data from the model
    	$this->loadModel('catalog/product');
		$product_info = $this->model_catalog_product->getProduct( $product_id );
		if ( count ($product_info) <= 0 ) {
			$this->rest->setResponseData( array('Error' => 'No product found') );
			$this->rest->sendResponse(200);	
			return null;
		}
		//filter data and return only QTY for product and option values
		
		$responce_arr['quantity'] = $product_info['quantity'];
		$responce_arr['stock_status'] = $product_info['stock_status'];		
		if ($product_info['quantity'] <= 0) {
		    $responce_arr['quantity'] = 0;
		}
					
		$product_info['options'] = $this->model_catalog_product->getProductOptions($product_id);	
		foreach ($product_info['options'] as $option) {
			foreach ($option['option_value'] as $option_val) {
				$responce_arr['option_value_quantities'][] = array( 
						'product_option_value_id' => $option_val['product_option_value_id'],
						'quantity' => $option_val['quantity']
						);
			}
		}

		if (isset($opt_val_id)) {		
			//replace and return only option value qtty
			foreach ($responce_arr['option_value_quantities'] as $option_val) {
				if ( $option_val['product_option_value_id'] == $opt_val_id ) {
					$responce_arr = array();
					$responce_arr = $option_val;
					if ($responce_arr['quantity'] <= 0) {
		    			$responce_arr['quantity'] = 0;
					}
					break;
				}
			}
		}	

        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->rest->setResponseData( $responce_arr );
		$this->rest->sendResponse(200);
	}
		
}