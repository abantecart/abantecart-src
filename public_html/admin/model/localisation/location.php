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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

/**
 * Class ModelLocalisationLocation
 */
class ModelLocalisationLocation extends Model {
	/**
	 * @param array $data
	 * @return int
	 */
    public function addLocation($data) {
        $this->db->query(
            "INSERT INTO " . $this->db->table("locations") . " 
			SET name = '" . $this->db->escape($data['name']) . "',
				description = '" . $this->db->escape($data['description']) . "',
				date_added = NOW()");
        $this->cache->delete('location');

        return $this->db->getLastId();
    }

	/**
	 * @param int $location_id
	 * @param array $data
	 * @return int
	 */
    public function addLocationZone($location_id, $data) {
        $zones = !is_array($data['zone_id']) ? array((int)$data['zone_id']) : $data['zone_id'];
        if (!$zones || !$location_id) {
            return null;
        }
        $sql = "INSERT INTO " . $this->db->table("zones_to_locations") . " (`country_id`, `zone_id`, `location_id`, `date_added`) VALUES ";
        foreach ($zones as $zone_id) {
            $temp[] = "('" . (int)$data['country_id'] . "',
					'" . (int)$zone_id . "',
					'" . (int)$location_id . "',
					NOW())";
        }
        $sql .= implode(", \n", $temp) . ';';
        $this->db->query($sql);

        $this->cache->delete('location');
        $this->cache->delete('zone.location.' . (int)$location_id);

        return $this->db->getLastId();
    }

	/**
	 * @param int $location_id
	 * @param array $data
	 */
    public function editLocation($location_id, $data) {
        $fields = array('name', 'description',);
        $update = array('date_modified = NOW()');
        foreach ($fields as $f) {
            if (isset($data[$f]))
                $update[] = $f." = '" . $this->db->escape($data[$f]) . "'";
        }
        if (!empty($update)) {
            $this->db->query("UPDATE " . $this->db->table("locations") . " SET " . implode(',', $update) . " WHERE location_id = '" . (int)$location_id . "'");
            $this->cache->delete('location');
            $this->cache->delete('zone.location.' . (int)$location_id);
        }
    }

	/**
	 * @param int $zone_to_location_id
	 * @param array $data
	 */
    public function editLocationZone($zone_to_location_id, $data) {
        $fields = array('country_id', 'zone_id',);
        $update = array('date_modified = NOW()');
        foreach ($fields as $f) {
            if (isset($data[$f]))
                $update[] = $f." = '" . $this->db->escape($data[$f]) . "'";
        }
        if (!empty($update)) {
            $this->db->query("UPDATE " . $this->db->table("zones_to_locations") . " SET " . implode(',', $update) . " WHERE zone_to_location_id = '" . (int)$zone_to_location_id . "'");
            $this->cache->delete('location');
        }
    }

	/**
	 * @param int $location_id
	 */
    public function deleteLocation($location_id) {
        $this->db->query("DELETE FROM " . $this->db->table("locations") . " WHERE location_id = '" . (int)$location_id . "'");
        $this->db->query("DELETE FROM " . $this->db->table("zones_to_locations") . " WHERE location_id = '" . (int)$location_id . "'");
        $this->cache->delete('location');
    }

	/**
	 * @param int $zone_to_location_id
	 */
    public function deleteLocationZone($zone_to_location_id) {
        $this->db->query("DELETE FROM " . $this->db->table("zones_to_locations") . " WHERE zone_to_location_id = '" . (int)$zone_to_location_id . "'");
        $this->cache->delete('location');
    }

	/**
	 * @param int $location_id
	 * @return array
	 */
    public function getLocation($location_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . $this->db->table("locations") . " WHERE location_id = '" . (int)$location_id . "'");

