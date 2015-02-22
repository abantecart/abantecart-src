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
class ControllerApiCheckoutPayment extends AControllerAPI {
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

		if ( $request['mode'] != 'select' && $request['mode'] != 'list' ) {
			$this->rest->sendResponse(400, array( 'error' => 'Incorrect request mode!' ) );
			return null;
		}

		//load language from main section
		$this->loadLanguage('checkout/payment');		
		//check coupon
		if ( isset($request[ 'coupon' ]) && $this->_validateCoupon( $request[ 'coupon' ] ) ) {
			//process data
			$this->extensions->hk_ProcessData($this);
			$this->session->data[ 'coupon' ] = $request[ 'coupon' ];
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

		$this->loadModel('account/address');
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

		if (!isset($this->session->data[ 'payment_address_id' ]) && isset($this->session->data[ 'shipping_address_id' ]) && $this->session->data[ 'shipping_address_id' ]) {
			$this->session->data[ 'payment_address_id' ] = $this->session->data[ 'shipping_address_id' ];
		}

		if (!isset($this->session->data[ 'payment_address_id' ])) {
			$this->session->data[ 'payment_address_id' ] = $this->customer->getAddressId();
		}

		if (!$this->session->data[ 'payment_address_id' ]) {
			//Problem. Missing shipping address
			$this->rest->sendResponse(200, array('status' => 6, 'error' => 'Missing billing address!' ) );
			return null;
		}

		$this->loadModel('account/address');
		$payment_address = $this->model_account_address->getAddress($this->session->data[ 'payment_address_id' ]);
		if (!$payment_address) {
			//Problem. Missing shipping address
			$this->rest->sendResponse(500, array('status' => 6, 'error' => 'Inaccessible billing address!' ) );
			return null;
		}
		if (!$this->cart->hasShipping() || $this->config->get('config_tax_customer')) {
			$this->tax->setZone($payment_address[ 'country_id' ], $payment_address[ 'zone_id' ]);
		}

		$this->loadModel('checkout/extension');
		$method_data = array();
		$results = $this->model_checkout_extension->getExtensions('payment');

		//TODO Load peyment methods that support API
		foreach ($results as $result) {
			$this->loadModel('extension/' . $result[ 'key' ]);
			$method = $this->{'model_extension_' . $result[ 'key' ]}->getMethod($payment_address);
			if ($method) {
				$method_data[ $result[ 'key' ] ] = $method;
			}
		}

		$this->session->data[ 'payment_methods' ] = $method_data;

		if ( $request['mode'] = 'select' && $this->_validate( $request ) ) {
			$this->session->data[ 'payment_method' ] = $this->session->data[ 'payment_methods' ][ $request[ 'payment_method' ] ];
			$this->session->data[ 'comment' ] = strip_tags($request[ 'comment' ]);

			//process data
			$this->extensions->hk_ProcessData($this);

			$this->rest->sendResponse( 200, array('status' => 1, 'payment_select' => 'success') );
			return null;
		}

		//build data for return
		if (isset($this->session->data[ 'error' ])) {
			$this->data['error_warning'] = $this->session->data[ 'error' ];
			unset($this->session->data[ 'error' ]);
		} else {
			$this->data['error_warning'] = $this->error[ 'warning' ];
		}
		$this->data['success'] = $this->session->data[ 'success' ];
		$this->data[ 'coupon' ] = isset($request[ 'coupon' ]) ? $request[ 'coupon' ] : $this->session->data[ 'coupon' ];
		$this->data[ 'address' ] = $this->customer->getFormatedAdress($payment_address, $payment_address[ 'address_format' ] );
		$this->data['payment_methods'] = $this->session->data[ 'payment_methods' ];
		if( $this->data['payment_methods']){
			$this->data['payment_method'] = isset($request[ 'payment_method' ]) ? $request[ 'payment_method' ] : $this->session->data[ 'payment_method' ][ 'id' ];
		} else {
			$this->data['payment_methods'] = array();
		}
		$this->data['comment'] = isset($request[ 'comment' ]) ? $request[ 'comment' ] : $this->session->data[ 'comment' ];
		
		if ($this->config->get('config_checkout_id')) {
			$this->loadModel('catalog/content');
			$content_info = $this->model_catalog_content->getContent($this->config->get('config_checkout_id'));
			if ($content_info) {
				$this->data['text_agree'] = sprintf($this->language->get('text_agree'), '' , $content_info[ 'title' ]);
			} else {
				$this->data['text_agree'] = '';
			}
		} else {
			$this->data['text_agree'] = '';
		}		
		$this->data['agree'] = $request[ 'agree' ];
		
		
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
		
		$this->rest->setResponseData( $this->data );
		$this->rest->sendResponse( 200 );		

	}

	private function _validate( $request ) {

		if (!isset($request[ 'payment_method' ])) {
			$this->error[ 'warning' ] = $this->language->get('error_payment');
			return FALSE;
		} else {
			if (!isset($this->session->data[ 'payment_methods' ][ $request[ 'payment_method' ] ])) {
				$this->error[ 'warning' ] = $this->language->get('error_payment');
				return FALSE;
			}
		}

		if ($this->config->get('config_checkout_id')) {
			$this->loadModel('catalog/content');

			$content_info = $this->model_catalog_content->getContent($this->config->get('config_checkout_id'));

			if ($content_info) {
				if (!isset($request[ 'agree' ])) {
					$this->error[ 'warning' ] = sprintf($this->language->get('error_agree'), $content_info[ 'title' ]);
					return FALSE;
				}
			}
		}

		//validate post data
		$this->extensions->hk_ValidateData($this);

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	private function _validateCoupon( $coupon ) {
		$promotion = new APromotion();
		$coupon = $promotion->getCouponData( $coupon );
		if (!$coupon) {
			$this->error[ 'warning' ] = $this->language->get('error_coupon');
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}