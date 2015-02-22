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
class ModelLocalisationOrderStatus extends Model {
	public function addOrderStatus($data) {
		$result = $this->db->query("SELECT MAX(order_status_id) as max_id FROM " . $this->db->table("order_statuses"));
		$order_status_id = (int)$result->row['max_id']+1;

		foreach ($data['order_status'] as $language_id => $value) {
			$this->language->replaceDescriptions('order_statuses',
											 array('order_status_id' => (int)$order_status_id,
												   'language_id' => (int)$language_id),
											 array($language_id => array(
																		'name' => $value['name']
											 )) );
		}
		$this->cache->delete('order_status');
		return $order_status_id;
	}

	public function editOrderStatus($order_status_id, $data) {

		foreach ($data['order_status'] as $language_id => $value) {
			$this->language->updateDescriptions('order_statuses',
											 array( 'order_status_id' => (int)$order_status_id,
												    'language_id' => (int)$language_id ),
											 array($language_id => array(
																		'name' => $value['name']
											 )) );
		}
		$this->cache->delete('order_status');
	}
	
	public function deleteOrderStatus($order_status_id) {
		$this->db->query(  "DELETE FROM " . DB_PREFIX . "order_statuses
							WHERE order_status_id = '" . (int)$order_status_id . "'");
	
		$this->cache->delete('order_status');
	}
		
	public function getOrderStatus($order_status_id) {
		$language_id = $this->language->getContentLanguageID();

		$query = $this->db->query("SELECT *
									FROM " . DB_PREFIX . "order_statuses
									WHERE order_status_id = '" . (int)$order_status_id . "'
											AND language_id = '" . (int)$language_id . "'");
		return $query->row;
	}
		
	public function getOrderStatuses($data = array()) {
		$language_id = $this->language->getContentLanguageID();

      	if ($data) {

      	    if ( isset($data['content_language_id']) && $data['content_language_id'] > 0 ) {
		          $language_id = $data['content_language_id'];
      	    }

			$sql = "SELECT *
				    FROM " . DB_PREFIX . "order_statuses
				    WHERE language_id = '" . (int)$language_id . "'
				    ORDER BY `name`";
			
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
			$order_status_data = $this->cache->get('order_status', $language_id);
		
			if (!$order_status_data) {
				$query = $this->db->query("SELECT order_status_id, `name`
										   FROM " . DB_PREFIX . "order_statuses
										   WHERE language_id = '" . $language_id . "'
										   ORDER BY `name`");
	
				$order_status_data = $query->rows;
			
				$this->cache->set('order_status', $order_status_data, $language_id);
			}	
	
			return $order_status_data;				
		}
	}
	
	public function getOrderStatusDescriptions($order_status_id) {
		$order_status_data = array();
		
		$query = $this->db->query( "SELECT *
									FROM " . DB_PREFIX . "order_statuses
									WHERE order_status_id = '" . (int)$order_status_id . "'");
		
		foreach ($query->rows as $result) {
			$order_status_data[$result['language_id']] = array('name' => $result['name']);
		}
		
		return $order_status_data;
	}
	
	public function getTotalOrderStatuses() {
      	$query = $this->db->query("SELECT COUNT(*) AS total
      	                           FROM " . DB_PREFIX . "order_statuses
      	                           WHERE language_id = '" . (int)$this->config->get('storefront_language_id') . "'");
		
		return $query->row['total'];
	}	
}
?>