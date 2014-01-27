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

class ControllerResponsesExtensionDefaultSagepay extends AController {
	public function main() {
		$this->loadLanguage('default_sagepay/default_sagepay');
				
		if ($this->config->get('default_sagepay_test') == 'live') {
    		$template_data['action'] = 'https://live.sagepay.com/gateway/service/vspform-register.vsp';
		} elseif ($this->config->get('default_sagepay_test') == 'test') {
			$template_data['action'] = 'https://test.sagepay.com/gateway/service/vspform-register.vsp';		
		} elseif ($this->config->get('default_sagepay_test') == 'sim') {
    		$template_data['action'] = 'https://test.sagepay.com/simulator/vspformgateway.asp';
  		} else {
  			$template_data['action'] = 'https://test.sagepay.com/simulator/vspformgateway.asp';
  		}
		
		$vendor = $this->config->get('default_sagepay_vendor');
		$password = $this->config->get('default_sagepay_password');		
		
		$this->load->model('checkout/order');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		$data = array();
		
		$data['VendorTxCode'] = $this->session->data['order_id'];
		$data['ReferrerID'] = 'E511AF91-E4A0-42DE-80B0-09C981A3FB61';
		$data['Amount'] = $this->currency->format($order_info['total'], $order_info['currency'], $order_info['value'], FALSE);
		$data['Currency'] = $order_info['currency'];
		$data['Description'] = sprintf($this->language->get('text_description'), date($this->language->get('date_format_short')), $this->session->data['order_id']);
		$data['SuccessURL'] = $this->html->getSecureURL('payment/sagepay/success', 'order_id=' . $this->session->data['order_id']);
		
		if ($this->request->get['rt'] != 'checkout/guest_step_3') {
			$data['FailureURL'] = $this->html->getSecureURL('checkout/payment');
		} else {
			$data['FailureURL'] = $this->html->getSecureURL('checkout/guest_step_2');
		}
		
		$data['CustomerName'] = html_entity_decode($order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
		$data['SendEMail'] = '1';
		$data['CustomerEMail'] = $order_info['email'];
		$data['VendorEMail'] = $this->config->get('store_main_email');  
		
		$data['BillingFirstnames'] = $order_info['payment_firstname'];
        $data['BillingSurname'] = $order_info['payment_lastname'];
        $data['BillingAddress1'] = $order_info['payment_address_1'];
		
		if ($order_info['payment_address_2']) {
        	$data['BillingAddress2'] = $order_info['payment_address_2'];
		}
		
		$data['BillingCity'] = $order_info['payment_city'];
       	$data['BillingPostCode'] = $order_info['payment_postcode'];	
        $data['BillingCountry'] = $order_info['payment_iso_code_2'];
		
		if ($order_info['payment_iso_code_2'] == 'US') {
			$data['BillingState'] = $order_info['payment_zone_code'];
		}
		
		$data['BillingPhone'] = $order_info['telephone'];
		
		if ($this->cart->hasShipping()) {
			$data['DeliveryFirstnames'] = $order_info['shipping_firstname'];
        	$data['DeliverySurname'] = $order_info['shipping_lastname'];
        	$data['DeliveryAddress1'] = $order_info['shipping_address_1'];
		
			if ($order_info['shipping_address_2']) {
        		$data['DeliveryAddress2'] = $order_info['shipping_address_2'];
			}
		
        	$data['DeliveryCity'] = $order_info['shipping_city'];
        	$data['DeliveryPostCode'] = $order_info['shipping_postcode'];
        	$data['DeliveryCountry'] = $order_info['shipping_iso_code_2'];
		
			if ($order_info['shipping_iso_code_2'] == 'US') {
				$data['DeliveryState'] = $order_info['shipping_zone_code'];
			}
		
			$data['DeliveryPhone'] = $order_info['telephone'];
		} else {
			$data['DeliveryFirstnames'] = $order_info['payment_firstname'];
        	$data['DeliverySurname'] = $order_info['payment_lastname'];
        	$data['DeliveryAddress1'] = $order_info['payment_address_1'];
		
			if ($order_info['payment_address_2']) {
        		$data['DeliveryAddress2'] = $order_info['payment_address_2'];
			}
		
        	$data['DeliveryCity'] = $order_info['payment_city'];
        	$data['DeliveryPostCode'] = $order_info['payment_postcode'];
        	$data['DeliveryCountry'] = $order_info['payment_iso_code_2'];
		
			if ($order_info['$payment_iso_code_2'] == 'US') {
				$data['DeliveryState'] = $order_info['payment_zone_code'];
			}
		
			$data['DeliveryPhone'] = $order_info['telephone'];			
		}
		
		$data['AllowGiftAid'] = '0';
		
		if (!$this->config->get('default_sagepay_transaction')) {
			$data['ApplyAVSCV2'] = '0';
		}
		
 		$data['Apply3DSecure'] = '0';
		
		$template_data['transaction'] = $this->config->get('default_sagepay_transaction');
		$template_data['vendor'] = $vendor;
		
		$crypt_data = array();
   
		foreach($data as $key => $value){
   			$crypt_data[] = $key . '=' . $value;
		}

		$template_data['crypt'] = base64_encode($this->simpleXor(implode('&', $crypt_data), $password));
		
		if ($this->request->get['rt'] != 'checkout/guest_step_3') {
			$template_data['back'] = $this->html->getSecureURL('checkout/payment');
		} else {
			$template_data['back'] = $this->html->getSecureURL('checkout/guest_step_2');
		}
		
		$this->view->batchAssign( $template_data );
		$this->processTemplate('responses/default_sagepay.tpl' );
	}
	
	public function success() {
		if (isset($this->request->get['crypt'])) {
			$string = base64_decode(str_replace(' ', '+', $this->request->get['crypt']));
			$password = $this->config->get('default_sagepay_password');	

			$stdout = $this->simpleXor($string, $password);
			
			$data = $this->getToken($stdout);
		
			if ($data) {
				$this->load->model('checkout/order');
		
				$this->model_checkout_order->confirm($this->request->get['order_id'], $this->config->get('default_sagepay_order_status_id'));

				$message = '';
		
				if (isset($data['VPSTxId'])) { 
					$message .= 'VPSTxId: ' . $data['VPSTxId'] . "\n";
				}

				if (isset($data['TxAuthNo'])) {
					$message .= 'TxAuthNo: ' . $data['TxAuthNo'] . "\n";
				}

				if (isset($data['AVSCV2'])) {
					$message .= 'AVSCV2: ' . $data['AVSCV2'] . "\n";
				}

				if (isset($data['AddressResult'])) {
					$message .= 'AddressResult: ' . $data['AddressResult'] . "\n";
				}
				
				if (isset($data['PostCodeResult'])) {
					$message .= 'PostCodeResult: ' . $data['PostCodeResult'] . "\n";
				}
				
				if (isset($data['CV2Result'])) {
					$message .= 'CV2Result: ' . $data['CV2Result'] . "\n";
				}

				if (isset($data['3DSecureStatus'])) {
					$message .= '3DSecureStatus: ' . $data['3DSecureStatus'] . "\n";
				}

				if (isset($data['CAVV'])) {
					$message .= 'CAVV: ' . $data['CAVV'] . "\n";
				}
				
				if (isset($data['CardType'])) {
					$message .= 'CardType: ' . $data['CardType'] . "\n";
				}
				
				if (isset($data['Last4Digits'])) {
					$message .= 'Last4Digits: ' . $data['Last4Digits'] . "\n";
				}
				
				$this->model_checkout_order->update($this->request->get['order_id'], $this->config->get('default_sagepay_order_status_id'), $message, FALSE);

				$this->redirect($this->html->getURL('checkout/success'));
			}
		}
	}	 
	
	private function simpleXor($string, $password) {
		$data = array();

		for ($i = 0; $i < strlen($password); $i++) {
			$data[$i] = ord(substr($password, $i, 1));
		}

		$stdout = '';

		$passLen = ( strlen($password) > 0 ) ? strlen($password) : 1; //fixed division on zero

		for ($i = 0; $i < strlen($string); $i++) {
    		$stdout .= chr(ord(substr($string, $i, 1)) ^ ($data[$i % $passLen]));
		}

		return $stdout;		
	}
	
	private function getToken($string) {
  		$tokens = array(
   			'Status',
    		'StatusDetail',
    		'VendorTxCode',
   			'VPSTxId',
    		'TxAuthNo',
    		'Amount',
   			'AVSCV2', 
    		'AddressResult', 
    		'PostCodeResult', 
    		'CV2Result', 
    		'GiftAid', 
    		'3DSecureStatus', 
    		'CAVV',
			'AddressStatus',
			'CardType',
			'Last4Digits',
			'PayerStatus',
			'CardType'
		);		
		
  		$stdout = array();
		$data = array();
  
  		for ($i = count($tokens) - 1; $i >= 0; $i--){
    		$start = strpos($string, $tokens[$i]);
    		
			if ($start){
     			$data[$i]['start'] = $start;
     			$data[$i]['token'] = $tokens[$i];
			}
		}
  
		sort($data);
		
		for ($i = 0; $i < count($data); $i++){
			$start = $data[$i]['start'] + strlen($data[$i]['token']) + 1;

			if ($i == (count($data) - 1)) {
				$stdout[$data[$i]['token']] = substr($string, $start);
			} else {
				$length = $data[$i+1]['start'] - $data[$i]['start'] - strlen($data[$i]['token']) - 2;
				
				$stdout[$data[$i]['token']] = substr($string, $start, $length);
			}      

		}
  
		return $stdout;
	}	
}