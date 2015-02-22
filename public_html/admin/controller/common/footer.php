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
class ControllerCommonFooter extends AController {
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
		$this->loadLanguage('common/header');

		$menu = new AMenu('admin','menu');
		$documentation = $menu->getMenuItem('documentation');
		$support = $menu->getMenuItem('support');
		$mp = $menu->getMenuItem('marketplace');
		$this->view->assign('doc_menu', $documentation);
		$this->view->assign('doc_menu_text', $this->language->get($documentation['item_text']));
		$this->view->assign('support_menu', $support);
		$this->view->assign('support_menu_text', $this->language->get($support['item_text']));
		$this->view->assign('mp_menu', $mp);
		$this->view->assign('mp_menu_text', $this->language->get($mp['item_text']));

		$this->view->assign('text_footer_left', sprintf($this->language->get('text_footer_left'), date('Y')));
		$this->view->assign('text_footer', sprintf($this->language->get('text_footer'),date('Y')).VERSION);
		
		if (!$this->user->isLogged() || !isset($this->request->get['token']) || !isset($this->session->data['token']) || ($this->request->get['token'] != $this->session->data['token'])) {
			$this->view->assign('logged', '');
			$this->view->assign('home', $this->html->getSecureURL('index/login', '', true));
		} else {
			$this->view->assign('logged', sprintf($this->language->get('text_logged'), $this->user->getUserName()));
			$this->view->assign('avatar', $this->user->getAvatar());
			$this->view->assign('username', $this->user->getUserName());
			$this->view->assign('last_login', sprintf($this->language->get('text_last_login'), $this->user->getLastLogin()));
			$this->view->assign('account_edit', $this->html->getSecureURL('index/edit_details', '', true));
		}

		//10 new orders and customers
		$this->loadModel('sale/order');
		$this->loadModel('sale/customer');
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

		$this->view->assign('new_orders', $this->language->get('new_orders'));
		$this->view->assign('recent_customers', $this->language->get('recent_customers'));
		
		$this->processTemplate('common/footer.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}
}
