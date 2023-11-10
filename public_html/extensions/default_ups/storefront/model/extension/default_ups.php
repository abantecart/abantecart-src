<?php

/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2021 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Licence details is bundled with this package in the file LICENSE.txt.
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

/**
 * Class ModelExtensionDefaultUps
 *
 * @property AWeight $weight
 * @property ALength $length
 */
class ModelExtensionDefaultUps extends Model
{
    protected $lang;

    function getQuote($address)
    {
        //create new instance of language for case when model called from admin-side
        $language = new ALanguage($this->registry, $this->language->getLanguageCode(), 0);
        $language->load($language->language_details['directory']);
        $language->load('default_ups/default_ups');
        $this->lang = $language;
        if ($this->config->get('default_ups_status')) {
            if (!$this->config->get('default_ups_location_id')) {
                $status = true;
            } else {
                $query = $this->db->query(
                    "SELECT *
                    FROM ".$this->db->table('zones_to_locations')."
                    WHERE location_id = '".(int) $this->config->get('default_ups_location_id')."'
                        AND country_id = '".(int) $address['country_id']."'
                        AND (zone_id = '".(int) $address['zone_id']."' OR zone_id = '0')"
                );
                if ($query->num_rows) {
                    $status = true;
                } else {
                    $status = false;
                }
            }
        } else {
            $status = false;
        }

        $method_data = [];
        if (!$status) {
            return $method_data;
        }

        $length = $width = $height = 0.00;
        $product_ids = [];
        $basic_products = $this->cart->basicShippingProducts();
        foreach ($basic_products as $product) {
            $product_ids[] = $product['product_id'];
            $length += $this->length->convert($product['length'], $product['length_class'], 'in');
            $width += $this->length->convert($product['width'], $product['length_class'], 'in');
            $height += $this->length->convert($product['height'], $product['length_class'], 'in');
        }

$this->config->set('default_ups_weight_class', 'LBS');
        $weight = $this->weight->convert(
            $this->cart->getWeight($product_ids),
            $this->config->get('config_weight_class'),
            $this->config->get('default_ups_weight_class')
        );

        $length = $length
            ? :
            $this->length->convert(
                $this->config->get('default_ups_length'),
                $this->config->get('config_length_class'),
                'in'
            );
        $width = $width
            ? :
            $this->length->convert(
                $this->config->get('default_ups_width'),
                $this->config->get('config_length_class'),
                'in'
            );
        $height = $height
            ? :
            $this->length->convert(
                $this->config->get('default_ups_height'),
                $this->config->get('config_length_class'),
                'in'
            );

        $use_width = $use_length = $use_height = 0;
##############################################################
        $accNumber = '54EA96';
        $clientId = 'IRWwra5OOdRMm4SpLSIb5bIAizGQByoEWguPdEl0ImUraB5X';
      //  $clientId = 'QULyQ5CvumWqPAtD0ZR37S8ESqrCV8zGWxnPb9QAJGOFf1Dk';
        $password = '1GQhyY13PoHUmZjL2kZ7yjkAltJ6mFVOdKrzQAmayidBTI8Uzy5fo4r0dJ3rpJfq';
       // $password = 'rAlRrfm8vGEVAwMHDe0sm6qgdIZ98oytmapGriKyMeAq3hN3Dsgrh4GHAJYAruEB';


        $config = \UPS\OAuthClientCredentials\Configuration::getDefaultConfiguration()
            ->setUsername($clientId)
            ->setPassword($password);

        $apiInstance = new \UPS\OAuthClientCredentials\Request\DefaultApi(
        // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
        // This is optional, `GuzzleHttp\Client` will be used as default.
            new \GuzzleHttp\Client(),
            $config
        );
        $grant_type = "client_credentials"; // string |
        $x_merchant_id = $accNumber; // string | Client merchant ID

        try {
            $result = $apiInstance->generateToken($grant_type, $x_merchant_id);
            $accessToken = $result['access_token'];
        } catch (UPS\OAuthClientCredentials\ApiException $e) {
            echo 'Exception when calling DefaultApi->generateToken: ', $e->getMessage(), PHP_EOL;
            echo 'Response Body: '.var_export($e->getResponseBody(), true);
        }


###############
        ///swagger client
       // Configure OAuth2 access token for authorization: oauth2
        $config = \UPS\Rating\Configuration::getDefaultConfiguration()->setAccessToken($accessToken);

        $apiInstance = new UPS\Rating\Request\DefaultApi(
        // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
        // This is optional, `GuzzleHttp\Client` will be used as default.
            new \GuzzleHttp\Client(
            ),
            $config
        );

        /** @var ModelLocalisationCountry $countryModel */
        $countryModel = $this->load->model('localisation/country');
        $country = $countryModel->getCountry($this->config->get('config_country_id'));
        /** @var ModelLocalisationZone $zoneModel */
        $zoneModel = $this->load->model('localisation/zone');
        $zone = $zoneModel->getZone($this->config->get('config_zone_id'));
        $fromAddress = new ShipperAddress(
            [
                'address_line' =>  $this->config->get('config_address'),
                'city' => $this->config->get('config_city'),
                'state_province_code' => $zone['code'],
                'postal_code' => $this->config->get('config_postcode'),
                'country_code' => $country['iso_code_2']
            ]
        );

        $toAddress = new \UPS\Rating\Rating\ShipToAddress(
            [
                'address_line' =>  $address['address_1'].' '. $address['address_2'],
                'city' => $address['city'],
                'state_province_code' => $address['zone_code'],
                'postal_code' => $address['postcode'],
                'country_code' => $address['iso_code_2']
            ]
        );


        $shipper = new \UPS\Rating\Rating\ShipmentShipper();
        $shipper->setAddress($fromAddress);
        $shipper->setShipperNumber('54EA96');
        $shipTo = new \UPS\Rating\Rating\ShipmentShipTo();
        $shipTo->setAddress($toAddress);

        $rateRequestShipment = new \UPS\Rating\Rating\RateRequestShipment();

        $rateRequestShipment->setShipper($shipper);
        $paymentDetails = new \UPS\Rating\Rating\ShipmentPaymentDetails();
        $paymentDetails->setShipmentCharge(
            [new \UPS\Rating\Rating\PaymentDetailsShipmentCharge(['type' => '01', 'bill_shipper' => ['AccountNumber' => '54EA96']])]
        );

        $rateRequestShipment
            ->setShipTo($shipTo)
            ->setPaymentDetails( $paymentDetails);

            $package = new \UPS\Rating\Rating\ShipmentPackage();
            $packageType = new \UPS\Rating\Rating\PackagePackagingType();
            $packageType->setCode('00');
            $package->setPackagingType($packageType);
            $dims = new \UPS\Rating\Rating\PackageDimensions();
            $dims->setLength($length);
            $dims->setWidth($width);
            $dims->setHeight($height);
            $dims->setUnitOfMeasurement( new \UPS\Rating\Rating\DimensionsUnitOfMeasurement(['code' => 'in', 'description' => 'inches']));
            $package->setDimensions( $dims );

            $pw = new \UPS\Rating\Rating\PackagePackageWeight( );
            $pw->setWeight( number_format($weight,4) );
            $pw->setUnitOfMeasurement(
                new \UPS\Rating\Rating\PackageWeightUnitOfMeasurement(
                    [
                        'code' => $this->config->get('default_ups_weight_class'),
                        'description' => $this->config->get('default_ups_weight_class')
                    ]
                )
            );
            $package->setPackageWeight($pw);


        $packages = [ $package ];
        $rateRequestShipment->setPackage( $packages);

        $rateRequest = new \UPS\Rating\Rating\RateRequest();
        $rateRequest->setShipment($rateRequestShipment);
        $rateRequestRequest = new \UPS\Rating\Rating\RateRequestRequest();
        $rateRequestRequest->setRequestOption('Shop');
        $rateRequest->setRequest($rateRequestRequest);

        $body = new \UPS\Rating\Rating\RATERequestWrapper();
        $body->setRateRequest($rateRequest);

        $version = 'v1';
        $requestoption = "Shop";
        $trans_id = "abantecart";
        $transaction_src = "testing";
        $additionalinfo = '';

        try {
            $result = $apiInstance->rate($body, $version, $requestoption, $trans_id, $transaction_src, $additionalinfo);
            $rated = $result->getRateResponse();

            foreach($rated['rated_shipment'] as $ratedShipment){
                /** @var \UPS\Rating\Rating\RateResponseRatedShipment */

                echo $language->get('text_us_origin_'.$ratedShipment['service']['code'],'default_ups/default_ups').": "
                .$ratedShipment['total_charges']['monetary_value'].$ratedShipment['total_charges']['currency_code']."<br>";
            }
        } catch (\UPS\Rating\ApiException $e) {
            echo 'wwwwwwwwwException when calling DefaultApi->rate: ', $e->getMessage(), PHP_EOL;
            //var_Dump($e->getResponseBody());
        }


        return;


####################################################################
        //$request = $this->_buildRequest($address, $weight, $length, $width, $height);

        $quote_data = $this->_processRequest($request);
        $error_msg = $quote_data['error_msg'];
        $quote_data = $quote_data['quote_data'];

        $special_ship_products = $this->cart->specialShippingProducts();
        $total_fixed_cost = 0;
        foreach ($special_ship_products as $product) {
            $weight = $this->weight->convert(
                $this->cart->getWeight([$product['product_id']]),
                $this->config->get('config_weight_class'),
                $this->config->get('default_usps_weight_class')
            );

            if ($product['width']) {
                $length_class_id = $this->length->getClassID('in');
                $use_width = $this->length->convertByID(
                    $product['length'],
                    $product['length_class'],
                    $length_class_id
                );
                $use_length = $this->length->convertByID(
                    $product['width'],
                    $product['length_class'],
                    $length_class_id
                );
                $use_height = $this->length->convertByID(
                    $product['height'],
                    $product['length_class'],
                    $length_class_id
                );
            }

            //check if free or fixed shipping
            $fixed_cost = -1;
            $new_quote_data = [];
            if ($product['free_shipping']) {
                $fixed_cost = 0;
            } else {
                if ($product['shipping_price'] > 0) {
                    $fixed_cost = $product['shipping_price'];
                    //If ship individually count every quantity
                    if ($product['ship_individually']) {
                        $fixed_cost = $fixed_cost * $product['quantity'];
                    }
                    $fixed_cost = $this->currency->convert(
                        $fixed_cost,
                        $this->config->get('config_currency'),
                        $this->currency->getCode()
                    );
                    $total_fixed_cost += $fixed_cost;
                } else {
                    $request = $this->_buildRequest($address, $weight, $use_width, $use_length, $use_height);
                    if ($request) {
                        $new_quote_data = $this->_processRequest($request);
                        $error_msg = $new_quote_data['error_msg'];
                        $new_quote_data = $new_quote_data['quote_data'];
                    }
                }
            }

            //merge data and accumulate shipping cost
            if ($quote_data) {
                foreach ($quote_data as $key => $value) {
                    if ($fixed_cost >= 0) {
                        $quote_data[$key]['cost'] = (float) $value['cost'] + $fixed_cost;
                    } else {
                        $quote_data[$key]['cost'] = (float) $value['cost'] + $new_quote_data[$key]['cost'];
                    }

                    $quote_data[$key]['text'] = $this->currency->format(
                        $this->tax->calculate(
                            $quote_data[$key]['cost'],
                            $this->config->get('default_usps_tax_class_id'),
                            $this->config->get('config_tax')
                        ),
                        $this->currency->getCode(),
                        1
                    );
                }
            } else {
                if ($new_quote_data) {
                    $quote_data = $new_quote_data;
                }
            }
        }

        $title = $language->get('text_title');
        //for case when only products with fixed shipping price are in the cart
        if (!$basic_products && $special_ship_products) {
            $quote_data = [
                'default_ups' => [
                    'id'           => 'default_ups.default_ups',
                    'title'        => $title,
                    'cost'         => $total_fixed_cost,
                    'tax_class_id' => 0,
                    'text'         => $this->currency->format($total_fixed_cost),
                ],
            ];
        }
        //when only products with free shipping are in the cart
        if (!$basic_products && $special_ship_products && !$total_fixed_cost) {
            $quote_data = [
                'default_ups' => [
                    'id'           => 'default_ups.default_ups',
                    'title'        => $title,
                    'cost'         => 0,
                    'tax_class_id' => 0,
                    'text'         => $language->get('text_free'),
                ],
            ];
        }

        if ($this->config->get('default_ups_display_weight')) {
            $title .= ' ('.$language->get('text_weight').' '.$this->weight->format(
                    $weight, $this->config->get(
                    'config_weight_class'
                )
                ).')';
        }

        return [
            'id'         => 'default_ups',
            'title'      => $title,
            'quote'      => $quote_data,
            'sort_order' => $this->config->get('default_ups_sort_order'),
            'error'      => $error_msg,
        ];
    }

