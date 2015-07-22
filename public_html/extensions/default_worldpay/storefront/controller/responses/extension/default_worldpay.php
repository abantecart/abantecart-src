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
if ( !defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

class ControllerResponsesExtensionDefaultWorldPay extends AController {
	public function main() {
		$this->loadLanguage('default_worldpay/default_worldpay');
    	$template_data['button_confirm'] = $this->language->get('button_confirm');
		$template_data['button_back'] = $this->language->get('button_back');

		$this->load->model('checkout/order');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		$this->load->library('encryption');
		
		$template_data['action'] = 'https://select.worldpay.com/wcc/purchase';

		$template_data['merchant'] = $this->config->get('default_worldpay_merchant');
		$template_data['order_id'] = $order_info['order_id'];
		$template_data['amount'] = $this->currency->format($order_info['total'], $order_info['currency'], $order_info['value'], FALSE);
		$template_data['currency'] = $order_info['currency'];
		$template_data['description'] = $this->config->get('store_name') . ' - #' . $order_info['order_id'];
		$template_data['name'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
		
		if (!$order_info['payment_address_2']) {
			$template_data['address'] = $order_info['payment_address_1'] . ', ' . $order_info['payment_city'] . ', ' . $order_info['payment_zone'];
		} else {
			$template_data['address'] = $order_info['payment_address_1'] . ', ' . $order_info['payment_address_2'] . ', ' . $order_info['payment_city'] . ', ' . $order_info['payment_zone'];
		}
		
		$template_data['postcode'] = $order_info['payment_postcode'];
		$template_data['country'] = $order_info['payment_iso_code_2'];
		$template_data['telephone'] = $order_info['telephone'];
		$template_data['email'] = $order_info['email'];
		$template_data['test'] = $this->config->get('default_worldpay_test');
		
		if ($this->request->get['rt'] != 'checkout/guest_step_3') {
			$template_data['back'] = $this->html->getSecureURL('checkout/payment');
		} else {
			$template_data['back'] = $this->html->getSecureURL('checkout/guest_step_2');
		}
		
		$this->view->batchAssign( $template_data );
		$this->processTemplate('responses/default_worldpay.tpl' );
	}
	
	public function callback() {
		if (isset($this->request->post['callbackPW']) && ($this->request->post['callbackPW'] == $this->config->get('default_worldpay_password'))) {
			$this->loadLanguage('default_worldpay/default_worldpay');
		
			$template_data['title'] = sprintf($this->language->get('heading_title'), $this->config->get('store_name'));

			if (!isset($this->request->server['HTTPS']) || ($this->request->server['HTTPS'] != 'on')) {
				$template_data['base'] = HTTP_SERVER;
			} else {
				$template_data['base'] = HTTPS_SERVER;
			}
		
			$template_data['charset'] = 'utf-8';
			$template_data['language'] = $this->language->get('code');
			$template_data['direction'] = $this->language->get('direction');
		
			$template_data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('store_name'));
			
			$template_data['text_response'] = $this->language->get('text_response');
			$template_data['text_success'] = $this->language->get('text_success');
			$template_data['text_success_wait'] = sprintf($this->language->get('text_success_wait'), $this->html->getSecureURL('checkout/success'));
			$template_data['text_failure'] = $this->language->get('text_failure');
			
			if ($this->request->get['rt'] != 'checkout/guest_step_3') {
				$template_data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->html->getSecureURL('checkout/payment'));
			} else {
				$template_data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->html->getSecureURL('checkout/guest_step_2'));
			}
		
			if (isset($this->request->post['transStatus']) && $this->request->post['transStatus'] == 'Y') { 
				$this->load->model('checkout/order');

				$this->model_checkout_order->confirm($this->request->post['cartId'], $this->config->get('default_worldpay_order_status_id'));
		
				$message = '';

				if (isset($this->request->post['transId'])) {
					$message .= 'transId: ' . $this->request->post['transId'] . "\n";
				}
			
				if (isset($this->request->post['transStatus'])) {
					$message .= 'transStatus: ' . $this->request->post['transStatus'] . "\n";
				}
			
				if (isset($this->request->post['countryMatch'])) {
					$message .= 'countryMatch: ' . $this->request->post['countryMatch'] . "\n";
				}
			
				if (isset($this->request->post['AVS'])) {
					$message .= 'AVS: ' . $this->request->post['AVS'] . "\n";
				}	

				if (isset($this->request->post['rawAuthCode'])) {
					$message .= 'rawAuthCode: ' . $this->request->post['rawAuthCode'] . "\n";
				}	

				if (isset($this->request->post['authMode'])) {
					$message .= 'authMode: ' . $this->request->post['authMode'] . "\n";
				}	

				if (isset($this->request->post['rawAuthMessage'])) {
					$message .= 'rawAuthMessage: ' . $this->request->post['rawAuthMessage'] . "\n";
				}	
			
				if (isset($this->request->post['wafMerchMessage'])) {
					$message .= 'wafMerchMessage: ' . $this->request->post['wafMerchMessage'] . "\n";
				}				

				$this->model_checkout_order->update($this->request->post['cartId'], $this->config->get('default_worldpay_order_status_id'), $message, FALSE);
				$template_data['continue'] = $this->html->getSecureURL('checkout/success');
				$this->template = 'extension/default_worldpay_success.tpl';

			} else {
    			$template_data['continue'] = $this->html->getSecureURL('checkout/cart');
				$this->template = 'extension/default_worldpay_failure.tpl';
			}

			$view = new AView($this->registry, 0);
			$view->batchAssign($template_data);
			$html = $view->fetch($this->template);
			$this->response->setOutput($html);
		}
	}
}