<?php
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

/** @noinspection PhpUndefinedClassInspection */

use PaypalServerSdkLib\Http\ApiResponse;
use PaypalServerSdkLib\Models\Order;

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * Class ModelExtensionPaypalCommerce
 *
 * @property ModelExtensionPaypalCommerce $model_extension_paypal_commerce
 * @property ModelCheckoutOrder $model_checkout_order
 * @property ModelCatalogProduct $model_catalog_product
 */
class ModelExtensionPaypalCommerce extends Model
{
    protected $paypal;

    /**
     * @param Registry $registry
     *
     * @throws AException
     */
    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->paypal = getPaypalClient(
            (string)$this->config->get('paypal_commerce_client_id'),
            (string)$this->config->get('paypal_commerce_client_secret'),
            (int)$this->config->get('paypal_commerce_test_mode')
        );
    }

    /**
     * Method needs in the hooks filtering of payment methods
     *
     * @return bool
     */
    public function isSubscriptionSupported()
    {
        return false;
    }

    /**
     * @return false|string
     */
    public function getClientToken(): ?string
    {
        try {
            $oauthToken = $this->paypal->getClientCredentialsAuth()->fetchToken();
            $accessToken = (string)$oauthToken->getAccessToken();

            if ($accessToken === '') {
                $this->log->write(__FILE__ . '::' . __METHOD__ . ' Empty access token received from PayPal.');
                return null;
            }

            $isSandbox = (bool)$this->config->get('paypal_commerce_test_mode');
            $baseUrl = $isSandbox ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

            $ch = curl_init($baseUrl . '/v1/identity/generate-token');
            if ($ch === false) {
                $this->log->write(__FILE__ . '::' . __METHOD__ . ' Failed to init cURL.');
                return null;
            }

            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER     => [
                    'Authorization: Bearer ' . $accessToken,
                    'Content-Type: application/json',
                    'Accept: application/json',
                ],
                CURLOPT_POSTFIELDS     => '{}',
                CURLOPT_TIMEOUT        => 30,
            ]);

            $raw = curl_exec($ch);
            $curlErr = curl_error($ch);
            $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($raw === false) {
                $this->log->write(__FILE__ . '::' . __METHOD__ . ' cURL error: ' . $curlErr);
                return null;
            }

            $decoded = json_decode($raw, true);
            if (!is_array($decoded)) {
                $this->log->write(
                    __FILE__ . '::' . __METHOD__ . ' Invalid JSON from PayPal. HTTP ' . $httpCode . '. Raw: ' . $raw
                );
                return null;
            }

            if ($httpCode < 200 || $httpCode >= 300) {
                $this->log->write(
                    __FILE__ . '::' . __METHOD__ . ' PayPal generate-token failed. HTTP ' . $httpCode . '. Response: ' . $raw
                );
                return null;
            }

            $clientToken = (string)($decoded['client_token'] ?? '');
            if ($clientToken === '') {
                $this->log->write(
                    __FILE__ . '::' . __METHOD__ . ' PayPal response has no client_token. Response: ' . $raw
                );
                return null;
            }

            return $clientToken;
        } catch (\Throwable $e) {
            $this->log->write(__FILE__ . '::' . __METHOD__ . ' Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * @param int $customerId
     * @param string $paypalCustomerId
     *
     * @throws AException
     */
    public function savePaypalCustomer($customerId, $paypalCustomerId)
    {
        if (!$customerId || !$paypalCustomerId) {
            return;
        }
        //create stripe customer entry
        $test_mode = $this->config->get('paypal_commerce_test_mode') ? 1 : 0;
        $result = $this->db->query(
            "SELECT *
             FROM " . $this->db->table("paypal_customers") . "
             WHERE  `paypal_test_mode` = '" . (int)$test_mode . "' 
                AND `customer_id` = '" . (int)$customerId . "'"
        );
        $exists = $result->row;
        if ($exists) {
            $this->db->query(
                "UPDATE " . $this->db->table("paypal_customers") . " 
                SET customer_paypal_id = '" . $this->db->escape($paypalCustomerId) . "'
                WHERE paypal_test_mode = '" . (int)$test_mode . "' 
                    AND customer_id = '" . (int)$customerId . "'"
            );
        } else {
            $this->db->query(
                "INSERT INTO `" . $this->db->table("paypal_customers") . "` 
                SET `customer_id` = '" . (int)$customerId . "', 
                    `customer_paypal_id` = '" . $this->db->escape($paypalCustomerId) . "', 
                    `paypal_test_mode` = '" . (int)$test_mode . "', 
                    `date_added` = now()"
            );
        }
    }

    /**
     * @param string $paypalOrderId
     *
     * @return Order|null
     *
     * If API result comes as array, it is mapped to Order.
     */
    public function getOrder(string $paypalOrderId): ?Order
    {
        if ($paypalOrderId === '') {
            return null;
        }

        $apiResponse = $this->paypal
            ->getOrdersController()
            ->getOrder(['id' => $paypalOrderId]);

        return paypalNormalizeOrderResult($apiResponse->getResult());
    }

    /**
     * @param $address
     *
     * @return array
     * @throws AException
     */
    public function getMethod(array $address = [])
    {
        $this->load->language('paypal_commerce/paypal_commerce');
        if ($this->config->get('paypal_commerce_status')) {
            $query = $this->db->query(
                "SELECT * 
                FROM `" . $this->db->table("zones_to_locations") . "` 
                WHERE location_id = '" . (int)$this->config->get('paypal_commerce_location_id') . "' 
                        AND country_id = '" . (int)$address['country_id'] . "' 
                        AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')"
            );

            if (!in_array($this->currency->getCode(), PAYPAL_SUPPORTED_CURRENCIES)) {
                $status = false;
            } elseif (!$this->config->get('paypal_commerce_location_id')) {
                $status = true;
            } elseif ($query->num_rows) {
                $status = true;
            } else {
                $status = false;
            }
        } else {
            $status = false;
        }

        $payment_data = [];
        if ($status) {
            $payment_data = [
                'id'         => 'paypal_commerce',
                'title'      => $this->language->get('text_title', 'paypal_commerce/paypal_commerce'),
                'sort_order' => $this->config->get('paypal_commerce_sort_order'),
            ];
        }
        return $payment_data;
    }

    /**
     * @param array $data
     *
     * @return int
     * @throws AException
     */
    public function addShippingAddress( array $data = [] )
    {
        //encrypt customer data
        $key_sql = '';
        if ($this->dcrypt->active) {
            $data = $this->dcrypt->encrypt_data($data, 'addresses');
            $key_sql = ", key_id = '" . (int)$data['key_id'] . "'";
        }

        if (!has_value($data['country_id'])) {
            $data['country_id'] = $this->getCountryIdByCode2((string)$data['iso_code_2']);
        }

        if (!has_value($data['zone_id'])) {
            $data['zone_id'] = $this->getZoneId( (int)$data['country_id'], (string)$data['zone_code']);
        }

        $this->db->query(
            "INSERT INTO " . $this->db->table("addresses") . "
            SET
                customer_id = '" . (int)$this->customer->getId() . "',
                company = '" . (has_value($data['company']) ? $this->db->escape($data['company']) : '') . "',
                firstname = '" . $this->db->escape($data['firstname']) . "',
                lastname = '" . $this->db->escape($data['lastname']) . "',
                address_1 = '" . $this->db->escape($data['address_1']) . "',
                address_2 = '" . (has_value($data['address_2']) ? $this->db->escape($data['address_2']) : '') . "',
                postcode = '" . $this->db->escape($data['postcode']) . "',
                city = '" . $this->db->escape($data['city']) . "',
                zone_id = '" . (int)$data['zone_id'] . "',
                country_id = '" . (int)$data['country_id'] . "'"
            . $key_sql
        );

        $address_id = (int)$this->db->getLastId();

        if (isset($data['default']) && $data['default'] == '1') {
            $this->db->query(
                "UPDATE " . $this->db->table("customers") . "
                SET address_id = '" . (int)$address_id . "'
                WHERE customer_id = '" . (int)$this->customer->getId() . "'"
            );
        }

        return $address_id;
    }

    /**
     * @param string $code
     *
     * @return false|int
     * @throws AException
     */
    public function getCountryIdByCode2( string $code)
    {
        $result = $this->db->query(
            'SELECT country_id 
             FROM ' . $this->db->table('countries') . '
             WHERE iso_code_2 = "' . strtoupper($this->db->escape($code)) . '"'
        );

        if ($result->num_rows > 0) {
            return (int)$result->row['country_id'];
        }
        return false;
    }

    public function getZoneId(int $country_id, string $zone_code)
    {
        $result = $this->db->query(
            'SELECT zone_id 
            FROM ' . $this->db->table('zones') . '
            WHERE country_id = "' . (int)$country_id . '"
                AND code = "' . strtoupper($this->db->escape($zone_code)) . '"'
        );

        if ($result->num_rows > 0) {
            return $result->row['zone_id'];
        }
        return null;
    }

    //record order with PayPal database
    public function savePaypalOrder($order_id, $data)
    {
        //settings contain order product meta-data such as selected options
        if (isset($data['settings'])) {
            $data['settings'] = !is_string($data['settings'])
                ? serialize($data['settings'])
                : $data['settings'];
        }
        $test_mode = $this->config->get('paypal_commerce_test_mode') ? 1 : 0;
        $this->db->query(
            "INSERT INTO `" . $this->db->table("paypal_orders") . "` 
            SET `order_id` = '" . (int)$order_id . "', 
                `charge_id` = '" . $this->db->escape($data['id']) . "', 
                `charge_id_previous` = '" . $this->db->escape($data['id']) . "', 
                `transaction_id` = '" . $this->db->escape($data['transaction_id']) . "', 
                `paypal_test_mode` = '" . (int)$test_mode . "',
                " . ($data['settings'] ? "`settings` = '" . $this->db->escape($data['settings']) . "'," : '') . " 
                `date_added` = now() "
        );
        return $this->db->getLastId();
    }

    //record order with paypal database
    public function updateOrder($order_id, $data)
    {
        $test_mode = $this->config->get('paypal_commerce_test_mode') ? 1 : 0;
        $fields = [
            'charge_id',
            'charge_id_previous',
            'transaction_id',
            'settings',
        ];
        $upd = [];
        foreach ($fields as $fld) {
            if (isset($data[$fld])) {
                if (!is_string($data[$fld])) {
                    $data[$fld] = serialize($data[$fld]);
                }
                $upd[] = "`" . $fld . "` = '" . $this->db->escape($data[$fld]) . "'";
            }
        }
        if (!$upd) {
            return false;
        }
        $this->db->query(
            "UPDATE `" . $this->db->table("paypal_orders") . "` 
             SET " . implode(", ", $upd) . " 
             WHERE  `order_id` = '" . (int)$order_id . "' AND `paypal_test_mode` = '" . (int)$test_mode . "'"
        );

        return true;
    }

    public function getPaypalOrder($orderId)
    {
        $test_mode = $this->config->get('paypal_commerce_test_mode') ? 1 : 0;
        $result = $this->db->query(
            "SELECT * 
             FROM `" . $this->db->table("paypal_orders") . "` 
             WHERE `order_id` = '" . (int)$orderId . "'
                AND `paypal_test_mode` = '" . (int)$test_mode . "'"
        );
        return $result->row;
    }

    //record order with paypal database
    public function getPaypalOrderByInvoiceId($invoiceId)
    {
        $test_mode = $this->config->get('paypal_commerce_test_mode') ? 1 : 0;
        $result = $this->db->query(
            "SELECT * 
             FROM `" . $this->db->table("paypal_orders") . "` 
             WHERE `transaction_id` = '" . $this->db->escape($invoiceId) . "'
                AND `paypal_test_mode` = '" . (int)$test_mode . "'"
        );
        return $result->row;
    }

    public function updateProductSettings($productId, $settings)
    {
        $productId = (int)$productId;
        if (!is_string($settings)) {
            $settings = serialize($settings);
        }
        $sql = "UPDATE " . $this->db->table('products') . "
                SET settings = '" . $this->db->escape($settings) . "'
                WHERE product_id = '" . $productId . "'";
        $this->db->query($sql);
    }

    /**
     * @param array $settings
     * @param int $product_option_value_id
     *
     * @return array
     */
    protected function convertSubSettings($settings, $product_option_value_id)
    {
        $output = [];
        foreach ($settings as $name => $values) {
            if (is_array($values)) {
                foreach ($values as $pov_id => $value) {
                    if ($pov_id == $product_option_value_id) {
                        $output[$name] = $value;
                    }
                }
            } else {
                $output[$name] = $values;
            }
        }
        return $output;
    }

    /**
     * @param $orderId
     * @param $productId
     *
     * @return array
     * @throws AException
     */
    public function getOrderProduct($orderId, $productId)
    {
        if (!$orderId || !$productId) {
            return [];
        }

        $query = $this->db->query(
            "SELECT *
            FROM " . $this->db->table("order_products") . " op  
            WHERE op.order_id = '" . (int)$orderId . "' 
                AND op.product_id = '" . (int)$productId . "'"
        );

        return $query->row;
    }

    public function getCountryIdByIsoCode2($code)
    {
        $query = $this->db->query(
            "SELECT *
            FROM " . $this->db->table("countries") . " c
            WHERE c.iso_code_2 = '" . $this->db->escape($code) . "'"
        );
        return $query->row;
    }

    /**
     * @param array $data
     *
     * @return PaypalServerSdkLib\Models\Order|array|null
     */
    public function createPPOrder(array $data)
    {
        $apiResponse = $this->paypal
            ->getOrdersController()
            ->createOrder([
                'paypalPartnerAttributionId' => base64_decode(ExtensionPaypalCommerce::getBnCode()),
                'body' => $data,
            ]);
        return $apiResponse->getResult();
    }


    /**
     * @param string $ppOrderId
     * @return PaypalServerSdkLib\Models\Order|null
     */
    public function capturePPOrder(string $ppOrderId): ?object
    {
        if ($ppOrderId === '') {
            return null;
        }
    
        try {
            $apiResponse = $this->paypal
                ->getOrdersController()
                ->captureOrder([
                    'id' => $ppOrderId,
                    // TODO: add metaDataID (PayPal-Client-Metadata-Id) if needed by your flow
                    // 'paypalClientMetadataId' => $payPalClientMetadataId,
                ]);
    
            return $apiResponse->getResult();
        } catch (Exception $e) {
            $this->log->write(
                __FILE__ . '::' . __METHOD__ . ' Exception: ' . $e->getMessage() . ' (' . $e->getCode() . ')'
            );
    
            return null;
        }
    }

    /**
     * @param string $ppOrderId
     *
     * @return PaypalServerSdkLib\Models\Order|null
     */
    public function authorizePPOrder(string $ppOrderId): ?object
    {
        if ($ppOrderId === '') {
            return null;
        }

        try {
            $apiResponse = $this->paypal
                ->getOrdersController()
                ->authorizeOrder([
                    'id' => $ppOrderId,
                    // TODO: add metaDataID (PayPal-Client-Metadata-Id) if needed by your flow
                    // 'paypalClientMetadataId' => $payPalClientMetadataId,
                ]);

            return $apiResponse->getResult();
        } catch (Exception $e) {
            $this->log->write(
                __FILE__ . '::' . __METHOD__ . ' Exception: ' . $e->getMessage() . ' (' . $e->getCode() . ')'
            );

            return null;
        }
    }
}
