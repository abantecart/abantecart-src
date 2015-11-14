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
class ControllerPagesSaleOrderTabs extends AController {
	private $error = array();
	public $data = array();
     
  	public function main() {

        $this->data = func_get_arg(0);
        if (!is_array($this->data)) {
            throw new AException (AC_ERR_LOAD, 'Error: Could not create order tabs. Tabs definition is not array.');
        }
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('sale/order');
		$order_id = $this->request->get['order_id'];
		$this->data['order_id'] = $order_id;
		$this->data['groups'] = array('order_details','shipping','payment');
		
		$this->data['link_details'] = $this->html->getSecureURL('sale/order/details', '&order_id=' . $order_id);
		$this->data['link_shipping'] = $this->html->getSecureURL('sale/order/shipping', '&order_id=' . $order_id);
		$this->data['link_payment'] = $this->html->getSecureURL('sale/order/payment', '&order_id=' . $order_id);
		if($this->model_sale_order->getTotalOrderDownloads($order_id)){
			$this->data['link_files'] = $this->html->getSecureURL('sale/order/files', '&order_id=' . $order_id);
			$this->data['groups'][] = 'files';
		}
		$this->data['link_history'] = $this->html->getSecureURL('sale/order/history', '&order_id=' . $order_id);
		$this->data['groups'][] = 'history';

		$this->view->batchAssign( $this->data );
		$this->processTemplate('pages/sale/order_tabs.tpl');

		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}
}

