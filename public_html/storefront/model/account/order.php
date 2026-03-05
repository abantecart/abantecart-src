<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2026 Belavier Commerce LLC
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

class ModelAccountOrder extends Model
{
    /**
     * @param int $orderId
     * @param string $orderStatusId
     * @param string $mode
     *
     * @return array
     * @throws AException
     */
    public function getOrder($orderId, $orderStatusId = '', $mode = '')
    {
        $orderId = (int) $orderId;
        if (!$orderId) {
            return [];
        }

        if ($orderStatusId == '') {
            //processed order
            $status_check = " AND order_status_id > '0'";
        } else {
            if ($orderStatusId == 'any') {
                //unrestricted to status
                $status_check = "";
            } else {
                //only specific status
                $status_check = " AND order_status_id = '" . (int) $orderStatusId . "'";
            }
        }

        $sql = "SELECT *
                FROM `" . $this->db->table("orders") . "`
                WHERE order_id = '" . (int) $orderId . "' ";
        if ($mode == '') {
            $sql .= " AND customer_id = '" . (int) $this->customer->getId() . "'";
        }
        $sql .= $status_check;
        $result = $this->db->query($sql);
        if (!$result->num_rows) {
            return [];
        }

        $orderRow = $this->dcrypt->decrypt_data($result->row, 'orders');
        /** @var ModelLocalisationCountry $cMdl */
        $cMdl = $this->load->model('localisation/country');
        /** @var ModelLocalisationZone $zMdl */
        $zMdl = $this->load->model('localisation/zone');
        $countryInfo = $cMdl->getCountry($orderRow['shipping_country_id']);
        $shippingIsoCode2 = $countryInfo['iso_code_2'] ? : '';
        $shippingIsoCode3 = $countryInfo['iso_code_3'] ? : '';
        $zoneInfo = $zMdl->getZone($orderRow['shipping_zone_id']);
        $shippingZoneCode = $zoneInfo['code'] ? : '';

        $countryInfo = $cMdl->getCountry($orderRow['payment_country_id']);
        $paymentIsoCode2 = $countryInfo['iso_code_2'] ? : '';
        $paymentIsoCode3 = $countryInfo['iso_code_3'] ? : '';
        $zoneInfo = $zMdl->getZone($orderRow['payment_zone_id']);
        $payment_zone_code = $zoneInfo['code'] ? : '';

        $orderRow['ext_fields'] = $orderRow['ext_fields']
            ? json_decode($orderRow['ext_fields'], true)
            : [];
        return
            $orderRow +
            [
                'shipping_zone_code' => $shippingZoneCode,
                'shipping_iso_code_2' => $shippingIsoCode2,
                'shipping_iso_code_3' => $shippingIsoCode3,
                'payment_zone_code' => $payment_zone_code,
                'payment_iso_code_2' => $paymentIsoCode2,
                'payment_iso_code_3' => $paymentIsoCode3,
            ];
    }

    /**
     * @param int $start
     * @param int $limit
     *
     * @return array
     * @throws AException
     */
    public function getOrders($start = 0, $limit = 20)
    {
        $language_id = (int) $this->config->get('storefront_language_id');
        if ($start < 0) {
            $start = 0;
        }
        $query = $this->db->query(
            "SELECT	" . $this->db->getSqlCalcTotalRows() . "
                    o.order_id,
                    o.firstname, 
                    o.lastname, 
                    os.name as status, 
                    o.date_added, 
                    o.total, 
                    o.currency, 
                    o.value
            FROM `" . $this->db->table("orders") . "` o 
            LEFT JOIN " . $this->db->table("order_statuses") . " os 
                ON (o.order_status_id = os.order_status_id 
                        AND os.language_id = '" . $language_id . "')
            WHERE customer_id = '" . (int) $this->customer->getId() . "' 
                AND o.order_status_id > '0' 
            ORDER BY o.order_id DESC 
            LIMIT " . (int) $start . "," . (int) $limit
        );
        if ($query->num_rows) {
            $totalNumRows = $this->db->getTotalNumRows();
            $query->rows[0]['total_num_rows'] = $totalNumRows;
        }
        return $query->rows;
    }

    /**
     * @param int $order_id
     *
     * @return array
     * @throws AException
     */
    public function getOrderProducts($order_id)
    {
        $query = $this->db->query(
            "SELECT *
            FROM " . $this->db->table("order_products") . "
            WHERE order_id = '" . (int) $order_id . "'"
        );
        return $query->rows;
    }

