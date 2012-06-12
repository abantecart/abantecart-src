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
	  
  	public function convert($value, $from, $to) {
		if ($from == $to) {
      		return $value;
		}
		
		if (isset($this->lengths[strtolower($from)])) {
			$from = $this->lengths[strtolower($from)]['value'];
		} else {
			$from = 0;
		}
		
		if (isset($this->lengths[strtolower($to)])) {
			$to = $this->lengths[strtolower($to)]['value'];
		} else {
			$to = 0;
		}		
		
      	return $value * ($to / $from);
  	}

	public function format($value, $unit, $decimal_point = '.', $thousand_point = ',') {
    	return number_format($value, 2, $decimal_point, $thousand_point) . $this->lengths[$unit]['unit'];
  	}
}
?>