<?php  
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Lincence details is bundled with this package in the file LICENSE.txt.
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
class ControllerApiCheckoutConfirm extends AControllerAPI {
	public $error = array();
	public $data = array();

	public function post() {
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
		$request = $this->rest->getRequestParams();
		
		if (!$this->customer->isLoggedWithToken( $request['token'] )) {
			$this->rest->sendResponse(401, array( 'error' => 'Not logged in or Login attempt failed!' ) );
			return null;
    	} 
    	
		if (!$this->cart->hasProducts()) {
			//No products in the cart.
			$this->rest->sendResponse(200, array('status' => 2, 'error' => 'Nothing in the cart!' ) );
			return null;
		}		
		if (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout')) {
			//No stock for products in the cart if tracked.
			$this->rest->sendResponse(200, array('status' => 3, 'error' => 'No stock for product!' ));
			return null;
		}

		if ($this->cart->hasShipping()) {
			if (!isset($this->session->data[ 'shipping_address_id' ]) || !$this->session->data[ 'shipping_address_id' ]) {
				//Problem. Missing shipping address
				$this->rest->sendResponse(200, array('status' => 4, 'error' => 'Missing shipping address!' ) );
				return null;
			}

			if (!isset($this->session->data[ 'shipping_method' ])) {
				//Problem. Missing shipping address
				$this->rest->sendResponse(200, array('status' => 5, 'error' => 'Missing shipping method!' ) );
				return null;
			}
		} else {
			unset($this->session->data[ 'shipping_address_id' ]);
			unset($this->session->data[ 'shipping_method' ]);
			unset($this->session->data[ 'shipping_methods' ]);

			$this->tax->setZone($this->session->data[ 'country_id' ], $this->session->data[ 'zone_id' ]);
		}
		
    	if (!isset($this->session->data['payment_address_id']) || !$this->session->data['payment_address_id']) { 
	  		$this->rest->sendResponse(200, array('status' => 6, 'error' => 'Missing payment (billing) address!' ) );
	  		return null;
    	}  
		
		if (!isset($this->session->data['payment_method'])) {
	  		$this->rest->sendResponse(200, array('status' => 5, 'error' => 'Missing payment (billing) method!' ) );
	  		return null;
    	}		

		//build order and pre-save
		$order = new AOrder( $this->registry );
		$this->data = $order->buildOrderData( $this->session->data );
		$this->session->data['order_id'] = $order->saveOrder();

		//build confirmation data 
		$this->loadModel('account/address');
		$shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);	
		if ($this->cart->hasShipping()) {
			$this->data['shipping_address'] = $this->customer->getFormatedAdress($shipping_address, $shipping_address[ 'address_format' ] );
		} else {
			$this->data['shipping_address'] = '';
		}
		
        $this->data['shipping_method'] = $this->session->data['shipping_method']['title'];
    	
    	$payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
		if ($payment_address) {
			$this->data['payment_address'] = $this->customer->getFormatedAdress($payment_address, $payment_address[ 'address_format' ] );
		} else {
			$this->data['payment_address'] = '';
		}

		if($this->session->data['payment_method']['id'] != 'no_payment_required'){
			$this->data['payment_method'] = $this->session->data['payment_method']['title'];
		}else{
			$this->data['payment_method'] = '';
		}

		$this->loadModel('tool/seo_url');
		$this->loadModel('tool/image');

		//Format product data specific for confirmation responce
        $resource = new AResource('image');
        for($i = 0; $i < sizeof( $this->data['products'] ); $i++){
        	$product_id = $this->data['products'][$i]['product_id'];
	        $thumbnail = $resource->getMainThumb('products',
			                                     $product_id,
			                                     $this->config->get('config_image_cart_width'),
			                                     $this->config->get('config_image_cart_height'),true);
	        
			$tax = $this->tax->calcTotalTaxAmount($this->data['products'][$i]['total'], $this->data['products'][$i]['tax_class_id']);
      		$this->data['products'][$i] = array_merge( 
      			$this->data['products'][$i], 
      			array(
        		'thumb'    => $thumbnail['thumb_url'],
				'tax'        => $this->currency->format($tax),
        		'price'      => $this->currency->format($this->data['products'][$i]['price']),
        		'total'      => $this->currency->format($this->data['products'][$i]['total'])
      		)); 
        }

        if ($this->config->get('config_checkout_id')) {
			$this->loadModel('catalog/content');
			$content_info = $this->model_catalog_content->getContent($this->config->get('config_checkout_id'));
			if ($content_info) {
				$this->data['text_accept_agree'] = sprintf($this->language->get('text_accept_agree'), '', $content_info['title']);
			} else {
				$this->data['text_accept_agree'] = '';
			}
		} else {
			$this->data['text_accept_agree'] = '';
		}
				
		// Load selected paymnet required data from payment extension
		if($this->session->data['payment_method']['id'] != 'no_payment_required'){
			$payment_controller = $this->dispatch( 'responses/extension/' . $this->session->data['payment_method']['id'] . '/api' );
		}else{
			$payment_controller = $this->dispatch( 'responses/checkout/no_payment/api' );
		}

		$this->load->library('json');
		$this->data['payment'] = AJson::decode( $payment_controller->dispatchGetOutput(), TRUE );
		//set process_rt for process step to run the payment 	
		$this->session->data['process_rt'] = $this->data['payment']['process_rt'];
		//mark confirmation viewed
		$this->session->data['confirmed'] = TRUE;
				
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
		
		$this->rest->setResponseData( $this->data );
		$this->rest->sendResponse( 200 );		
	}
	
}