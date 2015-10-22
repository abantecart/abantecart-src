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
if (!defined('DIR_CORE')){
	header('Location: static_pages/');
}

/**
 * Class ControllerPagesAccountInvoice
 */
class ControllerPagesAccountInvoice extends AController{
	public $data = array ();
	public function main(){

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if (!$this->customer->isLogged()){
			if (isset($this->request->get['order_id'])){
				$order_id = $this->request->get['order_id'];
			} else{
				$order_id = 0;
			}

			$this->session->data['redirect'] = $this->html->getSecureURL('account/invoice', '&order_id=' . $order_id);
			$this->redirect($this->html->getSecureURL('account/login'));
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->resetBreadcrumbs();

		$this->document->addBreadcrumb(array (
				'href'      => $this->html->getURL('index/home'),
				'text'      => $this->language->get('text_home'),
				'separator' => false
		));

		$this->document->addBreadcrumb(array (
				'href'      => $this->html->getURL('account/account'),
				'text'      => $this->language->get('text_account'),
				'separator' => $this->language->get('text_separator')
		));

		$this->document->addBreadcrumb(array (
				'href'      => $this->html->getURL('account/history'),
				'text'      => $this->language->get('text_history'),
				'separator' => $this->language->get('text_separator')
		));

		$this->document->addBreadcrumb(array (
				'href'      => $this->html->getURL('account/invoice', '&order_id=' . $this->request->get['order_id']),
				'text'      => $this->language->get('text_invoice'),
				'separator' => $this->language->get('text_separator')
		));

		$this->data['success'] = '';
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}

		$this->loadModel('account/order');

		if (isset($this->request->get['order_id'])){
			$order_id = $this->request->get['order_id'];
		} else{
			$order_id = 0;
		}

		$order_info = $this->model_account_order->getOrder($order_id);

		if ($order_info){
			$this->data['order_id'] = $order_id;
			$this->data['invoice_id'] = $order_info['invoice_id'] ? $order_info['invoice_prefix'] . $order_info['invoice_id'] : '';

			$this->data['email'] = $order_info['email'];
			$this->data['telephone'] = $order_info['telephone'];
			$this->data['fax'] = $order_info['fax'];

			$this->data['status'] = $this->model_account_order->getOrderStatus($order_id);

			$shipping_data = array (
					'firstname' => $order_info['shipping_firstname'],
					'lastname'  => $order_info['shipping_lastname'],
					'company'   => $order_info['shipping_company'],
					'address_1' => $order_info['shipping_address_1'],
					'address_2' => $order_info['shipping_address_2'],
					'city'      => $order_info['shipping_city'],
					'postcode'  => $order_info['shipping_postcode'],
					'zone'      => $order_info['shipping_zone'],
					'zone_code' => $order_info['shipping_zone_code'],
					'country'   => $order_info['shipping_country']
			);

			$this->data['shipping_address'] = $this->customer->getFormatedAdress($shipping_data, $order_info['shipping_address_format']);
			$this->data['shipping_method'] = $order_info['shipping_method'];

			$payment_data = array (
					'firstname' => $order_info['payment_firstname'],
					'lastname'  => $order_info['payment_lastname'],
					'company'   => $order_info['payment_company'],
					'address_1' => $order_info['payment_address_1'],
					'address_2' => $order_info['payment_address_2'],
					'city'      => $order_info['payment_city'],
					'postcode'  => $order_info['payment_postcode'],
					'zone'      => $order_info['payment_zone'],
					'zone_code' => $order_info['payment_zone_code'],
					'country'   => $order_info['payment_country']
			);

			$this->data['payment_address'] = $this->customer->getFormatedAdress($payment_data, $order_info['payment_address_format']);
			$this->data['payment_method'] = $order_info['payment_method'];

			$products = array ();

			$order_products = $this->model_account_order->getOrderProducts($order_id);
			$resource = new AResource('image');
			foreach ($order_products as $product){
				$options = $this->model_account_order->getOrderOptions($order_id, $product['order_product_id']);

				$thumbnail = $resource->getMainThumb('products',
						$product['product_id'],
						$this->config->get('config_image_cart_width'),
						$this->config->get('config_image_cart_height'),
						false);

				$option_data = array ();

				foreach ($options as $option){
					if ($option['element_type'] == 'H'){
						continue;
					} //hide hidden options

					$value = $option['value'];
					$title = '';
					// hide binary value for checkbox
					if ($option['element_type'] == 'C' && in_array($value, array (0, 1))){
						$value = '';
					}
					// strip long textarea value
					if ($option['element_type'] == 'T'){
						$title = strip_tags($value);
						$title = str_replace('\r\n', "\n", $title);

						$value = str_replace('\r\n', "\n", $value);
						if (mb_strlen($value) > 64){
							$value = mb_substr($value, 0, 64) . '...';
						}
					}

					$option_data[] = array (
							'name'  => $option['name'],
							'value' => $value,
							'title' => $title
					);
				}

				$products[] = array (
						'id'        => $product['product_id'],
						'thumbnail' => $thumbnail,
						'name'      => $product['name'],
						'model'     => $product['model'],
						'option'    => $option_data,
						'quantity'  => $product['quantity'],
						'price'     => $this->currency->format($product['price'], $order_info['currency'], $order_info['value']),
						'total'     => $this->currency->format($product['total'], $order_info['currency'], $order_info['value'])
				);
			}
			$this->data['products'] = $products;
			$this->data['totals'] = $this->model_account_order->getOrderTotals($order_id);
			$this->data['comment'] = $order_info['comment'];
			$this->data['product_link'] = $this->html->getSecureURL('product/product', '&product_id=%ID%');

			$historys = array ();
			$results = $this->model_account_order->getOrderHistories($order_id);
			foreach ($results as $result){
				$historys[] = array (
						'date_added' => dateISO2Display($result['date_added'], $this->language->get('date_format_short') . ' ' . $this->language->get('time_format')),
						'status'     => $result['status'],
						'comment'    => nl2br($result['comment'])
				);
			}
			$this->data['historys'] = $historys;
			$this->data['continue'] = $this->html->getSecureURL('account/history');
			$this->data['button_print'] = $this->html->buildElement(
					array ('type'  => 'button',
					       'name'  => 'print_button',
					       'text'  => $this->language->get('button_print'),
					       'icon'  => 'fa fa-print',
					       'style' => 'button'));

			//button for order cancelation
			if($this->config->get('config_customer_cancelation_order_status_id')){
				$order_cancel_ids = unserialize($this->config->get('config_customer_cancelation_order_status_id'));
				if(in_array($order_info['order_status_id'], $order_cancel_ids)){
					$this->data['button_order_cancel'] = $this->html->buildElement(
										array ('type'  => 'button',
										       'name'  => 'button_order_cancelation',
										       'text'  => $this->language->get('text_order_cancelation'),
										       'icon'  => 'fa fa-ban',
										       'style' => 'button'));
					$this->data['order_cancelation_url'] = $this->html->getSecureURL('account/invoice/CancelOrder', '&order_id='.$order_id);
				}
			}


			$this->view->setTemplate('pages/account/invoice.tpl');
		} else{
			$this->data['continue'] = $this->html->getSecureURL('account/account');
			$this->view->setTemplate('pages/error/not_found.tpl');
		}

		$this->data['button_continue'] = $this->html->buildElement(
				array ('type'  => 'button',
				       'name'  => 'continue_button',
				       'text'  => $this->language->get('button_continue'),
				       'icon'  => 'fa fa-arrow-right',
				       'style' => 'button'));

		$this->view->batchAssign($this->data);
		$this->processTemplate();

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function CancelOrder(){
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		//do a few checks
		//is order exists
		$order_id = (int)$this->request->get['order_id'];
		if(!$order_id){
			$this->redirect($this->html->getSecureURL('account/invoice'));
		}
		//is customer logged
		$customer_id = $this->customer->getId();
		if(!$customer_id){
			$this->redirect($this->html->getSecureURL('account/login'));
		}
		//is order of customer
		$this->loadModel('account/order');
		$order_info = $this->model_account_order->getOrder($order_id);
		if(!$order_info){
			$this->redirect($this->html->getSecureURL('account/invoice'));
		}
		//is cancelation enabled at all
		if($this->config->get('config_customer_cancelation_order_status_id')){
			$order_cancel_ids = unserialize($this->config->get('config_customer_cancelation_order_status_id'));
		}
		//is cancelation allowed for current order status
		if(!$order_cancel_ids || !in_array($order_info['order_status_id'], $order_cancel_ids)){
			$this->redirect($this->html->getSecureURL('account/invoice'));
		}

		//now do change

		$new_order_status_id = $this->order_status->getStatusByTextId('canceled_by_customer');
		if($new_order_status_id){
			$this->loadModel('checkout/order');
			$this->model_checkout_order->update($order_id, $new_order_status_id, 'Request an Order cancellation from Customer', true);
			$this->session->data['success'] = $this->language->get('text_order_cancelation_success');

			$this->messages->saveNotice(
					sprintf( $this->language->get('text_order_cancelation_message_title'),
							$order_id
					),
					sprintf( $this->language->get('text_order_cancelation_message_body'),
							$this->customer->getFirstName().' '.$this->customer->getLastName(),
							$order_id,
							'#admin#rt=sale/order/details&order_id='.$order_id
							)
			);
		}else{
			//when new order status id is null by some unexpected reason - just redirect on the same page
			$this->log->write('Error: Unknown cancelation order status id. Probably integrity code problem. Check is file /core/lib/order_status.php exists.');
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->redirect($this->html->getSecureURL('account/invoice', '&order_id='.$order_id));
	}
}
