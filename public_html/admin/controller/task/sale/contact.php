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
if (!defined('DIR_CORE') || !IS_ADMIN){
	header('Location: static_pages/');
}

class ControllerTaskSaleContact extends AController{

	private $protocol;
	public function sendSms(){
		//for aborting process
		ignore_user_abort(false);
		session_write_close();

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->protocol = 'sms';
		$result = $this->_send();
		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		return $result;
	}

	public function sendEmail(){
		//for aborting process
		ignore_user_abort(false);
		session_write_close();

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->protocol = 'email';
		$result = $this->_send();

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		return $result;
	}
	private function _send(){

		$this->loadLanguage('sale/contact');

		$task_id = (int)$this->request->get['task_id'];
		$step_id = (int)$this->request->get['step_id'];

		if (!$task_id || !$step_id){
			$error_text = 'Cannot run task step. Task_id (or step_id) has not been set.';
			$this->_return_error($error_text);
		}

		$tm = new ATaskManager();
		$task_info = $tm->getTaskById($task_id);
		$sent = (int)$task_info['settings']['sent'];
		$task_steps = $tm->getTaskSteps($task_id);
		$step_info = array();
		foreach($task_steps as $task_step){
			if($task_step['step_id'] == $step_id){
				$step_info = $task_step;
				if($task_step['sort_order']==1){
					$tm->updateTask($task_id, array('last_time_run' => date('Y-m-d H:i:s')));
				}
				break;
			}
		}

		if(!$step_info){
			$error_text = 'Cannot run task step. Looks like task #'.$task_id.' does not contain step #'.$step_id;
			$this->_return_error($error_text);
		}

		$tm->updateStep($step_id, array('last_time_run' => date('Y-m-d H:i:s')));

		if(!$step_info['settings'] || !$step_info['settings']['to']){
			$error_text = 'Cannot run task step #'.$step_id.'. Unknown settings for it.';
			$this->_return_error($error_text);
		}

		$this->loadModel('sale/customer');
		$this->loadModel('setting/store');
		$store_info = $this->model_setting_store->getStore((int)$this->session->data['current_store_id']);
		$from = '';
		if ($store_info){
			$from = $store_info['store_main_email'];
		}
		if(!$from){
			$from = $this->config->get('store_main_email');
		}

		$send_data = array(
				'subject' => $step_info['settings']['subject'],
				'message' => $step_info['settings']['message'],
				'sender' => $step_info['settings']['store_name'],
				'from' => $from
		);
		//send emails in loop and update task's step info for restarting if step or task failed
		$step_settings =  $step_info['settings'];
		$cnt = 0;
		$step_result = true;
		foreach($step_info['settings']['to'] as $to){
			$send_data['subscriber'] = in_array($to,$step_info['settings']['subscribers']) ? true: false;

			if($this->protocol=='email'){
				$result = $this->_send_email($to, $send_data);
			}elseif($this->protocol=='sms'){
				$result = $this->_send_sms($to, $send_data);
			}else{
				$result = false;
			}

			if($result){
				//remove sent address from step
				$k = array_search($to,$step_settings['to']);
				unset($step_settings['to'][$k]);
				$tm->updateStep($step_id, array('settings' => serialize($step_settings)));
				//update task details to show them at the end
				$sent++;
				$tm->updateTaskDetails($task_id,
						array(
								'created_by' => $this->user->getId(),
								'settings'   => array(
													'recipients_count' => $task_info['settings']['recipients_count'],
													'sent'             => $sent
													)
				));

			}else{
				$step_result = false;
			}
			$cnt++;
		}

		$tm->updateStep($step_id, array('last_result' => $step_result));

		if(!$step_result){
			$this->_return_error('Some errors during step run. See log for details.');
		}
		return $step_result;
	}

	private function _return_error($error_text){
		$error = new AError($error_text);
		$error->toLog()->toDebug();
		return $error->toJSONResponse('APP_ERROR_402',
				array ('error_text'  => $error_text,
				       'reset_value' => true
				));
	}


	private function _send_email($email, $data){
		if(!$email || !$data){
			$error = new AError('Error: Cannot send email. Unknown address or empty message.');
			$error->toLog()->toMessages();
			return false;
		}

		// HTML Mail
		$template = new ATemplate();
		$template->data['lang_direction'] = $this->language->get('direction');
		$template->data['lang_code'] = $this->language->get('code');
		$text_subject = $data['subject'];
		$template->data['subject'] = $text_subject;

		$text_unsubscribe = $this->language->get('text_unsubscribe');

		$text_message = $data['message'];

		$mail = new AMail($this->config);

		$mail->setTo($email);
		$mail->setFrom($data['from']);
		$mail->setSender($data['sender']);
		$mail->setSubject($text_subject);

		$message_body = $text_message;
		if ($data['subscriber']) {
			$customer_info = $this->model_sale_customer->getCustomersByEmails(array($email));
			$customer_id = $customer_info[0]['customer_id'];
			if($customer_id){
				$message_body .= "\n\n<br><br>" . sprintf($text_unsubscribe,
								$email,
								$this->html->getCatalogURL('account/notification', '&email=' . $email . '&customer_id=' . $customer_id));
			}
		}

		$template->data['body'] = html_entity_decode($message_body, ENT_QUOTES, 'UTF-8');
		$html = $template->fetch('mail/contact.tpl');
		$mail->setHtml($html);
		$mail->send();
		if ($mail->error) {
			return false;
		}

		return true;
	}

	private function _send_sms($phone, $data){
		if(!$phone || !$data){
			$error = new AError('Error: Cannot send sms. Unknown phone number or empty message.');
			$error->toLog()->toMessages();
			return false;
		}

		$driver = null;
		$driver_txt_id = $this->config->get('config_sms_driver');

		//if driver not set - skip protocol
		if (!$driver_txt_id){
			return false;
		}
		//use safe usage
		try{
			include_once(DIR_EXT . $driver_txt_id . '/core/lib/' . $driver_txt_id . '.php');
			//if class of driver
			$classname = preg_replace('/[^a-zA-Z]/', '', $driver_txt_id);
			if (!class_exists($classname)){
				$error = new AError('IM-driver ' . $driver_txt_id . ' load error.');
				$error->toLog()->toMessages();
				return false;
			}

			$driver = new $classname();
		} catch(AException $e){	}

		if($driver === null){
			return false;
		}

		$text = $this->config->get('store_name') . ": " .$data['message'];
		$to = $phone;
		$result = true;
		if ($text && $to){
			//use safe call
			try{
				$result = $driver->send($to, $text);
			} catch(AException $e){
				return false;
			}
		}

		return $result;
	}

}
