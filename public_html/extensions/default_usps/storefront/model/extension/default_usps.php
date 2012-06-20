<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2012 Belavier Commerce LLC

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

class ModelExtensionDefaultUsps extends Model {
	public function getQuote($address) {
		$this->load->language('default_usps/default_usps');
		
		if ($this->config->get('default_usps_status')) {
      		$taxes = $this->tax->getTaxes((int)$address['country_id'], (int)$address['zone_id']);
      		if (!$this->config->get('default_usps_location_id')) {
        		$status = TRUE;
      		} elseif ($taxes) {
        		$status = TRUE;
      		} else {
        		$status = FALSE;
      		}
		} else {
			$status = FALSE;
		}
	  
		$method_data = array();
	
		if ($status) {
			$this->load->model('localisation/country');

			$quote_data = array();
			
			$weight = $this->weight->convert($this->cart->getWeight(), $this->config->get('config_weight_class'), $this->config->get('default_usps_weight_class'));
			
			$weight = ($weight < 0.1 ? 0.1 : $weight);
			$pounds = floor($weight);
			$ounces = round(16 * ($weight - floor($weight)));
			
			$postcode = str_replace(' ', '', $address['postcode']);
			
			if ($address['iso_code_2'] == 'US') { 
				$xml  = '<RateV3Request USERID="' . $this->config->get('default_usps_user_id') . '" PASSWORD="' . $this->config->get('default_usps_password') . '">';
				$package_id = 1;
				//Process all products shipped together with not special shipping settings on a product level
				if ( count($this->cart->basicShippingProducts()) > 0 ) {
					$xml .= '	<Package ID="'. $package_id . '">';
					$xml .=	'		<Service>ALL</Service>';
					$xml .=	'		<ZipOrigination>' . substr($this->config->get('default_usps_postcode'), 0, 5) . '</ZipOrigination>';
					$xml .=	'		<ZipDestination>' . substr($postcode, 0, 5) . '</ZipDestination>';
					$xml .=	'		<Pounds>' . $pounds . '</Pounds>';
					$xml .=	'		<Ounces>' . $ounces . '</Ounces>';				
													
					// Size cannot be Regular if Container is Rectangular
					if ($this->config->get('default_usps_container') == 'RECTANGULAR' && $this->config->get('default_usps_container') == 'REGULAR') {
						$this->config->set('default_usps_container', 'VARIABLE');
					}
	
					$xml .=	'		<Container>' . $this->config->get('default_usps_container') . '</Container>';
					$xml .=	'		<Size>' . $this->config->get('default_usps_size') . '</Size>';
					
					$use_width = $this->config->get('default_usps_width');
					$use_length = $this->config->get('default_usps_length');
					$use_height = $this->config->get('default_usps_height');
					$xml .= '		<Width>' . $use_width . '</Width>';
					$xml .= '		<Length>' . $use_length . '</Length>';
					$xml .= '		<Height>' . $use_height . '</Height>';				
										
					// Calculate girth based on usps calculation if it is not provided
					if ( $this->config->get('default_usps_girth') ){
						$xml .= '	<Girth>' . $this->config->get('default_usps_girth') . '</Girth>';
					}
					else{
						$xml .= '	<Girth>' . (round(((float)$use_length + (float)$use_width * 2 + (float)$use_height * 2), 1)) . '</Girth>';
					}
					$xml .=	'		<Machinable>' . ($this->config->get('default_usps_machinable') ? 'true' : 'false') . '</Machinable>';				
					$xml .=	'	</Package>';
				}
				
				$special_ship_products = $this->cart->specialShippingProducts();
				foreach ($special_ship_products as $product) {
					//skip free and fixed price shipping 
					if($product['free_shipping'] || $product['shipping_price']) {
						next;
					}

					$pweight = $this->weight->convert($this->cart->getWeight($product['product_id']), $this->config->get('config_weight_class'), $this->config->get('default_usps_weight_class'));
					
					$pweight = ($pweight < 0.1 ? 0.1 : $pweight);
					$pounds = floor($pweight);
					$ounces = round(16 * ($pweight - floor($pweight)));
					
					$postcode = str_replace(' ', '', $address['postcode']);

					$package_id++;
					$xml .= '	<Package ID="'. $package_id . '">';
					$xml .=	'		<Service>ALL</Service>';
					$xml .=	'		<ZipOrigination>' . substr($this->config->get('default_usps_postcode'), 0, 5) . '</ZipOrigination>';
					$xml .=	'		<ZipDestination>' . substr($postcode, 0, 5) . '</ZipDestination>';
					$xml .=	'		<Pounds>' . $pounds . '</Pounds>';
					$xml .=	'		<Ounces>' . $ounces . '</Ounces>';				
													
					// Size cannot be Regular if Container is Rectangular
					if ($this->config->get('default_usps_container') == 'RECTANGULAR' && $this->config->get('default_usps_container') == 'REGULAR') {
						$this->config->set('default_usps_container', 'VARIABLE');
					}
					$xml .=	'		<Container>' . $this->config->get('default_usps_container') . '</Container>';
					$xml .=	'		<Size>' . $this->config->get('default_usps_size') . '</Size>';
					
					$use_width = $this->config->get('default_usps_width');
					$use_length = $this->config->get('default_usps_length');
					$use_height = $this->config->get('default_usps_height');
					//now check if we have most specific dimmention settings in the products. 
					//USPS works with inches 
					if ( $product['width'] ) {
						$length_class_id = $this->length->getClassID($length_class_unit); 
						$use_width = $this->length->convertByID($product['length'], $product['length_class'], $length_class_id);
						$use_length = $this->length->convertByID($product['width'], $product['length_class'], $length_class_id);
						$use_height = $this->length->convertByID($product['height'], $product['length_class'], $length_class_id);
					}				
					$xml .= '		<Width>' . $use_width . '</Width>';
					$xml .= '		<Length>' . $use_length . '</Length>';
					$xml .= '		<Height>' . $use_height . '</Height>';				
										
					// Calculate girth based on usps calculation if it is not provided
					if ( $this->config->get('default_usps_girth') ){
						$xml .= '	<Girth>' . $this->config->get('default_usps_girth') . '</Girth>';
					}
					else{
						$xml .= '	<Girth>' . (round(((float)$use_length + (float)$use_width * 2 + (float)$use_height * 2), 1)) . '</Girth>';
					}
					$xml .=	'		<Machinable>' . ($this->config->get('default_usps_machinable') ? 'true' : 'false') . '</Machinable>';
					$xml .=	'	</Package>';	
				}
								
				$xml .= '</RateV3Request>';
echo "<textarea>" . $xml . "</textarea>";
				$request = 'API=RateV3&XML=' . urlencode($xml);
			} else {
				//load all countires and codes
				$this->loadModel('localisation/country');
        		$countries = $this->model_localisation_country->getCountries();
		        foreach ($countries as $item) {
		            $country[$item['iso_code_2']] = $item['name'];
		        }
	  			
				if (isset($country[$address['iso_code_2']])) {
					$xml  = '<IntlRateRequest USERID="' . $this->config->get('default_usps_user_id') . '" PASSWORD="' . $this->config->get('default_usps_password') . '">';
					$xml .=	'	<Package ID="0">';
					$xml .=	'		<Pounds>' . $pounds . '</Pounds>';
					$xml .=	'		<Ounces>' . $ounces . '</Ounces>';
					$xml .=	'		<MailType>Package</MailType>';
					$xml .=	'		<Country>' . $country[$address['iso_code_2']] . '</Country>';
					$xml .=	'	</Package>';
					$xml .=	'</IntlRateRequest>';
		
					$request = 'API=IntlRate&XML=' . urlencode($xml);
				} else {
					$status = FALSE;	
				}
			}	
			
			if ($status) {
				$ch = curl_init();
				
				curl_setopt($ch, CURLOPT_URL, 'production.shippingapis.com/ShippingAPI.dll?' . $request);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
				$result = curl_exec($ch);
				
				curl_close($ch);  
				
				if ($result) {
					$dom = new DOMDocument('1.0', 'UTF-8');
					$dom->loadXml($result);	
					
					$rate_v3_response = $dom->getElementsByTagName('RateV3Response')->item(0);
					$intl_rate_response = $dom->getElementsByTagName('IntlRateResponse')->item(0);
					$error = $dom->getElementsByTagName('Error')->item(0);
echo ">>>>>>>>>>". $result;
					if ($rate_v3_response || $intl_rate_response) {
						if ($address['iso_code_2'] == 'US') { 
							$allowed = array(0, 1, 2, 3, 4, 5, 6, 7, 12, 13, 16, 17, 18, 19, 22, 23, 25, 27, 28);
							
							for ($pkg = 0; $pkg < $package_id; $pkg++) {					
								$package = $rate_v3_response->getElementsByTagName('Package')->item($pkg);
								
								$postages = $package->getElementsByTagName('Postage');
											
								foreach ($postages as $postage) {
									$classid = $postage->getAttribute('CLASSID');
									
									if (in_array($classid, $allowed) && $this->config->get('default_usps_domestic_' . $classid)) {
				
										$cost = $postage->getElementsByTagName('Rate')->item($pkg)->nodeValue;
										$title = $postage->getElementsByTagName('MailService')->item($pkg)->nodeValue;
										$title = preg_replace('/\&lt;sup\&gt;\&amp;reg;\&lt;\/sup\&gt;/', '<sup>&reg;</sup>', $title);
										
										$quote_data[$classid] = array(
											'id'           => 'default_usps.' . $classid,
											'title'        => $title,
											'cost'         => $this->currency->convert($cost, 'USD', $this->currency->getCode()),
											'tax_class_id' => 0,
											'text'         => $this->currency->format($this->tax->calculate($this->currency->convert($cost, 'USD', $this->currency->getCode()), $this->config->get('default_usps_tax_class_id'), $this->config->get('config_tax')))
										);							
									}
								}
							} 
						} else {
							$allowed = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 21);
							
							$package = $intl_rate_response->getElementsByTagName('Package')->item(0);
							
							$services = $package->getElementsByTagName('Service');
							
							foreach ($services as $service) {
								$id = $service->getAttribute('ID');
								
								if (in_array($id, $allowed) && $this->config->get('default_usps_international_' . $id)) {
									$title = $service->getElementsByTagName('SvcDescription')->item(0)->nodeValue;
									$title = preg_replace('/\&lt;sup\&gt;\&amp;reg;\&lt;\/sup\&gt;/', '<sup>&reg;</sup>', $title);
									
									if ($this->config->get('default_usps_display_weight')) {	  
										$title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
									}
						
									if ($this->config->get('default_usps_display_time')) {	  
										$title .= ' (' . $this->language->get('text_eta') . ' ' . $service->getElementsByTagName('SvcCommitments')->item(0)->nodeValue . ')';
									}
									
									$cost = $service->getElementsByTagName('Postage')->item(0)->nodeValue;
									
									$quote_data[$id] = array(
										'id'           => 'default_usps.' . $id,
										'title'        => $title,
										'cost'         => $this->currency->convert($cost, 'USD', $this->currency->getCode()),
										'tax_class_id' => $this->config->get('default_usps_tax_class_id'),
										'text'         => $this->currency->format($this->tax->calculate($this->currency->convert($cost, 'USD', $this->currency->getCode()), $this->config->get('default_usps_tax_class_id'), $this->config->get('config_tax')))
									);							
								}
							}
						}
					} elseif ($error) {
						$method_data = array(
							'id'         => 'default_usps',
							'title'      => $this->language->get('text_title'),
							'quote'      => $quote_data,
							'sort_order' => $this->config->get('default_usps_sort_order'),
							'error'      => $error->getElementsByTagName('Description')->item(0)->nodeValue
						);					
					}
				}
			}
			
	  		if ($quote_data) {
				
				$title = $this->language->get('text_title');
									
				if ($this->config->get('default_usps_display_weight')) {	  
					$title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
				}		
			
      			$method_data = array(
        			'id'         => 'default_usps',
        			'title'      => $title,
        			'quote'      => $quote_data,
					'sort_order' => $this->config->get('default_usps_sort_order'),
        			'error'      => FALSE
      			);
			}
		}
	
		return $method_data;
	}	
}