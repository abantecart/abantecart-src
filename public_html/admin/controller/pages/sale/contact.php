<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2016 Belavier Commerce LLC

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


	public function email(){
		$this->data['protocol'] = 'email';
		$this->main();
	}

	public function sms(){
		$driver = $this->config->get('config_sms_driver');
		//if sms driver not set or disabled - redirect
		if(!$driver || !$this->config->get($driver.'_status')){
			$this->redirect($this->html->getSecureURL('sale/contact/email'));
		}

		$this->data['protocol'] = 'sms';
		$this->main();
	}

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
		$this->data['store_id'] = (int)$this->session->data['current_store_id'];

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
			//get thumbnails by one pass
			$resource = new AResource('image');
			$thumbnails = $resource->getMainThumbList(
					'products',
					$product_ids,
					$this->config->get('config_image_grid_width'),
					$this->config->get('config_image_grid_height')
					);



			foreach ($product_ids as $product_id) {
				$product_info = $this->model_catalog_product->getProduct($product_id);
				if ($product_info) {
				$thumbnail = $thumbnails[ $product_id ];
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

		$form = new AForm('ST');
		$form->setForm(array(
				'form_name' => 'sendFrm',
				'update' => $this->data['update']
		));

		$this->data['form']['form_open'] = $form->getFieldHtml(
				array(
						'type' => 'form',
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


		//set store selector
		$this->view->assign('form_store_switch', $this->html->getStoreSwitcher());

		//build recipient filter
		$options = array('' => $this->language->get('text_custom_send'));

		$db_filter = array('status' => 1, 'approved' => 1);
		if($this->data['protocol']=='sms'){
			$db_filter['filter']['only_with_mobile_phones'] = 1;
		}

		$newsletter_dbfilter = $db_filter;
		$newsletter_dbfilter['filter']['newsletter_protocol'] = $this->data['protocol'];

		$all_subscribers_count = $this->model_sale_customer->getTotalAllSubscribers($newsletter_dbfilter);
		if($all_subscribers_count){
			$options['all_subscribers'] = $this->language->get('text_all_subscribers') . ' ' . sprintf($this->language->get('text_total_to_be_sent'), $all_subscribers_count);
		}

		$only_subscribers_count = $this->model_sale_customer->getTotalOnlyNewsletterSubscribers($newsletter_dbfilter);
		if($only_subscribers_count){
			$options['only_subscribers'] = $this->language->get('text_subscribers_only') . ' ' . sprintf($this->language->get('text_total_to_be_sent'), $only_subscribers_count);
		}

		$only_customers_count = $this->model_sale_customer->getTotalOnlyCustomers($db_filter);
		if($only_customers_count){
			$options['only_customers']  = $this->language->get('text_customers_only') . ' ' . sprintf($this->language->get('text_total_to_be_sent'), $only_customers_count);
		}

		if(sizeof($options)==1){
			$this->data['error_warning'] = $this->language->get('error_'.$this->data['protocol'].'_no_recipients');
		}

		$options['ordered'] = $this->language->get('text_customers_who_ordered');

		$this->data['form']['fields']['to'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'recipient',
		    'value' => $this->data['recipient'],
		    'options' => $options,
		    'required' => true
		));

		$this->data['recipients_count_url'] = $this->html->getSecureURL('r/sale/contact/getRecipientsCount');

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

		if($this->data['protocol']=='email'){
			$this->data['form']['fields']['subject'] = $form->getFieldHtml(array (
					'type'     => 'input',
					'name'     => 'subject',
					'value'    => $this->data['subject'],
					'required' => true
			));
		}

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
		$driver = $this->config->get('config_sms_driver');
		//if sms driver not set or disabled - redirect
		if($driver && $this->config->get($driver.'_status')){
			$this->data['protocols']['sms'] = array (
					'title' => $this->language->get('text_sms'),
					'href'  => $this->html->getSecureURL('sale/contact/sms')
			);
		}

		//check for incompleted tasks
		$tm = new ATaskManager();
		$incompleted = $tm->getTasks(array(
				'filter' => array(
						'name' => 'send_now'
				)
		));

		foreach($incompleted as $incm_task){
			//show all incompleted tasks for Top Administrator user group
			if($this->user->getUserGroupId() != 1){
				if ($incm_task['starter'] != $this->user->getId()){
					continue;
				}
				//rename task to prevent colission with new
				if($incm_task['name']=='send_now'){
					$tm->updateTask($incm_task['task_id'],array('name' => 'send_now_'.date('YmdHis')));
				}
			}
			//define incompleted tasks by last time run
			$max_exec_time = (int)$incm_task['max_execution_time'];
			if(!$max_exec_time){
				//if no limitations for execution time for task - think it's 2 hours
				$max_exec_time = 7200;
			}
			if( time() - dateISO2Int($incm_task['last_time_run']) > $max_exec_time ){
				$this->data['incomplete_tasks_url'] = $this->html->getSecureURL('r/sale/contact/incompleted');
				break;
			}
		}

		$this->view->batchAssign($this->data);
		$this->processTemplate('pages/sale/contact.tpl');

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

}
