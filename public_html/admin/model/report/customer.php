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

class ModelReportCustomer extends Model {

	public function getOnlineCustomers($data = array(), $mode = 'default') {

		if ($mode == 'total_only') {
			$total_sql = 'SELECT count(*) as total';
		}
		else {
			$total_sql = "SELECT	c.status, 
									co.ip, co.customer_id, 
									CONCAT(c.firstname, ' ', c.lastname) as customer, 
									co.url, co.referer, 
									co.date_added 
						";
		}

		$sql = $total_sql . " FROM " . $this->db->table("online_customers") . " co 
							LEFT JOIN " . $this->db->table("customers") . " c ON (co.customer_id = c.customer_id) ";

		$where = '';
		if (!empty($data['subsql_filter'])){
			$where .= " " . $data['subsql_filter'];
		}
		if ($where) {
			$sql .= " WHERE " . $where;
		}

		//If for total, we done bulding the query
		if ($mode == 'total_only') {
			$query = $this->db->query($sql);
			return $query->row['total'];
		}

		$sort_data = array(
		    'ip' => 'co.ip',
		    'url' => 'co.url',
		    'date_added ' => 'co.date_added '
		);

		if (isset($data['sort']) && array_key_exists($data['sort'], $sort_data)) {
		    $sql .= " ORDER BY " . $sort_data[$data['sort']];
		} else {
		    $sql .= " ORDER BY co.date_added";
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
	
	public function getTotalOnlineCustomers($data = array()) {
		return $this->getCustomersOnline($data, 'total_only');
	}


	public function getCustomerOrders($data = array(), $mode = 'default') {

		if ($mode == 'total_only') {
			$total_sql = 'SELECT COUNT(DISTINCT o.customer_id) as total';
		}
		else {
			$total_sql = "SELECT 	o.customer_id, 
									CONCAT(o.firstname, ' ', o.lastname) AS customer, 
									COALESCE(cg.name, 'N/A') AS customer_group, 
									c.status, 
									COUNT(DISTINCT o.order_id) AS order_count, 
									SUM(o.total) AS `total`
						";
		}


		$sql = $total_sql . " FROM `" . $this->db->table("orders") . "` o 
								LEFT JOIN `" . $this->db->table("customers") . "` c ON (o.customer_id = c.customer_id) 
								LEFT JOIN `" . $this->db->table("customer_groups") . "` cg ON (o.customer_group_id = cg.customer_group_id) 
							";

		$filter = (isset($data['filter']) ? $data['filter'] : array());
		$implode = array();
		$where = '';
		if (has_value($filter['order_status'])) {
			$implode[] = " o.order_status_id = " . (int)$filter['order_status'] . " ";
		}

		if (has_value($filter['customer_id'])) {
			$implode[] = " o.customer_id = " . (int)$filter['customer_id'] . " ";
		}
		if (!empty($filter['date_start'])) {
			$date_start = dateDisplay2ISO($filter['date_start'],$this->language->get('date_format_short'));
			$implode[] = " DATE_FORMAT(o.date_added,'%Y-%m-%d') >= DATE_FORMAT('" . $this->db->escape($date_start) . "','%Y-%m-%d') ";
		}
		if (!empty($filter['date_end'])) {
			$date_end = dateDisplay2ISO($filter['date_end'],$this->language->get('date_format_short'));
			$implode[] = " DATE_FORMAT(o.date_added,'%Y-%m-%d') <= DATE_FORMAT('" . $this->db->escape($date_end) . "','%Y-%m-%d') ";
		}	
		//fillter for first and last name 
		if (has_value($filter['customer'])) {
			$implode[] = "CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($filter['customer']) . "%' collate utf8_general_ci";
		}
		
		if ($implode) {
			$where .= implode(" AND ", $implode);
		}
		
		if ($where) {
			$sql .= " WHERE " . $where;
		}

		//If for total, we done bulding the query
		if ($mode == 'total_only') {
			$query = $this->db->query($sql);
			return $query->row['total'];
		}
		
		$sql .= " GROUP BY o.customer_id ";

		$sort_data = array(
		    'customer_group' => 'cg.name',
		    'orders' => 'COUNT(o.order_id)',
		    'products ' => 'SUM(op.quantity)',
		    'total' => 'SUM(o.total)'
		);

		if (isset($data['sort']) && array_key_exists($data['sort'], $sort_data)) {
		    $sql .= " ORDER BY " . $sort_data[$data['sort']];
		} else {
		    $sql .= " ORDER BY c.customer_id";
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

	public function getTotalCustomerOrders($data = array()) {
		return $this->getCustomerOrders($data, 'total_only');
	}

	public function getCustomerTransactions($data = array(), $mode = 'default') {
	
		if ($mode == 'total_only') {
			$total_sql = 'SELECT COUNT(DISTINCT c.customer_id) as total';
		}
		else {
			$total_sql = "SELECT 	c.customer_id, 
									CONCAT(c.firstname, ' ', c.lastname) AS customer, 
									ct.date_added,
									c.status, 
									ct.debit,
									ct.credit,
									ct.date_added,
									ct.transaction_type,
									u.username as created_by
						";
		}

		$sql = $total_sql . " FROM `" . $this->db->table("customer_transactions") . "` ct 
								LEFT JOIN `" . $this->db->table("customers") . "` c ON (ct.customer_id = c.customer_id) 
								LEFT JOIN `" . $this->db->table("users") . "` u ON u.user_id = ct.created_by 
							";

		$filter = (isset($data['filter']) ? $data['filter'] : array());
		$implode = array();
		$where = '';
		if (has_value($filter['customer_id'])) {
			$implode[] = " c.customer_id = " . (int)$filter['customer_id'] . " ";
		}
		if (!empty($filter['date_start'])) {
			$date_start = dateDisplay2ISO($filter['date_start'],$this->language->get('date_format_short'));
			$implode[] = " DATE_FORMAT(ct.date_added,'%Y-%m-%d') >= DATE_FORMAT('" . $this->db->escape($date_start) . "','%Y-%m-%d') ";
		}
		if (!empty($filter['date_end'])) {
			$date_end = dateDisplay2ISO($filter['date_end'],$this->language->get('date_format_short'));
			$implode[] = " DATE_FORMAT(ct.date_added,'%Y-%m-%d') <= DATE_FORMAT('" . $this->db->escape($date_end) . "','%Y-%m-%d') ";
		}	
		//fillter for first and last name 
		if (has_value($filter['customer'])) {
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($filter['customer']) . "%' collate utf8_general_ci";
		}
		
		if ($implode) {
			$where .= implode(" AND ", $implode);
		}

		if (!empty($data['subsql_filter'])){
			//$where .= " " . $data['subsql_filter'];
		}
		
		if ($where) {
			$sql .= " WHERE " . $where;
		}

		//If for total, we done bulding the query
		if ($mode == 'total_only') {
			$query = $this->db->query($sql);
			return $query->row['total'];
		}
		
		$sql .= " GROUP BY c.customer_id ";

		$sort_data = array(
		    'transaction_type' => 'ct.transaction_type',
		    'debit' => 'ct.debit',
		    'credit' => 'ct.credit',
		    'date_added' => 'ct.date_added'
		);

		if (isset($data['sort']) && array_key_exists($data['sort'], $sort_data)) {
		    $sql .= " ORDER BY " . $sort_data[$data['sort']];
		} else {
		    $sql .= " ORDER BY ct.date_added";
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

	public function getTotalCustomerTransactions($data = array()) {
		return $this->getCustomerTransactions($data, 'total_only');
	}

	public function getCustomersCountByDay() {
		$customer_data = array();

		for ($i = 0; $i < 24; $i++) {
			$customer_data[$i] = array(
				'hour'  => $i,
				'total' => 0
			);
		}

		$query = $this->db->query("SELECT COUNT(*) AS total, HOUR(date_added) AS hour FROM `" . $this->db->table("customers") . "` WHERE DATE(date_added) = DATE(NOW()) GROUP BY HOUR(date_added) ORDER BY date_added ASC");

		foreach ($query->rows as $result) {
			$customer_data[$result['hour']] = array(
				'hour'  => $result['hour'],
				'total' => $result['total']
			);
		}

		return $customer_data;
	}

	public function getCustomersCountByWeek() {
		$customer_data = array();

		$date_start = strtotime('-' . date('w') . ' days');

		for ($i = 0; $i < 7; $i++) {
			$date = date('Y-m-d', $date_start + ($i * 86400));

			$order_data[date('w', strtotime($date))] = array(
				'day'   => date('D', strtotime($date)),
				'total' => 0
			);
		}

		$query = $this->db->query("SELECT COUNT(*) AS total, date_added FROM `" . $this->db->table("customers") . "` WHERE DATE(date_added) >= DATE('" . $this->db->escape(date('Y-m-d', $date_start)) . "') GROUP BY DAYNAME(date_added)");

		foreach ($query->rows as $result) {
			$customer_data[date('w', strtotime($result['date_added']))] = array(
				'day'   => date('D', strtotime($result['date_added'])),
				'total' => $result['total']
			);
		}

		return $customer_data;
	}

	public function getCustomersCountByMonth() {
		$customer_data = array();

		for ($i = 1; $i <= date('t'); $i++) {
			$date = date('Y') . '-' . date('m') . '-' . $i;

			$customer_data[date('j', strtotime($date))] = array(
				'day'   => date('d', strtotime($date)),
				'total' => 0
			);
		}

		$query = $this->db->query("SELECT COUNT(*) AS total, date_added FROM `" . $this->db->table("customers") . "` WHERE DATE(date_added) >= '" . $this->db->escape(date('Y') . '-' . date('m') . '-1') . "' GROUP BY DATE(date_added)");

		foreach ($query->rows as $result) {
			$customer_data[date('j', strtotime($result['date_added']))] = array(
				'day'   => date('d', strtotime($result['date_added'])),
				'total' => $result['total']
			);
		}

		return $customer_data;
	}

	public function getCustomersCountByYear() {
		$customer_data = array();

		for ($i = 1; $i <= 12; $i++) {
			$customer_data[$i] = array(
				'month' => date('M', mktime(0, 0, 0, $i)),
				'total' => 0
			);
		}

		$query = $this->db->query("SELECT COUNT(*) AS total, date_added FROM `" . $this->db->table("customers") . "` WHERE YEAR(date_added) = YEAR(NOW()) GROUP BY MONTH(date_added)");

		foreach ($query->rows as $result) {
			$customer_data[date('n', strtotime($result['date_added']))] = array(
				'month' => date('M', strtotime($result['date_added'])),
				'total' => $result['total']
			);
		}

		return $customer_data;
	}
		
}