    protected function _buildRequest($address, $weight, $length, $width, $height)
    {
        //set hardcoded inches because of API error
        $length_code = 'IN';
        $weight_code = strtoupper($this->length->getUnit($this->config->get('default_ups_weight')));

        if (strlen($this->config->get('default_ups_country')) != 2) {
            $error = new AError('UPS error: Wrong Country Code!');
            $error->toLog();
        }
        if (strlen($this->config->get('default_ups_state')) != 2) {
            $error = new AError('UPS error: Wrong State Code!');
            $error->toLog();
        }

        $xml = '<?xml version="1.0"?>';
        $xml .= '<AccessRequest xml:lang="en-US">';
        $xml .= '	<AccessLicenseNumber>'.$this->config->get('default_ups_key').'</AccessLicenseNumber>';
        $xml .= '	<UserId>'.$this->config->get('default_ups_username').'</UserId>';
        $xml .= '	<Password>'.$this->config->get('default_ups_password').'</Password>';
        $xml .= '</AccessRequest>';
        $xml .= '<?xml version="1.0"?>';
        $xml .= '<RatingServiceSelectionRequest xml:lang="en-US">';
        $xml .= '	<Request>';
        $xml .= '		<TransactionReference>';
        $xml .= '			<CustomerContext>Bare Bones Rate Request</CustomerContext>';
        $xml .= '			<XpciVersion>1.0001</XpciVersion>';
        $xml .= '		</TransactionReference>';
        $xml .= '		<RequestAction>Rate</RequestAction>';
        $xml .= '		<RequestOption>shop</RequestOption>';
        $xml .= '	</Request>';
        $xml .= '   <PickupType>';
        $xml .= '       <Code>'.$this->config->get('default_ups_pickup').'</Code>';
        $xml .= '   </PickupType>';

        if ($this->config->get('default_ups_country') == 'US' && $this->config->get('default_ups_pickup') == '11') {
            $xml .= '   <CustomerClassification>';
            $xml .= '       <Code>'.$this->config->get('default_ups_classification').'</Code>';
            $xml .= '   </CustomerClassification>';
        }

        $xml .= '	<Shipment>';
        $xml .= '		<Shipper>';
        $xml .= '			<Address>';
        $xml .= '				<City>'.$this->config->get('default_ups_city').'</City>';
        $xml .= '				<StateProvinceCode>'.$this->config->get('default_ups_state').'</StateProvinceCode>';
        $xml .= '				<CountryCode>'.$this->config->get('default_ups_country').'</CountryCode>';
        $xml .= '				<PostalCode>'.$this->config->get('default_ups_postcode').'</PostalCode>';
        $xml .= '			</Address>';
        $xml .= '		</Shipper>';
        $xml .= '		<ShipTo>';
        $xml .= '			<Address>';
        $xml .= ' 				<City>'.$address['city'].'</City>';
        $xml .= '				<StateProvinceCode>'.$address['zone_code'].'</StateProvinceCode>';
        $xml .= '				<CountryCode>'.$address['iso_code_2'].'</CountryCode>';
        $xml .= '				<PostalCode>'.$address['postcode'].'</PostalCode>';

        if ($this->config->get('default_ups_quote_type') == 'residential') {
            $xml .= '				<ResidentialAddressIndicator/>';
        }

        $xml .= '			</Address>';
        $xml .= '		</ShipTo>';
        $xml .= '		<ShipFrom>';
        $xml .= '			<Address>';
        $xml .= '				<City>'.$this->config->get('default_ups_city').'</City>';
        $xml .= '				<StateProvinceCode>'.$this->config->get('default_ups_state').'</StateProvinceCode>';
        $xml .= '				<CountryCode>'.$this->config->get('default_ups_country').'</CountryCode>';
        $xml .= '				<PostalCode>'.$this->config->get('default_ups_postcode').'</PostalCode>';
        $xml .= '			</Address>';
        $xml .= '		</ShipFrom>';

        $xml .= '		<Package>';
        $xml .= '			<PackagingType>';
        $xml .= '				<Code>'.$this->config->get('default_ups_packaging').'</Code>';
        $xml .= '			</PackagingType>';

        $xml .= '		    <Dimensions>';
        $xml .= '				<UnitOfMeasurement>';
        $xml .= '					<Code>'.$length_code.'</Code>';
        $xml .= '				</UnitOfMeasurement>';
        $xml .= '				<Length>'.$length.'</Length>';
        $xml .= '				<Width>'.$width.'</Width>';
        $xml .= '				<Height>'.$height.'</Height>';
        $xml .= '			</Dimensions>';

        $xml .= '			<PackageWeight>';
        $xml .= '				<UnitOfMeasurement>';
        $xml .= '					<Code>'.$weight_code.'</Code>';
        $xml .= '				</UnitOfMeasurement>';
        $xml .= '				<Weight>'.$weight.'</Weight>';
        $xml .= '			</PackageWeight>';

        if ($this->config->get('default_ups_insurance')) {
            $xml .= '           <PackageServiceOptions>';
            $xml .= '               <InsuredValue>';
            $xml .= '                   <CurrencyCode>'.$this->currency->getCode().'</CurrencyCode>';
            $xml .= '                   <MonetaryValue>'
                .$this->currency->format($this->cart->getTotal(), false, false, false)
                .'</MonetaryValue>';
            $xml .= '               </InsuredValue>';
            $xml .= '           </PackageServiceOptions>';
        }

        $xml .= '		</Package>';

        $xml .= '	</Shipment>';
        $xml .= '</RatingServiceSelectionRequest>';

        return $xml;
    }

