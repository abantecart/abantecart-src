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
class ModelLocalisationZone extends Model {
	public function addZone($data) {
		$this->db->query("INSERT INTO " . $this->db->table("zones") . "
						  SET status = '" . (int)$data['status'] . "',
						        code = '" . $this->db->escape($data['code']) . "',
						        country_id = '" . (int)$data['country_id'] . "'");
			
		$zone_id = $this->db->getLastId();

		foreach ($data['zone_name'] as $language_id => $value) {
			$this->language->replaceDescriptions('zone_descriptions',
											 array('zone_id' => (int)$zone_id),
											 array($language_id => array(
												 'name' => $value['name'],
											 )) );
		}
	
		$this->cache->delete('zone');
		return $zone_id;
	}
	
	public function editZone($zone_id, $data) {
		$fields = array('status', 'code', 'country_id', );
		$update = array();
		foreach ( $fields as $f ) {
			if ( isset($data[$f]) )
				$update[] = $f." = '".$this->db->escape($data[$f])."'";
		}
		if ( !empty($update) ) {
			$this->db->query("UPDATE " . $this->db->table("zones") . " SET ". implode(',', $update) ." WHERE zone_id = '" . (int)$zone_id . "'");
			$this->cache->delete('zone');
		}
		
		if ( count($data['zone_name']) ) {
			foreach ($data['zone_name'] as $language_id => $value) {
				$this->language->replaceDescriptions('zone_descriptions',
												 array('zone_id' => (int)$zone_id),
												 array($language_id => array(
													 'name' => $value['name'],
												 )) );
			}
		}
		$this->cache->delete('zone');
	}
	
	public function deleteZone($zone_id) {
		$this->db->query("DELETE FROM " . $this->db->table("zones") . " WHERE zone_id = '" . (int)$zone_id . "'");
		$this->db->query("DELETE FROM " . $this->db->table("zone_descriptions") . " WHERE zone_id = '" . (int)$zone_id . "'");

		$this->cache->delete('zone');	
	}
	
	public function getZone($zone_id) {
		$language_id = $this->session->data['content_language_id'];
		$default_lang_id = $this->language->getDefaultLanguageID();	

		$query = $this->db->query("SELECT z.zone_id, 
										  z.country_id, 
										  z.code, z.status, 
										  z.sort_order, 
										  zd.name, 
										  zd.language_id
										FROM " . $this->db->table("zones") . " z
										LEFT JOIN " . $this->db->table("zone_descriptions") . " zd 
										ON (z.zone_id = zd.zone_id AND zd.language_id = '" . (int)$language_id . "')
										WHERE z.zone_id = '" . (int)$zone_id . "'");
		$ret_data = $query->row;
		$ret_data['zone_name'] = $this->getZoneDescriptions($zone_id); 
		return $ret_data;
	}

	public function getZoneDescriptions($zone_id) {
		$zone_data = array();
		
		$query = $this->db->query( "SELECT *
									FROM " . $this->db->table("zone_descriptions") . " 
									WHERE zone_id = '" . (int)$zone_id . "'");
		
		foreach ($query->rows as $result) {
			$zone_data[$result['language_id']] = array('name' => $result['name']);
		}
		
		return $zone_data;
	}

	
	public function getZones($data = array(), $mode = 'default') {
		$language_id = $this->session->data['content_language_id'];
		$default_language_id = $this->language->getDefaultLanguageID();
		
		if ($mode == 'total_only') {
		    $sql = "SELECT count(*) as total FROM " . $this->db->table("zones") . " z ";
		}
		else {
		    $sql = "SELECT	z.zone_id, 
						  	z.country_id, 
							z.code, z.status, 
							z.sort_order, 
							zd.name, 
							zd.language_id, 
		    				COALESCE( cd1.name,cd2.name) as country 
		    		FROM " . $this->db->table("zones") . " z ";
		}
		$sql .= "LEFT JOIN " . $this->db->table("zone_descriptions") . " zd ON (z.zone_id = zd.zone_id AND zd.language_id = '" . (int)$language_id . "') ";
		$sql .= "LEFT JOIN " . $this->db->table("countries") . " c ON (z.country_id = c.country_id)";
		$sql .= "LEFT JOIN " . $this->db->table("country_descriptions") . " cd1 ON (c.country_id = cd1.country_id AND cd1.language_id = '" . (int)$language_id . "') ";
		$sql .= "LEFT JOIN " . $this->db->table("country_descriptions") . " cd2 ON (c.country_id = cd2.country_id AND cd2.language_id = '" . (int)$default_language_id . "') ";

		if ( !empty($data['search']) )
			$sql .= " WHERE ".$data['search'];

		//If for total, we done bulding the query
		if ($mode == 'total_only') {
		    $query = $this->db->query($sql);
		    return $query->row['total'];
		}

		if ( isset($data['sort']) ) {
			if ( $data['sort'] == 'country_id' ) {
				$data['sort'] = 'cd1.name';
			} else if ( $data['sort'] == 'name' ) {
				$data['sort'] = 'zd.name';			
			} else {
				$data['sort'] = 'z.'.$data['sort'];
			}
		}

		if ( isset($data['sort']) ) {
			$sql .= " ORDER BY " . $data['sort'];	
		} else {
			$sql .= " ORDER BY cd1.name";	
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
	}

	public function getTotalZones($data = array()) {
		return $this->getZones($data, 'total_only');
	}
					
	public function getZonesByCountryId($country_id) {
		$language_id = $this->session->data['content_language_id'];
		$default_language_id = $this->language->getDefaultLanguageID();
		
		$zone_data = $this->cache->get('zone.' . $country_id, $language_id);
	
		if (!$zone_data) {
			$query = $this->db->query( "SELECT *, COALESCE( zd1.name, zd2.name) as name 
										FROM " . $this->db->table("zones") . " z
										LEFT JOIN " . $this->db->table("zone_descriptions") . " zd1 ON (z.zone_id = zd1.zone_id AND zd1.language_id = '" . (int)$language_id . "') 
										LEFT JOIN " . $this->db->table("zone_descriptions") . " zd2 ON (z.zone_id = zd2.zone_id AND zd2.language_id = '" . (int)$default_language_id . "') 
										WHERE z.country_id = '" . (int)$country_id . "'
										ORDER BY zd1.name, zd2.name");
			$zone_data = $query->rows;
			$this->cache->set('zone.' . $country_id, $zone_data, $language_id);
		}
	
		return $zone_data;
	}

	public function getZonesByLocationId($location_id) {
		$language_id = $this->session->data['content_language_id'];
		$default_language_id = $this->language->getDefaultLanguageID();

		$zone_data = $this->cache->get('zone.location.' . $location_id, $language_id);

		if (!$zone_data) {
			$query = $this->db->query( "SELECT z.*, COALESCE( zd1.name, zd2.name) as name
										FROM " . $this->db->table("zones") . " z
										LEFT JOIN " . $this->db->table("zone_descriptions") . " zd1 ON (z.zone_id = zd1.zone_id AND zd1.language_id = '" . (int)$language_id . "') 
										LEFT JOIN " . $this->db->table("zone_descriptions") . " zd2 ON (z.zone_id = zd2.zone_id AND zd2.language_id = '" . (int)$default_language_id . "') 
										INNER JOIN " . $this->db->table("zones_to_locations") . " zl
											ON ( zl.zone_id = z.zone_id AND zl.location_id = '".(int)$location_id."' )
										ORDER BY zd1.name, zd2.name");
			$zone_data = $query->rows;
			$this->cache->set('zone.location.' . $location_id, $zone_data, $language_id);
		}

		return $zone_data;
	}
	
	public function getTotalZonesByCountryId($country_id) {
		$query = $this->db->query( "SELECT count(*) AS total
									FROM " . $this->db->table("zones") . " 
									WHERE country_id = '" . (int)$country_id . "'");
	
		return $query->row['total'];
	}

	public function getCountryIdByName($name) {
		$language_id = $this->session->data['content_language_id'];
		$default_language_id = $this->language->getDefaultLanguageID();
		
		$query = $this->db->query("SELECT c.country_id FROM " . $this->db->table("countries") . " c
								   LEFT JOIN " . $this->db->table("country_descriptions") . " cd1 ON (c.country_id = cd1.country_id AND cd1.language_id = '" . (int)$language_id . "')
								   LEFT JOIN " . $this->db->table("country_descriptions") . " cd2 ON (c.country_id = cd2.country_id AND cd2.language_id = '" . (int)$default_language_id . "')
								   WHERE cd1.name = '" . $this->db->escape($name) . "' OR cd2.name = '" . $this->db->escape($name) . "' AND status = '1' LIMIT 1");

		if ( $query->num_rows > 0 ) {
			return $query->row['country_id'];
		}
		return 0;
	}
}