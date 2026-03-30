<?php
/*
 *   USPS shipment/label generation service (merchant-side flow).
 */
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}
require_once(DIR_EXT . 'usps' . DS . 'core' . DS . 'usps_api_context.php');
require_once(DIR_EXT . 'usps' . DS . 'core' . DS . 'usps_error_parser.php');
require_once(DIR_EXT . 'usps' . DS . 'core' . DS . 'usps_token_service.php');

use GuzzleHttp\Client;
use USPS\InternationalLabels\Api\ResourcesApi as InternationalLabelsApi;
use USPS\InternationalLabels\Configuration as InternationalLabelsConfiguration;
use USPS\InternationalLabels\Model\ImageInfo as InternationalImageInfo;
use USPS\InternationalLabels\Model\InternationalCustomsForm;
use USPS\InternationalLabels\Model\InternationalLabelAddress;
use USPS\InternationalLabels\Model\InternationalLabelRequest;
use USPS\InternationalLabels\Model\InternationalPackageDescription;
use USPS\InternationalLabels\Model\InternationalShippingContents;
use USPS\Labels\Api\ResourcesApi as DomesticLabelsApi;
use USPS\Labels\Configuration as DomesticLabelsConfiguration;
use USPS\Labels\Model\DomesticLabelAddress;
use USPS\Labels\Model\DomesticLabelToAddress;
use USPS\Labels\Model\DomesticPackageDescription;
use USPS\Labels\Model\ImageInfo as DomesticImageInfo;
use USPS\Labels\Model\LabelRequest as DomesticLabelRequest;

class UspsShipmentService
{
    public $errors = [];

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

        $serviceClassId = $this->extractServiceClassId((string)$order_info['shipping_method_key']);
        if (!$serviceClassId) {
            $this->errors[] = 'Unable to detect USPS service from order shipping method key.';
            return false;
        }

        $isInternational = strtoupper((string)$order_info['shipping_iso_code_2']) !== 'US';
        $payload = $this->buildLabelPayload($order_info, $serviceClassId, $isInternational, $savedData['usps_parcel_data'] ?? []);
        if (!$payload) {
            return false;
        }

        try {
            if ($isInternational) {
                $config = InternationalLabelsConfiguration::getDefaultConfiguration()
                    ->setHost($baseUrl . '/international-labels/v3')
                    ->setAccessToken($tokenData['token']);
                $apiClient = new InternationalLabelsApi(new Client(['timeout' => 30]), $config);
                $request = $this->buildInternationalLabelRequest($payload);
                $response = $apiClient->postInternationalLabel($request, $paymentAuthorizationToken);
                $metadata = $response->getLabelMetadata();
                $trackingNumber = $metadata ? (string)$metadata->getInternationalTrackingNumber() : '';
                $imageBase64 = (string)$response->getLabelImage();
            } else {
                $config = DomesticLabelsConfiguration::getDefaultConfiguration()
                    ->setHost($baseUrl . '/labels/v3')
                    ->setAccessToken($tokenData['token']);
                $apiClient = new DomesticLabelsApi(new Client(['timeout' => 30]), $config);
                $request = $this->buildDomesticLabelRequest($payload);
                $response = $apiClient->postLabel($request, $paymentAuthorizationToken);
                $metadata = $response->getLabelMetadata();
                $trackingNumber = $metadata ? (string)$metadata->getTrackingNumber() : '';
                $imageBase64 = (string)$response->getLabelImage();
            }
        } catch (\Throwable $e) {
            $this->errors[] = $this->formatUspsError($e, 'USPS Label API error');
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
                    'label'           => $imageBase64,
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

        return (bool)$this->saveOrderShippingData($order_id, $saveData);
    }

    private function saveOrderShippingData($order_id, $data)
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

    private function getOrderShippingData($order_id)
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

    private function extractServiceClassId($shippingMethodKey)
    {
        if (!str_contains($shippingMethodKey, '.')) {
            return 0;
        }
        [, $classId] = explode('.', $shippingMethodKey, 2);
        return (int)$classId;
    }

    private function normalizeZipCode($zip)
    {
        return substr(preg_replace('/[^0-9]/', '', (string)$zip), 0, 5);
    }

    private function buildLabelPayload(array $orderInfo, int $serviceClassId, bool $isInternational, array $parcelData)
    {
        $serviceMap = $isInternational ? self::USPS_V3_INTL_CLASS_MAP : self::USPS_V3_DOMESTIC_CLASS_MAP;
        if (empty($serviceMap[$serviceClassId])) {
            $this->errors[] = 'Selected USPS service is not configured for label creation.';
            return [];
        }

        $shippingName = trim((string)$orderInfo['shipping_firstname'] . ' ' . (string)$orderInfo['shipping_lastname']);
        $shipperName = (string)$this->config->get('store_name');
        $weight = max((float)($parcelData['weight'] ?? 1), 0.1);
        $length = max((float)($parcelData['depth'] ?? $this->config->get('usps_length')), 0.1);
        $width = max((float)($parcelData['width'] ?? $this->config->get('usps_width')), 0.1);
        $height = max((float)($parcelData['height'] ?? $this->config->get('usps_height')), 0.1);

        return [
            'is_international' => $isInternational,
            'mail_class' => $serviceMap[$serviceClassId]['mailClass'],
            'processing_category' => $serviceMap[$serviceClassId]['processingCategory'],
            'to' => [
                'name' => $shippingName ?: (string)$orderInfo['shipping_address_1'],
                'firm' => (string)$orderInfo['shipping_company'],
                'street_address' => (string)$orderInfo['shipping_address_1'],
                'secondary_address' => (string)$orderInfo['shipping_address_2'],
                'city' => (string)$orderInfo['shipping_city'],
                'state' => (string)$orderInfo['shipping_zone_code'],
                'zip_code' => $isInternational
                    ? (string)$orderInfo['shipping_postcode']
                    : $this->normalizeZipCode($orderInfo['shipping_postcode']),
                'country_code' => strtoupper((string)$orderInfo['shipping_iso_code_2']),
                'phone' => preg_replace('/\D+/', '', (string)$orderInfo['telephone']),
            ],
            'from' => [
                'name' => $shipperName ?: 'Store',
                'firm' => $shipperName,
                'street_address' => (string)$this->config->get('config_address'),
                'city' => (string)$this->config->get('config_city'),
                'state' => (string)$this->config->get('config_zone_code'),
                'zip_code' => $this->normalizeZipCode($this->config->get('usps_postcode') ?: $this->config->get('config_postcode')),
                'country_code' => strtoupper((string)$this->config->get('config_country_iso_code_2') ?: 'US'),
                'phone' => preg_replace('/\D+/', '', (string)$this->config->get('config_telephone')),
                'email' => (string)$this->config->get('store_main_email'),
            ],
            'package' => [
                'weight' => $weight,
                'length' => $length,
                'width' => $width,
                'height' => $height,
            ],
        ];
    }

