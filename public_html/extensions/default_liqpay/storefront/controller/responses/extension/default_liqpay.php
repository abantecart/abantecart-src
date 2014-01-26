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

class ControllerResponsesExtensionDefaultLiqPay extends AController {
	public function main() {		
		$this->load->model('checkout/order');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		$data['action'] = '';
		$form = new AForm();
		$form->setForm(array( 'form_name' => 'checkout' ));
		$data[ 'form' ][ 'form_open' ] = $form->getFieldHtml(array( 'type' => 'form',
		                                                          'name' => 'checkout',
		                                                          'action' => 'https://www.liqpay.com/?do=clickNbuy' ));

		$xml  = '<response>';
		$xml .= '	<version>1.2</version>';
		$xml .= '	<result_url>' . $this->html->getSecureURL('checkout/success') . '</result_url>';
		$xml .= '	<server_url>' . $this->html->getSecureURL('extension/liqpay/callback') . '</server_url>';
		$xml .= '	<merchant_id>' . $this->config->get('default_liqpay_merchant') . '</merchant_id>';
		$xml .= '	<order_id>' . $this->session->data['order_id'] . '</order_id>';
		$xml .= '	<amount>' . $this->currency->format($order_info['total'], $order_info['currency'], $order_info['value'], FALSE) . '</amount>';
		$xml .= '	<currency>' . $order_info['currency'] . '</currency>';
		$xml .= '	<description>' . $this->config->get('store_name') . ' ' . $order_info['payment_firstname'] . ' ' . $order_info['payment_address_1'] . ' ' . $order_info['payment_address_2'] . ' ' . $order_info['payment_city'] . ' ' . $order_info['email'] . '</description>';
		$xml .= '	<sender_phone></sender_phone>';
		$xml .= '	<pay_way>' . $this->config->get('default_liqpay_type') . '</pay_way>';
		$xml .= '</response>';

		$data[ 'form' ][ 'xml' ] = $form->getFieldHtml(array( 'type' => 'hidden',
		                                                      'name' => 'xml',
		                                                      'value' => base64_encode($xml),
		                                                       ));
		$data[ 'form' ][ 'signature' ] = $form->getFieldHtml(array( 'type' => 'hidden',
		                                                      'name' => 'signature',
		                                                      'value' => base64_encode(sha1($this->config->get('default_liqpay_signature') . $xml . $this->config->get('default_liqpay_signature'), TRUE)),
		                                                       ));

		$back = $this->request->get[ 'rt' ] != 'checkout/guest_step_3' ? $this->html->getSecureURL('checkout/payment')
				: $this->html->getSecureURL('checkout/guest_step_2');

		$data[ 'form' ][ 'back' ] = $form->getFieldHtml(array( 'type' => 'button',
		                                                     'name' => 'back',
		                                                     'text' => $this->language->get('button_back'),
		                                                     'style' => 'button',
		                                                     'href' => $back ));
		$data[ 'form' ][ 'submit' ] = $form->getFieldHtml(array( 'type' => 'submit',
		                                                       'name' => $this->language->get('button_confirm')
		                                                  ));



        $this->view->batchAssign( $data );
		$this->processTemplate('responses/default_liqpay.tpl' );
	}

	public function confirm() {
		return;
	}	

	public function callback() {
		$xml = base64_decode($this->request->post['operation_xml']);
		$signature = base64_encode(sha1($this->config->get('default_liqpay_signature') . $xml . $this->config->get('default_liqpay_signature'), TRUE));
		
		$posleft = strpos($xml, 'order_id');
		$posright = strpos($xml, '/order_id');
		
		$order_id = substr($xml, $posleft + 9, $posright - $posleft - 10);
		
		if ($signature == $this->request->post['signature']) {
			$this->load->model('checkout/order');
	
			$this->model_checkout_order->confirm($order_id, $this->config->get('config_order_status_id'));			
		}
	}
}