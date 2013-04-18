<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2013 Belavier Commerce LLC

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
class ModelSaleCustomerTransaction extends Model {

	public function addCustomerTransaction($data) {
	
      	$this->db->query("INSERT INTO " . $this->db->table("customer_transactions") . "
      	                SET customer_id 		= '" . (int)$data['customer_id'] . "',
      	                	order_id 			= '" . (int)$data['order_id'] . "',
      	                    transaction_type 	= '" . $this->db->escape($data['transaction_type']) . "',
      	                    description 		= '" . $this->db->escape($data['description']) . "',
      	                    comments 			= '" . $this->db->escape($data['comments']) . "',
      	                    debit 				= '" . $this->db->escape($data['debit']) . "',
      	                    credit 				= '" . $this->db->escape($data['credit']) . "',
      	                    created_by 			= '" . (int)$data['created_by'] . "',
      	                    created_by_id 			= '" . (int)$data['created_by_id'] . "',
      	                    created = NOW()");
      	
		return $this->db->getLastId();
	}
		
	public function deleteCustomerTransaction($customer_transaction_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_transactions WHERE customer_group_id = '" . (int)$customer_transaction_id . "'");
	}
	
	public function getCustomerTransaction($customer_transaction_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer_transactions WHERE customer_transaction_id = '" . (int)$customer_transaction_id . "'");
		
		return $query->row;
	}
	
	public function getCustomerTransactions($data = array(), $mode = 'default') {
		if ($mode == 'total_only') {
			$sql = "SELECT count(*) as total FROM " . DB_PREFIX . "customer_transactions";
		}
		else {
			$sql = "SELECT * FROM " . DB_PREFIX . "customer_transactions";
		}
		
		if ( !empty($data['subsql_filter']) ) {
			$sql .= " WHERE ".$data['subsql_filter'];
		}
		
		//If for total, we done bulding the query
		if ($mode == 'total_only') {
		    $query = $this->db->query($sql);
		    return $query->row['total'];
		}
		
		$sql .= " ORDER BY created";	
			
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
	
	public function getTotalCustomerTransactions($data = array()) {
		return $this->getCustomerTransactions($data, 'total_only');
	}
}
?>