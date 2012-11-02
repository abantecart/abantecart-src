<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  &lt;http://www.opensource.org/licenses/OSL-3.0&gt;

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/

if (!defined('DIR_CORE')) {
	header('Location: static_pages/');
}

class ControllerResponsesExtensionGoogleCheckout extends AController {
	public $data = array();

	public function main() {

		$this->load->language('google_checkout/google_checkout');

		if (!$this->config->get('google_checkout_test')) {
			$this->data[ 'action' ] = 'https://checkout.google.com/api/checkout/v2/checkout/Merchant/' . $this->config->get('google_checkout_merchant_id');
		} else {
			$this->data[ 'action' ] = 'https://sandbox.google.com/checkout/api/checkout/v2/checkout/Merchant/' . $this->config->get('google_checkout_merchant_id');
		}

		$this->loadModel('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data[ 'order_id' ]);

		if ($order_info) {
			$xml = '<?xml version="1.0" encoding="UTF-8"?>';
			$xml .= '<checkout-shopping-cart xmlns="http://checkout.google.com/schema/2">';
			$xml .= '	<shopping-cart>';
			$xml .= '   	<merchant-private-data>order_id=' . $this->session->data[ 'order_id' ].'</merchant-private-data>';
			$xml .= '<items>';

			$products = $this->cart->getProducts();

			foreach ($products as $product) {
				$xml .= '			<item>';
				$xml .= '				<merchant-item-id>' . $product[ 'product_id' ] . '</merchant-item-id>';

				$option_data = array();

				foreach ($product[ 'option' ] as $option) {
					$option_data[ ] = $option[ 'name' ] . ': ' . $option[ 'value' ];
				}

				if ($option_data) {
					$xml .= '				<item-name>' . $product[ 'name' ] . ' ' . implode('; ', $option_data) . '</item-name>';
					$xml .= '				<item-description>' . $product[ 'name' ] . ' ' . implode('; ', $option_data) . '</item-description>';
				} else {
					$xml .= '				<item-name>' . $product[ 'name' ] . '</item-name>';
					$xml .= '				<item-description>' . $product[ 'name' ] . '</item-description>';
				}

				$xml .= '				<unit-price currency="' . $this->currency->getCode() . '">' . $this->currency->format($product[ 'price' ], $this->currency->getCode(), false, false) . '</unit-price>';
				$xml .= '				<quantity>' . $product[ 'quantity' ] . '</quantity>';
				$xml .= '			</item>';
			}

			$xml .= '		</items>';
			$xml .= '	</shopping-cart>';

			if ($this->cart->hasShipping()) {
				$xml .= '	<checkout-flow-support>';
				$xml .= '		<merchant-checkout-flow-support>';
				$xml .= '			<shipping-methods>';
				$xml .= '				<flat-rate-shipping name="' . $this->session->data[ 'shipping_method' ][ 'title' ] . '">';
				$xml .= '					<price currency="' . $this->currency->getCode() . '">' . $this->currency->format($this->session->data[ 'shipping_method' ][ 'cost' ], $this->currency->getCode(), false, false) . '</price>';
				$xml .= '				</flat-rate-shipping>';
				$xml .= '			</shipping-methods>';
				$xml .= '		</merchant-checkout-flow-support>';
				$xml .= '	</checkout-flow-support>';
			}

			$xml .= '</checkout-shopping-cart>';

			$key = $this->config->get('google_checkout_merchant_key');
			$blocksize = 64;
			$hash = 'sha1';

			if (strlen($key) > $blocksize) {
				$key = pack('H*', $hash($key));
			}

			$key = str_pad($key, $blocksize, chr(0x00));
			$ipad = str_repeat(chr(0x36), $blocksize);
			$opad = str_repeat(chr(0x5c), $blocksize);
			$hmac = pack('H*', $hash(($key ^ $opad) . pack('H*', $hash(($key ^ $ipad) . $xml))));

			$this->data[ 'cart' ] = base64_encode($xml);
			$this->data[ 'signature' ] = base64_encode($hmac);
			$this->data[ 'continue_url' ] = $this->html->getSecureURL('checkout/success');

			$item = HtmlElementFactory::create(array( 'type' => 'button',
				'name' => 'back',
				'style' => 'button',
				'text' => $this->language->get('button_back') ));
			$item = $item->getHtml();
			$this->view->assign('button_back', $item);

			$item = HtmlElementFactory::create(array( 'type' => 'button',
				'name' => 'checkout',
				'style' => 'button',
				'text' => $this->language->get('button_confirm') ));
			$item = $item->getHtml();
			$this->view->assign('button_confirm', $item);

			$this->view->assign('continue', $this->html->getSecureURL('checkout/success'));

			if ($this->request->get[ 'rt' ] != 'checkout/guest_step_3') {
				$this->view->assign('back', $this->html->getSecureURL('checkout/payment'));
			} else {
				$this->view->assign('back', $this->html->getSecureURL('checkout/guest_step_2'));
			}


			$this->view->batchAssign($this->data);


			$this->processTemplate('responses/google_checkout/google_checkout.tpl');
		}
	}

	public function callback() {
		// this method wait order_id from Google notifier and then change order status id
		$order_id = (int) str_replace('order_id=','',$this->request->post['shopping-cart_merchant-private-data']);
		if($order_id){
			$this->load->model('checkout/order');
			$order_info = $this->model_checkout_order->getOrder($order_id);
			if(!$order_info){
				$warning = new AWarning('Warning: Unknown order_id of Google Checkout callback (order_id: #'.$order_id.')');
				$warning->toLog()->toMessages();
				return;
		}
		$this->model_checkout_order->confirm($order_id, $this->config->get('google_checkout_order_status_id'));
		}
	}
}