        return $query->row;
    }

	/**
	 * @param int $zone_to_location_id
	 * @return array mixed
	 */
    public function getLocationZone($zone_to_location_id) {
        $query = $this->db->query("SELECT * FROM " . $this->db->table("zones_to_locations") . " WHERE zone_to_location_id = '" . (int)$zone_to_location_id . "'");

        return $query->row;
    }

	/**
	 * @param array $data
	 * @return array
	 */
    public function getLocations($data = array()) {
        if ($data) {
            $sql = "SELECT * FROM " . $this->db->table("locations") . " ";

            if (!empty($data['subsql_filter'])) {
                $sql .= " WHERE " . $data['subsql_filter'];
            }

            $sort_data = array('name', 'description');

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
                $query = $this->db->query("SELECT * FROM " . $this->db->table("locations") . " ORDER BY name ASC");

                $location_data = $query->rows;

                $this->cache->set('location', $location_data);
            }

            return $location_data;
        }
    }

	/**
	 * @param array $data
	 * @return int
	 */
    public function getTotalLocations($data = array()) {
        $sql = "SELECT count(*) as total FROM " . $this->db->table("locations") . " ";
        if (!empty($data['subsql_filter'])) {
            $sql .= " WHERE " . $data['subsql_filter'];
        }
        $query = $this->db->query($sql);
        return $query->row['total'];
    }

	/**
	 * @param array $data
	 * @return array
	 */
    public function getZoneToLocations($data) {
		$language_id = $this->session->data['content_language_id'];
		$default_language_id = $this->language->getDefaultLanguageID();
        
        $sql = "SELECT zl.*, COALESCE( cd1.name,cd2.name) as country_name, COALESCE( zd1.name, zd2.name) as name
				FROM " . $this->db->table("zones_to_locations") . " zl
				LEFT JOIN " . $this->db->table("countries") . " c ON c.country_id = zl.country_id
				LEFT JOIN " . $this->db->table("country_descriptions") . " cd1 ON (c.country_id = cd1.country_id AND cd1.language_id = '" . (int)$language_id . "')
				LEFT JOIN " . $this->db->table("country_descriptions") . " cd2 ON (c.country_id = cd2.country_id AND cd2.language_id = '" . (int)$default_language_id . "')
				LEFT JOIN " . $this->db->table("zones") . " z ON z.zone_id = zl.zone_id
				LEFT JOIN " . $this->db->table("zone_descriptions") . " zd1 ON (z.zone_id = zd1.zone_id AND zd1.language_id = '" . (int)$language_id . "')
				LEFT JOIN " . $this->db->table("zone_descriptions") . " zd2 ON (z.zone_id = zd2.zone_id AND zd2.language_id = '" . (int)$default_language_id . "') 
				WHERE zl.location_id = '" . (int)$data['location_id'] . "'";

        if (isset($data['sort'])) {
            $sql .= " ORDER BY " . $this->db->escape($data['sort'] . ' ' . $data['order']);
        }
        if (isset($data['start']) && isset($data['limit'])) {
            $sql .= " LIMIT " . (int)$data['start'] . ', ' . (int)$data['limit'];
        }
        $query = $this->db->query($sql);
        return $query->rows;
    }

	/**
	 * @param int $location_id
	 * @return array
	 */
    public function getTotalZoneToLocationsByLocationID($location_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total
                                   FROM " . $this->db->table("zones_to_locations") . " 
                                   WHERE location_id = '" . (int)$location_id . "'");
        return $query->row['total'];
    }

	/**
	 * @param int $country_id
	 * @return int
	 */
    public function getTotalZoneToLocationByCountryID($country_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . $this->db->table("zones_to_locations") . " WHERE country_id = '" . (int)$country_id . "'");
        return $query->row['total'];
    }

	/**
	 * @param int $zone_id
	 * @return int
	 */
    public function getTotalZoneToLocationByZoneId($zone_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . $this->db->table("zones_to_locations") . " WHERE zone_id = '" . (int)$zone_id . "'");
        return $query->row['total'];
    }
}
