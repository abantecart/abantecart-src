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

class ModelExtensionDefaultStorePickup extends Model {
	function getQuote($address) {
		$this->load->language('default_store_pickup/default_store_pickup');
		
		if ($this->config->get('default_store_pickup_status')) {
      		$query = $this->db->query("SELECT *
										FROM " . $this->db->table("zones_to_locations") . "
										WHERE location_id = '" . (int)$this->config->get('default_store_pickup_location_id') . "'
											AND country_id = '" . (int)$address['country_id'] . "'
											AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
		
      		if (!$this->config->get('default_store_pickup_location_id')) {
        		$status = TRUE;
      		} elseif ($query->num_rows) {
        		$status = TRUE;
      		} else {
        		$status = FALSE;
      		}
		} else {
			$status = FALSE;
		}
		
		$method_data = array();
	
		if ($status) {
			$quote_data = array();
			
      		$quote_data['default_store_pickup'] = array(
        		'id'           => 'default_store_pickup.default_store_pickup',
        		'title'        => $this->language->get('text_description'),
        		'cost'         => 0.00,
        		'tax_class_id' => 0,
				'text'         => $this->language->get('text_free')
      		);

      		$method_data = array(
        		'id'         => 'default_store_pickup',
        		'title'      => $this->language->get('text_title'),
        		'quote'      => $quote_data,
				'sort_order' => $this->config->get('default_store_pickup_sort_order'),
        		'error'      => FALSE
      		);
		}
	
		return $method_data;
	}
}