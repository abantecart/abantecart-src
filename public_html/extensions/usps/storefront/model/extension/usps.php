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
require_once(DIR_EXT . 'usps' . DS . 'core' . DS . 'usps_token_service.php');
require_once(DIR_EXT . 'usps' . DS . 'core' . DS . 'usps_api_context.php');

use GuzzleHttp\Client;
use USPS\DomesticPrices\Api\ResourcesApi as DomesticRatesApi;
use USPS\DomesticPrices\Configuration as DomesticPricesConfiguration;
use USPS\DomesticPrices\Model\BaseRatesQuery as DomesticBaseRatesQuery;
use USPS\InternationalPrices\Api\ResourcesApi as InternationalRatesApi;
use USPS\InternationalPrices\Configuration as InternationalPricesConfiguration;
use USPS\InternationalPrices\Model\BaseRatesQuery as InternationalBaseRatesQuery;

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

    private function normalizeZipCode($zip)
    {
        return substr(preg_replace('/[^0-9]/', '', (string)$zip), 0, 5);
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
            return ['token' => '', 'error' => $this->formatUspsSdkError($e, 'USPS OAuth error')];
        }
    }

    private function getOauthTokenCacheKey()
    {
        $storeId = (int)$this->config->get('config_store_id');
        $clientId = (string)$this->config->get('usps_client_id');
        return UspsApiContext::buildHashKey(
            'usps.oauth_token.',
            [
                $storeId,
                UspsApiContext::getEnvironmentCode($this->config->get('usps_api_environment')),
                $clientId,
            ]
        );
    }

    private function getV3Quotes($address, $pounds, $ounces, $postcode, $genericProductIds, $shippingPriceCost)
    {
        $quoteData = [];
        $lastError = '';
        $baseUrl = UspsApiContext::getApiBaseUrl($this->config->get('usps_api_environment'));
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
        $baseUrl = UspsApiContext::getApiBaseUrl($this->config->get('usps_api_environment'));
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
        $baseUrl = UspsApiContext::getApiBaseUrl($this->config->get('usps_api_environment'));
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

    private function getTokenService()
    {
        return new UspsTokenService($this->cache, 20);
    }
}
