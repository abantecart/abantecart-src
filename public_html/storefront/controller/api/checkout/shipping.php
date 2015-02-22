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
class ControllerApiCheckoutShipping extends AControllerAPI {
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
		$this->loadLanguage('checkout/shipping');
		if ( $request['mode'] == 'select' && $this->validate( $request ) ) {
			$shipping = explode('.', $request[ 'shipping_method' ]);
			$this->session->data[ 'shipping_method' ] = $this->session->data[ 'shipping_methods' ][ $shipping[ 0 ] ][ 'quote' ][ $shipping[ 1 ] ];
			$this->session->data[ 'comment' ] = strip_tags($request[ 'comment' ]);

			//process data
			$this->extensions->hk_ProcessData($this);

			$this->rest->sendResponse( 200, array('status' => 1, 'shipping_select' => 'success') );
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
		
		if (!$this->cart->hasShipping()) {
			unset($this->session->data[ 'shipping_address_id' ]);
			unset($this->session->data[ 'shipping_method' ]);
			unset($this->session->data[ 'shipping_methods' ]);

			$this->tax->setZone($this->session->data[ 'country_id' ], $this->session->data[ 'zone_id' ]);
			$this->rest->sendResponse( 200, array('status' => 0, 'shipping' => 'products do not require shipping') );
			return null;
		}

		if (!isset($this->session->data[ 'shipping_address_id' ])) {
			$this->session->data[ 'shipping_address_id' ] = $this->customer->getAddressId();
		}

		if (!$this->session->data[ 'shipping_address_id' ]) {
			//Problem. Missing shipping address
			$this->rest->sendResponse(200, array('status' => 4, 'error' => 'Missing shipping address!' ) );
			return null;
		}

		$this->loadModel('account/address');

		$shipping_address = $this->model_account_address->getAddress($this->session->data[ 'shipping_address_id' ]);

		if (!$shipping_address) {
			//Problem. Missing shipping address
			$this->rest->sendResponse(500, array('status' => 4, 'error' => 'Inaccessible shipping address!' ) );
			return null;
		}

		// if tax zone is taken from shipping address
		if (!$this->config->get('config_tax_customer')) {
			$this->tax->setZone($shipping_address[ 'country_id' ], $shipping_address[ 'zone_id' ]);
		} else { // if tax zone is taken from billing address
			$address = $this->model_account_address->getAddress($this->customer->getAddressId());
			$this->tax->setZone($address[ 'country_id' ], $address[ 'zone_id' ]);
		}

		$this->loadModel('checkout/extension');

		if (!isset($this->session->data[ 'shipping_methods' ]) || !$this->config->get('config_shipping_session')) {
			$quote_data = array();

			$results = $this->model_checkout_extension->getExtensions('shipping');
			foreach ($results as $result) {
				$this->loadModel('extension/' . $result[ 'key' ]);

				$quote = $this->{'model_extension_' . $result[ 'key' ]}->getQuote($shipping_address);

				if ($quote) {
					$quote_data[ $result[ 'key' ] ] = array(
						'title' => $quote[ 'title' ],
						'quote' => $quote[ 'quote' ],
						'sort_order' => $quote[ 'sort_order' ],
						'error' => $quote[ 'error' ]
					);
				}
			}

			$sort_order = array();

			foreach ($quote_data as $key => $value) {
				$sort_order[ $key ] = $value[ 'sort_order' ];
			}

			array_multisort($sort_order, SORT_ASC, $quote_data);

			$this->session->data[ 'shipping_methods' ] = $quote_data;
		}

		$this->data[ 'error_warning' ] = $this->error[ 'warning' ];

		if (isset($this->session->data[ 'shipping_methods' ]) && !$this->session->data[ 'shipping_methods' ]) {
			$this->data[ 'error_warning' ] = $this->language->get('error_no_shipping');
		}

		$this->data[ 'address' ] = $this->customer->getFormatedAdress($shipping_address, $shipping_address[ 'address_format' ] );		
		$this->data[ 'shipping_methods' ] = $this->session->data[ 'shipping_methods' ] 	? $this->session->data[ 'shipping_methods' ] : array();
		$this->data[ 'comment' ] = isset($request[ 'comment' ]) ? $request[ 'comment' ] : $this->session->data[ 'comment' ];
		
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
		
		$this->rest->setResponseData( $this->data );
		$this->rest->sendResponse( 200 );
	}
	
	public function validate( $request ) {
		if (!isset($request[ 'shipping_method' ])) {
			$this->error[ 'warning' ] = $this->language->get('error_shipping');
		} else {
			$shipping = explode('.', $request[ 'shipping_method' ]);
			if (!isset($this->session->data[ 'shipping_methods' ][ $shipping[ 0 ] ][ 'quote' ][ $shipping[ 1 ] ])) {
				$this->error[ 'warning' ] = $this->language->get('error_shipping');
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
}