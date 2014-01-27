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

class ControllerResponsesExtensionDefaultSagepayDirect extends AController {
	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('default_sagepay_direct/default_sagepay_direct');
		
		$template_data['cc_cvv2_help_url'] = $this->html->getURL('r/extension/default_sagepay_direct/cvv2_help');

		$template_data['cards'] = array();

		$template_data['cards'][] = array(
			'text'  => 'Visa', 
			'value' => 'VISA'
		);

		$template_data['cards'][] = array(
			'text'  => 'MasterCard', 
			'value' => 'MC'
		);

		$template_data['cards'][] = array(
			'text'  => 'Visa Delta/Debit', 
			'value' => 'DELTA'
		);
		
		$template_data['cards'][] = array(
			'text'  => 'Solo', 
			'value' => 'SOLO'
		);	
		
		$template_data['cards'][] = array(
			'text'  => 'Maestro', 
			'value' => 'MAESTRO'
		);
		
		$template_data['cards'][] = array(
			'text'  => 'Visa Electron UK Debit', 
			'value' => 'UKE'
		);
		
		$template_data['cards'][] = array(
			'text'  => 'American Express', 
			'value' => 'AMEX'
		);
		
		$template_data['cards'][] = array(
			'text'  => 'Diners Club', 
			'value' => 'DC'
		);
		
		$template_data['cards'][] = array(
			'text'  => 'Japan Credit Bureau', 
			'value' => 'JCB'
		);
		
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

