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
if ( !IS_ADMIN || !defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

class ControllerResponsesExtensionDefaultPpPro extends AController {

	public $data = array();

	public function test() {

		$this->loadLanguage('default_pp_pro/default_pp_pro');

		if (!$this->config->get('default_pp_pro_test')) {
			$api_endpoint = 'https://api-3t.paypal.com/nvp';
		} else {
			$api_endpoint = 'https://api-3t.sandbox.paypal.com/nvp';
		}

		$payment_data = array(
			'METHOD'         => 'SetExpressCheckout',
			'VERSION'        => '98.0',
			'USER'           => html_entity_decode($this->config->get('default_pp_pro_username'), ENT_QUOTES, 'UTF-8'),
			'PWD'            => html_entity_decode($this->config->get('default_pp_pro_password'), ENT_QUOTES, 'UTF-8'),
			'SIGNATURE'      => html_entity_decode($this->config->get('default_pp_pro_signature'), ENT_QUOTES, 'UTF-8'),
			'PAYMENTREQUEST_0_PAYMENTACTION'  => 'Sale',
			'PAYMENTREQUEST_0_AMT'            => '10.00',
			'PAYMENTREQUEST_0_CURRENCYCODE'   => 'USD',
			'RETURNURL'		 => $this->html->getCatalogURL('r/extension/default_pp_pro/callback'),
			'CANCELURL'		 => $this->html->getSecureURL('extension/extensions/edit', '&extension=default_pp_pro'),
		);

		$curl = curl_init($api_endpoint);

		curl_setopt($curl, CURLOPT_PORT, 443);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($payment_data));

		$response = curl_exec($curl);
		$curl_error = curl_error($curl);

		curl_close($curl);

		$ec_settings = $this->_parse_http_query($response);

		$json = array();

		if ( empty($response) ) {
			$warning = new AWarning('CURL Error: ' . $curl_error . '. Test mode = ' . $this->config->get('default_pp_pro_test') .'.');
			$warning->toLog()->toDebug();
			$json['message'] = "Connection to PayPal server can not be established.\n" . $curl_error .".\nCheck your server configuration or contact your hosting provider.";
			$json['error'] = true;


		} elseif ( isset($ec_settings['TOKEN']) ) {
			$json['message'] = $this->language->get('text_connection_success');
			$json['error'] = false;
		} else {
			$warning = new AWarning('PayPal Error: ' . $ec_settings['L_LONGMESSAGE0'] . '. Test mode = ' . $this->config->get('default_pp_pro_test') .'.');
			$warning->toLog()->toDebug();
			$test_mode = $this->config->get('default_pp_pro_test') ? 'ON' : 'OFF';
			$json['message'] = 'PayPal Error: ' . $ec_settings['L_LONGMESSAGE0'] . ".\n" . 'Please check your API Credentials and try again.' . "\n" . 'Also please note that Test mode is ' . $test_mode .'!';
			$json['error'] = true;
		}

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($json));

	}

	public function callback() {

	}

	public function capture() {

		$json = array();

		if ( has_value($this->request->get['order_id']) ) {

			$this->loadModel('sale/order');
			$order_info = $this->model_sale_order->getOrder($this->request->get['order_id']);

			$this->loadLanguage('default_pp_pro/default_pp_pro');

			if ( has_value($order_info['payment_method_data']) ) {

				if ( $this->config->get('default_pp_pro_test') ) {
					$api_endpoint = 'https://api-3t.sandbox.paypal.com/nvp';
				} else {
					$api_endpoint = 'https://api-3t.paypal.com/nvp';
				}

				$payment_method_data = unserialize($order_info['payment_method_data']);
				$already_captured = has_value($payment_method_data['captured_amount']) ? (float) $payment_method_data['captured_amount'] : 0;

				$capture_data = array(
					'METHOD'			=> 'DoCapture',
					'VERSION'			=> '98.0',
					'USER'				=> html_entity_decode($this->config->get('default_pp_pro_username'), ENT_QUOTES, 'UTF-8'),
					'PWD'				=> html_entity_decode($this->config->get('default_pp_pro_password'), ENT_QUOTES, 'UTF-8'),
					'SIGNATURE'			=> html_entity_decode($this->config->get('default_pp_pro_signature'), ENT_QUOTES, 'UTF-8'),
					'AUTHORIZATIONID'	=> $payment_method_data['TRANSACTIONID'],
					'AMT'				=> $payment_method_data['AMT'],
					'CURRENCYCODE'		=> $order_info['currency'],
					'COMPLETETYPE'		=> 'Complete'
				);
				//echo_array($capture_data);exit;

				$curl = curl_init($api_endpoint);

				curl_setopt($curl, CURLOPT_PORT, 443);
				curl_setopt($curl, CURLOPT_HEADER, 0);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
				curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($capture_data));

				$response = curl_exec($curl);

				$curl_error = curl_error($curl);

				curl_close($curl);

				$result = $this->_parse_http_query($response);

				if ( $result['ACK'] != 'Success' ) {
					$this->session->data['error'] = $result['L_LONGMESSAGE0'];
				} else {

					$this->loadModel('extension/default_pp_pro');

					$payment_method_data['captured_amount'] = $already_captured + (float) $result['AMT'];
					$payment_method_data['captured_transaction_id'] = $result['TRANSACTIONID'];

					$payment_method_data['PAYMENTINFO_0_PAYMENTSTATUS'] = 'Completed';
					unset($payment_method_data['PAYMENTINFO_0_PENDINGREASON']);

					$this->model_extension_default_pp_pro->updatePaymentMethodData($this->request->get['order_id'], $payment_method_data);
					$this->model_extension_default_pp_pro->addOrderHistory(array(
						'order_id' => $this->request->get['order_id'],
						'order_status_id' => $order_info['order_status_id'],
						'notify' => 0,
						'comment' => $this->currency->format($result['AMT'], $order_info['currency'], $order_info['value']) . ' captured.'
					));

					$this->session->data['success'] = $this->language->get('text_capture_success');

				}


			} else {
				// no payment method data, funds can not be captured
				$this->session->data['error'] = $this->language->get('error_no_payment_method_data');
			}
		} else {
			// no order_id
			$this->session->data['error'] = $this->language->get('error_no_order_id');
		}

		$json['href'] = $this->html->getSecureURL('sale/order/payment_details', '&order_id=' . (int) $this->request->get['order_id'].'&extension=default_pp_pro');
		$this->load->library('json');
		$this->response->setOutput(AJson::encode($json));

	}

	public function refund() {

		$this->loadLanguage('default_pp_pro/default_pp_pro');

		$json = array();

		if ( has_value($this->request->get['order_id']) ) {

			$amount = (float) $this->request->get['amount'];

			if ( $amount > 0 ) {

				$this->loadModel('sale/order');
				$order_info = $this->model_sale_order->getOrder($this->request->get['order_id']);

				if ( has_value($order_info['payment_method_data']) ) {

					if ( $this->config->get('default_pp_pro_test') ) {
						$api_endpoint = 'https://api-3t.sandbox.paypal.com/nvp';
					} else {
						$api_endpoint = 'https://api-3t.paypal.com/nvp';
					}

					$payment_method_data = unserialize($order_info['payment_method_data']);

					$already_refunded = has_value($payment_method_data['refunded_amount']) ? (float) $payment_method_data['refunded_amount'] : 0;

					if ( $this->request->get['refund_captured'] ) {
						$payment_amount = (float) $payment_method_data['captured_amount'];
						$trasaction_id = $payment_method_data['captured_transaction_id'];
					} else {
						$payment_amount = (float) $payment_method_data['AMT'];
						$trasaction_id = $payment_method_data['TRANSACTIONID'];
					}

					if ( $amount > $payment_amount ) {
						// Amount limit exceeded
						$this->session->data['error'] = $this->language->get('error_amount_exceeded');

					} else {

						$capture_data = array(
							'METHOD'			=> 'RefundTransaction',
							'VERSION'			=> '98.0',
							'USER'				=> html_entity_decode($this->config->get('default_pp_pro_username'), ENT_QUOTES, 'UTF-8'),
							'PWD'				=> html_entity_decode($this->config->get('default_pp_pro_password'), ENT_QUOTES, 'UTF-8'),
							'SIGNATURE'			=> html_entity_decode($this->config->get('default_pp_pro_signature'), ENT_QUOTES, 'UTF-8'),
							'TRANSACTIONID'		=> $trasaction_id,
						);

						if ( $amount == $payment_amount ) {
							$capture_data['REFUNDTYPE'] = 'Full';
						} else {
							$capture_data['REFUNDTYPE'] = 'Partial';
							$capture_data['AMT'] = $amount;
						}



						$curl = curl_init($api_endpoint);

						curl_setopt($curl, CURLOPT_PORT, 443);
						curl_setopt($curl, CURLOPT_HEADER, 0);
						curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
						curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
						curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
						curl_setopt($curl, CURLOPT_POST, 1);
						curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($capture_data));

						$response = curl_exec($curl);

						$curl_error = curl_error($curl);

						curl_close($curl);

						$result = $this->_parse_http_query($response);

						if ( $result['ACK'] != 'Success' ) {
							$this->session->data['error'] = $result['L_LONGMESSAGE0'];
						} else {
							// update order_totals
							$this->loadModel('extension/default_pp_pro');
							$this->model_extension_default_pp_pro->processRefund(array(
								'order_id' => $this->request->get['order_id'],
								'amount' => $amount,
								'currency' => $order_info['currency']
							));

							$payment_method_data['refunded_amount'] = $already_refunded + (float) $result['GROSSREFUNDAMT'];

							$this->model_extension_default_pp_pro->updatePaymentMethodData($this->request->get['order_id'], $payment_method_data);
							$this->model_extension_default_pp_pro->addOrderHistory(array(
								'order_id' => $this->request->get['order_id'],
								'order_status_id' => $order_info['order_status_id'],
								'notify' => 0,
								'comment' => $this->currency->format($result['GROSSREFUNDAMT'], $order_info['currency'], $order_info['value']) . ' refunded.'
							));

							$this->session->data['success'] = $this->language->get('text_refund_success');
						}
					}

				} else {
					// no payment method data, funds can not be captured
					$this->session->data['error'] = $this->language->get('error_no_payment_method_data');
				}


			} else {
				// no amount
				$this->session->data['error'] = $this->language->get('error_empty_amount');
			}

		} else {
			// no order_id
			$this->session->data['error'] = $this->language->get('error_no_order_id');
		}

		$json['href'] = $this->html->getSecureURL('sale/order/payment_details', '&order_id=' . (int) $this->request->get['order_id'].'&extension=default_pp_pro');

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($json));
	}

	private function _parse_http_query($query) {

		$parts = explode('&', $query);

		$results = array();
		foreach ( $parts as $part ) {
			$item = explode('=', $part);
			$results[$item[0]] = urldecode($item[1]);
		}

		return $results;
	}
}