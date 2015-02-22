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
class ModelSaleCustomerGroup extends Model {
	public function addCustomerGroup($data) {
		$this->db->query("INSERT INTO " . $this->db->table("customer_groups") . " SET name = '" . $this->db->escape($data['name']) . "'");
		return $this->db->getLastId();
	}
	
	public function editCustomerGroup($customer_group_id, $data) {
		if ( !empty($data['name']) )
		$this->db->query("UPDATE " . $this->db->table("customer_groups") . " SET name = '" . $this->db->escape($data['name']) . "' WHERE customer_group_id = '" . (int)$customer_group_id . "'");
	}
	
	public function deleteCustomerGroup($customer_group_id) {
		$this->db->query("DELETE FROM " . $this->db->table("customer_groups") . " WHERE customer_group_id = '" . (int)$customer_group_id . "'");
		$this->db->query("DELETE FROM " . $this->db->table("product_discounts") . " WHERE customer_group_id = '" . (int)$customer_group_id . "'");
	}
	
	public function getCustomerGroup($customer_group_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . $this->db->table("customer_groups") . " WHERE customer_group_id = '" . (int)$customer_group_id . "'");
		
		return $query->row;
	}
	
	public function getCustomerGroups($data = array(), $mode = 'default') {
		if ($mode == 'total_only') {
			$sql = "SELECT count(*) as total FROM " . $this->db->table("customer_groups") . " ";
		}
		else {
			$sql = "SELECT * FROM " . $this->db->table("customer_groups") . " ";
		}
		
		if ( !empty($data['subsql_filter']) ) {
			$sql .= " WHERE ".$data['subsql_filter'];
		}
		
		//If for total, we done bulding the query
		if ($mode == 'total_only') {
		    $query = $this->db->query($sql);
		    return $query->row['total'];
		}
		
		$sql .= " ORDER BY name";	
			
		if (isset($data['order']) && (strtoupper($data['order']) == 'DESC')) {
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
	
	public function getTotalCustomerGroups($data = array()) {
		return $this->getCustomerGroups($data, 'total_only');
	}
}
?>