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
class ControllerApiCheckoutProcess extends AControllerAPI {
	public $error = array();
	public $data = array();

	public function post() {

		$request = $this->rest->getRequestParams();
		
		if (!$this->customer->isLoggedWithToken( $request['token'] )) {
			$this->rest->sendResponse(401, array( 'error' => 'Not logged in or Login attempt failed!' ) );
			return null;
    	} 

		//Check if confirmation details were reviewed. 
		if ( !$this->session->data['confirmed'] ) {
			$this->rest->sendResponse(400, array( 'status' => 0, 'error' => 'Need to review confirmation details first!' ) );
			return null;
		}
		$this->session->data['confirmed'] = FALSE;
		
		//Check if order is created and process payment
		if (!isset($this->session->data['order_id'])) {
			$this->rest->sendResponse(500, array( 'status' => 2, 'error' => 'Not order data available!' ) );
			return null;
		}
		
		$order = new AOrder( $this->registry );
		$order_data = $order->loadOrderData( $this->session->data['order_id'], 'any' );
		//Check if order is present and not processed yet
		if ( !isset( $order_data )) {
			$this->rest->sendResponse(500, array( 'status' => 3, 'error' => 'No order available. Something went wrong!' ) );
			return null;
		}
		if ( $order_data['order_status_id'] > 0 ) {
			$this->rest->sendResponse(200, array(  'status' => 4, 'error' => 'Order was already processed!' ) );
			return null;
		}
		
				
		//Dispatch the payment send controller process and capture the result
		if ( !$this->session->data['process_rt'] ) {
			$this->rest->sendResponse(500, array(  'status' => 5, 'error' => 'Something went wrong. Incomplete request!' ) );
			return null;
		}
		//we process only responce type payment extensions
		$payment_controller = $this->dispatch( 'responses/extension/' . $this->session->data['process_rt'] );
		$this->load->library('json');
		$this->data = AJson::decode( $payment_controller->dispatchGetOutput(), TRUE );

		if ( $this->data['error'] ){
			$this->data['status'] = 6;
			$this->rest->sendResponse(200, $this->data );
			return null;
		} else if ( $this->data['success']) {
			$this->data['status'] = 1;
			//order completed clean up 
			if (isset($this->session->data['order_id'])) {
				$this->cart->clear();
				
				unset($this->session->data['shipping_method']);
				unset($this->session->data['shipping_methods']);
				unset($this->session->data['payment_method']);
				unset($this->session->data['payment_methods']);
				unset($this->session->data['guest']);
				unset($this->session->data['comment']);
				unset($this->session->data['order_id']);	
				unset($this->session->data['coupon']);
			}	
	
			$this->rest->setResponseData( $this->data );
			$this->rest->sendResponse( 200 );		
		} else {
			$this->data['status'] = 0;
			$this->data['error'] = "Unexpected Error";
			$this->rest->sendResponse(500, $this->data );		
		}

	}
	
}    	