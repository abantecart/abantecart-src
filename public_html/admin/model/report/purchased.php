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

/**
 * Class ModelReportPurchased
 */
class ModelReportPurchased extends Model {
	/**
	 * @param array $data
	 * @return array
	 */
	public function getProductPurchasedReport($data = array()) {
		$start = (int)$data['start'];
		if ($start < 0) {
			$start = 0;
		}
		$limit = (int)$data['limit'];
		if ($limit < 1) {
			$limit = 20;
		}
		$implode = array("o.order_status_id > '0'");
		if (!empty($data['filter']['date_start'])) {
			$date_start = dateDisplay2ISO($data['filter']['date_start'],$this->language->get('date_format_short'));
			$implode[] = " DATE_FORMAT(o.date_added,'%Y-%m-%d') >= DATE_FORMAT('" . $this->db->escape($date_start) . "','%Y-%m-%d') ";
		}
		if (!empty($data['filter']['date_end'])) {
			$date_end = dateDisplay2ISO($data['filter']['date_end'],$this->language->get('date_format_short'));
			$implode[] = " DATE_FORMAT(o.date_added,'%Y-%m-%d') <= DATE_FORMAT('" . $this->db->escape($date_end) . "','%Y-%m-%d') ";
		}

		$sql = "SELECT op.name, op.model, SUM(op.quantity) AS quantity, SUM(op.total + op.tax) AS total
				FROM `" . $this->db->table("orders") . "` o
				LEFT JOIN " . $this->db->table("order_products") . " op ON (op.order_id = o.order_id)
				WHERE ".implode(' AND ',$implode)."
				GROUP BY model
				ORDER BY total DESC
				LIMIT " . (int)$start . "," . (int)$limit;
		$query = $this->db->query($sql);
		return $query->rows;
	}

	/**
	 * @param array $data
	 * @return int
	 */
	public function getTotalOrderedProducts($data = array()) {

		$implode = array("o.order_status_id > '0'");
		if (!empty($data['filter']['date_start'])) {
			$date_start = dateDisplay2ISO($data['filter']['date_start'],$this->language->get('date_format_short'));
			$implode[] = " DATE_FORMAT(o.date_added,'%Y-%m-%d') >= DATE_FORMAT('" . $this->db->escape($date_start) . "','%Y-%m-%d') ";
		}
		if (!empty($data['filter']['date_end'])) {
			$date_end = dateDisplay2ISO($data['filter']['date_end'],$this->language->get('date_format_short'));
			$implode[] = " DATE_FORMAT(o.date_added,'%Y-%m-%d') <= DATE_FORMAT('" . $this->db->escape($date_end) . "','%Y-%m-%d') ";
		}

      	$query = $this->db->query("SELECT op.*
      	                            FROM " . $this->db->table("order_products") . " op
      	                            LEFT JOIN " . $this->db->table("orders") . " o ON (op.order_id = o.order_id)
      	                            WHERE ".implode(' AND ',$implode)."
      	                            GROUP BY op.model");
		return (int)$query->num_rows;
	}
}
