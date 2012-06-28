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
if (!defined('DIR_CORE')) {
	header('Location: static_pages/');
}
class ControllerPagesSettingStore extends AController {
	private $error = array();
	public $data = array();
      
  	public function insert() {

          //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->document->setTitle( $this->language->get('heading_title') );
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateForm()) {
    	    $languages = $this->language->getAvailableLanguages();
		    foreach ( $languages as $l ) {
			    if ( $l['language_id'] == $this->session->data['content_language_id'] ) continue;
			    $this->request->post['store_description'][$l['language_id']] = $this->request->post['store_description'][ $this->session->data['content_language_id'] ];
		    }

		    $store_id = $this->model_setting_store->addStore($this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('setting/store/update', '&store_id=' . $store_id));
    	}
    	$this->getForm();

          //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}

  	public function update() {

          //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->document->setTitle( $this->language->get('heading_title') );

    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateForm()) {

			$this->model_setting_store->editStore($this->request->get['store_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('setting/store/update', '&store_id=' . $this->request->get['store_id']));
		}
    	$this->getForm();

          //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}

  	public function delete() {

          //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->document->setTitle( $this->language->get('heading_title') );

		if (isset($this->request->get['store_id']) && $this->_validateDelete()) {
			$this->model_setting_store->deleteStore($this->request->get['store_id']);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('setting/setting'));
		}
    	$this->getForm();

          //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}
 
	public function getForm() {

		$this->data = array();
		  //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->data['error'] = $this->error;
		$this->data['token'] = $this->session->data['token'];
		$this->data['content_language_id'] = $this->session->data['content_language_id'];
		$this->data['form_language_switch'] = $this->html->getContentLanguageSwitcher();

   		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('setting/setting'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
		$this->data['insert'] = $this->html->getSecureURL('setting/store/insert');
		$this->data['common_zone'] = $this->html->getSecureURL('common/zone');
		$this->data['template_image'] = $this->html->getSecureURL('setting/template_image');
        $this->data['rl'] = $this->html->getSecureURL('common/resource_library', '&mode=url&type=image');
        

		if (isset($this->request->get['store_id'])) {
			$this->data['delete'] = $this->html->getSecureURL('setting/store/delete', '&store_id=' . $this->request->get['store_id']);
		} else {
			$this->data['delete'] = '';
		}
		
		if (!isset($this->request->get['store_id'])) {
			$this->data['cancel'] = $this->html->getSecureURL('setting/setting');
		} else {
			$this->data['cancel'] = $this->html->getSecureURL('setting/store/update', '&store_id=' . $this->request->get['store_id']);
		}

		$this->data['stores'] = array();
		$this->data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default'),
			'href'     => $this->html->getSecureURL('setting/setting')
		); 
		
		$this->loadModel('setting/store');
		$results = $this->model_setting_store->getStores();
		foreach ($results as $result) {
			$this->data['stores'][] = array(
				'store_id' => $result['store_id'],
				'name'     => $result['name'],
				'href'     => $this->html->getSecureURL('setting/store/update', '&store_id=' . $result['store_id'])
			); 
		}
		
		if (isset($this->request->get['store_id'])) {
			$this->data['store_id'] = $this->request->get['store_id'];
		} else {
			$this->data['store_id'] = 0;
		}
	
		if (isset($this->request->get['store_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$store_info = $this->model_setting_store->getStore($this->request->get['store_id']);
    	}

		$templates = array();
				$directories = glob(DIR_STOREFRONT . 'view/*', GLOB_ONLYDIR);
				foreach ($directories as $directory) {
					$templates[basename($directory)] = basename($directory);
				}
                $extension_templates = $this->extension_manager->getExtensionsList( array('category' => 'template', 'status' => 1 ) );
                if ( $extension_templates->total > 0 )
                    foreach ( $extension_templates->rows as $row ) {
                        $templates[ $row['key'] ] = $row['key'];
                    }
		$this->data['templates'] = $templates;

		$this->loadModel('localisation/country');
		$countries = array();
		$results = $this->model_localisation_country->getCountries();
		foreach ( $results as $c ) {
			$countries[ $c['country_id'] ] = $c['name'];
		}

		$this->loadModel('localisation/language');
		$results = $this->model_localisation_language->getLanguages();
		$languages = array();
		foreach ( $results as $v ) {
			$languages[ $v['code'] ] = $v['name'];
		}

		$this->loadModel('localisation/currency');
		$results = $this->model_localisation_currency->getCurrencies();
		$currencies = array();
		foreach ( $results as $v ) {
			$currencies[ $v['code'] ] = $v['title'];
		}

		$this->loadModel('sale/customer_group');
		$results = $this->model_sale_customer_group->getCustomerGroups();
		$customer_groups = array();
		foreach( $results as $r ) {
			$customer_groups[ $r['customer_group_id'] ] = $r['name'];
		}

		$this->loadModel('localisation/stock_status');
		$stock_statuses = array();
		$results = $this->model_localisation_stock_status->getStockStatuses();
		foreach( $results as $r ) {
			$stock_statuses[ $r['stock_status_id'] ] = $r['name'];
		}

		$this->loadModel('localisation/order_status');
		$order_statuses = array();
		$results = $this->model_localisation_order_status->getOrderStatuses();
		foreach( $results as $r ) {
			$order_statuses[ $r['order_status_id'] ] = $r['name'];
		}

		$yes_no = array(
			1 => $this->language->get('text_yes'),
			0 => $this->language->get('text_no'),
		);

		$fields = array('config_name',
		                'config_url',
		                'config_title',
		                'config_meta_description',
		                'config_storefront_template',
		                'config_country_id',
		                'config_zone_id',
		                'config_storefront_language',
		                'config_currency',
		                'config_tax',
		                'config_tax_store',
		                'config_tax_customer',
		                'config_customer_group_id',
		                'config_customer_price',
		                'config_customer_approval',
		                'config_guest_checkout',
		                'config_account_id',
		                'config_checkout_id',
		                'config_stock_display',
		                'config_stock_checkout',
		                'config_catalog_limit',
		                'config_cart_weight',
		                'config_order_status_id',
		                'config_logo',
		                'config_icon',
		                'config_image_thumb_width',
		                'config_image_thumb_height',
		                'config_image_popup_width',
		                'config_image_popup_height',
		                'config_image_category_width',
		                'config_image_category_height',
		                'config_image_product_width',
		                'config_image_product_height',
		                'config_image_additional_width',
		                'config_image_additional_height',
		                'config_image_related_width',
		                'config_image_related_height',
		                'config_image_cart_width',
		                'config_image_cart_height',
		                'config_ssl'
		);

		foreach ( $fields as $f ) {
			if (isset ( $this->request->post [$f] )) {
				$this->data [$f] = $this->request->post [$f];
			} elseif (isset($store_info)) {
				$this->data[$f] = $store_info[$f];
			} else {
				$this->data[$f] = '';
			}
		}
		
		if (isset($this->request->post['store_description'])) {
			$this->data['store_description'] = $this->request->post['store_description'];
		} elseif (isset($store_info)) {
			$this->data['store_description'] = $this->model_setting_store->getStoreDescriptions($this->request->get['store_id']);
		} else {
			$this->data['store_description'] = array();
		}

		if ($this->data['language_code'] == '' ) {
			$this->data['language_code'] = $this->config->get('config_storefront_language');
		}
		if ($this->data['config_currency_code'] == '' ){
			$this->data['config_currency_code'] = $this->config->get('config_currency');
		}
		if ($this->data['config_catalog_limit'] == '' ) {
			$this->data['config_catalog_limit'] = '12';
		}

		if ($this->data['config_image_thumb_width'] == '') {
			$this->data['config_image_thumb_width'] = 180;
		}
		if ($this->data['config_image_thumb_height'] == '' ) {
			$this->data['config_image_thumb_height'] = 180;
		}
		/*if ($this->data['config_image_popup_width'] == '') {
			$this->data['config_image_popup_width'] = 500;
		}
		if ($this->data['config_image_popup_height'] == '' ) {
			$this->data['config_image_popup_height'] = 500;
		}*/
		if ($this->data['config_image_category_width'] == '') {
			$this->data['config_image_category_width'] = 120;
		}
		if ($this->data['config_image_category_height'] == '' ) {
			$this->data['config_image_category_height'] = 120;
		}
		if ($this->data['config_image_product_width'] == '') {
			$this->data['config_image_product_width'] = 120;
		}
		if ($this->data['config_image_product_height'] == '' ) {
			$this->data['config_image_product_height'] = 120;
		}
		if ($this->data['config_image_additional_width'] == '') {
			$this->data['config_image_additional_width'] = 45;
		}
		if ($this->data['config_image_additional_height'] == '' ) {
			$this->data['config_image_additional_height'] = 45;
		}
		if ($this->data['config_image_related_width'] == '') {
			$this->data['config_image_related_width'] = 120;
		}
		if ($this->data['config_image_related_height'] == '' ) {
			$this->data['config_image_related_height'] = 120;
		}
		if ($this->data['config_image_cart_width'] == '') {
			$this->data['config_image_cart_width'] = 120;
		}
		if ($this->data['config_image_cart_height'] == '' ) {
			$this->data['config_image_cart_height'] = 120;
		}

        if (!isset($this->request->get['store_id'])) {
			$this->data['action'] = $this->html->getSecureURL('setting/store/insert');
			$this->data['form_title'] = $this->language->get('button_add_store');
			$this->data['update'] = '';
			$form = new AForm('ST');
		} else {
			$this->data['action'] = $this->html->getSecureURL('setting/store/update', '&store_id=' . $this->request->get['store_id'] );
			$this->data['form_title'] = $this->language->get('text_edit_store');
			$this->data['update'] = $this->html->getSecureURL('listing_grid/store/update_field','&id='.$this->request->get['store_id']);
			$form = new AForm('HS');
		}

		$form->setForm(array(
		    'form_name' => 'storeFrm',
			'update' => $this->data['update'],
	    ));

        $this->data['form']['id'] = 'storeFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'storeFrm',
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

		$this->data['form']['fields']['general']['name'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'config_name',
			'value' => $this->data['config_name'],
			'required' => true,
		));
		$this->data['form']['fields']['general']['url'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'config_url',
			'value' => $this->data['config_url'],
			'required' => true,
			'style' => 'large-field',
		));

		$this->data['form']['fields']['store']['title'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'config_title',
			'value' => $this->data['config_title'],
			'required' => true,
		));
		$this->data['form']['fields']['store']['meta_description'] = $form->getFieldHtml(array(
			'type' => 'textarea',
			'name' => 'config_meta_description',
			'value' => $this->data['config_meta_description'],
			'style' => 'xl-field',
		));
		$this->data['form']['fields']['store']['template'] = $form->getFieldHtml(array(
			'type' => 'selectbox',
			'name' => 'config_storefront_template',
			'value' => $this->data['config_storefront_template'],
			'options' => $this->data['templates'],
			'style' => 'large-field',
		));
		$this->data['form']['fields']['store']['description'] = $form->getFieldHtml(array(
			'type' => 'textarea',
			'name' => 'store_description['.$this->session->data['content_language_id'].'][description]',
			'value' => $this->data['store_description'][$this->session->data['content_language_id']]['description'],
			'style' => 'xl-field',
		));

		$this->data['form']['fields']['local']['country'] = $form->getFieldHtml(array(
			'type' => 'selectbox',
			'name' => 'config_country_id',
			'value' => $this->data['config_country_id'],
			'options' => $countries,
			'style' => 'large-field',
		));
		$this->data['form']['fields']['local']['zone'] = $form->getFieldHtml(array(
			'type' => 'selectbox',
			'name' => 'config_zone_id',
			'value' => $this->data['config_zone_id'],
			'options' => array(),
			'style' => 'large-field',
		));
		$this->data['form']['fields']['local']['language'] = $form->getFieldHtml(array(
			'type' => 'selectbox',
			'name' => 'config_storefront_language',
			'value' => $this->data['config_storefront_language'],
			'options' => $languages,
		));

		$this->data['form']['fields']['local']['currency'] = $form->getFieldHtml(array(
			'type' => 'selectbox',
			'name' => 'config_currency',
			'value' => $this->data['config_currency'],
			'options' => $currencies,
		));

		$this->data['form']['fields']['option']['catalog_limit'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'config_catalog_limit',
			'value' => $this->data['config_catalog_limit'],
			'required' => true,
			'style' => 'small-field',
		));
		$this->data['form']['fields']['option']['tax'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_tax',
            'value' => $this->data['config_tax'],
            'style' => 'btn_switch'
		));

