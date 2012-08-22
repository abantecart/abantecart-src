<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011, 2012 Belavier Commerce LLC

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

class AConfigManager {
	protected $registry;
	public $errors = 0;
	private $temp = array();
	private $level = 0;
	private $groups = array();

	public function __construct() {
		if (!IS_ADMIN) { // forbid for non admin calls
			throw new AException (AC_ERR_LOAD, 'Error: permission denied to access class AConfigManager');
		}
		$this->registry = Registry::getInstance();
		$this->load->model('setting/extension');
		$this->load->model('setting/setting');
		$this->groups = $this->config->groups;
	}

	public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

	/*
	*	Build field for provided key setting
	*   Form - form object where filed will be shown
	*   Data - current settig data
	*   Store_id - Seleted store ID for the setting
	*/
	public function getFormField( $setting_key, $form, $data, $store_id ) {
		//locate setting group first
		$group = $this->model_setting_setting->getSettingGroup($setting_key, $store_id);
		$fields = $this->getFormFields($group, $form, $data);
		return $fields[$setting_key];
	}

	/*
	*	Build fields array for provided setting group (section)
	*   Form - form object where filed will be shown
	*   Data - current settig data
	*/
	
	public function getFormFields( $group, $form, $data ) {
		$method_name = "_build_form_".$group;
		if (!method_exists( $this, $method_name )) {
			return array();
		}
		return $this->$method_name($form, $data);
	}
	
