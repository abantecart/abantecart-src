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

class ModelExtensionDefaultWeight extends Model {    
  	public function getQuote($address) {
		$this->load->language('default_weight/default_weight');
		
		$quote_data = array();

		if ($this->config->get('default_weight_status')) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "locations ORDER BY name");
		
			foreach ($query->rows as $result) {
   				if ($this->config->get('default_weight_' . $result['location_id'] . '_status')) {
   					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zones_to_locations WHERE location_id = '" . (int)$result['location_id'] . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
				
					if ($query->num_rows) {
       					$status = TRUE;
   					} else {
       					$status = FALSE;
   					}
				} else {
					$status = FALSE;
				}
			
				if ($status) {
					$cost = '';
					$weight = $this->cart->getWeight();
					
					$rates = explode(',', $this->config->get('default_weight_' . $result['location_id'] . '_rate'));
					
					foreach ($rates as $rate) {
  						$data = explode(':', $rate);
  					
						if ($data[0] >= $weight) {
							if (isset($data[1])) {
    							$cost = $data[1];
							}
					
   							break;
  						}
					}
					
					if ((string)$cost != '') { 
      					$quote_data['default_weight_' . $result['location_id']] = array(
        					'id'           => 'default_weight.default_weight_' . $result['location_id'],
        					'title'        => $result['name'] . '  (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')',
        					'cost'         => $this->tax->calculate($cost, $this->config->get('default_weight_tax_class_id'), $this->config->get('config_tax')),
							'tax_class_id' => $this->config->get('default_weight_tax_class_id'),
        					'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('default_weight_tax_class_id'), $this->config->get('config_tax')))
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