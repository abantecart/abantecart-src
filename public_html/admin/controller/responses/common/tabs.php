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
class ControllerResponsesCommonTabs extends AController {
	private $error = array();
	public $data = array();
	public $parent_controller = ''; //rt of page where you plan to place tabs

  	public function main($parent_controller,$data) {
        $this->data = $data;
        $this->parent_controller = $parent_controller; //use it in hooks to recognize what page controller calls
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

	    $tabs = (array)$this->data['tabs'];
	    $this->data['tabs'] = $idx = array();
	    foreach($tabs as $k=>$tab){
		    $idx[] = (int)$tab['sort_order'];
	    }

	    array_multisort($idx,SORT_ASC,$tabs);
		$this->data['tabs'] = $tabs;

		$this->view->batchAssign( $this->data );
		$this->processTemplate('responses/common/tabs.tpl');
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}
	
  	public function latest_customers() {
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		//10 new customers
		$this->loadModel('sale/customer');		
		$filter = array(
			'sort'  => 'c.date_added',
			'order' => 'DESC',
			'start' => 0,
			'limit' => 10
		);
		$top_customers = $this->model_sale_customer->getCustomers($filter);
		foreach( $top_customers as $indx => $customer) {
			$top_customers[$indx]['url'] = $this->html->getSecureURL('sale/customer/update', '&customer_id='.$customer['customer_id']);
		}
		$this->view->assign('top_customers', $top_customers);
		$this->view->assign('recent_customers', $this->language->get('recent_customers'));

		$this->processTemplate('responses/common/latest_customers.tpl');
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

  	public function latest_orders() {
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		//10 new orders
		$this->loadModel('sale/order');
		$filter = array(
			'sort'  => 'o.date_added',
			'order' => 'DESC',
			'start' => 0,
			'limit' => 10
		);
		$top_orders = $this->model_sale_order->getOrders($filter);
		foreach( $top_orders as $indx => $order) {
			$top_orders[$indx]['url'] = $this->html->getSecureURL('sale/order/details', '&order_id='.$order['order_id']);
			$top_orders[$indx]['total'] = $this->currency->format($order['total'], $this->config->get('config_currency'));
		}
		$this->view->assign('top_orders', $top_orders);

		$this->view->assign('new_orders', $this->language->get('new_orders'));

		$this->processTemplate('responses/common/latest_orders.tpl');
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

}

