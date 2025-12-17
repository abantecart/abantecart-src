<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

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

class ModelCheckoutOrder extends Model
{
    public function __construct($registry)
    {
        parent::__construct($registry);
        //this list can be changed from hook beforeModelModelCheckoutOrder
        $this->data['order_column_list'] = [
            'store_id'                => 'int',
            'store_name'              => 'string',
            'store_url'               => 'string',
            'customer_id'             => 'int',
            'customer_group_id'       => 'int',
            'firstname'               => 'string',
            'lastname'                => 'string',
            'email'                   => 'string',
            'telephone'               => 'string',
            'fax'                     => 'string',
            'total'                   => 'float',
            'language_id'             => 'int',
            'currency'                => 'string',
            'currency_id'             => 'int',
            'value'                   => 'float',
            'coupon_id'               => 'int',
            'ip'                      => 'string',
            'shipping_firstname'      => 'string',
            'shipping_lastname'       => 'string',
            'shipping_company'        => 'string',
            'shipping_address_1'      => 'string',
            'shipping_address_2'      => 'string',
            'shipping_city'           => 'string',
            'shipping_postcode'       => 'string',
            'shipping_zone'           => 'string',
            'shipping_zone_id'        => 'int',
            'shipping_country'        => 'string',
            'shipping_country_id'     => 'int',
            'shipping_address_format' => 'string',
            'shipping_method'         => 'string',
            'shipping_method_key'     => 'string',
            'payment_firstname'       => 'string',
            'payment_lastname'        => 'string',
            'payment_company'         => 'string',
            'payment_address_1'       => 'string',
            'payment_address_2'       => 'string',
            'payment_city'            => 'string',
            'payment_postcode'        => 'string',
            'payment_zone'            => 'string',
            'payment_zone_id'         => 'int',
            'payment_country'         => 'string',
            'payment_country_id'      => 'int',
            'payment_address_format'  => 'string',
            'payment_method'          => 'string',
            'payment_method_key'      => 'string',
            'comment'                 => 'string',
        ];
        $this->data['order_product_column_list'] = [
            'product_id'       => 'int',
            'name'             => 'string',
            'model'            => 'string',
            'sku'              => 'string',
            'price'            => 'float',
            'cost'             => 'float',
            'total'            => 'float',
            'tax'              => 'float',
            'quantity'         => 'int',
            'subtract'         => 'int',
            'weight'           => 'float',
            'weight_iso_code'  => 'string',
            'width'            => 'float',
            'height'           => 'float',
            'length'           => 'float',
            'length_iso_code'  => 'string',
        ];
    }

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
        return null;
    }

    /**
     * @param array $data
     * @param int|string $setOrderId
     *
     * @return bool|int
     * @throws AException
     */
    public function _create($data, $setOrderId = 0)
    {
        $setOrderId = (int)$setOrderId;
        //reuse the same order_id or unused one order_status_id = 0
        if ($setOrderId) {
            $query = $this->db->query(
                "SELECT order_id
                FROM `" . $this->db->table("orders") . "`
                WHERE order_id = " . $setOrderId . " 
                    AND order_status_id = '0'"
            );

            if (!$query->num_rows) { // for already processed orders do redirect
                $query = $this->db->query(
                    "SELECT order_id
                    FROM `" . $this->db->table("orders") . "`
                    WHERE order_id = " . $setOrderId . " 
                        AND order_status_id > 0"
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
                ) . "' AND order_status_id = 0"
            );
            foreach ($query->rows as $result) {
                $this->_remove_order($result['order_id']);
            }
        }

        if (!$setOrderId && (int)$this->config->get('config_start_order_id')) {
            $query = $this->db->query(
                "SELECT MAX(order_id) AS order_id
                FROM `" . $this->db->table("orders") . "`"
            );
            if ($query->row['order_id'] && $query->row['order_id'] >= $this->config->get('config_start_order_id')) {
                $setOrderId = (int)$query->row['order_id'] + 1;
            } elseif ($this->config->get('config_start_order_id')) {
                $setOrderId = (int)$this->config->get('config_start_order_id');
            } else {
                $setOrderId = 0;
            }
        }

        $insertArr = [];
        if ($setOrderId) {
            $insertArr['order_id'] = (int)$setOrderId;
        }

        //remove to exclude these fields from "ext_fields" data
        unset(
            $data['shipping_format'],
            $data['payment_format'],
        );

        foreach ($this->data['order_column_list'] as $key => $dataType) {

            if (!isset($data[$key])) {
                continue;
            }
            if ($dataType == 'int') {
                $value = (int)$data[$key];
            } elseif ($dataType == 'float') {
                $value = (float)$data[$key];
            } elseif ($dataType == 'string') {
                $value = $this->db->escape($data[$key]);
            } else {
                $value = $this->db->escape(serialize($data[$key]));
            }
            $insertArr[$key] = $value;
        }

        //prepare extended fields values
        $extFields = [];
        $colNames = array_keys($this->data['order_column_list']);

        foreach ($data as $key => $value) {
            if (in_array($key, ['shipping', 'products', 'totals'])) {
                continue;
            }
            $kSet = $this->getOrderColumnNameVariants($key);
            if (array_intersect($kSet, $colNames)) {
                continue;
            }
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    $kSet = $this->getOrderColumnNameVariants($subKey);
                    if (array_intersect($kSet, $colNames)) {
                        continue;
                    }
                    $extFields[$key][$subKey] = $subValue;
                }
            } else {
                //prevent duplicates in the ext_fields data
                foreach (['payment_', 'shipping_'] as $t) {
                    $testKey = str_replace($t, '', $key);
                    if (//if data goes to the order table
                        isset($this->data['order_column_list'][$testKey])
                        && isset($data[$testKey])
                        //and values are equal
                        && $data[$testKey] == $value
                    ) {
                        continue 2;
                    }
                }
                $extFields[$key] = $value;
            }
        }

        if ($extFields) {
            $insertArr['ext_fields'] = $this->db->escape(js_encode($extFields));
        }

        if ($this->dcrypt->active) {
            $insertArr = $this->dcrypt->encrypt_data($insertArr, 'orders');
            $insertArr['key_id'] = (int)$data['key_id'];
        }
        $insertData = [];
        foreach ($insertArr as $k => $v) {
            $insertData[$k] = "`" . $k . "` = '" . $v . "'";
        }

        $this->db->query("INSERT INTO `" . $this->db->table("orders") . "` SET " . implode(', ', $insertData));

        $order_id = $this->db->getLastId();

        foreach ($data['products'] as $product) {
            $insertArr = [
                'order_id' => $order_id
            ];
            foreach ($this->data['order_product_column_list'] as $key => $dataType) {
                if (!isset($product[$key])) {
                    continue;
                }
                if ($dataType == 'int') {
                    $value = (int)$product[$key];
                } elseif ($dataType == 'float') {
                    $value = (float)$product[$key];
                } elseif ($dataType == 'string') {
                    $value = $this->db->escape($product[$key]);
                } else {
                    $value = $this->db->escape(serialize($product[$key]));
                }
                $insertArr[$key] = $value;
            }

            $insertData = [];
            foreach ($insertArr as $k => $v) {
                $insertData[$k] = "`" . $k . "` = '" . $v . "'";
            }
            $this->db->query("INSERT INTO `" . $this->db->table("order_products") . "` SET " . implode(', ', $insertData));
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

    /**
     * @param $orderId
     * @param $data
     * @return void
     * @throws AException
     */
    protected function saveIMOrderData($orderId, $data)
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
        $protocols = array_column($result->rows, 'protocol', 'type_id');

        $savedProtocols = [];
        foreach ($protocols as $type_id => $protocol) {
            //prevent duplicates
            if (in_array($protocol, $savedProtocols)) {
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
                        WHERE order_id = " . (int)$orderId . " 
                            AND type_id = " . $type_id;
                $r = $this->db->query($sql);
                if (!$r->num_rows) {
                    $sql = "INSERT INTO " . $this->db->table('order_data') . "
                        (`order_id`, `type_id`, `data`, `date_added`)
                        VALUES 
                        (" . (int)$orderId . ", " . (int)$type_id . ", '" . $this->db->escape($im_data) . "', NOW() )";
                } else {
                    $sql = "UPDATE " . $this->db->table('order_data') . "
                            SET `data` = '" . $this->db->escape($im_data) . "'
                            WHERE order_id = " . (int)$orderId . " AND type_id = " . (int)$type_id;
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
                comment = '" . $this->db->escape($comment) . "'"
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

        //update the product's inventory
        foreach ($order_product_query->rows as $product) {
            $orderOptionResult = $this->db->query(
                "SELECT op.*, pov.subtract
                FROM " . $this->db->table("order_options") . " op
                LEFT JOIN " . $this->db->table("product_option_values") . " pov
                    ON pov.product_option_value_id = op.product_option_value_id
                WHERE op.order_id = '" . (int)$orderId . "'
                   AND op.order_product_id = '" . (int)$product['order_product_id'] . "'"
            );
            //update options stock
            $stock_updated = false;
            foreach ($orderOptionResult->rows as $option) {
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
                    //notify admin about out of stock for option-based product
                    $message_arr = [
                        1 => [
                            'message' => $language->getAndReplace(
                                'im_product_out_of_stock_admin_text',
                                replaces: $product['product_id']
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
                            'message' => $language->getAndReplace(
                                'im_product_out_of_stock_admin_text',
                                replaces: $product['product_id']
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
        $mailTplData = &$this->data['mail_template_data'];

        $mailTplData['title'] = $language->getAndReplace(
            key: 'text_subject',
            replaces: [
                html_entity_decode($orderInfo['store_name']),
                $orderId,
            ]
        );
        $mailTplData['text_greeting'] = $language->getAndReplace(
            key: 'text_greeting',
            replaces: html_entity_decode($orderInfo['store_name'])
        );

        $langKeys = [
            'text_order_detail',
            'text_order_id',
            'text_invoice',
            'text_date_added',
            'text_telephone',
            'text_mobile_phone',
            'text_email',
            'text_ip',
            'text_fax',
            'text_shipping_address',
            'text_payment_address',
            'text_shipping_method',
            'text_payment_method',
            'text_comment',
            'text_powered_by',
            'text_total',
            'text_footer',
            'column_product',
            'column_model',
            'column_quantity',
            'column_price',
            'column_total',
        ];
        foreach ($langKeys as $k) {
            $mailTplData[$k] = $language->get($k);
        }

        $mailTplData['text_project_label'] = $language->get('text_powered_by') . ' ' . project_base();

        // add data from order
        $mailTplData += [
            'order_id'           => $orderId,
            'customer_id'        => $orderInfo['customer_id'],
            'date_added'         => dateISO2Display($orderInfo['date_added'], $language->get('date_format_short')),
            'store_name'         => $orderInfo['store_name'],
            'address'            => nl2br($this->config->get('config_address')),
            'telephone'          => $this->config->get('config_telephone'),
            'fax'                => $this->config->get('config_fax'),
            'email'              => $this->config->get('store_main_email'),
            'store_url'          => $orderInfo['store_url'],
            'firstname'          => $orderInfo['firstname'],
            'lastname'           => $orderInfo['lastname'],
            'shipping_method'    => $orderInfo['shipping_method'],
            'payment_method'     => $orderInfo['payment_method'],
            'customer_email'     => $orderInfo['email'],
            'customer_telephone' => $orderInfo['telephone'],
            'customer_fax'       => $orderInfo['fax'],
            'customer_ip'        => $orderInfo['ip'],
            'comment'            => trim(nl2br(html_entity_decode($orderInfo['comment'], ENT_QUOTES, 'UTF-8'))),
        ];

        $mailTplData['customer_mobile_phone'] = $this->im->getCustomerURI('sms', (int)$orderInfo['customer_id'], $orderId);

        $mailLogo = $this->config->get('config_mail_logo_' . $languageId)
            ?: $this->config->get('config_mail_logo');
        $mailLogo = $mailLogo ?: $this->config->get('config_logo_' . $languageId);
        $mailLogo = $mailLogo ?: $this->config->get('config_logo');

        if ($mailLogo) {
            $result = getMailLogoDetails($mailLogo);
            $mailTplData['logo_uri'] = $result['uri'];
            $mailTplData['logo_html'] = $result['html'];
        }

        if ($this->config->get('config_guest_checkout') && $orderInfo['email']) {
            $order_token = generateOrderToken($orderId, $orderInfo['email']);
            $mailTplData['invoice'] = $orderInfo['store_url']
                . 'index.php?rt=account/order_details&ot=' . $order_token . "\n\n";
        } elseif ($orderInfo['customer_id']) {
            $mailTplData['invoice'] = $orderInfo['store_url']
                . 'index.php?rt=account/order_details&order_id=' . $orderId;
        }

        //override with the data from the before hooks
        if ($this->data) {
            $mailTplData = array_merge($mailTplData, $this->data);
        }

        /** @var ModelLocalisationZone $zMdl */
        $zMdl = $this->load->model('localisation/zone');
        $zoneInfo = $zMdl->getZone($orderInfo['shipping_zone_id']);
        $zoneCode = (string)$zoneInfo['code'];

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

        $mailTplData['shipping_data'] = $shipping_data;
        $mailTplData['shipping_address'] = $this->customer->getFormattedAddress(
            $shipping_data,
            $orderInfo['shipping_format']
        );
        $zoneInfo = $zMdl->getZone($orderInfo['payment_zone_id']);
        $zoneCode = (string)$zoneInfo['code'];

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

        $mailTplData['payment_data'] = $payment_data;
        $mailTplData['payment_address'] = $this->customer->getFormattedAddress(
            $payment_data,
            $orderInfo['payment_format']
        );

        if (!has_value($this->data['products'])) {
            $this->data['products'] = [];
        }

        $orderProductIds = array_column($order_product_query->rows, 'product_id');
        $resource = new AResource('image');
        $thumbnails = $resource->getMainThumbList(
            'products',
            $orderProductIds,
            $this->config->get('config_image_cart_width'),
            $this->config->get('config_image_cart_height')
        );

        foreach ($order_product_query->rows as $product) {
            $option_data = [];

            $orderOptionResult = $this->db->query(
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

            foreach ($orderOptionResult->rows as $option) {
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

            $imgFile = str_replace(AUTO_SERVER, DIR_ROOT . DS, $thumbnails[(int)$product['product_id']]['thumb_url']);
            $thumbnailUrl = is_file($imgFile)
                ? 'data:' . mime_content_type($imgFile) . ';base64,' . base64_encode(file_get_contents($imgFile))
                : '';

            $this->data['products'][] = array_merge(
                $product,
                [
                    'thumbnail_url' => $thumbnailUrl,
                    'option'        => $option_data,
                    'price'         => $this->currency->format(
                        $product['price'],
                        $orderInfo['currency'],
                        $orderInfo['value']
                    ),
                    'total'         => $this->currency->format_total(
                        $product['price'],
                        $product['quantity'],
                        $orderInfo['currency'],
                        $orderInfo['value']
                    ),
                ]
            );
        }
        $mailTplData['products'] = $this->data['products'];
        $mailTplData['totals'] = $order_total_query->rows;

        $this->data['mail_template'] = 'mail/order_confirm.tpl';

        //allow changing email data from extensions
        $this->extensions->hk_ProcessData($this, 'sf_order_confirm_mail');

        $view = new AView($this->registry, 0);
        $view->batchAssign($mailTplData);

        //text email
        $this->data['mail_template'] = 'mail/order_confirm_text.tpl';

        //allow changing email data from extensions
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
            $mailTplData['text_greeting'] = $language->get('text_received') . "\n\n";
            $mailTplData['invoice'] = '';
            $mailTplData['text_invoice'] = '';
            $mailTplData['text_footer'] = '';
            $mailTplData['order_url'] = $this->html->getSecureURL(
                'sale/order/details',
                '&order_id=' . $orderId
            );
            $this->data['mail_template'] = 'mail/order_confirm.tpl';

            //allow changing email data from extensions
            $this->extensions->hk_ProcessData($this, 'sf_order_confirm_alert_mail');

            //textual e-mail
            //allow changing email data from extensions
            $this->data['mail_template'] = 'mail/order_confirm_text.tpl';
            $this->extensions->hk_ProcessData($this, 'sf_order_confirm_alert_mail_text');

            $this->sendEmail($this->config->get('store_main_email'), true);

            // Send additional alert emails
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

        $msg_text = $language->getAndReplace(
            'text_new_order_text',
            replaces: $orderInfo['firstname'] . ' ' . $orderInfo['lastname']
        );

        $msg_text .= "<br/><br/>";
        foreach ($mailTplData['totals'] as $total) {
            $msg_text .= $total['title'] . ' - ' . $total['text'] . "<br/>";
        }
        $msg = new AMessage();
        $msg->saveNotice($language->get('text_new_order') . $orderId, $msg_text);

        $language = new ALanguage($this->registry);
        $language->load($language->language_details['directory']);
        $language->load('common/im');
        $message_arr = [
            1 => [
                'message' => $language->getAndReplace('im_new_order_text_to_admin', replaces: $orderId),
            ],
        ];

        $this->im->send(
            'new_order',
            $message_arr,
            'storefront_order_confirm_admin_notify',
            $mailTplData,
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
     * @throws AException|TransportExceptionInterface
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
                    'message' => $language_im->getAndReplace(
                        'im_order_update_text_to_customer',
                        replaces: [$order_id, $status_name]
                    ),
                ],
                1 => [
                    'message' => $language_im->getAndReplace(
                        'im_order_update_text_to_admin',
                        replaces: [$order_id, $status_name]
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
     * @param int $orderId
     * @param string|array $data - array or serialized string
     *
     * @return bool|stdClass
     * @throws AException
     */
    public function updatePaymentMethodData($orderId, $data)
    {
        $sql = "SELECT payment_method_data
                FROM " . $this->db->table('orders') . "
                WHERE order_id = " . (int)$orderId;
        $result = $this->db->query($sql);
        $priorData = unserialize($result->row['payment_method_data']) ?: [];
        if (is_array($data)) {
            $data = $priorData + $data;
        } else {
            $data = $priorData + (unserialize($data) ?: []);
        }
        return $this->db->query(
            "UPDATE " . $this->db->table('orders') . "
            SET payment_method_data = '" . $this->db->escape(serialize($data)) . "'
            WHERE order_id = " . (int)$orderId
        );
    }

    /**
     * @param $orderId
     *
     * @return bool
     * @throws AException
     */
    protected function _remove_order($orderId)
    {
        $orderId = (int)$orderId;
        if (!$orderId) {
            return false;
        }

        $this->db->query("DELETE FROM `" . $this->db->table("order_products") . "` WHERE order_id = '" . $orderId . "'");
        $this->db->query("DELETE FROM `" . $this->db->table("order_options") . "` WHERE order_id = '" . $orderId . "'");
        $this->db->query("DELETE FROM `" . $this->db->table("order_downloads") . "` WHERE order_id = '" . $orderId . "'");
        $this->db->query("DELETE FROM `" . $this->db->table("order_totals") . "` WHERE order_id = '" . $orderId . "'");
        $this->db->query("DELETE FROM `" . $this->db->table("order_data") . "` WHERE order_id = '" . $orderId . "'");
        $this->db->query("DELETE FROM `" . $this->db->table("orders") . "` WHERE order_id = '" . $orderId . "'");

        return true;
    }

    public function saveOrderProductStocks($orderProductId, $productId, $optionValueId, $orderQuantity)
    {
        if (!$orderQuantity) {
            return false;
        }
        $stock_locations = $this->getProductStockLocations($productId, $optionValueId);

        if (!$stock_locations) {
            return false;
        }
        $remains = $orderQuantity;
        $available_quantity = array_sum(array_column($stock_locations, 'quantity'));

        //do not save when zero stocks on all locations
        if (!$available_quantity) {
            return false;
        }

        foreach ($stock_locations as $row) {
            //skip zero stock locations or non-trackable
            if (
                ($available_quantity && !$row['quantity'])
                || (!$optionValueId && !$row['product_subtract'])
                || ($optionValueId && !$row['product_option_value_subtract'])
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
                        AND product_id = " . (int)$productId
                . ((int)$optionValueId
                    ? " AND product_option_value_id='" . (int)$optionValueId . "' "
                    : " AND product_option_value_id IS NULL");
            $this->db->query($sql);
            //save stocks into order details
            $this->db->query(
                "INSERT INTO " . $this->db->table("order_product_stock_locations") . "
                    (order_product_id, product_id, product_option_value_id, location_id, location_name, quantity, sort_order)
                VALUES( 
                    " . (int)$orderProductId . ",
                    " . (int)$productId . ", 
                    " . ((int)$optionValueId ?: 'NULL') . ", 
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

    /**
     * @param string $key
     * @return array
     */
    protected function getOrderColumnNameVariants(string $key)
    {
        $pPrefix = 'payment_';
        $sPrefix = 'shipping_';
        return [
            $key,
            $sPrefix . $key,
            ltrim($key, $sPrefix),
            $pPrefix . $key,
            ltrim($key, $pPrefix)
        ];
    }

    /**
     * @param int $order_id
     * @param array $data
     *
     * @return bool
     * @throws AException
     */
    public function updateOrderDetails($order_id, $data = [])
    {
        $order_id = (int)$order_id;
        if (!$order_id) {
            return false;
        }

        $allowed = array_merge( ['telephone','comment'], (array)$this->data['allowed_order_details_columns']) ;
        $inArr = $upd = [];
        foreach ($allowed as $field_name) {
            if (isset($data[$field_name])) {
                $inArr[$field_name] = $data[$field_name];
            }
        }
        if(!$inArr){
            return false;
        }

        if ($this->dcrypt->active) {
            $inArr = $this->dcrypt->encrypt_data($inArr, 'orders');
        }

        foreach($inArr as $field_name => $value) {
            $upd[] = "`" . $field_name . "` = '" . $this->db->escape($value) . "' ";
        }

        $sql = "UPDATE " . $this->db->table('orders') . "
                SET " . implode(', ', $upd) . "
                WHERE order_id = " . $order_id . " AND order_status_id = 0";
        $this->db->query($sql);
        return true;
    }
}
