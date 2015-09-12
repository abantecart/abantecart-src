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


class ControllerPagesIndexHome extends AController {
	public $data = array();

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('common/header');
		$this->loadLanguage('common/home');

		$this->document->setTitle( $this->language->get('heading_title') );
		
		$this->document->resetBreadcrumbs();

		$this->document->addBreadcrumb( array (
			'href'      => $this->html->getSecureURL('index/home'),
			'text'      => $this->language->get('text_home'),
			'separator' => FALSE,
			'current' => true,
		 ));
		
		$this->view->assign('token', $this->session->data['token']);
		
		$this->loadModel('sale/order');
		$this->view->assign('total_sale', $this->currency->format($this->model_sale_order->getTotalSales(), $this->config->get('config_currency')));
		$this->view->assign('total_sale_year', $this->currency->format($this->model_sale_order->getTotalSalesByYear(date('Y')), $this->config->get('config_currency')));
		$this->view->assign('total_order', $this->model_sale_order->getTotalOrders());
		
		$this->loadModel('sale/customer');
		$this->view->assign('total_customer', $this->model_sale_customer->getTotalCustomers());
		$this->view->assign('total_customer_approval', $this->model_sale_customer->getTotalCustomersAwaitingApproval());
		
		$this->loadModel('catalog/product');
		$this->view->assign('total_product', $this->model_catalog_product->getTotalProducts());
		
		$this->loadModel('catalog/review');
		
		$this->view->assign('total_review', $this->model_catalog_review->getTotalReviews());
		$this->view->assign('total_review_approval', $this->model_catalog_review->getTotalReviewsAwaitingApproval());
		
		$this->view->assign('shortcut_heading', $this->language->get('text_dashboard'));
		
		$this->view->assign('shortcut', array(
			array(
				'href' => $this->html->getSecureURL('catalog/category'),
				'text' => $this->language->get('text_category'),
				'icon' => 'categories_icon.png',
			),
			array(
				'href' => $this->html->getSecureURL('catalog/product'),
				'text' => $this->language->get('text_product'),
				'icon' => 'products_icon.png',
			),
			array(
				'href' => $this->html->getSecureURL('catalog/manufacturer'),
				'text' => $this->language->get('text_manufacturer'),
				'icon' => 'brands_icon.png',
			),
			array(
				'href' => $this->html->getSecureURL('catalog/review'),
				'text' => $this->language->get('text_review'),
				'icon' => 'icon_manage3.png',
			),
			array(
				'href' => $this->html->getSecureURL('sale/customer'),
				'text' => $this->language->get('text_customer'),
				'icon' => 'customers_icon.png',
			),
			array(
				'href' => $this->html->getSecureURL('sale/order'),
				'text' => $this->language->get('text_order_short'),
				'icon' => 'orders_icon.png',
			),
			array(
				'href' => $this->html->getSecureURL('extension/extensions/extensions'),
				'text' => $this->language->get('text_extensions_short'),
				'icon' => 'extensions_icon.png',
			),
			array(
				'href' => $this->html->getSecureURL('localisation/language'),
				'text' => $this->language->get('text_language'),
				'icon' => 'languages_icon.png',
			),
			array(
				'href' => $this->html->getSecureURL('design/content'),
				'text' => $this->language->get('text_content'),
				'icon' => 'content_manager_icon.png',
			),
			array(
				'href' => $this->html->getSecureURL('setting/setting'),
				'text' => $this->language->get('text_setting'),
				'icon' => 'settings_icon.png',
			),
			array(
				'href' => $this->html->getSecureURL('tool/message_manager'),
				'text' => $this->language->get('text_messages'),
				'icon' => 'icon_messages.png',
			),
			array(
				'href' => $this->html->getSecureURL('design/layout'),
				'text' => $this->language->get('text_layout'),
				'icon' => 'icon_layouts.png',
			)
		));

		//10 new orders and customers
		$filter = array(
			'sort'  => 'c.date_added',
			'order' => 'DESC',
			'start' => 0,
			'limit' => 10
		);		
		$top_customers = $this->model_sale_customer->getCustomers($filter);
		foreach( $top_customers as $indx => $customer) {
			$action = array();
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->html->getSecureURL('sale/customer/update', '&customer_id='.$customer['customer_id'])
			);
			$top_customers[$indx]['action'] = $action;
		}
		$this->view->assign('customers', $top_customers);
		$this->view->assign('customers_url', $this->html->getSecureURL('sale/customer'));

		$orders = array();		
		$filter = array(
			'sort'  => 'o.date_added',
			'order' => 'DESC',
			'start' => 0,
			'limit' => 10
		);
		$this->view->assign('orders_url',  $this->html->getSecureURL('sale/order'));
		$this->view->assign('orders_text', $this->language->get('text_order'));
		
		$results = $this->model_sale_order->getOrders($filter);

		foreach ($results as $result) {
			$action = array();
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->html->getSecureURL('sale/order/update', '&order_id=' . $result['order_id'])
			);
					
			$orders[] = array(
				'order_id'   => $result['order_id'],
				'name'       => $result['name'],
				'status'     => $result['status'],
				'date_added' => dateISO2Display($result['date_added'],$this->language->get('date_format_short')),
				'total'      => $this->currency->format($result['total'], $result['currency'], $result['value']),
				'action'     => $action
			);
		}
		$this->view->assign('orders', $orders );

		if ($this->config->get('config_currency_auto')) {
			$this->loadModel('localisation/currency');
		
			$this->model_localisation_currency->updateCurrencies();
		}
		
		$this->view->assign('chart_url', $this->html->getSecureURL('index/chart') );

		//check at least one enabled payment extension
		$no_payment_installed = true;
		$ext_list = $this->extensions->getInstalled('payment');
		foreach($ext_list as $ext_txt_id){
			if( $this->config->get($ext_txt_id.'_status')){
				$no_payment_installed = false;
				break;
			}
		}

		if($no_payment_installed){
			$this->view->assign('no_payment_installed', $no_payment_installed);
			$this->loadLanguage('common/tips');
			$tip_content = $this->html->convertLinks($this->language->get('no_enabled_payments_tip'));

			$tip_content = sprintf($tip_content, $api_response_html);
			$this->view->assign('tip_content', $tip_content);
		}

		//check quick start quide based on no last_login and if it is not yet completed
		if( !$this->user->getLastLogin() && $this->session->data['quick_start_step'] != 'finished') {
			$store_id = !isset($this->session->data['current_store_id']) ? 0 : $this->session->data['current_store_id'];
			$resources_scripts = $this->dispatch(
			    'responses/common/resource_library/get_resources_scripts',
			    array(
			    	'object_name' => 'store',
			    	'object_id' => (int)$store_id,
			    	'types' => array('image'),
			    	'onload' => true,
			    	'mode' => 'single'
			    )
			);
			$this->view->assign('resources_scripts', $resources_scripts->dispatchGetOutput());
			$this->view->assign('quick_start_url', $this->html->getSecureURL('setting/setting_quick_form/quick_start'));
		}
		
		$this->processTemplate('pages/index/home.tpl' );

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

}