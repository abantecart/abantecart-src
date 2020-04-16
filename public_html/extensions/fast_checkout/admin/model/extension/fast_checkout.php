<?php
// Model to return enabled payment extensions with handler class. 

if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ModelExtensionFastCheckout extends Model
{

    /*
    * Get enabled payment extensions that support handler class. New arch (from 1.2.9).
    */
    public function getPaymentsWithHandler()
    {
        $query = $this->db->query("SELECT *
								   FROM ".$this->db->table("extensions")."
								   WHERE `type` = 'payment' and status = 1");
        $output = array();
        $output[] = array('' => '--- choose payment with handler ---');
        foreach ($query->rows as $row) {
            if (file_exists(DIR_EXT.$row['key'].DIR_EXT_CORE.'lib/handler.php') || $row['key'] == 'default_stripe') {
                $output[] = $row;
            }
        }
        return $output;
    }
}
