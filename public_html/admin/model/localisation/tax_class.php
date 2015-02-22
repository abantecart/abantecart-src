<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

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

/**
 * Class ModelLocalisationTaxClass
 */
class ModelLocalisationTaxClass extends Model {
	/**
	 * @param array $data
	 * @return int
	 */
	public function addTaxClass($data) {
		$this->db->query( "INSERT INTO " . $this->db->table("tax_classes") . " 
				SET date_added = NOW() ");
				
		$tax_class_id = $this->db->getLastId();

		foreach ($data['tax_class'] as $language_id => $value) {
			$this->language->replaceDescriptions('tax_class_descriptions',
											 array('tax_class_id' => (int)$tax_class_id),
											 array($language_id => array(
												 'title' => $value['title'],
												 'description' => $value['description'],
											 )) );
		}
				
		$this->cache->delete('tax_class');
		return $tax_class_id;
	}

	/**
	 * @param int $tax_class_id
	 * @param array $data
	 * @return int
	 */
	public function addTaxRate($tax_class_id, $data) {
		$this->db->query(
			"INSERT INTO " . $this->db->table("tax_rates") . " 
			SET location_id = '"  . (int)$data['location_id'] . "',
			    zone_id = '"  . (int)$data['zone_id'] . "',
				priority = '"  . (int)$data['priority'] . "',
				rate = '"  . (float)$data['rate'] . "',
				rate_prefix = '" . $this->db->escape($data['rate_prefix']) . "',
				threshold_condition = '" . $this->db->escape($data['threshold_condition']) . "',
				threshold = '"  . (float)$data['threshold'] . "',
				tax_class_id = '"  . (int)$tax_class_id . "',
				date_added = NOW()");

		$tax_rate_id = $this->db->getLastId();
				
		foreach ($data['tax_rate'] as $language_id => $value) {
			$this->language->replaceDescriptions('tax_rate_descriptions',
											 array('tax_rate_id' => (int)$tax_rate_id),
											 array($language_id => array(
												 'description' => $value['description'],
											 )) );
		}
						
		$this->cache->delete('tax_class');
		return $tax_rate_id;
	}

	/**
	 * @param int $tax_class_id
	 * @param array $data
	 */
	public function editTaxClass($tax_class_id, $data) {

		if ( count($data['tax_class']) ) {
			$update = array('date_modified = NOW()');
			$this->db->query("UPDATE " . $this->db->table("tax_classes") . " 
							  SET ". implode(',', $update) ."
							  WHERE tax_class_id = '" . (int)$tax_class_id . "'");
							  
			foreach ($data['tax_class'] as $language_id => $value) {
				//save only if value defined
				if (isset($value['title'])) {
					$this->language->replaceDescriptions('tax_class_descriptions',
											 array('tax_class_id' => (int)$tax_class_id),
											 array($language_id => array(
												 'title' => $value['title'],
											 )) );
				}
				if (isset($value['description'])) {
					$this->language->replaceDescriptions('tax_class_descriptions',
											 array('tax_class_id' => (int)$tax_class_id),
											 array($language_id => array(
												 'description' => $value['description'],
											 )) );
				}
			}
							  						  
			$this->cache->delete('tax_class');
		}
	}

	/**
	 * @param int $tax_rate_id
	 * @param array $data
	 */
	public function editTaxRate($tax_rate_id, $data) {
		$fields = array('location_id', 'zone_id', 'priority','rate_prefix', 'threshold_condition' );
		$update = array('date_modified = NOW()');
		foreach ( $fields as $f ) {
			if ( isset($data[$f]) )
				$update[] = $f." = '".$this->db->escape($data[$f])."'";
		}
		$update[] = "rate = '" . preformatFloat($data['rate'], $this->language->get('decimal_point'))."'";
		$update[] = "threshold = '" . preformatFloat($data['threshold'], $this->language->get('decimal_point'))."'";
		
		if ( !empty($update) ) {
			$this->db->query("UPDATE " . $this->db->table("tax_rates") . " 
								SET ". implode(',', $update) ."
								WHERE tax_rate_id = '" . (int)$tax_rate_id . "'");

			$this->cache->delete('tax_class');
			$this->cache->delete('location');
		} 
		if (count($data['tax_rate'])) {
			foreach ($data['tax_rate'] as $language_id => $value) {
				$this->language->replaceDescriptions('tax_rate_descriptions',
												 array('tax_rate_id' => (int)$tax_rate_id),
												 array($language_id => array(
													 'description' => $value['description'],
												 )) );
			}		
			$this->cache->delete('tax_class');
			$this->cache->delete('location');
		}
	}

	/**
	 * @param int $tax_class_id
	 */
	public function deleteTaxClass($tax_class_id) {
		$this->db->query("DELETE FROM " . $this->db->table("tax_classes") . " 
							WHERE tax_class_id = '" . (int)$tax_class_id . "'");
		$this->db->query("DELETE FROM " . $this->db->table("tax_class_descriptions") . " 
							WHERE tax_class_id = '" . (int)$tax_class_id . "'");
		$this->db->query("DELETE FROM " . $this->db->table("tax_rates") . " 
							WHERE tax_class_id = '" . (int)$tax_class_id . "'");
		$this->cache->delete('tax_class');
	}

	/**
	 * @param int $tax_rate_id
	 */
	public function deleteTaxRate($tax_rate_id) {
		$this->db->query("DELETE FROM " . $this->db->table("tax_rates") . " 
							WHERE tax_rate_id = '" . (int)$tax_rate_id . "'");
		$this->db->query("DELETE FROM " . $this->db->table("tax_rate_descriptions") . " 
							WHERE tax_rate_id = '" . (int)$tax_rate_id . "'");
		$this->cache->delete('tax_class');
	}

	/**
	 * @param int $tax_class_id
	 * @return array
	 */
	public function getTaxClass($tax_class_id) {
		$language_id = $this->session->data['content_language_id'];

		$query = $this->db->query("SELECT t.tax_class_id, td1.title, td1.description
									FROM " . $this->db->table("tax_classes") . " t
									LEFT JOIN " . $this->db->table("tax_class_descriptions") . " td1 ON 
									(t.tax_class_id = td1.tax_class_id AND td1.language_id = '" . (int)$language_id . "')
									WHERE t.tax_class_id = '" . (int)$tax_class_id . "'");
		$ret_data = $query->row;
		$ret_data['tax_class'] = $this->getTaxClassDescriptions($tax_class_id); 
		return $ret_data;
	}

	/**
	 * @param int $tax_class_id
	 * @return array
	 */
	public function getTaxClassDescriptions($tax_class_id) {
		$tax_data = array();
		$query = $this->db->query( "SELECT *
									FROM " . $this->db->table("tax_class_descriptions") . " 
									WHERE tax_class_id = '" . (int)$tax_class_id . "'");
		foreach ($query->rows as $result) {
			$tax_data[$result['language_id']] = array('title' => $result['title'], 'description' => $result['description'] );
		}	
		return $tax_data;
	}

	/**
	 * @param int $tax_rate_id
	 * @return array
	 */
	public function getTaxRate($tax_rate_id) {
		$language_id = $this->session->data['content_language_id'];

		$query = $this->db->query("SELECT td1.*, t.*
									FROM " . $this->db->table("tax_rates") . " t
									LEFT JOIN " . $this->db->table("tax_rate_descriptions") . " td1 ON 
									(t.tax_rate_id = td1.tax_rate_id AND td1.language_id = '" . (int)$language_id . "')
									WHERE t.tax_rate_id = '" . (int)$tax_rate_id . "'");
		$ret_data = $query->row;
		$ret_data['tax_rate'] = $this->getTaxRateDescriptions($tax_rate_id); 
		return $ret_data;
	}

	/**
	 * @param int $tax_rate_id
	 * @return array
	 */
	public function getTaxRateDescriptions($tax_rate_id) {
		$tax_data = array();
		$query = $this->db->query( "SELECT *
									FROM " . $this->db->table("tax_rate_descriptions") . " 
									WHERE tax_rate_id = '" . (int)$tax_rate_id . "'");
		foreach ($query->rows as $result) {
			$tax_data[$result['language_id']] = array('description' => $result['description']);
		}	
		return $tax_data;
	}

	/**
	 * @param array $data
	 * @param string $mode
	 * @return array
	 */
	public function getTaxClasses($data = array(), $mode = 'default') {
		$language_id = $this->session->data['content_language_id'];
		$default_language_id = $this->language->getDefaultLanguageID();

    	if ($data || $mode == 'total_only') {
			if ($mode == 'total_only') {
				$sql = "SELECT count(*) as total FROM " . $this->db->table("tax_classes") . " t ";
			}
			else {
				$sql = "SELECT t.tax_class_id, 
							   td.title,
							   td.description  
						FROM " . $this->db->table("tax_classes") . " t ";
			}
			$sql .= "LEFT JOIN " . $this->db->table("tax_class_descriptions") . " td ON (t.tax_class_id = td.tax_class_id AND td.language_id = '" . (int)$language_id . "') ";
				
		    if ( !empty($data['subsql_filter']) )
				$sql .= " WHERE ".$data['subsql_filter'];

			//If for total, we done bulding the query
			if ($mode == 'total_only') {
		 	   $query = $this->db->query($sql);
		 	   return $query->row['total'];
			}

			$sql .= " ORDER BY td.title";	
			
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
			$tax_class_data = $this->cache->get('tax_class.all', $language_id);

			if (is_null($tax_class_data)) {
				if ($language_id == $default_language_id) {
					$query = $this->db->query( "SELECT *
											FROM " . $this->db->table("tax_classes") . " t
											LEFT JOIN " . $this->db->table("tax_class_descriptions") . " td 
												ON (t.tax_class_id = td.tax_class_id AND td.language_id = '" . (int)$language_id . "') 
											");
							
				} else {
					//merge text for missing country translations. 
					$query = $this->db->query("SELECT t.tax_class_id, 
												COALESCE( td1.title,td2.title) as title, 
												COALESCE( td1.description,td2.description) as description
									FROM " . $this->db->table("tax_classes") . " t
									LEFT JOIN " . $this->db->table("tax_class_descriptions") . " td1 ON 
									(t.tax_class_id = td1.tax_class_id AND td1.language_id = '" . (int)$language_id . "')
									LEFT JOIN " . $this->db->table("tax_class_descriptions") . " td2 ON 
									(t.tax_class_id = td2.tax_class_id AND td2.language_id = '" . (int)$default_lang_id . "')
								");	
				}								
				$tax_class_data = $query->rows;
				$this->cache->set('tax_class.all', $tax_class_data, $language_id);
			}
			
			return $tax_class_data;			
		}
	}

	/**
	 * @param int $tax_class_id
	 * @return array
	 */
	public function getTaxRates($tax_class_id) {
		$language_id = $this->session->data['content_language_id'];
      	$query = $this->db->query("SELECT td.*, t.*
      	                            FROM " . $this->db->table("tax_rates") . " t
									LEFT JOIN " . $this->db->table("tax_rate_descriptions") . " td 
										ON (t.tax_rate_id = td.tax_rate_id AND td.language_id = '" . (int)$language_id . "') 
      	                            WHERE tax_class_id = '" . (int)$tax_class_id . "'");
		
		return $query->rows;
	}

	/**
	 * @param array $data
	 * @return array
	 */
	public function getTotalTaxClasses($data = array()) {
		return $this->getTaxClasses($data, 'total_only');
	}

	/**
	 * @param int $location_id
	 * @return int
	 */
	public function getTotalTaxRatesByLocationID($location_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total
      	                           FROM " . $this->db->table("tax_rates") . " 
      	                           WHERE location_id = '" . (int)$location_id . "'");
		
		return $query->row['total'];
	}		
}
