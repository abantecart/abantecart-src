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

class ModelExtensionAvataxIntegration extends Model
{

    /* getProductTaxCode return string value taxCode of product for request tax to Avatax  */
    public function getProductTaxCode($product_id)
    {
        $query = $this->db->query("SELECT taxcode_value 
                                    FROM ".$this->db->table("avatax_product_taxcode_values")." 
                                    WHERE product_id=".(int)$product_id." 
                                    LIMIT 1");

        if ($query->row['taxcode_value']) {
            return $query->row['taxcode_value'];
        } else {
            return $this->config->get('avatax_integration_default_taxcode');
        }

    }

    public function setOrderProductTaxCode($order_product_id, $value)
    {
        $this->db->query("UPDATE ".$this->db->table("order_products")." 
                        SET taxcode_value='".$value."' 
                        WHERE order_product_id=".(int)$order_product_id);
    }

    public function getCustomerSettings($customer_id)
    {
        $query = $this->db->query("SELECT * 
                                    FROM ".$this->db->table("avatax_customer_settings_values")." 
                                    WHERE customer_id=".(int)$customer_id." 
                                    LIMIT 1");
        return $query->row;
    }

    public function setCustomerSettings($customer_id, $data = array())
    {
        $sql = "SELECT * 
                FROM ".$this->db->table("avatax_customer_settings_values")." 
                WHERE customer_id=".(int)$customer_id;
        $result = $this->db->query($sql);
        if ($result->num_rows) {
            $sql = "UPDATE ".$this->db->table("avatax_customer_settings_values")." 
                    SET exemption_number = '".$this->db->escape($data['exemption_number'])."',
                        entity_use_code  = '".$this->db->escape($data['entity_use_code'])."',
                        status = 0
                    WHERE customer_id=".(int)$customer_id;
        } else {
            $sql = "INSERT INTO ".$this->db->table("avatax_customer_settings_values")." 
                    SET customer_id=".(int)$customer_id.",
                        exemption_number = '".$this->db->escape($data['exemption_number'])."',
                        entity_use_code  = '".$this->db->escape($data['entity_use_code'])."',
                        status = 0";
        }
        $this->db->query($sql);
        return true;
    }

}