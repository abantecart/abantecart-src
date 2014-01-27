<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
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

class ControllerResponsesExtensionDefaultAlertPay extends AController {
	public function main() {
		$data[ 'button_confirm' ] = $this->language->get('button_confirm');
		$data[ 'button_back' ] = $this->language->get('button_back');

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data[ 'order_id' ]);

		$form = new AForm();
		$form->setForm(array( 'form_name' => 'checkout' ));
		$data[ 'form' ][ 'form_open' ] = $form->getFieldHtml(array( 'type' => 'form',
		                                                          'name' => 'checkout',
		                                                          'action' => 'https://www.alertpay.com/PayProcess.aspx' ));
		$data[ 'form' ][ 'ap_merchant' ] = $form->getFieldHtml(array( 'type' => 'hidden',
		                                                            'name' => 'ap_merchant',
		                                                            'value' => $this->config->get('default_alertpay_merchant'),
		                                                       ));
		$data[ 'form' ][ 'ap_amount' ] = $form->getFieldHtml(array( 'type' => 'hidden',
		                                                          'name' => 'ap_amount',
		                                                          'value' => $this->currency->format($order_info[ 'total' ], $order_info[ 'currency' ], $order_info[ 'value' ], FALSE),
		                                                     ));
		$data[ 'form' ][ 'ap_currency' ] = $form->getFieldHtml(array( 'type' => 'hidden',
		                                                            'name' => 'ap_currency',
		                                                            'value' => $order_info[ 'currency' ],
		                                                       ));
		$data[ 'form' ][ 'ap_purchasetype' ] = $form->getFieldHtml(array( 'type' => 'hidden',
		                                                                'name' => 'ap_purchasetype',
		                                                                'value' => 'Item',
		                                                           ));
		$data[ 'form' ][ 'ap_itemname' ] = $form->getFieldHtml(array( 'type' => 'hidden',
		                                                            'name' => 'ap_itemname',
		                                                            'value' => $this->config->get('store_name') . ' - #' . $this->session->data[ 'order_id' ],
		                                                       ));
		$data[ 'form' ][ 'ap_itemcode' ] = $form->getFieldHtml(array( 'type' => 'hidden',
		                                                            'name' => 'ap_itemcode',
		                                                            'value' => $this->session->data[ 'order_id' ],
		                                                       ));
		$data[ 'form' ][ 'ap_returnurl' ] = $form->getFieldHtml(array( 'type' => 'hidden',
		                                                             'name' => 'ap_returnurl',
		                                                             'value' => $this->html->getSecureURL('checkout/success'),
		                                                        ));

		$back = $this->request->get[ 'rt' ] != 'checkout/guest_step_3' ? $this->html->getSecureURL('checkout/payment')
				: $this->html->getSecureURL('checkout/guest_step_2');

		$data[ 'form' ][ 'ap_cancelurl' ] = $form->getFieldHtml(array( 'type' => 'hidden',
		                                                             'name' => 'ap_cancelurl',
		                                                             'value' => $back,
		                                                        ));
		$data[ 'form' ][ 'back' ] = $form->getFieldHtml(array( 'type' => 'button',
		                                                     'name' => 'back',
		                                                     'text' => $this->language->get('button_back'),
		                                                     'style' => 'button',
		                                                     'href' => $back ));
		$data[ 'form' ][ 'submit' ] = $form->getFieldHtml(array( 'type' => 'submit',
		                                                       'name' => $this->language->get('button_confirm')
		                                                  ));


		$this->view->batchAssign($data);
		$this->processTemplate('responses/default_alertpay.tpl');
	}

	public function callback() {
		if (isset($this->request->post[ 'ap_securitycode' ]) && ($this->request->post[ 'ap_securitycode' ] == $this->config->get('default_alertpay_security'))) {
			$this->load->model('checkout/order');

			$this->model_checkout_order->confirm($this->request->post[ 'ap_itemcode' ], $this->config->get('default_alertpay_order_status_id'));
		}
	}
}

?>