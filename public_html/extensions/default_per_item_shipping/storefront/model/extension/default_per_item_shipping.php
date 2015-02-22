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

class ModelExtensionDefaultPerItemShipping extends Model {
	public function getQuote($address) {
		$this->load->language('default_per_item_shipping/default_per_item_shipping');

		if ($this->config->get('default_per_item_shipping_status')) {

			if (!$this->config->get('default_per_item_shipping_location_id')) {
				$status = TRUE;
			} else {
				$query = $this->db->query("SELECT *
											FROM " . $this->db->table('zones_to_locations') . "
											WHERE location_id = '" . (int)$this->config->get('default_per_item_shipping_location_id') . "'
												AND country_id = '" . (int)$address['country_id'] . "'
												AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
				if ($query->num_rows) {
					$status = TRUE;
				} else {
					$status = FALSE;
				}
			}
		} else {
			$status = FALSE;
		}

		$method_data = array();
		if (!$status) {
			return $method_data;
		}

		$cost = 0;

		//Process all products shipped together with not special shipping settings on a product level
		$b_products = $this->cart->basicShippingProducts();
		if (count($b_products) > 0) {
			foreach ($b_products as $prd) {
				$cost += $this->config->get('default_per_item_shipping_cost') * $prd['quantity'];
			}
		}

		//Process products that have special shipping settings
		$special_ship_products = $this->cart->specialShippingProducts();
		foreach ($special_ship_products as $product) {
			if ($product['free_shipping']) {
				continue;
			} else if ($product['shipping_price'] > 0) {
				$cost += $product['shipping_price'] * $product['quantity'];
			} else {
				$cost += $this->config->get('default_per_item_shipping_cost') * $product['quantity'];
			}
		}

		$quote_data = array();

		$cost_text = $this->language->get('text_free');
		if ($cost) {
			$cost_text = $this->currency->format(
				$this->tax->calculate(	$cost, 
										$this->config->get('default_per_item_shipping_tax'), 
										$this->config->get('config_tax')
										)
				);
		}

		$quote_data['default_per_item_shipping'] = array(
				'id' => 'default_per_item_shipping.default_per_item_shipping',
				'title' => $this->language->get('text_description'),
				'cost' => $cost,
				'tax_class_id' => $this->config->get('default_per_item_shipping_tax'),
				'text' => $cost_text
		);

		$method_data = array(
				'id' => 'default_per_item_shipping',
				'title' => $this->language->get('text_title'),
				'quote' => $quote_data,
				'sort_order' => $this->config->get('default_per_item_shipping_sort_order'),
				'error' => FALSE
		);

		return $method_data;
	}
}