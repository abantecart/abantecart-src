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

/**
 * Class ModelAccountOrder
 *
 * @property ModelLocalisationCountry $model_localisation_country
 * @property ModelLocalisationZone    $model_localisation_zone
 */
class ModelAccountOrder extends Model
{
    /**
     * @param int    $order_id
     * @param string $order_status_id
     * @param string $mode
     *
     * @return array
     * @throws AException
     */
    public function getOrder($order_id, $order_status_id = '', $mode = '')
    {
        if ($order_status_id == '') {
            //processed order
            $status_check = " AND order_status_id > '0'";
        } else {
            if ($order_status_id == 'any') {
                //unrestricted to status
                $status_check = "";
            } else {
                //only specific status
                $status_check = " AND order_status_id = '".$order_status_id."'";
            }
        }

        $sql = "SELECT *
                FROM `".$this->db->table("orders")."`
                WHERE order_id = '".(int)$order_id."' ";
        if ($mode == '') {
            $sql .= " AND customer_id = '".(int)$this->customer->getId()."'";
        }
        $sql .= $status_check;

        $order_query = $this->db->query($sql);
        if ($order_query->num_rows) {
            $order_row = $this->dcrypt->decrypt_data($order_query->row, 'orders');
            $this->load->model('localisation/country');
            $this->load->model('localisation/zone');
            $country_row = $this->model_localisation_country->getCountry($order_row['shipping_country_id']);
            if ($country_row) {
                $shipping_iso_code_2 = $country_row['iso_code_2'];
                $shipping_iso_code_3 = $country_row['iso_code_3'];
            } else {
                $shipping_iso_code_2 = '';
                $shipping_iso_code_3 = '';
            }

            $zone_row = $this->model_localisation_zone->getZone($order_row['shipping_zone_id']);
            if ($zone_row) {
                $shipping_zone_code = $zone_row['code'];
            } else {
                $shipping_zone_code = '';
            }

            $country_row = $this->model_localisation_country->getCountry($order_row['payment_country_id']);
            if ($country_row) {
                $payment_iso_code_2 = $country_row['iso_code_2'];
                $payment_iso_code_3 = $country_row['iso_code_3'];
            } else {
                $payment_iso_code_2 = '';
                $payment_iso_code_3 = '';
            }

            $zone_row = $this->model_localisation_zone->getZone($order_row['payment_zone_id']);
            if ($zone_row) {
                $payment_zone_code = $zone_row['code'];
            } else {
                $payment_zone_code = '';
            }

            $order_data = array(
                'order_id'                => $order_row['order_id'],
                'invoice_id'              => $order_row['invoice_id'],
                'invoice_prefix'          => $order_row['invoice_prefix'],
                'customer_id'             => $order_row['customer_id'],
                'firstname'               => $order_row['firstname'],
                'lastname'                => $order_row['lastname'],
                'telephone'               => $order_row['telephone'],
                'fax'                     => $order_row['fax'],
                'email'                   => $order_row['email'],
                'shipping_firstname'      => $order_row['shipping_firstname'],
                'shipping_lastname'       => $order_row['shipping_lastname'],
                'shipping_company'        => $order_row['shipping_company'],
                'shipping_address_1'      => $order_row['shipping_address_1'],
                'shipping_address_2'      => $order_row['shipping_address_2'],
                'shipping_postcode'       => $order_row['shipping_postcode'],
                'shipping_city'           => $order_row['shipping_city'],
                'shipping_zone_id'        => $order_row['shipping_zone_id'],
                'shipping_zone'           => $order_row['shipping_zone'],
                'shipping_zone_code'      => $shipping_zone_code,
                'shipping_country_id'     => $order_row['shipping_country_id'],
                'shipping_country'        => $order_row['shipping_country'],
                'shipping_iso_code_2'     => $shipping_iso_code_2,
                'shipping_iso_code_3'     => $shipping_iso_code_3,
                'shipping_address_format' => $order_row['shipping_address_format'],
                'shipping_method'         => $order_row['shipping_method'],
                'shipping_method_key'     => $order_row['shipping_method_key'],
                'payment_firstname'       => $order_row['payment_firstname'],
                'payment_lastname'        => $order_row['payment_lastname'],
                'payment_company'         => $order_row['payment_company'],
                'payment_address_1'       => $order_row['payment_address_1'],
                'payment_address_2'       => $order_row['payment_address_2'],
                'payment_postcode'        => $order_row['payment_postcode'],
                'payment_city'            => $order_row['payment_city'],
                'payment_zone_id'         => $order_row['payment_zone_id'],
                'payment_zone'            => $order_row['payment_zone'],
                'payment_zone_code'       => $payment_zone_code,
                'payment_country_id'      => $order_row['payment_country_id'],
                'payment_country'         => $order_row['payment_country'],
                'payment_iso_code_2'      => $payment_iso_code_2,
                'payment_iso_code_3'      => $payment_iso_code_3,
                'payment_address_format'  => $order_row['payment_address_format'],
                'payment_method'          => $order_row['payment_method'],
                'payment_method_key'      => $order_row['payment_method_key'],
                'comment'                 => $order_row['comment'],
                'total'                   => $order_row['total'],
                'order_status_id'         => $order_row['order_status_id'],
                'language_id'             => $order_row['language_id'],
                'currency_id'             => $order_row['currency_id'],
                'currency'                => $order_row['currency'],
                'value'                   => $order_row['value'],
                'coupon_id'               => $order_row['coupon_id'],
                'date_modified'           => $order_row['date_modified'],
                'date_added'              => $order_row['date_added'],
                'ip'                      => $order_row['ip'],
            );
            return $order_data;
        } else {
            return array();
        }
    }

