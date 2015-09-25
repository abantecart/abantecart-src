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
if (!defined('DIR_CORE')) {
	header('Location: static_pages/');
}

class ControllerResponsesExtensionDefaultAuthorizeNetAim extends AController {
	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('default_authorizenet_aim/default_authorizenet_aim');

		$data['text_credit_card'] = $this->language->get('text_credit_card');
		$data['text_wait'] = $this->language->get('text_wait');

		$data['entry_cc_owner'] = $this->language->get('entry_cc_owner');
		$data['cc_owner'] = HtmlElementFactory::create(array( 'type' => 'input',
		                                                      'name' => 'cc_owner',
		                                                      'value' => '' ));

		$data['entry_cc_number'] = $this->language->get('entry_cc_number');
		$data['cc_number'] = HtmlElementFactory::create(array( 'type' => 'input',
		                                                       'name' => 'cc_number',
		                                                       'attr' => 'autocomplete="off"',
		                                                       'value' => '' ));

		$data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');

		$data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');
		$data['entry_cc_cvv2_short'] = $this->language->get('entry_cc_cvv2_short');
		$data['cc_cvv2_help_url'] = $this->html->getURL('r/extension/default_authorizenet_aim/cvv2_help');

		$data['cc_cvv2'] = HtmlElementFactory::create(array( 'type' => 'input',
		                                                     'name' => 'cc_cvv2',
		                                                     'value' => '',
		                                                     'style' => 'short input-mini',
		                                                     'attr' => ' size="3" autocomplete="off"'
		                                                ));

		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['button_back'] = $this->language->get('button_back');

		$months = array();

		for ($i = 1; $i <= 12; $i++) {
			$months[ sprintf('%02d', $i) ] = strftime('%B', mktime(0, 0, 0, $i, 1, 2000));
		}
		$data['cc_expire_date_month'] = HtmlElementFactory::create(
			array( 'type' => 'selectbox',
			     'name' => 'cc_expire_date_month',
			     'value' => sprintf('%02d', date('m')),
			     'options' => $months,
			     'style' => 'short input-small'
			));

