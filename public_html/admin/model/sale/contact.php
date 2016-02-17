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

/**
 * Class ModelSaleContact
 * @property ModelSettingStore $model_setting_store
 * @property ModelSaleCustomer $model_sale_customer
 */
class ModelSaleContact extends Model {
	public $errors = array();
	private $eta = array();


	/**
	 * @param string $task_name
	 * @param array $data
	 * @return array|bool
	 */
	public function createTask($task_name, $data = array()){

		if (!$task_name){
			$this->errors[] = 'Can not to create task. Empty task name has been given.';
		}

		//first of all needs to define recipient count
		$this->load->model('sale/customer');
		$this->load->model('setting/store');
		$store_info = $this->model_setting_store->getStore($data['store_id']);
		if ($store_info){
			$store_name = $store_info['store_name'];
		} else{
			$store_name = $this->config->get('store_name');
		}

		//get URIs of recipients
		if($data['protocol']=='email'){
			list($uris, $subscribers) = $this->_get_email_list($data);
			$task_controller = 'task/sale/contact/sendEmail';
		}elseif($data['protocol']=='sms'){
			list($uris, $subscribers) = $this->_get_phone_list($data);
			$task_controller = 'task/sale/contact/sendSms';
		}

		if (!$uris){
			$this->errors[] = 'No recipients!';
			return false;
		}

		$divider = 10;
		$steps_count = ceil(sizeof($uris) / $divider);

		$tm = new ATaskManager();

		//1. create new task
		$task_id = $tm->addTask(
				array ('name'               => $task_name,
				       'starter'            => 1, //admin-side is starter
				       'created_by'         => $this->user->getId(), //get starter id
				       'status'             => 1, // shedule it!
				       'start_time'         => date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d') + 1, date('Y'))),
				       'last_time_run'      => '0000-00-00 00:00:00',
				       'progress'           => '0',
				       'last_result'        => '0',
				       'run_interval'       => '0',
				       'max_execution_time' => '0'
				)
		);
		if (!$task_id){
			$this->errors = array_merge($this->errors, $tm->errors);
			return false;
		}


		//create steps for sending
		$k=0;
		while ($steps_count > 0){
			$uri_list = array_slice($uris, $k, $divider);
			$step_id = $tm->addStep(array (
					'task_id'            => $task_id,
					'sort_order'         => 1,
					'status'             => 1,
					'last_time_run'      => '0000-00-00 00:00:00',
					'last_result'        => '0',
					'max_execution_time' => 4*$divider,
					'controller'         => $task_controller,
					'settings'           => array (
							'to'            => $uri_list,
							'subject'       => $data['subject'],
							'message'       => $data['message'],
							'store_name'    => $store_name,
							'subscribers'   => $subscribers

					)
			));

			if (!$step_id){
				$this->errors = array_merge($this->errors, $tm->errors);
				return false;
			} else{
				// get eta in seconds
				$this->eta[$step_id] = 4*$divider;
			}
			$steps_count--;
			$k = $k+5;
		}


		$task_details = $tm->getTaskById($task_id);
		if($task_details){
			foreach($this->eta as $step_id => $eta){
				$task_details['steps'][$step_id]['eta'] = $eta;
				//remove settings from output json array. We will take it from database on execution.
				$task_details['steps'][$step_id]['settings'] = array();
			}
			return $task_details;
		}else{
			$this->errors[] = 'Can not to get task details for execution';
			$this->errors = array_merge($this->errors,$tm->errors);
			return false;
		}

	}


	private function _get_email_list($data){
		$subscribers = $emails = array ();
		// All customers by group
		if (isset($data['recipient'])){
			$results = array ();
			if ($data['recipient'] == 'all_subscribers'){
				$all_subscribers = $this->model_sale_customer->getAllSubscribers();
				$results = $this->_unify_customer_list('email',$all_subscribers);
				$subscribers = $results;
			} else
			if ($data['recipient'] == 'only_subscribers'){
				$only_subscribers = $this->model_sale_customer->getOnlyNewsletterSubscribers();
				$results = $this->_unify_customer_list('email',$only_subscribers);
				$subscribers = $results;
			} else
			if ($data['recipient'] == 'only_customers'){
				$only_customers = $this->model_sale_customer->getOnlyCustomers(array ('status' => 1, 'approved' => 1));
				$results = $this->_unify_customer_list('email',$only_customers);
			}
			foreach ($results as $result){
				$customer_id = $result['customer_id'];
				$emails[$customer_id] = trim($result['email']);
			}
		}

		// All customers by name/email
		if (isset($data['to']) && is_array($data['to'])){
			foreach ($data['to'] as $customer_id){
				$customer_info = $this->model_sale_customer->getCustomer($customer_id);
				if ($customer_info){
					$emails[] = trim($customer_info['email']);
				}
			}
		}
		// All customers by product
		if (isset($data['products']) && is_array($data['products'])){
			$emails = array ();
			foreach ($data['products'] as $product_id){
				$results = $this->model_sale_customer->getCustomersByProduct($product_id);
				foreach ($results as $result){
					$emails[] = trim($result['email']);
				}
			}
		}

		// Prevent Duplicates
		$emails = array_unique($emails);

		return array($emails, $subscribers);
	}

	private function _get_phone_list($data){
		$subscribers = $phones = array ();
		// All customers by group
		if (isset($data['recipient'])){
			$results = array ();
			if ($data['recipient'] == 'all_subscribers'){
				$all_subscribers = $this->model_sale_customer->getAllSubscribers();
				$results = $this->_unify_customer_list('sms',$all_subscribers);
				$subscribers = $results;
			} else
			if ($data['recipient'] == 'only_subscribers'){
				$only_subscribers = $this->model_sale_customer->getOnlyNewsletterSubscribers();
				$results = $this->_unify_customer_list('sms',$only_subscribers);
				$subscribers = $results;
			} else
			if ($data['recipient'] == 'only_customers'){
				$only_customers = $this->model_sale_customer->getOnlyCustomers(array ('status' => 1, 'approved' => 1));
				$results = $this->_unify_customer_list('sms',$only_customers);
			}
			foreach ($results as $result){
				$customer_id = $result['customer_id'];
				$phones[$customer_id] = trim($result['sms']);
			}

		}

		// All customers by name/email
		if (isset($data['to']) && is_array($data['to'])){
			foreach ($data['to'] as $customer_id){
				$customer_info = $this->model_sale_customer->getCustomer($customer_id);
				if ($customer_info){
					$phones[] = trim($customer_info['sms']);
				}
			}
		}
		// All customers by product
		if (isset($data['products']) && is_array($data['products']) && $data['products']){
			foreach ($data['products'] as $product_id){
				$results = $this->model_sale_customer->getCustomersByProduct($product_id);
				foreach ($results as $result){
					$phones[] = trim($result['sms']);
				}
			}
		}

		// Prevent Duplicates
		$phones = array_unique($phones);
		return array($phones, $subscribers);
	}


	/**
	 * function filters customers list by unique email, to prevent duplicate emails
	 * @param array $list
	 * @return array|bool
	 */
	private function _unify_customer_list($field_name='email', $list = array()) {
		if (!is_array($list)) {
			return array();
		}
		$output = array();
		foreach ($list as $c) {
			if (has_value($c[$field_name])) {
				$output[$c[$field_name]] = $c;
			}
		}
		return $output;
	}


}