    /**
     * @param int $order_id
     * @param int $order_product_id
     *
     * @return array
     * @throws AException
     */
    public function getOrderOptions($order_id, $order_product_id)
    {
        $query = $this->db->query(
            "SELECT oo.*, po.element_type
            FROM " . $this->db->table("order_options") . " oo
            LEFT JOIN " . $this->db->table('product_option_values') . " pov 
                ON pov.product_option_value_id = oo.product_option_value_id
            LEFT JOIN " . $this->db->table('product_options') . " po 
                ON po.product_option_id = pov.product_option_id
            WHERE oo.order_id = '" . (int) $order_id . "' 
                AND oo.order_product_id = '" . (int) $order_product_id . "'
            ORDER BY po.sort_order"
        );
        return $query->rows;
    }

    /**
     * @param int $order_id
     *
     * @return array
     * @throws AException
     */
    public function getOrderTotals($order_id)
    {
        $query = $this->db->query(
            "SELECT *
            FROM " . $this->db->table("order_totals") . "
            WHERE order_id = '" . (int) $order_id . "'
            ORDER BY sort_order"
        );
        return $query->rows;
    }

    /**
     * @param int $order_id
     *
     * @return string
     * @throws AException
     */
    public function getOrderStatus($order_id)
    {
        $language_id = (int) $this->config->get('storefront_language_id');
        $query = $this->db->query(
            "SELECT os.name AS status
            FROM " . $this->db->table("orders") . " o, 
            " . $this->db->table("order_statuses") . " os
            WHERE o.order_id = '" . (int) $order_id . "' 
                AND o.order_status_id = os.order_status_id 
                AND os.language_id = '" . $language_id . "'"
        );
        return $query->row['status'];
    }

    /**
     * @param int $order_id
     *
     * @return array
     * @throws AException
     */
    public function getOrderHistories($order_id)
    {
        $language_id = (int) $this->config->get('storefront_language_id');
        $query = $this->db->query(
            "SELECT os.name AS status, oh.* 
            FROM " . $this->db->table("order_history") . " oh 
            LEFT JOIN " . $this->db->table("order_statuses") . " os 
                ON oh.order_status_id = os.order_status_id 
            WHERE oh.order_id = '" . (int) $order_id . "' 
                    AND oh.notify = '1' 
                    AND os.language_id = '" . $language_id . "' 
            ORDER BY oh.date_added"
        );
        return $query->rows;
    }

    /**
     * @param int $order_id
     *
     * @return array
     * @throws AException
     */
    public function getOrderDownloads($order_id)
    {
        $query = $this->db->query(
            "SELECT *
            FROM " . $this->db->table("order_downloads") . "
            WHERE order_id = '" . (int) $order_id . "'
            ORDER BY sort_order"
        );
        return $query->rows;
    }

    /**
     * @return int
     * @throws AException
     */
    public function getTotalOrders()
    {
        $query = $this->db->query(
            "SELECT COUNT(*) AS total
            FROM `" . $this->db->table("orders") . "`
            WHERE customer_id = '" . (int) $this->customer->getId() . "' AND order_status_id > '0'"
        );
        return (int) $query->row['total'];
    }

    /**
     * @param int|array $orderId
     *
     * @return int|array
     * @throws AException
     */
    public function getTotalOrderProductsByOrderId(int|array $orderId)
    {
        if (is_int($orderId)) {
            $sql = "SELECT COUNT(*) AS total
                    FROM " . $this->db->table("order_products") . "
                    WHERE order_id = " . $orderId;
            $query = $this->db->query($sql);
            return (int) $query->row['total'];
        } else {
            $orderIds = filterIntegerIdList($orderId);
            if ($orderIds) {
                $sql = "SELECT COUNT(*) AS total, order_id
                        FROM " . $this->db->table("order_products") . "
                        WHERE order_id IN (" . implode(',', $orderIds) . ")
                        GROUP BY order_id";
                $query = $this->db->query($sql);
                return array_column($query->rows, 'total', 'order_id');
            }
        }
        return [];
    }

    /**
     * @param array $list
     *
     * @return array
     * @throws AException
     */
    public function prepareExtendedFields(array $list = [])
    {
        if (!$list) {
            return [];
        }
        $names = array_map([$this->db, 'escape'], array_keys($list));
        $sql = "SELECT * 
                FROM " . $this->db->table('fields') . " f
                LEFT JOIN " . $this->db->table('field_descriptions') . " fd
                    ON fd.field_id = f.field_id AND fd.language_id = '" . (int) $this->config->get(
                'storefront_language_id'
            ) . "'
                WHERE f.field_name IN ('" . implode("','", $names) . "')";
        $result = $this->db->query($sql);
        $output = [];
        foreach ($result->rows as $row) {
            if ($list[$row['field_name']]) {
                $output[$row['name']] = $list[$row['field_name']];
            }
        }
        return $output;
    }
}
