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
if ( !defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

class ModelExtensionDefaultRoyalMail extends Model {
	function getQuote($address) {
		$this->load->language('default_royal_mail/default_royal_mail');
		
		if ($this->config->get('default_royal_mail_status')) {
      		if (!$this->config->get('default_royal_mail_location_id')) {
        		$status = TRUE;
      		} else {
		        $query = $this->db->query("SELECT *
                                            FROM " . $this->db->table('zones_to_locations') . "
                                            WHERE location_id = '" . (int)$this->config->get('default_royal_mail_location_id') . "'
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
		$quote_data = array();
		//build array with cost for shipping
		$generic_product_ids = $free_shipping_ids = $shipping_price_ids = array(); // ids of products without special shipping cost
		$shipping_price_cost = 0; // total shipping cost of product with fixed shipping price
		$cart_products = $this->cart->getProducts();
		foreach($cart_products as $product){
			//(exclude free shipping products)
			if($product['free_shipping']){
				$free_shipping_ids[] = $product['product_id'];
				continue;
			}
			if($product['shipping_price']>0){
				$shipping_price_ids[] = $product['product_id'];
				$shipping_price_cost += $product['shipping_price']*$product['quantity'];
			}
			$generic_product_ids[] = $product['product_id'];
		}

		if($generic_product_ids){
			$api_weight_product_ids = array_diff($generic_product_ids,$shipping_price_ids);
			//WHEN ONLY PRODUCTS WITH FIXED SHIPPING PRICES ARE IN BASKET
			if(!$api_weight_product_ids){
				$cost = $shipping_price_cost;
				$quote_data['default_royal_mail'] = array(
											'id' => 'default_royal_mail.default_royal_mail',
											'title' => $this->language->get('text_title'),
											'cost' => $cost,
											'tax_class_id' => $this->config->get('default_royal_mail_tax_class_id'),
											'text' => $this->currency->format(
																			$this->tax->calculate($this->currency->convert( $cost,
																															$this->config->get('config_currency'),
																															$this->currency->getCode()),
																								  $this->config->get('default_royal_mail_tax_class_id'),
																								  $this->config->get('config_tax')),
																			$this->currency->getCode(),
																			1.0000000)
				);

				$method_data = array(
									'id' => 'default_royal_mail',
									'title' => $this->language->get('text_title'),
									'quote' => $quote_data,
									'sort_order' => $this->config->get('default_royal_mail_sort_order'),
									'error' => ''
								);
				return $method_data;
			}
		}else{
			$api_weight_product_ids = $shipping_price_ids;
		}

		if($api_weight_product_ids){
			$weight = $this->weight->convert(
					$this->cart->getWeight($api_weight_product_ids), //get weight non-free shipping products only
					$this->config->get('config_weight_class_id'),
					$this->config->get('default_royal_mail_weight_class_id')
			);
			$weight = ($weight < 0.1 ? 0.1 : $weight);
		}



		// FOR CASE WHEN ONLY FREE SHIPPING PRODUCTS IN BASKET

		if(!$api_weight_product_ids && $free_shipping_ids){
			if($address['iso_code_2'] == 'GB'){
				$method_name = 'default_royal_mail_'.  $this->config->get('default_royal_mail_free_gb');
				$text = $this->language->get( $method_name );
			}else{
				$method_name = 'default_royal_mail_'.  $this->config->get('default_royal_mail_free');
				$text = $this->language->get( $method_name );
			}
			//check is that method enabled
			/*if(!$this->config->get($method_name)){
				return array();
			}*/

			$quote_data[$method_name] = array(
										'id' => 'default_royal_mail.'.$method_name,
										'title' => $text,
										'cost' => 0.0,
										'tax_class_id' => $this->config->get('default_royal_mail_tax_class_id'),
										'text' => $this->language->get('text_free')
			);


			$method_data = array(
								'id' => 'default_royal_mail',
								'title' => $this->language->get('text_title'),
								'quote' => $quote_data,
								'sort_order' => $this->config->get('default_royal_mail_sort_order'),
								'error' => ''
							);

			return $method_data;
		}

		$sub_total = $this->cart->getSubTotal();

		if ($this->config->get('default_royal_mail_1st_class_standard') && $address['iso_code_2'] == 'GB') {
			$cost = 0;
			$compensation = 0;

			$rates = explode(',', $this->config->get('default_royal_mail_1st_class_standard_rates'));
			foreach ($rates as $rate) {
				$data = explode(':', $rate);

				if ($data[0] >= $weight) {
					if (isset($data[1])) {
						$cost = $data[1];
					}
					break;
				}
			}

			$rates = explode(',', $this->config->get('default_royal_mail_1st_class_standard_compensation_rates'));

			foreach ($rates as $rate) {
				$data = explode(':', $rate);

				if ($data[0] >= $sub_total) {
					if (isset($data[1])) {
						$compensation = $data[1];
					}

					break;
				}
			}

			if ((float)$cost) {
				$title = $this->language->get('default_royal_mail_1st_class_standard');

				if ($this->config->get('default_royal_mail_display_weight')) {
					$title .= ' (' . $this->language->get('default_royal_mail_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
				}

				if ($this->config->get('default_royal_mail_display_insurance') && (float)$compensation) {
					$title .= ' (' . $this->language->get('default_royal_mail_insurance') . ' ' . $this->currency->format($compensation) . ')';
				}

				if ($this->config->get('default_royal_mail_display_time')) {
					$title .= ' (' . $this->language->get('default_royal_mail_eta') . ')';
				}

				if($generic_product_ids){
					$cost += $shipping_price_cost;
				}

				$quote_data['default_royal_mail_1st_class_standard'] = array(
					'id'           => 'default_royal_mail.default_royal_mail_1st_class_standard',
					'title'        => $title,
					'cost'         => $cost,
					'tax_class_id' => $this->config->get('default_royal_mail_tax'),
					'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('default_royal_mail_tax'), $this->config->get('config_tax')))
				);
			}
		}

		if ($this->config->get('default_royal_mail_1st_class_recorded') && $address['iso_code_2'] == 'GB') {
			$cost = 0;
			$compensation = 0;

		    $rates = explode(',',$this->config->get('default_royal_mail_1st_class_recorded_rates_gb'));
			foreach ($rates as $rate) {
				$data = explode(':', $rate);

				if ($data[0] >= $weight) {
					if (isset($data[1])) {
						$cost = $data[1];
					}

					break;
				}
			}

			$rates = explode(',', $this->config->get('default_royal_mail_1st_class_recorded_compensation_rates_gb'));

			foreach ($rates as $rate) {
				$data = explode(':', $rate);

				if ($data[0] >= $sub_total) {
					if (isset($data[1])) {
						$compensation = $data[1];
					}
					break;
				}
			}

			if ((float)$cost) {
				$title = $this->language->get('default_royal_mail_1st_class_recorded');

				if ($this->config->get('default_royal_mail_display_weight')) {
					$title .= ' (' . $this->language->get('default_royal_mail_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
				}

				if ($this->config->get('default_royal_mail_display_insurance') && (float)$compensation) {
					$title .= ' (' . $this->language->get('default_royal_mail_insurance') . ' ' . $this->currency->format($compensation) . ')';
				}

				if ($this->config->get('default_royal_mail_display_time')) {
					$title .= ' (' . $this->language->get('default_royal_mail_eta') . ')';
				}

				if($generic_product_ids){
					$cost += $shipping_price_cost;
				}

				$quote_data['default_royal_mail_1st_class_recorded'] = array(
					'id'           => 'default_royal_mail.default_royal_mail_1st_class_recorded',
					'title'        => $title,
					'cost'         => $cost,
					'tax_class_id' => $this->config->get('default_royal_mail_tax'),
					'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('default_royal_mail_tax'), $this->config->get('config_tax')))
				);
			}
		}

		if ($this->config->get('default_royal_mail_2nd_class_standard') && $address['iso_code_2'] == 'GB') {
			$cost = 0;

			$rates = explode(',', $this->config->get('default_royal_mail_2nd_class_standard_rates'));
			foreach ($rates as $rate) {
				$data = explode(':', $rate);

				if ($data[0] >= $weight) {
					if (isset($data[1])) {
						$cost = $data[1];
					}

					break;
				}
			}

			if ((float)$cost) {
				$title = $this->language->get('default_royal_mail_2nd_class_standard');

				if ($this->config->get('default_royal_mail_display_weight')) {
					$title .= ' (' . $this->language->get('default_royal_mail_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
				}

				if ($this->config->get('default_royal_mail_display_time')) {
					$title .= ' (' . $this->language->get('default_royal_mail_eta') . ')';
				}

				if($generic_product_ids){
					$cost += $shipping_price_cost;
				}

				$quote_data['default_royal_mail_2nd_class_standard'] = array(
					'id'           => 'default_royal_mail.default_royal_mail_2nd_class_standard',
					'title'        => $title,
					'cost'         => $cost,
					'tax_class_id' => $this->config->get('default_royal_mail_tax'),
					'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('default_royal_mail_tax'), $this->config->get('config_tax')))
				);
			}
		}

		if ($this->config->get('default_royal_mail_2nd_class_recorded') && $address['iso_code_2'] == 'GB') {
			$cost = 0;
			$compensation = 0;

			$rates = explode(',', $this->config->get('default_royal_mail_2nd_class_recorded_rates_gb'));
			foreach ($rates as $rate) {
				$data = explode(':', $rate);

				if ($data[0] >= $weight) {
					if (isset($data[1])) {
						$cost = $data[1];
					}

					break;
				}
			}

			$rates = explode(',', $this->config->get('default_royal_mail_2nd_class_recorded_compensation_rates_gb'));

			foreach ($rates as $rate) {
				$data = explode(':', $rate);

				if ($data[0] >= $sub_total) {
					if (isset($data[1])) {
						$compensation = $data[1];
					}

					break;
				}
			}

			if ((float)$cost) {
				$title = $this->language->get('default_royal_mail_2nd_class_recorded');

				if ($this->config->get('default_royal_mail_display_weight')) {
					$title .= ' (' . $this->language->get('default_royal_mail_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
				}

				if ($this->config->get('default_royal_mail_display_insurance') && (float)$compensation) {
					$title .= ' (' . $this->language->get('default_royal_mail_insurance') . ' ' . $this->currency->format($compensation) . ')';
				}

				if ($this->config->get('default_royal_mail_display_time')) {
					$title .= ' (' . $this->language->get('default_royal_mail_eta') . ')';
				}

				if($generic_product_ids){
					$cost += $shipping_price_cost;
				}

				$quote_data['default_royal_mail_2nd_class_recorded'] = array(
					'id'           => 'default_royal_mail.default_royal_mail_2nd_class_recorded',
					'title'        => $title,
					'cost'         => $cost,
					'tax_class_id' => $this->config->get('default_royal_mail_tax'),
					'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('default_royal_mail_tax'), $this->config->get('config_tax')))
				);
			}
		}

		if ($this->config->get('default_royal_mail_standard_parcels') && $address['iso_code_2'] == 'GB') {
			$cost = 0;
			$compensation = 0;

			$rates = explode(',', $this->config->get('default_royal_mail_standard_parcels_rates_gb'));

			foreach ($rates as $rate) {
				$data = explode(':', $rate);

				if ($data[0] >= $weight) {
					if (isset($data[1])) {
						$cost = $data[1];
					}

					break;
				}
			}

			$rates = explode(',', $this->config->get('default_royal_mail_standard_parcels_compensation_rates_gb'));

			foreach ($rates as $rate) {
				$data = explode(':', $rate);

				if ($data[0] >= $sub_total) {
					if (isset($data[1])) {
						$compensation = $data[1];
					}

					break;
				}
			}

			if ((float)$cost) {
				$title = $this->language->get('default_royal_mail_standard_parcels');

				if ($this->config->get('default_royal_mail_display_weight')) {
					$title .= ' (' . $this->language->get('default_royal_mail_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
				}

				if ($this->config->get('default_royal_mail_display_insurance') && (float)$compensation) {
					$title .= ' (' . $this->language->get('default_royal_mail_insurance') . ' ' . $this->currency->format($compensation) . ')';
				}

				if ($this->config->get('default_royal_mail_display_time')) {
					$title .= ' (' . $this->language->get('default_royal_mail_eta') . ')';
				}

				if($generic_product_ids){
					$cost += $shipping_price_cost;
				}

				$quote_data['default_royal_mail_standard_parcels'] = array(
					'id'           => 'default_royal_mail.default_royal_mail_standard_parcels',
					'title'        => $title,
					'cost'         => $cost,
					'tax_class_id' => $this->config->get('default_royal_mail_tax'),
					'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('default_royal_mail_tax'), $this->config->get('config_tax')))
				);
			}
		}
		if ($address['iso_code_2'] != 'GB'){
			if($this->config->get('default_royal_mail_airmail')){
				$cost = 0;

				$countries = unserialize($this->config->get('default_royal_mail_airmail_countries'));

				if(in_array($address['iso_code_2'], $countries)){
					$rates = explode(',', $this->config->get('default_royal_mail_airmail_in_countries_rates'));
				} else{
					$rates = explode(',', $this->config->get('default_royal_mail_airmail_not_in_countries_rates'));
				}

				foreach($rates as $rate){
					$data = explode(':', $rate);

					if($data[0] >= $weight){
						if(isset($data[1])){
							$cost = $data[1];
						}

						break;
					}
				}

				if((float)$cost){
					$title = $this->language->get('default_royal_mail_airmail');

					if($this->config->get('default_royal_mail_display_weight')){
						$title .= ' (' . $this->language->get('default_royal_mail_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
					}

					if($this->config->get('default_royal_mail_display_time')){
						$title .= ' (' . $this->language->get('default_royal_mail_eta') . ')';
					}

					if($generic_product_ids){
						$cost += $shipping_price_cost;
					}

					$quote_data['default_royal_mail_airmail'] = array(
							'id'           => 'default_royal_mail.default_royal_mail_airmail',
							'title'        => $title,
							'cost'         => $cost,
							'tax_class_id' => $this->config->get('default_royal_mail_tax'),
							'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('default_royal_mail_tax'), $this->config->get('config_tax')))
					);
				}
			}

			if($this->config->get('default_royal_mail_international_signed')){
				$cost = 0;
				$compensation = 0;

				$countries = unserialize($this->config->get('default_royal_mail_international_signed_countries'));

				if(in_array($address['iso_code_2'], $countries)){
					$rates = explode(',', $this->config->get('default_royal_mail_international_signed_in_countries_rates'));
				} else{
					$rates = explode(',', $this->config->get('default_royal_mail_international_signed_not_in_countries_rates'));
				}

				foreach($rates as $rate){
					$data = explode(':', $rate);

					if($data[0] >= $weight){
						if(isset($data[1])){
							$cost = $data[1];
						}

						break;
					}
				}

				if(in_array($address['iso_code_2'], $countries)){
					$rates = explode(',', $this->config->get('default_royal_mail_international_signed_in_countries_c_rates'));
				} else{
					$rates = explode(',', $this->config->get('default_royal_mail_international_signed_not_in_countries_c_rates'));
				}

				foreach($rates as $rate){
					$data = explode(':', $rate);

					if($data[0] >= $sub_total){
						if(isset($data[1])){
							$compensation = $data[1];
						}

						break;
					}
				}

				if((float)$cost){
					$title = $this->language->get('default_royal_mail_international_signed');

					if($this->config->get('default_royal_mail_display_weight')){
						$title .= ' (' . $this->language->get('default_royal_mail_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
					}

					if($this->config->get('default_royal_mail_display_insurance') && (float)$compensation){
						$title .= ' (' . $this->language->get('default_royal_mail_insurance') . ' ' . $this->currency->format($compensation) . ')';
					}

					if($this->config->get('default_royal_mail_display_time')){
						$title .= ' (' . $this->language->get('default_royal_mail_eta') . ')';
					}

					if($generic_product_ids){
						$cost += $shipping_price_cost;
					}

					$quote_data['default_royal_mail_international_signed'] = array(
							'id'           => 'default_royal_mail.default_royal_mail_international_signed',
							'title'        => $title,
							'cost'         => $cost,
							'tax_class_id' => $this->config->get('default_royal_mail_tax'),
							'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('default_royal_mail_tax'), $this->config->get('config_tax')))
					);
				}
			}

			if($this->config->get('default_royal_mail_airsure')){
				$cost = 0;
				$compensation = 0;

				$rates = array();

				$countries = (array)unserialize($this->config->get('default_royal_mail_airsure_countries'));

				if(in_array($address['iso_code_2'], $countries)){
					$rates = explode(',', $this->config->get('default_royal_mail_airsure_in_countries_rates'));
				}

				$countries = (array)unserialize($this->config->get('default_royal_mail_airsure_countries_2'));

				if(in_array($address['iso_code_2'], $countries)){
					$rates = explode(',', $this->config->get('default_royal_mail_airsure_in_countries_2_rates'));
				}


				foreach($rates as $rate){
					$data = explode(':', $rate);

					if($data[0] >= $weight){
						if(isset($data[1])){
							$cost = $data[1];
						}

						break;
					}
				}

				$rates = array();

				$countries = (array)unserialize($this->config->get('default_royal_mail_airsure_countries_3'));

				if(in_array($address['iso_code_2'], $countries)){
					$rates = explode(',', $this->config->get('default_royal_mail_airsure_in_countries_3_rates'));
				}

				$countries = (array)unserialize($this->config->get('default_royal_mail_airsure_countries_4'));

				if(in_array($address['iso_code_2'], $countries)){
					$rates = explode(',', $this->config->get('default_royal_mail_airsure_in_countries_4_rates'));
				}

				foreach($rates as $rate){
					$data = explode(':', $rate);

					if($data[0] >= $sub_total){
						if(isset($data[1])){
							$compensation = $data[1];
						}

						break;
					}
				}

				if((float)$cost){
					$title = $this->language->get('default_royal_mail_airsure');

					if($this->config->get('default_royal_mail_display_weight')){
						$title .= ' (' . $this->language->get('default_royal_mail_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
					}

					if($this->config->get('default_royal_mail_display_insurance') && (float)$compensation){
						$title .= ' (' . $this->language->get('default_royal_mail_insurance') . ' ' . $this->currency->format($compensation) . ')';
					}

					if($this->config->get('default_royal_mail_display_time')){
						$title .= ' (' . $this->language->get('default_royal_mail_eta') . ')';
					}

					if($generic_product_ids){
						$cost += $shipping_price_cost;
					}

					$quote_data['default_royal_mail_airsure'] = array(
							'id'           => 'default_royal_mail.default_royal_mail_airsure',
							'title'        => $title,
							'cost'         => $cost,
							'tax_class_id' => $this->config->get('default_royal_mail_tax'),
							'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('default_royal_mail_tax'), $this->config->get('config_tax')))
					);
				}
			}

			if($this->config->get('default_royal_mail_surface')){
				$cost = 0;
				$compensation = 0;

				$rates = explode(',', $this->config->get('default_royal_mail_surface_rates'));

				foreach($rates as $rate){
					$data = explode(':', $rate);

					if($data[0] >= $weight){
						if(isset($data[1])){
							$cost = $data[1];
						}

						break;
					}
				}

				if((float)$cost){
					$title = $this->language->get('default_royal_mail_surface');

					if($this->config->get('default_royal_mail_display_weight')){
						$title .= ' (' . $this->language->get('default_royal_mail_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
					}

					if($this->config->get('default_royal_mail_display_insurance') && (float)$compensation){
						$title .= ' (' . $this->language->get('default_royal_mail_insurance') . ' ' . $this->currency->format($compensation) . ')';
					}

					if($this->config->get('default_royal_mail_display_time')){
						$title .= ' (' . $this->language->get('default_royal_mail_eta') . ')';
					}

					if($generic_product_ids){
						$cost += $shipping_price_cost;
					}

					$quote_data['default_royal_mail_surface'] = array(
							'id'           => 'default_royal_mail.default_royal_mail_surface',
							'title'        => $title,
							'cost'         => $cost,
							'tax_class_id' => $this->config->get('default_royal_mail_tax'),
							'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('default_royal_mail_tax'), $this->config->get('config_tax')))
					);
				}
			}
		}

		if ($quote_data) {
			$method_data = array(
				'id'         => 'default_royal_mail.default_royal_mail',
				'title'      => $this->language->get('text_title'),
				'quote'      => $quote_data,
				'sort_order' => $this->config->get('default_royal_mail_sort_order'),
				'error'      => FALSE
			);
		}
			
		return $method_data;
	}
}