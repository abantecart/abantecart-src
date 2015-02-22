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
class ControllerApiCustomerDetails extends AControllerAPI {
  
	public function get() {
		$customer_details = array();
		$customer_addresses = array();

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadModel('sale/customer');
		$this->loadModel('sale/customer_group');

		$request = $this->rest->getRequestParams();
		
		if ( !has_value($request['customer_id']) ) {
			$this->rest->setResponseData( array('Error' => 'Customer ID is missing') );
			$this->rest->sendResponse(200);
			return null;
		}		
		
		$customer_details =  $this->model_sale_customer->getCustomer($request['customer_id']);
		if (!count($customer_details)) {
			$this->rest->setResponseData( array('Error' => 'Incorrect Customer ID or missing customer data') );
			$this->rest->sendResponse(200);
			return null;
		}

		//clean up data before display
		unset($customer_details['password']);
		unset($customer_details['cart']);
		$cst_grp = $this->model_sale_customer_group->getCustomerGroup($customer_details['customer_group_id']);
		$customer_details['customer_group'] = $cst_grp['name'];

		$customer_addresses =  $this->model_sale_customer->getAddressesByCustomerId($request['customer_id']);
		$customer_details['addresses'] = $customer_addresses;
			    
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->rest->setResponseData( $customer_details );
		$this->rest->sendResponse( 200 );
	    
	    }
}