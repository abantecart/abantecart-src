<?php
/*------------------------------------------------------------------------------
   $Id$

   AbanteCart, Ideal OpenSource Ecommerce Solution
   http://www.AbanteCart.com

   Copyright © 2011-2013 Belavier Commerce LLC

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
			'RETURNURL'		 => $this->html->getSecureURL('r/extension/default_pp_pro/callback'),
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