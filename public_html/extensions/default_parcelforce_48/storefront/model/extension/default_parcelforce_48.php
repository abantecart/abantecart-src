<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

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

class ModelExtensionDefaultParcelforce48 extends Model {
	function getQuote($address) {
		$this->load->language('default_parcelforce_48/default_parcelforce_48');
		
		if ($this->config->get('default_parcelforce_48_status')) {
      		$taxes = $this->tax->getTaxes((int)$address['country_id'], (int)$address['zone_id']);
      		if (!$this->config->get('default_parcelforce_48_location_id')) {
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
			$weight = $this->cart->getWeight();
			$sub_total = $this->cart->getSubTotal();
			
			$rates = explode(',', $this->config->get('default_parcelforce_48_rate'));
			
			foreach ($rates as $rate) {
  				$data = explode(':', $rate);
  					
				if ($data[0] >= $weight) {
					if (isset($data[1])) {
    					$cost = $data[1];
					}
					
   					break;
  				}
			}

			$rates = explode(',', $this->config->get('default_parcelforce_48_compensation'));
			
			foreach ($rates as $rate) {
  				$data = explode(':', $rate);
  				
				if ($data[0] >= $sub_total) {
					if (isset($data[1])) {
    					$compensation = $data[1];
					}
					
   					break; 
  				}
			}
			
			$quote_data = array();
			
			if ((float)$cost) {
				$text = $this->language->get('text_description');
			
				if ($this->config->get('default_parcelforce_48_display_weight')) {
					$text .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
				}
			
				if ($this->config->get('default_parcelforce_48_display_insurance') && (float)$compensation) {
					$text .= ' (' . $this->language->get('text_insurance') . ' ' . $this->currency->format($compensation) . ')';
				}		

				if ($this->config->get('default_parcelforce_48_display_time')) {
					$text .= ' (' . $this->language->get('text_time') . ')';
				}	
				
      			$quote_data['default_parcelforce_48'] = array(
        			'id'           => 'default_parcelforce_48.default_parcelforce_48',
        			'title'        => $text,
        			'cost'         => $cost,
        			'tax_class_id' => $this->config->get('default_parcelforce_48_tax'),
					'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('default_parcelforce_48_tax'), $this->config->get('config_tax')))
      			);

      			$method_data = array(
        			'id'         => 'default_parcelforce_48',
        			'title'      => $this->language->get('text_title'),
        			'quote'      => $quote_data,
					'sort_order' => $this->config->get('default_parcelforce_48_sort_order'),
        			'error'      => FALSE
      			);
			}
		}
	
		return $method_data;
	}
}