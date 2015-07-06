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

class ControllerPagesAccountSuccess extends AController {
	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->resetBreadcrumbs();

		$this->document->addBreadcrumb(array(
				'href' => $this->html->getURL('index/home'),
				'text' => $this->language->get('text_home'),
				'separator' => FALSE
		));

		$this->document->addBreadcrumb(array(
				'href' => $this->html->getURL('account/account'),
				'text' => $this->language->get('text_account'),
				'separator' => $this->language->get('text_separator')
		));

		$this->document->addBreadcrumb(array(
				'href' => $this->html->getURL('account/success'),
				'text' => $this->language->get('text_success'),
				'separator' => $this->language->get('text_separator')
		));

		$this->view->assign('heading_title', $this->language->get('heading_title'));

		if ($this->config->get('config_customer_email_activation')) {
			$this->view->assign('text_message', sprintf($this->language->get('text_activate'), $this->config->get('store_name'), $this->html->getURL('content/contact')));
		}elseif ($this->config->get('config_customer_approval')) {
			$this->view->assign('text_message', sprintf($this->language->get('text_approval'), $this->config->get('store_name'), $this->html->getURL('content/contact')));
		} else {
			$this->view->assign('text_message', sprintf($this->language->get('text_message'), $this->html->getURL('content/contact')));
		}

		$this->view->assign('button_continue', $this->language->get('button_continue'));

		if ($this->session->data['redirect']) {
			$this->view->assign('continue', $this->session->data['redirect']);
			unset($this->session->data['redirect']);
		} else {
			if ($this->cart->hasProducts()) {
				$this->view->assign('continue', $this->html->getURL('checkout/cart'));
			} else {
				$this->view->assign('continue', $this->html->getURL('account/account'));
			}
		}

		$continue = HtmlElementFactory::create(array('type' => 'button',
				'name' => 'continue_button',
				'text' => $this->language->get('button_continue'),
				'style' => 'button'));
		$this->view->assign('continue_button', $continue);

		if($this->config->get('embed_mode') == true){
		    //load special headers
	        $this->addChild('responses/embed/head', 'head');
	        $this->addChild('responses/embed/footer', 'footer');
		    $this->processTemplate('embed/common/success.tpl');
		} else {
	    	$this->processTemplate('common/success.tpl');
	    }

		//init controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		unset($this->session->data['success']);
	}

	public function sendCode() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadModel('account/customer');
		$customer_info = $this->model_account_customer->getCustomerByEmail( $this->request->get['email'] );
		//if can not find
		if(!$customer_info){
			$this->redirect($this->html->getSecureURL('account/create'));
		}
		$customer_id = $customer_info['customer_id'];
		$email = $customer_info['email'];

		$this->loadLanguage('mail/account_create');
		$subject = sprintf($this->language->get('text_subject'), $this->config->get('store_name'));
		$message = sprintf($this->language->get('text_welcome'), $this->config->get('store_name')) . "\n\n";

		$code = md5(mt_rand(1,3000));

		$this->session->data['activation'] = array(
				'customer_id' => $customer_id,
				'code' => $code,
				'email' => $email);

		$message .= sprintf($this->language->get('text_activate'), "\n" . $this->html->getSecureURL('account/login', '&activation=' . $code . '&email=' . $email)) . "\n";

		$message .= $this->language->get('text_thanks') . "\n";
		$message .= $this->config->get('store_name');

		$mail = new AMail($this->config);
		$mail->setTo( $email );
		$mail->setFrom($this->config->get('store_main_email'));
		$mail->setSender($this->config->get('store_name'));
		$mail->setSubject($subject);
		$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
		$mail->send();

		$this->extensions->hk_UpdateData($this, __FUNCTION__);
		$this->redirect($this->html->getSecureURL('account/success'));
	}
}
