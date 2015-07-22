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
/** @noinspection PhpUndefinedClassInspection */
class ModelCatalogManufacturer extends Model {
	/**
	 * @param array $data
	 * @return int
	 */
	public function addManufacturer($data) {
      	$this->db->query("INSERT INTO " . $this->db->table("manufacturers") . " SET name = '" . $this->db->escape($data['name']) . "', sort_order = '" . (int)$data['sort_order'] . "'");
		
		$manufacturer_id = $this->db->getLastId();

		if (isset($data['manufacturer_store'])) {
			foreach ($data['manufacturer_store'] as $store_id) {
				$this->db->query("INSERT INTO " . $this->db->table("manufacturers_to_stores") . " SET manufacturer_id = '" . (int)$manufacturer_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
		
		if ($data['keyword']) {
			$seo_key = SEOEncode($data['keyword'],
								'manufacturer_id',
								$manufacturer_id);
		}else {
			//Default behavior to save SEO URL keword from manufacturer name 
			$seo_key = SEOEncode( $data['name'],
									'manufacturer_id',
									$manufacturer_id);
		}

		if($seo_key){
			$this->language->replaceDescriptions('url_aliases',
												array('query' => "manufacturer_id=" . (int)$manufacturer_id),
												array((int)$this->session->data['content_language_id'] => array('keyword'=>$seo_key)));
		}else{
			$this->db->query("DELETE
							FROM " . $this->db->table("url_aliases") . " 
							WHERE query = 'manufacturer_id=" . (int)$manufacturer_id . "'
								AND language_id = '".(int)$this->session->data['content_language_id']."'");
		}

		$this->cache->delete('manufacturer');

		return $manufacturer_id;
	}

	/**
	 * @param int $manufacturer_id
	 * @param array $data
	 */
	public function editManufacturer($manufacturer_id, $data) {

		$fields = array('name', 'sort_order');
		$update = array();
		foreach ( $fields as $f ) {
			if ( isset($data[$f]) )
				$update[] = $f." = '".$this->db->escape($data[$f])."'";
		}
		if ( !empty($update) ) $this->db->query("UPDATE " . $this->db->table("manufacturers") . " SET ". implode(',', $update) ." WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		if (isset($data['manufacturer_store'])) {
			$this->db->query("DELETE FROM " . $this->db->table("manufacturers_to_stores") . " WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
			foreach ($data['manufacturer_store'] as $store_id) {
				$this->db->query("INSERT INTO " . $this->db->table("manufacturers_to_stores") . " SET manufacturer_id = '" . (int)$manufacturer_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
		
		if (isset($data['keyword'])) {
			$data['keyword'] =  SEOEncode($data['keyword'],'manufacturer_id',$manufacturer_id);
			if($data['keyword']){
				$this->language->replaceDescriptions('url_aliases',
														array('query' => "manufacturer_id=" . (int)$manufacturer_id),
														array((int)$this->session->data['content_language_id'] => array('keyword' => $data['keyword'])));
			}else{
				$this->db->query("DELETE
								FROM " . $this->db->table("url_aliases") . " 
								WHERE query = 'manufacturer_id=" . (int)$manufacturer_id . "'
									AND language_id = '".(int)$this->session->data['content_language_id']."'");
			}
		}
		
		$this->cache->delete('manufacturer');
	}

	/**
	 * @param int $manufacturer_id
	 */
	public function deleteManufacturer($manufacturer_id) {
		$this->db->query("DELETE FROM " . $this->db->table("manufacturers") . " WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		$this->db->query("DELETE FROM " . $this->db->table("manufacturers_to_stores") . " WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		$this->db->query("DELETE FROM " . $this->db->table("url_aliases") . " WHERE query = 'manufacturer_id=" . (int)$manufacturer_id . "'");

		$lm = new ALayoutManager();
		$lm->deletePageLayout('pages/product/manufacturer','manufacturer_id',(int)$manufacturer_id);
		$this->cache->delete('manufacturer');
	}

	/**
	 * @param int $manufacturer_id
	 * @return array
	 */
	public function getManufacturer($manufacturer_id) {
		$query = $this->db->query("SELECT DISTINCT *, ( SELECT keyword
														FROM " . $this->db->table("url_aliases") . " 
														WHERE query = 'manufacturer_id=" . (int)$manufacturer_id . "'
														  AND language_id='".(int)$this->session->data['content_language_id']."') AS keyword
									FROM " . $this->db->table("manufacturers") . " 
									WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		
		return $query->row;
	}

	/**
	 * @param array $data
	 * @param string $mode
	 * @return array|int
	 */
	public function getManufacturers($data = array(), $mode = 'default') {
		if ($data) {

			if ( $data['store_id'] ) {
				$store_id = (int)$data['store_id'];
			} else {
				$store_id = (int)$this->config->get('config_store_id');
			}

			if ($mode == 'total_only') {
				$total_sql = 'count(*) as total';
			}
			else {
				$total_sql = 'ms.*, m.*';
			}
			$sql = "SELECT $total_sql FROM ".$this->db->table("manufacturers")." m
						INNER JOIN " . $this->db->table('manufacturers_to_stores')." ms				
						ON (m.manufacturer_id = ms.manufacturer_id AND ms.store_id = '" . $store_id . "')";

			if ( !empty($data['subsql_filter']) )
				$sql .= " WHERE ".$data['subsql_filter'];

			//If for total, we done bulding the query
			if ($mode == 'total_only') {
			    $query = $this->db->query($sql);
			    return $query->row['total'];
			}
					
			$sort_data = array(
			    'name' => 'm.name',
			    'sort_order' => 'm.sort_order'				
			);	

			if (isset($data['sort']) && in_array($data['sort'], array_keys($sort_data)) ) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY m.name ";
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
			// this slice of code is duplicate of storefron model for manufacturer
			$manufacturer_data = $this->cache->get( 'manufacturer', '', (int)$this->config->get('config_store_id') );
			if (is_null($manufacturer_data)) {
				$query = $this->db->query( "SELECT *
											FROM " . $this->db->table("manufacturers") . " m
											LEFT JOIN " . $this->db->table("manufacturers_to_stores") . " m2s ON (m.manufacturer_id = m2s.manufacturer_id)
											WHERE m2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
											ORDER BY sort_order, LCASE(m.name) ASC");
	
				$manufacturer_data = $query->rows;
				$this->cache->set('manufacturer', $manufacturer_data, '', (int)$this->config->get('config_store_id'));
			}
		 
			return $manufacturer_data;
		}
	}

	/**
	 * @param int $manufacturer_id
	 * @return array
	 */
	public function getManufacturerStores($manufacturer_id) {
		$manufacturer_store_data = array();
		$rows = $this->getManufacturerStoresInfo($manufacturer_id);
		foreach ($rows as $result) {
			$manufacturer_store_data[] = $result['store_id'];
		}
		return $manufacturer_store_data;
	}

	/**
	 * @param int $manufacturer_id
	 * @return array
	 */
	public function getManufacturerStoresInfo($manufacturer_id) {

		$query = $this->db->query("SELECT m2s.*,
										s.name as store_name,
										ss.`value` as store_url,
										sss.`value` as store_ssl_url
									FROM " . $this->db->table("manufacturers_to_stores") . " m2s
									LEFT JOIN " . $this->db->table("stores") . " s ON s.store_id = m2s.store_id
									LEFT JOIN " . $this->db->table("settings") . " ss
										ON (ss.store_id = m2s.store_id AND ss.`key`='config_url')
									LEFT JOIN " . $this->db->table("settings") . " sss
										ON (sss.store_id = m2s.store_id AND sss.`key`='config_ssl_url')
									WHERE m2s.manufacturer_id = '" . (int)$manufacturer_id . "'");
		return $query->rows;
	}

	/**
	 * @param array $data
	 * @return mixed|null
	 */
	public function getTotalManufacturers($data = array()) {
		return $this->getManufacturers($data, 'total_only');
	}	
}
