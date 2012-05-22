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
class ModelLocalisationLocation extends Model {
	public function addLocation($data) {
		$this->db->query(
			"INSERT INTO " . DB_PREFIX . "locations
			SET name = '" . $this->db->escape($data['name']) . "',
				description = '" . $this->db->escape($data['description']) . "',
				date_added = NOW()");
		$this->cache->delete('location');

		return $this->db->getLastId();
	}

	public function addLocationZone($location_id, $data) {
		$this->db->query(
			"INSERT INTO " . DB_PREFIX . "zones_to_locations
			SET country_id = '"  . (int)$data['country_id'] . "',
				zone_id = '"  . (int)$data['zone_id'] . "',
				location_id = '"  .(int)$location_id . "',
				date_added = NOW()");
		$this->cache->delete('location');
		$this->cache->delete('zone.location.' . (int)$location_id);

		return $this->db->getLastId();
	}
	
	public function editLocation($location_id, $data) {
		$fields = array('name', 'description', );
		$update = array('date_modified = NOW()');
		foreach ( $fields as $f ) {
			if ( isset($data[$f]) )
				$update[] = "$f = '".$this->db->escape($data[$f])."'";
		}
		if ( !empty($update) ) {
			$this->db->query("UPDATE `" . DB_PREFIX . "locations` SET ". implode(',', $update) ." WHERE location_id = '" . (int)$location_id . "'");
			$this->cache->delete('location');
			$this->cache->delete('zone.location.' . (int)$location_id);
		}
	}

	public function editLocationZone($zone_to_location_id, $data) {
		$fields = array('country_id', 'zone_id', );
		$update = array('date_modified = NOW()');
		foreach ( $fields as $f ) {
			if ( isset($data[$f]) )
				$update[] = "$f = '".$this->db->escape($data[$f])."'";
		}
		if ( !empty($update) ) {
			$this->db->query("UPDATE `" . DB_PREFIX . "zones_to_locations` SET ". implode(',', $update) ." WHERE zone_to_location_id = '" . (int)$zone_to_location_id . "'");
			$this->cache->delete('location');
		}
	}

	
	public function deleteLocation($location_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "locations WHERE location_id = '" . (int)$location_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "zones_to_locations WHERE location_id = '" . (int)$location_id . "'");
		$this->cache->delete('location');
	}
	public function deleteLocationZone($zone_to_location_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "zones_to_locations WHERE zone_to_location_id = '" . (int)$zone_to_location_id . "'");
		$this->cache->delete('location');
	}
	
	public function getLocation($location_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "locations WHERE location_id = '" . (int)$location_id . "'");
		
		return $query->row;
	}

	public function getLocationZone($zone_to_location_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zones_to_locations WHERE zone_to_location_id = '" . (int)$zone_to_location_id . "'");

		return $query->row;
	}

	public function getLocations($data = array(), $mode = 'default') {
		if ($data || $mode == 'total_only') {
			if ($mode == 'total_only') {
				$sql = "SELECT count(*) as total FROM " . DB_PREFIX . "locations";
			} else {
				$sql = "SELECT * FROM " . DB_PREFIX . "locations";			
			}
			if ( !empty($data['subsql_filter']) )
				$sql .= " WHERE ".$data['subsql_filter'];

			//If for total, we done bulding the query
			if ($mode == 'total_only') {
				$query = $this->db->query($sql);
				return $query->row['total'];
			}
			
			$sort_data = array(
				'name',
				'description'
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
			$location_data = $this->cache->get('location');

			if (!$location_data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "locations ORDER BY name ASC");
	
				$location_data = $query->rows;
			
				$this->cache->set('location', $location_data);
			}
			
			return $location_data;				
		}
	}
	
	public function getTotalLocations( $data = array()) {
		return $this->getLocations($data, 'total_only');
	}	
	
	public function getZoneToLocations($location_id) {	
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zones_to_locations WHERE location_id = '" . (int)$location_id . "'");
		
		return $query->rows;	
	}		

	public function getTotalZoneToLocationsByLocationID($location_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "zones_to_locations WHERE location_id = '" . (int)$location_id . "'");

		return $query->row['total'];
	}
	
	public function getTotalZoneToLocationByCountryID($country_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "zones_to_locations WHERE country_id = '" . (int)$country_id . "'");
		
		return $query->row['total'];
	}	
	
	public function getTotalZoneToLocationByZoneId($zone_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "zones_to_locations WHERE zone_id = '" . (int)$zone_id . "'");
		
		return $query->row['total'];
	}		
}
?>