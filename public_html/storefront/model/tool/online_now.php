<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ModelToolOnlineNow extends Model
{
    /**
     * @param string $ip
     * @param int    $customer_id
     * @param string $url
     * @param string $referer
     */
    public function setOnline($ip, $customer_id, $url, $referer)
    {
        //if we save data less than 10 seconds ago - skip
        if( time() - (int)$this->session->data['marked_as_online'] < 10){
            return;
        }

        $this->deleteOld();
        //insert new record
        $result = $this->db->query(
                    "INSERT INTO `".$this->db->table("online_customers")."`
                            ( `ip`, `customer_id`, `url`, `referer`, `date_added` )
                            VALUES (
                                '".$this->db->escape($ip)."',
                                '".(int)$customer_id."',
                                '".$this->db->escape($url)."',
                                '".$this->db->escape($referer)."',
                                NOW()
                                )",
                    true
        );
        if(!$result){
            $sql = "UPDATE `".$this->db->table("online_customers")."`
                    SET `customer_id` = '".(int)$customer_id."',
                        `url` = '".$this->db->escape($url)."',
                        `referer` = '".$this->db->escape($referer)."',
                        `date_added` = NOW()
                    WHERE `ip` = '".$this->db->escape($ip)."'";
            $this->db->query($sql);
        }
        $this->session->data['marked_as_online'] = time();
    }

    protected function deleteOld(){
        $cache_key = "customer.online.save";
        $cache = $this->cache->pull($cache_key);
        if(!$cache || (time()-$cache) > 3600 ){
            //delete old records
            $this->db->query("DELETE FROM `".$this->db->table("online_customers")."`
                              WHERE `date_added`< (NOW() - INTERVAL 1 HOUR)");
            $this->cache->push($cache_key, time());
        }
    }
}
