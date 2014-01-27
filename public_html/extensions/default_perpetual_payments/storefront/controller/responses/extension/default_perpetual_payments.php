<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

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

class ControllerResponsesExtensionDefaultPerpetualPayments extends AController {
	public function main() {
    	$this->loadLanguage('default_perpetual_payments/default_perpetual_payments');
		
		$template_data['text_credit_card'] = $this->language->get('text_credit_card');
		$template_data['text_start_date'] = $this->language->get('text_start_date');
		$template_data['text_issue'] = $this->language->get('text_issue');
		$template_data['text_wait'] = $this->language->get('text_wait');
		
		$template_data['entry_cc_number'] = $this->language->get('entry_cc_number');
		$template_data['entry_cc_start_date'] = $this->language->get('entry_cc_start_date');
		$template_data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');
		$template_data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');
		$template_data['entry_cc_issue'] = $this->language->get('entry_cc_issue');
		
		$template_data['button_confirm'] = $this->language->get('button_confirm');
		$template_data['button_back'] = $this->language->get('button_back');
	
		$template_data['months'] = array();
		
		for ($i = 1; $i <= 12; $i++) {
			$template_data['months'][] = array(
				'text'  => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)), 
				'value' => sprintf('%02d', $i)
			);
		}
		
		$today = getdate();
		
		$template_data['year_valid'] = array();
		
		for ($i = $today['year'] - 10; $i < $today['year'] + 1; $i++) {	
			$template_data['year_valid'][] = array(
				'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)), 
				'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
			);
		}

		$template_data['year_expire'] = array();

		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
			$template_data['year_expire'][] = array(
				'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
				'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)) 
			);
		}

		if ($this->request->get['rt'] != 'checkout/guest_step_3') {
			$template_data['back'] = $this->html->getSecureURL('checkout/payment');
		} else {
			$template_data['back'] = $this->html->getSecureURL('checkout/guest_step_2');
		}

		$this->view->batchAssign( $template_data );
		$this->processTemplate('responses/default_perpetual_payments.tpl' );
	}

	public function send() {
		$this->loadLanguage('default_perpetual_payments/default_perpetual_payments');
		
		$this->load->model('checkout/order');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$payment_data = array(
			'auth_id'        => $this->config->get('default_perpetual_payments_auth_id'),
			'auth_pass'      => $this->config->get('default_perpetual_payments_auth_pass'),
			'card_num'       => str_replace(' ', '', $this->request->post['cc_number']),
			'card_cvv'       => $this->request->post['cc_cvv2'],
			'card_start'     => $this->request->post['cc_start_date_month'] . substr($this->request->post['cc_start_date_year'], 2),
			'card_expiry'    => $this->request->post['cc_expire_date_month'] . substr($this->request->post['cc_expire_date_year'], 2),
			'cust_name'      => $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'],
			'cust_address'   => $order_info['payment_address_1'] . ' ' . $order_info['payment_city'],
			'cust_country'   => $order_info['payment_iso_code_2'],
			'cust_postcode'	 => $order_info['payment_postcode'],
			'cust_tel'	 	 => $order_info['telephone'],
			'cust_ip'        => $this->request->server['REMOTE_ADDR'],
			'cust_email'     => $order_info['email'],
			'tran_ref'       => $order_info['order_id'],
			'tran_amount'    => $this->currency->format($order_info['total'], $order_info['currency'], 1.00000, FALSE),
			'tran_currency' => $order_info['currency'],
			'tran_testmode' => $this->config->get('default_perpetual_payments_test'),
			'tran_type'     => 'Sale',
			'tran_class'    => 'MoTo',
		);

		$curl = curl_init('https://secure.voice-pay.com/gateway/remote');
		
		curl_setopt($curl, CURLOPT_PORT, 443);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($payment_data));

		$response = curl_exec($curl);
 		
		curl_close($curl);
		
		if ($response) {
			$data = explode('|', $response);
			
			if (isset($data[0]) && $data[0] == 'A') {
				$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('config_order_status_id'));
				
				$message = '';
				
				if (isset($data[1])) {
					$message .= $this->language->get('text_transaction') . ' ' . $data[1] . "\n";
				}
				
				if (isset($data[2])) {
					if ($data[2] == '232') {
						$message .= $this->language->get('text_avs') . ' ' . $this->language->get('text_avs_full_match') . "\n";
					} elseif ($data[2] == '400') {
						$message .= $this->language->get('text_avs') . ' ' . $this->language->get('text_avs_not_match') . "\n";
					}
				}
				
				if (isset($data[3])) {
					$message .= $this->language->get('text_authorisation') . ' ' . $data[3] . "\n";
				}
				
				$this->model_checkout_order->update($this->session->data['order_id'], $this->config->get('default_perpetual_payments_order_status_id'), $message, FALSE);
					
				$json['success'] = $this->html->getSecureURL('checkout/success');
			} else {
				$json['error'] = end($data);
			}
		}
		
		$this->load->library('json');
		$this->response->setOutput(AJson::encode($json));
	}
}