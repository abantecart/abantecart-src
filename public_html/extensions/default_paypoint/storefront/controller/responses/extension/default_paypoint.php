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

class ControllerResponsesExtensionDefaultPaypoint extends AController {
	public function main() {
    	$template_data['button_confirm'] = $this->language->get('button_confirm');
		$template_data['button_back'] = $this->language->get('button_back');

		$this->load->model('checkout/order');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		$template_data['action'] = 'https://www.secpay.com/java-bin/ValCard';
		
		$template_data['merchant'] = $this->config->get('default_paypoint_merchant');
		$template_data['trans_id'] = $this->session->data['order_id'];
		$template_data['amount'] = $this->currency->format($order_info['total'], $order_info['currency'], $order_info['value'], FALSE);
		$template_data['bill_name'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
		$template_data['bill_addr_1'] = $order_info['payment_address_1'];
		$template_data['bill_addr_2'] = $order_info['payment_address_2'];
		$template_data['bill_city'] = $order_info['payment_city'];
		$template_data['bill_state'] = $order_info['payment_zone'];
		$template_data['bill_post_code'] = $order_info['payment_postcode'];
		$template_data['bill_country'] = $order_info['payment_country'];
		$template_data['bill_tel'] = $order_info['telephone'];
		$template_data['bill_email'] = $order_info['email'];
			
		if ($this->cart->hasShipping()) {
			$template_data['ship_name'] = $order_info['shipping_firstname'] . ' ' . $order_info['shipping_lastname'];
			$template_data['ship_addr_1'] = $order_info['shipping_address_1'];
			$template_data['ship_addr_2'] = $order_info['shipping_address_2'];
			$template_data['ship_city'] = $order_info['shipping_city'];
			$template_data['ship_state'] = $order_info['shipping_zone'];
			$template_data['ship_post_code'] = $order_info['shipping_postcode'];
			$template_data['ship_country'] = $order_info['shipping_country'];
		} else {
			$template_data['ship_name'] = '';
			$template_data['ship_addr_1'] = '';
			$template_data['ship_addr_2'] = '';
			$template_data['ship_city'] = '';
			$template_data['ship_state'] = '';
			$template_data['ship_post_code'] = '';
			$template_data['ship_country'] = '';			
		}
		
		$template_data['currency'] = $this->currency->getCode();
		$template_data['callback'] = $this->html->getSecureURL('extension/paypoint/callback');
		
		$this->load->library('encryption');
		
		$encryption = new AEncryption($this->config->get('encryption_key'));
		
		$template_data['order_id'] = $encryption->encrypt($this->session->data['order_id']);
		
		switch ($this->config->get('default_paypoint_test')) {
			case 'production':
				$status = 'live';
				break;
			case 'successful':
			default:
				$status = 'true';
				break;
			case 'fail':
				$status = 'false';
				break;
		}
		
		$template_data['options'] = 'test_status=' . $status . ',dups=false,cb_flds=order_id';

		if ($this->request->get['rt'] != 'checkout/guest_step_3') {
			$template_data['back'] = $this->html->getSecureURL('checkout/payment');
		} else {
			$template_data['back'] = $this->html->getSecureURL('checkout/guest_step_2');
		}
		
		$this->view->batchAssign( $template_data );
		$this->processTemplate('responses/default_paypoint.tpl' );
	}
	
	public function callback() {
		$this->loadLanguage('default_paypoint/default_paypoint');
	
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
		$template_data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->html->getSecureURL('checkout/cart'));
	
		if (isset($this->request->get['valid']) && $this->request->get['valid'] == 'true') {
			$this->load->library('encryption');
			
			$encryption = new AEncryption($this->config->get('encryption_key'));
		
			$order_id = $encryption->decrypt($this->request->get['order_id']);
	
			$this->load->model('checkout/order');
	
			$this->model_checkout_order->confirm($order_id, $this->config->get('config_order_status_id'));
	
			$message = '';
	
			if (isset($this->request->get['code'])) {
				$message .= 'code: ' . $this->request->get['code'] . "\n";
			}
		
			if (isset($this->request->get['auth_code'])) {
				$message .= 'auth_code: ' . $this->request->get['auth_code'] . "\n";
			}
		
			if (isset($this->request->get['ip'])) {
				$message .= 'ip: ' . $this->request->get['ip'] . "\n";
			}
		
			if (isset($this->request->get['cv2avs'])) {
				$message .= 'cv2avs: ' . $this->request->get['cv2avs'] . "\n";
			}	
	
			if (isset($this->request->get['trans_id'])) {
				$message .= 'trans_id: ' . $this->request->get['trans_id'] . "\n";
			}	
			
			if (isset($this->request->get['valid'])) {
				$message .= 'valid: ' . $this->request->get['valid'] . "\n";
			}
			
			$this->model_checkout_order->update($order_id, $this->config->get('default_paypoint_order_status_id'), $message, FALSE);	
			$template_data['continue'] = $this->html->getSecureURL('checkout/success');
            
	  		$this->view->batchAssign( $template_data );
            $this->view->setTemplate( 'responses/extension/paypoint_success.tpl' );
		} else {
			$template_data['continue'] = $this->html->getSecureURL('checkout/cart');
            
			$this->view->batchAssign( $template_data );
            $this->view->setTemplate( 'responses/extension/paypoint_failure.tpl' );
		}
        $this->processTemplate();
	}
}