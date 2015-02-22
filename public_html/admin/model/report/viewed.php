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
class ModelReportViewed extends Model {
	public function getProductViewedReport($start = 0, $limit = 20) {
		$total = 0;

		$product_data = array();
		
		$query = $this->db->query("SELECT SUM(viewed) AS total FROM " . $this->db->table("products") . " ");

		$total = $query->row['total'];

		if ($start < 0) {
			$start = 0;
		}
		
		if ($limit < 1) {
			$limit = 20;
		}

		$sql = "SELECT p.product_id, p.model, p.viewed, pd.name
				FROM " . $this->db->table("products") . " p
				LEFT JOIN " . $this->db->table("product_descriptions") . " pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('storefront_language_id') . "')
				ORDER BY viewed DESC LIMIT " . (int)$start . "," . (int)$limit;

		$query = $this->db->query($sql);
		
		foreach ($query->rows as $result) {
			if ($result['viewed']) {
				$percent = round(($result['viewed'] / $total) * 100, 2) . '%';
			} else {
				$percent = '0%';
			}
			
			$product_data[] = array(
				'product_id'   => $result['product_id'],
				'name'    => $result['name'],
				'model'   => $result['model'],
				'viewed'  => $result['viewed'],
				'percent' => $percent
			);
		}
		
		return $product_data;
	}	
	
	public function reset($start = 0, $limit = 20) {
		$this->db->query("UPDATE " . $this->db->table("products") . " SET viewed = '0'");
	}
}
?>