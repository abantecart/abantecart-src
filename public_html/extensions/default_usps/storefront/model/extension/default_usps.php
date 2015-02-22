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

class ModelExtensionDefaultUsps extends Model {
	public function getQuote($address) {
		$this->load->language('default_usps/default_usps');

		if ($this->config->get('default_usps_status')) {
			$this->load->model('localisation/country');
			if (!$this->config->get('default_usps_location_id')) {
				$status = TRUE;
			}else {
				$query = $this->db->query("SELECT *
											FROM " . $this->db->table('zones_to_locations') . "
											WHERE location_id = '" . (int)$this->config->get('default_usps_location_id') . "'
												AND country_id = '" . (int)$address['country_id'] . "'
												AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
				if ($query->num_rows) {
					$status = TRUE;
				} else {
					$status = FALSE;
				}
			}

			//load all countries and codes
			$countries = $this->model_localisation_country->getCountries();
			foreach ($countries as $item) {
				$country[$item['iso_code_2']] = $item['name'];
			}

			if($status && !has_value($country[$address['iso_code_2']])){
				$status = FALSE;
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
		//convert fixed prices to USD
		$shipping_price_cost = $this->currency->convert($shipping_price_cost, $this->config->get('config_currency'), 'USD');

		if($generic_product_ids){
			$api_weight_product_ids = array_diff($generic_product_ids,$shipping_price_ids);
			//WHEN ONLY PRODUCTS WITH FIXED SHIPPING PRICES ARE IN BASKET
			if(!$api_weight_product_ids){
				$cost = $shipping_price_cost;
				$quote_data = array('default_usps' => array(
											'id' => 'default_usps.default_usps',
											'title' => $this->language->get('text_title'),
											'cost' => $this->currency->convert($cost, 'USD', $this->config->get('config_currency')),
											'tax_class_id' => $this->config->get('default_usps_tax_class_id'),
											'text' => $this->currency->format(
																			$this->tax->calculate($this->currency->convert( $cost,
																															'USD',
																															$this->currency->getCode()),
																								  $this->config->get('default_usps_tax_class_id'),
																								  $this->config->get('config_tax')),
																			$this->currency->getCode(),
																			1.0000000)
				));

				$method_data = array(
									'id' => 'default_usps',
									'title' => $this->language->get('text_title'),
									'quote' => $quote_data,
									'sort_order' => $this->config->get('default_usps_sort_order'),
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
					$this->config->get('default_usps_weight_class_id')
			);
			$weight = ($weight < 0.1 ? 0.1 : $weight);
		}

		$pounds = floor($weight);
		$ounces = round(16 * ($weight - $pounds), 2); // max 5 digits
		$postcode = str_replace(' ', '', $address['postcode']);

		// FOR CASE WHEN ONLY FREE SHIPPING PRODUCTS IN BASKET
		if(!$api_weight_product_ids && $free_shipping_ids){

			$quote_data = array('default_usps' => array(
										'id' => 'default_usps.default_usps',
										'title' => $this->language->get('text_'.($address['iso_code_2'] == 'US'
																				? $this->config->get('default_usps_free_domestic_method')
																				: $this->config->get('default_usps_free_international_method'))),
										'cost' => 0.0,
										'tax_class_id' => $this->config->get('default_usps_tax_class_id'),
										'text' => $this->language->get('text_free')
			));


			$method_data = array(
								'id' => 'default_usps',
								'title' => $this->language->get('text_title'),
								'quote' => $quote_data,
								'sort_order' => $this->config->get('default_usps_sort_order'),
								'error' => ''
							);
			return $method_data;
		}

		if ($address['iso_code_2'] == 'US') {
			$xml = '<RateV4Request USERID="' . $this->config->get('default_usps_user_id') . '" PASSWORD="' . $this->config->get('default_usps_password') . '">';
			$xml .= '	<Package ID="1">';
			$xml .= '		<Service>ALL</Service>';
			$xml .= '		<ZipOrigination>' . substr($this->config->get('default_usps_postcode'), 0, 5) . '</ZipOrigination>';
			$xml .= '		<ZipDestination>' . substr($postcode, 0, 5) . '</ZipDestination>';
			$xml .= '		<Pounds>' . $pounds . '</Pounds>';
			$xml .= '		<Ounces>' . $ounces . '</Ounces>';

			// Prevent common size mismatch error from USPS (Size cannot be Regular if Container is Rectangular for some reason)
			if ($this->config->get('default_usps_container') == 'RECTANGULAR' && $this->config->get('default_usps_size') == 'REGULAR') {
				$this->config->set('default_usps_container', 'VARIABLE');
			}

			$xml .= '		<Container>' . $this->config->get('default_usps_container') . '</Container>';
			$xml .= '		<Size>' . $this->config->get('default_usps_size') . '</Size>';
			$xml .= '		<Width>' . $this->config->get('default_usps_width') . '</Width>';
			$xml .= '		<Length>' . $this->config->get('default_usps_length') . '</Length>';
			$xml .= '		<Height>' . $this->config->get('default_usps_height') . '</Height>';

			// Calculate girth based on usps calculation
			$xml .= '		<Girth>' . (round(((float)$this->config->get('default_usps_length') + (float)$this->config->get('default_usps_width') * 2 + (float)$this->config->get('default_usps_height') * 2), 1)) . '</Girth>';

			$xml .= '		<Machinable>' . ($this->config->get('default_usps_machinable') ? 'true' : 'false') . '</Machinable>';
			$xml .= '	</Package>';
			$xml .= '</RateV4Request>';

			$request = 'API=RateV4&XML=' . urlencode($xml);
		} else {

				$xml = '<IntlRateV2Request USERID="' . $this->config->get('default_usps_user_id') . '">';
				$xml .= '	<Package ID="1">';
				$xml .= '		<Pounds>' . $pounds . '</Pounds>';
				$xml .= '		<Ounces>' . $ounces . '</Ounces>';
				$xml .= '		<MailType>All</MailType>';
				$xml .= '		<GXG>';
				$xml .= '		  <POBoxFlag>N</POBoxFlag>';
				$xml .= '		  <GiftFlag>N</GiftFlag>';
				$xml .= '		</GXG>';
				$xml .= '		<ValueOfContents>' . $this->cart->getSubTotal() . '</ValueOfContents>';
				$xml .= '		<Country>' . $country[$address['iso_code_2']] . '</Country>';
				// Intl only supports RECT and NONRECT
				if ($this->config->get('default_usps_container') == 'VARIABLE') {
					$this->config->set('default_usps_container', 'NONRECTANGULAR');
				}
				$xml .= '		<Container>' . $this->config->get('default_usps_container') . '</Container>';
				$xml .= '		<Size>' . $this->config->get('default_usps_size') . '</Size>';
				$xml .= '		<Width>' . $this->config->get('default_usps_width') . '</Width>';
				$xml .= '		<Length>' . $this->config->get('default_usps_length') . '</Length>';
				$xml .= '		<Height>' . $this->config->get('default_usps_height') . '</Height>';
				$xml .= '		<Girth>' . $this->config->get('default_usps_girth') . '</Girth>';
				$xml .= '		<CommercialFlag>N</CommercialFlag>';
				$xml .= '	</Package>';
				$xml .= '</IntlRateV2Request>';

				$request = 'API=IntlRateV2&XML=' . urlencode($xml);

		}


		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, 'http://production.shippingapis.com/ShippingAPI.dll?' . $request);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($curl);

		curl_close($curl);

		// strip reg, trade and ** out 01-02-2011
		$result = str_replace('&amp;lt;sup&amp;gt;&amp;#8482;&amp;lt;/sup&amp;gt;', '', $result);
		$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);
		$result = str_replace('**', '', $result);
		$result = str_replace("\r\n", '', $result);
		$result = str_replace('\"', '"', $result);

		if ($result) {

			if ($this->config->get('default_usps_debug')) {
				$this->log->write("USPS DATA SENT: " . urldecode($request));
				$this->log->write("USPS DATA RECV: " . $result);
			}

			$dom = new DOMDocument('1.0', 'UTF-8');
			$dom->loadXml($result);
			/**
			 * @var $rate_response DOMElement
			 */
			$rate_response = $dom->getElementsByTagName('RateV4Response')->item(0);
			/**
			 * @var $intl_rate_response DOMElement
			 */
			$intl_rate_response = $dom->getElementsByTagName('IntlRateV2Response')->item(0);
			$error = $dom->getElementsByTagName('Error')->item(0);

			$firstclasses = array(
				'First-Class Mail Parcel',
				'First-Class Mail Large Envelope',
				'First-Class Mail Letter',
				'First-Class Mail Postcards'
			);

			if ($rate_response || $intl_rate_response) {
				if ($address['iso_code_2'] == 'US') {
					$allowed = array(0, 1, 2, 3, 4, 5, 6, 7, 12, 13, 16, 17, 18, 19, 22, 23, 25, 27, 28);
					/**
					 * @var $package DOMElement
					 */
					$package = $rate_response->getElementsByTagName('Package')->item(0);
					/**
					 * @var $postages DOMElement
					 */
					$postages = $package->getElementsByTagName('Postage');

					if ($postages->length) {
						/**
						 * @var $postage DOMElement
						 */
						foreach ($postages as $postage) {

							$classid = $postage->getAttribute('CLASSID');

							if (in_array($classid, $allowed)) {
								if ($classid == '0') {
									$mailservice = $postage->getElementsByTagName('MailService')->item(0)->nodeValue;

									foreach ($firstclasses as $k => $firstclass) {
										if ($firstclass == $mailservice) {
											$classid = $classid . $k;
											break;
										}
									}

									if (($this->config->get('default_usps_domestic_' . $classid))) {
										$cost = $postage->getElementsByTagName('Rate')->item(0)->nodeValue;
										if($generic_product_ids){
											$cost += $shipping_price_cost;
										}

										$quote_data[$classid] = array(
											'id' => 'default_usps.' . $classid,
											'title' => $postage->getElementsByTagName('MailService')->item(0)->nodeValue,
											'cost' => $this->currency->convert($cost, 'USD', $this->config->get('config_currency')),
											'tax_class_id' => $this->config->get('default_usps_tax_class_id'),
											'text' => $this->currency->format($this->tax->calculate($this->currency->convert($cost, 'USD', $this->currency->getCode()), $this->config->get('default_usps_tax_class_id'), $this->config->get('config_tax')), $this->currency->getCode(), 1.0000000)
										);
									}

								} elseif ($this->config->get('default_usps_domestic_' . $classid)) {
									$cost = $postage->getElementsByTagName('Rate')->item(0)->nodeValue;
									if($generic_product_ids){
										$cost += $shipping_price_cost;
									}

									$quote_data[$classid] = array(
										'id' => 'default_usps.' . $classid,
										'title' => $postage->getElementsByTagName('MailService')->item(0)->nodeValue,
										'cost' => $this->currency->convert($cost, 'USD', $this->config->get('config_currency')),
										'tax_class_id' => $this->config->get('default_usps_tax_class_id'),
										'text' => $this->currency->format($this->tax->calculate($this->currency->convert($cost, 'USD', $this->currency->getCode()), $this->config->get('default_usps_tax_class_id'), $this->config->get('config_tax')), $this->currency->getCode(), 1.0000000)
									);
								}
							}
						}
					} else {
						/**
						 * @var $error DOMElement
						 */
						$error = $package->getElementsByTagName('Error')->item(0);
						$method_data = array(
							'id' => 'default_usps',
							'title' => $this->language->get('text_title'),
							'quote' => $quote_data,
							'sort_order' => $this->config->get('default_usps_sort_order'),
							'error' => $error->getElementsByTagName('Description')->item(0)->nodeValue
						);
					}
				} else {
					$allowed = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 21);
					/**
					 * @var $package DOMElement
					 */
					$package = $intl_rate_response->getElementsByTagName('Package')->item(0);
					/**
					 * @var $services DOMElement
					 */
					$services = $package->getElementsByTagName('Service');
					/**
					 * @var $service DOMElement
					 */
					foreach ($services as $service) {
						$id = $service->getAttribute('ID');

						if (in_array($id, $allowed) && $this->config->get('default_usps_international_' . $id)) {
							$title = $service->getElementsByTagName('SvcDescription')->item(0)->nodeValue;

							if ($this->config->get('default_usps_display_time')) {
								$title .= ' (' . $this->language->get('text_eta') . ' ' . $service->getElementsByTagName('SvcCommitments')->item(0)->nodeValue . ')';
							}

							$cost = $service->getElementsByTagName('Postage')->item(0)->nodeValue;
							if($generic_product_ids){
								$cost += $shipping_price_cost;
							}

							$quote_data[$id] = array(
								'id' => 'default_usps.' . $id,
								'title' => $title,
								'cost' => $this->currency->convert($cost, 'USD', $this->config->get('config_currency')),
								'tax_class_id' => $this->config->get('default_usps_tax_class_id'),
								'text' => $this->currency->format($this->tax->calculate($this->currency->convert($cost, 'USD', $this->currency->getCode()), $this->config->get('default_usps_tax_class_id'), $this->config->get('config_tax')), $this->currency->getCode(), 1.0000000)

							);
						}
					}
				}
			} elseif ($error) {
				/**
				 * @var $error DOMElement
				 */
				$method_data = array(
					'id' => 'default_usps',
					'title' => $this->language->get('text_title'),
					'quote' => $quote_data,
					'sort_order' => $this->config->get('default_usps_sort_order'),
					'error' => $error->getElementsByTagName('Description')->item(0)->nodeValue
				);
			}
		}

		if ($quote_data) {
			$title = $this->language->get('text_title');

			if ($this->config->get('default_usps_display_weight')) {
				$title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('default_usps_weight_class_id')) . ')';
			}

			$method_data = array(
				'id' => 'default_usps',
				'title' => $title,
				'quote' => $quote_data,
				'sort_order' => $this->config->get('default_usps_sort_order'),
				'error' => false
			);
		}


		return $method_data;
	}

}