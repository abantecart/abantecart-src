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
if (!defined('DIR_CORE')) {
	header('Location: static_pages/');
}
/** @noinspection PhpUndefinedClassInspection */
/**
 * @property AExtensionManager $extension_manager
 * @property ModelSettingSetting $model_setting_setting
 * @property ModelLocalisationCountry $model_localisation_country
 * @property ModelLocalisationCurrency $model_localisation_currency
 * @property ModelLocalisationLengthClass $model_localisation_length_class
 * @property ModelLocalisationWeightClass $model_localisation_weight_class
 * @property ModelLocalisationStockStatus $model_localisation_stock_status
 * @property ModelLocalisationOrderStatus $model_localisation_order_status
 * @property ASession $session
 * @property ALanguageManager $language
 * @property ALoader $load
 *
 */
class AConfigManager {
	protected $registry;
	public $errors = 0;
	private $groups = array();
	private $templates = array();

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

	/**
	 *    Build field for provided key setting
	 * @param string $setting_key
	 * @param AForm $form  - form object where filed will be shown
	 * @param array $data  - current setting data
	 * @param int $store_id  - Selected store ID for the setting
	 * @param string $group
	 * @return array
	 */
	public function getFormField($setting_key, $form, $data, $store_id, $group = '') {
		//locate setting group first
		if (empty($group)) {
			$group = $this->model_setting_setting->getSettingGroup($setting_key, $store_id);
			$group = $group[0];
		}
		//set template id to get settings for default template in appearance section
		if($group=='appearance'){
			$data['tmpl_id'] = 'default';
		}
		$data['one_field'] = $setting_key;
		$fields = $this->getFormFields($group, $form, $data);
		return $fields;
	}

	/**
	 *    Build fields array for provided setting group (section)
	 * @param string $group
	 * @param AForm $form - form object where filed will be shown
	 * @param array $data - current setting data
	 * @return array
	 */
	public function getFormFields($group, $form, $data) {
		$method_name = "_build_form_" . $group;
		if (!method_exists($this, $method_name)) {
			return array();
		}
		return $this->$method_name($form, $data);
	}

