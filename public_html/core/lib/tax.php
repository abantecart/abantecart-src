<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

final class ATax {
	private $taxes = array();
	private $registry;
	private $config;
	private $cache;
	private $db;
	private $session;

	public function __construct($registry) {
		$this->registry = $registry;
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');	
		$this->session = $registry->get('session');
		$this->cache = $registry->get('cache');

		if (isset($this->session->data['country_id']) && isset($this->session->data['zone_id'])) {
			$country_id = $this->session->data['country_id'];
	 		$zone_id = $this->session->data['zone_id'];
		} else {
			if($this->config->get('config_tax_store')){
				$country_id = $this->config->get('config_country_id');
				$zone_id = $this->config->get('config_zone_id');
			}else{
				$country_id = $zone_id = 0;
			}
		}
		$this->setZone($country_id, $zone_id);
  	}
	
	public function setZone($country_id, $zone_id) {
		$country_id = (int)$country_id;
		$zone_id = (int)$zone_id;
		$results = $this->getTaxes($country_id, $zone_id);
		$this->taxes = array();
		foreach ($results as $result) {
      		$this->taxes[$result['tax_class_id']][] = array(
        		'rate'        => $result['rate'],
        		'description' => $result['description'],
				'priority'    => $result['priority']
      		);
    	}

		$this->session->data['country_id'] = $country_id;
		$this->session->data['zone_id'] = $zone_id;
	}

	public function getTaxes($country_id, $zone_id){
		$country_id = (int)$country_id;
		$zone_id = (int)$zone_id;

		$cache_name = 'tax_class.'.$country_id.'.'.$zone_id;
		$results = $this->cache->get($cache_name);

		if(is_null($results)){
			$sql = "SELECT tr.tax_class_id, tr.rate AS rate, tr.description, tr.priority
					FROM " . DB_PREFIX . "tax_rates tr
					WHERE (tr.zone_id = '0' OR tr.zone_id = '" . $zone_id . "')
						AND tr.location_id in (SELECT location_id
												FROM " . DB_PREFIX . "zones_to_locations z2l
												WHERE z2l.country_id = '0' OR z2l.country_id = '" . $country_id . "')
					ORDER BY tr.priority ASC";
			$tax_rate_query = $this->db->query( $sql );
			$results = $tax_rate_query->rows;
			$this->cache->set($cache_name,$results);
		}
		return $results;
	}
	
  	public function calculate($value, $tax_class_id, $calculate = TRUE) {

		if (($calculate) && (isset($this->taxes[$tax_class_id])))  {
			$rate = $this->getRate($tax_class_id);
			
      		return $value + ($value * $rate / 100);
    	} else {
      		return $value;
    	}
  	}
        
  	public function getRate($tax_class_id) {
		if (isset($this->taxes[$tax_class_id])) {
			$rate = 0;
			
			foreach ($this->taxes[$tax_class_id] as $tax_rate) {
				$rate += $tax_rate['rate'];
			}		
			
			return $rate;
		} else {
    		return 0;
		}
	}
  
  	public function getDescription($tax_class_id) {
		return (isset($this->taxes[$tax_class_id]) ? $this->taxes[$tax_class_id] : array());
  	}
  
  	public function has($tax_class_id) {
		return isset($this->taxes[$tax_class_id]);
  	}
}
?>