<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2023 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

use GuzzleHttp\Client;
use USPS\InternationalLabels\Api\ResourcesApi as InternationalLabelsApi;
use USPS\InternationalLabels\Configuration as InternationalLabelsConfiguration;
use USPS\InternationalLabels\Model\ImageInfo as InternationalImageInfo;
use USPS\InternationalLabels\Model\InternationalCustomsForm;
use USPS\InternationalLabels\Model\InternationalLabelAddress;
use USPS\InternationalLabels\Model\InternationalLabelRequest;
use USPS\InternationalLabels\Model\InternationalPackageDescription;
use USPS\InternationalLabels\Model\InternationalShippingContents;
use USPS\DomesticPrices\Api\ResourcesApi as DomesticRatesApi;
use USPS\DomesticPrices\Configuration as DomesticPricesConfiguration;
use USPS\DomesticPrices\Model\BaseRatesQuery as DomesticBaseRatesQuery;
use USPS\InternationalPrices\Api\ResourcesApi as InternationalRatesApi;
use USPS\InternationalPrices\Configuration as InternationalPricesConfiguration;
use USPS\InternationalPrices\Model\BaseRatesQuery as InternationalBaseRatesQuery;
use USPS\Labels\Api\ResourcesApi as DomesticLabelsApi;
use USPS\Labels\Configuration as DomesticLabelsConfiguration;
use USPS\Labels\Model\DomesticLabelAddress;
use USPS\Labels\Model\DomesticLabelToAddress;
use USPS\Labels\Model\DomesticPackageDescription;
use USPS\Labels\Model\ImageInfo as DomesticImageInfo;
use USPS\Labels\Model\LabelRequest as DomesticLabelRequest;
use USPS\OAuthClientCredentials\Api\ResourcesApi as OAuthResourcesApi;
use USPS\OAuthClientCredentials\Configuration as OAuthConfiguration;
use USPS\OAuthClientCredentials\Model\ClientCredentials;

/**
 * Class ModelExtensionUsps
 *
 * @property AWeight                  $weight
 * @property ModelLocalisationCountry $model_localisation_country
 */