	/**
	 * @var AForm $form
	 * @param array $data
	 * @return array
	 */
	private function _build_form_details($form, $data) {
		$fields = $props = array();
		// details section
		$fields['name'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'store_name',
			'value' => $data['store_name'],
			'required' => true,
			'style' => 'large-field',
		));
		$fields['url'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'config_url',
			'value' => $data['config_url'],
			'required' => true,
			'style' => 'large-field',
		));
		$fields['ssl'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_ssl',
			'value' => $data['config_ssl'],
			'style' => 'btn_switch',
		));
		$fields['ssl_url'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'config_ssl_url',
			'value' => $data['config_ssl_url'],
			'required' => true,
			'style' => 'large-field',
		));
		$fields['title'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'config_title',
			'value' => $data['config_title'],
			'required' => true,
			'style' => 'large-field',
		));
		$fields['meta_description'] = $form->getFieldHtml($props[] = array(
			'type' => 'textarea',
			'name' => 'config_meta_description',
			'value' => $data['config_meta_description'],
			'style' => 'large-field',
		));
		$fields['meta_keywords'] = $form->getFieldHtml($props[] = array(
			'type' => 'textarea',
			'name' => 'config_meta_keywords',
			'value' => $data['config_meta_keywords'],
			'style' => 'large-field',
		));
		$fields['description'] = $form->getFieldHtml($props[] = array(
			'type' => 'textarea',
			'name' => 'config_description_' . $this->session->data['content_language_id'],
			'value' => $data['config_description_' . $this->session->data['content_language_id']],
			'style' => 'xl-field',
		));
		$fields['owner'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'config_owner',
			'value' => $data['config_owner'],
			'required' => true,
			'style' => 'large-field',
		));
		$fields['address'] = $form->getFieldHtml($props[] = array(
			'type' => 'textarea',
			'name' => 'config_address',
			'value' => $data['config_address'],
			'required' => true,
			'style' => 'large-field',
		));
		$fields['email'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'store_main_email',
			'value' => $data['store_main_email'],
			'required' => true,
			'style' => 'large-field',
		));
		$fields['telephone'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'config_telephone',
			'value' => $data['config_telephone'],
			'style' => 'medium-field',
		));
		$fields['fax'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'config_fax',
			'value' => $data['config_fax'],
			'style' => 'medium-field',
		));


		$results = $this->language->getAvailableLanguages();
		$languages = array();
		foreach ($results as $v) {
			$languages[$v['code']] = $v['name'];
			$lng_code = $this->language->getLanguageCodeByLocale($v['locale']);
			$language_codes[$lng_code] = $v['name'];
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

		$fields['country'] = $form->getFieldHtml($props[] = array(
			'type' => 'zones',
			'name' => 'config_country_id',
			'value' => $data['config_country_id'],
			'zone_field_name' => 'config_zone_id',
			'zone_value' => $data['config_zone_id'],
			'submit_mode' => 'id'
		));


		$fields['language'] = $form->getFieldHtml($props[] = array(
			'type' => 'selectbox',
			'name' => 'config_storefront_language',
			'value' => $data['config_storefront_language'],
			'options' => $languages,
		));

		$fields['admin_language'] = $form->getFieldHtml($props[] = array(
			'type' => 'selectbox',
			'name' => 'admin_language',
			'value' => $data['admin_language'],
			'options' => $languages,
		));

		$fields['auto_translate_status'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'auto_translate_status',
			'value' => $data['auto_translate_status'],
			'style' => 'btn_switch',
		));

		$fields['translate_src_lang_code'] = $form->getFieldHtml($props[] = array(
			'type' => 'selectbox',
			'name' => 'translate_src_lang_code',
			'value' => $data['translate_src_lang_code'],
			'options' => $language_codes,
		));

		$translate_methods = $this->language->getTranslationMethods();
		$fields['translate_method'] = $form->getFieldHtml($props[] = array(
			'type' => 'selectbox',
			'name' => 'translate_method',
			'value' => $data['translate_method'],
			'options' => $translate_methods,
		));

		$fields['translate_override_existing'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'translate_override_existing',
			'value' => $data['translate_override_existing'],
			'style' => 'btn_switch',
		));
		$fields['warn_lang_text_missing'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'warn_lang_text_missing',
			'value' => $data['warn_lang_text_missing'],
			'style' => 'btn_switch',
		));

		$fields['currency'] = $form->getFieldHtml($props[] = array(
			'type' => 'selectbox',
			'name' => 'config_currency',
			'value' => $data['config_currency'],
			'options' => $currencies,
		));
		$fields['currency_auto'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_currency_auto',
			'value' => $data['config_currency_auto'],
			'style' => 'btn_switch',
		));
		$fields['length_class'] = $form->getFieldHtml($props[] = array(
			'type' => 'selectbox',
			'name' => 'config_length_class',
			'value' => $data['config_length_class'],
			'options' => $length_classes,
		));

		$fields['weight_class'] = $form->getFieldHtml($props[] = array(
			'type' => 'selectbox',
			'name' => 'config_weight_class',
			'value' => $data['config_weight_class'],
			'options' => $weight_classes,
		));

		if (isset($data['one_field'])) {
			$fields = $this->_filterField($fields, $props, $data['one_field']);
		}
		return $fields;
	}

	/**
	 * @var AForm $form
	 * @param array $data
	 * @return array
	 */
	private function _build_form_general($form, $data) {
		$fields = array();
		//general section
		$this->load->model('localisation/stock_status');
		$stock_statuses = array();
		$results = $this->model_localisation_stock_status->getStockStatuses();
		foreach ($results as $item) {
			$stock_statuses[$item['stock_status_id']] = $item['name'];
		}

		$fields['catalog_limit'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'config_catalog_limit',
			'value' => $data['config_catalog_limit'],
			'required' => true,
			'style' => 'small-field',
		));
		$fields['admin_limit'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'config_admin_limit',
			'value' => $data['config_admin_limit'],
			'required' => true,
			'style' => 'small-field',
		));
		$fields['bestseller_limit'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'config_bestseller_limit',
			'value' => $data['config_bestseller_limit'],
			'required' => true,
			'style' => 'small-field',
		));
		$fields['featured_limit'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'config_featured_limit',
			'value' => $data['config_featured_limit'],
			'required' => true,
			'style' => 'small-field',
		));
		$fields['latest_limit'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'config_latest_limit',
			'value' => $data['config_latest_limit'],
			'required' => true,
			'style' => 'small-field',
		));
		$fields['special_limit'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'config_special_limit',
			'value' => $data['config_special_limit'],
			'required' => true,
			'style' => 'small-field',
		));

		$fields['product_default_sort_order'] = $form->getFieldHtml($props[] = array(
			'type' => 'selectbox',
			'name' => 'config_product_default_sort_order',
			'value' => $data['config_product_default_sort_order'],
			'options' => array(
				'sort_order-ASC' => $this->language->get('text_sorting_sort_order_asc'),
				'name-ASC' => $this->language->get('text_sorting_name_asc'),
				'name-DESC' => $this->language->get('text_sorting_name_desc'),
				'price-ASC' => $this->language->get('text_sorting_price_asc'),
				'price-DESC' => $this->language->get('text_sorting_price_desc'),
				'rating-DESC' => $this->language->get('text_sorting_rating_desc'),
				'rating-ASC' => $this->language->get('text_sorting_rating_asc'),
				'date_modified-DESC' => $this->language->get('text_sorting_date_desc'),
				'date_modified-ASC' => $this->language->get('text_sorting_date_asc'),
			)));

		$fields['stock_display'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_stock_display',
			'value' => $data['config_stock_display'],
			'style' => 'btn_switch',
		));
		$fields['nostock_autodisable'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_nostock_autodisable',
			'value' => $data['config_nostock_autodisable'],
			'style' => 'btn_switch',
		));
		$fields['stock_status'] = $form->getFieldHtml($props[] = array(
			'type' => 'selectbox',
			'name' => 'config_stock_status_id',
			'value' => $data['config_stock_status_id'],
			'options' => $stock_statuses,
		));
		$fields['reviews'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'enable_reviews',
			'value' => $data['enable_reviews'],
			'style' => 'btn_switch',
		));
		$fields['download'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_download',
			'value' => $data['config_download'],
			'style' => 'btn_switch',
		));

		$fields['help_links'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_help_links',
			'value' => $data['config_help_links'],
			'style' => 'btn_switch',
		));
		$fields['show_tree_data'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_show_tree_data',
			'value' => $data['config_show_tree_data'],
			'style' => 'btn_switch',
		));
		$fields['embed_status'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_embed_status',
			'value' => $data['config_embed_status'],
			'style' => 'btn_switch',
		));

		$fields['embed_click_action'] = $form->getFieldHtml($props[] = array(
			'type' => 'selectbox',
			'name' => 'config_embed_click_action',
			'value' => $data['config_embed_click_action'],
			'options' => array(
					'modal' => $this->language->get('text_embed_click_action_modal'),
					'new_window' => $this->language->get('text_embed_click_action_new_window'),
					'same_window' => $this->language->get('text_embed_click_action_same_window')
			),
		));
		$fields['account_create_captcha'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_account_create_captcha',
			'value' => $data['config_account_create_captcha'],
			'style' => 'btn_switch',
		));

		$fields['recaptcha_site_key'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'config_recaptcha_site_key',
			'value' => $data['config_recaptcha_site_key'],
			'style' => 'medium-field',
		));
		$fields['recaptcha_secret_key'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'config_recaptcha_secret_key',
			'value' => $data['config_recaptcha_secret_key'],
			'style' => 'medium-field',
		));

		$fields['google_analytics'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'config_google_analytics_code',
			'value' => $data['config_google_analytics_code'],
			'style' => 'medium-field',
		));

		if (isset($data['one_field'])) {
			$fields = $this->_filterField($fields, $props, $data['one_field']);
		}
		return $fields;
	}

	/**
	 * @param AForm $form
	 * @param array $data
	 * @return array
	 *
	 */
	private function _build_form_checkout($form, $data) {
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

		$fields['tax'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_tax',
			'value' => $data['config_tax'],
			'style' => 'btn_switch',
		));
		$fields['tax_store'] = $form->getFieldHtml($props[] = array(
			'type' => 'selectbox',
			'name' => 'config_tax_store',
			'value' => $data['config_tax_store'],
			'options' => array($this->language->get('entry_tax_store_0'), $this->language->get('entry_tax_store_1')),
		));
		$fields['tax_customer'] = $form->getFieldHtml($props[] = array(
			'type' => 'selectbox',
			'name' => 'config_tax_customer',
			'value' => $data['config_tax_customer'],
			'options' => array($this->language->get('entry_tax_customer_0'), $this->language->get('entry_tax_customer_1')),
		));
		$fields['invoice'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'starting_invoice_id',
			'value' => $data['starting_invoice_id'],
			'style' => 'small-field',
		));
		$fields['invoice_prefix'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'invoice_prefix',
			'value' => $data['invoice_prefix'],
			'style' => 'small-field',
		));
		$fields['customer_group'] = $form->getFieldHtml($props[] = array(
			'type' => 'selectbox',
			'name' => 'config_customer_group_id',
			'value' => $data['config_customer_group_id'],
			'options' => $customer_groups,
		));
		$fields['customer_price'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_customer_price',
			'value' => $data['config_customer_price'],
			'style' => 'btn_switch',
		));
		$fields['customer_approval'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_customer_approval',
			'value' => $data['config_customer_approval'],
			'style' => 'btn_switch',
		));
		$fields['customer_email_activation'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_customer_email_activation',
			'value' => $data['config_customer_email_activation'],
			'style' => 'btn_switch',
		));
		$fields['prevent_email_as_login'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'prevent_email_as_login',
			'value' => $data['prevent_email_as_login'],
			'style' => 'btn_switch',
		));
		$fields['guest_checkout'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_guest_checkout',
			'value' => $data['config_guest_checkout'],
			'style' => 'btn_switch',
		));
		$fields['account'] = $form->getFieldHtml($props[] = array(
			'type' => 'selectbox',
			'name' => 'config_account_id',
			'value' => $data['config_account_id'],
			'options' => $contents,
		));
		$fields['checkout'] = $form->getFieldHtml($props[] = array(
			'type' => 'selectbox',
			'name' => 'config_checkout_id',
			'value' => $data['config_checkout_id'],
			'options' => $contents,
		));
		$fields['stock_checkout'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_stock_checkout',
			'value' => $data['config_stock_checkout'],
			'style' => 'btn_switch',
		));
		$fields['total_order_maximum'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'total_order_maximum',
			'value' => $data['total_order_maximum'],
			'style' => 'small-field'
		));
		$fields['total_order_minimum'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'total_order_minimum',
			'value' => $data['total_order_minimum'],
			'style' => 'small-field'
		));
		$fields['order_status'] = $form->getFieldHtml($props[] = array(
			'type' => 'selectbox',
			'name' => 'config_order_status_id',
			'value' => $data['config_order_status_id'],
			'options' => $order_statuses,
		));
		$fields['customer_cancelation_order_status'] = $form->getFieldHtml($props[] = array(
			'type' => 'multiselectbox',
			'name' => 'config_customer_cancelation_order_status_id[]',
			'value' => $data['config_customer_cancelation_order_status_id'] ? $data['config_customer_cancelation_order_status_id'] : array(),
			'options' => $order_statuses,
			'style' => 'chosen'
		));
		$fields['cart_weight'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_cart_weight',
			'value' => $data['config_cart_weight'],
			'style' => 'btn_switch',
		));
		$fields['shipping_session'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_shipping_session',
			'value' => $data['config_shipping_session'],
			'style' => 'btn_switch',
		));
		$fields['cart_ajax'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_cart_ajax',
			'value' => $data['config_cart_ajax'],
			'style' => 'btn_switch',
		));
		$fields['zero_customer_balance'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_zero_customer_balance',
			'value' => $data['config_zero_customer_balance'],
			'style' => 'btn_switch',
		));
		$fields['shipping_tax_estimate'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_shipping_tax_estimate',
			'value' => $data['config_shipping_tax_estimate'],
			'style' => 'btn_switch',
		));
		$fields['coupon_on_cart_page'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_coupon_on_cart_page',
			'value' => $data['config_coupon_on_cart_page'],
			'style' => 'btn_switch',
		));

		if (isset($data['one_field'])) {
			$fields = $this->_filterField($fields, $props, $data['one_field']);
		}
		return $fields;
	}

	/**
	 * @var AForm $form
	 * @param array $data
	 * @return array
	 */
	private function _build_form_appearance($form, $data) {
		$fields = array();

		//this method ca build filds for general apearance or template specific
		//for template settings, need to specify 'tmpl_id' as template_id for settings section
		if( empty($data['tmpl_id']) ){
			//general appearance section
			$templates = $this->getTemplates('storefront');

			$fields['template'] = $form->getFieldHtml($props[] = array(
				'type' => 'selectbox',
				'name' => 'config_storefront_template',
				'value' => $data['config_storefront_template'],
				'options' => $templates,
				'style' => 'large-field',
			));

			$templates = $this->getTemplates('admin');

			$fields['admin_template'] = $form->getFieldHtml($props[] = array(
				'type' => 'selectbox',
				'name' => 'admin_template',
				'value' => $data['admin_template'],
				'options' => $templates,
				'style' => 'large-field',
			));
			$fields['admin_width'] = $form->getFieldHtml($props[] = array(
				'type' => 'input',
				'name' => 'admin_width',
				'value' => $data['admin_width'],
				'style' => 'small-field',
				'required' => true,
			));
			$fields['image_grid_width'] = $form->getFieldHtml($props[] = array(
				'type' => 'input',
				'name' => 'config_image_grid_width',
				'value' => $data['config_image_grid_width'],
				'style' => 'small-field',
				'required' => true,
			));
			$fields['image_grid_height'] = $form->getFieldHtml($props[] = array(
				'type' => 'input',
				'name' => 'config_image_grid_height',
				'value' => $data['config_image_grid_height'],
				'style' => 'small-field',
				'required' => true,
			));
			$fields['image_product_width'] = $form->getFieldHtml($props[] = array(
				'type' => 'input',
				'name' => 'config_image_product_width',
				'value' => $data['config_image_product_width'],
				'style' => 'small-field',
				'required' => true,
			));
			$fields['image_product_height'] = $form->getFieldHtml($props[] = array(
				'type' => 'input',
				'name' => 'config_image_product_height',
				'value' => $data['config_image_product_height'],
				'style' => 'small-field',
				'required' => true,
			));

		}else{ 
			// settings per template
			$default_values = $this->model_setting_setting->getSetting('appearance', (int)$data['store_id']);
			$fieldset = array(
					'storefront_width' ,
					'config_logo' ,
					'config_icon' ,
					'config_image_thumb_width' ,
					'config_image_thumb_height' ,
					'config_image_popup_width' ,
					'config_image_popup_height' ,
					'config_image_category_width' ,
					'config_image_category_height' ,
					'config_image_product_width' ,
					'config_image_product_height' ,
					'config_image_additional_width' ,
					'config_image_additional_height' ,
					'config_image_related_width' ,
					'config_image_related_height' ,
					'config_image_cart_width' ,
					'config_image_cart_height' ,
					'config_image_grid_width' ,
					'config_image_grid_height');

			foreach($fieldset as $name){
				if(!has_value($data[$name]) && has_value($default_values[$name])){
					$data[$name] = $default_values[$name];
				}
			}

			$fields['storefront_width'] = $form->getFieldHtml($props[] = array(
				'type' => 'input',
				'name' => 'storefront_width',
				'value' => $data['storefront_width'],
				'style' => 'small-field',
				'required' => true,
			));
			//see if we have resource id or path 
			if ( is_numeric($data['config_logo']) ) {
				$fields['logo'] = $form->getFieldHtml($props[] = array(
					'type' => 'resource',
					'name' => 'config_logo',
					'resource_id' => $data['config_logo'],
					'rl_type' => 'image'
				));			
			} else {
				$fields['logo'] = $form->getFieldHtml($props[] = array(
					'type' => 'resource',
					'name' => 'config_logo',
					'resource_path' => htmlspecialchars($data['config_logo'], ENT_COMPAT, 'UTF-8'),
					'rl_type' => 'image'
				));
			}
			//see if we have resource id or path 
			if ( is_numeric($data['config_icon']) ) {
				$fields['icon'] = $form->getFieldHtml($props[] = array(
					'type' => 'resource',
					'name' => 'config_icon',
					'resource_id' => $data['config_icon'],
					'rl_type' => 'image'
				));			
			} else {
				$fields['icon'] = $form->getFieldHtml($props[] = array(
					'type' => 'resource',
					'name' => 'config_icon',
					'resource_path' => htmlspecialchars($data['config_icon'], ENT_COMPAT, 'UTF-8'),
					'rl_type' => 'image'
				));			
			}

			$fields['image_thumb_width'] = $form->getFieldHtml($props[] = array(
				'type' => 'input',
				'name' => 'config_image_thumb_width',
				'value' => $data['config_image_thumb_width'],
				'style' => 'small-field',
				'required' => true,
			));
			$fields['image_thumb_height'] = $form->getFieldHtml($props[] = array(
				'type' => 'input',
				'name' => 'config_image_thumb_height',
				'value' => $data['config_image_thumb_height'],
				'style' => 'small-field',
				'required' => true,
			));
			$fields['image_popup_width'] = $form->getFieldHtml($props[] = array(
				'type' => 'input',
				'name' => 'config_image_popup_width',
				'value' => $data['config_image_popup_width'],
				'style' => 'small-field',
				'required' => true,
			));
			$fields['image_popup_height'] = $form->getFieldHtml($props[] = array(
				'type' => 'input',
				'name' => 'config_image_popup_height',
				'value' => $data['config_image_popup_height'],
				'style' => 'small-field',
				'required' => true,
			));
			$fields['image_category_width'] = $form->getFieldHtml($props[] = array(
				'type' => 'input',
				'name' => 'config_image_category_width',
				'value' => $data['config_image_category_width'],
				'style' => 'small-field',
				'required' => true,
			));
			$fields['image_category_height'] = $form->getFieldHtml($props[] = array(
				'type' => 'input',
				'name' => 'config_image_category_height',
				'value' => $data['config_image_category_height'],
				'style' => 'small-field',
				'required' => true,
			));
			$fields['image_product_width'] = $form->getFieldHtml($props[] = array(
				'type' => 'input',
				'name' => 'config_image_product_width',
				'value' => $data['config_image_product_width'],
				'style' => 'small-field',
				'required' => true,
			));
			$fields['image_product_height'] = $form->getFieldHtml($props[] = array(
				'type' => 'input',
				'name' => 'config_image_product_height',
				'value' => $data['config_image_product_height'],
				'style' => 'small-field',
				'required' => true,
			));
			$fields['image_additional_width'] = $form->getFieldHtml($props[] = array(
				'type' => 'input',
				'name' => 'config_image_additional_width',
				'value' => $data['config_image_additional_width'],
				'style' => 'small-field',
				'required' => true,
			));
			$fields['image_additional_height'] = $form->getFieldHtml($props[] = array(
				'type' => 'input',
				'name' => 'config_image_additional_height',
				'value' => $data['config_image_additional_height'],
				'style' => 'small-field',
				'required' => true,
			));
			$fields['image_related_width'] = $form->getFieldHtml($props[] = array(
				'type' => 'input',
				'name' => 'config_image_related_width',
				'value' => $data['config_image_related_width'],
				'style' => 'small-field',
				'required' => true,
			));
			$fields['image_related_height'] = $form->getFieldHtml($props[] = array(
				'type' => 'input',
				'name' => 'config_image_related_height',
				'value' => $data['config_image_related_height'],
				'style' => 'small-field',
				'required' => true,
			));
			$fields['image_cart_width'] = $form->getFieldHtml($props[] = array(
				'type' => 'input',
				'name' => 'config_image_cart_width',
				'value' => $data['config_image_cart_width'],
				'style' => 'small-field',
				'required' => true,
			));
			$fields['image_cart_height'] = $form->getFieldHtml($props[] = array(
				'type' => 'input',
				'name' => 'config_image_cart_height',
				'value' => $data['config_image_cart_height'],
				'style' => 'small-field',
				'required' => true,
			));
			$fields['image_grid_width'] = $form->getFieldHtml($props[] = array(
				'type' => 'input',
				'name' => 'config_image_grid_width',
				'value' => $data['config_image_grid_width'],
				'style' => 'small-field',
				'required' => true,
			));
			$fields['image_grid_height'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'config_image_grid_height',
			'value' => $data['config_image_grid_height'],
			'style' => 'small-field',
			'required' => true,
			));
		}

		if (isset($data['one_field'])) {
			$fields = $this->_filterField($fields, $props, $data['one_field']);
		}
		return $fields;
	}

	/**To be removed in v 1.3 or next major release
	 * @deprecated since 1.2.4
	 * @param $section
	 * @return array
	 */
	public function getTemplatesLIst($section) {
		return $this->getTemplates($section);
	}

	/**
	 * @param 	string $section - can be storefront or admin
	 * @param 	int $status - template extension status
	 * @return array
	 */
	public function getTemplates($section, $status = 1){

		if(has_value($this->templates[$section])){
			return $this->templates[$section];
		}

		$basedir = $section=='admin' ? DIR_APP_SECTION : DIR_STOREFRONT;

		$directories = glob($basedir . 'view/*', GLOB_ONLYDIR);
		//get core templates
		foreach ($directories as $directory) {
			$this->templates[$section][basename($directory)] = basename($directory);
		}
		if($section!='admin'){
			//get extension templates
			$extension_templates = $this->extension_manager->getExtensionsList(array('filter' => 'template', 'status' => (int)$status));
			if($extension_templates->total > 0){
				foreach($extension_templates->rows as $row){
					$this->templates[$section][$row['key']] = $row['key'];
				}
			}
		}

		return $this->templates[$section];
	}

	/**
	 * @var AForm $form
	 * @param array $data
	 * @return array
	 */
	private function _build_form_mail($form, $data) {
		$fields = array();
		//mail section

		$fields['mail_protocol'] = $form->getFieldHtml($props[] = array(
			'type' => 'selectbox',
			'name' => 'config_mail_protocol',
			'value' => $data['config_mail_protocol'],
			'options' => array(
				'mail' => $this->language->get('text_mail'),
				'smtp' => $this->language->get('text_smtp'),
			),
			'style' => "no-save",
		));
		$fields['mail_parameter'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'config_mail_parameter',
			'value' => $data['config_mail_parameter'],
		));
		$fields['smtp_host'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'config_smtp_host',
			'value' => $data['config_smtp_host'],
			'required' => true
		));
		$fields['smtp_username'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'config_smtp_username',
			'value' => $data['config_smtp_username'],
		));
		$fields['smtp_password'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'config_smtp_password',
			'value' => $data['config_smtp_password'],
		));
		$fields['smtp_port'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'config_smtp_port',
			'value' => $data['config_smtp_port'],
			'required' => true
		));
		$fields['smtp_timeout'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'config_smtp_timeout',
			'value' => $data['config_smtp_timeout'],
			'required' => true
		));
		$fields['alert_mail'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_alert_mail',
			'value' => $data['config_alert_mail'],
			'style' => 'btn_switch',
		));
		$fields['alert_emails'] = $form->getFieldHtml($props[] = array(
			'type' => 'textarea',
			'name' => 'config_alert_emails',
			'value' => $data['config_alert_emails'],
			'style' => 'large-field',
		));
		if (isset($data['one_field'])) {
			$fields = $this->_filterField($fields, $props, $data['one_field']);
		}
		return $fields;
	}

	/**
	 * @var AForm $form
	 * @param array $data
	 * @return array
	 */
	private function _build_form_api($form, $data) {
		$fields = array();
		//api section 
		$fields['storefront_api_status'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_storefront_api_status',
			'value' => $data['config_storefront_api_status'],
			'style' => 'btn_switch',
		));
		$fields['storefront_api_key'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'config_storefront_api_key',
			'value' => $data['config_storefront_api_key'],
		));
		$fields['storefront_api_stock_check'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_storefront_api_stock_check',
			'value' => $data['config_storefront_api_stock_check'],
			'style' => 'btn_switch',
		));

		$fields['admin_api_status'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_admin_api_status',
			'value' => $data['config_admin_api_status'],
			'style' => 'btn_switch',
		));
		$fields['admin_api_key'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'config_admin_api_key',
			'value' => $data['config_admin_api_key'],
		));
		$fields['admin_access_ip_list'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'config_admin_access_ip_list',
			'value' => $data['config_admin_access_ip_list'],
			'style' => 'large-field',
		));

		if (isset($data['one_field'])) {
			$fields = $this->_filterField($fields, $props, $data['one_field']);
		}
		return $fields;
	}

	/**
	 * @var AForm $form
	 * @param array $data
	 * @return array
	 */
	private function _build_form_system($form, $data) {
		$fields = array();
		//system section
		$fields['session_ttl'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'config_session_ttl',
			'value' => $data['config_session_ttl'],
		));
		$fields['maintenance'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_maintenance',
			'value' => $data['config_maintenance'],
			'style' => 'btn_switch',
		));
		$fields['voicecontrol'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_voicecontrol',
			'value' => $data['config_voicecontrol'],
			'style' => 'btn_switch',
		));
		//backwards compatability. Can remove in the future. 
		if (!defined('ENCRYPTION_KEY')) {
			$fields['encryption'] = $form->getFieldHtml($props[] = array(
				'type' => 'input',
				'name' => 'encryption_key',
				'value' => $data['encryption_key'],
				'attr' => 'readonly',
			));		
		}
		$fields['seo_url'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'enable_seo_url',
			'value' => $data['enable_seo_url'],
			'style' => 'btn_switch',
		));
		$fields['retina_enable'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_retina_enable',
			'value' => $data['config_retina_enable'],
			'style' => 'btn_switch',
		));
		$fields['compression'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'config_compression',
			'value' => $data['config_compression'],
		));
		$fields['cache_enable'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_cache_enable',
			'value' => $data['config_cache_enable'],
			'style' => 'btn_switch',
		));
		$fields['upload_max_size'] = $form->getFieldHtml($props[] = array(
					'type' => 'input',
					'name' => 'config_upload_max_size',
					'value' => (int)$data['config_upload_max_size']
				)) . 'This value can not exceed your php.ini setting (<= ' . ini_get('post_max_size') . ')';

		$fields['error_display'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_error_display',
			'value' => $data['config_error_display'],
			'style' => 'btn_switch',
		));
		$fields['error_log'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'config_error_log',
			'value' => $data['config_error_log'],
			'style' => 'btn_switch',
		));
		$fields['debug'] = $form->getFieldHtml($props[] = array(
			'type' => 'selectbox',
			'name' => 'config_debug',
			'value' => $data['config_debug'],
			'options' => array(
				0 => $this->language->get('entry_debug_0'),
				1 => $this->language->get('entry_debug_1'),
				2 => $this->language->get('entry_debug_2'),
			),
		));
		$fields['debug_level'] = $form->getFieldHtml($props[] = array(
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
		$fields['template_debug'] = $form->getFieldHtml($props[] = array(
			'type' => 'checkbox',
			'name' => 'storefront_template_debug',
			'value' => $data['storefront_template_debug'],
			'style' => 'btn_switch',
			'attr' => 'reload_on_save="true"'
		));
		$fields['error_filename'] = $form->getFieldHtml($props[] = array(
			'type' => 'input',
			'name' => 'config_error_filename',
			'value' => $data['config_error_filename'],
			'required' => true,
		));
		$fields['system_check'] = $form->getFieldHtml($props[] = array(
			'type' => 'selectbox',
			'name' => 'config_system_check',
			'value' => $data['config_system_check'],
			'options' => array(
				0 => 'Admin & Storefront',
				1 => 'Admin',
				2 => 'Storefront',
				3 => $this->language->get('text_disabled'),
			),
		));
		if (isset($data['one_field'])) {
			$fields = $this->_filterField($fields, $props, $data['one_field']);
		}
		return $fields;
	}

	private function _filterField($fields, $props, $field_name) {
		$output = array();
		foreach ($props as $n => $properties) {
			if ($field_name == $properties['name']
					|| (is_int(strpos($field_name, 'config_description')) && is_int(strpos($properties['name'], 'config_description')))
			) {
				$names = array_keys($fields);
				$name = $names[$n];
				$output = array($name => $fields[$name]);
				break;
			}
		}
		return $output;
	}


	// validate form fields
	public function validate($group, $fields = array()) {
		if (empty($group) || !is_array($fields)) {
			return false;
		}
		$this->load->language('setting/setting');

		foreach ($fields as $field_name => $field_value) {
			switch ($group) {
				case 'details':
					if ($field_name == 'store_name' && !$field_value) {
						$error['name'] = $this->language->get('error_name');
					}
					if ($field_name == 'config_title' && !$field_value) {
						$error['title'] = $this->language->get('error_title');
					}
					if ($field_name == 'config_url' && !$field_value) {
						$error['url'] = $this->language->get('error_url');
					}
					if ($field_name == 'config_ssl_url' && !$field_value && $this->request->get['config_ssl']) {
						$error['ssl_url'] = $this->language->get('error_ssl_url');
					}
					if (sizeof($fields) > 1) {
						if ( mb_strlen($fields['config_owner']) < 2 ||  mb_strlen( $fields['config_owner'] ) > 64 ) {
							$error['owner'] = $this->language->get('error_owner');
						}

						if (  mb_strlen($fields['config_address']) < 2 ||  mb_strlen( $fields['config_address'] ) > 256 ) {
							$error['address'] = $this->language->get('error_address');
						}

						if ( mb_strlen($fields['store_main_email']) > 96 || (!preg_match(EMAIL_REGEX_PATTERN, $fields['store_main_email']))) {
							$error['email'] = $this->language->get('error_email');
						}

						if ( mb_strlen( $fields['config_telephone'] ) > 32 ) {
							$error['telephone'] = $this->language->get('error_telephone');
						}
					}
					break;

				case 'general':

					if ($field_name == 'config_catalog_limit' && !$field_value) {
						$error['catalog_limit'] = $this->language->get('error_limit');
					}

					if ($field_name == 'config_bestseller_limit' && !$field_value) {
						$error['bestseller_limit'] = $this->language->get('error_limit');
					}

					if ($field_name == 'config_featured_limit' && !$field_value) {
						$error['featured_limit'] = $this->language->get('error_limit');
					}

					if ($field_name == 'config_latest_limit' && !$field_value) {
						$error['latest_limit'] = $this->language->get('error_limit');
					}

					if ($field_name == 'config_special_limit' && !$field_value) {
						$error['special_limit'] = $this->language->get('error_limit');
					}
					break;

				case 'appearance':
					if (($field_name == 'config_image_thumb_width' && !$field_value) || ($field_name == 'config_image_thumb_height' && !$field_value)) {
						$error['image_thumb_width'] = $error['image_thumb_height'] = $this->language->get('error_image_thumb');
					}

					if (($field_name == 'config_image_popup_width' && !$field_value) || ($field_name == 'config_image_popup_height' && !$field_value)) {
						$error['image_popup_height'] = $error['image_popup_width'] = $this->language->get('error_image_popup');
					}

					if (($field_name == 'config_image_category_width' && !$field_value) || ($field_name == 'config_image_category_height' && !$field_value)) {
						$error['image_category_height'] = $this->language->get('error_image_category');
					}

					if (($field_name == 'config_image_product_width' && !$field_value) || ($field_name == 'config_image_product_height' && !$field_value)) {
						$error['image_product_height'] = $this->language->get('error_image_product');
					}

					if (($field_name == 'config_image_additional_width' && !$field_value) || ($field_name == 'config_image_additional_height' && !$field_value)) {
						$error['image_additional_height'] = $this->language->get('error_image_additional');
					}

					if (($field_name == 'config_image_related_width' && !$field_value) || ($field_name == 'config_image_related_height' && !$field_value)) {
						$error['image_related_height'] = $this->language->get('error_image_related');
					}

					if (($field_name == 'config_image_cart_width' && !$field_value) || ($field_name == 'config_image_cart_height' && !$field_value)) {
						$error['image_cart_height'] = $this->language->get('error_image_cart');
					}

					if (($field_name == 'config_image_grid_width' && !$field_value) || ($field_name == 'config_image_grid_height' && !$field_value)) {
						$error['image_grid_height'] = $this->language->get('error_image_grid');
					}
					break;

				case 'checkout':
					break;

				case 'api':
					break;

				case 'mail':

					if (($fields['config_mail_protocol'] == 'smtp')
							&& (($field_name == 'config_smtp_host' && !$field_value) || ($field_name == 'config_smtp_port' && !$field_value) || ($field_name == 'config_smtp_timeout' && !$field_value))
					) {
						$error['mail'] = $this->language->get('error_mail');
					}

					break;

				case 'system':
					if ($field_name == 'config_error_filename' && !$field_value) {
						$error['error_filename'] = $this->language->get('error_error_filename');
					}
					if ($field_name == 'config_upload_max_size') {
						$fields[$field_value] = preformatInteger($field_value);
					}

					break;
				default:
			}


		}
		return array('error' => $error, 'validated' => $fields);
	}

}

