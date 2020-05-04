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
