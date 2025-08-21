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
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ModelCheckoutOrder extends Model
{
    /**
     * @param $orderId
     *
     * @return array|bool
     * @throws AException
     */
    public function getOrder($orderId)
    {
        $result = $this->db->query(
            "SELECT *
            FROM `" . $this->db->table("orders") . "`
            WHERE order_id = '" . (int)$orderId . "'"
        );

        if ($result->num_rows) {
            /** @var ModelLocalisationCountry $cMdl */
            $cMdl = $this->load->model('localisation/country');
            $countryInfo = $cMdl->getCountry($result->row['shipping_country_id']);
            if ($countryInfo) {
                $shipping_iso_code_2 = $countryInfo['iso_code_2'];
                $shipping_iso_code_3 = $countryInfo['iso_code_3'];
            } else {
                $shipping_iso_code_2 = '';
                $shipping_iso_code_3 = '';
            }

            /** @var ModelLocalisationZone $zMdl */
            $zMdl = $this->load->model('localisation/zone');
            $zoneInfo = $zMdl->getZone($result->row['shipping_zone_id']);
            if ($zoneInfo) {
                $shipping_zone_code = $zoneInfo['code'];
            } else {
                $shipping_zone_code = '';
            }

            $countryInfo = $cMdl->getCountry($result->row['payment_country_id']);
            if ($countryInfo) {
                $payment_iso_code_2 = $countryInfo['iso_code_2'];
                $payment_iso_code_3 = $countryInfo['iso_code_3'];
            } else {
                $payment_iso_code_2 = '';
                $payment_iso_code_3 = '';
            }

            $zoneInfo = $zMdl->getZone($result->row['payment_zone_id']);
            if ($zoneInfo) {
                $payment_zone_code = $zoneInfo['code'];
            } else {
                $payment_zone_code = '';
            }

            $output = $this->dcrypt->decrypt_data($result->row, 'orders');

            $output['shipping_zone_code'] = $shipping_zone_code;
            $output['shipping_iso_code_2'] = $shipping_iso_code_2;
            $output['shipping_iso_code_3'] = $shipping_iso_code_3;
            $output['payment_zone_code'] = $payment_zone_code;
            $output['payment_iso_code_2'] = $payment_iso_code_2;
            $output['payment_iso_code_3'] = $payment_iso_code_3;

            return $output;
        } else {
            return false;
        }
    }

    /**
     * @param array $data
     * @param int $setOrderId
     *
     * @return null|bool|int
     */
    public function create($data, $setOrderId = null)
    {
        $result = $this->extensions->hk_create($this, $data, $setOrderId);
        if ($result !== null) {
            return $result;
        }
    }

    /**
     * @param array $data
     * @param int|string $set_order_id
     *
     * @return bool|int
     * @throws AException
     */
    public function _create($data, $set_order_id = '')
    {
        $set_order_id = (int)$set_order_id;
        //reuse same order_id or unused one order_status_id = 0
        if ($set_order_id) {
            $query = $this->db->query(
                "SELECT order_id
                FROM `" . $this->db->table("orders") . "`
                WHERE order_id = " . $set_order_id . " 
                    AND order_status_id = '0'"
            );

            if (!$query->num_rows) { // for already processed orders do redirect
                $query = $this->db->query(
                    "SELECT order_id
                    FROM `" . $this->db->table("orders") . "`
                    WHERE order_id = " . $set_order_id . " 
                        AND order_status_id > '0'"
                );
                if ($query->num_rows) {
                    return false;
                }
                //remove
            } else {
                $this->_remove_order($query->row['order_id']);
            }
        }

        //clean up based on setting (remove already created or abandoned orders)
        if ((int)$this->config->get('config_expire_order_days')) {
            $query = $this->db->query(
                "SELECT order_id
                    FROM " . $this->db->table("orders") . "
                    WHERE date_modified < '"
                . date(
                    'Y-m-d',
                    strtotime('-' . (int)$this->config->get('config_expire_order_days') . ' days')
                ) . "' AND order_status_id = '0'"
            );
            foreach ($query->rows as $result) {
                $this->_remove_order($result['order_id']);
            }
        }

        if (!$set_order_id && (int)$this->config->get('config_start_order_id')) {
            $query = $this->db->query(
                "SELECT MAX(order_id) AS order_id
                FROM `" . $this->db->table("orders") . "`"
            );
            if ($query->row['order_id'] && $query->row['order_id'] >= $this->config->get('config_start_order_id')) {
                $set_order_id = (int)$query->row['order_id'] + 1;
            } elseif ($this->config->get('config_start_order_id')) {
                $set_order_id = (int)$this->config->get('config_start_order_id');
            } else {
                $set_order_id = 0;
            }
        }

        if ($set_order_id) {
            $set_order_id = "order_id = '" . (int)$set_order_id . "', ";
        } else {
            $set_order_id = '';
        }

        $key_sql = '';
        if ($this->dcrypt->active) {
            $data = $this->dcrypt->encrypt_data($data, 'orders');
            $key_sql = ", key_id = '" . (int)$data['key_id'] . "'";
        }

        $this->db->query(
            "INSERT INTO `" . $this->db->table("orders") . "`
            SET " . $set_order_id . " store_id = '" . (int)$data['store_id'] . "',
                store_name = '" . $this->db->escape($data['store_name']) . "',
                store_url = '" . $this->db->escape($data['store_url']) . "',
                customer_id = '" . (int)$data['customer_id'] . "',
                customer_group_id = '" . (int)$data['customer_group_id'] . "',
                firstname = '" . $this->db->escape($data['firstname']) . "',
                lastname = '" . $this->db->escape($data['lastname']) . "',
                email = '" . $this->db->escape($data['email']) . "',
                telephone = '" . $this->db->escape($data['telephone']) . "',
                fax = '" . $this->db->escape($data['fax']) . "',
                total = '" . (float)$data['total'] . "',
                language_id = '" . (int)$data['language_id'] . "',
                currency = '" . $this->db->escape($data['currency']) . "',
                currency_id = '" . (int)$data['currency_id'] . "',
                value = '" . (float)$data['value'] . "',
                coupon_id = '" . (int)$data['coupon_id'] . "',
                ip = '" . $this->db->escape($data['ip']) . "',
                shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "',
                shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "',
                shipping_company = '" . $this->db->escape($data['shipping_company']) . "',
                shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "',
                shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "',
                shipping_city = '" . $this->db->escape($data['shipping_city']) . "',
                shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "',
                shipping_zone = '" . $this->db->escape($data['shipping_zone']) . "',
                shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "',
                shipping_country = '" . $this->db->escape($data['shipping_country']) . "',
                shipping_country_id = '" . (int)$data['shipping_country_id'] . "',
                shipping_address_format = '" . $this->db->escape($data['shipping_address_format']) . "',
                shipping_method = '" . $this->db->escape($data['shipping_method']) . "',
                shipping_method_key = '" . $this->db->escape($data['shipping_method_key']) . "',
                payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "',
                payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "',
                payment_company = '" . $this->db->escape($data['payment_company']) . "',
                payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "',
                payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "',
                payment_city = '" . $this->db->escape($data['payment_city']) . "',
                payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "',
                payment_zone = '" . $this->db->escape($data['payment_zone']) . "',
                payment_zone_id = '" . (int)$data['payment_zone_id'] . "',
                payment_country = '" . $this->db->escape($data['payment_country']) . "',
                payment_country_id = '" . (int)$data['payment_country_id'] . "',
                payment_address_format = '" . $this->db->escape($data['payment_address_format']) . "',
                payment_method = '" . $this->db->escape($data['payment_method']) . "',
                payment_method_key = '" . $this->db->escape($data['payment_method_key']) . "',
                comment = '" . $this->db->escape($data['comment']) . "'"
            . $key_sql . ",
                date_modified = NOW(),
                date_added = NOW()"
        );

        $order_id = $this->db->getLastId();

        foreach ($data['products'] as $product) {
            $this->db->query(
                "INSERT INTO " . $this->db->table("order_products") . "
                SET `order_id` = '" . (int)$order_id . "',
                    `product_id` = '" . (int)$product['product_id'] . "',
                    `name` = '" . $this->db->escape($product['name']) . "',
                    `model` = '" . $this->db->escape($product['model']) . "',
                    `sku` = '" . $this->db->escape($product['sku']) . "',
                    `price` = '" . (float)$product['price'] . "',
                    `cost` = '" . (float)$product['cost'] . "',
                    `total` = '" . (float)$product['total'] . "',
                    `tax` = '" . (float)$product['tax'] . "',
                    `quantity` = '" . (int)$product['quantity'] . "',
                    `subtract` = '" . (int)$product['stock'] . "',
                    `weight` = '" . (float)$product['weight'] . "',
                    `weight_iso_code` = '" . $this->db->escape($product['weight_iso_code']) . "',
                    `width` = '" . (float)$product['width'] . "',
                    `height` = '" . (float)$product['height'] . "',
                    `length` = '" . (float)$product['length'] . "',
                    `length_iso_code` = '" . $this->db->escape($product['length_iso_code']) . "'"
            );

            $order_product_id = $this->db->getLastId();

            foreach ($product['option'] as $option) {
                $this->db->query(
                    "INSERT INTO " . $this->db->table("order_options") . "
                    SET order_id = '" . (int)$order_id . "',
                        order_product_id = '" . (int)$order_product_id . "',
                        product_option_value_id = '" . (int)$option['product_option_value_id'] . "',
                        name = '" . $this->db->escape($option['name']) . "',
                        sku = '" . $this->db->escape($option['sku']) . "',
                        `value` = '" . $this->db->escape($option['value']) . "',
                        price = '" . (float)$product['price'] . "',
                        cost = '" . (float)$option['cost'] . "',
                        prefix = '" . $this->db->escape($option['prefix']) . "',
                        settings = '" . $this->db->escape($option['settings']) . "'"
                );
            }

            foreach ($product['download'] as $download) {
                // if expire days not set - 0  as unexpired
                $download['expire_days'] = (int)$download['expire_days'] > 0 ? $download['expire_days'] : 0;
                $download['max_downloads'] = ((int)$download['max_downloads']
                    ? (int)$download['max_downloads'] * $product['quantity']
                    : '');
                //disable download for manual mode for customer
                $download['status'] = $download['activate'] == 'manually' ? 0 : 1;
                $download['attributes_data'] = serialize(
                    $this->download->getDownloadAttributesValues($download['download_id'])
                );
                $this->download->addProductDownloadToOrder($order_product_id, $order_id, $download);
            }
        }
        foreach ($data['totals'] as $total) {
            $this->db->query(
                "INSERT INTO " . $this->db->table("order_totals") . "
                SET `order_id` = '" . (int)$order_id . "',
                    `title` = '" . $this->db->escape($total['title']) . "',
                    `text` = '" . $this->db->escape($total['text']) . "',
                    `value` = '" . (float)$total['value'] . "',
                    `sort_order` = '" . (int)$total['sort_order'] . "',
                    `type` = '" . $this->db->escape($total['total_type']) . "',
                    `key` = '" . $this->db->escape($total['id']) . "'"
            );
        }

        //save IM URI of order
        $this->saveIMOrderData($order_id, $data);
        return $order_id;
    }

    protected function saveIMOrderData($order_id, $data)
    {
        $protocols = $this->im->getProtocols();
        $p = [];
        foreach ($protocols as $protocol) {
            $p[] = $this->db->escape($protocol);
        }

        $sql = "SELECT DISTINCT `type_id`, `name` as protocol
                FROM " . $this->db->table('order_data_types') . "
                WHERE `name` IN ('" . implode("', '", $p) . "')";
        $result = $this->db->query($sql);
        $protocols = array_column($result->rows, 'protocol','type_id');

        $savedProtocols = [];
        foreach ($protocols as $type_id => $protocol) {
            //prevent duplicates
            if(in_array($protocol, $savedProtocols)) {
                continue;
            }

            $type_id = (int)$type_id;
            if ($data['customer_id']) {
                $uri = $this->im->getCustomerURI($protocol, $data['customer_id']);
            } else {
                $uri = $data[$protocol];
            }
            if ($uri) {
                $im_data = serialize(
                    [
                        'uri'    => $uri,
                        'status' => $this->config->get('config_im_guest_' . $protocol . '_status'),
                    ]
                );

                $sql = "SELECT * 
                        FROM " . $this->db->table('order_data') . "
                        WHERE order_id = " . (int)$order_id . " 
                            AND type_id = " . $type_id;
                $r = $this->db->query($sql);
                if (!$r->num_rows) {
                    $sql = "INSERT INTO " . $this->db->table('order_data') . "
                        (`order_id`, `type_id`, `data`, `date_added`)
                        VALUES 
                        (" . (int)$order_id . ", " . (int)$type_id . ", '" . $this->db->escape($im_data) . "', NOW() )";
                }else{
                    $sql = "UPDATE " . $this->db->table('order_data') . "
                            SET `data` = '" . $this->db->escape($im_data) . "'
                            WHERE order_id = " . (int)$order_id . " AND type_id = " . (int)$type_id;
                }
                $this->db->query($sql);
                $savedProtocols[] = $protocol;
            }
        }
    }

    /**
     * @param int $order_id
     * @param int $order_status_id
     * @param string $comment
     */
    public function confirm($order_id, $order_status_id, $comment = '')
    {
        $this->extensions->hk_confirm($this, $order_id, $order_status_id, $comment);
    }

    /**
     * @param int $orderId
     * @param int $order_status_id
     * @param string $comment
     *
     * @return bool
     * @throws AException|TransportExceptionInterface
     */
    public function _confirm($orderId, $order_status_id, $comment = '')
    {
        /** @var ModelCatalogProduct $pMdl */
        $pMdl = $this->load->model('catalog/product');

        $order_query = $this->db->query(
            "SELECT *,
                l.filename AS filename,
                l.directory AS directory
            FROM `" . $this->db->table("orders") . "` o
            LEFT JOIN " . $this->db->table("languages") . " l
            ON (o.language_id = l.language_id)
            WHERE o.order_id = '" . (int)$orderId . "'
                AND o.order_status_id = '0'"
        );
        if (!$order_query->num_rows) {
            return false;
        }
        $orderInfo = $this->dcrypt->decrypt_data($order_query->row, 'orders');
        $update = [];

        //update order status
        $update[] = "order_status_id = '" . (int)$order_status_id . "'";
        $sql = "UPDATE `" . $this->db->table("orders") . "`
                SET " . implode(", ", $update) . "
                WHERE order_id = '" . (int)$orderId . "'";
        $this->db->query($sql);

        //record history
        $this->db->query(
            "INSERT INTO " . $this->db->table("order_history") . "
            SET order_id = '" . (int)$orderId . "',
                order_status_id = '" . (int)$order_status_id . "',
                notify = '1',
                comment = '" . $this->db->escape($comment) . "',
                date_added = NOW()"
        );
        $orderInfo['comment'] = $orderInfo['comment'] . ' ' . $comment;

        $order_product_query = $this->db->query(
            "SELECT *
             FROM " . $this->db->table("order_products") . "
             WHERE order_id = '" . (int)$orderId . "'"
        );
        // load language for IM
        $language = new ALanguage($this->registry);
        $language->load($language->language_details['directory']);
        $language->load('common/im');

        //update products inventory
        foreach ($order_product_query->rows as $product) {
            $order_option_query = $this->db->query(
                "SELECT op.*, pov.subtract
                FROM " . $this->db->table("order_options") . " op
                LEFT JOIN " . $this->db->table("product_option_values") . " pov
                    ON pov.product_option_value_id = op.product_option_value_id
                WHERE op.order_id = '" . (int)$orderId . "'
                   AND op.order_product_id = '" . (int)$product['order_product_id'] . "'"
            );
            //update options stock
            $stock_updated = false;
            foreach ($order_option_query->rows as $option) {
                $this->db->query(
                    "UPDATE " . $this->db->table("product_option_values") . "
                    SET quantity = (quantity - " . (int)$product['quantity'] . ")
                    WHERE product_option_value_id = '" . (int)$option['product_option_value_id'] . "'
                        AND subtract = 1"
                );
                if ($option['subtract']) {
                    $this->saveOrderProductStocks(
                        $product['order_product_id'],
                        $product['product_id'],
                        $option['product_option_value_id'],
                        $product['quantity']
                    );
                    $stock_updated = true;
                }

                $sql = "SELECT quantity
                        FROM " . $this->db->table("product_option_values") . "
                        WHERE product_option_value_id = '" . (int)$option['product_option_value_id'] . "'
                            AND subtract = 1";
                $res = $this->db->query($sql);
                $threshold = (int)$this->config->get('product_out_of_stock_threshold');
                if ($res->num_rows && $res->row['quantity'] <= $threshold) {
                    //notify admin about out of stock for option based product
                    $message_arr = [
                        1 => [
                            'message' => sprintf(
                                $language->get('im_product_out_of_stock_admin_text'),
                                $product['product_id']
                            ),
                        ],
                    ];

                    $stock = $pMdl->hasAnyStock((int)$product['product_id']);
                    if ($stock <= $threshold && (int)$product['product_id']) {
                        if ($stock <= 0 && $this->config->get('config_nostock_autodisable')) {
                            $this->db->query(
                                'UPDATE ' . $this->db->table('products') . ' 
                                SET status=0 
                                WHERE product_id=' . (int)$product['product_id']
                            );
                        }
                        $this->im->send(
                            'product_out_of_stock',
                            $message_arr,
                            'storefront_product_out_of_stock_admin_notify',
                            $product
                        );
                    }
                }
            }

            if (!$stock_updated) {
                $this->db->query(
                    "UPDATE " . $this->db->table("products") . "
                    SET quantity = (quantity - " . (int)$product['quantity'] . ")
                    WHERE product_id = '" . (int)$product['product_id'] . "' 
                        AND subtract = 1"
                );
                $this->saveOrderProductStocks(
                    $product['order_product_id'],
                    $product['product_id'],
                    null,
                    $product['quantity']
                );

                //check quantity and send notification when 0 or less
                $sql = "SELECT quantity
                        FROM " . $this->db->table("products") . "
                        WHERE product_id = '" . (int)$product['product_id'] . "' AND subtract = 1";
                $res = $this->db->query($sql);
                $threshold = (int)$this->config->get('product_out_of_stock_threshold');
                if ($res->num_rows && $res->row['quantity'] <= $threshold) {
                    //notify admin about out of stock
                    $message_arr = [
                        1 => [
                            'message' => sprintf(
                                $language->get('im_product_out_of_stock_admin_text'),
                                $product['product_id']
                            ),
                        ],
                    ];
                    $stock = $pMdl->hasAnyStock((int)$product['product_id']);
                    if ($stock <= $threshold && (int)$product['product_id']) {
                        if ($stock <= 0 && $this->config->get('config_nostock_autodisable')) {
                            $this->db->query(
                                'UPDATE ' . $this->db->table('products') . ' 
                                SET status=0 
                                WHERE product_id=' . (int)$product['product_id']
                            );
                        }
                        $this->im->send(
                            'product_out_of_stock',
                            $message_arr,
                            'storefront_product_out_of_stock_admin_notify',
                            $product
                        );
                    }
                }
            }
        }

        //clean product cache as stock might have changed.
        $this->cache->remove('product');

        //build confirmation email
        $language = new ALanguage($this->registry, $orderInfo['code']);
        $language->load($orderInfo['filename']);
        $language->setCurrentLanguage();
        $language->load('mail/order_confirm');
        $languageId = $language->getLanguageID();

        $this->load->model('localisation/currency');
        $order_product_query = $this->db->query(
            "SELECT *
            FROM " . $this->db->table("order_products") . "
            WHERE order_id = '" . (int)$orderId . "'"
        );
        $order_total_query = $this->db->query(
            "SELECT *
            FROM " . $this->db->table("order_totals") . "
            WHERE order_id = '" . (int)$orderId . "'
            ORDER BY sort_order ASC"
        );

        foreach ($order_total_query->rows as $row) {
            if ($row['type'] == 'total') {
                $this->data['mail_template_data']['order_total'] = $row;
                break;
            }
        }

        // HTML Mail
        $this->data['mail_template_data']['title'] = sprintf(
            $language->get('text_subject'),
            html_entity_decode($orderInfo['store_name'], ENT_QUOTES, 'UTF-8'),
            $orderId
        );
        $this->data['mail_template_data']['text_greeting'] = sprintf(
            $language->get('text_greeting'),
            html_entity_decode($orderInfo['store_name'], ENT_QUOTES, 'UTF-8')
        );
        $this->data['mail_template_data']['text_order_detail'] = $language->get('text_order_detail');
        $this->data['mail_template_data']['text_order_id'] = $language->get('text_order_id');
        $this->data['mail_template_data']['text_invoice'] = $language->get('text_invoice');
        $this->data['mail_template_data']['text_date_added'] = $language->get('text_date_added');
        $this->data['mail_template_data']['text_telephone'] = $language->get('text_telephone');
        $this->data['mail_template_data']['text_mobile_phone'] = $language->get('text_mobile_phone');

        $this->data['mail_template_data']['text_email'] = $language->get('text_email');
        $this->data['mail_template_data']['text_ip'] = $language->get('text_ip');
        $this->data['mail_template_data']['text_fax'] = $language->get('text_fax');
        $this->data['mail_template_data']['text_shipping_address'] = $language->get('text_shipping_address');
        $this->data['mail_template_data']['text_payment_address'] = $language->get('text_payment_address');
        $this->data['mail_template_data']['text_shipping_method'] = $language->get('text_shipping_method');
        $this->data['mail_template_data']['text_payment_method'] = $language->get('text_payment_method');
        $this->data['mail_template_data']['text_comment'] = $language->get('text_comment');
        $this->data['mail_template_data']['text_powered_by'] = $language->get('text_powered_by');
        $this->data['mail_template_data']['text_project_label'] = $language->get('text_powered_by') . ' ' . project_base();

        $this->data['mail_template_data']['text_total'] = $language->get('text_total');
        $this->data['mail_template_data']['text_footer'] = $language->get('text_footer');

        $this->data['mail_template_data']['column_product'] = $language->get('column_product');
        $this->data['mail_template_data']['column_model'] = $language->get('column_model');
        $this->data['mail_template_data']['column_quantity'] = $language->get('column_quantity');
        $this->data['mail_template_data']['column_price'] = $language->get('column_price');
        $this->data['mail_template_data']['column_total'] = $language->get('column_total');

        $this->data['mail_template_data']['order_id'] = $orderId;
        $this->data['mail_template_data']['customer_id'] = $orderInfo['customer_id'];
        $this->data['mail_template_data']['date_added'] = dateISO2Display(
            $orderInfo['date_added'],
            $language->get('date_format_short')
        );

        $mailLogo = $this->config->get('config_mail_logo_' . $languageId)
            ?: $this->config->get('config_mail_logo');
        $mailLogo = $mailLogo ?: $this->config->get('config_logo_' . $languageId);
        $mailLogo = $mailLogo ?: $this->config->get('config_logo');

        if ($mailLogo) {
            $result = getMailLogoDetails($mailLogo);
            $this->data['mail_template_data']['logo_uri'] = $result['uri'];
            $this->data['mail_template_data']['logo_html'] = $result['html'];
        }

        $this->data['mail_template_data']['store_name'] = $orderInfo['store_name'];
        $this->data['mail_template_data']['address'] = nl2br($this->config->get('config_address'));
        $this->data['mail_template_data']['telephone'] = $this->config->get('config_telephone');
        $this->data['mail_template_data']['fax'] = $this->config->get('config_fax');
        $this->data['mail_template_data']['email'] = $this->config->get('store_main_email');
        $this->data['mail_template_data']['store_url'] = $orderInfo['store_url'];

        //give link on order page for quest
        if ($this->config->get('config_guest_checkout') && $orderInfo['email']) {
            $order_token = generateOrderToken($orderId, $orderInfo['email']);
            $this->data['mail_template_data']['invoice'] = $orderInfo['store_url']
                . 'index.php?rt=account/order_details&ot=' . $order_token . "\n\n";
        }//give link on order for registered customers
        elseif ($orderInfo['customer_id']) {
            $this->data['mail_template_data']['invoice'] = $orderInfo['store_url']
                . 'index.php?rt=account/order_details&order_id=' . $orderId;
        }

        $this->data['mail_template_data']['firstname'] = $orderInfo['firstname'];
        $this->data['mail_template_data']['lastname'] = $orderInfo['lastname'];
        $this->data['mail_template_data']['shipping_method'] = $orderInfo['shipping_method'];
        $this->data['mail_template_data']['payment_method'] = $orderInfo['payment_method'];
        $this->data['mail_template_data']['customer_email'] = $orderInfo['email'];
        $this->data['mail_template_data']['customer_telephone'] = $orderInfo['telephone'];
        $this->data['mail_template_data']['customer_mobile_phone'] = $this->im->getCustomerURI(
            'sms',
            (int)$orderInfo['customer_id'],
            $orderId
        );
        $this->data['mail_template_data']['customer_fax'] = $orderInfo['fax'];
        $this->data['mail_template_data']['customer_ip'] = $orderInfo['ip'];
        $this->data['mail_template_data']['comment'] = trim(
            nl2br(html_entity_decode($orderInfo['comment'], ENT_QUOTES, 'UTF-8'))
        );

        //override with the data from the before hooks
        if ($this->data) {
            $this->data['mail_template_data'] = array_merge($this->data['mail_template_data'], $this->data);
        }

        /** @var ModelLocalisationZone $zMdl */
        $zMdl = $this->load->model('localisation/zone');
        $zoneInfo = $zMdl->getZone($orderInfo['shipping_zone_id']);
        if ($zoneInfo) {
            $zoneCode = $zoneInfo['code'];
        } else {
            $zoneCode = '';
        }

        $shipping_data = [
            'firstname' => $orderInfo['shipping_firstname'],
            'lastname'  => $orderInfo['shipping_lastname'],
            'company'   => $orderInfo['shipping_company'],
            'address_1' => $orderInfo['shipping_address_1'],
            'address_2' => $orderInfo['shipping_address_2'],
            'city'      => $orderInfo['shipping_city'],
            'postcode'  => $orderInfo['shipping_postcode'],
            'zone'      => $orderInfo['shipping_zone'],
            'zone_code' => $zoneCode,
            'country'   => $orderInfo['shipping_country'],
        ];

        $this->data['mail_template_data']['shipping_data'] = $shipping_data;
        $this->data['mail_template_data']['shipping_address'] = $this->customer->getFormattedAddress(
            $shipping_data,
            $orderInfo['shipping_address_format']
        );
        $zoneInfo = $zMdl->getZone($orderInfo['payment_zone_id']);
        if ($zoneInfo) {
            $zoneCode = $zoneInfo['code'];
        } else {
            $zoneCode = '';
        }

        $payment_data = [
            'firstname' => $orderInfo['payment_firstname'],
            'lastname'  => $orderInfo['payment_lastname'],
            'company'   => $orderInfo['payment_company'],
            'address_1' => $orderInfo['payment_address_1'],
            'address_2' => $orderInfo['payment_address_2'],
            'city'      => $orderInfo['payment_city'],
            'postcode'  => $orderInfo['payment_postcode'],
            'zone'      => $orderInfo['payment_zone'],
            'zone_code' => $zoneCode,
            'country'   => $orderInfo['payment_country'],
        ];

        $this->data['mail_template_data']['payment_data'] = $payment_data;
        $this->data['mail_template_data']['payment_address'] = $this->customer->getFormattedAddress(
            $payment_data,
            $orderInfo['payment_address_format']
        );

        if (!has_value($this->data['products'])) {
            $this->data['products'] = [];
        }

        foreach ($order_product_query->rows as $product) {
            $option_data = [];

            $order_option_query = $this->db->query(
                "SELECT oo.*, po.element_type, p.sku, p.product_id
                FROM " . $this->db->table("order_options") . " oo
                LEFT JOIN " . $this->db->table("product_option_values") . " pov
                    ON pov.product_option_value_id = oo.product_option_value_id
                LEFT JOIN " . $this->db->table("product_options") . " po
                    ON po.product_option_id = pov.product_option_id
                LEFT JOIN " . $this->db->table("products") . " p
                    ON p.product_id = po.product_id
                WHERE oo.order_id = '" . (int)$orderId . "' 
                    AND oo.order_product_id = '" . (int)$product['order_product_id'] . "'"
            );

            foreach ($order_option_query->rows as $option) {
                if ($option['element_type'] == 'H') {
                    continue;
                } //skip hidden options
                elseif ($option['element_type'] == 'C' && in_array($option['value'], [0, 1, ''])) {
                    $option['value'] = '';
                }
                $option_data[] = [
                    'name'  => $option['name'],
                    'value' => $option['value'],
                ];
            }

            $this->data['products'][] = [
                'name'             => $product['name'],
                'product_id'       => $product['product_id'],
                'order_product_id' => $product['order_product_id'],
                'sku'              => $product['sku'],
                'model'            => $product['model'],
                'option'           => $option_data,
                'quantity'         => $product['quantity'],
                'price'            => $this->currency->format(
                    $product['price'],
                    $orderInfo['currency'],
                    $orderInfo['value']
                ),
                'total'            => $this->currency->format_total(
                    $product['price'],
                    $product['quantity'],
                    $orderInfo['currency'],
                    $orderInfo['value']
                ),
            ];
        }
        $this->data['mail_template_data']['products'] = $this->data['products'];
        $this->data['mail_template_data']['totals'] = $order_total_query->rows;

        $this->data['mail_template'] = 'mail/order_confirm.tpl';

        //allow to change email data from extensions
        $this->extensions->hk_ProcessData($this, 'sf_order_confirm_mail');

        $view = new AView($this->registry, 0);
        $view->batchAssign($this->data['mail_template_data']);

        //text email
        $this->data['mail_template'] = 'mail/order_confirm_text.tpl';

        //allow to change email data from extensions
        $this->extensions->hk_ProcessData($this, 'sf_order_confirm_mail_text');

        $this->data['sender'] = $this->config->get('store_main_email');

        $attachments = [];
        if (is_file(DIR_RESOURCE . $mailLogo)) {
            $attachments[] = [
                'file' => DIR_RESOURCE . $mailLogo,
                'name' => md5(pathinfo($mailLogo, PATHINFO_FILENAME))
                    . '.'
                    . pathinfo($mailLogo, PATHINFO_EXTENSION)
            ];
        }
        $this->data['attachments'] = array_merge($attachments, (array)$this->data['mail_attachments']);
        $this->sendEmail($orderInfo['email']);

        //send alert email for merchant
        if ($this->config->get('config_alert_mail')) {
            // HTML
            $this->data['mail_template_data']['text_greeting'] = $language->get('text_received') . "\n\n";
            $this->data['mail_template_data']['invoice'] = '';
            $this->data['mail_template_data']['text_invoice'] = '';
            $this->data['mail_template_data']['text_footer'] = '';
            $this->data['mail_template_data']['order_url'] = $this->html->getSecureURL(
                'sale/order/details',
                '&order_id=' . $orderId
            );
            $this->data['mail_template'] = 'mail/order_confirm.tpl';

            //allow to change email data from extensions
            $this->extensions->hk_ProcessData($this, 'sf_order_confirm_alert_mail');

            //text email
            //allow to change email data from extensions
            $this->data['mail_template'] = 'mail/order_confirm_text.tpl';
            $this->extensions->hk_ProcessData($this, 'sf_order_confirm_alert_mail_text');

            $this->sendEmail($this->config->get('store_main_email'), true);

            // Send to additional alert emails
            $emails = array_unique(
                array_map(
                    'trim',
                    explode(',', $this->config->get('config_alert_emails'))
                )
            );
            $emails = array_filter($emails, function ($email) {
                return preg_match(EMAIL_REGEX_PATTERN, $email);
            });
            foreach ($emails as $email) {
                $this->sendEmail($email, true);
            }
        }

        $msg_text = sprintf($language->get('text_new_order_text'), $orderInfo['firstname'] . ' ' . $orderInfo['lastname']);
        $msg_text .= "<br/><br/>";
        foreach ($this->data['mail_template_data']['totals'] as $total) {
            $msg_text .= $total['title'] . ' - ' . $total['text'] . "<br/>";
        }
        $msg = new AMessage();
        $msg->saveNotice($language->get('text_new_order') . $orderId, $msg_text);

        $language = new ALanguage($this->registry);
        $language->load($language->language_details['directory']);
        $language->load('common/im');
        $message_arr = [
            1 => [
                'message' => sprintf($language->get('im_new_order_text_to_admin'), $orderId),
            ],
        ];

        $this->im->send(
            'new_order',
            $message_arr,
            'storefront_order_confirm_admin_notify',
            $this->data['mail_template_data'],
            $this->data['attachments']
        );

        return true;
    }

    /**
     * @param string $to
     * @param bool|null $alertEmail
     * @return void
     * @throws AException
     * @throws TransportExceptionInterface
     */
    protected function sendEmail(string $to, ?bool $alertEmail = false)
    {
        $mail = new AMail($this->config);
        $mail->setTo($to);
        $mail->setFrom($this->config->get('store_main_email'));
        $mail->setReplyTo($this->config->get('store_main_email'));
        $mail->setSender($this->data['sender']);
        $defaultTpl = $alertEmail ? 'storefront_order_confirm_admin_notify' : 'storefront_order_confirm';
        $mail->setTemplate(
            $this->data['email_template_text_id'] ?: $defaultTpl,
            $this->data['mail_template_data']
        );
        foreach ($this->data['attachments'] as $attachment) {
            $mail->addAttachment(
                $attachment['file'],
                $attachment['name']
            );
        }
        //silent sending
        $mail->send(true);
    }

    /**
     * @param int $order_id
     * @param int $order_status_id
     * @param string $comment
     * @param bool|false $notify
     *
     */
    public function update($order_id, $order_status_id, $comment = '', $notify = false)
    {
        $this->extensions->hk_update($this, $order_id, $order_status_id, $comment, $notify);
    }

    /**
     * @param int $order_id
     * @param int $order_status_id
     * @param string $comment
     * @param bool $notify
     *
     * @throws AException
     */
    public function _update($order_id, $order_status_id, $comment = '', $notify = false)
    {
        $order_query = $this->db->query(
            "SELECT *
            FROM `" . $this->db->table("orders") . "` o
            LEFT JOIN " . $this->db->table("languages") . " l 
                ON (o.language_id = l.language_id)
            WHERE o.order_id = '" . (int)$order_id . "' 
                AND o.order_status_id > '0'"
        );

        if ($order_query->num_rows) {
            $order_row = $this->dcrypt->decrypt_data($order_query->row, 'orders');

            $this->db->query(
                "UPDATE `" . $this->db->table("orders") . "`
                SET order_status_id = '" . (int)$order_status_id . "',
                    date_modified = NOW()
                WHERE order_id = '" . (int)$order_id . "'"
            );

            $this->db->query(
                "INSERT INTO " . $this->db->table("order_history") . "
                SET order_id = '" . (int)$order_id . "',
                    order_status_id = '" . (int)$order_status_id . "',
                    notify = '" . (int)$notify . "',
                    comment = '" . $this->db->escape($comment) . "',
                    date_added = NOW()"
            );

            //send notifications
            $language = new ALanguage($this->registry, $order_row['code']);
            $language->load($order_row['filename']);
            $language->load('mail/order_update');

            $order_status_query = $this->db->query(
                "SELECT *
                FROM " . $this->db->table("order_statuses") . "
                WHERE order_status_id = '" . (int)$order_status_id . "'
                    AND language_id = '" . (int)$order_row['language_id'] . "'"
            );

            $language_im = new ALanguage($this->registry);
            $language->load($language->language_details['directory']);
            $language_im->load('common/im');
            $status_name = '';
            if ($order_status_query->row['name']) {
                $status_name = $order_status_query->row['name'];
            }

            $invoiceUrl = '';
            if (!$order_row['customer_id'] && $this->config->get('config_guest_checkout') && $order_row['email']) {
                $order_token = generateOrderToken($order_id, $order_row['email']);
                if ($order_token) {
                    $invoiceUrl = $order_row['store_url'] . 'index.php?rt=account/order_details&ot=' . $order_token;
                }
            }

            $data = [
                'store_name'       => $order_row['store_name'],
                'order_id'         => $order_id,
                'order_date_added' => dateISO2Display($order_row['date_added'], $language->get('date_format_short')),
                'order_status'     => $order_status_query->num_rows ? $order_status_query->row['name'] : '',
                'invoice'          => $order_row['customer_id']
                    ? $order_row['store_url'] . 'index.php?rt=account/order_details&order_id=' . $order_id
                    : $invoiceUrl,
                'comment'          => $comment ?: '',
            ];

            $message_arr = [
                0 => [
                    'message' => sprintf(
                        $language_im->get('im_order_update_text_to_customer'),
                        $order_id,
                        $status_name
                    ),
                ],
                1 => [
                    'message' => sprintf(
                        $language_im->get('im_order_update_text_to_admin'),
                        $order_id,
                        $status_name
                    ),
                ],
            ];
            $this->im->send('order_update', $message_arr, 'admin_order_status_notify', $data);

            //notify via email
            if ($notify) {
                $mail = new AMail($this->config);
                $mail->setTo($order_row['email']);
                $mail->setFrom($this->config->get('store_main_email'));
                $mail->setReplyTo($this->config->get('store_main_email'));
                $mail->setSender($order_row['store_name']);
                $mail->setTemplate('admin_order_status_notify', $data);
                $mail->send(true);
            }
        }
    }

    /**
     * @param int $order_id
     * @param int $order_status_id
     * @param string $comment
     *
     * @return null
     * @throws AException
     */
    public function addHistory($order_id, $order_status_id, $comment)
    {
        $this->db->query(
            "INSERT INTO " . $this->db->table('order_history') . " 
            SET order_id = '" . (int)$order_id . "', 
                order_status_id = '" . (int)$order_status_id . "', 
                notify = '0', 
                comment = '" . $this->db->escape($comment) . "', 
                date_added = NOW()"
        );
        return null;
    }

    /**
     * @param int $order_id
     * @param string|array $data
     *
     * @return bool|stdClass
     * @throws AException
     */
    public function updatePaymentMethodData($order_id, $data)
    {
        if (is_array($data)) {
            $data = serialize($data);
        }

        return $this->db->query(
            'UPDATE ' . $this->db->table('orders') . '
            SET payment_method_data = "' . $this->db->escape($data) . '"
            WHERE order_id = "' . (int)$order_id . '"'
        );
    }

    /**
     * @param $order_id
     *
     * @return bool
     * @throws AException
     */
    protected function _remove_order($order_id)
    {
        $order_id = (int)$order_id;
        if (!$order_id) {
            return false;
        }

        $this->db->query("DELETE FROM `" . $this->db->table("order_products") . "` WHERE order_id = '" . $order_id . "'");
        $this->db->query("DELETE FROM `" . $this->db->table("order_options") . "` WHERE order_id = '" . $order_id . "'");
        $this->db->query("DELETE FROM `" . $this->db->table("order_downloads") . "` WHERE order_id = '" . $order_id . "'");
        $this->db->query("DELETE FROM `" . $this->db->table("order_totals") . "` WHERE order_id = '" . $order_id . "'");
        $this->db->query("DELETE FROM `" . $this->db->table("order_data") . "` WHERE order_id = '" . $order_id . "'");
        $this->db->query("DELETE FROM `" . $this->db->table("orders") . "` WHERE order_id = '" . $order_id . "'");

        return true;
    }

    public function saveOrderProductStocks($order_product_id, $product_id, $product_option_value_id, $order_quantity)
    {
        if (!$order_quantity) {
            return false;
        }
        $stock_locations = $this->getProductStockLocations($product_id, $product_option_value_id);

        if (!$stock_locations) {
            return false;
        }
        $remains = $order_quantity;
        $available_quantity = array_sum(array_column($stock_locations, 'quantity'));

        //do not save when zero stock on all locations
        if (!$available_quantity) {
            return false;
        }

        foreach ($stock_locations as $row) {
            //skip zero stock locations or non-trackable
            if (
                ($available_quantity && !$row['quantity'])
                || (!$product_option_value_id && !$row['product_subtract'])
                || ($product_option_value_id && !$row['product_option_value_subtract'])
            ) {
                continue;
            }

            if ($row['quantity'] >= $remains) {
                $newQnty = $row['quantity'] - $remains;
                $quantity = $remains;
                $remains = 0;
            } else {
                $newQnty = 0;
                $quantity = $row['quantity'];
                $remains -= $row['quantity'];
            }
            //update stocks
            $sql = "UPDATE " . $this->db->table("product_stock_locations") . " 
                    SET quantity = " . (int)$newQnty . "
                    WHERE location_id= " . (int)$row['location_id'] . "
                        AND product_id = " . (int)$product_id
                . ((int)$product_option_value_id
                    ? " AND product_option_value_id='" . (int)$product_option_value_id . "' "
                    : " AND product_option_value_id IS NULL");
            $this->db->query($sql);
            //save stocks into order details
            $this->db->query(
                "INSERT INTO " . $this->db->table("order_product_stock_locations") . "
                    (order_product_id, product_id, product_option_value_id, location_id, location_name, quantity, sort_order)
                VALUES( 
                    " . (int)$order_product_id . ",
                    " . (int)$product_id . ", 
                    " . ((int)$product_option_value_id ?: 'NULL') . ", 
                    " . (int)$row['location_id'] . ", 
                    '" . $this->db->escape($row['location_name']) . "',
                    " . (int)$quantity . ", 
                    " . (int)$row['sort_order'] . "
                );"
            );

            if (!$remains) {
                break;
            }
        }
        return true;
    }

    /**
     * @param int $product_id
     *
     * @param int $product_option_value_id
     *
     * @return array
     * @throws AException
     */
    public function getProductStockLocations($product_id, $product_option_value_id = 0)
    {
        $sql = "SELECT psl.*,
                CONCAT(l.name,' ', l.description) as location_name, 
                p.subtract as product_subtract";

        if ($product_option_value_id) {
            $sql .= ", pov.subtract as product_option_value_subtract";
        }
        $sql .= " FROM " . $this->db->table('product_stock_locations') . " psl
                  LEFT JOIN " . $this->db->table('products') . " p
                     ON p.product_id = psl.product_id ";

        if ($product_option_value_id) {
            $sql .= " LEFT JOIN " . $this->db->table('product_option_values') . " pov
                          ON pov.product_option_value_id = psl.product_option_value_id";
        }
        $sql .= " LEFT JOIN " . $this->db->table('locations') . " l
                    ON l.location_id = psl.location_id
                  WHERE psl.product_id=" . (int)$product_id;
        if ($product_option_value_id) {
            $sql .= " AND psl.product_option_value_id = " . (int)$product_option_value_id;
        } else {
            $sql .= " AND psl.product_option_value_id IS NULL";
        }
        $sql .= " ORDER BY psl.sort_order ASC";

        $result = $this->db->query($sql);
        return $result->rows;
    }

    /**
     * @param int $customerId
     * @param int $productId
     *
     * @return bool
     * @throws AException
     */
    public function productIsPurchasedByCustomer($customerId, $productId)
    {
        if (!(int)$customerId || !(int)$productId) {
            return false;
        }
        $orderProductsTable = $this->db->table('order_products');
        $ordersTable = $this->db->table('orders');

        $sql = 'SELECT product_id 
                FROM ' . $orderProductsTable .
            ' INNER JOIN ' . $ordersTable . ' 
                    ON ' . $ordersTable . '.order_id=' . $orderProductsTable . '.order_id 
                        AND ' . $ordersTable . '.customer_id=' . $customerId . ' 
                        AND ' . $ordersTable . '.order_status_id>0 
                        AND ' . $ordersTable . '.store_id=' . $this->config->get('config_store_id') .
            ' WHERE ' . $orderProductsTable . '.product_id=' . $productId . ' 
                 LIMIT 1';
        $result = $this->db->query($sql);
        if ($result->num_rows > 0) {
            return true;
        }
        return false;
    }

    /**
     * @param int $customerId
     * @param array $productIds
     *
     * @return array
     * @throws AException
     */
    public function getProductsPurchasedByCustomer($customerId, $productIds)
    {
        if (!(int)$customerId || !is_array($productIds) || empty($productIds)) {
            return [];
        }

        $orderProductsTable = $this->db->table('order_products');
        $ordersTable = $this->db->table('orders');

        $sql = 'SELECT product_id 
                FROM ' . $orderProductsTable .
            ' INNER JOIN ' . $ordersTable . ' 
                    ON ' . $ordersTable . '.order_id=' . $orderProductsTable . '.order_id 
                        AND ' . $ordersTable . '.customer_id=' . $customerId . ' 
                        AND ' . $ordersTable . '.order_status_id>0
                        AND ' . $ordersTable . '.store_id=' . $this->config->get('config_store_id') .
            ' WHERE ' . $orderProductsTable . '.product_id IN (' . implode(',', $productIds) . ')';
        $result = $this->db->query($sql);
        if ($result->num_rows > 0) {
            $arProducts = [];
            foreach ($result->rows as $row) {
                $arProducts[$row['product_id']] = true;
            }
            return $arProducts;
        }
        return [];
    }
}
