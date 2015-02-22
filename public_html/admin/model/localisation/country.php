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
 * Class ModelLocalisationCountry
 */
class ModelLocalisationCountry extends Model {
	/**
	 * @param array $data
	 * @return int
	 */
	public function addCountry($data) {
		$this->db->query("INSERT INTO " . $this->db->table("countries") . " SET status = '" . (int)$data['status'] . "', iso_code_2 = '" . $this->db->escape($data['iso_code_2']) . "', iso_code_3 = '" . $this->db->escape($data['iso_code_3']) . "', address_format = '" . $this->db->escape($data['address_format']) . "'");

		$country_id = $this->db->getLastId();

		foreach ($data['country_name'] as $language_id => $value) {
			$this->language->replaceDescriptions('country_descriptions',
											 array('country_id' => (int)$country_id),
											 array($language_id => array(
												 'name' => $value['name'],
											 )) );
		}
	
		$this->cache->delete('country');
		return $country_id;
	}

	/**
	 * @param int $country_id
	 * @param $data
	 */
	public function editCountry($country_id, $data) {

		$fields = array('status', 'iso_code_2', 'iso_code_3', 'address_format', );
		$update = array();
		foreach ( $fields as $f ) {
			if ( isset($data[$f]) )
				$update[] = $f." = '".$this->db->escape($data[$f])."'";
		}
		if ( !empty($update) ) {
			$this->db->query("UPDATE " . $this->db->table("countries") . " SET ". implode(',', $update) ." WHERE country_id = '" . (int)$country_id . "'");
			$this->cache->delete('country');
		}

		if ( count($data['country_name']) ) {
			foreach ($data['country_name'] as $language_id => $value) {
				$this->language->replaceDescriptions('country_descriptions',
												 array('country_id' => (int)$country_id),
												 array($language_id => array(
													 'name' => $value['name'],
												 )) );
			}
		}
	}

	/**
	 * @param int $country_id
	 */
	public function deleteCountry($country_id) {
		$this->db->query("DELETE FROM " . $this->db->table("countries") . " WHERE country_id = '" . (int)$country_id . "'");
		$this->db->query("DELETE FROM " . $this->db->table("country_descriptions") . " WHERE country_id = '" . (int)$country_id . "'");		
		$this->cache->delete('country');
	}

	/**
	 * @param int $country_id
	 * @return array
	 */
	public function getCountry($country_id) {
		$language_id = $this->language->getContentLanguageID();

		$query = $this->db->query("SELECT DISTINCT *
										FROM " . $this->db->table("countries") . " c
										LEFT JOIN " . $this->db->table("country_descriptions") . " cd
										ON (c.country_id = cd.country_id AND cd.language_id = '" . (int)$language_id . "')
										WHERE c.country_id = '" . (int)$country_id . "'");
		$ret_data = $query->row;
		$ret_data['country_name'] = $this->getCountryDescriptions($country_id); 
		return $ret_data;
	}

	/**
	 * @param int $country_id
	 * @return array
	 */
	public function getCountryDescriptions($country_id) {
		$country_data = array();
		
		$query = $this->db->query( "SELECT *
									FROM " . $this->db->table("country_descriptions") . " 
									WHERE country_id = '" . (int)$country_id . "'");
		
		foreach ($query->rows as $result) {
			$country_data[$result['language_id']] = array('name' => $result['name']);
		}
		
		return $country_data;
	}

	/**
	 * @param array $data
	 * @param string $mode
	 * @return array|int
	 */
	public function getCountries($data = array(), $mode = 'default') {
		$language_id = $this->language->getContentLanguageID();
		$default_language_id = $this->language->getDefaultLanguageID();
		
		if ($data) {
			if ($mode == 'total_only') {
				$sql = "SELECT count(*) as total FROM " . $this->db->table("countries") . " c ";
			}
			else {
				$sql = "SELECT c.country_id, 
							   c.iso_code_2,
							   c.iso_code_3, 
							   c.address_format, 
							   c.status, 
							   c.sort_order, 
							   cd.name  
						FROM " . $this->db->table("countries") . " c ";
			}
			$sql .= "LEFT JOIN " . $this->db->table("country_descriptions") . " cd ON (c.country_id = cd.country_id AND cd.language_id = '" . (int)$language_id . "') ";
			
			if ( !empty($data['subsql_filter']) ) {
				$sql .= " WHERE ".$data['subsql_filter'];
			}
			
			//If for total, we done bulding the query
			if ($mode == 'total_only') {
			    $query = $this->db->query($sql);
		    	return $query->row['total'];
			}

			$sort_data = array(
				'name' => 'cd.name',
				'status' => 'c.status',
				'iso_code_2' => 'c.iso_code_2',
				'iso_code_3' => 'c.iso_code_3'
			);	
			
			if (isset($data['sort']) && in_array($data['sort'], array_keys($sort_data))) {
				$sql .= " ORDER BY " . $sort_data[$data['sort']];	
			} else {
				$sql .= " ORDER BY cd.name";	
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
			$country_data = $this->cache->get('country', $language_id);
		
			if (!$country_data) {
				if ($language_id == $default_language_id) {
					$query = $this->db->query( "SELECT *
											FROM " . $this->db->table("countries") . " c
											LEFT JOIN " . $this->db->table("country_descriptions") . " cd 
												ON (c.country_id = cd.country_id AND cd.language_id = '" . (int)$language_id . "') 
											ORDER BY cd.name ASC");
							
				} else {
					//merge text for missing country translations. 
					$query = $this->db->query("SELECT *, COALESCE( cd1.name,cd2.name) as name
							FROM " . $this->db->table("countries") . " c
							LEFT JOIN " . $this->db->table("country_descriptions") . " cd1
							ON (c.country_id = cd1.country_id AND cd1.language_id = '" . (int)$language_id . "')
							LEFT JOIN " . $this->db->table("country_descriptions") . " cd2
							ON (c.country_id = cd2.country_id AND cd2.language_id = '" . (int)$default_language_id . "')
							ORDER BY cd1.name,cd2.name ASC");	
				}								
	
				$country_data = $query->rows;
			
				$this->cache->set('country', $country_data, $language_id);
			}

			return $country_data;			
		}	
	}

	/**
	 * @param array $data
	 * @return int
	 */
	public function getTotalCountries( $data = array()) {
		return $this->getCountries($data, 'total_only');
	}
}
