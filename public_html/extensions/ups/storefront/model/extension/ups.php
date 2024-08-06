<?php /*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
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
 */ /*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
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
 */ /*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
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
 */ /** @noinspection SqlResolve */

use GuzzleHttp\Client;
use UPS\Rating\ApiException;
use UPS\Rating\Configuration;
use UPS\Rating\Rating\DimensionsUnitOfMeasurement;
use UPS\Rating\Rating\PackageDimensions;
use UPS\Rating\Rating\PackagePackageWeight;
use UPS\Rating\Rating\PackagePackagingType;
use UPS\Rating\Rating\PackageWeightUnitOfMeasurement;
use UPS\Rating\Rating\PaymentDetailsShipmentCharge;
use UPS\Rating\Rating\RateRequest;
use UPS\Rating\Rating\RateRequestRequest;
use UPS\Rating\Rating\RateRequestShipment;
use UPS\Rating\Rating\RATERequestWrapper;
use UPS\Rating\Rating\RateResponseRatedShipment;
use UPS\Rating\Rating\ShipmentPackage;
use UPS\Rating\Rating\ShipmentPaymentDetails;
use UPS\Rating\Rating\ShipmentShipper;
use UPS\Rating\Rating\ShipmentShipTo;
use UPS\Rating\Rating\ShipperAddress;
use UPS\Rating\Rating\ShipToAddress;
use function ups\core\getUPSAccessToken;

/**
 * Class ModelExtensionUps
 *
 * @property AWeight $weight
 * @property ALength $length
 */
class ModelExtensionUps extends Model
{
    protected $lang, $lengthUnit, $weightUnit;
    protected $errors = [];

    protected $fromAddress = [];
    protected $toAddress = [];

    public function __construct($registry)
    {
        parent::__construct($registry);

        /** @var ModelLocalisationCountry $countryModel */
        $countryModel = $this->load->model('localisation/country');
        $country = $countryModel->getCountry($this->config->get('ups_country'));
        /** @var ModelLocalisationZone $zoneModel */
        $zoneModel = $this->load->model('localisation/zone');
        $zone = $zoneModel->getZone($this->config->get('ups_country_zone'));
        $this->fromAddress =
            [
                'address_line'        => $this->config->get('ups_address'),
                'city'                => $this->config->get('ups_city'),
                'state_province_code' => $zone['code'],
                'postal_code'         => $this->config->get('ups_postcode'),
                'country_code'        => $country['iso_code_2'],
                'phone'               => $this->config->get('ups_telephone')
            ];
    }

