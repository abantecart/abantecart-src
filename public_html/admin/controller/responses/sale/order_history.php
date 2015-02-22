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
class ControllerResponsesSaleOrderHistory extends AController {
	private $error = array();
    
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('sale/order');
		
		$this->loadModel('sale/order');
		
		$json = array();
    	
		if (!$this->user->canModify('sale/order')) {
      		$json['error'] = $this->language->get('error_permission'); 
    	} else {
			$this->model_sale_order->addOrderHistory($this->request->get['order_id'], $this->request->post);
			
			$json['success'] = $this->language->get('text_success');
			
			$json['date_added'] = date($this->language->get('date_format_short'));

			$this->loadModel('localisation/order_status');
			
			$order_status_info = $this->model_localisation_order_status->getOrderStatus($this->request->post['order_status_id']);
			
			if ($order_status_info) {
				$json['order_status'] = $order_status_info['name'];
			} else {
				$json['order_status'] = '';
			}	
			
			if ($this->request->post['notify']) {
				$json['notify'] = $this->language->get('text_yes');
			} else {
				$json['notify'] = $this->language->get('text_no');
			}
			
			if (isset($this->request->post['comment'])) {
				$json['comment'] = $this->request->post['comment'];
			} else {
				$json['comment'] = '';
			}
		}

          //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
			
		$this->load->library('json');
		$this->response->setOutput(AJson::encode($json));
  	} 
	
}
?>