    private function _processRequest($request = '')
    {
        $error_msg = '';
        /**
         * @var ALanguage $language
         */
        $language = $this->lang;
        if (!$this->config->get('default_ups_test')) {
            $url = 'https://onlinetools.ups.com/ups.app/xml/Rate';
        } else {
            $url = 'https://wwwcie.ups.com/ups.app/xml/Rate';
        }

        $service_code = [
            // US Origin
            'US'    => [
                '01' => $language->get('text_us_origin_01'),
                '02' => $language->get('text_us_origin_02'),
                '03' => $language->get('text_us_origin_03'),
                '07' => $language->get('text_us_origin_07'),
                '08' => $language->get('text_us_origin_08'),
                '11' => $language->get('text_us_origin_11'),
                '12' => $language->get('text_us_origin_12'),
                '13' => $language->get('text_us_origin_13'),
                '14' => $language->get('text_us_origin_14'),
                '54' => $language->get('text_us_origin_54'),
                '59' => $language->get('text_us_origin_59'),
                '65' => $language->get('text_us_origin_65'),
            ],
            // Canada Origin
            'CA'    => [
                '01' => $language->get('text_ca_origin_01'),
                '02' => $language->get('text_ca_origin_02'),
                '07' => $language->get('text_ca_origin_07'),
                '08' => $language->get('text_ca_origin_08'),
                '11' => $language->get('text_ca_origin_11'),
                '12' => $language->get('text_ca_origin_12'),
                '13' => $language->get('text_ca_origin_13'),
                '14' => $language->get('text_ca_origin_14'),
                '54' => $language->get('text_ca_origin_54'),
                '65' => $language->get('text_ca_origin_65'),
            ],
            // European Union Origin
            'EU'    => [
                '07' => $language->get('text_eu_origin_07'),
                '08' => $language->get('text_eu_origin_08'),
                '11' => $language->get('text_eu_origin_11'),
                '54' => $language->get('text_eu_origin_54'),
                '65' => $language->get('text_eu_origin_65'),
                // next five services Poland domestic only
                '82' => $language->get('text_eu_origin_82'),
                '83' => $language->get('text_eu_origin_83'),
                '84' => $language->get('text_eu_origin_84'),
                '85' => $language->get('text_eu_origin_85'),
                '86' => $language->get('text_eu_origin_86'),
            ],
            // Puerto Rico Origin
            'PR'    => [
                '01' => $language->get('text_eu_origin_01'),
                '02' => $language->get('text_eu_origin_02'),
                '03' => $language->get('text_eu_origin_03'),
                '07' => $language->get('text_ca_origin_07'),
                '08' => $language->get('text_ca_origin_08'),
                '14' => $language->get('text_eu_origin_14'),
                '54' => $language->get('text_other_origin_54'),
                '65' => $language->get('text_other_origin_65'),
            ],
            // Mexico Origin
            'MX'    => [
                '07' => $language->get('text_mx_origin_07'),
                '08' => $language->get('text_mx_origin_08'),
                '54' => $language->get('text_mx_origin_54'),
                '65' => $language->get('text_mx_origin_65'),
            ],
            // All other origins
            'other' => [
                // service code 7 seems to be gone after January 2, 2007
                '07' => $language->get('text_other_origin_07'),
                '08' => $language->get('text_other_origin_08'),
                '11' => $language->get('text_other_origin_11'),
                '54' => $language->get('text_other_origin_54'),
                '65' => $language->get('text_other_origin_65'),
            ],
        ];

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);