    public function getQuote($address)
    {
        $this->session->data['ups_data'] = [];

        //create new instance of language for case when model called from admin-side
        $language = new ALanguage($this->registry, $this->language->getLanguageCode(), 0);
        $language->load($language->language_details['directory']);
        $language->load('ups/ups');
        $this->lang = $language;
        if ($this->config->get('ups_status')) {
            if (!$this->config->get('ups_location_id')) {
                $status = true;
            } else {
                $query = $this->db->query(
                    "SELECT *
                    FROM " . $this->db->table('zones_to_locations') . "
                    WHERE location_id = '" . (int)$this->config->get('ups_location_id') . "'
                        AND country_id = '" . (int)$address['country_id'] . "'
                        AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')"
                );
                $status = (bool)$query->num_rows;
            }
        } else {
            $status = false;
        }

        $methodData = [];
        if (!$status) {
            return $methodData;
        }

        $this->lengthUnit = $this->length->getUnitByCode($this->config->get('ups_length_code'));
        $this->weightUnit = $this->weight->getUnitByCode($this->config->get('ups_weight_code'));

        $weight = $length = $width = $height = 0.00;
        $basicProducts = $this->cart->basicShippingProducts();
        if ($basicProducts) {
            foreach ($basicProducts as $product) {
                $l = $this->length->convert((float)$product['length'], $product['length_class'], $this->lengthUnit);
                $l = $l ?: (float)$this->config->get('ups_default_length');
                $length += $l * $product['quantity'];

                $w = $this->length->convert((float)$product['width'], $product['length_class'], $this->lengthUnit);
                $w = $w ?: (float)$this->config->get('ups_default_width');
                $width += $w * $product['quantity'];

                $h = $this->length->convert((float)$product['height'], $product['length_class'], $this->lengthUnit);
                $h = $h ?: (float)$this->config->get('ups_default_height');
                $height += $h * $product['quantity'];

                $wht = $this->weight->convert(
                    $this->cart->getWeight([$product['product_id']]),
                    $this->config->get('config_weight_class'),
                    $this->weightUnit
                );
                $weight += ($wht < 0.1 ? (float)$this->config->get('ups_default_weight') * $product['quantity'] : $wht);
            }
            //volume of parcel in base length units
            $volume = $this->length->convert($length, $this->lengthUnit, $this->config->get('config_length_class'))
                * $this->length->convert($width, $this->lengthUnit, $this->config->get('config_length_class'))
                * $this->length->convert($height, $this->lengthUnit, $this->config->get('config_length_class'));


            //skip when oversize or less
            if (($this->config->get('ups_min_volume') && $volume < $this->config->get('ups_min_volume'))
                ||
                ($this->config->get('ups_max_volume') && $volume > $this->config->get('ups_max_volume'))
            ) {
                return [];
            }

            $baseWeight = $this->weight->convert(
                $weight,
                $this->weightUnit,
                $this->config->get('config_weight_class')
            );

            //skip when overweight or less
            if (($this->config->get('ups_min_weight') && $baseWeight < $this->config->get('ups_min_weight'))
                ||
                ($this->config->get('ups_max_weight') && $baseWeight > $this->config->get('ups_max_weight'))
            ) {
                return [];
            }


            $quoteData = $this->processRequest($address, $weight, $length, $width, $height);

            //skip shipping by some reason - for example zero weight
            if ($quoteData === false) {
                return [];
            }

            $error_msg = $quoteData['error_msg'];
            $quoteData = $quoteData['quote_data'];
        }
        $specialShipProducts = $this->cart->specialShippingProducts();
        $totalFixedCost = 0;
        foreach ($specialShipProducts as $product) {
            $lengthClassId = $this->length->getClassID($this->lengthUnit);
            $specialLength = $this->length->convertByID($product['length'], $product['length_class'], $lengthClassId)
                ?: $this->config->get('ups_default_width');
            $specialWidth = $this->length->convertByID($product['width'], $product['length_class'], $lengthClassId)
                ?: $this->config->get('ups_default_length');
            $specialHeight = $this->length->convertByID($product['height'], $product['length_class'], $lengthClassId)
                ?: $this->config->get('ups_default_height');

            $wht = $this->weight->convert(
                $this->cart->getWeight([$product['product_id']]),
                $this->config->get('config_weight_class'),
                $this->weightUnit
            );
            $weight += ($wht < 0.1 ? (float)$this->config->get('ups_default_weight') * $product['quantity'] : $wht);

            //check if free or fixed shipping
            $fixedCost = -1;
            $newQuoteData = [];
            if ($product['free_shipping']) {
                $fixedCost = 0;
            } else {
                if ($product['shipping_price'] > 0) {
                    $fixedCost = $product['shipping_price'];
                    //If ship individually count every quantity
                    if ($product['ship_individually']) {
                        $fixedCost = $fixedCost * $product['quantity'];
                    }
                    $fixedCost = $this->currency->convert(
                        $fixedCost,
                        $this->config->get('config_currency'),
                        $this->currency->getCode()
                    );
                    $totalFixedCost += $fixedCost;
                } else {
                    //volume of parcel in base length units
                    $volume = $this->length->convert($specialLength, $this->lengthUnit, $this->config->get('config_length_class'))
                        * $this->length->convert($specialWidth, $this->lengthUnit, $this->config->get('config_length_class'))
                        * $this->length->convert($specialHeight, $this->lengthUnit, $this->config->get('config_length_class'));

                    //skip when oversize or less
                    if (($this->config->get('ups_min_volume') && $volume < $this->config->get('ups_min_volume'))
                        ||
                        ($this->config->get('ups_max_volume') && $volume > $this->config->get('ups_max_volume'))
                    ) {
                        return [];
                    }

                    $baseWeight = $this->weight->convert(
                        $weight,
                        $this->weightUnit,
                        $this->config->get('config_weight_class')
                    );

                    //skip when overweight or less
                    if (($this->config->get('ups_min_weight') && $baseWeight < $this->config->get('ups_min_weight'))
                        ||
                        ($this->config->get('ups_max_weight') && $baseWeight > $this->config->get('ups_max_weight'))
                    ) {
                        return [];
                    }

                    $newQuoteData = $this->processRequest($address, $weight, $specialWidth, $specialLength, $specialHeight);
                    if ($newQuoteData === false) {
                        return [];
                    }
                    $error_msg = $newQuoteData['error_msg'];
                    $newQuoteData = $newQuoteData['quote_data'];
                }
            }

            //merge data and accumulate shipping cost
            if ($quoteData) {
                foreach ($quoteData as $key => $value) {
                    $quoteData[$key]['cost'] = (float)$value['cost']
                        + ($fixedCost >= 0 ? $fixedCost : $newQuoteData[$key]['cost']);

                    $quoteData[$key]['text'] = $this->currency->format(
                        $this->tax->calculate(
                            $quoteData[$key]['cost'],
                            $this->config->get('ups_tax_class_id'),
                            $this->config->get('config_tax')
                        ),
                        $this->currency->getCode(),
                        1
                    );
                }
            } else {
                if ($newQuoteData) {
                    $quoteData = $newQuoteData;
                }
            }
        }

        $title = $language->get('ups_text_title');
        //for case when only products with fixed shipping price are in the cart
        if (!$basicProducts && $specialShipProducts) {
            $quoteData = [
                'ups' => [
                    'id'           => 'ups.ups',
                    'title'        => $title,
                    'cost'         => $totalFixedCost,
                    'tax_class_id' => 0,
                    'text'         => $this->currency->format($totalFixedCost),
                ],
            ];
        }
        //when only products with free shipping are in the cart
        if (!$basicProducts && $specialShipProducts && !$totalFixedCost) {
            $quoteData = [
                'ups' => [
                    'id'           => 'ups.ups',
                    'title'        => $title,
                    'cost'         => 0,
                    'tax_class_id' => 0,
                    'text'         => $language->get('text_free'),
                ],
            ];
        }

        if ($this->config->get('ups_display_weight')) {
            $title .= ' ('
                . $language->get('ups_text_weight')
                . ' '
                . $this->weight->format($weight, $this->config->get('config_weight_class'))
                . ')';
        }
        return [
            'id'         => 'ups',
            'title'      => $title,
            'quote'      => $quoteData,
            'sort_order' => $this->config->get('ups_sort_order'),
            'error'      => $error_msg,
        ];
    }

