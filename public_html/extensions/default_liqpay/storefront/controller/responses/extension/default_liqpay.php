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
if(!defined('DIR_CORE')){
	header('Location: static_pages/');
}

class ControllerResponsesExtensionDefaultLiqPay extends AController{
	public function main(){
		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		$order_id = $this->session->data['order_id'];
		$description = 'Order #' . $order_id;
		$order_id .= '#' . time();
		$return_url = $this->html->getSecureURL('checkout/success');
		$callback_url = $this->html->getSecureURL('extension/default_liqpay/callback');
		$private_key = $this->config->get('default_liqpay_private_key');
		$public_key = $this->config->get('default_liqpay_public_key');
		$currency = $order_info['currency'];
		if($currency == 'RUR'){
			$currency = 'RUB';
		}

		$amount = $this->currency->format(
			$order_info['total'],
			$order_info['currency_code'],
			$order_info['currency_value'],
			false
		);

		$language = $this->language->getCurrentLanguage();
		$language_code = $language['code'] == 'ru' ? 'ru' : 'en';

		$fields = array();
		$fields['version'] = 3;
		$fields['public_key'] = $public_key;
		$fields['amount'] = $amount;
		$fields['currency'] = $currency;
		$fields['description'] = $description;
		$fields['order_id'] = $order_id;
		$fields['result_url'] = $return_url;
		$fields['server_url'] = $callback_url;
		$fields['type'] = 'buy';
		$fields['pay_way'] = 'card,liqpay';
		$fields['language'] = $language_code;
		$fields['sandbox'] = (int)$this->config->get('default_liqpay_test_mode');

		//data - result of base64_encode( $json_string )
		//signature - result of base64_encode( sha1( $private_key . $data . $private_key ) )
		$params = array();
		$params['data'] = base64_encode(json_encode($fields));
		$params['signature'] = base64_encode(sha1($private_key.$params['data'].$private_key, 1));

		$form = new AForm();
		$form->setForm(array('form_name' => 'checkout'));
		$data['form']['form_open'] = $form->getFieldHtml(array('type'   => 'form',
															   'name'   => 'checkout',
															   'action' => 'https://www.liqpay.com/api/checkout'
		));

		foreach($params as $k=>$val){
			$data['form']['fields'][$k] = $form->getFieldHtml(array('type'  => 'hidden',
																	'name'  => $k,
																	'value' => $val
			));
		}

		$back = $this->request->get['rt'] != 'checkout/guest_step_3' ? $this->html->getSecureURL('checkout/payment')
																	 : $this->html->getSecureURL('checkout/guest_step_2');

		$data['form']['back'] = $form->getFieldHtml(array('type'  => 'button',
														  'name'  => 'back',
														  'text'  => $this->language->get('button_back'),
														  'style' => 'button',
														  'href'  => $back
		));
		$data['form']['submit'] = $form->getFieldHtml(array('type' => 'submit',
															'name' => $this->language->get('button_confirm')
		));

		$this->view->batchAssign($data);
		$this->processTemplate('responses/default_liqpay.tpl');
	}

	public function confirm(){
		return null;
	}

	private function getOrderStatus($liqpay_status){

		if($this->config->get('default_liqpay_order_status_id')!='5'){
			return $this->config->get('default_liqpay_order_status_id');
		}
		//for "auto-complete" orders check status from api-response. If something wrong - set pending
		switch ($liqpay_status){
			case 'sandbox':
			case 'success':
					$ac_status = 5;
				break;
			case 'failure':
					$ac_status = 10;
				break;
			case 'processing':
					$ac_status = 2;
				break;
			case 'reversed':
					$ac_status = 12;
				break;
			default:
				$ac_status = 1; // pending
				break;
		}
		return $ac_status;
	}

	public function callback(){
		$callback_data = json_decode(base64_decode($this->request->post['data']), true);

		$private_key = $this->config->get('default_liqpay_private_key');

		$data = base64_encode(json_encode($callback_data));
		$signature = base64_encode(sha1($private_key.$data.$private_key, 1));

		if ($signature == $this->request->post['signature']) {

			$order_status_id = $this->getOrderStatus($callback_data['status']);
			$this->load->model('checkout/order');
			$this->model_checkout_order->confirm($callback_data['order_id'], (int)$order_status_id);
			$this->model_checkout_order->updatePaymentMethodData($callback_data['order_id'], serialize($callback_data));
		}
	}
}