		$today = getdate();
		$years = array();
		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
			$years[ strftime('%Y', mktime(0, 0, 0, 1, 1, $i)) ] = strftime('%Y', mktime(0, 0, 0, 1, 1, $i));
		}
		$data['cc_expire_date_year'] = HtmlElementFactory::create(
			array( 'type' => 'selectbox',
			    'name' => 'cc_expire_date_year',
			    'value' => sprintf('%02d', date('Y') + 1),
			    'options' => $years,
			    'style' => 'short input-small' 
			));

		$back = $this->request->get['rt'] != 'checkout/guest_step_3' ? $this->html->getSecureURL('checkout/payment')
				: $this->html->getSecureURL('checkout/guest_step_2');
		$data['back'] = HtmlElementFactory::create(array( 'type' => 'button',
		                                                  'name' => 'back',
		                                                  'text' => $this->language->get('button_back'),
		                                                  'style' => 'button',
		                                                  'href' => $back 
		                                           ));
		$data['submit'] = HtmlElementFactory::create(array( 'type' => 'button',
			                                                  'name' => 'authorizenet_button',
		                                                      'text' => $this->language->get('button_confirm'),
			                                                  'style' => 'button'
		                                             ));
		$this->view->batchAssign($data);

		//init controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		//load creditcard input validation
		$this->document->addScriptBottom($this->view->templateResource('/javascript/credit_card_validation.js'));

		$this->processTemplate('responses/default_authorizenet_aim.tpl');
	}

	public function api() {
		$this->loadLanguage('default_authorizenet_aim/default_authorizenet_aim');

		$data['text_credit_card'] = $this->language->get('text_credit_card');

		$data['entry_cc_owner'] = $this->language->get('entry_cc_owner');
		$data['cc_owner'] = array( 'type' => 'input',
		                             'name' => 'cc_owner',
		                             'required' => true,
		                             'value' => '' );

		$data['entry_cc_number'] = $this->language->get('entry_cc_number');
		$data['cc_number'] = array( 'type' => 'input',
		                              'name' => 'cc_number',
		                              'required' => true,
		                              'value' => '' );
		                              
		$data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');
		$data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');
		$data['entry_cc_cvv2_short'] = $this->language->get('entry_cc_cvv2_short');
		$data['cc_cvv2_help_url'] = $this->html->getURL('r/extension/default_authorizenet_aim/cvv2_help');

		$data['cc_cvv2'] = array( 'type' => 'input',
		                            'name' => 'cc_cvv2',
		                            'value' => '',
		                            'style' => 'short input-small',
		                            'required' => true,
		                            'attr' => ' size="3"');
		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['button_back'] = $this->language->get('button_back');

		$months = array();
		for ($i = 1; $i <= 12; $i++) {
			$months[ sprintf('%02d', $i) ] = strftime('%B', mktime(0, 0, 0, $i, 1, 2000));
		}
		$data['cc_expire_date_month'] =
			array( 'type' => 'selectbox',
			     'name' => 'cc_expire_date_month',
			     'value' => sprintf('%02d', date('m')),
			     'options' => $months,
			     'required' => true,
			     'style' => 'short input-small'
			);

		$today = getdate();
		$years = array();
		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
			$years[ strftime('%Y', mktime(0, 0, 0, 1, 1, $i)) ] = strftime('%Y', mktime(0, 0, 0, 1, 1, $i));
		}
		$data['cc_expire_date_year'] = array( 'type' => 'selectbox',
		                                        'name' => 'cc_expire_date_year',
		                                        'value' => sprintf('%02d', date('Y') + 1),
		                                        'options' => $years,
		                                        'required' => true,
		                                        'style' => 'short input-small' );

		$data['process_rt'] = 'default_authorizenet_aim/send';
				
		$this->load->library('json');
		$this->response->setOutput(AJson::encode($data));
	}


	public function send() {
		if ($this->config->get('default_authorizenet_aim_mode') == 'live') {
			$url = 'https://secure.authorize.net/gateway/transact.dll';
		} elseif ($this->config->get('default_authorizenet_aim_mode') == 'test') {
			$url = 'https://test.authorize.net/gateway/transact.dll';
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

		$data['x_login'] = $this->config->get('default_authorizenet_aim_login');
		$data['x_tran_key'] = $this->config->get('default_authorizenet_aim_key');
		$data['x_version'] = '3.1';
		$data['x_delim_data'] = 'TRUE';
		$data['x_delim_char'] = ',';
		$data['x_encap_char'] = '"';
		$data['x_relay_response'] = 'FALSE';
		$data['x_first_name'] = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');
		$data['x_last_name'] = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
		$data['x_company'] = html_entity_decode($order_info['payment_company'], ENT_QUOTES, 'UTF-8');
		$data['x_address'] = html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8');
		$data['x_city'] = html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');
		$data['x_state'] = html_entity_decode($order_info['payment_zone'], ENT_QUOTES, 'UTF-8');
		$data['x_zip'] = html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');
		$data['x_country'] = html_entity_decode($order_info['payment_country'], ENT_QUOTES, 'UTF-8');
		$data['x_phone'] = $order_info['telephone'];
		$data['x_customer_ip'] = $this->request->server['REMOTE_ADDR'];
		$data['x_email'] = $order_info['email'];
		$data['x_description'] = html_entity_decode($this->config->get('store_name'), ENT_QUOTES, 'UTF-8');
		$data['x_amount'] = $this->currency->format($order_info['total'], $order_info['currency'], 1.00000, FALSE);
		$data['x_currency_code'] = $this->currency->getCode();
		$data['x_method'] = 'CC';
		$data['x_type'] = ($this->config->get('default_authorizenet_aim_method') == 'capture') ? 'AUTH_CAPTURE'
				: 'AUTH_ONLY';
		$data['x_card_num'] = str_replace(' ', '', $this->request->post['cc_number']);
		$data['x_exp_date'] = $this->request->post['cc_expire_date_month'] . $this->request->post['cc_expire_date_year'];
		$data['x_card_code'] = $this->request->post['cc_cvv2'];
		$data['x_invoice_num'] = $this->session->data['order_id'];

		if ($this->config->get('default_authorizenet_aim_mode') == 'test') {
			$data['x_test_request'] = 'TRUE';
		}

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

		$i = 1;

		$response_data = array();

		$results = explode(',', $response);

		foreach ($results as $result) {
			$response_data[ $i ] = trim($result, '"');

			$i++;
		}

		$json = array();

		//build responce message for records
		$message = '';
		if (has_value($response_data['5'])) {
		    $message .= 'Authorization Code: ' . $response_data['5'] . "\n";
		}
		if (has_value($response_data['6'])) {
		    $message .= 'AVS Response: ' . $response_data['6'] . "\n";
		}
		if (has_value($response_data['7'])) {
		    $message .= 'Transaction ID: ' . $response_data['7'] . "\n";
		}
		if (has_value($response_data['39'])) {
		    $message .= 'Card Code Response: ' . $response_data['39'] . "\n";
		}
		if (has_value($response_data['40'])) {
		    $message .= 'Cardholder Authentication Verification Response: ' . $response_data['40'] . "\n";
		}

		/*
			Response Code:
			Value: The overall status of the transaction
			format:
			 1 = Approved
			 2 = Declined
			 3 = Error
			 4 = Held for Review		
		*/

		if ($response_data[ 1 ] == '1') {
			if (strtoupper($response_data[ 38 ]) != strtoupper(md5($this->config->get('default_authorizenet_aim_hash') . $this->config->get('default_authorizenet_aim_login') . $response_data[ 6 ] . $this->currency->format($order_info['total'], $order_info['currency'], 1.00000, FALSE)))) {
				$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('config_order_status_id'));
				$this->model_checkout_order->update($this->session->data['order_id'], $this->config->get('default_authorizenet_aim_order_status_id'), $message, FALSE);
			}

			$json['success'] = $this->html->getSecureURL('checkout/success');
		} else if ($response_data[ 1 ] == '2') {
			$this->loadLanguage('default_authorizenet_aim/default_authorizenet_aim');
			
			//special case of declined payment. Count declined. If limit is set. 
			$this->session->data['decline_count'] = $this->session->data['decline_count'] + 1;
			$decline_limit = $this->config->get('default_authorizenet_aim_decline_limit');
			if (has_value($decline_limit) && $this->session->data['decline_count'] > $decline_limit) {

				$json['error'] = $this->language->get('warning_suspicious');				
				$this->loadModel('account/customer');
				$customer_id = $this->customer->getId();
				$this->model_account_customer->editStatus($customer_id, 0);
				
				$link = $this->html->getSecureURL('sale/customer/update', '&s=' . ADMIN_PATH .'&customer_id='.$customer_id);
				$msg = new AMessage();
				//send message with unique title to prevent grouping message
				$msg->saveNotice(
								 $this->language->get('warning_suspicious_to_admin') . '. Customer ID: ' . $customer_id, 
								 sprintf($this->language->get('warning_suspicious_to_admin_body'), $link)
								);				
			} else {
				$json['error'] = $this->language->get("warning_declined");
				//record this decline to history
				$message = 'Credit card was declined: ' . "<br>" . $message;
				$this->model_checkout_order->addHistory(
					$this->session->data['order_id'], 
					0, 
					$message 
				);
			} 
		} else if ($response_data[ 1 ] == '4') {
			//special case of sucess payment in review stage. Create order with pending status
			$new_order_status_id = $this->order_status->getStatusByTextId('pending');
			$this->model_checkout_order->confirm($this->session->data['order_id'], $new_order_status_id);
			$this->model_checkout_order->update($this->session->data['order_id'], $new_order_status_id, $message, FALSE);
			$json['success'] = $this->html->getSecureURL('checkout/success');
		} else {
			$json['error'] = $response_data[ 4 ];
			//record this incident to history
			$message = 'Error processing credit card: '."<br>".$json['error']."<br>".$message;
			$this->model_checkout_order->addHistory(
				$this->session->data['order_id'], 
				0, 
				$message 
			);
		}

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($json));
	}

	public function cvv2_help() {
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('default_authorizenet_aim/default_authorizenet_aim');

		$image = '<img src="' . $this->view->templateResource('/image/securitycode.jpg') . '" alt="' . $this->language->get('entry_what_cvv2') . '" />';

		$this->view->assign('description', $image );

		//init controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->processTemplate('responses/content/content.tpl' );
	}
}
