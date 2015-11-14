<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Lincence details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
/** @noinspection PhpUndefinedClassInspection */
/**
 * Class ControllerPagesFormsManagerDefaultEmail
 * @property ModelToolFormsManager $model_tool_forms_manager
 */
class ControllerPagesFormsManagerDefaultEmail extends AController {

	public $data = array ();

	public function main() {

		$this->loadModel('tool/forms_manager');
		$this->loadLanguage('forms_manager/forms_manager');
		$this->loadLanguage('forms_manager/default_email');

		if ( $this->request->is_POST() ) {

			$path = $_SERVER['HTTP_REFERER'];

			if ( !isset($this->request->get['form_id']) ) {
				$this->redirect($path);
				exit;
			}

			$form_id = $this->request->get['form_id'];
			$form_data = $this->model_tool_forms_manager->getForm($form_id);
			$form = new AForm($form_data['form_name']);
			$form->loadFromDb($form_data['form_name']);
			$errors = $form->validateFormData($this->request->post);

			if ( $errors ) {
				//save error and data to session
				$this->session->data['custom_form_'.$form_id] = $this->request->post;
				$this->session->data['custom_form_'.$form_id]['errors'] = $errors;
				$this->redirect($path);
				exit;
			}else {

				$mail = new AMail( $this->config );
				$mail->setTo($this->config->get('store_main_email'));

				if ( isset($this->request->post['email']) ) {
					$mail->setFrom($this->request->post['email']);
					unset($this->request->post['email']);
				} else {
					$sender_email = $this->config->get('forms_manager_default_sender_email');
					$sender_email = !$sender_email ? $this->config->get('store_main_email') : $sender_email;
					$mail->setFrom($sender_email);
				}

				if ( isset($this->request->post['first_name']) ) {
					$mail->setSender($this->request->post['first_name']);
					unset($this->request->post['first_name']);
				} else {

					$sender_name = $this->config->get('forms_manager_default_sender_name');
					$sender_name = !$sender_name ? $this->config->get('store_name') : $sender_name;
					$mail->setSender($sender_name);
				}

				if ( isset($this->request->post['email_subject']) ) {
					$mail->setSubject($this->request->post['email_subject']);
					unset($this->request->post['email_subject']);
				} else {
					$mail->setSubject($form_data['form_name']);
				}

				$msg = $this->config->get('store_name')."\r\n".$this->config->get('config_url')."\r\n";

				$fields = $this->model_tool_forms_manager->getFields($form_id);

				foreach ( $fields as $field ) {
					// skip files and captchas
					if(in_array($field['element_type'],array('K', 'J' ,'U'))){ continue; }

					if ( isset($this->request->post[$field['field_name']]) ) {
						$val = $this->request->post[$field['field_name']];
						$val = $this->_prepareValue($val);

						//for zones
						if($field['element_type']=='Z') {
							$msg .= $field['name'] . ': ' . $val . "";
							$val = $this->request->post[$field['field_name'].'_zones'];
							$val = $this->_prepareValue($val);
							$msg .= "\t" . $val . "\r\n";
						}else{
							$msg .= $field['name'] . ': ' . $val . "\r\n";
						}
					}
				}

				// add attachments
				$file_pathes = $form->processFileUploads($this->request->files);
				if($file_pathes){
					$msg .= "\r\n".$this->language->get('entry_attached').": \r\n";
					foreach($file_pathes as $file_info){
						$basename = pathinfo(str_replace(' ','_',$file_info['path']),PATHINFO_BASENAME);
						$msg .= "\t" .$file_info['display_name'] . ': ' . $basename . " (". round(filesize($file_info['path'])/1024,2) ."Kb)\r\n";
						$mail->addAttachment($file_info['path'], $basename);
					}
				}

				$mail->setText(strip_tags(html_entity_decode($msg, ENT_QUOTES, 'UTF-8')));

				$mail->send();

				if ( empty($mail->error) ) {
					if($form_data['success_page']){
						$success_url = $this->html->getSecureURL($form_data['success_page']);
					}else{
						$success_url = $this->html->getSecureURL('forms_manager/default_email/success');
					}
					
					//clear form session 
					unset($this->session->data['custom_form_'.$form_id]);
					$this->redirect($success_url);
					exit;
				} else {
					$this->session->data['warning'] = $mail->error;
					$this->redirect($this->html->getSecureURL('forms_manager/default_email','&form_id='.$form_id));
					exit;
				}
			}
		}

		$this->data['warning'] = $this->session->data['warning'];
		if ( isset($this->session->data['warning']) ) {
			unset($this->session->data['warning']);
		}

		$this->document->setTitle( $this->language->get('text_default_email_title') );

		$this->document->resetBreadcrumbs();

		$this->document->addBreadcrumb( array (
			'href'      => $this->html->getURL('index/home'),
			'text'      => $this->language->get('text_home'),
			'separator' => FALSE
		));

		$this->document->addBreadcrumb( array (
			'href'      => $this->html->getURL('forms_manager/default_email'),
			'text'      => $this->language->get('text_default_email_title'),
			'separator' => $this->language->get('text_separator')
		));

		$this->data['continue'] = $_SERVER['HTTP_REFERER'];
		$continue = HtmlElementFactory::create(
			array(
				'type' => 'button',
				'name' => 'continue_button',
				'text'=> $this->language->get('button_continue'),
				'style' => 'button',
				'icon' => 'icon-arrow-right'
			)
		);
		$this->data['continue_button'] = $continue;

		$this->view->batchAssign($this->data);
		$this->processTemplate('pages/default_email.tpl');
	}

	private function _prepareValue($val){
		if ( is_array($val) ) {
			if(sizeof($val)>1){
				$str = "\r\n";
			}
			foreach ( $val as $k => $v ) {
				$str .= "\t" . $k . ': ' . $v . "\r\n";
			}
			$val = $str;
		}
		return $val;
	}

	public function success() {

		$this->loadLanguage('forms_manager/default_email');

		$this->data['warning'] = $this->session->data['warning'];
		if ( isset($this->session->data['warning']) ) {
			unset($this->session->data['warning']);
		}

		$this->document->setTitle( $this->language->get('text_default_email_title') );

		$this->document->resetBreadcrumbs();

		$this->document->addBreadcrumb( array (
			'href'      => $this->html->getURL('index/home'),
			'text'      => $this->language->get('text_home'),
			'separator' => FALSE
		));

		$this->document->addBreadcrumb( array (
			'href'      => $this->html->getURL('forms_manager/default_email/success'),
			'text'      => $this->language->get('text_default_email_title'),
			'separator' => $this->language->get('text_separator')
		));

		$this->data['continue'] = $this->html->getURL('index/home');
		$continue = HtmlElementFactory::create(
			array(
				'type' => 'button',
				'name' => 'continue_button',
				'text'=> $this->language->get('button_continue'),
				'style' => 'button',
				'icon' => 'icon-arrow-right'
			)
		);
		$this->data['continue_button'] = $continue;

		$this->view->batchAssign($this->data);
		$this->processTemplate('pages/default_email_success.tpl');
	}
}