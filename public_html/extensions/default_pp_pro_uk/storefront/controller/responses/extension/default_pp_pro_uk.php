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

class ControllerResponsesExtensionDefaultPPProUK extends AController {
	public function main() {
		$this->loadLanguage('default_pp_pro_uk/default_pp_pro_uk');
		 		
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		$data[ 'cc_owner' ] = HtmlElementFactory::create(array(
			'type' => 'input',
			'name' => 'cc_owner',
			'value' => $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'],
			'style' => 'input-medium'
		));
		$data[ 'cc_owner' ] = $data[ 'cc_owner' ]->getHtml();

		$cards = array('Visa' => 'Visa',
					'MasterCard' => 'MasterCard',
					'Maestro' => 'Maestro',
		            //'Discover'=>'Discover',
		           // 'Alex'=>'Alex'
				);
				
        $data[ 'cc_type' ] = HtmlElementFactory::create(
			array( 'type' => 'selectbox',
			     'name' => 'cc_type',
			     'value' => 0,
			     'options' => $cards,
			     'style' => 'short input-small'
			));

        $data[ 'cc_number' ] = HtmlElementFactory::create(array(
			'type' => 'input',
			'name' => 'cc_number',
			'value' => '',
			'style' => 'input-medium'
		));

		$months = array();
		for ($i = 1; $i <= 12; $i++) {
			$months[ sprintf('%02d', $i) ] = strftime('%B', mktime(0, 0, 0, $i, 1, 2000));
		}
		$data[ 'cc_expire_date_month' ] = HtmlElementFactory::create(
			array( 'type' => 'selectbox',
			     'name' => 'cc_expire_date_month',
			     'value' => sprintf('%02d', date('m')),
			     'options' => $months,
			     'style' => 'short input-small'
			));

        $today = getdate();
		$years = array();
		for ($i = $today[ 'year' ]; $i < $today[ 'year' ] + 11; $i++) {
			$years[ strftime('%Y', mktime(0, 0, 0, 1, 1, $i)) ] = strftime('%Y', mktime(0, 0, 0, 1, 1, $i));
		}
		$data[ 'cc_expire_date_year' ] = HtmlElementFactory::create(array( 'type' => 'selectbox',
		                                                                 'name' => 'cc_expire_date_year',
		                                                                 'value' => sprintf('%02d', date('Y') + 1),
		                                                                 'options' => $years,
		                                                                 'style' => 'short input-small' ));

        $data[ 'cc_start_date_month' ] = HtmlElementFactory::create(
			array( 'type' => 'selectbox',
			     'name' => 'cc_start_date_month',
			     'value' => sprintf('%02d', date('m')),
			     'options' => $months,
			     'style' => 'short input-small'
			));

		$years = array();
		for ($i = $today[ 'year' ]-10; $i < $today[ 'year' ] + 2; $i++) {
			$years[ strftime('%Y', mktime(0, 0, 0, 1, 1, $i)) ] = strftime('%Y', mktime(0, 0, 0, 1, 1, $i));
		}
        $data[ 'cc_start_date_year' ] = HtmlElementFactory::create(array( 'type' => 'selectbox',
		                                                                 'name' => 'cc_start_date_year',
		                                                                 'value' => sprintf('%02d', date('Y') ),
		                                                                 'options' => $years,
		                                                                 'style' => 'short input-small' ));

        $data[ 'cc_cvv2' ] = HtmlElementFactory::create(array( 'type' => 'input',
		                                                     'name' => 'cc_cvv2',
		                                                     'value' => '',
		                                                     'style' => 'short',
		                                                     'attr' => ' size="3" '
		                                                ));
        $data[ 'cc_issue' ] = HtmlElementFactory::create(array( 'type' => 'input',
		                                                     'name' => 'cc_issue',
		                                                     'value' => '',
		                                                     'style' => 'short',
		                                                     'attr' => ' size="1" '
		                                                ));

		$back = $this->request->get[ 'rt' ] != 'checkout/guest_step_3' ? $this->html->getSecureURL('checkout/payment')
				: $this->html->getSecureURL('checkout/guest_step_2');
		$data[ 'back' ] = HtmlElementFactory::create(array( 'type' => 'button',
		                                                  'name' => 'back',
		                                                  'text' => $this->language->get('button_back'),
		                                                  'style' => 'button',
		                                                  'href' => $back ));

		$data[ 'submit' ] = HtmlElementFactory::create(array( 'type' => 'button',
			                                                  'name' => 'paypal_button',
		                                                      'text' => $this->language->get('button_confirm'),
			                                                  'style' => 'button btn-orange',
		                                               ));
		$this->view->batchAssign( $data );
		$this->processTemplate('responses/default_pp_pro_uk.tpl' );
	}

