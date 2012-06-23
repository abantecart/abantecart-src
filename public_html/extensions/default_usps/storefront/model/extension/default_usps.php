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
									
			//global method dimensions settings
			$use_width = $this->config->get('default_usps_width');
			$use_length = $this->config->get('default_usps_length');
			$use_height = $this->config->get('default_usps_height');
								
			//Process all products shipped together with not special shipping settings on a product level
			$b_products = $this->cart->basicShippingProducts(); 
			if ( count($b_products) > 0 ) {
			    $prod_ids = array();
			    foreach ($b_products as $prd) {
			    	$prod_ids[] = $prd['product_id'];
			    }

			    $weight = $this->weight->convert($this->cart->getWeight( $prod_ids ), $this->config->get('config_weight_class'), $this->config->get('default_usps_weight_class'));

			    $request = $this->_build_qoute_request( $weight, $use_width, $use_length, $use_height, $address ); 
			    if ($request) {
			    	$quote_data = $this->_process_request( $request, $address, $weight );
			    }				
			}
			
			$special_ship_products = $this->cart->specialShippingProducts();
			foreach ($special_ship_products as $product) {
			    
			    $weight = $this->weight->convert($this->cart->getWeight( array($product['product_id']) ), $this->config->get('config_weight_class'), $this->config->get('default_usps_weight_class'));
			    
			    if ( $product['width'] ) {
			    	$length_class_id = $this->length->getClassID($this->config->get('default_usps_length_class'));
			    	$use_width = $this->length->convertByID($product['length'], $product['length_class'], $length_class_id);
			    	$use_length = $this->length->convertByID($product['width'], $product['length_class'], $length_class_id);
			    	$use_height = $this->length->convertByID($product['height'], $product['length_class'], $length_class_id);
			    }	
			    									
			    //check if free or fixed shipping
			    $fixed_cost = -1;
			    $new_quote_data = array();
			    if ($product['free_shipping']) {
			    	$fixed_cost = 0;
			    } else if($product['shipping_price'] > 0) {
			    	$fixed_cost = $product['shipping_price'];
			    	//If ship individually count every quintaty 
			     	if ($product['ship_individually']) {
			     		$fixed_cost = $fixed_cost * $product['quantity'];	
			     	}
			    	$fixed_cost = $this->currency->convert($fixed_cost, 'USD', $this->currency->getCode());						
			    } else {
			    	$request = $this->_build_qoute_request( $weight, $use_width, $use_length, $use_height, $address ); 
			    	if ($request) {
			    		$new_quote_data = $this->_process_request( $request, $address, $weight );
			    	}	
			    }

			    //merge data and accumulate shipping cost
			    if ( $quote_data) {
			    	foreach ($quote_data as $key => $value) {
			    		if ( isset($quote_data[$key]) ) {
			    			if ($fixed_cost >= 0){
			    				$quote_data[$key]['cost'] += $fixed_cost;
			    			} else {
			    		 		$quote_data[$key]['cost'] +=  $new_quote_data[$key]['cost'];						
			    			}
			    		} else {
			    			$quote_data[$key] = $value;
			    			if ($fixed_cost >= 0){
			    				$quote_data[$key]['cost'] = $fixed_cost;
			    			}						
			    		}
			    		$quote_data[$key]['text'] = $this->currency->format(
			    			$this->tax->calculate(
			    				$this->currency->convert($quote_data[$key]['cost'], 'USD', $this->currency->getCode()), 
			    				$this->config->get('default_usps_tax_class_id'), $this->config->get('config_tax')
			    			)
			    		);	
			    	}
			    } else if ( $new_quote_data ) {
			    	$quote_data = $new_quote_data;
			    }
			}
			
	
	  		if ($quote_data) {
				
				$title = $this->language->get('text_title');
									
				$total_weight = $this->cart->getWeight();					
									
				if ($this->config->get('default_usps_display_weight')) {	  
					$title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($total_weight, $this->config->get('config_weight_class')) . ')';
				}		

				if ( isset($quote_data[0]['error'])) {
		      		$method_data = array(
		        	    'id'         => 'default_usps',
		        	    'title'      => $title,
		        	    'quote'      => $quote_data,
					    'sort_order' => $this->config->get('default_usps_sort_order'),
		        	    'error'      => 'Error Processing USPS'
		      		);						
				} else {
		      		$method_data = array(
		        	    'id'         => 'default_usps',
		        	    'title'      => $title,
		        	    'quote'      => $quote_data,
					    'sort_order' => $this->config->get('default_usps_sort_order'),
		        	    'error'      => FALSE
		      		);						
				
				}
			}
		}
	
		return $method_data;
	}	
	
	public function _build_qoute_request( $weight, $width, $length, $height, $address ) {

		$postcode = str_replace(' ', '', $address['postcode']);
		$weight = ($weight < 0.1 ? 0.1 : $weight);
		$pounds = floor($weight);
		$ounces = round(16 * ($weight - floor($weight)));

		if ($address['iso_code_2'] == 'US') { 
				
			$xml  = '<RateV3Request USERID="' . $this->config->get('default_usps_user_id') . '" PASSWORD="' . $this->config->get('default_usps_password') . '">';
				
			$xml .= '	<Package ID="1">';
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
			$xml .= '		<Width>' . $width . '</Width>';
			$xml .= '		<Length>' . $length . '</Length>';
			$xml .= '		<Height>' . $height . '</Height>';				
			    				
			// Calculate girth based on usps calculation if it is not provided
			if ( $this->config->get('default_usps_girth') ){
			    $xml .= '	<Girth>' . $this->config->get('default_usps_girth') . '</Girth>';
			}
			else{
			    $xml .= '	<Girth>' . (round(((float)$width + (float)$length * 2 + (float)$height * 2), 1)) . '</Girth>';
			}
			$xml .=	'		<Machinable>' . ($this->config->get('default_usps_machinable') ? 'true' : 'false') . '</Machinable>';				
			$xml .=	'	</Package>';
			$xml .= '</RateV3Request>';
			
			return 'API=RateV3&XML=' . urlencode($xml);
		} else {
			//load all countires and codes
			$this->loadModel('localisation/country');
        	$countries = $this->model_localisation_country->getCountries();
		    foreach ($countries as $item) {
		        $country[$item['iso_code_2']] = $item['name'];
		    }
		    
			if (isset($country[$address['iso_code_2']])) {
			    $xml  = '<IntlRateV2Request USERID="' . $this->config->get('default_usps_user_id') . '" PASSWORD="' . $this->config->get('default_usps_password') . '">';
			    $xml .=	'	<Package ID="0">';
			    $xml .=	'		<Pounds>' . $pounds . '</Pounds>';
			    $xml .=	'		<Ounces>' . $ounces . '</Ounces>';
				$xml .=	'		<MailType>All</MailType>';
				$xml .=	'		<GXG>';
				$xml .=	'		  <POBoxFlag>N</POBoxFlag>';
				$xml .=	'		  <GiftFlag>N</GiftFlag>';
				$xml .=	'		</GXG>';
				$xml .=	'		<ValueOfContents>' . $this->cart->getSubTotal() . '</ValueOfContents>';
			    $xml .=	'		<Country>' . $country[$address['iso_code_2']] . '</Country>';				
				// International only supports RECT and NONRECT
				if ($this->config->get('default_usps_container') == 'VARIABLE') {
				    $use_conteiner = 'NONRECTANGULAR';
				} else {
				    $use_conteiner = $this->config->get('default_usps_container');
				}
				$xml .=	'		<Container>' . $use_conteiner . '</Container>';
			    $xml .=	'		<Size>' . $this->config->get('default_usps_size') . '</Size>';
			    $xml .= '		<Width>' . $width . '</Width>';
			    $xml .= '		<Length>' . $length . '</Length>';
			    $xml .= '		<Height>' . $height . '</Height>';
			    $xml .= '		<Girth>' . $this->config->get('default_usps_girth') . '</Girth>';
			    $xml .= '		<CommercialFlag>N</CommercialFlag>';		    	    
			    $xml .=	'	</Package>';
			    $xml .=	'</IntlRateV2Request>';
			    return 'API=IntlRateV2&XML=' . urlencode($xml);
			} else {
			    return FALSE;	
			}
		}			
			
	}
	
	public function _process_request( $request, $address, $weight ) {
		$return_data = array();
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
		    if ($rate_v3_response || $intl_rate_response) {
		    	if ($address['iso_code_2'] == 'US') { 
		    		$allowed = array(0, 1, 2, 3, 4, 5, 6, 7, 12, 13, 16, 17, 18, 19, 22, 23, 25, 27, 28);
		    		
		    		$package = $rate_v3_response->getElementsByTagName('Package')->item(0);
		    		
		    		$postages = $package->getElementsByTagName('Postage');
		    		    		
		    		foreach ($postages as $postage) {
		    		    $classid = $postage->getAttribute('CLASSID');
		    		    
		    		    if (in_array($classid, $allowed) && $this->config->get('default_usps_domestic_' . $classid)) {
		
		    		    	$cost = $postage->getElementsByTagName('Rate')->item(0)->nodeValue;
		    		    	$title = $postage->getElementsByTagName('MailService')->item(0)->nodeValue;
		    		    	$title = preg_replace('/\&lt;sup\&gt;\&amp;reg;\&lt;\/sup\&gt;/', '<sup>&reg;</sup>', $title);
		    		    	
		    		    	$return_data[$classid] = array(
		    		    		'id'           => 'default_usps.' . $classid,
		    		    		'title'        => $title,
		    		    		'cost'         => $this->currency->convert($cost, 'USD', $this->currency->getCode()),
		    		    		'tax_class_id' => 0,
		    		    		'text'         => $this->currency->format($this->tax->calculate($this->currency->convert($cost, 'USD', $this->currency->getCode()), $this->config->get('default_usps_tax_class_id'), $this->config->get('config_tax')))
		    		    	);							
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
		    				
		    				$return_data[$id] = array(
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
		    	$return_data[0] = array(
		    		'id'         => 'default_usps',
		    		'error'      => $error->getElementsByTagName('Description')->item(0)->nodeValue
		    	);
	        	$error = new AError('Error : default_usps processing error. '  . $error->getElementsByTagName('Description')->item(0)->nodeValue );
            	$error->toLog()->toDebug();
            	$error->toMessages();		    						
		    }
		}
		
		return $return_data;
	}
	
}