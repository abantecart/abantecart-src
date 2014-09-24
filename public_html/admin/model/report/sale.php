<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

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
class ModelReportSale extends Model {

	public function getSaleReport($data = array(), $mode = 'default') {

		if ($mode == 'total_only') {
			$total_sql = 'COUNT(*) AS total';
		} else if ($mode == 'summary') {
			$total_sql = 'COUNT(*) AS orders, SUM(total) AS total_amount';
		}
		else {
			$total_sql = "MIN(date_added) AS date_start, MAX(date_added) AS date_end, COUNT(*) AS orders, SUM(total) AS total ";
		}

		$sql = "SELECT $total_sql FROM `" . $this->db->table("orders") . "`";

		if (isset($data['filter_order_status_id']) && $data['filter_order_status_id']) {
			$sql .= " WHERE order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE order_status_id > '0'";
		}
		
		if (isset($data['date_start'])) {
			$date_start = dateDisplay2ISO($data['date_start'],$this->language->get('date_format_short'));
		} else {
			$date_start = date('Y-m-d', strtotime('-7 day'));
		}

		if (isset($data['date_end'])) {
			$date_end = dateDisplay2ISO($data['date_end'],$this->language->get('date_format_short'));
		} else {
			$date_end = date('Y-m-d', time());
		}
		
		$sql .= " AND (DATE_FORMAT(date_added,'%Y-%m-%d') >= DATE_FORMAT('" . $this->db->escape($date_start) . "','%Y-%m-%d') 
				  AND DATE_FORMAT(date_added,'%Y-%m-%d') <= DATE_FORMAT('" . $this->db->escape($date_end) . "','%Y-%m-%d') )";

		//If for total, we done bulding the query
		if ($mode == 'total_only') {
		    $query = $this->db->query($sql);
		    return $query->row['total'];
		}
		if ($mode == 'summary') {
		    $query = $this->db->query($sql);
		    return $query->row;
		}
		
		if (isset($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = $data['group'];
		}
		$group = !$group ? 'week' : $group;

		switch($group) {
			case 'day';
				$sql .= " GROUP BY DAY(date_added)";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY WEEK(date_added)";
				break;	
			case 'month':
				$sql .= " GROUP BY MONTH(date_added)";
				break;
			case 'year':
				$sql .= " GROUP BY YEAR(date_added)";
				break;									
		}

		if(isset($data['sort'])){
			$sql .= " ORDER BY ".$this->db->escape($data['sort'])." ".$this->db->escape($data['order']);
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
	
	public function getSaleReportTotal($data = array()) {
		return $this->getSaleReport($data, 'total_only');	
	}

	public function getSaleReportSummary($data = array()) {
		return $this->getSaleReport($data, 'summary');	
	}

}
?>