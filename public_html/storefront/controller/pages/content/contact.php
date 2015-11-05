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
if (!defined('DIR_CORE')){
	header('Location: static_pages/');
}

class ControllerPagesContentContact extends AController{
	private $error = array ();
	/**
	 * @var AForm
	 */
	private $form;

	public function main(){

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->document->setTitle($this->language->get('heading_title'));

		$this->form = new AForm('ContactUsFrm');
		$this->form->loadFromDb('ContactUsFrm');
		$form = $this->form->getForm();

		if ($this->request->is_POST() && $this->_validate()){
			// move all uploaded files to their directories
			$file_pathes = $this->form->processFileUploads($this->request->files);
			$template = new ATemplate();

			$subject = sprintf($this->language->get('email_subject'), $this->request->post['name']);
			$template->data['subject'] = $subject;

			$mail = new AMail($this->config);
			$mail->setTo($this->config->get('store_main_email'));
			$mail->setFrom($this->request->post['email']);
			$mail->setSender($this->request->post['first_name']);
			$mail->setSubject($subject);

			$store_logo = md5(pathinfo($this->config->get('config_logo'), PATHINFO_FILENAME)) . '.' . pathinfo($this->config->get('config_logo'), PATHINFO_EXTENSION);
			$template->data['logo'] = 'cid:' . $store_logo;
			$template->data['store_name'] = $this->config->get('store_name');
			$template->data['store_url'] = $this->config->get('config_url');
			$template->data['text_project_label'] = project_base();
			$template->data['entry_enquiry'] = $msg = $this->language->get('entry_enquiry');
			$msg .= "\r\n" . $this->request->post['enquiry'] . "\r\n";
			$template->data['enquiry'] = nl2br($this->request->post['enquiry'] . "\r\n");

			$form_fields = $this->form->getFields();
			$template->data['form_fields'] = array();
			foreach ($form_fields as $field_name => $field_info){
				if (has_value($this->request->post[$field_name]) && !in_array($field_name, array ('first_name', 'email', 'enquiry', 'captcha'))){
					$field_details = $this->form->getField($field_name);
					$msg .= "\r\n" . rtrim($field_details['name'], ':') . ":\t" . $this->request->post[$field_name];
					$template->data['form_fields'][rtrim($field_details['name'], ':')] = $this->request->post[$field_name];
				}
			}

			if ($file_pathes){
				$msg .= "\r\n" . $this->language->get('entry_attached') . ": \r\n";
				foreach ($file_pathes as $file_info){
					$basename = pathinfo(str_replace(' ', '_', $file_info['path']), PATHINFO_BASENAME);
					$msg .= "\t" . $file_info['display_name'] . ': ' . $basename . " (" . round(filesize($file_info['path']) / 1024, 2) . "Kb)\r\n";
					$mail->addAttachment($file_info['path'], $basename);
					$template->data['form_fields'][$file_info['display_name']] = $basename . " (" . round(filesize($file_info['path']) / 1024, 2) . "Kb)";
				}
			}
			$mail_html = $template->fetch('mail/contact.tpl');
			$mail->setHtml($mail_html);
			$mail->addAttachment(DIR_RESOURCE . $this->config->get('config_logo'), $store_logo);

			$mail->setText(strip_tags(html_entity_decode($msg, ENT_QUOTES, 'UTF-8')));
			$mail->send();
			//get success_page
			if ($form['success_page']){
				$success_url = $this->html->getSecureURL($form['success_page']);
			} else{
				$success_url = $this->html->getSecureURL('content/contact/success');
			}
			$this->redirect($success_url);
		}

		if ($this->request->is_POST()){
			foreach ($this->request->post as $name => $value){
				$this->form->assign($name, $value);
			}
		}

		$this->document->resetBreadcrumbs();

		$this->document->addBreadcrumb(array (
				'href'      => $this->html->getURL('index/home'),
				'text'      => $this->language->get('text_home'),
				'separator' => false
		));

		$this->document->addBreadcrumb(array (
				'href'      => $this->html->getURL('content/contact'),
				'text'      => $this->language->get('heading_title'),
				'separator' => $this->language->get('text_separator')
		));

		$this->view->assign('form_output', $this->form->getFormHtml());

		$this->view->assign('action', $this->html->getURL('content/contact'));
		$this->view->assign('store', $this->config->get('store_name'));
		$this->view->assign('address', nl2br($this->config->get('config_address')));
		$this->view->assign('telephone', $this->config->get('config_telephone'));
		$this->view->assign('fax', $this->config->get('config_fax'));

		$this->processTemplate('pages/content/contact.tpl');

		//init controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function success(){

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->resetBreadcrumbs();

		$this->document->addBreadcrumb(array (
				'href'      => $this->html->getURL('index/home'),
				'text'      => $this->language->get('text_home'),
				'separator' => false
		));

		$this->document->addBreadcrumb(array (
				'href'      => $this->html->getURL('content/contact'),
				'text'      => $this->language->get('heading_title'),
				'separator' => $this->language->get('text_separator')
		));

		if ($this->config->get('embed_mode') == true){
			$continue_url = $this->html->getURL('product/category');
		} else{
			$continue_url = $this->html->getURL('index/home');
		}

		$this->view->assign('continue', $continue_url);

		$continue = HtmlElementFactory::create(array ('type'  => 'button',
		                                              'name'  => 'continue_button',
		                                              'text'  => $this->language->get('button_continue'),
		                                              'style' => 'button'));
		$this->view->assign('continue_button', $continue);

		if ($this->config->get('embed_mode') == true){
			//load special headers
			$this->addChild('responses/embed/head', 'head');
			$this->addChild('responses/embed/footer', 'footer');
			$this->processTemplate('embed/common/success.tpl');
		} else{
			$this->processTemplate('common/success.tpl');
		}

		//init controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	/**
	 * @return bool
	 */
	private function _validate(){
		$this->error = array_merge($this->form->validateFormData($this->request->post), $this->error);
		if (!$this->error){
			return true;
		} else{
			$this->form->setErrors($this->error);
			return false;
		}
	}
}
