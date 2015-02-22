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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ControllerApiOrderDetails extends AControllerAPI {
  
	public function get() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('sale/order');
		$this->loadModel('sale/order');

		$request = $this->rest->getRequestParams();
		
		if ( !has_value($request['order_id']) ) {
			$this->rest->setResponseData( array('Error' => 'Order ID is missing') );
			$this->rest->sendResponse(200);
			return null;
		}		

		$order_details =  $this->model_sale_order->getOrder($request['order_id']);
		if (!count($order_details)) {
			$this->rest->setResponseData( array('Error' => 'Incorrect order ID or missing order data') );
			$this->rest->sendResponse(200);
			return null;
		}
			    
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->rest->setResponseData( $order_details );
		$this->rest->sendResponse( 200 );
	    
	    }
}