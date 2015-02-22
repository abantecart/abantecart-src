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

class ModelToolOnlineNow extends Model {

	public function getTotalTodayOnline($mode = 'all') {
		$sql = "SELECT count(*) as total
	        	FROM `" . $this->db->table("online_customers") . "` cn 
	        	WHERE DATE_FORMAT(cn.date_added,'%Y-%m-%d') = DATE_FORMAT(now(),'%Y-%m-%d') ";
		if ($mode == 'new') {
			$sql .= " AND  cn.customer_id = 0";
		} else if ($mode == 'registered') {
			$sql .= " AND  cn.customer_id > 0";
		}
		$query = $this->db->query($sql);
		return $query->row['total'];		
	}

	public function getCustomersOnline($data, $mode = 'default') {

		if ($mode == 'total_only') {
			$total_sql = 'count(*) as total';
		}
		else {
			$total_sql = "cn.ip, cn.customer_id, cn.url, cn.referer, cn.date_added";
		}
		
		$sql = "SELECT ". $total_sql ."
	        	FROM `" . $this->db->table("online_customers") . "` cn 
	        	LEFT JOIN `" . $this->db->table("customers")  . "` c ON (cn.customer_id = c.customer_id)";

		$where = array();
		if (isset($data['filter_ip']) && !is_null($data['filter_ip'])) {
		        $where[] = "cn.ip LIKE '" . $this->db->escape($data['filter_ip']) . "'";
		}
		if (isset($data['filter_customer']) && !is_null($data['filter_customer'])) {
		        $where[] = "cn.customer_id > 0 AND CONCAT(c.firstname, ' ', c.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "'";
		}
		if ($where) {
		        $sql .= " WHERE " . implode(" AND ", $where);
		}

		if ( !empty($data['subsql_filter']) ) {
			$sql .= ($where ? " AND " : 'WHERE ').$data['subsql_filter'];
		}

		//If for total, we done bulding the query
		if ($mode == 'total_only') {
		    $query = $this->db->query($sql);
		    return $query->row['total'];
		}

		$sort_data = array(
		    'ip' => 'cn.ip',
		    'customer_id' => 'cn.customer_id',
		    'date_added' => 'cn.date_added'
		);	
		
		if (isset($data['sort']) && in_array($data['sort'], array_keys($sort_data)) ) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY cn.date_added ";
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

	public function getTotalCustomersOnline($data) {
		return $this->getCustomersOnline($data, 'total_only');
	}

}