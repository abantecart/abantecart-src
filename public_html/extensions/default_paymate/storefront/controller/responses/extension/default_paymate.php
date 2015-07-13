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

class ControllerResponsesExtensionDefaultPaymate extends AController {
	public function main() {
    	$template_data['button_confirm'] = $this->language->get('button_confirm');
		$template_data['button_back'] = $this->language->get('button_back');

		$this->load->model('checkout/order');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$template_data['mid'] = $this->config->get('default_paymate_username');

		$this->load->library('encryption');
		
		$encryption = new AEncryption($this->config->get('encryption_key'));

		$template_data['return'] = $this->html->getSecureURL('extension/paymate/callback', '&oid=' . base64_encode($encryption->encrypt($order_info['order_id'])) . '&conf=' . base64_encode($encryption->encrypt($order_info['payment_firstname'] . $order_info['payment_lastname'])));

		if ($this->config->get('default_paymate_include_order')) {
			$template_data['ref'] = html_entity_decode($this->config->get('store_name'), ENT_QUOTES, 'UTF-8') . " (#" . $order_info['order_id'] . ")";
		} else {
			$template_data['ref'] = html_entity_decode($this->config->get('store_name'), ENT_QUOTES, 'UTF-8');
		}

		$currency = array(
			'AUD',
			'NZD',
			'USD',
			'EUR',
			'GBP'
		);

		if (in_array(strtoupper($order_info['currency']), $currency)) {
			$template_data['currency'] = $order_info['currency'];
			$template_data['amt'] = $this->currency->format($order_info['total'], $order_info['currency'], $order_info['value'], FALSE); 
		} else {
			for ($findcur = 0; $findcur < sizeof($currency); $findcur++) {
				if ($this->currency->getValue($currency[$findcur])) {
					$template_data['currency'] = $currency[$findcur];
					$template_data['amt'] = $this->currency->format($order_info['total'], $currency[$findcur], '',FALSE);
					break;
				} elseif ($findcur == (sizeof($currency) - 1)){
					$template_data['currency'] = 'AUD';
					$template_data['amt'] = $order_info['total'];
				}
			}
		}

		$template_data['pmt_contact_firstname'] = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');
		$template_data['pmt_contact_surname'] = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
		$template_data['pmt_contact_phone'] = $order_info['telephone'];
		$template_data['pmt_sender_email'] = $order_info['email'];
		$template_data['regindi_address1'] = html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8');
		$template_data['regindi_address2'] = html_entity_decode($order_info['payment_address_2'], ENT_QUOTES, 'UTF-8');
		$template_data['regindi_sub'] = html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');
		$template_data['regindi_state'] = html_entity_decode($order_info['payment_zone'], ENT_QUOTES, 'UTF-8');
		$template_data['regindi_pcode'] = html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');
		$template_data['pmt_country'] = $order_info['iso_code_2'];

		$template_data['action'] = 'https://www.paymate.com/PayMate/ExpressPayment';
		$template_data['back'] = $this->html->getSecureURL('checkout/payment');
		
		$this->view->batchAssign( $template_data );
		$this->processTemplate('responses/default_paymate.tpl' );
	}
	
	public function callback() {
	 	$this->loadLanguage('default_paymate/default_paymate');
		
		$error = '';

		if (isset($this->request->post['responseCode'])) {
			if($this->request->post['responseCode'] == 'PA' || $this->request->post['responseCode'] == 'PP') {
				if (isset($this->request->get['oid']) && isset($this->request->get['conf'])) {
					$this->load->library('encryption');
					
					$encryption = new AEncryption($this->config->get('encryption_key'));

					$order_id = $encryption->decrypt(base64_decode($this->request->get['oid']));

					$this->load->model('checkout/order');
					
					$order_info = $this->model_checkout_order->getOrder($order_id);

					if((isset($order_info['payment_firstname']) && isset($order_info['payment_lastname'])) && strcmp($encryption->decrypt(base64_decode($this->request->get['conf'])),$order_info['payment_firstname'] . $order_info['payment_lastname']) == 0) {
						$this->model_checkout_order->confirm($order_id, $this->config->get('default_paymate_order_status_id'));
					} else {
						$error = $this->language->get('text_unable');
					}
				} else {
					$error = $this->language->get('text_unable');
				}
			} else {
				$error = $this->language->get('text_declined'); 
			}
		} else {
			$error = $this->language->get('text_unable');
		}

		if ($error != '') {
			$template_data['heading_title'] = $this->language->get('text_failed');
			$template_data['text_message'] = sprintf($this->language->get('text_failed_message'), $error, $this->html->getURL('content/contact'));
			$template_data['button_continue'] = $this->language->get('button_continue');
			$template_data['continue'] = $this->html->getURL('index/home');

			$this->view->batchAssign( $template_data );
            $this->processTemplate($this->config->get('config_storefront_template') . 'common/success.tpl' );
		} else {
			$this->redirect($this->html->getSecureURL('checkout/success'));
		}
	}
}
