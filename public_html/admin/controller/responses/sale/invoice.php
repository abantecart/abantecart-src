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
if (!defined('DIR_CORE') || !IS_ADMIN) {
	header('Location: static_pages/');
}
class ControllerResponsesSaleInvoice extends AController {
	private $error = array();
	public $data = array();

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('sale/order');

		$this->data['title'] = $this->language->get('heading_title');
		$this->data['css_url'] = RDIR_TEMPLATE . 'stylesheet/invoice.css';

		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$this->data['base'] = HTTPS_SERVER;
		} else {
			$this->data['base'] = HTTP_SERVER;
		}

		$this->data['direction'] = $this->language->get('direction');
		$this->data['language'] = $this->language->get('code');

		$this->data['text_invoice'] = $this->language->get('text_invoice');

		$this->data['text_order_id'] = $this->language->get('text_order_id');
		$this->data['text_invoice_id'] = $this->language->get('text_invoice_id');
		$this->data['text_date_added'] = $this->language->get('text_date_added');
		$this->data['text_telephone'] = $this->language->get('text_telephone');
		$this->data['text_fax'] = $this->language->get('text_fax');
		$this->data['text_to'] = $this->language->get('text_to');
		$this->data['text_ship_to'] = $this->language->get('text_ship_to');

		$this->data['column_product'] = $this->language->get('column_product');
		$this->data['column_model'] = $this->language->get('column_model');
		$this->data['column_quantity'] = $this->language->get('column_quantity');
		$this->data['column_price'] = $this->language->get('column_price');
		$this->data['column_total'] = $this->language->get('column_total');
		$this->data['column_comment'] = $this->language->get('column_comment');


		if (is_file(DIR_RESOURCE . $this->config->get('config_logo'))) {
			$this->data['logo'] = HTTPS_DIR_RESOURCE . $this->config->get('config_logo');
		} else {
			$this->data['logo'] = $this->config->get('config_logo');
		}

		$this->loadModel('sale/order');

		$this->data['orders'] = array();

		$orders = array();

		if (isset($this->request->post['selected'])) {
			$orders = $this->request->post['selected'];
		} elseif (isset($this->request->get['order_id'])) {
			$orders[] = $this->request->get['order_id'];
		}

		foreach ($orders as $order_id) {
			$order_info = $this->model_sale_order->getOrder($order_id);

			if ($order_info) {
				if ($order_info['invoice_id']) {
					$invoice_id = $order_info['invoice_prefix'] . $order_info['invoice_id'];
				} else {
					$invoice_id = '';
				}

				$customer = new ACustomer($this->registry);
				$shipping_data = array(
						'firstname' => $order_info['shipping_firstname'],
						'lastname' => $order_info['shipping_lastname'],
						'company' => $order_info['shipping_company'],
						'address_1' => $order_info['shipping_address_1'],
						'address_2' => $order_info['shipping_address_2'],
						'city' => $order_info['shipping_city'],
						'postcode' => $order_info['shipping_postcode'],
						'zone' => $order_info['shipping_zone'],
						'zone_code' => $order_info['shipping_zone_code'],
						'country' => $order_info['shipping_country']
				);

				$shipping_address = $customer->getFormatedAdress($shipping_data, $order_info['shipping_address_format']);

				$payment_data = array(
						'firstname' => $order_info['payment_firstname'],
						'lastname' => $order_info['payment_lastname'],
						'company' => $order_info['payment_company'],
						'address_1' => $order_info['payment_address_1'],
						'address_2' => $order_info['payment_address_2'],
						'city' => $order_info['payment_city'],
						'postcode' => $order_info['payment_postcode'],
						'zone' => $order_info['payment_zone'],
						'zone_code' => $order_info['payment_zone_code'],
						'country' => $order_info['payment_country']
				);

				$payment_address = $customer->getFormatedAdress($payment_data, $order_info['payment_address_format']);

				$product_data = array();

				$products = $this->model_sale_order->getOrderProducts($order_id);

				foreach ($products as $product) {
					$option_data = array();

					$options = $this->model_sale_order->getOrderOptions($order_id, $product['order_product_id']);

					foreach ($options as $option) {
						$option_data[] = array(
								'name' => $option['name'],
								'value' => $option['value']
						);
					}

					$product_data[] = array(
							'name' => $product['name'],
							'model' => $product['model'],
							'option' => $option_data,
							'quantity' => $product['quantity'],
							'price' => $this->currency->format($product['price'], $order_info['currency'], $order_info['value']),
							'total' => $this->currency->format($product['total'], $order_info['currency'], $order_info['value'])
					);
				}

				$total_data = $this->model_sale_order->getOrderTotals($order_id);

				$this->data['orders'][] = array(
						'order_id' => $order_id,
						'invoice_id' => $invoice_id,
						'date_added' => dateISO2Display($order_info['date_added'], $this->language->get('date_format_short')),
						'store_name' => $order_info['store_name'],
						'store_url' => rtrim($order_info['store_url'], '/'),
						'address' => nl2br($this->config->get('config_address')),
						'telephone' => $this->config->get('config_telephone'),
						'fax' => $this->config->get('config_fax'),
						'email' => $this->config->get('store_main_email'),
						'shipping_address' => $shipping_address,
						'payment_address' => $payment_address,
						'customer_email' => $order_info['email'],
						'ip' => $order_info['ip'],
						'customer_telephone' => $order_info['telephone'],
						'comment' => $order_info['comment'],
						'product' => $product_data,
						'total' => $total_data
				);
			}
		}

		$this->view->batchAssign($this->data);
		$this->processTemplate('responses/sale/order_invoice.tpl');

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}


	public function generate() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if (!$this->user->canModify('sale/invoice')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
					array('error_text' => sprintf($this->language->get('error_permission_modify'), 'sale/invoice'),
							'reset_value' => true
					));
		}

		$this->loadModel('sale/order');

		$json = array();

		if (isset($this->request->get['order_id'])) {
			$json['invoice_id'] = $this->model_sale_order->generateInvoiceId($this->request->get['order_id']);
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($json));
	}

}