    /**
     * @param int $start
     * @param int $limit
     *
     * @return array
     */
    public function getOrders($start = 0, $limit = 20)
    {
        $language_id = (int)$this->config->get('storefront_language_id');
        if ($start < 0) {
            $start = 0;
        }
        $query = $this->db->query(
            "SELECT	o.order_id,
                        o.firstname, 
                        o.lastname, 
                        os.name as status, 
                        o.date_added, 
                        o.total, 
                        o.currency, 
                        o.value
                FROM `".$this->db->table("orders")."` o 
                LEFT JOIN ".$this->db->table("order_statuses")." os 
                    ON (o.order_status_id = os.order_status_id 
                            AND os.language_id = '".(int)$language_id."')
                WHERE customer_id = '".(int)$this->customer->getId()."' 
                    AND o.order_status_id > '0' 
                ORDER BY o.order_id DESC 
                LIMIT ".(int)$start.",".(int)$limit);
        return $query->rows;
    }

    /**
     * @param int $order_id
     *
     * @return array
     */
    public function getOrderProducts($order_id)
    {
        $query = $this->db->query("SELECT *
                                    FROM ".$this->db->table("order_products")."
                                    WHERE order_id = '".(int)$order_id."'");
        return $query->rows;
    }

    /**
     * @param int $order_id
     * @param int $order_product_id
     *
     * @return array
     */
    public function getOrderOptions($order_id, $order_product_id)
    {
        $query = $this->db->query("SELECT oo.*, po.element_type
                                    FROM ".$this->db->table("order_options")." oo
                                    LEFT JOIN ".$this->db->table('product_option_values')." pov 
                                        ON pov.product_option_value_id = oo.product_option_value_id
                                    LEFT JOIN ".$this->db->table('product_options')." po 
                                        ON po.product_option_id = pov.product_option_id
                                    WHERE oo.order_id = '".(int)$order_id."' 
                                        AND oo.order_product_id = '".(int)$order_product_id."'");
        return $query->rows;
    }

    /**
     * @param int $order_id
     *
     * @return array
     */
    public function getOrderTotals($order_id)
    {
        $query = $this->db->query("SELECT *
                                    FROM ".$this->db->table("order_totals")."
                                    WHERE order_id = '".(int)$order_id."'
                                    ORDER BY sort_order");
        return $query->rows;
    }

    /**
     * @param int $order_id
     *
     * @return string
     */
    public function getOrderStatus($order_id)
    {
        $language_id = (int)$this->config->get('storefront_language_id');
        $query = $this->db->query("SELECT os.name AS status
                                    FROM ".$this->db->table("orders")." o, 
                                    ".$this->db->table("order_statuses")." os
                                    WHERE o.order_id = '".(int)$order_id."' 
                                        AND o.order_status_id = os.order_status_id 
                                        AND os.language_id = '".(int)$language_id."'"
        );
        return $query->row['status'];
    }

    /**
     * @param int $order_id
     *
     * @return array
     */
    public function getOrderHistories($order_id)
    {
        $language_id = (int)$this->config->get('storefront_language_id');
        $query = $this->db->query("SELECT 	date_added, 
                                            os.name AS status, 
                                            oh.comment, 
                                            oh.notify 
                                    FROM ".$this->db->table("order_history")." oh 
                                    LEFT JOIN ".$this->db->table("order_statuses")." os 
                                        ON oh.order_status_id = os.order_status_id 
                                    WHERE oh.order_id = '".(int)$order_id."' 
                                            AND oh.notify = '1' 
                                            AND os.language_id = '".(int)$language_id."' 
                                    ORDER BY oh.date_added");
        return $query->rows;
    }

    /**
     * @param int $order_id
     *
     * @return array
     */
    public function getOrderDownloads($order_id)
    {
        $query = $this->db->query("SELECT *
                                    FROM ".$this->db->table("order_downloads")."
                                    WHERE order_id = '".(int)$order_id."'
                                    ORDER BY sort_order ASC");
        return $query->rows;
    }

    /**
     * @return int
     */
    public function getTotalOrders()
    {
        $query = $this->db->query("SELECT COUNT(*) AS total
                                    FROM `".$this->db->table("orders")."`
                                    WHERE customer_id = '".(int)$this->customer->getId()."' AND order_status_id > '0'");
        return (int)$query->row['total'];
    }

    /**
     * @param int $order_id
     *
     * @return int
     */
    public function getTotalOrderProductsByOrderId($order_id)
    {
        $query = $this->db->query("SELECT COUNT(*) AS total
                                    FROM ".$this->db->table("order_products")."
                                    WHERE order_id = '".(int)$order_id."'");
        return (int)$query->row['total'];
    }
}
