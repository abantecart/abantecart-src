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
class ModelLocalisationZone extends Model {
	public function addZone($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "zones SET status = '" . (int)$data['status'] . "', name = '" . $this->db->escape($data['name']) . "', code = '" . $this->db->escape($data['code']) . "', country_id = '" . (int)$data['country_id'] . "'");
			
		$this->cache->delete('zone');
		return $this->db->getLastId();
	}
	
	public function editZone($zone_id, $data) {
		$fields = array('status', 'name', 'code', 'country_id', );
		$update = array();
		foreach ( $fields as $f ) {
			if ( isset($data[$f]) )
				$update[] = "$f = '".$this->db->escape($data[$f])."'";
		}
		if ( !empty($update) ) {
			$this->db->query("UPDATE `" . DB_PREFIX . "zones` SET ". implode(',', $update) ." WHERE zone_id = '" . (int)$zone_id . "'");
			$this->cache->delete('zone');
		}
	}
	
	public function deleteZone($zone_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "zones WHERE zone_id = '" . (int)$zone_id . "'");

		$this->cache->delete('zone');	
	}
	
	public function getZone($zone_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "zones WHERE zone_id = '" . (int)$zone_id . "'");
		
		return $query->row;
	}
	
	public function getZones($data = array()) {
		$sql = "SELECT z.*, c.name AS country FROM " . DB_PREFIX . "zones z LEFT JOIN " . DB_PREFIX . "countries c ON (z.country_id = c.country_id)";

		if ( !empty($data['search']) )
			$sql .= " WHERE ".$data['search'];

		$sort_data = array(
			'c.name',
			'z.name',
			'z.code',
			'z.status',
		);
		if ( isset($data['sort']) ) {
			if ( $data['sort'] == 'country_id'  )
				$data['sort'] = 'c.name';
			else
				$data['sort'] = 'z.'.$data['sort'];
		}

			
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];	
		} else {
			$sql .= " ORDER BY c.name";	
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
	
	public function getZonesByCountryId($country_id) {
		$zone_data = $this->cache->get('zone.' . $country_id);
	
		if (!$zone_data) {
			$query = $this->db->query( "SELECT *
										FROM " . DB_PREFIX . "zones
										WHERE country_id = '" . (int)$country_id . "'
										ORDER BY name");
			$zone_data = $query->rows;
			$this->cache->set('zone.' . $country_id, $zone_data);
		}
	
		return $zone_data;
	}

	public function getZonesByLocationId($location_id) {
		$zone_data = $this->cache->get('zone.location.' . $location_id);

		if (!$zone_data) {
			$query = $this->db->query( "SELECT z.*
										FROM " . DB_PREFIX . "zones z
										INNER JOIN " . DB_PREFIX . "zones_to_locations zl
											ON ( zl.zone_id = z.zone_id AND zl.location_id = '".(int)$location_id."' )
										ORDER BY z.name");
			$zone_data = $query->rows;
			$this->cache->set('zone.location.' . $location_id, $zone_data);
		}

		return $zone_data;
	}
	
	public function getTotalZones($data = array()) {
      	$sql = "SELECT COUNT(*) AS total
      	        FROM " . DB_PREFIX . "zones z";
		if ( !empty($data['search']) )
			$sql .= " WHERE ".$data['search'];
      	$query = $this->db->query($sql);

		return $query->row['total'];
	}
				
	public function getTotalZonesByCountryId($country_id) {
		$query = $this->db->query( "SELECT count(*) AS total
									FROM " . DB_PREFIX . "zones
									WHERE country_id = '" . (int)$country_id . "'");
	
		return $query->row['total'];
	}

	public function getCountryIdByName($name) {
		$query = $this->db->query("SELECT country_id FROM " . DB_PREFIX . "countries WHERE name = '" . $this->db->escape($name) . "' AND status = '1' LIMIT 1");

		if ( $query->num_rows > 0 ) {
			return $query->row['country_id'];
		}
		return 0;
	}
}
?>