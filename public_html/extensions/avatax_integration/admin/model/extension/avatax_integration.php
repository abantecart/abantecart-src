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

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ModelExtensionAvataxIntegration extends Model
{
    /* getProductTaxCode return string value taxCode of product for request tax to Avatax  */
    public function getProductTaxCode(int $product_id)
    {
        $query = $this->db->query(
            "SELECT taxcode_value 
            FROM " . $this->db->table("avatax_product_taxcode_values") . " 
            WHERE product_id=" . $product_id . " 
            LIMIT 1"
        );
        return (string)$query->row['taxcode_value'];
    }

    /**
     * @param int $product_id
     * @param string $value
     * @return true
     * @throws AException
     */
    public function setProductTaxCode(int $product_id, string $value)
    {
        $value = trim($value);
        if (strlen($value) == 0) {
            $this->deleteProductTaxCode($product_id);
        } else {
            $query = $this->db->query(
                "SELECT taxcode_value 
                FROM " . $this->db->table("avatax_product_taxcode_values") . " 
                WHERE product_id=" . $product_id . "
                 LIMIT 1"
            );
            if ($query->num_rows) {
                $this->db->query(
                    "UPDATE " . $this->db->table("avatax_product_taxcode_values") . " 
                    SET taxcode_value='" . $this->db->escape($value) . "' 
                    WHERE product_id=" . $product_id);
            } else {
                $this->db->query(
                    "INSERT INTO " . $this->db->table("avatax_product_taxcode_values") . " 
                    (product_id, taxcode_value) 
                    VALUES (" . $product_id . ",'" . $this->db->escape($value) . "')"
                );
            }
        }
        return true;
    }

    /**
     * @param int $product_id
     * @return void
     * @throws AException
     */
    public function deleteProductTaxCode(int $product_id)
    {
        $this->db->query(
            "DELETE FROM " . $this->db->table("avatax_product_taxcode_values") . " 
			WHERE product_id = " . $product_id
        );
    }

    /**
     * @param int $customer_id
     * @return array
     * @throws AException
     */
    public function getCustomerSettings(int $customer_id)
    {
        $query = $this->db->query(
            "SELECT * 
            FROM " . $this->db->table("avatax_customer_settings_values") . " 
            WHERE customer_id=" . $customer_id . " 
            LIMIT 1"
        );
        return $query->row;
    }

    /**
     * @param int $customer_id
     * @param array $data
     * @return true
     * @throws AException
     * @throws TransportExceptionInterface
     */
    public function setCustomerSettings(int $customer_id, array $data)
    {
        $query = $this->db->query(
            "SELECT * 
            FROM " . $this->db->table("avatax_customer_settings_values") . " 
            WHERE customer_id=" . (int)$customer_id . " 
            LIMIT 1"
        );
        if ($query->num_rows == 1) {
            $set_string = "";
            if (isset($data['status'])) {
                $set_string .= " `status` = " . (int)$data['status'];
            }
            if (isset($data['exemption_number'])) {
                if ($set_string == "") {
                    $set_string .= "`exemption_number` = '" . $this->db->escape($data['exemption_number']) . "'";
                } else {
                    $set_string .= ",`exemption_number` = '" . $this->db->escape($data['exemption_number']) . "'";
                }
            }
            if (isset($data['entity_use_code'])) {
                if ($set_string == "") {
                    $set_string .= "`entity_use_code`='" . $this->db->escape($data['entity_use_code']) . "'";
                } else {
                    $set_string .= ",`entity_use_code`='" . $this->db->escape($data['entity_use_code']) . "'";
                }
            }

            $this->db->query(
                "UPDATE " . $this->db->table("avatax_customer_settings_values") . " 
                SET " . $set_string . "  
                WHERE customer_id=" . (int)$customer_id
            );
            //send email when declined
            if ($data['status'] == 2 && $query->row['status'] != 2) {
                $this->load->model('sale/customer');
                $customer_info = $this->model_sale_customer->getCustomer($customer_id);
                if ($customer_info) {
                    $this->load->language('avatax_integration/avatax_integration');
                    $this->load->model('setting/store');
                    $store_info = $this->model_setting_store->getStore($customer_info['store_id']);
                    $mail = new AMail($this->config);
                    $mail->setTo($customer_info['email']);
                    $mail->setFrom($this->config->get('store_main_email'));
                    $mail->setSender($store_info['store_name']);
                    $mail->setSubject(
                        $this->language->getAndReplace(
                            'avatax_integration_subject',
                            replaces: $store_info['store_name']
                        )
                    );
                    $mail_text = $this->language->getAndReplace(
                        'avatax_integration_mail_text',
                        replaces: $store_info['config_url'] . 'index.php?rt=account/edit'
                    );
                    $mail->setText(html_entity_decode($mail_text, ENT_QUOTES, 'UTF-8'));
                    $mail->send();
                }
            }

        } else {
            if (!isset($data['status'])) {
                $data['status'] = (int)$data['status'];
            }
            if (!isset($data['exemption_number'])) {
                $data['exemption_number'] = "";
            }
            if (!isset($data['entity_use_code'])) {
                $data['entity_use_code'] = "";
            }
            $this->db->query(
                "INSERT INTO " . $this->db->table("avatax_customer_settings_values") . " 
                (customer_id,`status`,`exemption_number`,`entity_use_code`)  
                VALUES (" . (int)$customer_id . ",
                        " . (int)$data['status'] . ",
                        '" . $this->db->escape($data['exemption_number']) . "',
                        '" . $this->db->escape($data['entity_use_code']) . "')"
            );

        }

        return true;
    }
}