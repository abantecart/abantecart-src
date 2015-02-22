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
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

class ModelToolOnlineNow extends Model {

        public function setOnline($ip, $customer_id, $url, $referer) {
        		//delete old records
                $this->db->query("DELETE FROM `" . $this->db->table("online_customers") ."` 
                					WHERE UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(`date_added`) > 3600");
				//insert new record
                $this->db->query("REPLACE INTO `" . $this->db->table("online_customers") . "` 
                					SET `ip` = '" . $this->db->escape($ip) . "', `customer_id` = '" . (int)$customer_id . "',
                					 	`url` = '" . $this->db->escape($url) . "',
                					 	`referer` = '" . $this->db->escape($referer) . "', 
                					 	`date_added` = NOW()");
        }
}
