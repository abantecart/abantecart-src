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

class ControllerResponsesExtensionDefaultCashflows extends AController {
	public function main() {
    	$this->loadLanguage('default_cashflows/default_cashflows');
		
		$data['text_credit_card'] = $this->language->get('text_credit_card');
		$data['text_start_date'] = $this->language->get('text_start_date');
		$data['text_issue'] = $this->language->get('text_issue');
		$data['text_wait'] = $this->language->get('text_wait');
		
		$data['entry_cc_number'] = $this->language->get('entry_cc_number');
		$data['entry_cc_start_date'] = $this->language->get('entry_cc_start_date');
		$data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');
		$data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');
		$data['entry_cc_issue'] = $this->language->get('entry_cc_issue');
		
		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['button_back'] = $this->language->get('button_back');


		$data[ 'cc_owner' ] = HtmlElementFactory::create(array(
					'type' => 'input',
					'name' => 'cc_owner',
					'value' => $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'],
					'style' => 'input-medium'
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


		$data[ 'cc_expire_date_month' ] = $this->html->buildElement(
			array( 'type' => 'selectbox',
			     'name' => 'cc_expire_date_month',
			     'value' => sprintf('%02d', date('m')),
			     'options' => $months,
			     'style' => 'short input-small'
			));

		$data[ 'cc_start_date_month' ] = $this->html->buildElement(
			array( 'type' => 'selectbox',
			     'name' => 'cc_start_date_month',
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

		$data[ 'cc_start_date_year' ] = HtmlElementFactory::create(array( 'type' => 'selectbox',
		                                                                 'name' => 'cc_start_date_year',
		                                                                 'value' => sprintf('%02d', date('Y') + 1),
		                                                                 'options' => $years,
		                                                                 'style' => 'short input-small' ));



		$data[ 'cc_cvv2' ] = HtmlElementFactory::create(array( 'type' => 'input',
		                                                     'name' => 'cc_cvv2',
		                                                     'value' => '',
		                                                     'style' => 'short',
		                                                     'attr' => ' size="3" maxlength="4" '
		                                                ));

		$data[ 'cc_issue' ] = HtmlElementFactory::create(array( 'type' => 'input',
			                                                     'name' => 'cc_issue',
			                                                     'value' => '',
			                                                     'style' => 'short',
			                                                     'attr' => ' size="1" maxlength="2" '
			                                                ));



		if ($this->request->get['rt'] != 'checkout/guest_step_3') {
			$back = $this->html->getSecureURL('checkout/payment');
		} else {
			$back = $this->html->getSecureURL('checkout/guest_step_2');
		}

		$data[ 'back' ] = HtmlElementFactory::create(array( 'type' => 'button',
				                                                  'name' => 'back',
				                                                  'text' => $this->language->get('button_back'),
				                                                  'style' => 'button',
				                                                  'href' => $back ));

		$data[ 'submit' ] = HtmlElementFactory::create(array( 'type' => 'button',
			                                                  'name' => 'pp_button',
		                                                      'text' => $this->language->get('button_confirm'),
			                                                  'style' => 'button btn-orange',
		                                               ));

		$this->view->batchAssign( $data );
		//load creditcard input validation
		$this->document->addScriptBottom($this->view->templateResource('/javascript/credit_card_validation.js'));
		$this->processTemplate('responses/default_cashflows.tpl' );
	}

	public function send() {
		$this->loadLanguage('default_cashflows/default_cashflows');
		
		$this->load->model('checkout/order');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$payment_data = array(
			'auth_id'        => $this->config->get('default_cashflows_auth_id'),
			'auth_pass'      => $this->config->get('default_cashflows_auth_pass'),
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
			'tran_testmode' => $this->config->get('default_cashflows_test'),
			'tran_type'     => 'Sale',
			'tran_class'    => 'MoTo',
		);

		$curl = curl_init('https://secure.cashflows.com/gateway/remote');
		
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
				
				$this->model_checkout_order->update($this->session->data['order_id'], $this->config->get('default_cashflows_order_status_id'), $message, FALSE);
					
				$json['success'] = $this->html->getSecureURL('checkout/success');
			} else {
				$json['error'] = end($data);
			}
		}
		
		$this->load->library('json');
		$this->response->setOutput(AJson::encode($json));
	}
}