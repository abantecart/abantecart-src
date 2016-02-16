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
if (!defined('DIR_CORE') || !IS_ADMIN) {
	header('Location: static_pages/');
}

if (defined('IS_DEMO') && IS_DEMO) {
	header('Location: static_pages/demo_mode.php');
}

class ControllerPagesSaleContact extends AController {
	public $data = array();
	public $error = array();

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if(!has_value($this->data['protocol'])){
			$this->data['protocol'] = 'email';
		}

		$this->document->setTitle($this->language->get('text_send_'.$this->data['protocol']));
		$this->loadModel('sale/customer');

		$this->data['token'] = $this->session->data['token'];

		if (isset($this->error)) {
			$this->data['error_warning'] = '';
			foreach ($this->error as $message) {
				$this->data['error_warning'] .= $message . '<br/>';
			}
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['subject'])) {
			$this->data['error_subject'] = $this->error['subject'];
		} else {
			$this->data['error_subject'] = '';
		}

		if (isset($this->error['message'])) {
			$this->data['error_message'] = $this->error['message'];
		} else {
			$this->data['error_message'] = '';
		}

		if (isset($this->error['recipient'])) {
			$this->data['error_recipient'] = $this->error['recipient'];
		} else {
			$this->data['error_recipient'] = '';
		}

		$this->document->initBreadcrumb(array(
				'href' => $this->html->getSecureURL('index/home'),
				'text' => $this->language->get('text_home'),
				'separator' => FALSE
		));
		$this->document->addBreadcrumb(array(
				'href' => $this->html->getSecureURL('sale/contact'),
				'text' => $this->language->get('text_send_'.$this->data['protocol']),
				'separator' => ' :: ',
				'current' => true
		));

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->data['action'] = $this->html->getSecureURL('sale/contact');
		$this->data['cancel'] = $this->html->getSecureURL('sale/contact');

		//get store from main switcher and current config
		$this->data['store_id'] = $this->config->get('config_store_id');

		$this->data['customers'] = array();
		$this->data['products'] = array();
		$this->loadModel('catalog/product');
		$customer_ids = $this->request->get_or_post('to');
		if(!$customer_ids && has_value($this->session->data['sale_contact_presave']['to'])){
			$customer_ids = $this->session->data['sale_contact_presave']['to'];
		}
		$product_ids = $this->request->get_or_post('products');
		if(!$product_ids && has_value($this->session->data['sale_contact_presave']['products'])){
			$product_ids = $this->session->data['sale_contact_presave']['products'];
		}
		
		//process list of customer or product IDs to be notified
		if (isset($customer_ids) && is_array($customer_ids)) {
			foreach ($customer_ids as $customer_id) {
				$customer_info = $this->model_sale_customer->getCustomer($customer_id);

				if ($customer_info) {
					$this->data['customers'][$customer_info['customer_id']] = $customer_info['firstname'] . ' ' . $customer_info['lastname'] . ' (' . $customer_info['email'] . ')';
				}
			}
		} 
		if (isset($product_ids) && is_array($product_ids)) {
			foreach ($product_ids as $product_id) {
				$product_info = $this->model_catalog_product->getProduct($product_id);
				if ($product_info) {
				$resource = new AResource('image');
				$thumbnail = $resource->getMainThumb('products',
						$product_id,
						(int)$this->config->get('config_image_grid_width'),
						(int)$this->config->get('config_image_grid_height'),
						true
				);
				$this->data['products'][$product_id] = array(
						'name' => $product_info['name'],
						'image' =>	$thumbnail['thumb_html']
															);
				}
			}
		}

		foreach(array('recipient','subject','message') as $n){
			$this->data[$n] = $this->request->post_or_get($n);
			if (!$this->data[$n] && has_value($this->session->data['sale_contact_presave'][$n])){
				$this->data[$n] = $this->session->data['sale_contact_presave'][$n];
			}
		}


		$this->loadModel('catalog/category');
		$categories = $this->model_catalog_category->getCategories(0);
		$this->data['categories'][0] = $this->language->get('text_select_category');
		foreach ($categories as $category) {
			$this->data['categories'][$category['category_id']] = $category['name'];
		}

		$form = new AForm('ST');
		$form->setForm(array(
				'form_name' => 'sendFrm',
				'update' => $this->data['update']
		));

		$this->data['form']['form_open'] = $form->getFieldHtml(
				array('type' => 'form',
						'name' => 'sendFrm',
						'action' => '',
						'attr' => 'data-confirm-exit="true" class="form-horizontal"',
				));


		$this->data['form']['submit'] = $form->getFieldHtml(array(
				'type' => 'button',
				'name' => 'submit',
				'text' => $this->language->get('button_send'),
				'style' => 'button1',
		));
		$this->data['form']['cancel'] = $form->getFieldHtml(array(
				'type' => 'button',
				'name' => 'cancel',
				'text' => $this->language->get('button_cancel'),
				'style' => 'button2',
		));


		$this->data['form']['fields']['protocol'] = $form->getFieldHtml(array(
				'type' => 'hidden',
				'name' => 'protocol',
				'value' => $this->data['protocol']
		));

		$this->data['form']['build_task_url'] = $this->html->getSecureURL('r/sale/contact/buildTask');
		$this->data['form']['complete_task_url'] = $this->html->getSecureURL('r/sale/contact/complete');
		$this->data['form']['abort_task_url'] = $this->html->getSecureURL('r/sale/contact/abort');

		$this->loadModel('setting/store');
		$this->loadModel('setting/setting');
		$stores = array(0 => $this->language->get('text_default'));
		$allstores = $this->model_setting_store->getStores();
		if ($allstores) {
			foreach ($allstores as $item) {
				//get store email address to display
				$settings = $this->model_setting_setting->getSetting('details', $item['store_id']);
				$stores[$item['store_id']] = $settings['store_name'];
				if($this->data['protocol']=='email'){
					$stores[$item['store_id']] .= ' ( ' . $settings['store_main_email'].' )';
				}
			}
		}

		//do not allow to edit store. This is changed with main store switcher
		$this->data['form']['fields']['store'] = $form->getFieldHtml(array(
				'type' => 'selectbox',
				'name' => 'store_id',
				'value' => $this->data['store_id'],
				'options' => $stores,
				'style' => 'large-field',
				'attr' => 'disabled'
		));

		//build recipient filter
		$options = array('' => $this->language->get('text_custom_send'));

		$all_subscribers = $this->model_sale_customer->getAllSubscribers(array('status' => 1, 'approved' => 1));
		$all_subscribers_count = sizeof($this->_unify_customer_list($all_subscribers));
		if($all_subscribers_count){
			$options['all_subscribers'] = $this->language->get('text_all_subscribers') . ' ' . sprintf($this->language->get('text_total_to_be_sent'), $all_subscribers_count);
		}

		$only_subscribers = $this->model_sale_customer->getOnlyNewsletterSubscribers(array('status' => 1, 'approved' => 1));
		$only_subscribers_count = sizeof($this->_unify_customer_list($only_subscribers));
		if($only_subscribers_count){
			$options['only_subscribers'] = $this->language->get('text_subscribers_only') . ' ' . sprintf($this->language->get('text_total_to_be_sent'), $only_subscribers_count);
		}

		$only_customers = $this->model_sale_customer->getOnlyCustomers(array('status' => 1, 'approved' => 1));
		$only_customers_count = sizeof($this->_unify_customer_list($only_customers));
		if($only_customers_count){
			$options['only_customers']  = $this->language->get('text_customers_only') . ' ' . sprintf($this->language->get('text_total_to_be_sent'), $only_customers_count);
		}

		$options['ordered'] = $this->language->get('text_customers_who_ordered');

		$this->data['form']['fields']['to'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'recipient',
		    'value' => $this->data['recipient'],
		    'options' => $options,
		    'required' => true
		));

		$this->data['form']['fields']['customers'] = $form->getFieldHtml( array(
		    'type' => 'multiselectbox',
		    'name' => 'to[]',
		    'value' => $customer_ids,
		    'options' => $this->data['customers'],
		    'style' => 'chosen',
		    'ajax_url' => $this->html->getSecureURL('r/listing_grid/customer/customers'),
		    'placeholder' => $this->language->get('text_customers_from_lookup')
		));	

		$this->data['form']['fields']['product'] = $form->getFieldHtml( array(
		    'type' => 'multiselectbox',
		    'name' => 'products[]',
		    'value' => $product_ids,
		    'options' => $this->data['products'],
		    'style' => 'chosen',
		    'ajax_url' => $this->html->getSecureURL('r/product/product/products'),
		    'placeholder' => $this->language->get('text_products_from_lookup')
		));

		$this->data['form']['fields']['subject'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'subject',
				'value' => $this->data['subject'],
				'required' => true
		));

		$this->data['form']['fields']['message'] = $form->getFieldHtml(array(
				'type' => ($this->data['protocol']=='email' ? 'texteditor' : 'textarea'),
				'name' => 'message',
				'value' => $this->data['message'],
				'style' => 'ml_ckeditor',
				'required' => true
		));

		//if email address given
		if (has_value($this->request->get['email'])) {
			$this->data['emails'] = (array)$this->request->get['email'];
		}

		$this->data['category_products'] = $this->html->getSecureURL('product/product/category');
		$this->data['customers_list'] = $this->html->getSecureURL('user/customers');
		$this->data['presave_url'] = $this->html->getSecureURL('r/sale/contact/presave');

		$this->data['help_url'] = $this->gen_help_url('send_mail');

		if($this->data['protocol'] == 'email'){
			$resources_scripts = $this->dispatch(
					'responses/common/resource_library/get_resources_scripts',
					array (
							'object_name' => 'contact',
							'object_id'   => '',
							'types'       => array ('image'),
					)
			);
			$this->data['resources_scripts'] = $resources_scripts->dispatchGetOutput();
			$this->data['rl'] = $this->html->getSecureURL('common/resource_library', '&action=list_library&object_name=&object_id&type=image&mode=single');
		}

		//load tabs controller
		if($this->data['protocol']=='email' || !has_value($this->data['protocol'])){
			$this->data['active'] = 'email';
		}elseif($this->data['protocol']=='sms'){
			$this->data['active'] = 'sms';
		}

		$this->data['protocols'] = array();
		$this->data['protocols']['email'] = array(
				'title' => $this->language->get('text_email'),
				'href'  => $this->html->getSecureURL('sale/contact/email'),
				'icon' => 'mail'
		);
		$this->data['protocols']['sms'] = array(
				'title' => $this->language->get('text_sms'),
				'href'  => $this->html->getSecureURL('sale/contact/sms')
		);


		$this->view->batchAssign($this->data);
		$this->processTemplate('pages/sale/contact.tpl');

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}


	public function email(){
		$this->data['protocol'] = 'email';
		$this->main();
	}

	public function sms(){
		$this->data['protocol'] = 'sms';
		$this->main();
	}




	/**
	 * function filters customers list by unique email, to prevent duplicate emails
	 * @param array $list
	 * @return array|bool
	 */
	private function _unify_customer_list($list = array()) {
		if (!is_array($list)) {
			return array();
		}
		$output = array();
		foreach ($list as $c) {
			if (has_value($c['email'])) {
				$output[$c['email']] = $c;
			}
		}
		return $output;
	}

}
