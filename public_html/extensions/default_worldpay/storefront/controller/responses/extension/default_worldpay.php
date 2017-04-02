<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2017 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Lincence details is bundled with this package in the file LICENSE.txt.
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

class ControllerResponsesExtensionDefaultWorldPay extends AController{
	public $data = array ();

	public function main(){
		$this->loadLanguage('default_worldpay/default_worldpay');
		$this->data['button_confirm'] = $this->language->get('button_confirm');
		$this->data['button_back'] = $this->language->get('button_back');

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$this->load->library('encryption');

		$this->data['action'] = 'https://select.worldpay.com/wcc/purchase';

		$this->data['merchant'] = $this->config->get('default_worldpay_merchant');
		$this->data['order_id'] = $order_info['order_id'];
		$this->data['amount'] = $this->currency->format($order_info['total'], $order_info['currency'], $order_info['value'], false);
		$this->data['currency'] = $order_info['currency'];
		$this->data['description'] = $this->config->get('store_name') . ' - #' . $order_info['order_id'];
		$this->data['name'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];

		if (!$order_info['payment_address_2']){
			$this->data['address'] = $order_info['payment_address_1'] . ', ' . $order_info['payment_city'] . ', ' . $order_info['payment_zone'];
		} else{
			$this->data['address'] = $order_info['payment_address_1'] . ', ' . $order_info['payment_address_2'] . ', ' . $order_info['payment_city'] . ', ' . $order_info['payment_zone'];
		}

		$this->data['postcode'] = $order_info['payment_postcode'];
		$this->data['country'] = $order_info['payment_iso_code_2'];
		$this->data['telephone'] = $order_info['telephone'];
		$this->data['email'] = $order_info['email'];
		$this->data['test'] = $this->config->get('default_worldpay_test');

		if ($this->request->get['rt'] == 'checkout/guest_step_3'){
			$this->view->assign('back', $this->html->getSecureURL('checkout/guest_step_2', '&mode=edit', true));
		} else{
			$this->view->assign('back', $this->html->getSecureURL('checkout/payment', '&mode=edit', true));
		}

		$this->view->batchAssign($this->data);
		$this->processTemplate('responses/default_worldpay.tpl');
	}

	public function callback(){
		if (isset($this->request->post['callbackPW']) && ($this->request->post['callbackPW'] == $this->config->get('default_worldpay_password'))){
			$this->loadLanguage('default_worldpay/default_worldpay');

			$this->data['title'] = sprintf($this->language->get('heading_title'), $this->config->get('store_name'));

			if (HTTPS === true){
				$this->data['base'] = HTTP_SERVER;
			} else{
				$this->data['base'] = HTTPS_SERVER;
			}

			$this->data['charset'] = 'utf-8';
			$this->data['language'] = $this->language->get('code');
			$this->data['direction'] = $this->language->get('direction');

			$this->data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('store_name'));

			$this->data['text_response'] = $this->language->get('text_response');
			$this->data['text_success'] = $this->language->get('text_success');
			$this->data['text_success_wait'] = sprintf($this->language->get('text_success_wait'), $this->html->getSecureURL('checkout/success'));
			$this->data['text_failure'] = $this->language->get('text_failure');

			if ($this->request->get['rt'] != 'checkout/guest_step_3'){
				$this->data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->html->getSecureURL('checkout/payment'));
			} else{
				$this->data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->html->getSecureURL('checkout/guest_step_2'));
			}

			if (isset($this->request->post['transStatus']) && $this->request->post['transStatus'] == 'Y'){
				$this->load->model('checkout/order');

				$this->model_checkout_order->confirm($this->request->post['cartId'], $this->config->get('default_worldpay_order_status_id'));

				$message = '';

				if (isset($this->request->post['transId'])){
					$message .= 'transId: ' . $this->request->post['transId'] . "\n";
				}

				if (isset($this->request->post['transStatus'])){
					$message .= 'transStatus: ' . $this->request->post['transStatus'] . "\n";
				}

				if (isset($this->request->post['countryMatch'])){
					$message .= 'countryMatch: ' . $this->request->post['countryMatch'] . "\n";
				}

				if (isset($this->request->post['AVS'])){
					$message .= 'AVS: ' . $this->request->post['AVS'] . "\n";
				}

				if (isset($this->request->post['rawAuthCode'])){
					$message .= 'rawAuthCode: ' . $this->request->post['rawAuthCode'] . "\n";
				}

				if (isset($this->request->post['authMode'])){
					$message .= 'authMode: ' . $this->request->post['authMode'] . "\n";
				}

				if (isset($this->request->post['rawAuthMessage'])){
					$message .= 'rawAuthMessage: ' . $this->request->post['rawAuthMessage'] . "\n";
				}

				if (isset($this->request->post['wafMerchMessage'])){
					$message .= 'wafMerchMessage: ' . $this->request->post['wafMerchMessage'] . "\n";
				}

				$this->model_checkout_order->update($this->request->post['cartId'], $this->config->get('default_worldpay_order_status_id'), $message, false);
				$this->data['continue'] = $this->html->getSecureURL('checkout/success');
				$this->template = 'extension/default_worldpay_success.tpl';

			} else{
				$this->data['continue'] = $this->html->getSecureURL('checkout/cart');
				$this->template = 'extension/default_worldpay_failure.tpl';
			}

			$view = new AView($this->registry, 0);
			$view->batchAssign($this->data);
			$html = $view->fetch($this->template);
			$this->response->setOutput($html);
		}
	}
}