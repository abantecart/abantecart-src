<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2012 Belavier Commerce LLC

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

final class ALength {
	private $lengths = array();
	
	public function __construct($registry) {
		$this->db = $registry->get('db');
		$this->config = $registry->get('config');

		$length_class_query = $this->db->query("SELECT *
												FROM " . DB_PREFIX . "length_classes mc
												LEFT JOIN " . DB_PREFIX . "length_class_descriptions mcd
													ON (mc.length_class_id = mcd.length_class_id)
												WHERE mcd.language_id = '" . (int)$this->config->get('storefront_language_id') . "'");
    
    	foreach ($length_class_query->rows as $result) {
      		$this->lengths[strtolower($result['unit'])] = array(
				'length_class_id' => $result['length_class_id'],
        		'unit'            => $result['unit'],
        		'title'           => $result['title'],
				'value'           => $result['value']
      		);
    	}
  	}

	/*
	* convert length unit based
	*/
	  
  	public function convert($value, $unit_from, $unit_to) {
		if ($unit_from == $unit_to || is_null($unit_to) || is_null($unit_from)) {
      		return $value;
		}
        if(empty($value)) return 0;
		
		if (isset($this->lengths[strtolower($unit_from)])) {
			$from = $this->lengths[strtolower($unit_from)]['value'];
		} else {
			$from = 0;
		}
		
		if (isset($this->lengths[strtolower($unit_to)])) {
			$to = $this->lengths[strtolower($unit_to)]['value'];
		} else {
			$to = 0;
		}		
		
      	return $value * ($to / $from);
  	}

	/*
	* convert length id based
	*/
	
  	public function convertByID($value, $from_id, $to_id) {
		return $this->convert( $value, $this->getUnit($from_id), $this->getUnit($to_id) );
	}

	/*
	* convert format unit based
	*/
	public function format($value, $unit, $decimal_point = '.', $thousand_point = ',') {
		if ( isset( $this->lengths[$unit]['unit']) ) {		
    		return number_format($value, 2, $decimal_point, $thousand_point) . $this->lengths[$unit]['unit'];
    	} else {
    		return number_format($value, 2, $decimal_point, $thousand_point);
    	}
  	}

	/*
	* convert format id based
	*/
	public function formatByID($value, $length_class_id, $decimal_point = '.', $thousand_point = ',') {
		return $this->format($value, $this->getUnit($length_class_id), $decimal_point, $thousand_point);
	}
	
	/*
	* get length unit code based on $length_class_id
	*/
	
	public function getUnit($length_class_id) {
		foreach ($this->lengths as $lth) {
			if (isset($lth[$length_class_id])) {
    			return $lth['unit'];
			}		
		}
		return '';
	}	  	

	/*
	* get length_class_id based on unit code
	*/
	
	public function getClassID($length_unit) {
		if (isset($this->lengths[$length_unit])) {
    		return $this->lengths[$length_unit]['length_class_id'];
		} else {
			return '';
		}
	}	  	
  	
}
?>