		$this->data['form']['fields']['option']['customer_group'] = $form->getFieldHtml(array(
			'type' => 'selectbox',
			'name' => 'config_customer_group_id',
			'value' => $this->data['config_customer_group_id'],
			'options' => $customer_groups,
		));
		$this->data['form']['fields']['option']['customer_price'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_customer_price',
            'value' => $this->data['config_customer_price'],
            'style' => 'btn_switch'
		));
		$this->data['form']['fields']['option']['customer_approval'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_customer_approval',
            'value' => $this->data['config_customer_approval'],
            'style' => 'btn_switch'
		));
		$this->data['form']['fields']['option']['guest_checkout'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_guest_checkout',
            'value' => $this->data['config_guest_checkout'],
            'style' => 'btn_switch'
		));

		$acm = new AContentManager();
		$results = $acm->getContents(array(),'default', $this->request->get['store_id']);
        $contents = array( '' => $this->language->get('text_none'));
        foreach( $results as $item ) {
			if(!$item['status']) continue;
			$contents[ $item['content_id'] ] = $item['title'];
		}

		$this->data['form']['fields']['option']['account'] = $form->getFieldHtml(array(
			'type' => 'selectbox',
			'name' => 'config_account_id',
			'value' => $this->data['config_account_id'],
			'options' => $contents
		));
		$this->data['form']['fields']['option']['checkout'] = $form->getFieldHtml(array(
			'type' => 'selectbox',
			'name' => 'config_checkout_id',
			'value' => $this->data['config_checkout_id'],
			'options' => $contents
		));
		$this->data['form']['fields']['option']['stock_display'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_stock_display',
            'value' => $this->data['config_stock_display'],
            'style' => 'btn_switch'
		));
		$this->data['form']['fields']['option']['stock_checkout'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_stock_checkout',
            'value' => $this->data['config_stock_checkout'],
            'style' => 'btn_switch'
		));
		$this->data['form']['fields']['option']['order_status'] = $form->getFieldHtml(array(
			'type' => 'selectbox',
			'name' => 'config_order_status_id',
			'value' => $this->data['config_order_status_id'],
			'options' => $order_statuses,
		));
		$this->data['form']['fields']['option']['cart_weight'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_cart_weight',
            'value' => $this->data['config_cart_weight'],
            'style' => 'btn_switch'
		));


		$resource = new AResource( 'image' );
		$this->data['settings']['config_logo'] = $this->dispatch(
					'responses/common/resource_library/get_resource_html_single',
					array('type'=>'image',
						  'wrapper_id'=>'config_logo',
						  'resource_id'=> $resource->getIdFromHexPath(str_replace('image/','',$this->data['config_logo'])),
						  'field' => 'config_logo'));
		$this->data['settings']['config_logo'] = $this->data['settings']['config_logo']->dispatchGetOutput();

		$this->data['settings']['config_icon'] = $this->dispatch(
					'responses/common/resource_library/get_resource_html_single',
					array('type'=>'image',
						  'wrapper_id'=>'config_icon',
						  'resource_id'=> $resource->getIdFromHexPath(str_replace('image/','',$this->data['config_icon'])),
						  'field' => 'config_icon'));
		$this->data['settings']['config_icon'] = $this->data['settings']['config_icon']->dispatchGetOutput();

        $resources_scripts = $this->dispatch(
                    'responses/common/resource_library/get_resources_scripts',
                    array(
                        'object_name' => 'store',
                        'object_id' => ( isset($this->request->get['store_id']) ? $this->request->get['store_id'] : ''),
                        'types' => 'image',
                        'mode' => 'url'
                    )
                );
        $this->data['resources_scripts'] = $resources_scripts->dispatchGetOutput();
		

		$this->data['form']['fields']['image']['config_logo'] .= $form->getFieldHtml(array(  'type' => 'hidden',
																							 'name' => 'config_logo',
																							 'value' => $this->data['config_logo']));


		$this->data['form']['fields']['image']['config_icon'] .= $form->getFieldHtml(array( 'type' => 'hidden',
																							'name' => 'config_icon',
		                                                                                    'value' => $this->data['config_icon']));

		$this->data['form']['fields']['image']['image_thumb_width'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_image_thumb_width',
            'value' => $this->data['config_image_thumb_width'],
            'style' => 'small-field',
            'required' => true,
        ));
        $this->data['form']['fields']['image']['image_thumb_height'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_image_thumb_height',
            'value' => $this->data['config_image_thumb_height'],
            'style' => 'small-field',
            'required' => true,
        ));

        $this->data['form']['fields']['image']['image_category_width'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_image_category_width',
            'value' => $this->data['config_image_category_width'],
            'style' => 'small-field',
            'required' => true,
        ));
        $this->data['form']['fields']['image']['image_category_height'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_image_category_height',
            'value' => $this->data['config_image_category_height'],
            'style' => 'small-field',
            'required' => true,
        ));
        $this->data['form']['fields']['image']['image_product_width'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_image_product_width',
            'value' => $this->data['config_image_product_width'],
            'style' => 'small-field',
            'required' => true,
        ));
        $this->data['form']['fields']['image']['image_product_height'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_image_product_height',
            'value' => $this->data['config_image_product_height'],
            'style' => 'small-field',
            'required' => true,
        ));
        $this->data['form']['fields']['image']['image_additional_width'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_image_additional_width',
            'value' => $this->data['config_image_additional_width'],
            'style' => 'small-field',
            'required' => true,
        ));
        $this->data['form']['fields']['image']['image_additional_height'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_image_additional_height',
            'value' => $this->data['config_image_additional_height'],
            'style' => 'small-field',
            'required' => true,
        ));
        $this->data['form']['fields']['image']['image_related_width'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_image_related_width',
            'value' => $this->data['config_image_related_width'],
            'style' => 'small-field',
            'required' => true,
        ));
        $this->data['form']['fields']['image']['image_related_height'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_image_related_height',
            'value' => $this->data['config_image_related_height'],
            'style' => 'small-field',
            'required' => true,
        ));
        $this->data['form']['fields']['image']['image_cart_width'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_image_cart_width',
            'value' => $this->data['config_image_cart_width'],
            'style' => 'small-field',
            'required' => true,
        ));
        $this->data['form']['fields']['image']['image_cart_height'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_image_cart_height',
            'value' => $this->data['config_image_cart_height'],
            'style' => 'small-field',
            'required' => true,
        ));

		$this->data['form']['fields']['server']['ssl'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_ssl',
            'value' => $this->data['config_ssl'],
            'style' => 'btn_switch'
		));

		$this->view->batchAssign( $this->data );
		$this->view->assign('language_code', $this->session->data['language']);
        $this->processTemplate('pages/setting/store.tpl' );

		   //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	private function _validateForm() {
		if (!$this->user->hasPermission('modify', 'setting/store')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		if (!$this->request->post['config_name']) {
			$this->error['name'] = $this->language->get('error_name');
		}	
		
		if (!$this->request->post['config_url']) {
			$this->error['url'] = $this->language->get('error_url');
		}	
		
		if (!$this->request->post['config_title']) {
			$this->error['title'] = $this->language->get('error_title');
		}	
		
		if (!$this->request->post['config_image_thumb_width'] || !$this->request->post['config_image_thumb_height']) {
			$this->error['image_thumb'] = $this->language->get('error_image_thumb');
		}	
		
		/*if (!$this->request->post['config_image_popup_width'] || !$this->request->post['config_image_popup_height']) {
			$this->error['image_popup'] = $this->language->get('error_image_popup');
		}	*/
		
		if (!$this->request->post['config_image_category_width'] || !$this->request->post['config_image_category_height']) {
			$this->error['image_category'] = $this->language->get('error_image_category');
		}
		
		if (!$this->request->post['config_image_product_width'] || !$this->request->post['config_image_product_height']) {
			$this->error['image_product'] = $this->language->get('error_image_product');
		}
		
		if (!$this->request->post['config_image_additional_width'] || !$this->request->post['config_image_additional_height']) {
			$this->error['image_additional'] = $this->language->get('error_image_additional');
		}
		
		if (!$this->request->post['config_image_related_width'] || !$this->request->post['config_image_related_height']) {
			$this->error['image_related'] = $this->language->get('error_image_related');
		}
		
		if (!$this->request->post['config_image_cart_width'] || !$this->request->post['config_image_cart_height']) {
			$this->error['image_cart'] = $this->language->get('error_image_cart');
		}
		
		if (!$this->request->post['config_catalog_limit']) {
			$this->error['catalog_limit'] = $this->language->get('error_limit');
		}

        $this->request->post['config_tax'] = (int)$this->request->post['config_tax'];
        $this->request->post['config_customer_price'] = (int)$this->request->post['config_customer_price'];
        $this->request->post['config_customer_approval'] = (int)$this->request->post['config_customer_approval'];
        $this->request->post['config_guest_checkout'] = (int)$this->request->post['config_guest_checkout'];
        $this->request->post['config_stock_display'] = (int)$this->request->post['config_stock_display'];
        $this->request->post['config_stock_checkout'] = (int)$this->request->post['config_stock_checkout'];
        $this->request->post['config_cart_weight'] = (int)$this->request->post['config_cart_weight'];
        $this->request->post['config_ssl'] = (int)$this->request->post['config_ssl'];


		if (!$this->error) {
			return TRUE;
		} else {
			if (!isset($this->error['warning'])) {
				$this->error['warning'] = $this->language->get('error_required_data');
			}
			return FALSE;
		}
	}

	private function _validateDelete() {
		if (!$this->user->hasPermission('modify', 'setting/store')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		$this->loadModel('sale/order');
		
		$store_total = $this->model_sale_order->getTotalOrdersByStoreId($this->request->get['store_id']);

		if ($store_total) {
			$this->error['warning'] = sprintf($this->language->get('error_store'), $store_total);
		}	
		
		if (!$this->error) {
			return TRUE; 
		} else {
			return FALSE;
		}
	}

}
?>