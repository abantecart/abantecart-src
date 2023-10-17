<?php

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}
class ModelExtensionBackToStock extends Model
{
    /**
     * Set queue by user id and product id
     * @param $user_id
     * @param $product_id
     * @throws AException
     */
    public function addToQueue($user_id, $product_id)
    {
        $query = "INSERT INTO ".$this->db->table('back_to_stock')."
        SET `user_id` = '".(int) $user_id."', 
            `product_id` = '".(int) $product_id."',  
            `date_modified` = NOW(), 
            `date_added` = NOW()";

        if ($this->db->query($query)) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Get Queue by user id and product id
     * @param $user_id
     * @param $product_id
     */
    public function getQueueByUserProductId($user_id,$product_id){
        $result = $this->db->query(
            "SELECT * 
             FROM `".$this->db->table("back_to_stock")."` 
             WHERE `user_id` = '".intval($user_id)."'
                AND `product_id` = '".intval($product_id)."' "
        );
        return $result->row;
    }
}