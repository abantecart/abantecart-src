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
class ModelCatalogManufacturer extends Model {
	public function addManufacturer($data) {
      	$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturers SET name = '" . $this->db->escape($data['name']) . "', sort_order = '" . (int)$data['sort_order'] . "'");
		
		$manufacturer_id = $this->db->getLastId();

		if (isset($data['manufacturer_store'])) {
			foreach ($data['manufacturer_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturers_to_stores SET manufacturer_id = '" . (int)$manufacturer_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
		
		if ($data['keyword']) {
			$seo_key = $data['keyword'];
		}else {
			//Default behavior to save SEO URL keword from manufacturer name 
			$seo_key = SEOEncode( $data['name'] );
			 
			//Check if key is unique  
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_aliases
									   WHERE keyword = '" . $this->db->escape($seo_key) . "'");
			if ($query->num_rows) {
				$seo_key .= '_' . $manufacturer_id;
			}						
		}
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "url_aliases SET query = 'manufacturer_id=" . (int)$manufacturer_id . "', keyword = '" . $this->db->escape( $seo_key ) . "'");
		
		$this->cache->delete('manufacturer');

		return $manufacturer_id;
	}
	
	public function editManufacturer($manufacturer_id, $data) {

		$fields = array('name', 'sort_order');
		$update = array();
		foreach ( $fields as $f ) {
			if ( isset($data[$f]) )
				$update[] = "$f = '".$this->db->escape($data[$f])."'";
		}
		if ( !empty($update) ) $this->db->query("UPDATE " . DB_PREFIX . "manufacturers SET ". implode(',', $update) ." WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		if (isset($data['manufacturer_store'])) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "manufacturers_to_stores WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
			foreach ($data['manufacturer_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturers_to_stores SET manufacturer_id = '" . (int)$manufacturer_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
		
		if (isset($data['keyword'])) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "url_aliases WHERE query = 'manufacturer_id=" . (int)$manufacturer_id. "'");
			if($data['keyword']){
				$this->db->query("INSERT INTO " . DB_PREFIX . "url_aliases SET query = 'manufacturer_id=" . (int)$manufacturer_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
			}
		}
		
		$this->cache->delete('manufacturer');
	}
	
	public function deleteManufacturer($manufacturer_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "manufacturers WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "manufacturers_to_stores WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_aliases WHERE query = 'manufacturer_id=" . (int)$manufacturer_id . "'");

		$lm = new ALayoutManager();
		$lm->deletePageLayout('pages/product/manufacturer','manufacturer_id',(int)$manufacturer_id);
		$this->cache->delete('manufacturer');
	}	
	
	public function getManufacturer($manufacturer_id) {
		$query = $this->db->query("SELECT DISTINCT *, ( SELECT keyword
														FROM " . DB_PREFIX . "url_aliases
														WHERE query = 'manufacturer_id=" . (int)$manufacturer_id . "') AS keyword
									FROM " . DB_PREFIX . "manufacturers
									WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		
		return $query->row;
	}
	
	public function getManufacturers($data = array(), $mode = 'default') {
		if ($data) {
			if ($mode == 'total_only') {
				$total_sql = 'count(*) as total';
			}
			else {
				$total_sql = '*';
			}
			$sql = "SELECT $total_sql FROM " . DB_PREFIX . "manufacturers";

			if ( !empty($data['subsql_filter']) )
				$sql .= " WHERE ".$data['subsql_filter'];

			//If for total, we done bulding the query
			if ($mode == 'total_only') {
			    $query = $this->db->query($sql);
			    return $query->row['total'];
			}
					
			$sort_data = array(
				'name',
				'sort_order'
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
			$manufacturer_data = $this->cache->get('manufacturer');
		
			if (!$manufacturer_data) {
				$query = $this->db->query("SELECT *
										   FROM " . DB_PREFIX . "manufacturers
										   ORDER BY name");
	
				$manufacturer_data = $query->rows;
			
				$this->cache->set('manufacturer', $manufacturer_data);
			}
		 
			return $manufacturer_data;
		}
	}

	public function getManufacturerStores($manufacturer_id) {
		$manufacturer_store_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturers_to_stores WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		foreach ($query->rows as $result) {
			$manufacturer_store_data[] = $result['store_id'];
		}
		
		return $manufacturer_store_data;
	}

	public function getTotalManufacturers($data = array()) {
		return $this->getManufacturers($data, 'total_only');
	}	
}
?>