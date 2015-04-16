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
class ControllerPagesSaleOrderSummary extends AController {

	public $data = array();
     
  	public function main() {

          //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->loadLanguage('sale/order');
    	$this->loadModel('sale/order');

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}

    	$order_info = $this->model_sale_order->getOrder($order_id);

		if (empty($order_info)) {
			$this->data['error_warning'] = $this->language->get('error_order_load');
		} else {
			//if virtual product (no shippment);
			if(!$order_info['shipping_method']){
				$order_info['shipping_method'] = $this->language->get('text_not_applicable');
			}
			// no payment 
			if(!$order_info['payment_method']){
				$order_info['payment_method'] = $this->language->get('text_not_applicable');
			}
		
			$this->data['order'] = array(
				'order_id' => '#'.$order_info['order_id'],
				'name' => $order_info['firstname'] .' '.$order_info['lastname'],
				'email' => $order_info['email'],
				'telephone' => $order_info['telephone'],
				'date_added' => dateISO2Display($order_info['date_added'], $this->language->get('date_format_short').' '.$this->language->get('time_format')),
				'total' => $this->currency->format($order_info['total'], $order_info['currency'], $order_info['value']),
				'order_status' => $order_info['order_status_id'],
				'shipping_method' => $order_info['shipping_method'],
				'payment_method' => $order_info['payment_method'],
			);

			if ($order_info['customer_id']) {
				$this->data['order']['name'] = '<a href="'.$this->html->getSecureURL('sale/customer/update', '&customer_id=' . $order_info['customer_id']).'">'.$this->data['order']['name'].'</a>';
			}

			$this->loadModel('localisation/order_status');
			$status = $this->model_localisation_order_status->getOrderStatus($order_info['order_status_id']);
			if ( $status )
				$this->data['order']['order_status'] = $status['name'];

		}

        $this->view->batchAssign( $this->data );
		$this->processTemplate('pages/sale/order_summary.tpl' );

          //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

}