<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
class ModelAccountDownload extends Model {
	public function getDownload($order_download_id) {
		$query = $this->db->query("SELECT * FROM " . $this->db->table("order_downloads") . " od LEFT JOIN `" . $this->db->table("orders") . "` o ON (od.order_id = o.order_id) WHERE o.customer_id = '" . (int)$this->customer->getId(). "' AND o.order_status_id > '0' AND o.order_status_id = '" . (int)$this->config->get('config_download_status') . "' AND od.order_download_id = '" . (int)$order_download_id . "' AND od.remaining > 0");
		 
		return $query->row;
	}
	
	public function getDownloads($start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}

		$query = $this->db->query("SELECT o.order_id, o.date_added, od.order_download_id, od.name, od.filename, od.remaining
								   FROM " . $this->db->table("order_downloads") . " od
								   LEFT JOIN `" . $this->db->table("orders") . "` o
								        ON (od.order_id = o.order_id)
								   WHERE o.customer_id = '" . (int)$this->customer->getId() . "'
								        AND o.order_status_id > '0'
								        AND o.order_status_id = '" . (int)$this->config->get('config_download_status') . "'
								   ORDER BY o.date_added DESC
								   LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}
	
	public function updateRemaining($order_download_id) {
		$this->db->query("UPDATE " . $this->db->table("order_downloads") . " SET remaining = (remaining - 1) WHERE order_download_id = '" . (int)$order_download_id . "'");
	}
	
	public function getTotalDownloads() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . $this->db->table("order_downloads") . " od LEFT JOIN `" . $this->db->table("orders") . "` o ON (od.order_id = o.order_id) WHERE o.customer_id = '" . (int)$this->customer->getId() . "' AND o.order_status_id > '0' AND o.order_status_id = '" . (int)$this->config->get('config_download_status') . "'");
		
		return $query->row['total'];
	}	
}
?>