<?php
	/*------------------------------------------------------------------------------
   $Id$

   AbanteCart, Ideal OpenSource Ecommerce Solution
   http://www.AbanteCart.com

   Copyright © 2011 Belavier Commerce LLC

   This source file is subject to Open Software License (OSL 3.0)
   Lincence details is bundled with this package in the file LICENSE.txt.
   It is also available at this URL:
   &lt;http://www.opensource.org/licenses/OSL-3.0&gt;

  UPGRADE NOTE:
	Do not edit or add to this file if you wish to upgrade AbanteCart to newer
	versions in the future. If you wish to customize AbanteCart for your
	needs please refer to http://www.AbanteCart.com for more information.
 ------------------------------------------------------------------------------*/

	if (! defined ( 'DIR_CORE' )) {
		header ( 'Location: static_pages/' );
	}

class ExtensionDefaultPpPro extends Extension {

	public function onControllerPagesSaleOrder_UpdateData() {

		if ( has_value($this->baseObject->data['order_info']['payment_method_data']) ) {

			$payment_method_data = unserialize($this->baseObject->data['order_info']['payment_method_data']);

			if ( has_value($payment_method_data['payment_method']) && $payment_method_data['payment_method'] == 'default_pp_pro' ) {
				$this->baseObject->loadLanguage('default_pp_pro/default_pp_pro');

				// for some reason after language loading 'button_invoice' html object is removed from baseObject->data
				$this->baseObject->view->assign('button_invoice', $this->baseObject->html->buildButton(array('name' => 'btn_invoice', 'text' => $this->baseObject->language->get('text_invoice'), 'style' => 'button3',)));

				$data = array();

				$data['text_payment_status'] = $this->baseObject->language->get('text_payment_status');

				if ( strtolower($payment_method_data['PAYMENTACTION']) == 'authorization' ) {
					// show "capture" form
					$this->_get_capture_form($data, $payment_method_data);
				} else {
					// show "refund" form
					$this->_get_refund_form($data, $payment_method_data);
				}

			}
		}
	}

	private function _get_capture_form($data = array(), $payment_method_data = array()) {
		$captured_amount = has_value($payment_method_data['captured_amount']) ? (float) $payment_method_data['captured_amount'] : 0;

		if ( $captured_amount < $payment_method_data['AMT'] ) {
			$data['payment_status'] = $this->baseObject->language->get('text_pending_authorization');
			$data['pp_capture_amount'] = $this->baseObject->html->buildInput(
				array(
					'name' => 'pp_capture_amount',
					'value' => $payment_method_data['AMT'] - $captured_amount,
					'style' => 'no-save',
					'attr' => 'disabled'
				)
			);
			$data['text_capture_funds'] = $this->baseObject->language->get('text_capture_funds');
			$data['pp_capture_submit'] = $this->baseObject->html->buildButton(array(
				'text' => $this->baseObject->language->get('text_capture'),
				'name' => 'pp_capture_submit',
				'style' => 'button3'
			));



			$data['pp_capture_action'] = $this->baseObject->html->getSecureURL(
				'r/extension/default_pp_pro/capture',
				'&order_id=' . (int) $this->baseObject->data['order_info']['order_id'] .
				'&currency=' . $this->baseObject->data['currency']['code']
			);
		} else {
			$data['payment_status'] = $this->baseObject->language->get('text_completed');
		}

		if ( $captured_amount > 0 ) {

			$this->_get_refund_form($data, $payment_method_data, $captured_amount);
		} else {

			$view = new AView(Registry::getInstance(), 0);
			$view->batchAssign($data);

			$this->baseObject->view->addHookVar('order_details', $view->fetch('pages/extension/paypal_capture.tpl'));
		}
	}

	private function _get_refund_form($data = array(), $payment_method_data = array(), $not_refunded = 0 ) {
		$refunded_amount = has_value($payment_method_data['refunded_amount']) ? (float) $payment_method_data['refunded_amount'] : 0;

		if ( $not_refunded ) {
			$data['add_to_capture'] = true;
			$not_refunded = (float) $not_refunded;
		} else {
			$data['add_to_capture'] = false;
			$not_refunded = (float) $payment_method_data['AMT'];
		}

		$data['payment_status'] = $this->baseObject->language->get('text_processing');

		if ( (float) $refunded_amount > 0 ) {
			$data['payment_status'] = $this->baseObject->language->get('text_partially_refunded');
			$data['refunded_amount'] = $this->baseObject->currency->format($refunded_amount, $this->baseObject->data['currency']['code'], $this->baseObject->data['order_info']['value']);
		}

		if ( (float) $refunded_amount < $not_refunded ) {

			$data['pp_refund_amount'] = $this->baseObject->html->buildInput(
				array(
					'name' => 'pp_refund_amount',
					'value' => $not_refunded - $refunded_amount,
					'style' => 'no-save'
				)
			);
			$data['text_do_paypal_refund'] = $this->baseObject->language->get('text_do_paypal_refund');
			$data['pp_refund_submit'] = $this->baseObject->html->buildButton(array(
				'text' => $this->baseObject->language->get('text_refund'),
				'name' => 'pp_refund_submit',
				'style' => 'button3'
			));

			$params = '&order_id=' . (int) $this->baseObject->data['order_info']['order_id'] .
				'&currency=' . $this->baseObject->data['currency']['code'];

			if ( $data['add_to_capture'] ) {
				$params .= '&refund_captured=1';
			}

			$data['pp_refund_action'] = $this->baseObject->html->getSecureURL(
				'r/extension/default_pp_pro/refund',
				$params
			);


		} else {
			$data['payment_status'] = $this->baseObject->language->get('text_refunded');
		}
		$data['text_already_refunded'] = $this->baseObject->language->get('text_already_refunded');
		$data['error_wrong_amount'] = $this->baseObject->language->get('error_wrong_amount');

		$view = new AView(Registry::getInstance(), 0);
		$view->batchAssign($data);

		$this->baseObject->view->addHookVar('order_details', $view->fetch('pages/extension/paypal_refund.tpl'));
	}

}
