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
class ModelLocalisationCountry extends Model {
	public function addCountry($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "countries SET status = '" . (int)$data['status'] . "', name = '" . $this->db->escape($data['name']) . "', iso_code_2 = '" . $this->db->escape($data['iso_code_2']) . "', iso_code_3 = '" . $this->db->escape($data['iso_code_3']) . "', address_format = '" . $this->db->escape($data['address_format']) . "'");
	
		$this->cache->delete('country');
		return $this->db->getLastId();
	}
	
	public function editCountry($country_id, $data) {

		$fields = array('status', 'name', 'iso_code_2', 'iso_code_3', 'address_format', );
		$update = array();
		foreach ( $fields as $f ) {
			if ( isset($data[$f]) )
				$update[] = "$f = '".$this->db->escape($data[$f])."'";
		}
		if ( !empty($update) ) {
			$this->db->query("UPDATE `" . DB_PREFIX . "countries` SET ". implode(',', $update) ." WHERE country_id = '" . (int)$country_id . "'");
			$this->cache->delete('country');
		}
	}
	
	public function deleteCountry($country_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "countries WHERE country_id = '" . (int)$country_id . "'");
		
		$this->cache->delete('country');
	}
	
	public function getCountry($country_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "countries WHERE country_id = '" . (int)$country_id . "'");
		
		return $query->row;
	}
		
	public function getCountries($data = array(), $mode = 'default') {
		if ($data) {
			if ($mode == 'total_only') {
				$sql = "SELECT count(*) as total FROM " . DB_PREFIX . "countries";
			}
			else {
				$sql = "SELECT * FROM " . DB_PREFIX . "countries";
			}

			if ( !empty($data['subsql_filter']) ) {
				$sql .= " WHERE ".$data['subsql_filter'];
			}
			
			//If for total, we done bulding the query
			if ($mode == 'total_only') {
			    $query = $this->db->query($sql);
		    	return $query->row['total'];
			}

			$sort_data = array(
				'name',
				'status',
				'iso_code_2',
				'iso_code_3'
			);	
			
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];	
			} else {
				$sql .= " ORDER BY name";	
			}
			
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
			$country_data = $this->cache->get('country');
		
			if (!$country_data) {
				$query = $this->db->query( "SELECT *
											FROM " . DB_PREFIX . "countries
											ORDER BY name ASC");
	
				$country_data = $query->rows;
			
				$this->cache->set('country', $country_data);
			}

			return $country_data;			
		}	
	}
	
	public function getTotalCountries( $data = array()) {
		return $this->getCountries($data, 'total_only');
	}
}
?>