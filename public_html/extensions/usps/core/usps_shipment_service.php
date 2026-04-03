<?php
/**
 * $Id$
 *
 * AbanteCart, Ideal OpenSource Ecommerce Solution
 * http://www.AbanteCart.com
 *
 * Copyright © 2011-2026 Belavier Commerce LLC
 *
 * This source file is subject to Open Software License (OSL 3.0)
 * License details are bundled with this package in the file LICENSE.txt.
 * It is also available at this URL:
 * <http://www.opensource.org/licenses/OSL-3.0>
 *
 * UPGRADE NOTE:
 *   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *   versions in the future. If you wish to customize AbanteCart for your
 *   needs, please refer to http://www.AbanteCart.com for more information.
 */
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}
require_once(DIR_EXT . 'usps' . DS . 'core' . DS . 'usps_api_context.php');
require_once(DIR_EXT . 'usps' . DS . 'core' . DS . 'usps_error_parser.php');
require_once(DIR_EXT . 'usps' . DS . 'core' . DS . 'usps_token_service.php');

use GuzzleHttp\Client;
use USPS\InternationalLabels\Model\ImageInfo as InternationalImageInfo;
use USPS\InternationalLabels\Model\InternationalCustomsForm;
use USPS\InternationalLabels\Model\InternationalLabelAddress;
use USPS\InternationalLabels\Model\InternationalLabelRequest;
use USPS\InternationalLabels\Model\InternationalPackageDescription;
use USPS\InternationalLabels\Model\InternationalShippingContents;
use USPS\Labels\Model\DomesticLabelAddress;
use USPS\Labels\Model\DomesticLabelToAddress;
use USPS\Labels\Model\DomesticPackageDescription;
use USPS\Labels\Model\ImageInfo as DomesticImageInfo;
use USPS\Labels\Model\LabelRequest as DomesticLabelRequest;

class UspsShipmentService
{
    public $errors = [];
    public $lastShipment = [];

    private const USPS_V3_DOMESTIC_CLASS_MAP = [
        1    => ['mailClass' => 'PRIORITY_MAIL', 'processingCategory' => 'MACHINABLE'],
        2    => ['mailClass' => 'PRIORITY_MAIL_EXPRESS', 'processingCategory' => 'MACHINABLE'],
        3    => ['mailClass' => 'PARCEL_SELECT', 'processingCategory' => 'MACHINABLE'],
        4    => ['mailClass' => 'BOUND_PRINTED_MATTER', 'processingCategory' => 'MACHINABLE'],
        5    => ['mailClass' => 'MEDIA_MAIL', 'processingCategory' => 'MACHINABLE'],
        6    => ['mailClass' => 'LIBRARY_MAIL', 'processingCategory' => 'MACHINABLE'],
        7    => ['mailClass' => 'USPS_GROUND_ADVANTAGE', 'processingCategory' => 'MACHINABLE'],
    ];

    private const USPS_V3_INTL_CLASS_MAP = [
        1  => ['mailClass' => 'PRIORITY_MAIL_EXPRESS_INTERNATIONAL', 'processingCategory' => 'NON_MACHINABLE'],
        2  => ['mailClass' => 'PRIORITY_MAIL_INTERNATIONAL', 'processingCategory' => 'NON_MACHINABLE'],
        3  => ['mailClass' => 'GLOBAL_EXPRESS_GUARANTEED', 'processingCategory' => 'NON_MACHINABLE'],
        4  => ['mailClass' => 'FIRST-CLASS_PACKAGE_INTERNATIONAL_SERVICE', 'processingCategory' => 'NON_MACHINABLE'],
    ];

