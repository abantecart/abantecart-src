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
class ControllerApiCheckoutAddress extends AControllerAPI {
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

		//load language from main section
		$this->loadLanguage('checkout/address');	
		$this->loadModel('account/address');
		
		if ( $request['mode'] == 'shipping' ) {

			if (!$this->cart->hasShipping()) {
				$this->rest->sendResponse( 200, array('status' => 0, 'shipping' => 'products do not require shipping') );
				return null;
			}
		
			if ( isset($request['address_id'])) {
				$this->session->data['shipping_address_id'] = $request['address_id'];
				unset($this->session->data['shipping_methods']);
				unset($this->session->data['shipping_method']);
				
				if ($this->cart->hasShipping()) {
					$address_info = $this->model_account_address->getAddress($request['address_id']);
				
					if ($address_info) {
						$this->tax->setZone($address_info['country_id'], $address_info['zone_id']);
					}
				}
				
				$this->rest->sendResponse( 200, array('status' => 1, 'shipping' => 'shipping address selected') );
				return null;
			}

		   	if ( $request['action'] == 'save' ) {
				$this->error = $this->model_account_address->validateAddressData($request);
	    		if ( !$this->error ) {			
					$this->session->data['shipping_address_id'] = $this->model_account_address->addAddress( $request );
					unset($this->session->data['shipping_methods']);
					unset($this->session->data['shipping_method']);
		
					if ($this->cart->hasShipping()) {
						$this->tax->setZone($request['country_id'], $request['zone_id']);
					}	
	
					$this->rest->sendResponse( 200, array('status' => 1, 'shipping' => 'shipping address selected') );
					return null;
				}
			}
			
			$this->data['selected_address_id'] = $this->session->data['shipping_address_id'];	
			$this->_build_responce_data( $request );

		}
		else if ( $request['mode'] == 'payment' ) {
		
	    	if ( isset($request['address_id']) ) {
				$this->session->data['payment_address_id'] = $request['address_id'];
		  		
				unset($this->session->data['payment_methods']);
				unset($this->session->data['payment_method']);
				
				$this->rest->sendResponse( 200, array('status' => 1, 'payment' => 'payment address selected') );
				return null;
			} 
		   
		   	if ( $request['action'] == 'save' ) {
				$this->error = $this->model_account_address->validateAddressData($this->request->post);
		    	if ( !$this->error ) {			
					$this->session->data['payment_address_id'] = $this->model_account_address->addAddress($this->request->post);
			  		
					unset($this->session->data['payment_methods']);
					unset($this->session->data['payment_method']);
					
					$this->rest->sendResponse( 200, array('status' => 1, 'payment' => 'payment address selected') );
					return null;
		    	}
	    	}
	    	
			$this->data['selected_address_id'] = $this->session->data['payment_address_id'];	
			$this->_build_responce_data( $request );
		}
		
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->rest->setResponseData( $this->data );
		$this->rest->sendResponse( 200 );		
	}
	

	private function _build_responce_data ( $request_data ){

        $addresses = array();
		$results = $this->model_account_address->getAddresses();

		foreach ($results as $result) {
      		$addresses[] = array(
        		'address_id' => $result['address_id'],
	    		'address'    => $result['firstname'] . ' ' . $result['lastname'] . ', ' . $result['address_1'] . ', ' . $result['city'] . ', ' . (($result['zone']) ? $result['zone']  . ', ' : FALSE) . (($result['postcode']) ? $result['postcode']  . ', ' : FALSE) . $result['country']
      		);
    	}
        $this->data['saved_addresses'] = $addresses;


		//Build data before responce 
		if ($this->error) {
	      	$this->data['status'] = 'error';
			$this->data['error_firstname'] = $this->error['firstname'];
			$this->data['error_lastname'] = $this->error['lastname'];
			$this->data['error_address_1'] = $this->error['address_1'];
			$this->data['error_city'] = $this->error['city'];
			$this->data['error_country'] = $this->error['country'];
			$this->data['error_zone'] = $this->error['zone'];	
		}
		
		$this->data['fields']['firstname'] = array(	'type' => 'input',
													'name' => 'firstname',
													'value' => $request_data['firstname'],
													'required' => true,
													'error' =>  $this->error['firstname']);
		$this->data['fields'][ 'lastname' ] = array(
                                                    'type' => 'input',
		                                            'name' => 'lastname',
		                                            'value' => $request_data['lastname'],
													'required' => true,
													'error' =>  $this->error['lastname']);
		$this->data['fields'][ 'company' ] = array(
                                                  	'type' => 'input',
		                                           	'name' => 'company',
		                                          	'value' => $request_data['company'],
		                                            'required' => false );
		$this->data['fields'][ 'address_1' ] = array(
                                                    'type' => 'input',
		                                            'name' => 'address_1',
		                                            'value' => $request_data['address_1'],
													'required' => true,
													'error' =>  $this->error['address_1']);
		$this->data['fields'][ 'address_2' ] = array(
                                                    'type' => 'input',
		                                            'name' => 'address_2',
		                                            'value' => $request_data['address_2'],
		                                            'required' => false );
		$this->data['fields'][ 'city' ] = array(
                                                  	'type' => 'input',
		                                            'name' => 'city',
		                                            'value' => $request_data['city'],
													'required' => true,
													'error' =>  $this->error['city']);
		$this->data['fields'][ 'postcode' ] = array(
                                                    'type' => 'input',
		                                            'name' => 'postcode',
		                                            'value' => $request_data['postcode'],
		                                            'required' => false );
		$this->loadModel('localisation/country');
        $countries = $this->model_localisation_country->getCountries();
        $options = array("FALSE" => $this->language->get('text_select') );
        foreach($countries as $item){
            $options[ $item['country_id'] ] = $item['name'];
        }
	    $this->data['fields'][ 'country_id' ] = array(
                                                    'type' => 'selectbox',
		                                            'name' => 'country_id',
                                                    'options' => $options,
		                                            'value' => ( isset($request_data['country_id']) ? $request_data['country_id'] : $this->config->get('config_country_id')),
													'required' => true,
													'error' =>  $this->v_error['country_id']);
        $this->data['fields'][ 'zone_id' ] = array(
                                                    'type' => 'selectbox',
		                                            'name' => 'zone_id',
													'required' => true,
													'value' => $request_data['zone_id'],
													'error' =>  $this->v_error['lastname']);		
		
		
	
	}

	
}