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
class ModelReportPurchased extends Model {
	public function getProductPurchasedReport($start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}
		
		if ($limit < 1) {
			$limit = 20;
		}
		
		$query = $this->db->query("SELECT op.name, op.model, SUM(op.quantity) AS quantity, SUM(op.total + op.tax) AS total
									FROM `" . $this->db->table("orders") . "` o
									LEFT JOIN " . $this->db->table("order_products") . " op ON (op.order_id = o.order_id)
									WHERE o.order_status_id > '0'
									GROUP BY model
									ORDER BY total DESC
									LIMIT " . (int)$start . "," . (int)$limit);
		return $query->rows;
	}
	
	public function getTotalOrderedProducts() {
      	$query = $this->db->query("SELECT *
      	                            FROM `" . $this->db->table("order_products") . "`
      	                            GROUP BY model");
		return $query->num_rows;
	}
}
?>