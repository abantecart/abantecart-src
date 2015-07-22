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
/**
 * @property ModelExtension2Checkout $model_extension_2checkout
 * @property ModelCheckoutOrder $model_checkout_order
 */
class ControllerResponsesExtension2Checkout extends AController {

	public function main() {
		$this->loadLanguage('2checkout/2checkout');
		$template_data['button_confirm'] = $this->language->get('button_confirm');
		$template_data['button_back'] = $this->language->get('button_back');

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		if($this->config->get('2checkout_test')){
			$template_data['action'] = 'https://sandbox.2checkout.com/checkout/purchase';
		} else{
			$template_data['action'] = 'https://www.2checkout.com/checkout/purchase';
		}

		$template_data['sid'] = $this->config->get('2checkout_account');
		$template_data['total'] = $this->currency->format($order_info['total'], $order_info['currency'], $order_info['value'], FALSE);
		$template_data['cart_order_id'] = $this->session->data['order_id'];
		$template_data['order_number'] = $this->session->data['order_id'];
		$template_data['card_holder_name'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
		$template_data['street_address'] = $order_info['payment_address_1'];
		$template_data['city'] = $order_info['payment_city'];
		$template_data['state'] = $order_info['payment_zone'];
		$template_data['zip'] = $order_info['payment_postcode'];
		$template_data['country'] = $order_info['payment_country'];
		$template_data['email'] = $order_info['email'];
		$template_data['phone'] = $order_info['telephone'];
		if ($order_info['shipping_lastname']) {
			$template_data['ship_name'] = $order_info['shipping_firstname'] . ' ' . $order_info['shipping_lastname'];
		} else {
			$template_data['ship_name'] = $order_info['firstname'] . ' ' . $order_info['lastname'];
		}

		if ($this->cart->hasShipping()) {
			$template_data['ship_street_address'] = $order_info['shipping_address_1'];
			$template_data['ship_city'] = $order_info['shipping_city'];
			$template_data['ship_state'] = $order_info['shipping_zone'];
			$template_data['ship_zip'] = $order_info['shipping_postcode'];
			$template_data['ship_country'] = $order_info['shipping_country'];
		} else {
			$template_data['ship_street_address'] = $order_info['payment_address_1'];
			$template_data['ship_city'] = $order_info['payment_city'];
			$template_data['ship_state'] = $order_info['payment_zone'];
			$template_data['ship_zip'] = $order_info['payment_postcode'];
			$template_data['ship_country'] = $order_info['payment_country'];
		}

		$template_data['products'] = array();

		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$template_data['products'][] = array(
				'product_id' => $product['product_id'],
				'name' => $product['name'],
				'description' => $product['name'],
				'quantity' => $product['quantity'],
				'price' => $this->currency->format($product['price'], $order_info['currency'], $order_info['value'], FALSE)
			);
		}

		if ($this->config->get('2checkout_test')) {
			$template_data['demo'] = 'Y';
		}

		$template_data['lang'] = $this->session->data['language'];

		if ($this->request->get['rt'] != 'checkout/guest_step_3') {
			$template_data['back'] = $this->html->getSecureURL('checkout/payment');
		} else {
			$template_data['back'] = $this->html->getSecureURL('checkout/guest_step_2');
		}
		$this->view->batchAssign($template_data);
		$this->processTemplate('responses/2checkout.tpl');
	}

	public function callback() {
		if ($this->request->is_GET()) {
			$this->redirect($this->html->getURL('index/home'));
		}

		$this->load->model('checkout/order');

		$order_id = (int)$this->request->post['vendor_order_id'];
		$order_info = $this->model_checkout_order->getOrder($order_id);
		if (!$order_info) {
			return null;
		}

		$post  = $this->request->post;

		$this->load->model('extension/2checkout');
		// hash check
		if (!md5($post['sale_id'] . $this->config->get('2checkout_account') . $post['invoice_id'] . $this->config->get('2checkout_secret')) == strtolower($post['md5_hash'])) {
			exit;
		}

		if ($post['message_type'] == 'ORDER_CREATED') {
			$this->model_checkout_order->confirm((int)$post['vendor_order_id'], $this->config->get('2checkout_order_status_id'));
			} elseif ($post['message_type'] == 'REFUND_ISSUED') {
			$order_status_id = $this->model_extension_2checkout->getOrderStatusIdByName('failed');
			$this->model_checkout_order->update((int)$post['vendor_order_id'], $order_status_id,'Status changed by 2Checkout INS');
		} elseif ($post['message_type'] == 'FRAUD_STATUS_CHANGED' && $post['fraud_status'] == 'pass') {
			$order_status_id = $this->model_extension_2checkout->getOrderStatusIdByName('processing');
			$this->model_checkout_order->update((int)$post['vendor_order_id'], $order_status_id,'Status changed by 2Checkout INS');
		} elseif ($post['message_type'] == 'SHIP_STATUS_CHANGED' && $post['ship_status'] == 'shipped') {
			$order_status_id = $this->model_extension_2checkout->getOrderStatusIdByName('complete');
			$this->model_checkout_order->update((int)$post['vendor_order_id'], $order_status_id,'Status changed by 2Checkout INS');
		} else {
			$this->redirect($this->html->getURL('checkout/confirm'));
		}
	}
}