	public function send() {
		$this->loadLanguage('default_pp_pro_uk/default_pp_pro_uk');
		
		if (!$this->config->get('default_pp_pro_uk_test')) {
			$api_url = 'https://payflowpro.verisign.com/transaction';
		} else {
			$api_url = 'https://pilot-payflowpro.verisign.com/transaction';
		}
		
		if (!$this->config->get('default_pp_pro_uk_transaction')) {
			$payment_type = 'A';	
		} else {
			$payment_type = 'S';
		}
		
		$this->load->model('checkout/order');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		$payment_data = array(
			'USER'      => html_entity_decode($this->config->get('default_pp_pro_uk_user'), ENT_QUOTES, 'UTF-8'),
			'VENDOR'    => html_entity_decode($this->config->get('default_pp_pro_uk_vendor'), ENT_QUOTES, 'UTF-8'),
			'PARTNER'   => html_entity_decode($this->config->get('default_pp_pro_uk_partner'), ENT_QUOTES, 'UTF-8'),
			'PWD'       => html_entity_decode($this->config->get('default_pp_pro_uk_password'), ENT_QUOTES, 'UTF-8'),
			'TENDER'    => 'C',
			'TRXTYPE'   => $payment_type,
			'AMT'       => $this->currency->format($order_info['total'], $order_info['currency'], 1.00000, FALSE),
			'CURRENCY'  => $order_info['currency'],
			'NAME'      => $this->request->post['cc_owner'],
			'STREET'    => $order_info['payment_address_1'],
			'CITY'      => $order_info['payment_city'],
            'STATE'     => ($order_info['payment_iso_code_2'] != 'US') ? $order_info['payment_zone'] : $order_info['payment_zone_code'],
			'COUNTRY'   => $order_info['payment_iso_code_2'],
			'ZIP'       => str_replace(' ', '', $order_info['payment_postcode']),
			'CLIENTIP'  => $this->request->server['REMOTE_ADDR'],
			'EMAIL'     => $order_info['email'],
            'ACCT'      => str_replace(' ', '', $this->request->post['cc_number']),
            'ACCTTYPE'  => $this->request->post['cc_type'],
            'CARDSTART' => $this->request->post['cc_start_date_month'] . substr($this->request->post['cc_start_date_year'], - 2, 2),
            'EXPDATE'   => $this->request->post['cc_expire_date_month'] . substr($this->request->post['cc_expire_date_year'], - 2, 2),
            'CVV2'      => $this->request->post['cc_cvv2'],
			'CARDISSUE' => $this->request->post['cc_issue']
		);
		 
		$curl = curl_init($api_url);

		curl_setopt($curl, CURLOPT_PORT, 443);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($payment_data));
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-VPS-REQUEST-ID: ' . md5($this->session->data['order_id'] . rand())));

		$response = curl_exec($curl);
  		
		curl_close($curl);
 
 		$response_data = array();
 
		parse_str($response, $response_data);

		$json = array();

		if ($response_data['RESULT'] == '0') {
			$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('config_order_status_id'));
			
			$message = '';
			
			if (isset($response_data['AVSCODE'])) {
				$message .= 'AVSCODE: ' . $response_data['AVSCODE'] . "\n";
			}

			if (isset($response_data['CVV2MATCH'])) {
				$message .= 'CVV2MATCH: ' . $response_data['CVV2MATCH'] . "\n";
			}

			if (isset($response_data['TRANSACTIONID'])) {
				$message .= 'TRANSACTIONID: ' . $response_data['TRANSACTIONID'] . "\n";
			}

			$this->model_checkout_order->updatePaymentMethodData($this->session->data['order_id'], $response);
			$this->model_checkout_order->update($this->session->data['order_id'], $this->config->get('default_pp_pro_uk_order_status_id'), $message, FALSE);
		
			$json['success'] = $this->html->getSecureURL('checkout/success'); 
		} else {
			switch ($response_data['RESULT']) {
				case '1':
				case '26':
					$json['error'] = $this->language->get('error_config');
					break;
				case '7':
					$json['error'] = $this->language->get('error_address');
					break;
				case '12':
					$json['error'] = $this->language->get('error_declined');
					break;
				case '23':
				case '24':
					$json['error'] = $this->language->get('error_invalid');
					break;
				default:
					$json['error'] = $this->language->get('error_general');
					break;
			}		
		}

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($json));
	}
}