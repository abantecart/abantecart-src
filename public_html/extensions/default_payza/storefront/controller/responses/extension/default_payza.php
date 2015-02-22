<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

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

class ControllerResponsesExtensionDefaultPayza extends AController {
	public function main() {
		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['button_back'] = $this->language->get('button_back');

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$form = new AForm();
		$form->setForm(array( 'form_name' => 'checkout' ));

		if($this->config->get('default_payza_test_mode')){
			$action = 'https://sandbox.Payza.com/sandbox/payprocess.aspx';
		}else{
			$action = 'https://secure.payza.com/checkout';
		}

		$data['form']['form_open'] = $form->getFieldHtml(array( 
														'type' => 'form',
														'name' => 'checkout',
														'action' => $action
														));
		$fs = array();														
		$fs['ap_purchasetype'] = $form->getFieldHtml(array( 
														'type' => 'hidden',
		                                                'name' => 'ap_purchasetype',
		                                                'value' => 'Item'
		                                                ));
		$fs['ap_merchant'] = $form->getFieldHtml(array(
														'type' => 'hidden',
														'name' => 'ap_merchant',
														'value' => $this->config->get('default_payza_merchant'),
														));

		$fs['ap_currency'] = $form->getFieldHtml(array(
														'type' => 'hidden',
														'name' => 'ap_currency',
														'value' => $order_info['currency'],
														));
		$fs['apc_1'] = $form->getFieldHtml(array(
														'type' => 'hidden',
														'name' => 'apc_1',
														'value' => $this->session->data['order_id']
														));

		//product details
		$products = $this->cart->getProducts();
		$icount = 0;
		foreach ($products as $product) {
			$option_data = '';
			foreach ($product['option'] as $option) {
				if ($option['type'] != 'file') {
					$value = $option['value'];
				} else {
					$filename = $encryption->decrypt($option['value']);
					$value = mb_substr($filename, 0, mb_strrpos($filename, '.'));
				}
				$option_data .= ' - '.$option['name'].' : '.(mb_strlen($value) > 20 ? mb_substr($value, 0, 20) . '..' : $value);
			}
			
			$postfix = '';
			if ($icount > 0) {
				$postfix = "_$icount";
			}
			$fs['ap_itemname'.$postfix] = $form->getFieldHtml(array(
														'type' => 'hidden',
														'name' => 'ap_itemname'.$postfix,
														'value' => $product['name'],
														));
			$fs['ap_description'.$postfix] = $form->getFieldHtml(array(
														'type' => 'hidden',
														'name' => 'ap_description'.$postfix,
														'value' => $option_data,
														));
			$fs['ap_itemcode'.$postfix] = $form->getFieldHtml(array(
														'type' => 'hidden',
														'name' => 'ap_itemcode'.$postfix,
														'value' => $product['model'],
														));
			$fs['ap_quantity'.$postfix] = $form->getFieldHtml(array(
														'type' => 'hidden',
														'name' => 'ap_quantity'.$postfix,
														'value' => $product['quantity'],
														));
			
			$fs['ap_amount'.$postfix] = $form->getFieldHtml(array(
														'type' => 'hidden',
														'name' => 'ap_amount'.$postfix,
														'value' => $this->currency->format($product['price'], $order_info['currency'], $order_info['value'], FALSE),
														));			
			$icount++;
		}

		//total and discounts
		$discount_amount_cart = 0;
		$tax_total = 0;
		$shipping_total = 0;
		$handling_total = 0;
		$items_total = 0;
		$totals = $this->cart->buildTotalDisplay();

		foreach($totals['total_data'] as $total){
			if(in_array($total['id'],array('subtotal','total'))){ continue;}
			if(in_array($total['id'],array('promotion','coupon'))){
				$total['value'] = $total['value']<0 ? $total['value']*-1 : $total['value'];
				$discount_amount_cart += $total['value'];
			}else{
				$price = $this->currency->format($total['value'], $order_info['currency'], $order_info['value'], FALSE);

			    if(in_array($total['id'],array('tax'))){
					$tax_total += $price;
				}elseif(in_array($total['id'],array('shipping'))){
					$shipping_total += $price;
				}elseif(in_array($total['id'],array('handling'))){
					$handling_total += $price;
				}else{
				    $items_total += $price;
			    }
			}
		}

		$calc_total = $items_total + $shipping_total + $tax_total + $handling_total;
		
		$fs['ap_taxamount'] = $form->getFieldHtml(array(
														'type' => 'hidden',
														'name' => 'ap_taxamount',
														'value' => $tax_total,
														));
		$fs['ap_shippingcharges'] = $form->getFieldHtml(array(
														'type' => 'hidden',
														'name' => 'ap_shippingcharges',
														'value' => $shipping_total,
														));

		$fs['ap_additionalcharges'] = $form->getFieldHtml(array(
														'type' => 'hidden',
														'name' => 'ap_additionalcharges',
														'value' => $handling_total,
														));

		$fs['ap_discountamount'] = $form->getFieldHtml(array(
														'type' => 'hidden',
														'name' => 'ap_discountamount',
														'value' => $discount_amount_cart,
														));

		$fs['ap_totalamount'] = $form->getFieldHtml(array(
														'type' => 'hidden',
														'name' => 'ap_totalamount',
														'value' => $calc_total,
														));

		$fs['ap_returnurl'] = $form->getFieldHtml(array( 
														'type' => 'hidden',
														'name' => 'ap_returnurl',
														'value' => $this->html->getSecureURL('checkout/success'),
														));

		$back = $this->request->get['rt'] != 'checkout/guest_step_3' ? $this->html->getSecureURL('checkout/payment')
				: $this->html->getSecureURL('checkout/guest_step_2');

		$fs['ap_cancelurl'] = $form->getFieldHtml(array(
														'type' => 'hidden',
														'name' => 'ap_cancelurl',
														'value' => $back,
														));

		$fs['ap_alerturl'] = $form->getFieldHtml(array( 
														'type' => 'hidden',
														'name' => 'ap_alerturl',
														'value' => $this->html->getSecureURL('extension/default_payza/callback'),
														));

		$data['form']['fields'] = $fs;

		$data['form']['back'] = $form->getFieldHtml(array( 'type' => 'button',
		                                                     'name' => 'back',
		                                                     'text' => $this->language->get('button_back'),
		                                                     'style' => 'button',
		                                                     'href' => $back ));
		$data['form']['submit'] = $form->getFieldHtml(array( 'type' => 'submit',
		                                                       'name' => $this->language->get('button_confirm')
		                                                  ));

		$this->view->batchAssign($data);
		$this->processTemplate('responses/default_payza.tpl');
	}

	//This is Payza IPN callback
	public function callback() {
		if (has_value($this->request->post['ap_securitycode']) && ($this->request->post['ap_securitycode'] == $this->config->get('default_payza_security'))) {
			$this->load->model('checkout/order');
			$this->model_checkout_order->confirm($this->request->post['apc_1'], $this->config->get('default_payza_order_status_id'));
		}
	}
}
