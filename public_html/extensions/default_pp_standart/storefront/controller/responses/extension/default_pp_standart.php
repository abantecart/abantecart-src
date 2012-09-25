<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

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

class ControllerResponsesExtensionDefaultPPStandart extends AController {
	public function main() {
    	$template_data['button_confirm'] = $this->language->get('button_confirm');
		$template_data['button_back'] = $this->language->get('button_back');

		if (!$this->config->get('default_pp_standart_test')) {
    		$template_data['action'] = 'https://www.paypal.com/cgi-bin/webscr';
  		} else {
			$template_data['action'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		}		
		
		$this->load->model('checkout/order');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		$template_data['business'] = $this->config->get('default_pp_standart_email');
		$template_data['item_name'] = html_entity_decode($this->config->get('store_name'), ENT_QUOTES, 'UTF-8');				
		$template_data['currency_code'] = $order_info['currency'];
		$template_data['amount'] = $this->currency->format($order_info['total'], $order_info['currency'], $order_info['value'], FALSE);
		$template_data['first_name'] = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');	
		$template_data['last_name'] = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');	
		$template_data['address1'] = html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8');	
		$template_data['address2'] = html_entity_decode($order_info['payment_address_2'], ENT_QUOTES, 'UTF-8');	
		$template_data['city'] = html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');	
		$template_data['zip'] = html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');	
		$template_data['country'] = $order_info['payment_iso_code_2'];
		$template_data['notify_url'] = $this->html->getURL('extension/default_pp_standart/callback');
		$template_data['email'] = $order_info['email'];
		$template_data['invoice'] = $this->session->data['order_id'] . ' - ' . html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
		$template_data['lc'] = $this->session->data['language'];
		
		if (!$this->config->get('default_pp_standart_transaction')) {
			$template_data['paymentaction'] = 'authorization';
		} else {
			$template_data['paymentaction'] = 'sale';
		}
		
		$template_data['return'] = $this->html->getSecureURL('checkout/success');
		
		if ($this->request->get['rt'] != 'checkout/guest_step_3') {
			$template_data['cancel_return'] = $this->html->getSecureURL('checkout/payment');
		} else {
			$template_data['cancel_return'] = $this->html->getSecureURL('checkout/guest_step_2');
		}
		
		$this->load->library('encryption');
		
		$encryption = new AEncryption($this->config->get('encryption_key'));
		
		$template_data['custom'] = $encryption->encrypt($this->session->data['order_id']);
		
		if ($this->request->get['rt'] != 'checkout/guest_step_3') {
			$template_data['back'] = $this->html->getSecureURL('checkout/payment');
		} else {
			$template_data['back'] = $this->html->getSecureURL('checkout/guest_step_2');
		}
		
		$this->view->batchAssign( $template_data ); 
		$this->processTemplate('responses/default_pp_standart.tpl');
	}
	
	public function callback() {
		$this->load->library('encryption');
	
		$encryption = new AEncryption($this->config->get('encryption_key'));
		
		if (isset($this->request->post['custom'])) {
			$order_id = $encryption->decrypt($this->request->post['custom']);
		} else {
			$order_id = 0;
		}

		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($order_id);
		$suspect = false;
		$message = '';
		if ($order_info) {
			// check seller email and save message if not equal
			if($this->request->post['receiver_email'] != $this->config->get('default_pp_standart_email')){

				$this->load->language('default_pp_standart/default_pp_standart');

				$message .= $this->language->get('text_suspect');
				$params = array(
					'payment_status',
					'pending_reason',
					'address_zip',
					'address_country_code',
					'address_name',
					'address_country',
					'address_city',
					'quantity',
					'payer_email',
					'first_name',
					'last_name',
					'payment_gross',
					'shipping',
					'ipn_track_id',
					'receiver_email'
				);
				foreach($params as $p){
					if(isset($this->request->post[$p])){
						$message .=  $p .": ".$this->request->post[$p]."<br>\n";
					}
				}
				$msg = new AMessage();
				$msg->saveNotice(sprintf($this->language->get('text_suspect_subj'),$order_id), $message);
				$suspect = true;
			}


			$request = 'cmd=_notify-validate';
		
			foreach ($this->request->post as $key => $value) {
				$request .= '&' . $key . '=' . urlencode(stripslashes(html_entity_decode($value, ENT_QUOTES, 'UTF-8')));
			}
				
			if (extension_loaded('curl')) {
				if (!$this->config->get('default_pp_standart_test')) {
					$ch = curl_init('https://www.paypal.com/cgi-bin/webscr');
				} else {
					$ch = curl_init('https://www.sandbox.paypal.com/cgi-bin/webscr');
				}

				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_TIMEOUT, 30);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
				$response = curl_exec($ch);

				if($suspect===true){
					// set pending status for all suspected orders
					$this->model_checkout_order->confirm($order_id, 1, $message );
				}elseif (strcmp($response, 'VERIFIED') == 0 || $this->request->post['payment_status'] == 'Completed') {
					$this->model_checkout_order->confirm($order_id, $this->config->get('default_pp_standart_order_status_id'));
				} else {
					$this->model_checkout_order->confirm($order_id, $this->config->get('config_order_status_id'));
				}
					
				curl_close($ch);
			} else {
				$header  = 'POST /cgi-bin/webscr HTTP/1.0' . "\r\n";
				$header .= 'Content-Type: application/x-www-form-urlencoded' . "\r\n";
				$header .= 'Content-Length: ' . strlen(utf8_decode($request)) . "\r\n";
				$header .= 'Connection: close'  ."\r\n\r\n";
				
				if (!$this->config->get('default_pp_standart_test')) {
					$fp = fsockopen('www.paypal.com', 80, $errno, $errstr, 30);
				} else {
					$fp = fsockopen('www.sandbox.paypal.com', 80, $errno, $errstr, 30);
				}
			
				if ($fp) {
					fputs($fp, $header . $request);
				
					while (!feof($fp)) {
						$response = fgets($fp, 1024);
					
						if (strcmp($response, 'VERIFIED') == 0) {
							$this->model_checkout_order->confirm($order_id, $this->config->get('default_pp_standart_order_status_id'));
						} else {
							$this->model_checkout_order->confirm($order_id, $this->config->get('config_order_status_id'));
						}
					}
				
					fclose($fp);
				}
			}
		}
	}
}
