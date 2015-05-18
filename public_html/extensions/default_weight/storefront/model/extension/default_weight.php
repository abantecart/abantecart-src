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

class ModelExtensionDefaultWeight extends Model {    
  	public function getQuote($address) {
		$this->load->language('default_weight/default_weight');
		
		$quote_data = array();

		if ($this->config->get('default_weight_status')) {
			$query = $this->db->query("SELECT * FROM " . $this->db->table("locations") . " ORDER BY name");
		
			foreach ($query->rows as $result) {			
   				if ($this->config->get('default_weight_' . $result['location_id'] . '_status')) {
   					$query2 = $this->db->query("SELECT * FROM " . $this->db->table("zones_to_locations") . " WHERE location_id = '" . (int)$result['location_id'] . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");				
					if ($query2->num_rows) {
       					$status = TRUE;
   					} else {
       					$status = FALSE;
   					}
				} else {
					$status = FALSE;
				}

				if ($status) {
					$cost = '';

					$rates = explode(',', $this->config->get('default_weight_' . $result['location_id'] . '_rate'));

					//Process all products shipped together with not special shipping settings on a product level
					$b_products = $this->cart->basicShippingProducts(); 
					if ( count($b_products) > 0 ) {
					    $prod_ids = array();
					    foreach ($b_products as $prd) {
					    	$prod_ids[] = $prd['product_id'];
					    }
					    $weight = $this->cart->getWeight( $prod_ids );
						foreach ($rates as $rate) {
	  						$data = explode(':', $rate);
							if ($data[0] >= $weight) {
								if (isset($data[1])) {
	    							$cost = $data[1];
								}
	   							break;
	  						}
						}
					}
					
					//Process products that have special shipping settings
					$special_ship_products = $this->cart->specialShippingProducts();
					foreach ($special_ship_products as $product) {		    
					    $weight = $this->cart->getWeight( array($product['product_id']) );
					    if ($product['free_shipping']) {
					    	continue;
					    } else if($product['shipping_price'] > 0) {
					    	$fixed_cost = $product['shipping_price'];
					    	//If ship individually count every quintaty 
					     	if ($product['ship_individually']) {
					     		$cost = $cost + $fixed_cost * $product['quantity'];	
					     	} else {
					     		$cost = $cost + $fixed_cost;
					     	}
					    } else {
							foreach ($rates as $rate) {
				  				$data = explode(':', $rate);  					
								if ($data[0] >= $weight) {
									if (isset($data[1])) {
				    					$cost = $cost + $data[1];
									}
				   					break;
				  				}
							}
					    }
					}

					if ((string)$cost != '') { 
      					$quote_data['default_weight_' . $result['location_id']] = array(
        					'id'           => 'default_weight.default_weight_' . $result['location_id'],
        					'title'        => $result['name'] . '  (' . $this->language->get('text_weight') . ' ' . $this->weight->format($this->cart->getWeight(), $this->config->get('config_weight_class')) . ')',
        					'cost'         => $this->tax->calculate($cost, $this->config->get('default_weight_tax_class_id'), $this->config->get('config_tax')),
							'tax_class_id' => $this->config->get('default_weight_tax_class_id'),
        					'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('default_weight_tax_class_id'), $this->config->get('config_tax')))
      					);	
					}
					if ($this->cart->areAllFreeShipping()) { 
      					$quote_data['default_weight_' . $result['location_id']] = array(
        					'id'           => 'default_weight.default_weight_' . $result['location_id'],
        					'title'        => $result['name'] . '  (' . $this->language->get('text_weight') . ' ' . $this->weight->format($this->cart->getWeight(), $this->config->get('config_weight_class')) . ')',
        					'cost'         => $this->tax->calculate($cost, $this->config->get('default_weight_tax_class_id'), $this->config->get('config_tax')),
							'tax_class_id' => $this->config->get('default_weight_tax_class_id'),
        					'text'         => $this->language->get('text_free')
      					);					
					}
				}
			}
		}
		
		$method_data = array();
	
		if ($quote_data) {
      		$method_data = array(
        		'id'         => 'default_weight.default_weight',
        		'title'      => $this->language->get('text_title'),
        		'quote'      => $quote_data,
				'sort_order' => $this->config->get('default_weight_sort_order'),
        		'error'      => FALSE
      		);
		}

		return $method_data;
  	}
}