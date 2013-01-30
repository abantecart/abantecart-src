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
	
	/*
	* Set tax country ID and zone ID for the session 
	* Also it loads available taxes for the zone
	*/
	
	public function setZone($country_id, $zone_id) {
		$country_id = (int)$country_id;
		$zone_id = (int)$zone_id;
		$results = $this->getTaxes($country_id, $zone_id);
		$this->taxes = array();
		foreach ($results as $result) {
      		$this->taxes[$result['tax_class_id']][] = array(
        		'rate'        => $result['rate'],
				'rate_prefix'    => $result['rate_prefix'],
				'threshold_condition'    => $result['threshold_condition'],
				'threshold'    => $result['threshold'],
        		'description' => $result['description'],
				'priority'    => $result['priority']
      		);
    	}

		$this->session->data['country_id'] = $country_id;
		$this->session->data['zone_id'] = $zone_id;
	}

	/*
	* Get available tax classes for country ID and zone ID
	* 
	*/

	public function getTaxes($country_id, $zone_id){
		$country_id = (int)$country_id;
		$zone_id = (int)$zone_id;

		$cache_name = 'tax_class.'.$country_id.'.'.$zone_id;
		$results = $this->cache->get($cache_name);

		if(is_null($results)){
			$sql = "SELECT tr.tax_class_id,
							tr.rate AS rate, tr.rate_prefix AS rate_prefix, 
							tr.threshold_condition AS threshold_condition, tr.threshold AS threshold,
							CASE WHEN tr.description='' THEN tc.title ELSE tr.description END as description,
							tr.priority
					FROM " . DB_PREFIX . "tax_rates tr
					LEFT JOIN " . DB_PREFIX . "tax_classes tc ON tc.tax_class_id = tr.tax_class_id
					WHERE (tr.zone_id = '0' OR tr.zone_id = '" . $zone_id . "')
						AND tr.location_id in (SELECT z2l.location_id
											   FROM " . DB_PREFIX . "zones_to_locations z2l, " . DB_PREFIX . "locations l
											   WHERE z2l.location_id = l.location_id and z2l.zone_id = '" . $zone_id . "')
					ORDER BY tr.priority ASC";
			$tax_rate_query = $this->db->query( $sql );
			$results = $tax_rate_query->rows;
			$this->cache->set($cache_name,$results);
		}
		return $results;
	}
	
	/*
	* Add Calculated tax based on provided $tax_class_id
	* If $calculate switch passed as false skip tax calculation. 
	* This is used in display of product price with tax added or not (based on config_tax )
	* 
	*/
  	public function calculate($value, $tax_class_id, $calculate = TRUE) {
		if (($calculate) && (isset($this->taxes[$tax_class_id])))  {
      		return $value + $this->calcTotalTaxAmount($value, $tax_class_id);
    	} else {
    		//skip culculation
      		return $value;
    	}
  	}
        
	/*
	* Culculate total applicable tax amount based on the amount provided 
	* Input: $amount and $tax_class_id
	* Output: $tax amount 		
	*/
  	public function calcTotalTaxAmount($amount, $tax_class_id) {
		$total_tax_amount = 0.0;
		if (isset($this->taxes[$tax_class_id])) {
			foreach ($this->taxes[$tax_class_id] as $tax_rate) {
				$total_tax_amount += $this->calcTaxAmount($amount, $tax_rate);			
			}					
		}
    	return $total_tax_amount;
	}
	
	/*
	* Culculate applicable tax amount based on the amount and tax rate record provided
	* Input: $amount and $tax_rate_record (array)
	* Output: $tax amount 		
	*/
  	public function calcTaxAmount($amount, $tax_rate = array() ) {
		$tax_amount = 0.0;
		if (!empty($tax_rate) && isset($tax_rate['rate'])) {
			//Validate tax class rules if condition present and see if applicable
			if ( $tax_rate['threshold_condition'] && is_numeric($tax_rate['threshold']) ) {
			    if ( !$this->_compare($amount, $tax_rate['threshold'], $tax_rate['threshold_condition']) ) {
			    	//does not match. get out
			    	return 0.0;
			    }
			}

			if ( $tax_rate['rate_prefix'] == '$'  ) {
			    //this is absolute value rate in default currency 
			    $tax_amount = $tax_rate['rate'];
			} else {
			    //This is percent based rate
			    $tax_amount = $amount * $tax_rate['rate'] / 100;
			}
		}
    	return $tax_amount;
	}

	/*
	* Get array with applicable rates for tax class based on the provided amount
	* Array returns Absolute and Percent rates in separate arrays
	*/
  	public function getAplicableRates($amount, $tax_class_id) {
  		$rates = array();
		if (isset($this->taxes[$tax_class_id])) {
			foreach ($this->taxes[$tax_class_id] as $tax_rate) {
				if (!empty($tax_rate) && isset($tax_rate['rate'])) {
					if ( $tax_rate['threshold_condition'] && is_numeric($tax_rate['threshold']) ) {
						if ( !$this->_compare($amount, $tax_rate['threshold'], $tax_rate['threshold_condition']) ) {
							continue;
						}
					}
					if ( $tax_rate['rate_prefix'] == '$'  ) {
					    $rates['absolute'][] = $tax_rate['rate'];   
					} else {
					    $rates['percent'][] = $tax_rate['rate'];  
					}					
				}
			}
		}
		return $rates;
	}
		
	/*
	* Get accumulative tax rate (depricated) 
	* 
	*/
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

    private function _compare($value1, $value2, $operator) {
        switch ($operator) {
            case 'eq':
                return ($value1 == $value2);
                break;
            case 'ne':
                return ($value1 != $value2);
                break;
            case 'le':
                return ($value1 <= $value2);
                break;
            case 'ge':
                return ($value1 >= $value2);
                break;
            case 'lt':
                return ($value1 < $value2);
                break;
            case 'gt':
                return ($value1 > $value2);
                break;
            default:
                return false;
                break;
        }
    }
}
?>