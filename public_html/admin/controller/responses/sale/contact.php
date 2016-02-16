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

/**
 * Class ControllerResponsesSaleContact
 * @property ModelSaleContact $model_sale_contact
 */
class ControllerResponsesSaleContact extends AController {
	public $data = array();
	public $errors = array();

	public function buildTask(){
		$this->data['output'] = array();
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);


		if ($this->request->is_POST() && $this->_validate()) {
			$this->loadModel('sale/contact');
			$task_details = $this->model_sale_contact->createTask('send_now', $this->request->post);

			if(!$task_details){
				$this->errors = array_merge($this->errors,$this->model_sale_contact->errors);
				$error = new AError("Mail/Notification Sending Error: \n ".implode(' ', $this->errors));
				return $error->toJSONResponse('APP_ERROR_402',
										array( 'error_text' => implode(' ', $this->errors),
												'reset_value' => true
										));
			}else{
				$this->data['output']['task_details'] = $task_details;
			//	$this->data['output']['task_details']['backup_name'] = "manual_backup_".date('Ymd_His');
			}

		}else{
			$error = new AError(implode('<br>', $this->errors));
			return $error->toJSONResponse('VALIDATION_ERROR_406',
									array( 'error_text' => implode('<br>', $this->errors),
											'reset_value' => true
									));
		}

		//update controller data
    	$this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput( AJson::encode($this->data['output']) );

	}

	public function complete(){
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		$task_id = (int)$this->request->post['task_id'];
		if(!$task_id){
			return null;
		}

		//check task result
		$tm = new ATaskManager();
		$task_info = $tm->getTaskById($task_id);
		$task_result = $task_info['last_result'];
		if($task_result){
			$tm->deleteTask($task_id);
			$result_text = 'Messages was sent successfully';
			if(has_value($this->session->data['sale_contact_presave'])){
				unset($this->session->data['sale_contact_presave']);
			}
		}else{
			$result_text = 'Some errors occured during task process. Please see log for details or restart this task.';
		}




		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput( AJson::encode(array(
													'result' => $task_result,
													'result_text' => $result_text ))
		);
	}

	public function abort(){
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		$task_id = (int)$this->request->post['task_id'];
		if(!$task_id){
			return null;
		}

		//check task result
		$tm = new ATaskManager();
		$task_info = $tm->getTaskById($task_id);

		if($task_info['name']=='send_now'){
			$tm->deleteTask($task_id);
			$result_text = 'Task aborted successfully.';
		}else{
			$error_text = 'Task #'.$task_id.' not found!';
			$error = new AError($error_text);
			return $error->toJSONResponse('APP_ERROR_402',
						array( 'error_text' => $error_text,
								'reset_value' => true
						));
		}


		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput( AJson::encode(array(
													'result' => true,
													'result_text' => $result_text ))
		);
	}

	public function presave(){
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		$this->session->data['sale_contact_presave'] = array();
		$this->session->data['sale_contact_presave'] = $this->request->post;

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	/*public function recipientCount(){
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);




		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput( AJson::encode(array(
													'recipient_count' => $recipient_count ))
		);
	}*/

	/*public function send() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);
		// this method can process only posting.
		if ($this->request->is_GET() ) {
			$this->redirect($this->html->getSecureURL('sale/contact'));
		}

		if (!$this->_validate()) {
			$this->main();
			return null;
		}

		$this->loadModel('sale/customer');
		$this->loadModel('setting/store');
		$store_info = $this->model_setting_store->getStore($this->request->post['store_id']);
		if ($store_info) {
			$store_name = $store_info['store_name'];
		} else {
			$store_name = $this->config->get('store_name');
		}

		$customers = $emails = array();

		// All customers by group
		if (isset($this->request->post['recipient'])) {
			$results = array();
			if ($this->request->post['recipient'] == 'all_subscribers') {
				$all_subscribers = $this->model_sale_customer->getAllSubscribers();
				$results = $this->_unify_customer_list($all_subscribers);
			} else if ($this->request->post['recipient'] == 'only_subscribers') {
				$only_subscribers = $this->model_sale_customer->getOnlyNewsletterSubscribers();
				$results = $this->_unify_customer_list($only_subscribers);
			} else if ($this->request->post['recipient'] == 'only_customers') {
				$only_customers = $this->model_sale_customer->getOnlyCustomers(array('status' => 1, 'approved' => 1));
				$results = $this->_unify_customer_list($only_customers);
			}
			foreach ($results as $result) {
				$customer_id = $result['customer_id'];
				$emails[$customer_id] = $customers[$customer_id] = trim($result['email']);
			}
		}

		// All customers by name/email
		if (isset($this->request->post['to']) && is_array($this->request->post['to'])) {
			foreach ($this->request->post['to'] as $customer_id) {
				$customer_info = $this->model_sale_customer->getCustomer($customer_id);
				if ($customer_info) {
					$emails[] = trim($customer_info['email']);
				}
			}
		}
		// All customers by product
		if (isset($this->request->post['products']) && is_array($this->request->post['products'])) {
			foreach ($this->request->post['products'] as $product_id) {
				$results = $this->model_sale_customer->getCustomersByProduct($product_id);
				if ($customers) {
					$emails = array();
				}
				foreach ($results as $result) {
					if ($customers && in_array($result['email'], $customers)) {
						$emails[] = trim($result['email']);
					}
				}
			}
		}

		// Prevent Duplicates
		$emails = array_unique($emails);

		if ($emails) {

			// HTML Mail
			$template = new ATemplate();
			$template->data['lang_direction'] = $this->language->get('direction');
			$template->data['lang_code'] = $this->language->get('code');
			$template->data['subject'] = $this->request->post['subject'];

			$text_unsubscribe = $this->language->get('text_unsubscribe');
			$text_subject = $this->request->post['subject'];
			$text_message = $this->request->post['message'];
			$from = $this->config->get('store_main_email');

			$mail = new AMail($this->config);
			foreach ($emails as $email) {
				$mail->setTo($email);
				$mail->setFrom($from);
				$mail->setSender($store_name);
				$mail->setSubject($text_subject);

				$message_body = $text_message;
				if ($this->request->post['recipient'] == 'newsletter') {
					if (($customer_id = array_search($email, $customers))) {
						$message_body .= "\n\n<br><br>" . sprintf($text_unsubscribe,
										$email,
										$this->html->getCatalogURL('account/unsubscribe', '&email=' . $email . '&customer_id=' . $customer_id));
					}
				}

				$template->data['body'] = html_entity_decode($message_body, ENT_QUOTES, 'UTF-8');
				$html = $template->fetch('mail/contact.tpl');
				$mail->setHtml($html);
				$mail->send();
				if ($mail->error) {
					$this->error[] = 'Error: No emails were sent! Please see error log for details.';
					$this->main();
					return null;
				}
			}
			unset($mail);
		}

		$this->session->data['success'] = $this->language->get('text_success');
		$this->redirect($this->html->getSecureURL('sale/contact'));


		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}
*/

	private function _validate() {
		if (!$this->user->canModify('sale/contact')) {
			$this->errors['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['subject']) {
			$this->errors['subject'] = $this->language->get('error_subject');
		}

		if (!$this->request->post['message']) {
			$this->errors['message'] = $this->language->get('error_message');
		}

		if (!$this->request->post['recipient'] && !$this->request->post['to'] && !$this->request->post['products']) {
			$this->errors['recipient'] = $this->language->get('error_recipients');
		}

		$this->extensions->hk_ValidateData( $this );

		if (!$this->errors) {
			return TRUE;
		} else {
			return FALSE;
		}
	}


}
