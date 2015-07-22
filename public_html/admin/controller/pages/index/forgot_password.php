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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ControllerPagesIndexForgotPassword extends AController {

	public $data = array();
	private $user_data;
	public $error = array();

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);
		
		$this->loadLanguage('common/forgot_password');
		$this->document->setTitle( $this->language->get('heading_title') );

		if ($this->request->is_POST() && $this->_validate()) {

			//generate hash
			$hash = AEncryption::getHash(time());
			$link = $this->html->getSecureURL('index/forgot_password/validate','&hash='.$hash);

			$this->cache->set($hash, $this->request->post['email']);

			$mail = new AMail( $this->config );
			$mail->setTo($this->request->post['email']);
			$mail->setFrom($this->config->get('store_main_email'));
			$mail->setSender($this->config->get('config_owner'));
			$mail->setSubject(sprintf($this->language->get('reset_email_subject'), $this->config->get('store_name')));
			$mail->setHtml(sprintf($this->language->get('reset_email_body_html'), $link, $link));
			$mail->setText(sprintf($this->language->get('reset_email_body_text'), $link, $link));
			$mail->send();

			$this->redirect($this->html->getSecureURL('index/forgot_password','&mail=sent'));

		}

		$this->data['login'] =  $this->html->getSecureURL('index/login');

		if ( isset($this->request->get['mail']) && $this->request->get['mail'] == 'sent' ) {

			$this->data['show_instructions'] = true;

		} else {

			$this->data['error'] = $this->error;

			$fields = array('username', 'email', 'captcha');
			foreach ( $fields as $f ) {
				if (isset ( $this->request->post [$f] )) {
					$this->data [$f] = $this->request->post [$f];
				} else {
					$this->data[$f] = '';
				}
			}

			$this->data['action'] = $this->html->getSecureURL('index/forgot_password');
			$this->data['update'] = '';
			$form = new AForm('ST');

			$form->setForm(
				array(
					'form_name' => 'forgotFrm',
					'update' => $this->data['update'],
				)
			);

			$this->data['form']['id'] = 'forgotFrm';
			$this->data['form']['form_open'] = $form->getFieldHtml(
				array(
					'type' => 'form',
					'name' => 'forgotFrm',
					'action' => $this->data['action'],
				)
			);
			$this->data['form']['submit'] = $form->getFieldHtml(
				array(
					'type' => 'button',
					'name' => 'submit',
					'text' => $this->language->get('button_reset_password'),
					'style' => 'button3',
				)
			);

			foreach ( $fields as $f ) {
				$this->data['form']['fields'][$f] = $form->getFieldHtml(
					array(
						'type' => ($f == 'captcha' ? 'captcha' : 'input'),
						'name' => $f,
						'value' => $this->data[$f],
						'required' => true,
						'placeholder' => $this->language->get('entry_'.$f),
					)
				);
			}

		}

		$this->view->batchAssign( $this->data );

		$this->processTemplate('pages/index/forgot_password.tpl' );

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function validate() {
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);
		$this->loadLanguage('common/forgot_password');
		$this->document->setTitle( $this->language->get('heading_title') );

		if ($this->request->is_POST() && $this->_validateCaptcha()) {

			//generate password
			$password = AUser::generatePassword(8);
			$this->model_user_user->editUser($this->user_data['user_id'], array('password' => $password));

			$mail = new AMail($this->config);
			$mail->setTo($this->user_data['email']);
			$mail->setFrom($this->config->get('store_main_email'));
			$mail->setSender($this->config->get('config_owner'));
			$mail->setSubject(sprintf($this->language->get('reset_email_subject'), $this->config->get('store_name')));
			$mail->setHtml(sprintf($this->language->get('new_password_email_body'), $password));
			$mail->setText(sprintf($this->language->get('new_password_email_body'), $password));
			$mail->send();

			$this->cache->delete($this->request->get['hash']);

			$this->redirect($this->html->getSecureURL('index/forgot_password/validate','&mail=sent'));

		}

		$this->data['text_heading'] =  $this->language->get('text_heading_reset');
		$this->data['login'] =  $this->html->getSecureURL('index/login');


		if ( isset($this->request->get['mail']) && $this->request->get['mail'] == 'sent' ) {

			$this->data['show_instructions'] = true;
			$this->data['text_instructions'] =  $this->language->get('text_instructions_reset');

		} else {

			$this->data['error'] = $this->error;

			$fields = array('captcha');
			foreach ( $fields as $f ) {
				if (isset ( $this->request->post [$f] )) {
					$this->data [$f] = $this->request->post [$f];
				} else {
					$this->data[$f] = '';
				}
			}

			$this->data['action'] = $this->html->getSecureURL('index/forgot_password/validate', '&hash='.$this->request->get['hash']);
			$this->data['update'] = '';
			$form = new AForm('ST');

			$form->setForm(
				array(
					'form_name' => 'forgotFrm',
					'update' => $this->data['update'],
				)
			);

			$this->data['form']['id'] = 'forgotFrm';
			$this->data['form']['form_open'] = $form->getFieldHtml(
				array(
					'type' => 'form',
					'name' => 'forgotFrm',
					'action' => $this->data['action'],
				)
			);
			$this->data['form']['submit'] = $form->getFieldHtml(
				array(
					'type' => 'button',
					'name' => 'submit',
					'text' => $this->language->get('button_reset_password'),
					'style' => 'button3',
				)
			);

			foreach ( $fields as $f ) {
				$this->data['form']['fields'][$f] = $form->getFieldHtml(
					array(
						'type' => ($f == 'captcha' ? 'captcha' : 'input'),
						'name' => $f,
						'value' => $this->data[$f],
						'required' => true,
						'placeholder' => $this->language->get('entry_'.$f),
					)
				);
			}

		}

		$this->view->batchAssign( $this->data );

		$this->processTemplate('pages/index/forgot_password.tpl' );

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	private function _validate() {
		if ( mb_strlen($this->request->post['username']) < 1 ) {
			$this->error['username'] = $this->language->get('error_username');
		}

		$pattern = '/^[A-Z0-9._%-]+@[A-Z0-9][A-Z0-9.-]{0,61}[A-Z0-9]\.[A-Z]{2,6}$/i';
		if (!preg_match($pattern, $this->request->post['email'])) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if (!isset($this->session->data['captcha']) || ($this->session->data['captcha'] != $this->request->post['captcha'])) {
			$this->error['captcha'] = $this->language->get('error_captcha');
		}

		if ( !$this->error && !$this->user->validate($this->request->post['username'], $this->request->post['email']) ) {
			$this->error['warning'] = $this->language->get('error_match');
		}

		$this->extensions->hk_ValidateData($this);

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	private function _validateCaptcha() {
		if (!isset($this->session->data['captcha']) || ($this->session->data['captcha'] != $this->request->post['captcha'])) {
			$this->error['captcha'] = $this->language->get('error_captcha');
		}

		$email = $this->cache->get($this->request->get['hash']);

		if ( empty($email) ) {
			$this->error['warning'] =  $this->language->get('error_hash');
		} else {
			$this->loadModel('user/user');
			$users = $this->model_user_user->getUsers( array( 'search' => "email = '".$this->db->escape($email)."'" ) );
			if ( empty( $users ) ) {
				$this->error['warning'] =  $this->language->get('error_hash');
			} else {
				$this->user_data = $users[0];
			}
		}

		$this->extensions->hk_ValidateData($this);

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}