    private $registry;
    private $db;
    private $config;
    private $cache;
    private $language;
    private $log;

    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->db = $registry->get('db');
        $this->config = $registry->get('config');
        $this->cache = $registry->get('cache');
        $this->language = $registry->get('language');
        $this->log = $registry->get('log');
    }

    public function createShipment($order_info)
    {
        $this->errors = [];
        $this->lastShipment = [];
        if (!$order_info || !$order_info['order_id']) {
            return false;
        }

        $order_id = (int)$order_info['order_id'];
        $order_shipping_data = $this->getOrderShippingData($order_id);
        $savedData = (array)($order_shipping_data['data']['usps_data'] ?? []);
        if (!$savedData) {
            $this->errors[] = 'USPS shipping data was not saved for this order.';
            return false;
        }
        if (!empty($savedData['shipmentId'])) {
            $existingTracking = ($savedData['packages'][0]['tracking_number'] ?? $savedData['shipmentId']);
            $this->lastShipment = [
                'shipment_id'      => $savedData['shipmentId'],
                'tracking_number'  => $existingTracking,
            ];
            return true;
        }

        $baseUrl = UspsApiContext::getApiBaseUrl($this->config->get('usps_api_environment'));
        $tokenData = $this->getOauthToken($baseUrl);
        if (!$tokenData['token']) {
            $this->errors[] = $tokenData['error'] ?: 'USPS OAuth token is empty.';
            return false;
        }

        $paymentTokenData = $this->getPaymentAuthorizationToken($baseUrl, $tokenData['token']);
        if (!$paymentTokenData['token']) {
            $this->errors[] = $paymentTokenData['error'] ?: 'USPS Payment Authorization Token could not be generated.';
            return false;
        }
        $paymentAuthorizationToken = $paymentTokenData['token'];

        $serviceClassId = $this->extractServiceClassId($order_info['shipping_method_key']);
        if (!$serviceClassId) {
            $this->errors[] = 'Unable to detect USPS service from order shipping method key.';
            return false;
        }

        $isInternational = $order_info['shipping_iso_code_2'] !== 'US';
        $payload = $this->buildLabelPayload(
            $order_info,
            $serviceClassId,
            $isInternational,
            $savedData['usps_parcel_data'] ?? [],
            $savedData['fromAddress'] ?? []
        );
        if (!$payload) {
            return false;
        }

        try {
            $compliance = null;
            if ((bool)$this->config->get('usps_debug')) {
                $this->log->write(
                    __METHOD__ . ' order_id=' . $order_id . ' USPS payload: ' . json_encode(
                        $payload,
                        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                    )
                );
            }
            if ($isInternational) {
                $compliance = $this->resolveInternationalCompliance($order_info, $savedData);
                $request = $this->buildInternationalLabelRequest($payload, (string)($compliance['value'] ?? ''));
                [$trackingNumber, $imageBase64] = $this->requestInternationalLabelRaw(
                    $baseUrl,
                    $tokenData['token'],
                    $paymentAuthorizationToken,
                    $request
                );
            } else {
                $request = $this->buildDomesticLabelRequest($payload);
                [$trackingNumber, $imageBase64] = $this->requestDomesticLabelRaw(
                    $baseUrl,
                    $tokenData['token'],
                    $paymentAuthorizationToken,
                    $request
                );
            }
        } catch (\Throwable $e) {
            $apiError = $this->formatUspsError($e, 'USPS Label API error');
            $this->log->write(__METHOD__ . ' order_id=' . $order_id . ' ' . $apiError);
            $this->errors[] = $apiError;
            return false;
        }

        if ($trackingNumber === '' || $imageBase64 === '') {
            $this->errors[] = 'USPS label response is missing tracking number or label image.';
            return false;
        }

        $filename = $this->saveLabelFile($order_id, $trackingNumber, $imageBase64);
        if (!$filename) {
            $this->errors[] = 'Unable to save USPS label file.';
            return false;
        }

        $saveData = [
            'shipmentId' => $trackingNumber,
            'packages'   => [
                [
                    'tracking_number' => $trackingNumber,
                    'file'            => basename($filename),
                    'image_type'      => 'pdf',
                ],
            ],
        ];
        if (!empty($savedData['toAddress'])) {
            $saveData['toAddress'] = $savedData['toAddress'];
        }
        if (!empty($savedData['fromAddress'])) {
            $saveData['fromAddress'] = $savedData['fromAddress'];
        }
        if (!empty($savedData['usps_parcel_data'])) {
            $saveData['usps_parcel_data'] = $savedData['usps_parcel_data'];
        }
        if (!empty($compliance) && is_array($compliance)) {
            $saveData['compliance'] = [
                'aesitn' => (string)($compliance['value'] ?? ''),
                'source' => (string)($compliance['source'] ?? ''),
                'rule_id' => (string)($compliance['rule_id'] ?? ''),
            ];
        }

        $saved = (bool)$this->saveOrderShippingData($order_id, $saveData);
        if ($saved) {
            $this->lastShipment = [
                'shipment_id'      => $trackingNumber,
                'tracking_number'  => $trackingNumber,
            ];
        }
        return $saved;
    }

    public static function resolveTrackUrl(array $package, array $uspsData, int $index = 0): string
    {
        $trackUrl = ($package['track_url'] ?? '');
        if ($trackUrl !== '') {
            return $trackUrl;
        }

        $trackUrls = (array)($uspsData['track_urls'] ?? []);
        if (isset($trackUrls[$index])) {
            $trackUrlFromList = $trackUrls[$index];
            if ($trackUrlFromList !== '') {
                return $trackUrlFromList;
            }
        }

        $trackingNumber = ($package['tracking_number'] ?? '');
        if ($trackingNumber === '') {
            return '';
        }

        return 'https://tools.usps.com/go/TrackConfirmAction?qtc_tLabels1=' . urlencode($trackingNumber);
    }

    protected function saveOrderShippingData($order_id, $data)
    {
        if (!$order_id || !$data) {
            return false;
        }

        $sql = "SELECT type_id
                FROM " . $this->db->table('order_data_types') . "
                WHERE name='shipping_data'
                    AND language_id='" . (int)$this->language->getDefaultLanguageID() . "'";
        $result = $this->db->query($sql);
        $type_id = (int)$result->row['type_id'];
        if (!$type_id) {
            $this->db->query(
                "INSERT INTO " . $this->db->table('order_data_types') . "
                (language_id, name, date_added)
                VALUES (" . (int)$this->language->getDefaultLanguageID() . ", 'shipping_data', NOW())"
            );
            $type_id = (int)$this->db->getLastId();
        }
        if (!$type_id) {
            $this->log->write(__METHOD__ . ': Cannot resolve shipping_data type_id.');
            return false;
        }

        $data = ['usps_data' => $data];

        $sql = "SELECT *
                FROM " . $this->db->table('order_data') . "
                WHERE order_id = '" . (int)$order_id . "'
                    AND type_id = '" . (int)$type_id . "'";
        $result = $this->db->query($sql);
        $exists = $result->row;
        $shipping_data = $exists['data'];
        if ($shipping_data) {
            $shipping_data = unserialize($shipping_data);
        }
        $shipping_data['usps_data'] = $shipping_data !== false
            ? array_merge((array)$shipping_data['usps_data'], $data['usps_data'])
            : $data['usps_data'];

        if (!$exists) {
            $sql = "INSERT INTO " . $this->db->table('order_data') . "
                (order_id, type_id, data)
                VALUES
                (" . (int)$order_id . ", " . (int)$type_id . ", '" . $this->db->escape(serialize($shipping_data)) . "')";
        } else {
            $sql = "UPDATE " . $this->db->table('order_data') . "
                SET data = '" . $this->db->escape(serialize($shipping_data)) . "'
                WHERE order_id = '" . (int)$order_id . "' AND type_id = '" . (int)$type_id . "'";
        }
        return $this->db->query($sql);
    }

    protected function getOrderShippingData($order_id)
    {
        $sql = "SELECT *
                FROM " . $this->db->table('order_data_types') . "
                WHERE name = 'shipping_data'
                    AND language_id = " . $this->language->getDefaultLanguageID();
        $result = $this->db->query($sql);
        $order_data_type_id = (int)$result->row['type_id'];

        $shipping_data = [];
        if ($order_data_type_id) {
            $sql = "SELECT *
                    FROM " . $this->db->table('order_data') . "
                    WHERE order_id = " . (int)$order_id . "
                        AND type_id = " . $order_data_type_id;
            $result = $this->db->query($sql);
            $shipping_data = (array)unserialize($result->row['data']);
        }

        return [
            'order_data_type_id' => $order_data_type_id,
            'data'               => $shipping_data,
        ];
    }

    protected function extractServiceClassId($shippingMethodKey)
    {
        if (!str_contains($shippingMethodKey, '.')) {
            return 0;
        }
        [, $classId] = explode('.', $shippingMethodKey, 2);
        return (int)$classId;
    }

    protected function resolveStoreStateCode()
    {
        $zoneId = (int)$this->config->get('usps_country_zone');
        if ($zoneId > 0) {
            $result = $this->db->query(
                "SELECT code
                 FROM " . $this->db->table('zones') . "
                 WHERE zone_id = '" . $zoneId . "'
                 LIMIT 1"
            );
            $state = ($result->row['code'] ?? '');
            if ($state !== '') {
                return $state;
            }
        }

        return '';
    }

    protected function resolveStoreCountryCode()
    {
        $countryId = (int)$this->config->get('usps_country');
        if ($countryId <= 0) {
            return '';
        }

        $result = $this->db->query(
            "SELECT iso_code_2
             FROM " . $this->db->table('countries') . "
             WHERE country_id = '" . $countryId . "'
             LIMIT 1"
        );

        return ($result->row['iso_code_2'] ?? '');
    }

    protected function getStoreFromAddress(array $savedFromAddress = []): array
    {
        return [
            'name'              => $savedFromAddress['name'] ?? $this->config->get('store_name'),
            'firm'              => $savedFromAddress['firm'] ?? $this->config->get('store_name'),
            'street_address'    => $savedFromAddress['street_address'] ?? $this->config->get('usps_address'),
            'secondary_address' => $savedFromAddress['secondary_address'] ?? '',
            'city'              => $savedFromAddress['city'] ?? $this->config->get('usps_city'),
            'state'             => $savedFromAddress['state'] ?? $this->resolveStoreStateCode(),
            'zip_code'          => $savedFromAddress['zip_code'] ?? $this->config->get('usps_postcode'),
            'country_code'      => $savedFromAddress['country_code'] ?? $this->resolveStoreCountryCode(),
            'phone'             => $savedFromAddress['phone'] ?? $this->config->get('usps_telephone'),
            'email'             => $savedFromAddress['email'] ?? $this->config->get('store_main_email'),
        ];
    }

    protected function resolveOrderStateCode(array $orderInfo)
    {
        $state = ($orderInfo['shipping_zone_code'] ?? '');
        if ($state !== '') {
            return $state;
        }

        $zoneId = (int)($orderInfo['shipping_zone_id'] ?? 0);
        if ($zoneId > 0) {
            $result = $this->db->query(
                "SELECT code
                 FROM " . $this->db->table('zones') . "
                 WHERE zone_id = '" . $zoneId . "'
                 LIMIT 1"
            );
            $state = ($result->row['code'] ?? '');
            if ($state !== '') {
                return $state;
            }
        }

        return '';
    }

    protected function buildLabelPayload(
        array $orderInfo,
        int $serviceClassId,
        bool $isInternational,
        array $parcelData,
        array $savedFromAddress = []
    )
    {
        $serviceMap = $isInternational ? self::USPS_V3_INTL_CLASS_MAP : self::USPS_V3_DOMESTIC_CLASS_MAP;
        if (empty($serviceMap[$serviceClassId])) {
            $this->errors[] = 'Selected USPS service is not configured for label creation.';
            return [];
        }

        $shippingFirstName = $orderInfo['shipping_firstname'];
        $shippingLastName = $orderInfo['shipping_lastname'];
        $shippingName = ($shippingFirstName !== '' && $shippingLastName !== '')
            ? $shippingFirstName . ' ' . $shippingLastName
            : $shippingFirstName . $shippingLastName;
        $shipperName = $this->config->get('store_name');
        $weight = max((float)($parcelData['weight'] ?? 1), 0.1);
        $length = max((float)($parcelData['depth'] ?? $this->config->get('usps_length')), 0.1);
        $width = max((float)($parcelData['width'] ?? $this->config->get('usps_width')), 0.1);
        $height = max((float)($parcelData['height'] ?? $this->config->get('usps_height')), 0.1);
        $fromAddress = $this->getStoreFromAddress($savedFromAddress);
        $fromState = $fromAddress['state'];
        $fromCountryCode = $fromAddress['country_code'];
        $fromCity = $fromAddress['city'];
        $fromZip = $fromAddress['zip_code'];
        $toState = $this->resolveOrderStateCode($orderInfo);

        return [
            'is_international' => $isInternational,
            'mail_class' => $serviceMap[$serviceClassId]['mailClass'],
            'processing_category' => $serviceMap[$serviceClassId]['processingCategory'],
            'to' => [
                'name' => $shippingName ?: $orderInfo['shipping_address_1'],
                'firm' => $orderInfo['shipping_company'] ?: ($shippingName ?: $orderInfo['shipping_address_1']),
                'street_address' => $orderInfo['shipping_address_1'],
                'secondary_address' => $orderInfo['shipping_address_2'],
                'city' => $orderInfo['shipping_city'],
                'state' => $toState,
                'zip_code' => $orderInfo['shipping_postcode'],
                'country_code' => $orderInfo['shipping_iso_code_2'],
                'country_name' => $orderInfo['shipping_country'],
                'phone' => $orderInfo['telephone'],
            ],
            'from' => [
                'name' => $fromAddress['name'] ?: ($shipperName ?: 'Store'),
                'firm' => $fromAddress['firm'] ?: $shipperName,
                'street_address' => $fromAddress['street_address'],
                'secondary_address' => $fromAddress['secondary_address'],
                'city' => $fromCity,
                'state' => $fromState,
                'zip_code' => $fromZip,
                'country_code' => $fromCountryCode,
                'phone' => $fromAddress['phone'],
                'email' => $fromAddress['email'],
            ],
            'package' => [
                'weight' => $weight,
                'length' => $length,
                'width' => $width,
                'height' => $height,
            ],
        ];
    }

    protected function buildDomesticLabelRequest(array $payload)
    {
        $toAddress = new DomesticLabelToAddress(
            [
                'first_name'        => $payload['to']['name'],
                'firm'              => $payload['to']['firm'],
                'street_address'    => $payload['to']['street_address'],
                'secondary_address' => $payload['to']['secondary_address'],
                'city'              => $payload['to']['city'],
                'state'             => $payload['to']['state'],
                'zip_code'          => $payload['to']['zip_code'],
            ]
        );
        $fromAddress = new DomesticLabelAddress(
            [
                'first_name'     => $payload['from']['name'],
                'firm'           => $payload['from']['firm'],
                'street_address' => $payload['from']['street_address'],
                'secondary_address' => $payload['from']['secondary_address'],
                'city'           => $payload['from']['city'],
                'state'          => $payload['from']['state'],
                'zip_code'       => $payload['from']['zip_code'],
                'ignore_bad_address' => true,
            ]
        );
        $packageDescription = new DomesticPackageDescription(
            [
                'mail_class'                     => $payload['mail_class'],
                'processing_category'            => $payload['processing_category'],
                'destination_entry_facility_type' => 'NONE',
                'mailing_date'                   => date('Y-m-d'),
                'rate_indicator'                 => 'DR',
                'shape'                          => 'RECTANGLE',
                'packaging_type'                 => 'VARIABLE',
                'weight_uom'                     => 'lb',
                'weight'                         => $payload['package']['weight'],
                'dimensions_uom'                 => 'in',
                'length'                         => $payload['package']['length'],
                'height'                         => $payload['package']['height'],
                'width'                          => $payload['package']['width'],
            ]
        );

        return new DomesticLabelRequest(
            [
                'image_info'          => new DomesticImageInfo(['image_type' => 'PDF', 'label_type' => '4X6LABEL']),
                'to_address'          => $toAddress,
                'from_address'        => $fromAddress,
                'package_description' => $packageDescription,
            ]
        );
    }

    protected function buildInternationalLabelRequest(array $payload, string $aesItn)
    {
        $toAddress = new InternationalLabelAddress(
            [
                'first_name'        => $payload['to']['name'],
                'firm'              => $payload['to']['firm'],
                'street_address'    => $payload['to']['street_address'],
                'secondary_address' => $payload['to']['secondary_address'],
                'city'              => $payload['to']['city'],
                'country'           => $payload['to']['country_name'] ?: $payload['to']['country_code'],
                'country_iso_alpha2_code' => $payload['to']['country_code'],
                'postal_code'       => $payload['to']['zip_code'],
                'phone'             => $payload['to']['phone'],
            ]
        );
        $fromAddress = new \USPS\InternationalLabels\Model\DomesticLabelAddress(
            [
                'first_name'     => $payload['from']['name'],
                'firm'           => $payload['from']['firm'],
                'street_address' => $payload['from']['street_address'],
                'secondary_address' => $payload['from']['secondary_address'],
                'city'           => $payload['from']['city'],
                'state'          => $payload['from']['state'],
                'zip_code'       => $payload['from']['zip_code'],
                'ignore_bad_address' => true,
            ]
        );
        $packageDescription = new InternationalPackageDescription(
            [
                'mail_class'                     => $payload['mail_class'],
                'processing_category'            => $payload['processing_category'],
                'destination_entry_facility_type' => 'NONE',
                'mailing_date'                   => date('Y-m-d'),
                'rate_indicator'                 => 'SP',
                'weight_uom'                     => 'lb',
                'weight'                         => $payload['package']['weight'],
                'dimensions_uom'                 => 'in',
                'length'                         => $payload['package']['length'],
                'height'                         => $payload['package']['height'],
                'width'                          => $payload['package']['width'],
            ]
        );
        $customsForm = new InternationalCustomsForm(
            [
                'customs_content_type' => 'MERCHANDISE',
                'aesitn'               => $aesItn,
                'contents'             => [
                    new InternationalShippingContents(
                        [
                            'item_description' => 'Retail goods',
                            'item_quantity'    => 1,
                            'item_total_value' => 1.0,
                            'item_total_weight' => max((float)$payload['package']['weight'], 0.1),
                            'weight_uom'       => 'lb',
                            'countryof_origin' => $payload['from']['country_code'] ?: 'US',
                        ]
                    ),
                ],
            ]
        );

        return new InternationalLabelRequest(
            [
                'image_info'          => new InternationalImageInfo(['image_type' => 'PDF', 'label_type' => '4X6LABEL']),
                'to_address'          => $toAddress,
                'from_address'        => $fromAddress,
                'package_description' => $packageDescription,
                'customs_form'        => $customsForm,
            ]
        );
    }

    protected function resolveInternationalCompliance(array $orderInfo, array $savedData): array
    {
        $manualOverride = (string)($savedData['shipment_overrides']['aesitn'] ?? $savedData['aesitn'] ?? '');
        if ($manualOverride !== '') {
            return [
                'value' => $manualOverride,
                'source' => 'manual_override',
                'rule_id' => 'manual_override',
            ];
        }

        $configured = (string)$this->config->get('usps_international_aesitn');
        if ($configured !== '') {
            return [
                'value' => $configured,
                'source' => 'config_default',
                'rule_id' => 'config_default',
            ];
        }

        return [
            'value' => 'NO EEI 30.37(a)',
            'source' => 'fallback',
            'rule_id' => 'fallback_default',
        ];
    }

    protected function saveLabelFile($orderId, $trackingNumber, $imageBase64)
    {
        $labelDir = DIR_ROOT . DS . 'admin' . DS . 'system' . DS . 'data' . DS . 'usps_labels';
        if (!is_dir($labelDir) && !mkdir($labelDir, 0775, true) && !is_dir($labelDir)) {
            return '';
        }

        $filename = $labelDir . DS . 'order_label_' . $orderId . '.' . $trackingNumber . '.pdf';
        $binary = base64_decode($imageBase64, true);
        if ($binary === false) {
            return '';
        }
        return file_put_contents($filename, $binary) !== false ? $filename : '';
    }

    protected function getOauthToken($baseUrl)
    {
        $clientId = $this->config->get('usps_client_id');
        $clientSecret = $this->config->get('usps_client_secret');
        try {
            $tokenData = $this->getTokenService()->getOauthToken(
                $baseUrl,
                $clientId,
                $clientSecret,
                $this->getOauthTokenCacheKey()
            );
            return ['token' => $tokenData['token'], 'error' => ''];
        } catch (\Throwable $e) {
            return ['token' => '', 'error' => $this->formatUspsError($e, 'USPS OAuth error')];
        }
    }

    protected function getPaymentAuthorizationToken($baseUrl, $oauthToken)
    {
        $crid = $this->config->get('usps_payment_crid');
        $mid = $this->config->get('usps_payment_mid');
        $manifestMid = $this->config->get('usps_payment_manifest_mid');
        $accountNumber = $this->config->get('usps_payment_account_number');

        if ($crid === '' || $mid === '' || $manifestMid === '' || $accountNumber === '') {
            return [
                'token' => '',
                'error' => 'USPS payment settings are not configured. Please fill Customer Registration ID (CRID), Mailer ID (MID), Manifest MID and EPS Account Number.',
            ];
        }

        try {
            $tokenData = $this->getTokenService()->getPaymentAuthorizationToken(
                $baseUrl,
                $oauthToken,
                $crid,
                $mid,
                $manifestMid,
                $accountNumber,
                $this->getPaymentAuthorizationTokenCacheKey()
            );
            return ['token' => $tokenData['token'], 'error' => ''];
        } catch (\Throwable $e) {
            return ['token' => '', 'error' => $this->formatUspsError($e, 'USPS Payments API error')];
        }
    }

    protected function getOauthTokenCacheKey()
    {
        return UspsApiContext::buildHashKey(
            'usps.oauth_token.',
            [
                (int)$this->config->get('config_store_id'),
                UspsApiContext::getEnvironmentCode($this->config->get('usps_api_environment')),
                $this->config->get('usps_client_id'),
            ]
        );
    }

    protected function getPaymentAuthorizationTokenCacheKey()
    {
        return UspsApiContext::buildHashKey(
            'usps.payment_token.',
            [
                (int)$this->config->get('config_store_id'),
                UspsApiContext::getEnvironmentCode($this->config->get('usps_api_environment')),
                $this->config->get('usps_client_id'),
                $this->config->get('usps_payment_crid'),
                $this->config->get('usps_payment_mid'),
                $this->config->get('usps_payment_manifest_mid'),
                $this->config->get('usps_payment_account_number'),
            ]
        );
    }

    protected function getTokenService()
    {
        return new UspsTokenService($this->cache, 20);
    }

    protected function formatUspsError(\Throwable $e, $prefix)
    {
        $message = (new UspsErrorParser())->parseThrowable($e);
        return $prefix . ': ' . $message;
    }

    protected function requestDomesticLabelRaw($baseUrl, $oauthToken, $paymentAuthorizationToken, DomesticLabelRequest $request)
    {
        $payload = \USPS\Labels\ObjectSerializer::sanitizeForSerialization($request);
        $client = new Client(['timeout' => 30]);
        $response = $client->post(
            rtrim($baseUrl, '/') . '/labels/v3/label',
            [
                'headers' => [
                    'Authorization'                  => 'Bearer ' . $oauthToken,
                    'X-Payment-Authorization-Token' => $paymentAuthorizationToken,
                    'Accept'                         => 'application/vnd.usps.labels+json',
                    'Content-Type'                   => 'application/json',
                ],
                'http_errors' => false,
                'json' => $payload,
            ]
        );

        $statusCode = (int)$response->getStatusCode();
        $body = $response->getBody()->getContents();
        if ($statusCode >= 400) {
            throw new \RuntimeException(
                'HTTP ' . $statusCode . ': ' . ($body !== '' ? $body : 'USPS Labels API returned empty error response.')
            );
        }
        $json = json_decode($body, true);
        if (!is_array($json)) {
            throw new \RuntimeException('USPS Labels raw response is not valid JSON.');
        }

        $trackingNumber = $json['labelMetadata']['trackingNumber'] ?? '';
        if ($trackingNumber === '') {
            $trackingNumber = $json['trackingNumber'] ?? $json['tracking_number'] ?? '';
        }
        if ($trackingNumber === '') {
            $trackingNumber = $this->getHeaderValue($response->getHeaders(), 'X-Tracking-Number');
        }
        $labelImage = $json['labelImage'] ?? '';
        if ($trackingNumber === '' || $labelImage === '') {
            throw new \RuntimeException(
                'USPS Labels raw response is missing trackingNumber or labelImage. Body: '
                . substr(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 0, 700)
            );
        }

        return [$trackingNumber, $labelImage];
    }

    protected function requestInternationalLabelRaw(
        $baseUrl,
        $oauthToken,
        $paymentAuthorizationToken,
        InternationalLabelRequest $request
    ) {
        $payload = \USPS\InternationalLabels\ObjectSerializer::sanitizeForSerialization($request);
        $client = new Client(['timeout' => 30]);
        $response = $client->post(
            rtrim($baseUrl, '/') . '/international-labels/v3/international-label',
            [
                'headers' => [
                    'Authorization'                  => 'Bearer ' . $oauthToken,
                    'X-Payment-Authorization-Token' => $paymentAuthorizationToken,
                    'Accept'                         => 'application/vnd.usps.labels+json',
                    'Content-Type'                   => 'application/json',
                ],
                'http_errors' => false,
                'json' => $payload,
            ]
        );

        $statusCode = (int)$response->getStatusCode();
        $body = $response->getBody()->getContents();
        if ($statusCode >= 400) {
            throw new \RuntimeException(
                'HTTP ' . $statusCode . ': ' . ($body !== '' ? $body : 'USPS International Labels API returned empty error response.')
            );
        }
        $json = json_decode($body, true);
        if (!is_array($json)) {
            throw new \RuntimeException('USPS International Labels raw response is not valid JSON.');
        }

        $trackingNumber = $json['labelMetadata']['internationalTrackingNumber'] ?? '';
        if ($trackingNumber === '') {
            $trackingNumber = $json['internationalTrackingNumber'] ?? $json['trackingNumber'] ?? $json['tracking_number'] ?? '';
        }
        if ($trackingNumber === '') {
            $trackingNumber = $this->getHeaderValue($response->getHeaders(), 'X-Tracking-Number');
        }
        $labelImage = $json['labelImage'] ?? '';
        if ($trackingNumber === '' || $labelImage === '') {
            throw new \RuntimeException(
                'USPS International Labels raw response is missing trackingNumber or labelImage. Body: '
                . substr(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 0, 700)
            );
        }

        return [$trackingNumber, $labelImage];
    }

    protected function getHeaderValue(array $headers, string $headerName): string
    {
        foreach ($headers as $name => $values) {
            if (strcasecmp($name, $headerName) !== 0) {
                continue;
            }
            if (is_array($values)) {
                return ($values[0] ?? '');
            }
            return $values;
        }
        return '';
    }

}
