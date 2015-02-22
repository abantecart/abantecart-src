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
class ControllerApiAccountHistory extends AControllerAPI {
	public $data;

	public function post() {
        $this->extensions->hk_InitData($this,__FUNCTION__);
		$request_data = $this->rest->getRequestParams();
		
		if (!$this->customer->isLoggedWithToken( $request_data['token'] )) {
			$this->rest->setResponseData( array( 'error' => 'Not logged in or Login attempt failed!' ) );	
			$this->rest->sendResponse(401);
			return null;
    	} 
    	
		$this->loadModel('account/order');
		$this->loadLanguage('account/history');
		
		$order_total = $this->model_account_order->getTotalOrders();
		
		if ($order_total) {			
			if (isset($request_data['page'])) {
				$page = $request_data['page'];
			} else {
				$page = 1;
			}

            $this->data['limit'] = $this->config->get('config_catalog_limit');			
      		$orders = array();
			$results = $this->model_account_order->getOrders(($page - 1) * $this->data['limit'], $this->data['limit']);
      		
			foreach ($results as $result) {
        		$product_total = $this->model_account_order->getTotalOrderProductsByOrderId($result['order_id']);
        		$orders[] = array(
								'order_id'   => $result['order_id'],
								'name'       => $result['firstname'] . ' ' . $result['lastname'],
								'status'     => $result['status'],
								'date_added' => dateISO2Display($result['date_added'], $this->language->get('date_format_short')),
								'products'   => $product_total,
								'total'      => $this->currency->format($result['total'], $result['currency'], $result['value']),
        		);
      		}

            $this->data['orders'] = $orders;
            $this->data['total_orders'] = $order_total;
            $this->data['page'] = $page;
            
    	} else {
 			$this->data['orders'] = array();
 			$this->data['total_orders'] = 0;
		}

        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->rest->setResponseData( $this->data );
		$this->rest->sendResponse( 200 );    	
    }
}