        $result = curl_exec($ch);
        if (!$result) {
            $this->log->write('UPS Curl Error: '.curl_error($ch));
        }
        curl_close($ch);
        $quote_data = [];

        if ($result) {
            $dom = new DOMDocument('1.0', 'UTF-8');
            $dom->loadXml($result);

            /**
             * @var $rating_service_selection_response DOMElement
             * @var $response                          DOMElement
             * @var $error                             DOMElement
             */
            $rating_service_selection_response = $dom->getElementsByTagName('RatingServiceSelectionResponse')->item(0);
            $response = $rating_service_selection_response->getElementsByTagName('Response')->item(0);

            $response_status_code = $response->getElementsByTagName('ResponseStatusCode');

            if ($response_status_code->item(0)->nodeValue != '1') {
                $error = $response->getElementsByTagName('Error')->item(0);
                $error_msg = $error->getElementsByTagName('ErrorCode')->item(0)->nodeValue;
                $error_msg .= ': '.$error->getElementsByTagName('ErrorDescription')->item(0)->nodeValue;
            } else {
                /**
                 * @var $rated_shipments DOMElement
                 */
                $rated_shipments = $rating_service_selection_response->getElementsByTagName('RatedShipment');
                foreach ($rated_shipments as $rated_shipment) {
                    /**
                     * @var $rated_shipment DOMElement
                     * @var $service        DOMElement
                     * @var $total_charges  DOMElement
                     */
                    $service = $rated_shipment->getElementsByTagName('Service')->item(0);
                    $code = $service->getElementsByTagName('Code')->item(0)->nodeValue;
                    $total_charges = $rated_shipment->getElementsByTagName('TotalCharges')->item(0);
                    $cost = $total_charges->getElementsByTagName('MonetaryValue')->item(0)->nodeValue;

                    if (!($code && $cost)) {
                        continue;
                    }

                    if ($this->config->get(
                        'default_ups_'.strtolower($this->config->get('default_ups_origin')).'_'.$code
                    )) {
                        $quote_data[$code] = [
                            'id'           => 'default_ups.'.$code,
                            'title'        => $service_code[$this->config->get('default_ups_origin')][$code],
                            'cost'         => $this->currency->convert($cost, 'USD', $this->currency->getCode()),
                            'tax_class_id' => $this->config->get('default_ups_tax_class_id'),
                            'text'         => $this->currency->format(
                                $this->tax->calculate(
                                    $this->currency->convert(
                                        $cost,
                                        'USD',
                                        $this->currency->getCode()
                                    ),
                                    $this->config->get('default_ups_tax_class_id'),
                                    $this->config->get('config_tax')
                                )
                            ),
                        ];
                    }
                }
            }
        }
        return ['quote_data' => $quote_data, 'error_msg' => $error_msg];
    }
}