class ModelExtensionUsps extends Model
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

    public function getQuote($address)
    {
        $this->session->data['usps_data'] = [];
        $this->session->data['usps_parcel_data'] = [];

        //create new instance of language for case when model called from admin-side
        $language = new ALanguage($this->registry, $this->language->getLanguageCode(), 0);
        $language->load($language->language_details['directory']);
        $language->load('usps/usps');
        $country = [];
        $weight = 0.001;
        $cart_weight = 0;
        if (!$this->config->get('usps_status')) {
            return false;
        }

        $this->load->model('localisation/country');
        if (!$this->config->get('usps_location_id')) {
            $status = true;
        } else {
            $query = $this->db->query(
                "SELECT *
                 FROM " . $this->db->table('zones_to_locations') . "
                 WHERE location_id = '" . (int)$this->config->get('usps_location_id') . "'
                     AND country_id = '" . (int)$address['country_id'] . "'
                     AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')"
            );
            $status = (bool)$query->num_rows;
        }

        //load all countries and codes
        $countries = $this->model_localisation_country->getCountries();
        $country = array_column($countries, 'name', 'iso_code_2');

        if ($status && !has_value($country[$address['iso_code_2']])) {
            $status = false;
        }

        if (!$status) {
            return [];
        }

        $quote_data = [];

        //build array with cost for shipping
        // ids of products without special shipping cost
        $generic_product_ids = $free_shipping_ids = $shipping_price_ids = [];
        // total shipping cost of product with fixed shipping price
        $shipping_price_cost = 0;
        $cart_products = $this->cart->getProducts();
        foreach ($cart_products as $product) {
            //(exclude free shipping products)
            if ($product['free_shipping']) {
                $free_shipping_ids[] = $product['product_id'];
                continue;
            }
            if ($product['shipping_price'] > 0) {
                $shipping_price_ids[] = $product['product_id'];
                $shipping_price_cost += $product['shipping_price'] * $product['quantity'];
            }
            $generic_product_ids[] = $product['product_id'];
        }
        //convert fixed prices to USD
        $shipping_price_cost = $this->currency->convert(
            $shipping_price_cost,
            $this->config->get('config_currency'),
            'USD'
        );

        if ($generic_product_ids) {
            $api_weight_product_ids = array_diff($generic_product_ids, $shipping_price_ids);
            //WHEN ONLY PRODUCTS WITH FIXED SHIPPING PRICES ARE IN BASKET
            if (!$api_weight_product_ids) {
                $cost = $shipping_price_cost;
                $quote_data = [
                    'usps' => [
                        'id'           => 'usps.usps',
                        'title'        => $language->get('text_title'),
                        'cost'         => $this->currency->convert(
                            $cost,
                            'USD',
                            $this->config->get('config_currency')
                        ),
                        'tax_class_id' => $this->config->get('usps_tax_class_id'),
                        'text'         => $this->currency->format(
                            $this->tax->calculate(
                                $this->currency->convert(
                                    $cost,
                                    'USD',
                                    $this->currency->getCode()
                                ),
                                $this->config->get('usps_tax_class_id'),
                                $this->config->get('config_tax')
                            ),
                            $this->currency->getCode(),
                            1.0000000
                        ),
                    ],
                ];
                return [
                    'id'         => 'usps',
                    'title'      => $language->get('text_title'),
                    'quote'      => $quote_data,
                    'sort_order' => $this->config->get('usps_sort_order'),
                    'error'      => '',
                ];
            }
        } else {
            $api_weight_product_ids = $shipping_price_ids;
        }

        if ($api_weight_product_ids) {
            //do trick to get int instead unit name
            //TODO: change this in 2.0
            $cart_weight_class_id = $this->weight->getClassIDByUnit($this->config->get('config_weight_class'));
            $cart_weight = $this->cart->getWeight($api_weight_product_ids);
            $weight = $this->weight->convertByID(
                //get weight non-free shipping products only
                $cart_weight,
                $cart_weight_class_id,
                $this->weight->getClassIDByCode('PUND')
            );
            $weight = max($weight, 0.001);
        }

        $pounds = floor($weight);
        $ounces = round(16 * ($weight - $pounds), 2); // max 5 digits
        $postcode = str_replace(' ', '', $address['postcode']);
        $this->storeParcelData(
            [
                'height'         => (float)$this->config->get('usps_height'),
                'width'          => (float)$this->config->get('usps_width'),
                'depth'          => (float)$this->config->get('usps_length'),
                'dimension_unit' => 'in',
                'weight'         => (float)$weight,
                'weight_unit'    => 'lb',
            ]
        );

        // FOR CASE WHEN ONLY FREE SHIPPING PRODUCTS IN BASKET
        if (!$api_weight_product_ids && $free_shipping_ids) {
            $quote_data = [
                'usps' => [
                    'id'           => 'usps.usps',
                    'title'        => $language->get('text_' . ($address['iso_code_2'] == 'US'
                        ? $this->config->get('usps_free_domestic_method')
                        : $this->config->get('usps_free_international_method'))),
                    'cost'         => 0.0,
                    'tax_class_id' => $this->config->get('usps_tax_class_id'),
                    'text'         => $language->get('text_free'),
                ],
            ];
            return [
                'id'         => 'usps',
                'title'      => $language->get('text_title'),
                'quote'      => $quote_data,
                'sort_order' => $this->config->get('usps_sort_order'),
                'error'      => '',
            ];
        }

        if (!$this->hasUspsV3Credentials()) {
            return [
                'id'         => 'usps',
                'title'      => $language->get('text_title'),
                'quote'      => [],
                'sort_order' => $this->config->get('usps_sort_order'),
                'error'      => 'USPS API v3 credentials are not configured.',
            ];
        }

        $uspsV3Result = $this->getV3Quotes(
            $address,
            $pounds,
            $ounces,
            $postcode,
            $generic_product_ids,
            $shipping_price_cost
        );
        if ($uspsV3Result['quote_data']) {
            $title = $language->get('text_title');
            if ($this->config->get('usps_display_weight')) {
                $title .= ' (' . $language->get('text_weight')
                    . ' ' . $this->weight->formatByID(
                        $cart_weight,
                        $this->weight->getClassIDByUnit($this->config->get('config_weight_class'))
                    ) . ')';
            }
            return [
                'id'         => 'usps',
                'title'      => $title,
                'quote'      => $uspsV3Result['quote_data'],
                'sort_order' => $this->config->get('usps_sort_order'),
                'error'      => false,
            ];
        }

        return [
            'id'         => 'usps',
            'title'      => $language->get('text_title'),
            'quote'      => [],
            'sort_order' => $this->config->get('usps_sort_order'),
            'error'      => $uspsV3Result['error'],
        ];
    }

    private function hasUspsV3Credentials()
    {
        return (bool)($this->config->get('usps_client_id') && $this->config->get('usps_client_secret'));
    }

    private function getUspsApiBaseUrl()
    {
        return $this->isDeveloperEnvironment()
            ? 'https://apis-tem.usps.com'
            : 'https://apis.usps.com';
    }

    private function isDeveloperEnvironment()
    {
        $value = $this->config->get('usps_api_environment');
        if (is_bool($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return (int)$value === 1;
        }
        $normalized = strtolower(trim((string)$value));
        return in_array($normalized, ['1', 'true', 'yes', 'on', 'tem', 'developer', 'test'], true);
    }

    private function getEnvironmentCode()
    {
        return $this->isDeveloperEnvironment() ? 'developer' : 'production';
    }

    private function normalizeZipCode($zip)
    {
        return substr(preg_replace('/[^0-9]/', '', (string)$zip), 0, 5);
    }

    private function getOauthToken($baseUrl)
    {
        $cacheKey = $this->getOauthTokenCacheKey();
        $cached = $this->cache->pull($cacheKey);
        if (is_array($cached) && !empty($cached['access_token']) && !empty($cached['expires_at'])) {
            if ((int)$cached['expires_at'] > (time() + 120)) {
                return ['token' => (string)$cached['access_token'], 'error' => ''];
            }
        }

        $clientId = (string)$this->config->get('usps_client_id');
        $clientSecret = (string)$this->config->get('usps_client_secret');
        try {
            $config = OAuthConfiguration::getDefaultConfiguration()
                ->setHost($baseUrl . '/oauth2/v3');
            $api = new OAuthResourcesApi(
                new Client(['timeout' => 20]),
                $config
            );
            $tokenRequest = new ClientCredentials([
                'grant_type'    => 'client_credentials',
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
            ]);
            $response = $api->postToken($tokenRequest);
            $token = (string)$response->getAccessToken();
            if (!$token) {
                return ['token' => '', 'error' => 'USPS OAuth token was not returned.'];
            }
            $expiresIn = (int)$response->getExpiresIn();
            if ($expiresIn <= 0) {
                $expiresIn = 3000;
            }
            $this->cache->push(
                $cacheKey,
                [
                    'access_token' => $token,
                    'expires_at'   => time() + $expiresIn,
                ]
            );

            return ['token' => $token, 'error' => ''];
        } catch (\Throwable $e) {
            return ['token' => '', 'error' => $this->formatUspsSdkError($e, 'USPS OAuth error')];
        }
    }

    private function getOauthTokenCacheKey()
    {
        $storeId = (int)$this->config->get('config_store_id');
        $environment = $this->getEnvironmentCode();
        $clientId = (string)$this->config->get('usps_client_id');
        return 'usps.oauth_token.' . md5($storeId . '|' . $environment . '|' . $clientId);
    }

    private function getV3Quotes($address, $pounds, $ounces, $postcode, $genericProductIds, $shippingPriceCost)
    {
        $quoteData = [];
        $lastError = '';
        $baseUrl = $this->getUspsApiBaseUrl();
        $tokenData = $this->getOauthToken($baseUrl);
        if (!$tokenData['token']) {
            return ['quote_data' => [], 'error' => $tokenData['error']];
        }

        $weight = max(round((float)$pounds + ((float)$ounces / 16), 3), 0.001);
        $length = max((float)$this->config->get('usps_length'), 0.1);
        $width = max((float)$this->config->get('usps_width'), 0.1);
        $height = max((float)$this->config->get('usps_height'), 0.1);
        $this->storeQuoteContext($address, $weight, $length, $width, $height);
        $originZip = $this->normalizeZipCode($this->config->get('usps_postcode'));
        if (!$originZip) {
            return ['quote_data' => [], 'error' => 'USPS origin ZIP is not configured.'];
        }

        $requests = $address['iso_code_2'] === 'US'
            ? $this->getDomesticRequests($originZip, $postcode, $weight, $length, $width, $height)
            : $this->getInternationalRequests($originZip, $postcode, $address['iso_code_2'], $weight, $length, $width, $height);

        $isDomestic = $address['iso_code_2'] === 'US';
        $httpClient = new Client(['timeout' => 20]);
        if (!$isDomestic) {
            $config = InternationalPricesConfiguration::getDefaultConfiguration()
                ->setHost($baseUrl . '/international-prices/v3')
                ->setAccessToken($tokenData['token']);
            $apiClient = new InternationalRatesApi(
                $httpClient,
                $config
            );
        }

        foreach ($requests as $classId => $requestData) {
            try {
                if ($isDomestic) {
                    $responseRaw = $httpClient->post(
                        $requestData['endpoint'],
                        [
                            'headers' => [
                                'Authorization' => 'Bearer ' . $tokenData['token'],
                                'Accept'        => 'application/json',
                                'Content-Type'  => 'application/json',
                            ],
                            'json'    => $requestData['payload'],
                        ]
                    );
                    $responseData = json_decode((string)$responseRaw->getBody(), true);
                    if (!is_array($responseData)) {
                        throw new \RuntimeException('USPS v3 response is not valid JSON.');
                    }
                } else {
                    $query = $this->createInternationalRateQuery($requestData['payload']);
                    $response = $apiClient->getInternationalBaseRatesSearch($query);
                }
            } catch (\Throwable $e) {
                $lastError = $this->formatUspsSdkError($e);
                continue;
            }

            if ($this->config->get('usps_debug')) {
                $this->log->write('USPS V3 SENT: ' . $requestData['endpoint'] . ' ' . json_encode($requestData['payload']));
                $this->log->write(
                    'USPS V3 RECV: '
                    . ($isDomestic ? json_encode($responseData) : (string)$response)
                );
            }

            if ($isDomestic) {
                $rates = (array)($responseData['rates'] ?? []);
                $rate = $rates ? reset($rates) : null;
                $amount = $responseData['totalBasePrice'] ?? null;
                if ($amount === null && is_array($rate)) {
                    $amount = $rate['price'] ?? null;
                }
            } else {
                $rates = (array)$response->getRates();
                $rate = $rates ? reset($rates) : null;
                $amount = $response->getTotalBasePrice();
                if ($amount === null && is_object($rate)) {
                    $amount = $rate->getPrice();
                }
            }
            if ($amount === null) {
                $lastError = 'USPS v3 response does not contain a rate.';
                continue;
            }

            $cost = (float)$amount;
            if ($genericProductIds) {
                $cost += (float)$shippingPriceCost;
            }

            $titleFromRate = $isDomestic
                ? (string)($rate['description'] ?? '')
                : (is_object($rate) ? (string)$rate->getDescription() : '');
            $title = $titleFromRate
                ?: (string)(USPS_CLASSES[$isDomestic ? 'domestic' : 'international'][$classId] ?? ('USPS ' . $classId));
            $quoteData[$classId] = [
                'id'           => 'usps.' . $classId,
                'title'        => $title,
                'cost'         => $this->currency->convert(
                    $cost,
                    'USD',
                    $this->config->get('config_currency')
                ),
                'tax_class_id' => $this->config->get('usps_tax_class_id'),
                'text'         => $this->currency->format(
                    $this->tax->calculate(
                        $this->currency->convert(
                            $cost,
                            'USD',
                            $this->currency->getCode()
                        ),
                        $this->config->get('usps_tax_class_id'),
                        $this->config->get('config_tax')
                    ),
                    $this->currency->getCode(),
                    1.0000000
                ),
            ];
        }

        return ['quote_data' => $quoteData, 'error' => $quoteData ? '' : $lastError];
    }

    private function createDomesticRateQuery(array $payload)
    {
        return new DomesticBaseRatesQuery([
            'origin_zip_code'                => $payload['originZIPCode'],
            'destination_zip_code'           => $payload['destinationZIPCode'],
            'weight'                         => $payload['weight'],
            'length'                         => $payload['length'],
            'width'                          => $payload['width'],
            'height'                         => $payload['height'],
            'mail_class'                     => $payload['mailClass'],
            'processing_category'            => $payload['processingCategory'],
            'destination_entry_facility_type' => $payload['destinationEntryFacilityType'],
            'rate_indicator'                 => $payload['rateIndicator'],
            'price_type'                     => $payload['priceType'],
            'mailing_date'                   => $payload['mailingDate'],
        ]);
    }

    private function createInternationalRateQuery(array $payload)
    {
        return new InternationalBaseRatesQuery([
            'origin_zip_code'                => $payload['originZIPCode'],
            'foreign_postal_code'            => $payload['foreignPostalCode'],
            'destination_country_code'       => $payload['destinationCountryCode'],
            'destination_entry_facility_type' => $payload['destinationEntryFacilityType'],
            'weight'                         => $payload['weight'],
            'length'                         => $payload['length'],
            'width'                          => $payload['width'],
            'height'                         => $payload['height'],
            'mail_class'                     => $payload['mailClass'],
            'processing_category'            => $payload['processingCategory'],
            'rate_indicator'                 => $payload['rateIndicator'],
            'price_type'                     => $payload['priceType'],
            'mailing_date'                   => $payload['mailingDate'],
        ]);
    }

    private function getDomesticRequests($originZip, $destinationPostcode, $weight, $length, $width, $height)
    {
        $requests = [];
        $destinationZip = $this->normalizeZipCode($destinationPostcode);
        if (!$destinationZip) {
            return $requests;
        }
        $baseUrl = $this->getUspsApiBaseUrl();
        foreach (self::USPS_V3_DOMESTIC_CLASS_MAP as $classId => $service) {
            if (!$this->config->get('usps_domestic_' . $classId)) {
                continue;
            }
            $requests[$classId] = [
                'endpoint' => $baseUrl . '/prices/v3/base-rates/search',
                'payload'  => [
                    'originZIPCode'               => $originZip,
                    'destinationZIPCode'          => $destinationZip,
                    'weight'                      => $weight,
                    'length'                      => $length,
                    'width'                       => $width,
                    'height'                      => $height,
                    'mailClass'                   => $service['mailClass'],
                    'processingCategory'          => $service['processingCategory'],
                    'destinationEntryFacilityType' => 'NONE',
                    'rateIndicator'               => 'DR',
                    'priceType'                   => 'RETAIL',
                    'mailingDate'                 => date('Y-m-d'),
                ],
            ];
        }
        return $requests;
    }

    private function getInternationalRequests($originZip, $destinationPostcode, $destinationCountryCode, $weight, $length, $width, $height)
    {
        $requests = [];
        $baseUrl = $this->getUspsApiBaseUrl();
        $foreignPostalCode = strtoupper(substr(preg_replace('/\s+/', '', (string)$destinationPostcode), 0, 10));
        foreach (self::USPS_V3_INTL_CLASS_MAP as $classId => $service) {
            if (!$this->config->get('usps_international_' . $classId)) {
                continue;
            }
            $requests[$classId] = [
                'endpoint' => $baseUrl . '/international-prices/v3/base-rates/search',
                'payload'  => [
                    'originZIPCode'               => $originZip,
                    'foreignPostalCode'           => $foreignPostalCode,
                    'destinationCountryCode'      => $destinationCountryCode,
                    'destinationEntryFacilityType' => 'NONE',
                    'weight'                      => $weight,
                    'length'                      => $length,
                    'width'                       => $width,
                    'height'                      => $height,
                    'mailClass'                   => $service['mailClass'],
                    'processingCategory'          => $service['processingCategory'],
                    'rateIndicator'               => 'SP',
                    'priceType'                   => 'RETAIL',
                    'mailingDate'                 => date('Y-m-d'),
                ],
            ];
        }
        return $requests;
    }

    public function saveOrderShippingData($order_id, $data)
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

    public function getOrderShippingData($order_id)
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

        $baseUrl = $this->getUspsApiBaseUrl();
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
            $this->errors[] = $this->formatUspsSdkError($e, 'USPS Label API error');
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

    protected function storeParcelData(array $data)
    {
        $this->session->data['usps_parcel_data'] = array_merge((array)$this->session->data['usps_parcel_data'], $data);
    }

    private function storeQuoteContext(array $address, float $weight, float $length, float $width, float $height)
    {
        $this->session->data['usps_data']['toAddress'] = [
            'name'               => trim((string)$address['firstname'] . ' ' . (string)$address['lastname']),
            'street_address'     => trim((string)$address['address_1']),
            'secondary_address'  => trim((string)$address['address_2']),
            'city'               => (string)$address['city'],
            'state'              => (string)$address['zone_code'],
            'zip_code'           => $this->normalizeZipCode($address['postcode']),
            'country_code'       => strtoupper((string)$address['iso_code_2']),
        ];
        $this->session->data['usps_data']['fromAddress'] = [
            'name'               => (string)$this->config->get('store_name'),
            'street_address'     => (string)$this->config->get('config_address'),
            'city'               => (string)$this->config->get('config_city'),
            'state'              => (string)$this->config->get('config_zone_code'),
            'zip_code'           => $this->normalizeZipCode(
                $this->config->get('usps_postcode') ?: $this->config->get('config_postcode')
            ),
            'country_code'       => strtoupper((string)$this->config->get('config_country_iso_code_2') ?: 'US'),
            'phone'              => preg_replace('/\D+/', '', (string)$this->config->get('config_telephone')),
            'email'              => (string)$this->config->get('store_main_email'),
        ];
        $this->session->data['usps_data']['package'] = [
            'weight' => $weight,
            'length' => $length,
            'width'  => $width,
            'height' => $height,
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

    private function formatUspsSdkError(\Throwable $e, $prefix = 'USPS API error')
    {
        $message = trim((string)$e->getMessage());
        $statusCode = (int)$e->getCode();
        $responseBody = '';

        if (method_exists($e, 'getResponseBody')) {
            $responseBody = (string)$e->getResponseBody();
        }

        // Guzzle RequestException path (Payments API call uses Guzzle directly).
        if ($responseBody === '' && method_exists($e, 'getResponse')) {
            $response = $e->getResponse();
            if ($response) {
                $statusCode = (int)$response->getStatusCode();
                $body = $response->getBody();
                if (is_object($body) && method_exists($body, '__toString')) {
                    $responseBody = (string)$body;
                }
            }
        }

        if ($responseBody !== '') {
            if (is_object($responseBody) && method_exists($responseBody, '__toString')) {
                $responseBody = (string)$responseBody;
            }
            if (is_string($responseBody) && $responseBody !== '') {
                $json = json_decode($responseBody, true);
                if (is_array($json)) {
                    $err = $json['error'] ?? [];
                    $errorText = '';
                    if (is_array($err)) {
                        $errorText = trim((string)($err['message'] ?? $err['description'] ?? ''));
                    } elseif (is_scalar($err)) {
                        $errorText = trim((string)$err);
                    }
                    $message = $errorText
                        ?: (string)($json['message'] ?? $json['error_description'] ?? '')
                        ?: $message;
                }
            }
        }
        $error = $prefix;
        if ($statusCode > 0) {
            $error .= ' HTTP ' . $statusCode;
        }
        if ($message !== '') {
            $error .= ': ' . $message;
        }
        return $error;
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

        $payload = [
            'roles' => [
                [
                    'roleName'      => 'PAYER',
                    'CRID'          => $crid,
                    'MID'           => $mid,
                    'manifestMID'   => $manifestMid,
                    'accountType'   => 'EPS',
                    'accountNumber' => $accountNumber,
                ],
                [
                    'roleName'      => 'LABEL_OWNER',
                    'CRID'          => $crid,
                    'MID'           => $mid,
                    'manifestMID'   => $manifestMid,
                    'accountType'   => 'EPS',
                    'accountNumber' => $accountNumber,
                ],
            ],
        ];

        try {
            $response = (new Client(['timeout' => 20]))->post(
                $baseUrl . '/payments/v3/payment-authorization',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $oauthToken,
                        'Accept'        => 'application/json',
                        'Content-Type'  => 'application/json',
                    ],
                    'json'    => $payload,
                ]
            );

            $json = json_decode((string)$response->getBody(), true);
            $token = is_array($json) ? (string)($json['paymentAuthorizationToken'] ?? '') : '';
            if ($token === '') {
                return ['token' => '', 'error' => 'USPS Payment Authorization Token was not returned by USPS API.'];
            }
            return ['token' => $token, 'error' => ''];
        } catch (\Throwable $e) {
            return ['token' => '', 'error' => $this->formatUspsSdkError($e, 'USPS Payments API error')];
        }
    }
}
