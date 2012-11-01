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

class ModelExtensionGoogleCheckout extends Model {

	public $data = array();
	private $error = array();

	public function getMethod($address) {
		$this->load->language('google_checkout/google_checkout');
		if( $this->config->get('google_checkout_status')<1 ){
			return array();
		}

		$sql = "SELECT *
				FROM " . DB_PREFIX . "zones_to_locations
				WHERE location_id = '" . (int)$this->config->get('google_checkout_location_id') . "'
					   AND country_id = '" . (int)$address['country_id'] . "'
					   AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')";
		$query = $this->db->query($sql);


		$total = $this->cart->getTotal();

		if ($this->config->get('google_checkout_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('google_checkout_location_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}


		$currencies = array(
			'AUD',
			'CAD',
			'EUR',
			'GBP',
			'JPY',
			'USD',
			'NZD',
			'CHF',
			'HKD',
			'SGD',
			'SEK',
			'DKK',
			'PLN',
			'NOK',
			'HUF',
			'CZK',
			'ILS',
			'MXN',
			'MYR',
			'BRL',
			'PHP',
			'TWD',
			'THB',
			'TRY'
		);

		if (!in_array(strtoupper($this->currency->getCode()), $currencies)) {
			$status = false;
		}

		$method_data = array();

		if ($status) {
			$method_data = array(
				'id' => 'google_checkout',
				'title' => $this->language->get('google_checkout_title'),
				'sort_order' => $this->config->get('google_checkout_sort_order')
			);
		}

		return $method_data;
	}

}
