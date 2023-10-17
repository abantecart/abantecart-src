<?php

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}
class ModelUserUser extends Model
{
    public function getUserById($user_id){
        {
            $query = $this->db->query("SELECT * FROM ".$this->db->table("customers")." WHERE customer_id = '".intval($user_id)."'");
            return $query->row;
        }
    }

}