    /**
     * @param $address
     * @param $weight
     * @param $length
     * @param $width
     * @param $height
     * @return array|false
     * @throws AException
     * @throws \UPS\OAuthClientCredentials\ApiException
     */
    protected function processRequest($address, $weight, $length, $width, $height): array|false
    {
        $weight = (string)round($weight, 2);
        $length = (string)round($length, 2);
        $width = (string)round($width, 2);
        $height = (string)round($height, 2);

        $error_msg = '';
        /** @var ALanguage $language */
        $language = $this->lang;

        $accessToken = getUPSAccessToken(Registry::getInstance());
        $config = Configuration::getDefaultConfiguration()->setAccessToken($accessToken);
        $apiInstance = new UPS\Rating\Request\DefaultApi(new Client(), $config);

        $this->session->data['ups_data']['toAddress'] = [
            'name'                => $address['firstname'] . ' ' . $address['firstname'] . ' ' . $address['company'],
            'address_line'        => $address['address_1'] . ' ' . $address['address_2'],
            'city'                => $address['city'],
            'state_province_code' => $address['zone_code'],
            'postal_code'         => $address['postcode'],
            'country_code'        => $address['iso_code_2']
        ];

        $toAddressObj = new ShipToAddress(
            $this->session->data['ups_data']['toAddress']
        );

        $shipper = new ShipmentShipper();
        $this->session->data['ups_data']['fromAddress'] = $this->fromAddress;
        $shipper->setAddress(new ShipperAddress($this->fromAddress));
        $shipper->setShipperNumber($this->config->get('ups_account_number'));
        $shipTo = new ShipmentShipTo();
        $shipTo->setAddress($toAddressObj);

        $rateRequestShipment = new RateRequestShipment();

        $rateRequestShipment->setShipper($shipper);
        $paymentDetails = new ShipmentPaymentDetails();
        $paymentDetails->setShipmentCharge(
            [
                new PaymentDetailsShipmentCharge(
                    [
                        'type'         => '01', //01 = Transportation, 02 = Duties and Taxes
                        'bill_shipper' => [
                            'AccountNumber' => $this->config->get('ups_account_number')
                        ]
                    ]
                )
            ]
        );

        $rateRequestShipment
            ->setShipTo($shipTo)
            ->setPaymentDetails($paymentDetails);

        $package = new ShipmentPackage();
        $packageType = new PackagePackagingType();
        $packageType->setCode('00');
        $package->setPackagingType($packageType);
        $dims = new PackageDimensions();
        $dims->setLength($length);
        $dims->setWidth($width);
        $dims->setHeight($height);

        $dims->setUnitOfMeasurement(
            new DimensionsUnitOfMeasurement(
                [
                    'code'        => strtoupper($this->lengthUnit),
                    'description' => $this->lengthUnit
                ]
            )
        );
        $package->setDimensions($dims);
        $pw = new PackagePackageWeight();
        $pw->setWeight(number_format($weight, 2));
        $pw->setUnitOfMeasurement(
            new PackageWeightUnitOfMeasurement(
                [
                    'code'        => $this->weightUnit == 'kg' ? 'KGS' : 'LBS',
                    'description' => $this->weightUnit
                ]
            )
        );
        $package->setPackageWeight($pw);

        $this->session->data['ups_data']['packages'][] = [
            'length'     => $length,
            'width'      => $width,
            'height'     => $height,
            'lengthUnit' => $this->lengthUnit,
            'weight'     => $weight,
            'weightUnit' => $this->weightUnit
        ];
        $packages = [$package];
        $rateRequestShipment->setPackage($packages);

        $rateRequest = new RateRequest();
        $rateRequest->setShipment($rateRequestShipment);
        $rateRequestRequest = new RateRequestRequest();
        $rateRequestRequest->setRequestOption('Shop');
        $rateRequest->setRequest($rateRequestRequest);

        $body = new RATERequestWrapper();

        $body->setRateRequest($rateRequest);
        $version = 'v1';
        $requestoption = "Shop";
        $trans_id = "abantecart";
        $transaction_src = $this->config->get('config_url');
        $additionalinfo = ''; // leave empty!

        $quote_data = [];
        try {
            $result = $apiInstance->rate($body, $version, $requestoption, $trans_id, $transaction_src, $additionalinfo);
            $rated = $result->getRateResponse();
            foreach ($rated['rated_shipment'] as $ratedShipment) {
                /** @var RateResponseRatedShipment $ratedShipment */
                $code = $ratedShipment['service']['code'];
                $cost = $ratedShipment['total_charges']['monetary_value'];
                if (!($code && $cost)) {
                    continue;
                }
                $quote_data[$code] = [
                    'id'           => 'ups.' . $code,
                    'title'        => $language->get('ups_text_us_origin_' . $code, 'ups/ups') ?: $ratedShipment['service']['description'],
                    'cost'         => $this->currency->convert($cost, $ratedShipment['total_charges']['currency_code'], $this->currency->getCode()),
                    'tax_class_id' => $this->config->get('ups_tax_class_id'),
                    'text'         => $this->currency->format(
                        $this->tax->calculate(
                            $this->currency->convert(
                                $cost,
                                $ratedShipment['total_charges']['currency_code'],
                                $this->currency->getCode()
                            ),
                            $this->config->get('ups_tax_class_id'),
                            $this->config->get('config_tax')
                        )
                    ),
                ];
            }
        } catch (ApiException $e) {
            $rBody = json_decode($e->getResponseBody(), true);
            $codes = array_column($rBody['response']['errors'], 'code');
            if ($codes[0] == '111030') {
                $this->messages->saveWarning(
                    'UPS Shipping Method ignored by zero weight',
                    'Cart: ' . var_export($this->cart->getProducts(), true)
                );
                //skip method as unavailable
                return false;
            }

            foreach ($rBody['response']['errors'] as $err) {
                $error_msg .= $err['message'] . '(' . $err['code'] . ')';
            }
        }
        return ['quote_data' => $quote_data, 'error_msg' => $error_msg];
    }

