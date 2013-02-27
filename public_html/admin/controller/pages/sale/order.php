<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

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
class ControllerPagesSaleOrder extends AController {
	public $data = array();
	private $error = array();

	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
		$this->document->setTitle( $this->language->get('heading_title') );
		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('sale/order'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));

		if (isset($this->session->data['error'])) {
			$this->data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} elseif (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$grid_settings = array(
			//id of grid
            'table_id' => 'order_grid',
            // url to load data from
			'url' => $this->html->getSecureURL('listing_grid/order'),
			'editurl' => $this->html->getSecureURL('listing_grid/order/update'),
			'update_field' => $this->html->getSecureURL ( 'listing_grid/order/update_field' ),
            'sortname' => 'order_id',
            'sortorder' => 'desc',
			'multiselect' => 'true',
            // actions
            'actions' => array(
                'print' => array(
                    'text' => $this->language->get('button_invoice'),
				    'href' => $this->html->getSecureURL('sale/invoice', '&order_id=%ID%'),
	                'target' => '_invoice',

                ),
	            'edit' => array(
                    'text' => $this->language->get('text_edit'),
				    'href' => $this->html->getSecureURL('sale/order/update', '&order_id=%ID%')
                ),
	            'save' => array(
                    'text' => $this->language->get('button_save'),
                ),
                'delete' => array(
                    'text' => $this->language->get('button_delete'),
                ),
            ),
		);

		$grid_settings['colNames'] = array(
			$this->language->get('column_order'),
			$this->language->get('column_name'),
			$this->language->get('column_status'),
			$this->language->get('column_date_added'),
			$this->language->get('column_total'),
		);
		$grid_settings['colModel'] = array(
			array( 'name' => 'order_id',
				   'index' => 'order_id',
				   'width' => 60,
				   'align' => 'center', ),
			array( 'name' => 'name',
				   'index' => 'name',
				   'width' => 140,
				   'align' => 'center', ),
			array( 'name' => 'status',
				   'index' => 'status',
				   'width' => 140,
				   'align' => 'center',
				   'search' => false ),
			array( 'name' => 'date_added',
				   'index' => 'date_added',
				   'width' => 90,
				   'align' => 'center',
				   'search' => false ),
			array( 'name' => 'total',
				   'index' => 'total',
				   'width' => 90,
				   'align' => 'center'  ),
		);

		$this->loadModel('localisation/order_status');
		$results = $this->model_localisation_order_status->getOrderStatuses();
		$statuses = array( '' => $this->language->get('text_select_status'), );
		foreach ( $results as $item) {
			$statuses[ $item['order_status_id'] ] = $item['name'];
		}

		$form = new AForm();
	    $form->setForm(array(
		    'form_name' => 'order_grid_search',
	    ));

	    $grid_search_form = array();
        $grid_search_form['id'] = 'order_grid_search';
        $grid_search_form['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'order_grid_search',
		    'action' => '',
	    ));
        $grid_search_form['submit'] = $form->getFieldHtml(array(
		    'type' => 'button',
		    'name' => 'submit',
		    'text' => $this->language->get('button_go'),
		    'style' => 'button1',
	    ));
		$grid_search_form['reset'] = $form->getFieldHtml(array(
		    'type' => 'button',
		    'name' => 'reset',
		    'text' => $this->language->get('button_reset'),
		    'style' => 'button2',
	    ));
        $grid_search_form['fields']['status'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'status',
            'options' => $statuses,
	    ));
		$grid_settings['search_form'] = true;


        $grid = $this->dispatch('common/listing_grid', array( $grid_settings ) );
		$this->view->assign('listing_grid', $grid->dispatchGetOutput());
		$this->view->assign ( 'search_form', $grid_search_form );
		$this->view->assign('help_url', $this->gen_help_url('order_listing') );

		$this->document->setTitle( $this->language->get('heading_title') );

		$this->processTemplate('pages/sale/order_list.tpl' );

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
   
  	public function update() {

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->document->setTitle( $this->language->get('heading_title') );
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateForm()) {
			$this->model_sale_order->editOrder($this->request->get['order_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('sale/order'));
		}
		    	
    	$this->redirect($this->html->getSecureURL('sale/order/details', '&order_id='.$this->request->get['order_id']));

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}

	public function details() {

		$this->data = array();
		$fields = array('email', 'telephone', 'shipping_method', 'payment_method');

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->document->setTitle( $this->language->get('heading_title') );

    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateForm()) {
			$this->model_sale_order->editOrder($this->request->get['order_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('sale/order/details', '&order_id='.$this->request->get['order_id']));
		}

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}

    	$order_info = $this->model_sale_order->getOrder($order_id);
        $this->data['order_info'] = $order_info;

		if (empty($order_info)) {
			$this->session->data['error'] = $this->language->get('error_order_load');
			$this->redirect($this->html->getSecureURL('sale/order'));
		}

		$this->document->initBreadcrumb( array (
			'href'      => $this->html->getSecureURL('index/home'),
			'text'      => $this->language->get('text_home'),
			'separator' => FALSE
		 ));

		$this->document->addBreadcrumb( array (
			'href'      => $this->html->getSecureURL('sale/order'),
			'text'      => $this->language->get('heading_title'),
			'separator' => ' :: '
		 ));
		$this->document->addBreadcrumb( array (
			'href'      => $this->html->getSecureURL('sale/order/details', '&order_id='. $this->request->get['order_id'] ),
			'text'      => $this->language->get('heading_title') .' #'.$order_info['order_id'],
			'separator' => ' :: '
		 ));

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->data['heading_title'] = $this->language->get('heading_title') .' #'.$order_info['order_id'];
		$this->data['token'] = $this->session->data['token'];
		$this->data['invoice'] = $this->html->getSecureURL('sale/invoice', '&order_id=' . (int)$this->request->get['order_id']);
		$this->data['button_invoice'] = $this->html->buildButton(array('name' => 'btn_invoice', 'text' => $this->language->get('text_invoice'), 'style' => 'button3',));
		$this->data['invoice_generate'] = $this->html->getSecureURL('sale/invoice/generate');
		$this->data['category_products'] = $this->html->getSecureURL('product/product/category');
        $this->data['product_update'] = $this->html->getSecureURL('catalog/product/update');
		$this->data['order_id'] = $this->request->get['order_id'];
		$this->data['action'] = $this->html->getSecureURL('sale/order/details', '&order_id=' . $this->request->get['order_id']);
		$this->data['cancel'] = $this->html->getSecureURL('sale/order');

        $this->_initTabs('details');

        // These only change for insert, not edit. To be added later
		$this->data['ip'] = $order_info['ip'];
		$this->data['history'] = $this->html->getSecureURL('sale/order/history', '&order_id='. $this->request->get['order_id'] );
		$this->data['store_name'] = $order_info['store_name'];
		$this->data['store_url'] = $order_info['store_url'];
		$this->data['comment'] = nl2br($order_info['comment']);
		$this->data['firstname'] = $order_info['firstname'];
		$this->data['lastname'] = $order_info['lastname'];
		$this->data['lastname'] = $order_info['lastname'];
		$this->data['total'] = $this->currency->format($order_info['total'], $order_info['currency'], $order_info['value']);
		$this->data['date_added'] = date($this->language->get('date_format_short').' '.$this->language->get('time_format'), strtotime($order_info['date_added']));

		$this->loadModel('localisation/order_status');
		$status = $this->model_localisation_order_status->getOrderStatus($order_info['order_status_id']);
		if ( $status ) {
			$this->data['order_status'] = $status['name'];
		} else {
			$this->data['order_status'] = '';
		}

		$this->loadModel('sale/customer_group');
		$customer_group_info = $this->model_sale_customer_group->getCustomerGroup($order_info['customer_group_id']);
		if ($customer_group_info) {
			$this->data['customer_group'] = $customer_group_info['name'];
		} else {
			$this->data['customer_group'] = '';
		}

		if ($order_info['invoice_id']) {
			$this->data['invoice_id'] = $order_info['invoice_prefix'] . $order_info['invoice_id'];
		} else {
			$this->data['invoice_id'] = '';
		}

		foreach ( $fields as $f ) {
			if (isset ( $this->request->post [$f] )) {
				$this->data [$f] = $this->request->post [$f];
			} elseif (isset($order_info[$f])) {
				$this->data[$f] = $order_info[$f];
			} else {
				$this->data[$f] = '';
			}
		}

		$this->data['email'] = $this->html->buildInput( array(
			                                                'name' => 'email',
			                                                'value' => $order_info['email']
		                                                ) );
		$this->data['telephone'] = $this->html->buildInput( array(
			                                                'name' => 'telephone',
			                                                'value' => $order_info['telephone']
		                                                ) );

		$this->loadModel('catalog/category');
		$this->data['categories'] = $this->model_catalog_category->getCategories(0);

		$this->loadModel('catalog/product');
		$this->data['products'] = $this->model_catalog_product->getProducts();

		$this->data['order_products'] = array();
		$order_products = $this->model_sale_order->getOrderProducts($this->request->get['order_id']);
		/*if($this->data['shipping_method']){
			$shipping_methods = array();
		}else{
			$shipping_methods = array(0=>$this->language->get('text_none'));

		}

        $extensions = $this->extension_manager->getExtensionsList( array('filter' => 'shipping','status'=>1) );
        foreach ($extensions->rows as $row) {
            $this->loadLanguage( $row['key'].'/'.$row['key']);
			$shipping_methods[$this->language->get($row['key'].'_name')] = $this->language->get($row['key'].'_name');
        }*/

		foreach ($order_products as $order_product) {
			$option_data = array();

			$options = $this->model_sale_order->getOrderOptions($this->request->get['order_id'], $order_product['order_product_id']);

			foreach ($options as $option) {
				$option_data[] = array(
					'name'  => $option['name'],
					'value' => $option['value']
				);
			}

			$this->data['order_products'][] = array(
				'order_product_id'     => $order_product['order_product_id'],
				'product_id'     => $order_product['product_id'],
				'name'     		 => $order_product['name'],
				'model'    		 => $order_product['model'],
				'option'   		 => $option_data,
				'quantity' 		 => $order_product['quantity'],
				'price'    		 => $this->currency->format($order_product['price'], $order_info['currency'], $order_info['value']),
				'total'    		 => $this->currency->format($order_product['total'], $order_info['currency'], $order_info['value']),
				'href'     		 => $this->html->getSecureURL('catalog/product/update', '&product_id=' . $order_product['product_id'])
			);
		}

        $this->data['currency'] = $this->currency->getCurrency($order_info['currency']);

		$this->data['totals'] = $this->model_sale_order->getOrderTotals($this->request->get['order_id']);

		$this->data['downloads'] = array();
		$results = $this->model_sale_order->getOrderDownloads($this->request->get['order_id']);
		foreach ($results as $result) {
			$this->data['downloads'][] = array(
				'name'      => $result['name'],
				'filename'  => $result['mask'],
				'remaining' => $result['remaining']
			);
		}

		$this->data['form_title'] = $this->language->get('edit_title_details');
		$this->data['update'] = $this->html->getSecureURL('listing_grid/order/update_field','&id='.$this->request->get['order_id']);
		$form = new AForm('HS');

		$form->setForm(array(
		    'form_name' => 'orderFrm',
			'update' => $this->data['update'],
	    ));

        $this->data['form']['id'] = 'orderFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'orderFrm',
		    'attr' => 'confirm-exit="true"',
		    'action' => $this->data['action'],
	    ));
        $this->data['form']['submit'] = $form->getFieldHtml(array(
		    'type' => 'button',
		    'name' => 'submit',
		    'text' => $this->language->get('button_save'),
		    'style' => 'button1',
	    ));
		$this->data['form']['cancel'] = $form->getFieldHtml(array(
		    'type' => 'button',
		    'name' => 'cancel',
		    'text' => $this->language->get('button_cancel'),
		    'style' => 'button2',
	    ));

		$this->data['form']['fields']['shipping_method'] = $this->data['shipping_method'];
		//TODO: need to add shipping method changing based on shipping extensions (shipping can have submethods of shipping like ground,aero,1 day, 5 day etc)
       /* $this->data['form']['fields']['shipping_method'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'shipping_method',
		    'value' => $this->data['shipping_method'],
			'options' => $shipping_methods,
	    ));*/
        $this->data['form']['fields']['payment_method'] =  $this->data['payment_method'];

        $this->addChild('pages/sale/order_summary', 'summary_form', 'pages/sale/order_summary.tpl');

		$this->view->batchAssign( $this->data );
		$this->view->assign('help_url', $this->gen_help_url('order_details') );
		
		$this->processTemplate('pages/sale/order_details.tpl' );

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}

	public function shipping() {

		$this->data = array();
		$fields = array(
			'shipping_firstname', 'shipping_lastname', 'shipping_company', 'shipping_address_1', 'shipping_address_2',
			'shipping_city', 'shipping_postcode', 'shipping_zone', 'shipping_zone_id', 'shipping_country', 'shipping_country_id',
		);

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->document->setTitle( $this->language->get('heading_title') );

    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateForm()) {
			$this->model_sale_order->editOrder($this->request->get['order_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('sale/order/shipping', '&order_id='.$this->request->get['order_id']));
		}

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}

    	$order_info = $this->model_sale_order->getOrder($order_id);

		if (empty($order_info)) {
			$this->session->data['error'] = $this->language->get('error_order_load');
			$this->redirect($this->html->getSecureURL('sale/order'));
		}

		$this->document->initBreadcrumb( array (
			'href'      => $this->html->getSecureURL('index/home'),
			'text'      => $this->language->get('text_home'),
			'separator' => FALSE
		 ));

		$this->document->addBreadcrumb( array (
			'href'      => $this->html->getSecureURL('sale/order'),
			'text'      => $this->language->get('heading_title'),
			'separator' => ' :: '
		 ));
		$this->document->addBreadcrumb( array (
			'href'      => $this->html->getSecureURL('sale/order/details', '&order_id='. $this->request->get['order_id'] ),
			'text'      => $this->language->get('heading_title') .' #'.$order_info['order_id'],
			'separator' => ' :: '
		 ));
		$this->document->addBreadcrumb( array (
			'href'      => $this->html->getSecureURL('sale/order/shipping', '&order_id='. $this->request->get['order_id'] ),
			'text'      => $this->language->get('tab_shipping'),
			'separator' => ' :: '
		 ));

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->data['order_id'] = $this->request->get['order_id'];
		$this->data['invoice'] = $this->html->getSecureURL('sale/invoice', '&order_id=' . (int)$this->request->get['order_id']);
		$this->data['button_invoice'] = $this->html->buildButton(array('name' => 'invoice', 'text' => $this->language->get('text_invoice'), 'style' => 'button3',));
		$this->data['action'] = $this->html->getSecureURL('sale/order/shipping', '&order_id=' . $this->request->get['order_id']);
		$this->data['cancel'] = $this->html->getSecureURL('sale/order');
		$this->data['common_zone'] = $this->html->getSecureURL('common/zone');

        $this->_initTabs('shipping');

		foreach ( $fields as $f ) {
			if (isset ( $this->request->post [$f] )) {
				$this->data [$f] = $this->request->post [$f];
			} elseif (isset($order_info[$f])) {
				$this->data[$f] = $order_info[$f];
			}
		}

		$this->data['form_title'] = $this->language->get('edit_title_shipping');
		$this->data['update'] = $this->html->getSecureURL('listing_grid/order/update_field','&id='.$this->request->get['order_id']);
		$form = new AForm('HS');

		$form->setForm(array(
		    'form_name' => 'orderFrm',
			'update' => $this->data['update'],
	    ));

        $this->data['form']['id'] = 'orderFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'orderFrm',
		    'attr' => 'confirm-exit="true"',
		    'action' => $this->data['action'],
	    ));
        $this->data['form']['submit'] = $form->getFieldHtml(array(
		    'type' => 'button',
		    'name' => 'submit',
		    'text' => $this->language->get('button_save'),
		    'style' => 'button1',
	    ));
		$this->data['form']['cancel'] = $form->getFieldHtml(array(
		    'type' => 'button',
		    'name' => 'cancel',
		    'text' => $this->language->get('button_cancel'),
		    'style' => 'button2',
	    ));

		foreach ( $fields as $f ) {
			if ( $f == 'shipping_zone' ) break;
			$name = str_replace('shipping_', '', $f);
			$this->data['form']['fields'][$name] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => $f,
				'value' => $this->data[$f],
			));
		}

		$this->loadModel('localisation/country');
		$this->data['countries'] = $this->model_localisation_country->getCountries();
		$this->data['countries'] = array_merge(array(0=>array('country_id' => 0,'country_name' => $this->language->get('text_select_country')) ),$this->data['countries']);

		$countries = array();
		foreach ( $this->data['countries'] as $country ){
			$countries[$country['country_id']] = $country['name'];
		}

		$this->data['form']['country_select'] = $form->getFieldHtml(array(
			'type' => 'selectbox',
			'name' => 'shipping_country_id',
			'value' => $this->data['shipping_country_id'],
			'options' => $countries,
			'style' => 'no-save'
		));

		$this->data['form']['zone_select'] = $form->getFieldHtml(array(
			'type' => 'selectbox',
			'name' => 'shipping_zone_id',
			'value' => '',
			'options' => array(),
			'style' => 'no-save'
		));

        $this->addChild('pages/sale/order_summary', 'summary_form', 'pages/sale/order_summary.tpl');
		$this->view->assign('help_url', $this->gen_help_url('order_shipping') );
		$this->view->batchAssign( $this->data );

		$this->processTemplate('pages/sale/order_shipping.tpl' );

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}

	public function payment() {

		$this->data = array();
		$fields = array(
			'payment_firstname', 'payment_lastname', 'payment_company', 'payment_address_1', 'payment_address_2',
			'payment_city', 'payment_postcode', 'payment_zone', 'payment_zone_id', 'payment_country', 'payment_country_id',
		);

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->document->setTitle( $this->language->get('heading_title') );

    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateForm()) {
			$this->model_sale_order->editOrder($this->request->get['order_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('sale/order/payment', '&order_id='.$this->request->get['order_id']));
		}

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}

    	$order_info = $this->model_sale_order->getOrder($order_id);

		if (empty($order_info)) {
			$this->session->data['error'] = $this->language->get('error_order_load');
			$this->redirect($this->html->getSecureURL('sale/order'));
		}

		$this->document->initBreadcrumb( array (
			'href'      => $this->html->getSecureURL('index/home'),
			'text'      => $this->language->get('text_home'),
			'separator' => FALSE
		 ));

		$this->document->addBreadcrumb( array (
			'href'      => $this->html->getSecureURL('sale/order'),
			'text'      => $this->language->get('heading_title'),
			'separator' => ' :: '
		 ));
		$this->document->addBreadcrumb( array (
			'href'      => $this->html->getSecureURL('sale/order/details', '&order_id='. $this->request->get['order_id'] ),
			'text'      => $this->language->get('heading_title') .' #'.$order_info['order_id'],
			'separator' => ' :: '
		 ));
		$this->document->addBreadcrumb( array (
			'href'      => $this->html->getSecureURL('sale/order/payment', '&order_id='. $this->request->get['order_id'] ),
			'text'      => $this->language->get('tab_payment'),
			'separator' => ' :: '
		 ));

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->data['order_id'] = $this->request->get['order_id'];
		$this->data['invoice'] = $this->html->getSecureURL('sale/invoice', '&order_id=' . (int)$this->request->get['order_id']);
		$this->data['button_invoice'] = $this->html->buildButton(array('name' => 'invoice', 'text' => $this->language->get('text_invoice'), 'style' => 'button3',));
		$this->data['action'] = $this->html->getSecureURL('sale/order/payment', '&order_id=' . $this->request->get['order_id']);
		$this->data['cancel'] = $this->html->getSecureURL('sale/order');
		$this->data['common_zone'] = $this->html->getSecureURL('common/zone');

        $this->_initTabs('payment');

		foreach ( $fields as $f ) {
			if (isset ( $this->request->post [$f] )) {
				$this->data [$f] = $this->request->post [$f];
			} elseif (isset($order_info[$f])) {
				$this->data[$f] = $order_info[$f];
			}
		}

		$this->data['form_title'] = $this->language->get('edit_title_payment');
		$this->data['update'] = $this->html->getSecureURL('listing_grid/order/update_field','&id='.$this->request->get['order_id']);
		$form = new AForm('HS');

		$form->setForm(array(
		    'form_name' => 'orderFrm',
			'update' => $this->data['update'],
	    ));

        $this->data['form']['id'] = 'orderFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'orderFrm',
		    'attr' => 'confirm-exit="true"',
		    'action' => $this->data['action'],
	    ));
        $this->data['form']['submit'] = $form->getFieldHtml(array(
		    'type' => 'button',
		    'name' => 'submit',
		    'text' => $this->language->get('button_save'),
		    'style' => 'button1',
	    ));
		$this->data['form']['cancel'] = $form->getFieldHtml(array(
		    'type' => 'button',
		    'name' => 'cancel',
		    'text' => $this->language->get('button_cancel'),
		    'style' => 'button2',
	    ));

		foreach ( $fields as $f ) {
			if ( $f == 'payment_zone' ) break;
			$name = str_replace('payment_', '', $f);
			$this->data['form']['fields'][$name] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => $f,
				'value' => $this->data[$f],
			));
		}

		$this->loadModel('localisation/country');
		$this->data['countries'] = $this->model_localisation_country->getCountries();

		$countries = array();
		foreach ( $this->data['countries'] as $country )
		{
			$countries[$country['country_id']] = $country['name'];
		}

		$this->data['form']['country_select'] = $form->getFieldHtml(array(
			'type' => 'selectbox',
			'name' => 'payment_country_id',
			'value' => $this->data['payment_country_id'],
			'options' => $countries,
			'style' => 'no-save'
		));

		$this->data['form']['zone_select'] = $form->getFieldHtml(array(
			'type' => 'selectbox',
			'name' => 'payment_zone_id',
			'value' => '',
			'options' => array(),
			'style' => 'no-save'
		));

        $this->addChild('pages/sale/order_summary', 'summary_form', 'pages/sale/order_summary.tpl');

		$this->view->assign('help_url', $this->gen_help_url('order_payment') );
		$this->view->batchAssign( $this->data );

		$this->processTemplate('pages/sale/order_payment.tpl' );

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}

	public function history() {

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->data = array();
    	$this->document->setTitle( $this->language->get('heading_title') );

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateForm()) {
			$this->model_sale_order->addOrderHistory($this->request->get['order_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('sale/order/history', '&order_id='.$this->request->get['order_id']));
		}

    	if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}

    	$order_info = $this->model_sale_order->getOrder($order_id);

		if (empty($order_info)) {
			$this->session->data['error'] = $this->language->get('error_order_load');
			$this->redirect($this->html->getSecureURL('sale/order'));
		}

		$this->document->initBreadcrumb( array (
			'href'      => $this->html->getSecureURL('index/home'),
			'text'      => $this->language->get('text_home'),
			'separator' => FALSE
		 ));

		$this->document->addBreadcrumb( array (
			'href'      => $this->html->getSecureURL('sale/order'),
			'text'      => $this->language->get('heading_title'),
			'separator' => ' :: '
		 ));
		$this->document->addBreadcrumb( array (
			'href'      => $this->html->getSecureURL('sale/order/details', '&order_id='. $this->request->get['order_id'] ),
			'text'      => $this->language->get('heading_title') .' #'.$order_info['order_id'],
			'separator' => ' :: '
		 ));
		$this->document->addBreadcrumb( array (
			'href'      => $this->html->getSecureURL('sale/order/history', '&order_id='. $this->request->get['order_id'] ),
			'text'      => $this->language->get('tab_history'),
			'separator' => ' :: '
		 ));

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->loadModel('localisation/order_status');
		$results = $this->model_localisation_order_status->getOrderStatuses();
		$statuses = array( '' => $this->language->get('text_select_status'), );
		foreach ( $results as $item) {
			$statuses[ $item['order_status_id'] ] = $item['name'];
		}

		$this->data['order_id'] = $this->request->get['order_id'];
		$this->data['invoice'] = $this->html->getSecureURL('sale/invoice', '&order_id=' . (int)$this->request->get['order_id']);
		$this->data['button_invoice'] = $this->html->buildButton(array('name' => 'invoice', 'text' => $this->language->get('text_invoice'), 'style' => 'button3',));
		$this->data['order_history'] = $this->html->getSecureURL('sale/order_history');
		$this->data['cancel'] = $this->html->getSecureURL('sale/order');

        $this->_initTabs('history');

		$this->data['action'] = $this->html->getSecureURL('sale/order/history', '&order_id=' . $this->request->get['order_id'] );
		$this->data['form_title'] = $this->language->get('text_edit') .' '. $this->language->get('tab_history');
		$form = new AForm('ST');

		$form->setForm(array(
		    'form_name' => 'orderFrm',
			'update' => $this->data['update'],
	    ));

        $this->data['form']['id'] = 'orderFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'orderFrm',
		    'attr' => 'confirm-exit="true"',
		    'action' => $this->data['action'],
	    ));
		$this->data['form']['submit'] = $form->getFieldHtml(array(
		    'type' => 'button',
		    'name' => 'submit',
		    'text' => $this->language->get('button_add_history'),
		    'style' => 'button1',
	    ));
		$this->data['form']['cancel'] = $form->getFieldHtml(array(
		    'type' => 'button',
		    'name' => 'cancel',
		    'text' => $this->language->get('button_cancel'),
		    'style' => 'button2',
	    ));

		$this->data['form']['order_status_id'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'order_status_id',
			'value' => $order_info['order_status_id'],
			'options' => $statuses,
	    ));
		$this->data['form']['notify'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'notify',
	    ));
		$this->data['form']['append'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'append',
			'value' => 1,
	    ));
		$this->data['form']['comment'] = $form->getFieldHtml(array(
		    'type' => 'textarea',
		    'name' => 'comment',
			'style' => 'large-field',
	    ));



		$this->data['histories'] = array();
		$results = $this->model_sale_order->getOrderHistory($this->request->get['order_id']);
		foreach ($results as $result) {
			$this->data['histories'][] = array(
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'status'     => $result['status'],
				'comment'    => nl2br($result['comment']),
				'notify'     => $result['notify'] ? $this->language->get('text_yes') : $this->language->get('text_no')
			);
		}

        $this->addChild('pages/sale/order_summary', 'summary_form', 'pages/sale/order_summary.tpl');

		$this->view->assign('help_url', $this->gen_help_url('order_history') );
		$this->view->batchAssign( $this->data );
		$this->processTemplate('pages/sale/order_history.tpl' );

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}

	private function _validateForm() {
    	if (!$this->user->canModify('sale/order')) {
      		$this->error['warning'] = $this->language->get('error_permission'); 
    	}
	
		if (!$this->error) {
	  		return TRUE;
		} else {
	  		return FALSE;
		}
  	}

    private function _initTabs($active) {
        $this->data['tabs'] = array(
            'details' => array(
                'href' => $this->html->getSecureURL('sale/order/details', '&order_id=' . $this->request->get['order_id']),
                'text' => $this->language->get('tab_details'),
            ),
            'shipping' => array(
                'href' => $this->html->getSecureURL('sale/order/shipping', '&order_id=' . $this->request->get['order_id']),
                'text' => $this->language->get('tab_shipping'),
            ),
            'payment' => array(
                'href' => $this->html->getSecureURL('sale/order/payment', '&order_id=' . $this->request->get['order_id']),
                'text' => $this->language->get('tab_payment'),
            ),
            'history' => array(
                'href' => $this->html->getSecureURL('sale/order/history', '&order_id=' . $this->request->get['order_id']),
                'text' => $this->language->get('tab_history'),
            ),
        );

        if ( in_array($active, array_keys($this->data['tabs'])) ) {
            $this->data['tabs'][$active]['active'] = 1;
        } else {
            $this->data['tabs']['details']['active'] = 1;
        }
    }

}
?>