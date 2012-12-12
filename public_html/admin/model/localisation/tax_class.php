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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ModelLocalisationTaxClass extends Model {
	public function addTaxClass($data) {
		$this->db->query(
			"INSERT INTO " . DB_PREFIX . "tax_classes
			SET title = '" . $this->db->escape($data['title']) . "',
				description = '" . $this->db->escape($data['description']) . "',
				date_added = NOW()");
		$this->cache->delete('tax_class');
		return $this->db->getLastId();
	}

	public function addTaxRate($tax_class_id, $data) {
		$this->db->query(
			"INSERT INTO " . DB_PREFIX . "tax_rates
			SET location_id = '"  . (int)$data['location_id'] . "',
			    zone_id = '"  . (int)$data['zone_id'] . "',
				priority = '"  . (int)$data['priority'] . "',
				rate = '"  . (float)$data['rate'] . "',
				rate_prefix = '" . $this->db->escape($data['rate_prefix']) . "',
				threshold_condition = '" . $this->db->escape($data['threshold_condition']) . "',
				threshold = '"  . (float)$data['threshold'] . "',
				description = '" . $this->db->escape($data['description']) . "',
				tax_class_id = '"  . (int)$tax_class_id . "',
				date_added = NOW()");
		$this->cache->delete('tax_class');
		return $this->db->getLastId();
	}
	
	public function editTaxClass($tax_class_id, $data) {

		$fields = array('title', 'description', );
		$update = array('date_modified = NOW()');
		foreach ( $fields as $f ) {
			if ( isset($data[$f]) )
				$update[] = "$f = '".$this->db->escape($data[$f])."'";
		}
		if ( !empty($update) ) {
			$this->db->query("UPDATE `" . DB_PREFIX . "tax_classes`
							  SET ". implode(',', $update) ."
							  WHERE tax_class_id = '" . (int)$tax_class_id . "'");
			$this->cache->delete('tax_class');
		}
	}

	public function editTaxRate($tax_rate_id, $data) {
		$fields = array('location_id', 'zone_id', 'priority', 'rate', 'description', 'rate_prefix', 'threshold_condition', 'threshold' );
		$update = array('date_modified = NOW()');
		foreach ( $fields as $f ) {
			if ( isset($data[$f]) )
				$update[] = "$f = '".$this->db->escape($data[$f])."'";
		}
		if ( !empty($update) ) {
			$this->db->query("UPDATE `" . DB_PREFIX . "tax_rates`
								SET ". implode(',', $update) ."
								WHERE tax_rate_id = '" . (int)$tax_rate_id . "'");
			$this->cache->delete('tax_class');
			$this->cache->delete('location');
		}
	}
	
	public function deleteTaxClass($tax_class_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "tax_classes
							WHERE tax_class_id = '" . (int)$tax_class_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "tax_rates
							WHERE tax_class_id = '" . (int)$tax_class_id . "'");
		$this->cache->delete('tax_class');
	}

	public function deleteTaxRate($tax_rate_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "tax_rates
							WHERE tax_rate_id = '" . (int)$tax_rate_id . "'");
		$this->cache->delete('tax_class');
	}
	
	public function getTaxClass($tax_class_id) {
		$query = $this->db->query("SELECT *
									FROM " . DB_PREFIX . "tax_classes
									WHERE tax_class_id = '" . (int)$tax_class_id . "'");
		return $query->row;
	}

	public function getTaxRate($tax_rate_id) {
		$query = $this->db->query("SELECT *
									FROM " . DB_PREFIX . "tax_rates
									WHERE tax_rate_id = '" . (int)$tax_rate_id . "'");
		return $query->row;
	}

	public function getTaxClasses($data = array(), $mode = 'default') {

    	if ($data || $mode == 'total_only') {
			if ($mode == 'total_only') {
				$sql = "SELECT count(*) as total FROM " . DB_PREFIX . "tax_classes";
			}
			else {
				$sql = "SELECT * FROM " . DB_PREFIX . "tax_classes";
			}    
				
		    if ( !empty($data['subsql_filter']) )
				$sql .= " WHERE ".$data['subsql_filter'];

			//If for total, we done bulding the query
			if ($mode == 'total_only') {
		 	   $query = $this->db->query($sql);
		 	   return $query->row['total'];
			}

			$sql .= " ORDER BY title";	
			
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}
			
			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}					

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}	
			
				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}
			
	  		$query = $this->db->query($sql);
		
			return $query->rows;		
		} else {
			$tax_class_data = $this->cache->get('tax_class.all');

			if (is_null($tax_class_data)) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tax_classes");
				$tax_class_data = $query->rows;
				$this->cache->set('tax_class.all', $tax_class_data);
			}
			
			return $tax_class_data;			
		}
	}
	
	public function getTaxRates($tax_class_id) {
      	$query = $this->db->query("SELECT *
      	                            FROM " . DB_PREFIX . "tax_rates
      	                            WHERE tax_class_id = '" . (int)$tax_class_id . "'");
		
		return $query->rows;
	}

	public function getTotalTaxClasses($data = array()) {
		return $this->getTaxClasses($data, 'total_only');
	}	
	
	public function getTotalTaxRatesByLocationID($location_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total
      	                           FROM " . DB_PREFIX . "tax_rates
      	                           WHERE location_id = '" . (int)$location_id . "'");
		
		return $query->row['total'];
	}		
}
?>