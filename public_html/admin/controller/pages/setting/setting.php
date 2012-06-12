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
class ControllerPagesSettingSetting extends AController {
	private $error = array();
	public $groups = array('general', 'store', 'local', 'options', 'images', 'mail', 'server');
	public $data = array();
 
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->document->setTitle( $this->language->get('heading_title') );
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validate($this->request->get['active'])) {
            if (isset ( $this->request->post ['config_logo'] )) {
                $this->request->post['config_logo'] = html_entity_decode($this->request->post['config_logo'], ENT_COMPAT, 'UTF-8');
            }
            if (isset ( $this->request->post ['config_icon'] )) {
                $this->request->post['config_icon'] = html_entity_decode($this->request->post['config_icon'], ENT_COMPAT, 'UTF-8');
            }
			$this->model_setting_setting->editSetting($this->request->get['active'], $this->request->post);
			if ($this->config->get('config_currency_auto')) {
				$this->loadModel('localisation/currency');
				$this->model_localisation_currency->updateCurrencies();
			}

			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('setting/setting', '&active='.$this->request->get['active']));
		}

		$this->data['groups'] = $this->groups;
        if ( isset($this->request->get['active']) && strpos($this->request->get['active'], '-') !== false ) {
            $this->request->get['active'] = substr($this->request->get['active'], 0, strpos($this->request->get['active'], '-') );
        }
		$this->data['active'] = isset($this->request->get['active']) && in_array($this->request->get['active'], $this->data['groups']) ?
				$this->request->get['active'] : $this->data['groups'][0];
		$this->data['link_all'] = $this->html->getSecureURL('setting/setting/all');
		foreach ( $this->data['groups'] as $group ) {
			$this->data['link_'.$group] = $this->html->getSecureURL('setting/setting', '&active='.$group);
		}

		$this->data['token'] = $this->session->data['token'];
		$this->data['error'] = $this->error;
		
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
		}
		if (isset($this->session->data['error'])) {
			$this->error['warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		}
		
		$this->data['insert'] = $this->html->getSecureURL('setting/store/insert');
		$this->data['cancel'] = $this->html->getSecureURL('setting/setting');
		$this->data['action'] = $this->html->getSecureURL('setting/setting');
		
		$this->data['stores'] = array();
		$this->data['stores'][] = array(
			'name' => $this->language->get('text_default'),
			'href' => $this->html->getSecureURL('setting/setting')
		); 
		
		$this->loadModel('setting/store');
		$results = $this->model_setting_store->getStores();
		foreach ($results as $result) {
			$this->data['stores'][] = array(
				'name' => $result['name'],
				'href' => $this->html->getSecureURL('setting/store/update', '&store_id=' . $result['store_id'])
			); 
		}

		$this->data['settings'] = $this->model_setting_setting->getSetting( $this->data['active'] );
		foreach ( $this->data['settings'] as $key => $value ) {
			if (isset($this->request->post[$key])) {
				$this->data['settings'][$key] = $this->request->post[$key];
			}
		}

		$this->_getForm();

		$this->data['content_language_id'] = $this->session->data['content_language_id'];
        $this->data['common_zone'] = $this->html->getSecureURL('common/zone');
		$this->data['template_image'] = $this->html->getSecureURL('setting/template_image');

        $this->view->assign('help_url', $this->gen_help_url($this->data['active']) );
		$this->view->batchAssign( $this->data );
        $this->processTemplate('pages/setting/setting.tpl' );

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
	
	public function all() {

          //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->document->setTitle( $this->language->get('heading_title') );

		$this->view->assign('error_warning', $this->session->data['warning']);
		if (isset($this->session->data['warning'])) {
			unset($this->session->data['warning']);
		}
		$this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

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

		$grid_settings = array(
			'table_id' => 'setting_grid',
			'url' => $this->html->getSecureURL('listing_grid/setting'),
			'editurl' => '',
            'update_field' => '',
			'sortname' => 'group',
			'sortorder' => 'asc',
			'multiselect' => "false",
            'actions' => array(
                'edit' => array(
                    'text' => $this->language->get('text_edit'),
				    'href' => $this->html->getSecureURL('setting/setting', '&active=%ID%')
                ),
            ),
		);

        $grid_settings['colNames'] = array(
            $this->language->get('column_group'),
            $this->language->get('column_key'),
            $this->language->get('column_value'),
		);
		$grid_settings['colModel'] = array(
			array(
				'name' => 'group',
				'index' => 'group',
                'align' => 'left',
				'width' => 150,
			),
			array(
				'name' => 'key',
				'index' => 'key',
                'align' => 'left',
				'width' => 200,
			),
			array(
				'name' => 'value',
				'index' => 'value',
                'align' => 'left',
				'width' => 500,
				'sortable' => false,
				'search' => false,
			),
		);

		$form = new AForm();
	    $form->setForm(array(
		    'form_name' => 'setting_grid_search',
	    ));

	    $grid_search_form = array();
        $grid_search_form['id'] = 'setting_grid_search';
        $grid_search_form['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'setting_grid_search',
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

	    $grid_search_form['fields']['group'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'group',
            'options' => array(
	            '' => $this->language->get('text_select_group'),
	            'all' => 'all',
	            'general' => 'general',
	            'store' => 'store',
	            'local' => 'local',
	            'options' => 'options',
	            'images' => 'images',
	            'mail' => 'mail',
	            'server' => 'server',
            ),
	    ));

		$grid_settings['search_form'] = true;

		$this->data['insert'] = $this->html->getSecureURL('setting/store/insert');
		$this->data['stores'] = array();
		$this->data['stores'][] = array(
			'name' => $this->language->get('text_default'),
			'href' => $this->html->getSecureURL('setting/setting')
		);

		$this->loadModel('setting/store');
		$results = $this->model_setting_store->getStores();
		foreach ($results as $result) {
			$this->data['stores'][] = array(
				'name' => $result['name'],
				'href' => $this->html->getSecureURL('setting/store/update', '&store_id=' . $result['store_id'])
			);
		}

		$this->data['groups'] = $this->groups;
		$this->data['link_all'] = $this->html->getSecureURL('setting/setting/all');
		foreach ( $this->data['groups'] as $group ) {
			$this->data['link_'.$group] = $this->html->getSecureURL('setting/setting', '&active='.$group);
		}

		$grid = $this->dispatch('common/listing_grid', array( $grid_settings ) );
		$this->view->assign('listing_grid', $grid->dispatchGetOutput());
		$this->view->assign ( 'search_form', $grid_search_form );

        $this->view->batchAssign( $this->data );
		$this->view->assign('help_url', $this->gen_help_url('setting_listing') );

		$this->processTemplate('pages/setting/setting_list.tpl' );

          //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	private function _getForm(  ) {

		$yes_no = array(
			1 => $this->language->get('text_yes'),
			0 => $this->language->get('text_no'),
		);
		
		$this->data['action'] = $this->html->getSecureURL('setting/setting', '&active='.$this->data['active']);
		$this->data['form_title'] = $this->language->get('text_edit') . ' ' . $this->language->get('heading_title');
		$this->data['update'] = $this->html->getSecureURL('listing_grid/setting/update_field','&group='.$this->data['active']);
		$this->view->assign('language_code', $this->session->data['language']);
		$form = new AForm('HS');
		
		$form->setForm(array(
		    'form_name' => 'settingFrm',
			'update' => $this->data['update'],
	    ));

        $this->data['form']['id'] = 'settingFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'settingFrm',
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
		
		switch ( $this->data['active'] ) {
			case 'general':
				
				$this->data['form']['fields']['name'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'store_name',
					'value' => $this->data['settings']['store_name'],
					'required' => true,
					'style' => 'large-field',
				));
				$this->data['form']['fields']['url'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_url',
					'value' => $this->data['settings']['config_url'],
					'required' => true,
					'style' => 'large-field',
				));
				$this->data['form']['fields']['owner'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_owner',
					'value' => $this->data['settings']['config_owner'],
					'required' => true,
					'style' => 'large-field',
				));
				$this->data['form']['fields']['address'] = $form->getFieldHtml(array(
					'type' => 'textarea',
					'name' => 'config_address',
					'value' => $this->data['settings']['config_address'],
					'required' => true,
					'style' => 'large-field',
				));
				$this->data['form']['fields']['email'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'store_main_email',
					'value' => $this->data['settings']['store_main_email'],
					'required' => true,
					'style' => 'large-field',
				));
				$this->data['form']['fields']['telephone'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_telephone',
					'value' => $this->data['settings']['config_telephone'],
					'required' => true,
					'style' => 'medium-field',
				));
				$this->data['form']['fields']['fax'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_fax',
					'value' => $this->data['settings']['config_fax'],
					'style' => 'medium-field',
				));
				break;

			case 'store' :

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

				$this->data['form_language_switch'] = $this->html->getContentLanguageSwitcher();

				$this->data['form']['fields']['title'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_title',
					'value' => $this->data['settings']['config_title'],
					'required' => true,
					'style' => 'medium-field',
				));
				$this->data['form']['fields']['meta_description'] = $form->getFieldHtml(array(
					'type' => 'textarea',
					'name' => 'config_meta_description',
					'value' => $this->data['settings']['config_meta_description'],
					'style' => 'large-field',
				));
				$this->data['form']['fields']['template'] = $form->getFieldHtml(array(
					'type' => 'selectbox',
					'name' => 'config_storefront_template',
					'value' => $this->data['settings']['config_storefront_template'],
					'options' => $templates,
					'style' => 'large-field',
				));
				$this->data['form']['fields']['description'] = $form->getFieldHtml(array(
					'type' => 'textarea',
					'name' => 'config_description_'.$this->session->data['content_language_id'],
					'value' => $this->data['settings']['config_description_'.$this->session->data['content_language_id']],
					'style' => 'xl-field',
				));
				$this->data['rl'] = $this->html->getSecureURL('common/resource_library', '&type=image&mode=url');
				break;

			case 'local' :
				$this->loadModel('localisation/country');
				$countries = array();
				$results = $this->model_localisation_country->getCountries();
				foreach ( $results as $c ) {
					$countries[ $c['country_id'] ] = $c['name'];
				}

				$results = $this->language->getAvailableLanguages();
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

				$this->loadModel('localisation/length_class');
				$results = $this->model_localisation_length_class->getLengthClasses();
				$length_classes = array();
				foreach ( $results as $v ) {
					$length_classes[ $v['unit'] ] = $v['title'];
				}

				$this->loadModel('localisation/weight_class');
				$results = $this->model_localisation_weight_class->getWeightClasses();
				$weight_classes = array();
				foreach ( $results as $v ) {
					$weight_classes[ $v['unit'] ] = $v['title'];
				}

				$this->data['form']['fields']['country'] = $form->getFieldHtml(array(
					'type' => 'selectbox',
					'name' => 'config_country_id',
					'value' => $this->data['settings']['config_country_id'],
					'options' => $countries,
					'style' => 'large-field',
				));
				$this->data['form']['fields']['zone'] = $form->getFieldHtml(array(
					'type' => 'selectbox',
					'name' => 'config_zone_id',
					'value' => $this->data['settings']['config_zone_id'],
					'options' => array(),
					'style' => 'large-field',
				));
				$this->data['form']['fields']['language'] = $form->getFieldHtml(array(
					'type' => 'selectbox',
					'name' => 'config_storefront_language',
					'value' => $this->data['settings']['config_storefront_language'],
					'options' => $languages,
				));
				$this->data['form']['fields']['admin_language'] = $form->getFieldHtml(array(
					'type' => 'selectbox',
					'name' => 'admin_language',
					'value' => $this->data['settings']['admin_language'],
					'options' => $languages,
				));
				$this->data['form']['fields']['currency'] = $form->getFieldHtml(array(
					'type' => 'selectbox',
					'name' => 'config_currency',
					'value' => $this->data['settings']['config_currency'],
					'options' => $currencies,
				));
				$this->data['form']['fields']['currency_auto'] = $form->getFieldHtml(array(
					'type' => 'selectbox',
					'name' => 'config_currency_auto',
					'value' => $this->data['settings']['config_currency_auto'],
					'options' => $yes_no,
				));
				$this->data['form']['fields']['length_class'] = $form->getFieldHtml(array(
					'type' => 'selectbox',
					'name' => 'config_length_class',
					'value' => $this->data['settings']['config_length_class'],
					'options' => $length_classes,
				));
					
				$this->data['form']['fields']['weight_class'] = $form->getFieldHtml(array(
					'type' => 'selectbox',
					'name' => 'config_weight_class',
					'value' => $this->data['settings']['config_weight_class'],
					'options' => $weight_classes,
				));		
				break;

			case 'options':

				$this->loadModel('sale/customer_group');
				$results = $this->model_sale_customer_group->getCustomerGroups();
				$customer_groups = array();
				foreach( $results as $item ) {
					$customer_groups[ $item['customer_group_id'] ] = $item['name'];
				}
					
				$this->loadModel('localisation/stock_status');
				$stock_statuses = array();
				$results = $this->model_localisation_stock_status->getStockStatuses();
				foreach( $results as $item ) {
					$stock_statuses[ $item['stock_status_id'] ] = $item['name'];
				}
					
				$this->loadModel('localisation/order_status');
				$order_statuses = array();
				$results = $this->model_localisation_order_status->getOrderStatuses();
				foreach( $results as $item ) {
					$order_statuses[ $item['order_status_id'] ] = $item['name'];
				}

                $this->acm = new AContentManager();
                $results = $this->acm->getContents();
                $contents = array( '' => $this->language->get('text_none') );
                foreach( $results as $item ) {
					if(!$item['status']) continue;
					$contents[ $item['content_id'] ] = $item['title'];
				}

				$this->data['form']['fields']['catalog_limit'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_catalog_limit',
					'value' => $this->data['settings']['config_catalog_limit'],
					'required' => true,
					'style' => 'small-field',
				));
				$this->data['form']['fields']['bestseller_limit'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_bestseller_limit',
					'value' => $this->data['settings']['config_bestseller_limit'],
					'required' => true,
					'style' => 'small-field',
				));
				$this->data['form']['fields']['featured_limit'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_featured_limit',
					'value' => $this->data['settings']['config_featured_limit'],
					'required' => true,
					'style' => 'small-field',
				));
				$this->data['form']['fields']['latest_limit'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_latest_limit',
					'value' => $this->data['settings']['config_latest_limit'],
					'required' => true,
					'style' => 'small-field',
				));
				$this->data['form']['fields']['special_limit'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_special_limit',
					'value' => $this->data['settings']['config_special_limit'],
					'required' => true,
					'style' => 'small-field',
				));
				$this->data['form']['fields']['tax'] = $form->getFieldHtml(array(
					'type' => 'checkbox',
					'name' => 'config_tax',
					'value' => $this->data['settings']['config_tax'],
					'style' => 'btn_switch',
				));
				$this->data['form']['fields']['tax_store'] = $form->getFieldHtml(array(
					'type' => 'selectbox',
					'name' => 'config_tax_store',
					'value' => $this->data['settings']['config_tax_store'],
					'options' => array($this->language->get('entry_tax_store_0'),$this->language->get('entry_tax_store_1')),
				));
				$this->data['form']['fields']['tax_customer'] = $form->getFieldHtml(array(
					'type' => 'selectbox',
					'name' => 'config_tax_customer',
					'value' => $this->data['settings']['config_tax_customer'],
					'options' => array($this->language->get('entry_tax_customer_0'),$this->language->get('entry_tax_customer_1')),
				));
				$this->data['form']['fields']['invoice'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'starting_invoice_id',
					'value' => $this->data['settings']['starting_invoice_id'],
					'style' => 'small-field',
				));
				$this->data['form']['fields']['invoice_prefix'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'invoice_prefix',
					'value' => $this->data['settings']['invoice_prefix'],
					'style' => 'small-field',
				));
				$this->data['form']['fields']['customer_group'] = $form->getFieldHtml(array(
					'type' => 'selectbox',
					'name' => 'config_customer_group_id',
					'value' => $this->data['settings']['config_customer_group_id'],
					'options' => $customer_groups,
				));
				$this->data['form']['fields']['customer_price'] = $form->getFieldHtml(array(
					'type' => 'checkbox',
					'name' => 'config_customer_price',
					'value' => $this->data['settings']['config_customer_price'],
					'style' => 'btn_switch',
				));
				$this->data['form']['fields']['customer_approval'] = $form->getFieldHtml(array(
					'type' => 'checkbox',
					'name' => 'config_customer_approval',
					'value' => $this->data['settings']['config_customer_approval'],
					'style' => 'btn_switch',
				));
				$this->data['form']['fields']['guest_checkout'] = $form->getFieldHtml(array(
					'type' => 'checkbox',
					'name' => 'config_guest_checkout',
					'value' => $this->data['settings']['config_guest_checkout'],
					'style' => 'btn_switch',
				));
				$this->data['form']['fields']['account'] = $form->getFieldHtml(array(
					'type' => 'selectbox',
					'name' => 'config_account_id',
					'value' => $this->data['settings']['config_account_id'],
					'options' => $contents,
				));
				$this->data['form']['fields']['checkout'] = $form->getFieldHtml(array(
					'type' => 'selectbox',
					'name' => 'config_checkout_id',
					'value' => $this->data['settings']['config_checkout_id'],
					'options' => $contents,
				));
				$this->data['form']['fields']['stock_display'] = $form->getFieldHtml(array(
					'type' => 'checkbox',
					'name' => 'config_stock_display',
					'value' => $this->data['settings']['config_stock_display'],
					'style' => 'btn_switch',
				));
				$this->data['form']['fields']['stock_checkout'] = $form->getFieldHtml(array(
					'type' => 'checkbox',
					'name' => 'config_stock_checkout',
					'value' => $this->data['settings']['config_stock_checkout'],
					'style' => 'btn_switch',
				));
				$this->data['form']['fields']['order_status'] = $form->getFieldHtml(array(
					'type' => 'selectbox',
					'name' => 'config_order_status_id',
					'value' => $this->data['settings']['config_order_status_id'],
					'options' => $order_statuses,
				));
				$this->data['form']['fields']['stock_status'] = $form->getFieldHtml(array(
					'type' => 'selectbox',
					'name' => 'config_stock_status_id',
					'value' => $this->data['settings']['config_stock_status_id'],
					'options' => $stock_statuses,
				));
				$this->data['form']['fields']['reviews'] = $form->getFieldHtml(array(
					'type' => 'checkbox',
					'name' => 'enable_reviews',
					'value' => $this->data['settings']['enable_reviews'],
					'style' => 'btn_switch',
				));
				$this->data['form']['fields']['download'] = $form->getFieldHtml(array(
					'type' => 'checkbox',
					'name' => 'config_download',
					'value' => $this->data['settings']['config_download'],
					'style' => 'btn_switch',
				));
				$this->data['form']['fields']['download_status'] = $form->getFieldHtml(array(
					'type' => 'selectbox',
					'name' => 'config_download_status',
					'value' => $this->data['settings']['config_download_status'],
					'options' => $order_statuses,
				));
				$this->data['form']['fields']['cart_weight'] = $form->getFieldHtml(array(
					'type' => 'checkbox',
					'name' => 'config_cart_weight',
					'value' => $this->data['settings']['config_cart_weight'],
					'style' => 'btn_switch',
				));
				$this->data['form']['fields']['shipping_session'] = $form->getFieldHtml(array(
					'type' => 'checkbox',
					'name' => 'config_shipping_session',
					'value' => $this->data['settings']['config_shipping_session'],
					'style' => 'btn_switch',
				));		
				break;

			case 'images':
				$resource = new AResource( 'image' );

				$this->data['logo'] = $this->dispatch(
					'responses/common/resource_library/get_resource_html_single',
					array('type'=>'image',
						  'wrapper_id'=>'config_logo',
						  'resource_id'=> $resource->getIdFromHexPath(str_replace('image/','',$this->data['settings']['config_logo'])),
						  'field' => 'config_logo'));
				$this->data['logo'] = $this->data['logo']->dispatchGetOutput();

				$this->data['icon'] = $this->dispatch(
					'responses/common/resource_library/get_resource_html_single',
					array('type'=>'image',
						  'wrapper_id'=>'config_icon',
						  'resource_id'=> $resource->getIdFromHexPath(str_replace('image/','',$this->data['settings']['config_icon'])),
						  'field' => 'config_icon'));
				$this->data['icon'] = $this->data['icon']->dispatchGetOutput();

                $resources_scripts = $this->dispatch(
                    'responses/common/resource_library/get_resources_scripts',
                    array(
                        'object_name' => 'store',
                        'object_id' => '0',
                        'types' => 'image',
                        'mode' => 'url'
                    )
                );
                $this->data['resources_scripts'] = $resources_scripts->dispatchGetOutput();

                $this->data['form']['fields']['logo'] = $form->getFieldHtml(array(
					'type' => 'hidden',
					'name' => 'config_logo',
                    'value' => htmlspecialchars($this->data['settings']['config_logo'], ENT_COMPAT, 'UTF-8'),
				));
				$this->data['form']['fields']['icon'] = $form->getFieldHtml(array(
					'type' => 'hidden',
					'name' => 'config_icon',
                    'value' => htmlspecialchars($this->data['settings']['config_icon'], ENT_COMPAT, 'UTF-8'),
				));
				$this->data['form']['fields']['image_thumb_width'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_image_thumb_width',
					'value' => $this->data['settings']['config_image_thumb_width'],
					'style' => 'small-field',
                    'required' => true,
				));
                $this->data['form']['fields']['image_thumb_height'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_image_thumb_height',
					'value' => $this->data['settings']['config_image_thumb_height'],
					'style' => 'small-field',
					'required' => true,
				));
				/*$this->data['form']['fields']['image_popup_width'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_image_popup_width',
					'value' => $this->data['settings']['config_image_popup_width'],
					'style' => 'small-field',
                    'required' => true,
				));
                $this->data['form']['fields']['image_popup_height'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_image_popup_height',
					'value' => $this->data['settings']['config_image_popup_height'],
					'style' => 'small-field',
					'required' => true,
				));*/
				$this->data['form']['fields']['image_category_width'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_image_category_width',
					'value' => $this->data['settings']['config_image_category_width'],
					'style' => 'small-field',
                    'required' => true,
				));
                $this->data['form']['fields']['image_category_height'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_image_category_height',
					'value' => $this->data['settings']['config_image_category_height'],
					'style' => 'small-field',
					'required' => true,
				));
				$this->data['form']['fields']['image_product_width'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_image_product_width',
					'value' => $this->data['settings']['config_image_product_width'],
					'style' => 'small-field',
                    'required' => true,
				));
                $this->data['form']['fields']['image_product_height'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_image_product_height',
					'value' => $this->data['settings']['config_image_product_height'],
					'style' => 'small-field',
					'required' => true,
				));
				$this->data['form']['fields']['image_additional_width'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_image_additional_width',
					'value' => $this->data['settings']['config_image_additional_width'],
					'style' => 'small-field',
                    'required' => true,
				));
                $this->data['form']['fields']['image_additional_height'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_image_additional_height',
					'value' => $this->data['settings']['config_image_additional_height'],
					'style' => 'small-field',
					'required' => true,
				));
				$this->data['form']['fields']['image_related_width'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_image_related_width',
					'value' => $this->data['settings']['config_image_related_width'],
					'style' => 'small-field',
                    'required' => true,
				));
                $this->data['form']['fields']['image_related_height'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_image_related_height',
					'value' => $this->data['settings']['config_image_related_height'],
					'style' => 'small-field',
					'required' => true,
				));
				$this->data['form']['fields']['image_cart_width'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_image_cart_width',
					'value' => $this->data['settings']['config_image_cart_width'],
					'style' => 'small-field',
                    'required' => true,
				));
                $this->data['form']['fields']['image_cart_height'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_image_cart_height',
					'value' => $this->data['settings']['config_image_cart_height'],
					'style' => 'small-field',
					'required' => true,
				));
                $this->data['form']['fields']['image_grid_width'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_image_grid_width',
					'value' => $this->data['settings']['config_image_grid_width'],
					'style' => 'small-field',
                    'required' => true,
				));
                $this->data['form']['fields']['image_grid_height'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_image_grid_height',
					'value' => $this->data['settings']['config_image_grid_height'],
					'style' => 'small-field',
					'required' => true,
				));
				break;

			case 'mail' :

				$this->data['form']['fields']['mail_protocol'] = $form->getFieldHtml(array(
					'type' => 'selectbox',
					'name' => 'config_mail_protocol',
					'value' => $this->data['settings']['config_mail_protocol'],
					'options' => array(
						'mail' => $this->language->get('text_mail'),
						'smtp' => $this->language->get('text_smtp'),
					),
					'style' => "no-save",
				));
				$this->data['form']['fields']['mail_parameter'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_mail_parameter',
					'value' => $this->data['settings']['config_mail_parameter'],
				));
				$this->data['form']['fields']['smtp_host'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_smtp_host',
					'value' => $this->data['settings']['config_smtp_host'],
					'style' => "no-save",
					'required'=> true
				));
				$this->data['form']['fields']['smtp_username'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_smtp_username',
					'value' => $this->data['settings']['config_smtp_username'],
				));
				$this->data['form']['fields']['smtp_password'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_smtp_password',
					'value' => $this->data['settings']['config_smtp_password'],
				));
				$this->data['form']['fields']['smtp_port'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_smtp_port',
					'value' => $this->data['settings']['config_smtp_port'],
					'style' => "no-save",
					'required'=> true
				));
				$this->data['form']['fields']['smtp_timeout'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_smtp_timeout',
					'value' => $this->data['settings']['config_smtp_timeout'],
					'style' => "no-save",
					'required'=> true
				));
				$this->data['form']['fields']['alert_mail'] = $form->getFieldHtml(array(
					'type' => 'selectbox',
					'name' => 'config_alert_mail',
					'value' => $this->data['settings']['config_alert_mail'],
					'options' => $yes_no,
				));
				$this->data['form']['fields']['alert_emails'] = $form->getFieldHtml(array(
					'type' => 'textarea',
					'name' => 'config_alert_emails',
					'value' => $this->data['settings']['config_alert_emails'],
					'style' => 'large-field',
				));
				break;

			case 'server':

				if($this->data['settings']['storefront_template_debug']) {
					$this->session->data['tmpl_debug'] = AEncryption::getHash(mt_rand());
					$this->data['storefront_debug_url'] = $this->html->getCatalogURL('index/home', '&tmpl_debug=' . $this->session->data['tmpl_debug']);
					$this->data['admin_debug_url'] = $this->html->getSecureURL('index/home', '&tmpl_debug=' . $this->session->data['tmpl_debug']);
				} else {
					unset($this->session->data['tmpl_debug']);
					$this->data['storefront_debug_url'] = '';
					$this->data['admin_debug_url'] = '';
				}
					
				$ignore = array(
					'common/login',
					'common/logout',
					'error/not_found',
					'error/permission'
				);
				
				$this->data['tokens'] = array();
				
				$files_pages = glob(DIR_APP_SECTION . 'controller/pages/*/*.php');
				$files_response = glob(DIR_APP_SECTION . 'controller/responses/*/*.php');
				$files = array_merge($files_pages, $files_response);

				foreach ($files as $file) {
					$data = explode('/', dirname($file));
					$token = end($data) . '/' . basename($file, '.php');
					if (!in_array($token, $ignore)) {
						$this->data['tokens'][$token] = $token;
					}
				}

				$this->data['form']['fields']['ssl'] = $form->getFieldHtml(array(
					'type' => 'checkbox',
					'name' => 'config_ssl',
					'value' => $this->data['settings']['config_ssl'],
					'style' => 'btn_switch',
				));
				$this->data['form']['fields']['session_ttl'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_session_ttl',
					'value' => $this->data['settings']['config_session_ttl'],
				));
				$this->data['form']['fields']['maintenance'] = $form->getFieldHtml(array(
					'type' => 'checkbox',
					'name' => 'config_maintenance',
					'value' => $this->data['settings']['config_maintenance'],
					'style' => 'btn_switch',
				));
				$this->data['form']['fields']['encryption'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'encryption_key',
					'value' => $this->data['settings']['encryption_key'],
				));
				$this->data['form']['fields']['seo_url'] = $form->getFieldHtml(array(
					'type' => 'checkbox',
					'name' => 'enable_seo_url',
					'value' => $this->data['settings']['enable_seo_url'],
					'style' => 'btn_switch',
				));
				$this->data['form']['fields']['compression'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_compression',
					'value' => $this->data['settings']['config_compression'],
				));
				$this->data['form']['fields']['cache_enable'] = $form->getFieldHtml(array(
					'type' => 'checkbox',
					'name' => 'config_cache_enable',
					'value' => $this->data['settings']['config_cache_enable'],
					'style' => 'btn_switch',
				));
				$this->data['form']['fields']['upload_max_size'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_upload_max_size',
					'value' => $this->data['settings']['config_upload_max_size'],
					'attr' => ' onKeyUp="formatQty(this);"',
				));
				$this->data['form']['fields']['storefront_api_status'] = $form->getFieldHtml(array(
					'type' => 'checkbox',
					'name' => 'config_storefront_api_status',
					'value' => $this->data['settings']['config_storefront_api_status'],
					'style' => 'btn_switch',
					'help_url' => $this->gen_help_url('storefront_api')
				));
				$this->data['form']['fields']['storefront_api_key'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_storefront_api_key',
					'value' => $this->data['settings']['config_storefront_api_key'],
				));

				$this->data['form']['fields']['error_display'] = $form->getFieldHtml(array(
					'type' => 'checkbox',
					'name' => 'config_error_display',
					'value' => $this->data['settings']['config_error_display'],
					'style' => 'btn_switch',
				));
				$this->data['form']['fields']['error_log'] = $form->getFieldHtml(array(
					'type' => 'checkbox',
					'name' => 'config_error_log',
					'value' => $this->data['settings']['config_error_log'],
					'style' => 'btn_switch',
				));
				$this->data['form']['fields']['debug'] = $form->getFieldHtml(array(
					'type' => 'selectbox',
					'name' => 'config_debug',
					'value' => $this->data['settings']['config_debug'],
					'options' => array(
						0 => $this->language->get('entry_debug_0'),
						1 => $this->language->get('entry_debug_1'),
						2 => $this->language->get('entry_debug_2'),
					),
				));
				$this->data['form']['fields']['debug_level'] = $form->getFieldHtml(array(
					'type' => 'selectbox',
					'name' => 'config_debug_level',
					'value' => $this->data['settings']['config_debug_level'],
					'options' => array(
						0 => $this->language->get('entry_debug_level_0'),
						1 => $this->language->get('entry_debug_level_1'),
						2 => $this->language->get('entry_debug_level_2'),
						3 => $this->language->get('entry_debug_level_3'),
						4 => $this->language->get('entry_debug_level_4'),
						5 => $this->language->get('entry_debug_level_5'),
					),
				));
				$this->data['form']['fields']['template_debug'] = $form->getFieldHtml(array(
					'type' => 'checkbox',
					'name' => 'storefront_template_debug',
					'value' => $this->data['settings']['storefront_template_debug'],
					'style' => 'btn_switch',
					'attr' => 'reload_on_save="true"'
				));
				$this->data['form']['fields']['error_filename'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'config_error_filename',
					'value' => $this->data['settings']['config_error_filename'],
					'required' => true,
				));
				$this->data['form']['fields']['help_links'] = $form->getFieldHtml(array(
					'type' => 'checkbox',
					'name' => 'config_help_links',
					'value' => $this->data['settings']['config_help_links'],
					'style' => 'btn_switch',
				));
				break;

			default:
		}
		
	}
	
	private function _validate( $group ) {
		if (!$this->user->hasPermission('modify', 'setting/setting')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		switch ( $group ) {
			case 'general':
				if (!$this->request->post['store_name']) {
					$this->error['name'] = $this->language->get('error_name');
				}

				if (!$this->request->post['config_url']) {
					$this->error['url'] = $this->language->get('error_url');
				}

				if ((strlen(utf8_decode($this->request->post['config_owner'])) < 2) || (strlen(utf8_decode($this->request->post['config_owner'])) > 64)) {
					$this->error['owner'] = $this->language->get('error_owner');
				}

				if ((strlen(utf8_decode($this->request->post['config_address'])) < 2) || (strlen(utf8_decode($this->request->post['config_address'])) > 256)) {
					$this->error['address'] = $this->language->get('error_address');
				}
					
				$pattern = '/^[A-Z0-9._%-]+@[A-Z0-9][A-Z0-9.-]{0,61}[A-Z0-9]\.[A-Z]{2,6}$/i';
				if ((strlen(utf8_decode($this->request->post['store_main_email'])) > 96) || (!preg_match($pattern, $this->request->post['store_main_email']))) {
					$this->error['email'] = $this->language->get('error_email');
				}
		
				if ((strlen(utf8_decode($this->request->post['config_telephone'])) < 2) || (strlen(utf8_decode($this->request->post['config_telephone'])) > 32)) {
					$this->error['telephone'] = $this->language->get('error_telephone');
				}	
				break;


			case 'store':
				if (!$this->request->post['config_title']) {
					$this->error['title'] = $this->language->get('error_title');
				}
				break;

			case 'local':
				break;

			case 'options':
				/*if (!$this->request->post['config_admin_limit']) {
					$this->error['admin_limit'] = $this->language->get('error_limit');
				}*/

				if (!$this->request->post['config_catalog_limit']) {
					$this->error['catalog_limit'] = $this->language->get('error_limit');
				}

				if (!$this->request->post['config_bestseller_limit']) {
					$this->error['bestseller_limit'] = $this->language->get('error_limit');
				}

				if (!$this->request->post['config_featured_limit']) {
					$this->error['featured_limit'] = $this->language->get('error_limit');
				}

				if (!$this->request->post['config_latest_limit']) {
					$this->error['latest_limit'] = $this->language->get('error_limit');
				}

				if (!$this->request->post['config_special_limit']) {
					$this->error['special_limit'] = $this->language->get('error_limit');
				}
				break;

			case 'images':
				if (!$this->request->post['config_image_thumb_width'] || !$this->request->post['config_image_thumb_height']) {
					$this->error['image_thumb_height'] = $this->language->get('error_image_thumb');
				}

				/*if (!$this->request->post['config_image_popup_width'] || !$this->request->post['config_image_popup_height']) {
					$this->error['image_popup_height'] = $this->language->get('error_image_popup');
				}*/

				if (!$this->request->post['config_image_category_width'] || !$this->request->post['config_image_category_height']) {
					$this->error['image_category_height'] = $this->language->get('error_image_category');
				}

				if (!$this->request->post['config_image_product_width'] || !$this->request->post['config_image_product_height']) {
					$this->error['image_product_height'] = $this->language->get('error_image_product');
				}

				if (!$this->request->post['config_image_additional_width'] || !$this->request->post['config_image_additional_height']) {
					$this->error['image_additional_height'] = $this->language->get('error_image_additional');
				}

				if (!$this->request->post['config_image_related_width'] || !$this->request->post['config_image_related_height']) {
					$this->error['image_related_height'] = $this->language->get('error_image_related');
				}

				if (!$this->request->post['config_image_cart_width'] || !$this->request->post['config_image_cart_height']) {
					$this->error['image_cart_height'] = $this->language->get('error_image_cart');
				}

                if (!$this->request->post['config_image_grid_width'] || !$this->request->post['config_image_grid_height']) {
					$this->error['image_grid_height'] = $this->language->get('error_image_grid');
				}
				break;

			case 'mail':

				if($this->request->post['config_mail_protocol']=='smtp'
				   && (!$this->request->post['config_smtp_host'] || !$this->request->post['config_smtp_port'] || !$this->request->post['config_smtp_timeout'] )){
					$this->error['mail'] = $this->language->get('error_mail');
				}

				break;

			case 'server':
				if (!$this->request->post['config_error_filename']) {
					$this->error['error_filename'] = $this->language->get('error_error_filename');
				}
				break;

			default:
		}

		if (!$this->error) {
			return TRUE;
		} else {
			if (!isset($this->error['warning'])) {
				$this->error['warning'] = $this->language->get('error_required_data');
			}
			return FALSE;
		}
	}

}
?>