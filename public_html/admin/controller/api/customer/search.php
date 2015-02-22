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
class ControllerApiCustomerSearch extends AControllerAPI {
  
	public function get() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('sale/customer');
		$this->loadModel('sale/customer');

		$approved = array(
			1 => $this->language->get('text_yes'),
			0 => $this->language->get('text_no'),
		);

	    $filter_params = array('loginname', 'firstname', 'lastname', 'email', 'telephone', 'fax', 'customer_group_id', 'approved');
	    //no keyword search here
 		$grid_filter_params = array( );
		$filter_data = array(
			'method' => 'get',
			'filter_params' => $filter_params, 
			'grid_filter_params' => $grid_filter_params
		);

		$filter = new AFilter( $filter_data );

		if ( !$filter->getFilterParam('loginname') && !$filter->getFilterParam('lastname')) {
			$this->rest->setResponseData( array('Error' => 'Login name or last name is required for customers search') );
			$this->rest->sendResponse(200);
			return null;
		}		
		if (  !$filter->getFilterParam('loginname') && strlen($filter->getFilterParam('lastname')) < 3 ) {
			$this->rest->setResponseData( array('Error' => 'Minimum last name length for search is 3 characters') );
			$this->rest->sendResponse(200);
			return null;
		}		
		
	    $total = $this->model_sale_customer->getTotalCustomers( $filter->getFilterData() );
	    if ($total > 0) {
	    	$total_pages = ceil($total / $filter->getParam('rows'));
	    } else {
	    	$total_pages = 0;
	    }

		//Preserved jqGrid JSON interface 
	    $response = new stdClass();
	    $response->page = $filter->getParam('page');
	    $response->total = $total_pages;
	    $response->records = $total;

	    $results = $this->model_sale_customer->getCustomers( $filter->getFilterData() );
	    $i = 0;

	    if ($results) {
	    	foreach ($results as $result) {
	    		$response->rows[ $i ]['id'] = $result['customer_id'];
	    		$response->rows[ $i ]['cell']['loginname'] = $result['loginname'];
	    		$response->rows[ $i ]['cell']['name'] = $result['name'];
	    		$response->rows[ $i ]['cell']['email'] = $result['email'];
	    		$response->rows[ $i ]['cell']['customer_group'] = $result['customer_group'];
	    		$response->rows[ $i ]['cell']['status'] = $result['status'];
	    		$response->rows[ $i ]['cell']['approved'] = $result['approved'];
	    		$i++;
	    	}
	    }
	    
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->rest->setResponseData( $response );
		$this->rest->sendResponse( 200 );
	    
	    }
}