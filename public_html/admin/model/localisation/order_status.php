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
if (!defined('DIR_CORE') || !IS_ADMIN){
	header('Location: static_pages/');
}

/**
 * Class ModelLocalisationOrderStatus
 */
class ModelLocalisationOrderStatus extends Model{
	/**
	 * @param array $data
	 * @return int
	 */
	public function addOrderStatus($data){

		$status_text_id = preformatTextID($data['status_text_id']);
		if (!$status_text_id){
			return false;
		}

		$result = $this->db->query("SELECT MAX(order_status_id) as max_id FROM " . $this->db->table("order_statuses"));
		$order_status_id = (int)$result->row['max_id'] + 1;

		//check new order status id value. it need to be greater than base order status ids (see AOrderStatus class)
		$max_base_order_status_id = max(array_keys($this->order_status->getBaseStatuses()));
		$order_status_id = $order_status_id <= $max_base_order_status_id ? ($max_base_order_status_id + 1) : $order_status_id;
		$language_id = $this->language->getContentLanguageID();
		$this->language->replaceDescriptions('order_statuses',
				array ('order_status_id' => (int)$order_status_id,
				       'language_id'     => (int)$language_id),
				array ($language_id => array (
						'name' => $data['name']
				)));

		$sql = "INSERT INTO " . $this->db->table('order_status_ids') . " (order_status_id, status_text_id)
				VALUES (" . $order_status_id . ", '" . $this->db->escape($status_text_id) . "');";
		$this->db->query($sql);

		$this->cache->delete('order_status');
		return $order_status_id;
	}

	/**
	 * @param int $order_status_id
	 * @param array $data
	 */

	public function editOrderStatus($order_status_id, $data){

		$language_id = $this->language->getContentLanguageID();
		if($data['name']){
			$this->language->updateDescriptions('order_statuses',
					array ('order_status_id' => (int)$order_status_id,
					       'language_id'     => (int)$language_id),
					array ($language_id => array (
							'name' => $data['name']
					)));
		}


		$status_text_id = preformatTextID($data['status_text_id']);
		if ($status_text_id){
			$sql = "UPDATE " . $this->db->table('order_status_ids') . "
					SET status_text_id = '" . $this->db->escape($status_text_id) . "'
					WHERE order_status_id = '" . $order_status_id . "'";
			$this->db->query($sql);
		}

		$this->cache->delete('order_status');
	}

	/**
	 * @param int $order_status_id
	 * @return bool
	 */
	public function deleteOrderStatus($order_status_id){
		//prevent deleting of base statuses
		if ($order_status_id <= max(array_keys($this->order_status->getBaseStatuses()))){
			return false;
		}
		$this->db->query("DELETE FROM " . $this->db->table('order_statuses') . "
							WHERE order_status_id = '" . (int)$order_status_id . "'");
		$this->db->query("DELETE FROM " . $this->db->table('order_status_ids') . "
							WHERE order_status_id = '" . (int)$order_status_id . "'");

		$this->cache->delete('order_status');
		return true;
	}

	/**
	 * @param $order_status_id
	 * @param int $language_id
	 * @return array
	 */
	public function getOrderStatus($order_status_id, $language_id = null){
		$language_id = !(int)$language_id ? $this->language->getContentLanguageID() : (int)$language_id;
		$query = $this->db->query("SELECT os.*, osi.status_text_id
								    FROM " . $this->db->table('order_statuses') . " os
									LEFT JOIN " . $this->db->table('order_status_ids') . " osi ON osi.order_status_id = os.order_status_id
								    WHERE os.order_status_id = '" . (int)$order_status_id . "'
											AND os.language_id = '" . (int)$language_id . "'");
		return $query->row;
	}

	/**
	 * @param array $data
	 * @return array
	 */
	public function getOrderStatuses($data = array ()){
		$language_id = $this->language->getContentLanguageID();
		if ($data){
			if (isset($data['content_language_id']) && $data['content_language_id'] > 0){
				$language_id = $data['content_language_id'];
			}
			$sql = "SELECT os.*, osi.status_text_id
				    FROM " . $this->db->table('order_statuses') . " os
					LEFT JOIN " . $this->db->table('order_status_ids') . " osi ON osi.order_status_id = os.order_status_id
				    WHERE os.language_id = '" . (int)$language_id . "'
				    ORDER BY os.`name`";

			if (isset($data['order']) && ($data['order'] == 'DESC')){
				$sql .= " DESC";
			} else{
				$sql .= " ASC";
			}

			if (isset($data['start']) || isset($data['limit'])){
				if ($data['start'] < 0){
					$data['start'] = 0;
				}
				if ($data['limit'] < 1){
					$data['limit'] = 20;
				}
				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}
			$query = $this->db->query($sql);
			return $query->rows;

		} else{
			$order_status_data = $this->cache->get('order_status', $language_id);

			if (!$order_status_data){
				$query = $this->db->query("SELECT os.order_status_id, os.`name`, osi.status_text_id
										   FROM " . $this->db->table('order_statuses') . " os
										   LEFT JOIN " . $this->db->table('order_status_ids') . " osi ON osi.order_status_id = os.order_status_id
										   WHERE os.language_id = '" . $language_id . "'
										   ORDER BY os.`name`");
				$order_status_data = $query->rows;
				$this->cache->set('order_status', $order_status_data, $language_id);
			}

			return $order_status_data;
		}
	}

	/**
	 * @return int
	 */
	public function getTotalOrderStatuses(){
		$query = $this->db->query("SELECT COUNT(DISTINCT order_status_id) AS total
      	                           FROM " . $this->db->table('order_statuses'));
		return (int)$query->row['total'];
	}


	/**
	 * @deprecated since 1.2.5
	 * @param int $order_status_id
	 * @return array
	 */
	public function getOrderStatusDescriptions($order_status_id){
		$order_status_data = array ();

		$query = $this->db->query("SELECT *
									FROM " . $this->db->table('order_statuses') . "
									WHERE order_status_id = '" . (int)$order_status_id . "'");

		foreach ($query->rows as $result){
			$order_status_data[$result['language_id']] = array ('name' => $result['name']);
		}

		return $order_status_data;
	}

}
