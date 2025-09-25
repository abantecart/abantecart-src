<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */
/** @noinspection PhpMultipleClassDeclarationsInspection */
class ModelExtensionAvataxIntegration extends Model
{
    /* getProductTaxCode return string value taxCode of product for request tax to Avatax  */
    /**
     * @param int $productId
     * @return mixed|null
     * @throws AException
     */
    public function getProductTaxCode(int $productId): ?string
    {
        $query = $this->db->query(
            "SELECT taxcode_value 
            FROM " . $this->db->table("avatax_product_taxcode_values") . " 
            WHERE product_id=" . (int)$productId . " 
            LIMIT 1"
        );
        if ($query->row['taxcode_value']) {
            return $query->row['taxcode_value'];
        } else {
            return $this->config->get('avatax_integration_default_taxcode');
        }
    }

    /**
     * @param int $orderProductId
     * @param string $value
     * @return void
     * @throws AException
     */
    public function setOrderProductTaxCode(int $orderProductId, string $value): void
    {
        $this->db->query(
            "UPDATE " . $this->db->table("order_products") . " 
            SET taxcode_value='" . $this->db->escape($value) . "' 
            WHERE order_product_id=" . $orderProductId
        );
    }

    /**
     * @param int $customerId
     * @return array
     * @throws AException
     */
    public function getCustomerSettings(int $customerId): array
    {
        $query = $this->db->query(
            "SELECT * 
            FROM " . $this->db->table("avatax_customer_settings_values") . " 
            WHERE customer_id=" . $customerId . " 
            LIMIT 1"
        );
        return $query->row;
    }

    /**
     * @param int $customerId
     * @param array $data
     * @return bool
     * @throws AException
     */
    public function setCustomerSettings(int $customerId, array $data = []): bool
    {
        $sql = "SELECT * 
                FROM " . $this->db->table("avatax_customer_settings_values") . " 
                WHERE customer_id=" . $customerId;
        $result = $this->db->query($sql);
        if ($result->num_rows) {
            $sql = "UPDATE " . $this->db->table("avatax_customer_settings_values") . " 
                    SET exemption_number = '" . $this->db->escape($data['exemption_number']) . "',
                        entity_use_code  = '" . $this->db->escape($data['entity_use_code']) . "',
                        status = 0
                    WHERE customer_id=" . $customerId;
        } else {
            $sql = "INSERT INTO " . $this->db->table("avatax_customer_settings_values") . " 
                    SET customer_id=" . $customerId . ",
                        exemption_number = '" . $this->db->escape($data['exemption_number']) . "',
                        entity_use_code  = '" . $this->db->escape($data['entity_use_code']) . "',
                        status = 0";
        }
        $this->db->query($sql);
        return true;
    }
}