	private function _build_form_details( $form, $data ) {
		$fields = array();
 		// details section
        $fields['name'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'store_name',
            'value' => $data['store_name'],
            'required' => true,
            'style' => 'large-field',
        ));
        $fields['url'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_url',
            'value' => $data['config_url'],
            'required' => true,
            'style' => 'large-field',
        ));
        $fields['title'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_title',
            'value' => $data['config_title'],
            'required' => true,
            'style' => 'large-field',
        ));
        $fields['meta_description'] = $form->getFieldHtml(array(
            'type' => 'textarea',
            'name' => 'config_meta_description',
            'value' => $data['config_meta_description'],
            'style' => 'large-field',
        ));
        $fields['description'] = $form->getFieldHtml(array(
            'type' => 'textarea',
            'name' => 'config_description_' . $this->session->data['content_language_id'],
            'value' => $data['config_description_' . $this->session->data['content_language_id']],
            'style' => 'xl-field',
        ));
        $fields['owner'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_owner',
            'value' => $data['config_owner'],
            'required' => true,
            'style' => 'large-field',
        ));
        $fields['address'] = $form->getFieldHtml(array(
            'type' => 'textarea',
            'name' => 'config_address',
            'value' => $data['config_address'],
            'required' => true,
            'style' => 'large-field',
        ));
        $fields['email'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'store_main_email',
            'value' => $data['store_main_email'],
            'required' => true,
            'style' => 'large-field',
        ));
        $fields['telephone'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_telephone',
            'value' => $data['config_telephone'],
            'required' => true,
            'style' => 'medium-field',
        ));
        $fields['fax'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_fax',
            'value' => $data['config_fax'],
            'style' => 'medium-field',
        ));

        $this->load->model('localisation/country');
        $countries = array();
        $results =  $this->model_localisation_country->getCountries();
        foreach ($results as $c) {
            $countries[$c['country_id']] = $c['name'];
        }

        $results = $this->language->getAvailableLanguages();
        $languages = array();
        foreach ($results as $v) {
            $languages[$v['code']] = $v['name'];
        }

        $this->load->model('localisation/currency');
        $results = $this->model_localisation_currency->getCurrencies();
        $currencies = array();
        foreach ($results as $v) {
            $currencies[$v['code']] = $v['title'];
        }

        $this->load->model('localisation/length_class');
        $results = $this->model_localisation_length_class->getLengthClasses();
        $length_classes = array();
        foreach ($results as $v) {
            $length_classes[$v['unit']] = $v['title'];
        }

        $this->load->model('localisation/weight_class');
        $results = $this->model_localisation_weight_class->getWeightClasses();
        $weight_classes = array();
        foreach ($results as $v) {
            $weight_classes[$v['unit']] = $v['title'];
        }

        $fields['country'] = $form->getFieldHtml(array(
            'type' => 'selectbox',
            'name' => 'config_country_id',
            'value' => $data['config_country_id'],
            'options' => $countries,
            'style' => 'large-field',
        ));
        $fields['zone'] = $form->getFieldHtml(array(
            'type' => 'selectbox',
            'name' => 'config_zone_id',
            'value' => $data['config_zone_id'],
            'options' => array(),
            'style' => 'large-field',
        ));
        $fields['language'] = $form->getFieldHtml(array(
            'type' => 'selectbox',
            'name' => 'config_storefront_language',
            'value' => $data['config_storefront_language'],
            'options' => $languages,
        ));
        $fields['admin_language'] = $form->getFieldHtml(array(
            'type' => 'selectbox',
            'name' => 'admin_language',
            'value' => $data['admin_language'],
            'options' => $languages,
        ));
        $fields['currency'] = $form->getFieldHtml(array(
            'type' => 'selectbox',
            'name' => 'config_currency',
            'value' => $data['config_currency'],
            'options' => $currencies,
        ));
        $fields['currency_auto'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_currency_auto',
            'value' => $data['config_currency_auto'],
            'style' => 'btn_switch',
        ));
        $fields['length_class'] = $form->getFieldHtml(array(
            'type' => 'selectbox',
            'name' => 'config_length_class',
            'value' => $data['config_length_class'],
            'options' => $length_classes,
        ));

        $fields['weight_class'] = $form->getFieldHtml(array(
            'type' => 'selectbox',
            'name' => 'config_weight_class',
            'value' => $data['config_weight_class'],
            'options' => $weight_classes,
        ));
		return $fields;
	}
	
	private function _build_form_general( $form, $data ) {
		$fields = array();
		//general section
        $this->load->model('localisation/stock_status');
        $stock_statuses = array();
        $results = $this->model_localisation_stock_status->getStockStatuses();
        foreach ($results as $item) {
            $stock_statuses[$item['stock_status_id']] = $item['name'];
        }

        $fields['catalog_limit'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_catalog_limit',
            'value' => $data['config_catalog_limit'],
            'required' => true,
            'style' => 'small-field',
        ));
        $fields['admin_limit'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_admin_limit',
            'value' => $data['config_admin_limit'],
            'required' => true,
            'style' => 'small-field',
        ));
        $fields['bestseller_limit'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_bestseller_limit',
            'value' => $data['config_bestseller_limit'],
            'required' => true,
            'style' => 'small-field',
        ));
        $fields['featured_limit'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_featured_limit',
            'value' => $data['config_featured_limit'],
            'required' => true,
            'style' => 'small-field',
        ));
        $fields['latest_limit'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_latest_limit',
            'value' => $data['config_latest_limit'],
            'required' => true,
            'style' => 'small-field',
        ));
        $fields['special_limit'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_special_limit',
            'value' => $data['config_special_limit'],
            'required' => true,
            'style' => 'small-field',
        ));
        $fields['stock_display'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_stock_display',
            'value' => $data['config_stock_display'],
            'style' => 'btn_switch',
        ));
        $fields['nostock_autodisable'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_nostock_autodisable',
            'value' => $data['config_nostock_autodisable'],
            'style' => 'btn_switch',
        ));
        $fields['stock_status'] = $form->getFieldHtml(array(
            'type' => 'selectbox',
            'name' => 'config_stock_status_id',
            'value' => $data['config_stock_status_id'],
            'options' => $stock_statuses,
        ));        
        $fields['reviews'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'enable_reviews',
            'value' => $data['enable_reviews'],
            'style' => 'btn_switch',
        ));
        $fields['download'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_download',
            'value' => $data['config_download'],
            'style' => 'btn_switch',
        ));
        $this->load->model('localisation/order_status');
        $order_statuses = array();
        $results = $this->model_localisation_order_status->getOrderStatuses();
        foreach ($results as $item) {
            $order_statuses[$item['order_status_id']] = $item['name'];
        }
        $fields['download_status'] = $form->getFieldHtml(array(
            'type' => 'selectbox',
            'name' => 'config_download_status',
            'value' => $data['config_download_status'],
            'options' => $order_statuses,
        ));
        $fields['help_links'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_help_links',
            'value' => $data['config_help_links'],
            'style' => 'btn_switch',
        ));

		return $fields;
	}
	
	private function _build_form_checkout( $form, $data ) {
		$fields = array();
		//checkout section
        $this->load->model('sale/customer_group');
        $results = $this->model_sale_customer_group->getCustomerGroups();
        $customer_groups = array();
        foreach ($results as $item) {
            $customer_groups[$item['customer_group_id']] = $item['name'];
        }

        $this->load->model('localisation/order_status');
        $order_statuses = array();
        $results = $this->model_localisation_order_status->getOrderStatuses();
        foreach ($results as $item) {
            $order_statuses[$item['order_status_id']] = $item['name'];
        }

        $cntmnr = new AContentManager();
        $results = $cntmnr->getContents();
        $contents = array('' => $this->language->get('text_none'));
        foreach ($results as $item) {
            if (!$item['status']) continue;
            $contents[$item['content_id']] = $item['title'];
        }

        $fields['tax'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_tax',
            'value' => $data['config_tax'],
            'style' => 'btn_switch',
        ));
        $fields['tax_store'] = $form->getFieldHtml(array(
            'type' => 'selectbox',
            'name' => 'config_tax_store',
            'value' => $data['config_tax_store'],
            'options' => array($this->language->get('entry_tax_store_0'), $this->language->get('entry_tax_store_1')),
        ));
        $fields['tax_customer'] = $form->getFieldHtml(array(
            'type' => 'selectbox',
            'name' => 'config_tax_customer',
            'value' => $data['config_tax_customer'],
            'options' => array($this->language->get('entry_tax_customer_0'), $this->language->get('entry_tax_customer_1')),
        ));
        $fields['invoice'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'starting_invoice_id',
            'value' => $data['starting_invoice_id'],
            'style' => 'small-field',
        ));
        $fields['invoice_prefix'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'invoice_prefix',
            'value' => $data['invoice_prefix'],
            'style' => 'small-field',
        ));
        $fields['customer_group'] = $form->getFieldHtml(array(
            'type' => 'selectbox',
            'name' => 'config_customer_group_id',
            'value' => $data['config_customer_group_id'],
            'options' => $customer_groups,
        ));
        $fields['customer_price'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_customer_price',
            'value' => $data['config_customer_price'],
            'style' => 'btn_switch',
        ));
        $fields['customer_approval'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_customer_approval',
            'value' => $data['config_customer_approval'],
            'style' => 'btn_switch',
        ));
        $fields['guest_checkout'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_guest_checkout',
            'value' => $data['config_guest_checkout'],
            'style' => 'btn_switch',
        ));
        $fields['account'] = $form->getFieldHtml(array(
            'type' => 'selectbox',
            'name' => 'config_account_id',
            'value' => $data['config_account_id'],
            'options' => $contents,
        ));
        $fields['checkout'] = $form->getFieldHtml(array(
            'type' => 'selectbox',
            'name' => 'config_checkout_id',
            'value' => $data['config_checkout_id'],
            'options' => $contents,
        ));
        $fields['stock_checkout'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_stock_checkout',
            'value' => $data['config_stock_checkout'],
            'style' => 'btn_switch',
        ));
        $fields['order_status'] = $form->getFieldHtml(array(
            'type' => 'selectbox',
            'name' => 'config_order_status_id',
            'value' => $data['config_order_status_id'],
            'options' => $order_statuses,
        ));
        $fields['cart_weight'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_cart_weight',
            'value' => $data['config_cart_weight'],
            'style' => 'btn_switch',
        ));
        $fields['shipping_session'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_shipping_session',
            'value' => $data['config_shipping_session'],
            'style' => 'btn_switch',
        ));
        $fields['cart_ajax'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'cart_ajax',
            'value' => $data['cart_ajax'],
            'style' => 'btn_switch',
        ));

		return $fields;
	}
	
	private function _build_form_appearance( $form, $data ) {
		$fields = array();
		//appearance section 
        $templates = array();
        $directories = glob(DIR_STOREFRONT . 'view/*', GLOB_ONLYDIR);
        foreach ($directories as $directory) {
            $templates[basename($directory)] = basename($directory);
        }
        $extension_templates = $this->extension_manager->getExtensionsList(array('category' => 'template', 'status' => 1));
        if ($extension_templates->total > 0)
            foreach ($extension_templates->rows as $row) {
                $templates[$row['key']] = $row['key'];
            }

        $fields['template'] = $form->getFieldHtml(array(
            'type' => 'selectbox',
            'name' => 'config_storefront_template',
            'value' => $data['config_storefront_template'],
            'options' => $templates,
            'style' => 'large-field',
        ));
 
        $fields['storefront_width'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'storefront_width',
            'value' => $data['storefront_width'],
            'style' => 'small-field',
            'required' => true,
        ));
        $fields['admin_width'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'admin_width',
            'value' => $data['admin_width'],
            'style' => 'small-field',
            'required' => true,
        ));

        $fields['logo'] = $form->getFieldHtml(array(
            'type' => 'hidden',
            'name' => 'config_logo',
            'value' => htmlspecialchars($data['config_logo'], ENT_COMPAT, 'UTF-8'),
        ));
        $fields['icon'] = $form->getFieldHtml(array(
            'type' => 'hidden',
            'name' => 'config_icon',
            'value' => htmlspecialchars($data['config_icon'], ENT_COMPAT, 'UTF-8'),
        ));
        $fields['image_thumb_width'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_image_thumb_width',
            'value' => $data['config_image_thumb_width'],
            'style' => 'small-field',
            'required' => true,
        ));
        $fields['image_thumb_height'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_image_thumb_height',
            'value' => $data['config_image_thumb_height'],
            'style' => 'small-field',
            'required' => true,
        ));
        $fields['image_category_width'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_image_category_width',
            'value' => $data['config_image_category_width'],
            'style' => 'small-field',
            'required' => true,
        ));
        $fields['image_category_height'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_image_category_height',
            'value' => $data['config_image_category_height'],
            'style' => 'small-field',
            'required' => true,
        ));
        $fields['image_product_width'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_image_product_width',
            'value' => $data['config_image_product_width'],
            'style' => 'small-field',
            'required' => true,
        ));
        $fields['image_product_height'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_image_product_height',
            'value' => $data['config_image_product_height'],
            'style' => 'small-field',
            'required' => true,
        ));
        $fields['image_additional_width'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_image_additional_width',
            'value' => $data['config_image_additional_width'],
            'style' => 'small-field',
            'required' => true,
        ));
        $fields['image_additional_height'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_image_additional_height',
            'value' => $data['config_image_additional_height'],
            'style' => 'small-field',
            'required' => true,
        ));
        $fields['image_related_width'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_image_related_width',
            'value' => $data['config_image_related_width'],
            'style' => 'small-field',
            'required' => true,
        ));
        $fields['image_related_height'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_image_related_height',
            'value' => $data['config_image_related_height'],
            'style' => 'small-field',
            'required' => true,
        ));
        $fields['image_cart_width'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_image_cart_width',
            'value' => $data['config_image_cart_width'],
            'style' => 'small-field',
            'required' => true,
        ));
        $fields['image_cart_height'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_image_cart_height',
            'value' => $data['config_image_cart_height'],
            'style' => 'small-field',
            'required' => true,
        ));
        $fields['image_grid_width'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_image_grid_width',
            'value' => $data['config_image_grid_width'],
            'style' => 'small-field',
            'required' => true,
        ));
        $fields['image_grid_height'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_image_grid_height',
            'value' => $data['config_image_grid_height'],
            'style' => 'small-field',
            'required' => true,
        ));
        $fields['cart_ajax'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_cart_ajax',
            'value' => $data['config_cart_ajax'],
            'style' => 'btn_switch',
        ));

		return $fields;
	}
	
	private function _build_form_mail( $form, $data ) {
		$fields = array();
		//mail section
		
        $fields['mail_protocol'] = $form->getFieldHtml(array(
            'type' => 'selectbox',
            'name' => 'config_mail_protocol',
            'value' => $data['config_mail_protocol'],
            'options' => array(
                'mail' => $this->language->get('text_mail'),
                'smtp' => $this->language->get('text_smtp'),
            ),
            'style' => "no-save",
        ));
        $fields['mail_parameter'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_mail_parameter',
            'value' => $data['config_mail_parameter'],
        ));
        $fields['smtp_host'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_smtp_host',
            'value' => $data['config_smtp_host'],
            'style' => "no-save",
            'required' => true
        ));
        $fields['smtp_username'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_smtp_username',
            'value' => $data['config_smtp_username'],
        ));
        $fields['smtp_password'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_smtp_password',
            'value' => $data['config_smtp_password'],
        ));
        $fields['smtp_port'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_smtp_port',
            'value' => $data['config_smtp_port'],
            'style' => "no-save",
            'required' => true
        ));
        $fields['smtp_timeout'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_smtp_timeout',
            'value' => $data['config_smtp_timeout'],
            'style' => "no-save",
            'required' => true
        ));
        $fields['alert_mail'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_alert_mail',
            'value' => $data['config_alert_mail'],
            'style' => 'btn_switch',
        ));
        $fields['alert_emails'] = $form->getFieldHtml(array(
            'type' => 'textarea',
            'name' => 'config_alert_emails',
            'value' => $data['config_alert_emails'],
            'style' => 'large-field',
        ));

		return $fields;
	}
	
	private function _build_form_api( $form, $data ) {
		$fields = array();
		//api section 
        $fields['storefront_api_status'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_storefront_api_status',
            'value' => $data['config_storefront_api_status'],
            'style' => 'btn_switch',
        ));
        $fields['storefront_api_key'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_storefront_api_key',
            'value' => $data['config_storefront_api_key'],
        ));
        $fields['storefront_api_stock_check'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_storefront_api_stock_check',
            'value' => $data['config_storefront_api_stock_check'],
            'style' => 'btn_switch',
        ));
				
		return $fields;
	}
	
	private function _build_form_system( $form, $data ) {
		$fields = array();
		//system section 
        $fields['ssl'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_ssl',
            'value' => $data['config_ssl'],
            'style' => 'btn_switch',
        ));
        $fields['session_ttl'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_session_ttl',
            'value' => $data['config_session_ttl'],
        ));
        $fields['maintenance'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_maintenance',
            'value' => $data['config_maintenance'],
            'style' => 'btn_switch',
        ));
        $fields['encryption'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'encryption_key',
            'value' => $data['encryption_key'],
        ));
        $fields['seo_url'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'enable_seo_url',
            'value' => $data['enable_seo_url'],
            'style' => 'btn_switch',
        ));
        $fields['compression'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_compression',
            'value' => $data['config_compression'],
        ));
        $fields['cache_enable'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_cache_enable',
            'value' => $data['config_cache_enable'],
            'style' => 'btn_switch',
        ));
        $fields['upload_max_size'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_upload_max_size',
            'value' => number_format($data['config_upload_max_size'], 0, '.', $this->language->get('thousand_point'))
        ));

        $fields['error_display'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_error_display',
            'value' => $data['config_error_display'],
            'style' => 'btn_switch',
        ));
        $fields['error_log'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'config_error_log',
            'value' => $data['config_error_log'],
            'style' => 'btn_switch',
        ));
        $fields['debug'] = $form->getFieldHtml(array(
            'type' => 'selectbox',
            'name' => 'config_debug',
            'value' => $data['config_debug'],
            'options' => array(
                0 => $this->language->get('entry_debug_0'),
                1 => $this->language->get('entry_debug_1'),
                2 => $this->language->get('entry_debug_2'),
            ),
        ));
        $fields['debug_level'] = $form->getFieldHtml(array(
            'type' => 'selectbox',
            'name' => 'config_debug_level',
            'value' => $data['config_debug_level'],
            'options' => array(
                0 => $this->language->get('entry_debug_level_0'),
                1 => $this->language->get('entry_debug_level_1'),
                2 => $this->language->get('entry_debug_level_2'),
                3 => $this->language->get('entry_debug_level_3'),
                4 => $this->language->get('entry_debug_level_4'),
                5 => $this->language->get('entry_debug_level_5'),
            ),
        ));
        $fields['template_debug'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'storefront_template_debug',
            'value' => $data['storefront_template_debug'],
            'style' => 'btn_switch',
            'attr' => 'reload_on_save="true"'
        ));
        $fields['error_filename'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'config_error_filename',
            'value' => $data['config_error_filename'],
            'required' => true,
        ));

		return $fields;
	}
	
}

