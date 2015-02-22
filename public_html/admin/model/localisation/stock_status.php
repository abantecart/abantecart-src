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
class ModelLocalisationStockStatus extends Model {
	public function addStockStatus($data) {

		$result = $this->db->query("SELECT MAX(stock_status_id) as max_id FROM " . $this->db->table("stock_statuses") . " ");
		$stock_status_id = (int)$result->row['max_id']+1;

		foreach ($data['stock_status'] as $language_id => $value) {

			$this->language->replaceDescriptions('stock_statuses',
											 array( 'stock_status_id' => (int)$stock_status_id,
													'language_id' => (int)$language_id ),
											 array($language_id => array(
																	'name' => $value['name']
											 )) );
		}
		
		$this->cache->delete('stock_status');

		return $stock_status_id;
	}

	public function editStockStatus($stock_status_id, $data) {
		foreach ($data['stock_status'] as $language_id => $value) {

			$this->language->updateDescriptions('stock_statuses',
												 array( 'stock_status_id' => (int)$stock_status_id,
														'language_id' => (int)$language_id ),
												 array($language_id => array(
																			'name' => $value['name']
												 )) );

		}
		$this->cache->delete('stock_status');
	}
	
	public function deleteStockStatus($stock_status_id) {
		$this->db->query("DELETE FROM " . $this->db->table("stock_statuses") . " 
						WHERE stock_status_id = '" . (int)$stock_status_id . "'");
		$this->cache->delete('stock_status');
	}
		
	public function getStockStatus($stock_status_id) {
		$query = $this->db->query("SELECT *
									FROM " . $this->db->table("stock_statuses") . " 
									WHERE stock_status_id = '" . (int)$stock_status_id . "'
										AND language_id = '" . (int)$this->config->get('storefront_language_id') . "'");
		
		return $query->row;
	}
	
	public function getStockStatuses($data = array()) {

		if ( !empty($data['content_language_id']) ) {
			$language_id = ( int )$data['content_language_id'];
		} else {
			$language_id = (int)$this->session->data['content_language_id'];
		}

		if ($data) {
			$sql = "SELECT * FROM " . $this->db->table("stock_statuses") . " WHERE language_id = '" . $language_id . "'";
      		
			$sql .= " ORDER BY name";	
			
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
			$stock_status_data = $this->cache->get('stock_status', $language_id);
		
			if (!$stock_status_data) {
				$query = $this->db->query( "SELECT stock_status_id, name
											FROM " . $this->db->table("stock_statuses") . " 
											WHERE language_id = '" . $language_id . "'
											ORDER BY name");
				$stock_status_data = $query->rows;
				$this->cache->set('stock_status', $stock_status_data, $language_id);
			}	
	
			return $stock_status_data;			
		}
	}
	
	public function getStockStatusDescriptions($stock_status_id) {
		$stock_status_data = array();
		
		$query = $this->db->query("SELECT * FROM " . $this->db->table("stock_statuses") . " WHERE stock_status_id = '" . (int)$stock_status_id . "'");
		
		foreach ($query->rows as $result) {
			$stock_status_data[$result['language_id']] = array('name' => $result['name']);
		}
		
		return $stock_status_data;
	}
	
	public function getTotalStockStatuses() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . $this->db->table("stock_statuses") . " WHERE language_id = '" . (int)$this->config->get('storefront_language_id') . "'");
		
		return $query->row['total'];
	}	
}
?>