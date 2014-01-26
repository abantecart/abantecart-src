<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

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


class ModelExtensionDefaultCitylink extends Model {
	function getQuote($address) {
		$this->load->language('default_citylink/default_citylink');
		
		if ($this->config->get('default_citylink_status')) {
      		$taxes = $this->tax->getTaxes((int)$address['country_id'], (int)$address['zone_id']);

      		if (!$this->config->get('default_citylink_location_id')) {
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
			$cost = 0;
			
			$rates = explode(',', $this->config->get('default_citylink_rates'));

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
				
			$quote_data = array();
			
			if ((float)$cost) {
				$quote_data['default_citylink'] = array(
        			'id'           => 'default_citylink.default_citylink',
        			'title'        => $this->language->get('text_title') . '  (' . $this->language->get('text_weight') . ' ' . $this->weight->format($this->cart->getWeight(), $this->config->get('config_weight_class')) . ')',
        			'cost'         => $cost,
        			'tax_class_id' => $this->config->get('default_citylink_tax_class_id'),
					'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('default_citylink_tax_class_id'), $this->config->get('config_tax')))
      			);
				
      			$method_data = array(
        			'id'         => 'default_citylink',
        			'title'      => $this->language->get('text_title'),
        			'quote'      => $quote_data,
					'sort_order' => $this->config->get('default_citylink_sort_order'),
        			'error'      => FALSE
      			);
			}
		}
	
		return $method_data;
	}
}