    private function buildDomesticLabelRequest(array $payload)
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
                'city'           => $payload['from']['city'],
                'state'          => $payload['from']['state'],
                'zip_code'       => $payload['from']['zip_code'],
            ]
        );
        $packageDescription = new DomesticPackageDescription(
            [
                'mail_class'                     => $payload['mail_class'],
                'processing_category'            => $payload['processing_category'],
                'destination_entry_facility_type' => 'NONE',
                'mailing_date'                   => date('Y-m-d'),
                'rate_indicator'                 => 'SP',
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

    private function buildInternationalLabelRequest(array $payload)
    {
        $toAddress = new InternationalLabelAddress(
            [
                'first_name'        => $payload['to']['name'],
                'firm'              => $payload['to']['firm'],
                'street_address'    => $payload['to']['street_address'],
                'secondary_address' => $payload['to']['secondary_address'],
                'city'              => $payload['to']['city'],
                'country'           => $payload['to']['country_code'],
                'postal_code'       => strtoupper(substr(preg_replace('/\s+/', '', (string)$payload['to']['zip_code']), 0, 10)),
                'phone'             => $payload['to']['phone'],
            ]
        );
        $fromAddress = new \USPS\InternationalLabels\Model\DomesticLabelAddress(
            [
                'first_name'     => $payload['from']['name'],
                'firm'           => $payload['from']['firm'],
                'street_address' => $payload['from']['street_address'],
                'city'           => $payload['from']['city'],
                'state'          => $payload['from']['state'],
                'zip_code'       => $payload['from']['zip_code'],
            ]
        );
        $packageDescription = new InternationalPackageDescription(
            [
                'mail_class'                     => $payload['mail_class'],
                'processing_category'            => $payload['processing_category'],
                'destination_entry_facility_type' => 'NONE',
                'mailing_date'                   => date('Y-m-d'),
                'rate_indicator'                 => 'SP',
                'packaging_type'                 => 'VARIABLE',
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
                'contents'             => [
                    new InternationalShippingContents(
                        [
                            'item_description' => 'Merchandise',
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

    private function saveLabelFile($orderId, $trackingNumber, $imageBase64)
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

    private function getOauthToken($baseUrl)
    {
        $clientId = (string)$this->config->get('usps_client_id');
        $clientSecret = (string)$this->config->get('usps_client_secret');
        try {
            $tokenData = $this->getTokenService()->getOauthToken(
                $baseUrl,
                $clientId,
                $clientSecret,
                $this->getOauthTokenCacheKey()
            );
            return ['token' => (string)$tokenData['token'], 'error' => ''];
        } catch (\Throwable $e) {
            return ['token' => '', 'error' => $this->formatUspsError($e, 'USPS OAuth error')];
        }
    }

    private function getPaymentAuthorizationToken($baseUrl, $oauthToken)
    {
        $crid = trim((string)$this->config->get('usps_payment_crid'));
        $mid = trim((string)$this->config->get('usps_payment_mid'));
        $manifestMid = trim((string)$this->config->get('usps_payment_manifest_mid'));
        $accountNumber = trim((string)$this->config->get('usps_payment_account_number'));

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
            return ['token' => (string)$tokenData['token'], 'error' => ''];
        } catch (\Throwable $e) {
            return ['token' => '', 'error' => $this->formatUspsError($e, 'USPS Payments API error')];
        }
    }

    private function getOauthTokenCacheKey()
    {
        return UspsApiContext::buildHashKey(
            'usps.oauth_token.',
            [
                (int)$this->config->get('config_store_id'),
                UspsApiContext::getEnvironmentCode($this->config->get('usps_api_environment')),
                (string)$this->config->get('usps_client_id'),
            ]
        );
    }

    private function getPaymentAuthorizationTokenCacheKey()
    {
        return UspsApiContext::buildHashKey(
            'usps.payment_token.',
            [
                (int)$this->config->get('config_store_id'),
                UspsApiContext::getEnvironmentCode($this->config->get('usps_api_environment')),
                trim((string)$this->config->get('usps_client_id')),
                trim((string)$this->config->get('usps_payment_crid')),
                trim((string)$this->config->get('usps_payment_mid')),
                trim((string)$this->config->get('usps_payment_manifest_mid')),
                trim((string)$this->config->get('usps_payment_account_number')),
            ]
        );
    }

    private function getTokenService()
    {
        return new UspsTokenService($this->cache, 20);
    }

    private function formatUspsError(\Throwable $e, $prefix)
    {
        $message = (new UspsErrorParser())->parseThrowable($e);
        return $prefix . ': ' . $message;
    }
}