    /**
     * @param int $order_id
     * @param array $data
     *
     * @return bool
     * @throws AException
     */
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
        $type_id = $result->row['type_id'];
        if (!$type_id) {
            $this->log->write(
                __CLASS__ . '. Cannot find order data type with name "shipping_data" in the table "order_data_types"'
            );
            return false;
        }

        $data = ['ups_data' => $data];

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
        $shipping_data = $shipping_data !== false ? array_merge((array)$shipping_data, $data) : $data;

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
        if (!$order_info || !$order_info['order_id']) {
            //$this->errors[] = __CLASS__.'::'.__FUNCTION__.' - Empty order info given';
            return false;
        }
        $order_id = (int)$order_info['order_id'];
        list(, $serviceCode) = explode('.', $order_info['shipping_method_key']);

        $order_shipping_data = $this->getOrderShippingData($order_id);
        if (!$order_shipping_data['data']['ups_data']['packages']) {
            //$this->errors[] = __CLASS__.'::'.__FUNCTION__.' - Unknown parcel data of order #'.$order_id;
            return false;
        }
        $shipmentData = $order_shipping_data['data']['ups_data'];

        try {

            $accessToken = getUPSAccessToken(Registry::getInstance());
            $config = \UPS\Shipping\Configuration::getDefaultConfiguration()->setAccessToken($accessToken);

            $apiInstance = new UPS\Shipping\Request\DefaultApi(
            // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
            // This is optional, `GuzzleHttp\Client` will be used as default.
                new GuzzleHttp\Client(),
                $config
            );

            $shipper = new \UPS\Shipping\Shipping\ShipmentShipper();
            $shipper->setAddress(
                new \UPS\Shipping\Shipping\ShipperAddress($shipmentData['fromAddress'])
            );

            $shipper->setPhone(
                new \UPS\Shipping\Shipping\ShipperPhone(
                    [
                        'number' => $this->config->get('ups_telephone')
                    ]
                )
            );
            $shipper->setShipperNumber($this->config->get('ups_account_number'));
            $shipper->setName($this->config->get('store_name'));
            $shipper->setAttentionName($this->config->get('store_name'));

            $shipmentObj = new \UPS\Shipping\Shipping\ShipmentRequestShipment();
            $shipmentObj->setService(
                new \UPS\Shipping\Shipping\ShipmentService(
                    [
                        'code'        => $serviceCode,
                        'description' => $this->language->get('ups_text_us_origin_' . $serviceCode, 'ups/ups')
                    ]
                )
            );

            $shipmentObj->setShipper($shipper);
            $paymentDetails = new \UPS\Shipping\Shipping\ShipmentPaymentInformation();
            $paymentDetails->setShipmentCharge(
                [
                    new \UPS\Shipping\Shipping\PaymentInformationShipmentCharge(
                        [
                            'type'         => '01', //01 = Transportation
                            'bill_shipper' => [
                                'AccountNumber' => $this->config->get('ups_account_number')
                            ]
                        ]
                    )
                ]
            );
            $shipmentObj->setPaymentInformation($paymentDetails);

            $shipTo = new \UPS\Shipping\Shipping\ShipmentShipTo();
            $shipTo->setName($shipmentData['toAddress']['name']);
            $shipTo->setAttentionName($shipmentData['toAddress']['name']);
            $toAddress = new \UPS\Shipping\Shipping\ShipToAddress($shipmentData['toAddress']);
            $shipTo->setAddress($toAddress);
            if ($order_info['telephone']) {
                $shipTo->setPhone(
                    new \UPS\Shipping\Shipping\ShipToPhone(
                        [
                            'number' => $order_info['telephone']
                        ]
                    )
                );
            }
            $shipmentObj->setShipTo($shipTo);

            $shipFrom = new \UPS\Shipping\Shipping\ShipmentShipFrom();
            $shipFrom->setName($shipmentData['toAddress']['name']);
            $shipFrom->setAttentionName($this->config->get('store_name'));
            $fromAddress = new \UPS\Shipping\Shipping\ShipFromAddress($shipmentData['fromAddress']);
            $shipFrom->setAddress($fromAddress);
            $shipFrom->setPhone(
                new \UPS\Shipping\Shipping\ShipFromPhone(
                    [
                        'number' => $this->config->get('ups_telephone')
                    ]
                )
            );
            $shipmentObj->setShipFrom($shipFrom);

            $packageList = [];
            foreach ($shipmentData['packages'] as $packageData) {
                $package = new \UPS\Shipping\Shipping\ShipmentPackage();
                $dims = new \UPS\Shipping\Shipping\PackageDimensions();
                $dims->setLength($packageData['length']);
                $dims->setWidth($packageData['width']);
                $dims->setHeight($packageData['height']);
                $dims->setUnitOfMeasurement(
                    new \UPS\Shipping\Shipping\DimensionsUnitOfMeasurement(
                        [
                            'code'        => strtoupper($packageData['lengthUnit']),
                            'description' => $packageData['lengthUnit']
                        ]
                    )
                );

                $package->setDimensions($dims);
                $pw = new \UPS\Shipping\Shipping\PackagePackageWeight();
                $pw->setWeight($packageData['weight']);
                $pw->setUnitOfMeasurement(
                    new \UPS\Shipping\Shipping\PackageWeightUnitOfMeasurement(
                        [
                            'code'        => $packageData['weightUnit'] == 'kg' ? 'KGS' : 'LBS',
                            'description' => $packageData['weightUnit']
                        ]
                    )
                );
                $package->setPackageWeight($pw);
                $packaging = new \UPS\Shipping\Shipping\PackagePackaging();
                $packaging->setCode($this->config->get('ups_packaging'));
                $package->setPackaging($packaging);

                $packageList[] = $package;
            }

            $shipmentObj->setPackage($packageList);
            $shipmentObj->setDescription('Order #' . $order_id);
            $shipmentRequest = new \UPS\Shipping\Shipping\ShipmentRequest();
            $shipmentRequest->setShipment($shipmentObj);
            $labelSpec = new \UPS\Shipping\Shipping\ShipmentRequestLabelSpecification();

//???? ERROR HERE. WRONG PARAMETERS
            $labelSpec->setLabelImageFormat(
                [
                    'label_image_format' => 'GIF',
                    'code'               => 'GIF'
                ]
            );
            $labelSpec->setHttpUserAgent("Mozilla/4.5");
            $shipmentRequest->setLabelSpecification($labelSpec);
            $body = new \UPS\Shipping\Shipping\SHIPRequestWrapper();
            $body->setShipmentRequest($shipmentRequest);

            $version = "2205";
            $trans_id = 'abantecart';
            $transaction_src = $this->config->get('config_url');

            $response = $apiInstance->shipment($body, $version, $trans_id, $transaction_src, $additionaladdressvalidation = null);

$this->log->write(var_export($response, true));


//            $shipmentData['label'] = [
//
//                    $shipmentId => [
//                        'mime' => $mime,
//                        'content' => (string)$response->LabelImage->OutputImage
//                    ]
//                ];
//
//                $this->saveOrderShippingData($order_id, $data);
            return true;
        } catch (\UPS\Shipping\ApiException $e) {
            $this->log->write(
                "UPS API Create Shipment Request Exception (Order ID '.$order_id.'): \n"
                . $e->getResponseBody()
            );
            return false;
        }

        return true;
    }

}