		//init controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->processTemplate('responses/default_sagepay_direct.tpl' );

	}
	
	public function send() {
		if ($this->config->get('default_sagepay_direct_test') == 'live') {
    		$url = 'https://live.sagepay.com/gateway/service/vspdirect-register.vsp';
		} elseif ($this->config->get('default_sagepay_direct_test') == 'test') {
			$url = 'https://test.sagepay.com/gateway/service/vspdirect-register.vsp';		
		} elseif ($this->config->get('default_sagepay_direct_test') == 'sim') {
    		$url = 'https://test.sagepay.com/Simulator/VSPDirectGateway.asp';
  		}

		if ( $this->config->get('store_credit_cards_status') ) {
			if ( has_value($this->session->data['stored_credit_card']) ) {

				foreach ( $this->session->data['stored_credit_card'] as $key => $val ) {
					$this->request->post[$key] = $val;
				}
				unset($this->session->data['stored_credit_card']);
			}

			if ( $this->request->post['credit_card_save'] ) {

				$data = array(
					'card_nickname' => $this->request->post['cc_nickname'],
					'card_owner' => $this->request->post['cc_owner'],
					'card_number' => $this->request->post['cc_number'],
					'cc_start_date_month' => isset($this->request->post['cc_start_date_month']) ? $this->request->post['cc_start_date_month'] : date('m'),
					'cc_start_date_year' => isset($this->request->post['cc_start_date_year']) ? $this->request->post['cc_start_date_year'] : date('Y'),
					'cc_expire_date_month' => $this->request->post['cc_expire_date_month'],
					'cc_expire_date_year' => $this->request->post['cc_expire_date_year'],
				);

				$this->loadModel('extension/store_credit_cards');
				$this->model_extension_store_credit_cards->addCard($data);
			}
		}

		$this->load->model('checkout/order');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
        $data = array();
		
		$data['VPSProtocol'] = '2.23';
        $data['ReferrerID'] = 'E511AF91-E4A0-42DE-80B0-09C981A3FB61';
        $data['Vendor'] = $this->config->get('default_sagepay_direct_vendor');
		$data['VendorTxCode'] = $this->session->data['order_id'];
		$data['Amount'] = $this->currency->format($order_info['total'], $order_info['currency'], 1.00000, FALSE);
		$data['Currency'] = $this->currency->getCode();
		$data['Description'] = substr($this->config->get('store_name'), 0, 100);
		$data['CardHolder'] = $this->request->post['cc_owner'];
		$data['CardNumber'] = $this->request->post['cc_number'];
		$data['ExpiryDate'] = $this->request->post['cc_expire_date_month'] . substr($this->request->post['cc_expire_date_year'], 2);
		$data['CardType'] = $this->request->post['cc_type'];
		$data['TxType'] = $this->config->get('default_sagepay_direct_transaction');
		$data['StartDate'] = $this->request->post['cc_start_date_month'] . substr($this->request->post['cc_start_date_year'], 2);
		$data['IssueNumber'] = $this->request->post['cc_issue'];
		$data['CV2'] = $this->request->post['cc_cvv2'];
		
		$data['BillingSurname'] = substr($order_info['payment_lastname'], 0, 20);
		$data['BillingFirstnames'] = substr($order_info['payment_firstname'], 0, 20);
		$data['BillingAddress1'] = substr($order_info['payment_address_1'], 0, 100);
		
		if ($order_info['payment_address_2']) {
        	$data['BillingAddress2'] = $order_info['payment_address_2'];
		}
		
		$data['BillingCity'] = substr($order_info['payment_city'], 0, 40);
		$data['BillingPostCode'] = substr($order_info['payment_postcode'], 0, 10);
		$data['BillingCountry'] = $order_info['payment_iso_code_2'];

		if ($order_info['payment_iso_code_2'] == 'US') {
			$data['BillingState'] = $order_info['payment_zone_code'];
		}
		
		$data['BillingPhone'] = substr($order_info['telephone'], 0, 20);
		
		if ($this->cart->hasShipping()) {
			$data['DeliverySurname'] = substr($order_info['shipping_lastname'], 0, 20);
			$data['DeliveryFirstnames'] = substr($order_info['shipping_firstname'], 0, 20);
			$data['DeliveryAddress1'] = substr($order_info['shipping_address_1'], 0, 100);
			
			if ($order_info['shipping_address_2']) {
        		$data['DeliveryAddress2'] = $order_info['shipping_address_2'];
			}		
			
			$data['DeliveryCity'] = substr($order_info['shipping_city'], 0, 40);
			$data['DeliveryPostCode'] = substr($order_info['shipping_postcode'], 0, 10);
			$data['DeliveryCountry'] = $order_info['shipping_iso_code_2'];
			
			if ($order_info['shipping_iso_code_2'] == 'US') {
				$data['DeliveryState'] = $order_info['shipping_zone_code'];
			}
			
			$data['CustomerName'] = substr($order_info['firstname'] . ' ' . $order_info['lastname'], 0, 100);
			$data['DeliveryPhone'] = substr($order_info['telephone'], 0, 20);
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
		
			if ($order_info['payment_iso_code_2'] == 'US') {
				$data['DeliveryState'] = $order_info['payment_zone_code'];
			}
		
			$data['DeliveryPhone'] = $order_info['telephone'];			
		}		
		
		$data['CustomerEMail'] = substr($order_info['email'], 0, 255);
		$data['Apply3DSecure'] = '0';
		$data['ClientIPAddress'] = $this->request->server['REMOTE_ADDR'];
		
		$curl = curl_init($url);

		curl_setopt($curl, CURLOPT_PORT, 443);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
 
		$response = curl_exec($curl);
  		
		curl_close($curl);

		$data = array();

		$response_data = explode("\n", $response);

		foreach ($response_data as $string) {
			if (strpos($string, '=')) {
				$parts = explode('=', $string, 2);
				
				$data[trim($parts[0])] = trim($parts[1]);
			}
		}
		
		$json = array();
      
		if ($data['Status'] == '3DAUTH') {
			$json['ACSURL'] = $data['ACSURL'];
			$json['MD'] = $data['MD'];
			$json['PaReq'] = $data['PAReq'];
			$json['TermUrl'] = $this->html->getSecureURL('extension/sagepay_direct/callback');
		} elseif ($data['Status'] == 'OK' || $data['Status'] == 'AUTHENTICATED' || $data['Status'] == 'REGISTERED') {
			$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('config_order_status_id'));
			
			$message = '';
			
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
			
			$this->model_checkout_order->update($this->session->data['order_id'], $this->config->get('default_sagepay_direct_order_status_id'), $message, FALSE);

			$json['success'] = $this->html->getSecureURL('checkout/success'); 			
		} else {
			$json['error'] = 'Status: INVALID.';
		}
		
		$this->load->library('json');
		$this->response->setOutput(AJson::encode($json));
	}	 
	
	public function callback() {
		if (isset($this->session->data['order_id'])) {
			if ($this->config->get('default_sagepay_direct_test') == 'live') {
				$url = 'https://live.sagepay.com/gateway/service/direct3dcallback.vsp';
			} elseif ($this->config->get('default_sagepay_direct_test') == 'test') {
				$url = 'https://test.sagepay.com/gateway/service/direct3dcallback.vsp';		
			} elseif ($this->config->get('default_sagepay_direct_test') == 'sim') {
				$url = 'https://test.sagepay.com/Simulator/VSPDirectCallback.asp';
			} 	
			
			$curl = curl_init($url);
	
			curl_setopt($curl, CURLOPT_PORT, 443);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($this->request->post));
	
			$response = curl_exec($curl);
			
			curl_close($curl);
			
			$data = array();
			
			$response_data = explode(chr(10), $response);
	
			foreach ($response_data as $string) {
				if (strpos($string, '=')) {
					$parts = explode('=', $string, 2);
					
					$data[trim($parts[0])] = trim($parts[1]);
				}
			}
			
			if ($data['Status'] == 'OK' || $data['Status'] == 'AUTHENTICATED' || $data['Status'] == 'REGISTERED') {
				$this->load->model('checkout/order');
				
				$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('config_order_status_id'));
				
				$message = '';
				
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
				
				$this->model_checkout_order->update($this->session->data['order_id'], $this->config->get('default_sagepay_direct_order_status_id'), $message, FALSE);	
				
				$this->redirect($this->html->getSecureURL('checkout/success'));
			} else {
				$this->session->data['error'] = $data['StatusDetail'];

				if ($this->request->get['rt'] != 'checkout/guest_step_3') {
					$this->redirect($this->html->getSecureURL('checkout/payment'));
				} else {
					$this->redirect($this->html->getSecureURL('checkout/guest_step_2'));
				}
			}
		} else {
			$this->redirect($this->html->getSecureURL('account/login'));
		}
	}

	public function cvv2_help() {
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('default_sagepay_direct/default_sagepay_direct');

		$image = '<img src="' . $this->view->templateResource('/image/securitycode.jpg') . '" alt="' . $this->language->get('entry_what_cvv2') . '" />';

		$this->view->assign('title', $this->language->get('entry_what_cvv2') );
		$this->view->assign('description', $image );

		//init controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->processTemplate('responses/content/content.tpl' );
	}
}
