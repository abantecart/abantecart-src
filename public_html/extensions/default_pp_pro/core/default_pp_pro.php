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

if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

/**
 * Class ExtensionDefaultPpPro
 */
class ExtensionDefaultPpPro extends Extension {

	protected $registry;
	protected $pp_data;

	public function  __construct() {
		$this->registry = Registry::getInstance();
	}

	//Hook to extension edit in the admin
	public function onControllerPagesExtensionExtensions_UpdateData() {

		$that = $this->baseObject;
		$current_ext_id = $that->request->get['extension'];
	    if ( IS_ADMIN && $current_ext_id == 'default_pp_pro' && $this->baseObject_method == 'edit' ) {
	    	$html = '<a class="btn btn-white tooltips"
						target="_blank"
						href="https://www.paypal.com/us/webapps/mpp/referral/paypal-payments-pro?partner_id=V5VQZUVNK5RT6"
						title="Visit Paypal"><i class="fa fa-external-link fa-lg"></i></a>';

	    	$that->view->addHookVar('extension_toolbar_buttons', $html);
		}
	}


		//Hook to enable payment details tab in admin
	public function onControllerPagesSaleOrderTabs_UpdateData() {
		$that = $this->baseObject;
		$order_id = $that->data['order_id'];
		//are we logged in and in admin?
	    if ( IS_ADMIN && $that->user->isLogged() ) {
	    	//check if tab is not yet enabled.
	    	if ( in_array('payment_details', $that->data['groups'])) {
	    		return null;
	    	}

	    	$that->data['groups'][] = 'payment_details';
	    	$that->data['link_payment_details'] = $that->html->getSecureURL('sale/order/payment_details', '&order_id=' . $order_id.'&extension=default_pp_express');
			//reload main view data with updated tab
			$that->view->batchAssign( $that->data );
	    }
	}

	//Hook to payment details page to show information
	public function onControllerPagesSaleOrder_UpdateData() {
		$that = $this->baseObject;
		//are we logged to admin and correct method called?
	    if ( IS_ADMIN && $that->user->isLogged() && $this->baseObject_method == 'payment_details' && has_value($that->data['order_info']['payment_method_data']) ) {

			$payment_method_data = unserialize($that->data['order_info']['payment_method_data']);

			if ( has_value($payment_method_data['payment_method']) && $payment_method_data['payment_method'] == 'default_pp_pro' ) {
				$that->loadLanguage('default_pp_pro/default_pp_pro');

				// for some reason after language loading 'button_invoice' html object is removed from baseObject->data
				$that->view->assign('button_invoice', $that->html->buildButton( array(
						'name' => 'btn_invoice',
						'text' => $that->language->get('text_invoice')
				)));

				$data = array();
				$data['text_payment_status'] = $that->language->get('text_payment_status');
				if ( strtolower($payment_method_data['PAYMENTACTION']) == 'authorization' ) {
					// show "capture" form
					$tpl_data = $this->_get_capture_form($data, $payment_method_data);
				} else {
					// show "refund" form
					$tpl_data = $this->_get_refund_form($data, $payment_method_data);
				}

				$view = new AView($this->registry, 0);
				$view->batchAssign($that->language->getASet('default_pp_pro/default_pp_pro'));
				$view->batchAssign($tpl_data);
				$that->view->addHookVar('extension_payment_details', $view->fetch('pages/sale/pp_pro_payment_details.tpl'));
			}
		}
	}

	/**
	 * @param array $data
	 * @param array $payment_method_data
	 * @return array
	 */
	private function _get_capture_form($data = array(), $payment_method_data = array()) {
		$that = $this->baseObject;

		$captured_amount = has_value($payment_method_data['captured_amount']) ? (float) $payment_method_data['captured_amount'] : 0;

		if ( $captured_amount < $payment_method_data['AMT'] ) {
			$data['payment_status'] = $that->language->get('text_pending_authorization');
			$data['pp_capture_amount'] = $that->html->buildInput(
				array(
					'name' => 'pp_capture_amount',
					'value' => $payment_method_data['AMT'] - $captured_amount,
					'style' => 'no-save',
					'attr' => 'disabled'
				)
			);
			$data['text_capture_funds'] = $that->language->get('text_capture_funds');
			$data['pp_capture_submit'] = $that->html->buildElement(array(
				'type' => 'button',
				'text' => $that->language->get('text_capture'),
				'name' => 'pp_capture_submit'
			));

			$data['pp_capture_action'] = $that->html->getSecureURL(
				'r/extension/default_pp_pro/capture',
				'&order_id=' . (int) $that->data['order_info']['order_id'] .
				'&currency=' . $that->data['currency']['code']
			);
		} else {
			$data['payment_status'] = $that->language->get('text_completed');
		}

		if ( $captured_amount > 0 ) {

			return $this->_get_refund_form($data, $payment_method_data, $captured_amount);
		} else {
			return $data;
		}
	}

	/**
	 * @param array $data
	 * @param array $payment_method_data
	 * @param int $not_refunded
	 * @return array
	 */
	private function _get_refund_form($data = array(), $payment_method_data = array(), $not_refunded = 0 ) {
		$that = $this->baseObject;
		$refunded_amount = has_value($payment_method_data['refunded_amount']) ? (float) $payment_method_data['refunded_amount'] : 0;

		if ( $not_refunded ) {
			$data['add_to_capture'] = true;
			$not_refunded = (float) $not_refunded;
		} else {
			$data['add_to_capture'] = false;
			$not_refunded = (float) $payment_method_data['AMT'];
		}

		$data['payment_status'] = $that->language->get('text_processing');

		if ( (float) $refunded_amount > 0 ) {
			$data['payment_status'] = $that->language->get('text_partially_refunded');
			$data['refunded_amount'] = $that->currency->format($refunded_amount, $that->data['currency']['code'], $that->data['order_info']['value']);
		}

		if ( (float) $refunded_amount < $not_refunded ) {

			$data['pp_refund_amount'] = $that->html->buildInput(
				array(
					'name' => 'pp_refund_amount',
					'value' => $not_refunded - $refunded_amount,
					'style' => 'no-save'
				)
			);
			$data['text_do_paypal_refund'] = $that->language->get('text_do_paypal_refund');
			$data['pp_refund_submit'] = $that->html->buildElement(array(
				'type' => 'button',
				'text' => $that->language->get('text_refund'),
				'title' => $that->language->get('text_refund'),
				'name' => 'pp_refund_submit'
			));

			$params = '&order_id=' . (int) $that->data['order_info']['order_id'] .
				'&currency=' . $that->data['currency']['code'];

			if ( $data['add_to_capture'] ) {
				$params .= '&refund_captured=1';
			}

			$data['pp_refund_action'] = $that->html->getSecureURL(
				'r/extension/default_pp_pro/refund',
				$params
			);


		} else {
			$data['payment_status'] = $that->language->get('text_refunded');
		}
		$data['text_already_refunded'] = $that->language->get('text_already_refunded');
		$data['error_wrong_amount'] = $that->language->get('error_wrong_amount');

		return $data;
	}

}
