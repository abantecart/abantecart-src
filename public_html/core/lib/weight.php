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

final class AWeight {
	private $weights = array();
	
	public function __construct($registry) {
		$this->db = $registry->get('db');
		$this->config = $registry->get('config');
		$sql = "SELECT *
				FROM " . DB_PREFIX . "weight_classes wc
				LEFT JOIN " . DB_PREFIX . "weight_class_descriptions wcd
					ON (wc.weight_class_id = wcd.weight_class_id)
				WHERE wcd.language_id = '" . (int)$this->config->get('storefront_language_id') . "'";
		$weight_class_query = $this->db->query($sql);
    	foreach ($weight_class_query->rows as $result) {
      		$this->weights[strtolower($result['unit'])] = array('weight_class_id' => $result['weight_class_id'],
																'title'           => $result['title'],
																'unit'            => $result['unit'],
																'value'           => $result['value'] );
    	}
  	}

	/*
	* convert weigth unit based
	*/
	  
  	public function convert($value, $unit_from, $unit_to) {
		if ($unit_from == $unit_to) {
      		return $value;
		}
		
		if (!isset($this->weights[strtolower($unit_from)]) || !isset($this->weights[strtolower($unit_to)])) {
			return $value;
		} else {			
			$from = $this->weights[strtolower($unit_from)]['value'];
			$to = $this->weights[strtolower($unit_to)]['value'];
		
			return $value * ($to / $from);
		}
  	}

	/*
	* convert weigth id based
	*/
	
  	public function convertByID($value, $from_id, $to_id) {
		return $this->convert( $value, $this->getUnit($from_id), $this->getUnit($to_id) );
	}

	/*
	* convert format unit based
	*/
	public function format($value, $unit, $decimal_point = '.', $thousand_point = ',') {
		if (isset($this->weights[strtolower($unit)])) {
    		return number_format($value, 2, $decimal_point, $thousand_point) . $this->weights[strtolower($unit)]['unit'];
		} else {
			return number_format($value, 2, $decimal_point, $thousand_point);
		}
	}

	/*
	* convert format id based
	*/
	public function formatByID($value, $weight_class_id, $decimal_point = '.', $thousand_point = ',') {
		return $this->format($value, $this->getUnit($weight_class_id), $decimal_point, $thousand_point);
	}

	/*
	* get weigth unit code based on $weigth_class_id
	*/
	
	public function getUnit($weight_class_id) {
		foreach ($this->weights as $wth) {
			if ( $wth['weight_class_id'] == $weight_class_id ) {
    			return $wth['unit'];
			}		
		}
		return '';
	}	  	

	/*
	* get weigth_class_id based on unit code
	*/
	
	public function getClassID($weight_unit) {
		if (isset($this->weights[$weight_unit])) {
    		return $this->weights[$weight_unit]['weight_class_id'];
		} else {
			return '';
		}
	}	  	
	
}
?>