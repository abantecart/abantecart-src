<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Lincence details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if ( !defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}


class ModelExtensionDefaultUps extends Model {
	function getQuote($address) {
		$this->load->language('default_ups/default_ups');
		
		if ($this->config->get('default_ups_status')) {

      		if (!$this->config->get('default_ups_location_id')) {
        		$status = TRUE;
      		}else {
		        $query = $this->db->query("SELECT *
                                            FROM " . $this->db->table('zones_to_locations') . "
                                            WHERE location_id = '" . (int)$this->config->get('default_ups_location_id') . "'
                                                AND country_id = '" . (int)$address['country_id'] . "'
                                                AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
                if ($query->num_rows) {
                    $status = TRUE;
                } else {
                    $status = FALSE;
                }
      		}
		} else { 
			$status = FALSE;
		}

		$method_data = array();
		if (!$status) {
			return $method_data;
		}

        $basic_products = $this->cart->basicShippingProducts();
        foreach($basic_products as $product){
           $product_ids[] = $product['product_id'];
        }

		$weight = $this->weight->convert($this->cart->getWeight($product_ids), $this->config->get('config_weight_class'), $this->config->get('default_ups_weight_class'));

		$weight = ($weight < 0.1 ? 0.1 : $weight);

		$length = $this->length->convert($this->config->get('default_ups_length'), $this->config->get('config_length_class'), 'in');
		$width = $this->length->convert($this->config->get('default_ups_width'), $this->config->get('config_length_class'), 'in');
		$height = $this->length->convert($this->config->get('default_ups_height'), $this->config->get('config_length_class'), 'in');



		$request = $this->_buildRequest($address, $weight, $length, $width, $height);

        $quote_data = $this->_processRequest($request);
		$error_msg =  $quote_data['error_msg'];
        $quote_data =  $quote_data['quote_data'];

        $special_ship_products = $this->cart->specialShippingProducts();
		$total_fixed_cost = 0;
        foreach ($special_ship_products as $product) {
            $weight = $this->weight->convert($this->cart->getWeight( array($product['product_id']) ), $this->config->get('config_weight_class'), $this->config->get('default_usps_weight_class'));
            if ( $product['width'] ) {
                $length_class_id = $this->length->getClassID('in');
                $use_width = $this->length->convertByID($product['length'], $product['length_class'], $length_class_id);
                $use_length = $this->length->convertByID($product['width'], $product['length_class'], $length_class_id);
                $use_height = $this->length->convertByID($product['height'], $product['length_class'], $length_class_id);
            }

            //check if free or fixed shipping
            $fixed_cost = -1;
            $new_quote_data = array();
            if ($product['free_shipping']) {
                $fixed_cost = 0;
            } else if($product['shipping_price'] > 0) {
                $fixed_cost = $product['shipping_price'];
                //If ship individually count every quintaty
                if ($product['ship_individually']) {
                    $fixed_cost = $fixed_cost * $product['quantity'];
                }
                $fixed_cost = $this->currency->convert($fixed_cost, $this->config->get('config_currency'), $this->currency->getCode());
	            $total_fixed_cost +=$fixed_cost;
            } else {
                $request = $this->_buildRequest($address, $weight, $use_width, $use_length, $use_height);
                if ($request) {
                    $new_quote_data = $this->_processRequest( $request );
                    $error_msg =  $new_quote_data['error_msg'];
                    $new_quote_data =  $new_quote_data['quote_data'];
                }
            }

            //merge data and accumulate shipping cost
            if ( $quote_data) {
                foreach ($quote_data as $key => $value) {

                    if ($fixed_cost >= 0){
                        $quote_data[$key]['cost'] = (float)$quote_data[$key]['cost'] + $fixed_cost;
                    } else {
                        $quote_data[$key]['cost'] =  (float)$quote_data[$key]['cost'] + $new_quote_data[$key]['cost'];
                    }

                    $quote_data[$key]['text'] = $this->currency->format(
                                                                        $this->tax->calculate(
                                                                                            $quote_data[$key]['cost'],
                                                                                            $this->config->get('default_usps_tax_class_id'),
                                                                                            $this->config->get('config_tax')
                                                                                        ) ,
                                                                        $this->currency->getCode(),
                                                                        1
                    );
                }
            } else if ( $new_quote_data ) {
                $quote_data = $new_quote_data;
            }
        }


		$title = $this->language->get('text_title');

		//for case when only products with fixed shippig price are in the cart

		if(!$basic_products && $special_ship_products){
			$quote_data = array('default_ups' => array(
			                    'id'           => 'default_ups.default_ups',
			                    'title'        => $title,
			                    'cost'         => $total_fixed_cost,
			                    'tax_class_id' => 0,
			                    'text'         => $this->currency->format( $total_fixed_cost )
			));
		}
		//when only products with free shipping are in the cart
		if(!$basic_products && $special_ship_products && !$total_fixed_cost){
			$quote_data = array('default_ups' => array(
								                    'id'           => 'default_ups.default_ups',
								                    'title'        => $title,
								                    'cost'         => 0,
								                    'tax_class_id' => 0,
								                    'text'         => $this->language->get('text_free')
			));
		}




		if ($this->config->get('default_ups_display_weight')) {
			$title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class')) . ')';
		}

		$method_data = array(
			'id'         => 'default_ups',
			'title'      => $title,
			'quote'      => $quote_data,
			'sort_order' => $this->config->get('default_ups_sort_order'),
			'error'      => $error_msg
		);

		return $method_data;
	}

    private function _buildRequest($address, $weight,$length,$width,$height){
	    //set hardcoded inches beause of API error
        $length_code = 'IN';//strtoupper( $this->length->getUnit($this->config->get('default_ups_length_class') ) );
        $weight_code = strtoupper( $this->length->getUnit($this->config->get('default_ups_weight') ) );

	    if(strlen($this->config->get('default_ups_country'))!=2){
		    $error = new AError('UPS error: Wrong Country Code!');
		    $error->toLog()->toMessages();
	    }
	    if(strlen($this->config->get('default_ups_state'))!=2){
		    $error = new AError('UPS error: Wrong State Code!');
		    $error->toLog()->toMessages();
	    }


        $xml  = '<?xml version="1.0"?>';
        $xml .= '<AccessRequest xml:lang="en-US">';
        $xml .= '	<AccessLicenseNumber>' . $this->config->get('default_ups_key') . '</AccessLicenseNumber>';
        $xml .= '	<UserId>' . $this->config->get('default_ups_username') . '</UserId>';
        $xml .= '	<Password>' . $this->config->get('default_ups_password') . '</Password>';
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
        $xml .= '       <Code>' . $this->config->get('default_ups_pickup') . '</Code>';
        $xml .= '   </PickupType>';

        if ($this->config->get('default_ups_country') == 'US' && $this->config->get('default_ups_pickup') == '11') {
            $xml .= '   <CustomerClassification>';
            $xml .= '       <Code>' . $this->config->get('default_ups_classification') . '</Code>';
            $xml .= '   </CustomerClassification>';
        }

        $xml .= '	<Shipment>';
        $xml .= '		<Shipper>';
        $xml .= '			<Address>';
        $xml .= '				<City>' . $this->config->get('default_ups_city') . '</City>';
        $xml .= '				<StateProvinceCode>'. $this->config->get('default_ups_state') . '</StateProvinceCode>';
        $xml .= '				<CountryCode>' . $this->config->get('default_ups_country') . '</CountryCode>';
        $xml .= '				<PostalCode>' . $this->config->get('default_ups_postcode') . '</PostalCode>';
        $xml .= '			</Address>';
        $xml .= '		</Shipper>';
        $xml .= '		<ShipTo>';
        $xml .= '			<Address>';
        $xml .= ' 				<City>' . $address['city'] . '</City>';
        $xml .= '				<StateProvinceCode>' . $address['zone_code'] . '</StateProvinceCode>';
        $xml .= '				<CountryCode>' . $address['iso_code_2'] . '</CountryCode>';
        $xml .= '				<PostalCode>' . $address['postcode'] . '</PostalCode>';

        if ($this->config->get('default_ups_quote_type') == 'residential') {
            $xml .= '				<ResidentialAddressIndicator/>';
        }

        $xml .= '			</Address>';
        $xml .= '		</ShipTo>';
        $xml .= '		<ShipFrom>';
        $xml .= '			<Address>';
        $xml .= '				<City>' . $this->config->get('default_ups_city') . '</City>';
        $xml .= '				<StateProvinceCode>'. $this->config->get('default_ups_state') . '</StateProvinceCode>';
        $xml .= '				<CountryCode>' . $this->config->get('default_ups_country') . '</CountryCode>';
        $xml .= '				<PostalCode>' . $this->config->get('default_ups_postcode') . '</PostalCode>';
        $xml .= '			</Address>';
        $xml .= '		</ShipFrom>';

        $xml .= '		<Package>';
        $xml .= '			<PackagingType>';
        $xml .= '				<Code>' . $this->config->get('default_ups_packaging') . '</Code>';
        $xml .= '			</PackagingType>';

        $xml .= '		    <Dimensions>';
        $xml .= '				<UnitOfMeasurement>';
        $xml .= '					<Code>' . $length_code . '</Code>';
        $xml .= '				</UnitOfMeasurement>';
        $xml .= '				<Length>' . $length . '</Length>';
        $xml .= '				<Width>' . $width . '</Width>';
        $xml .= '				<Height>' . $height . '</Height>';
        $xml .= '			</Dimensions>';

        $xml .= '			<PackageWeight>';
        $xml .= '				<UnitOfMeasurement>';
        $xml .= '					<Code>' . $weight_code . '</Code>';
        $xml .= '				</UnitOfMeasurement>';
        $xml .= '				<Weight>' . $weight . '</Weight>';
        $xml .= '			</PackageWeight>';

        if ($this->config->get('default_ups_insurance')) {
            $xml .= '           <PackageServiceOptions>';
            $xml .= '               <InsuredValue>';
            $xml .= '                   <CurrencyCode>' . $this->currency->getCode() . '</CurrencyCode>';
            $xml .= '                   <MonetaryValue>' . $this->currency->format($this->cart->getTotal(), false, false, false) . '</MonetaryValue>';
            $xml .= '               </InsuredValue>';
            $xml .= '           </PackageServiceOptions>';
        }

        $xml .= '		</Package>';

        $xml .= '	</Shipment>';
        $xml .= '</RatingServiceSelectionRequest>';

        return $xml;
    }

    private function _processRequest($request=''){

        if (!$this->config->get('default_ups_test')) {
            $url = 'https://www.ups.com/ups.app/xml/Rate';
        } else {
            $url = 'https://wwwcie.ups.com/ups.app/xml/Rate';
        }

        $service_code = array(
            // US Origin
            'US' => array(
                '01' => $this->language->get('text_us_origin_01'),
                '02' => $this->language->get('text_us_origin_02'),
                '03' => $this->language->get('text_us_origin_03'),
                '07' => $this->language->get('text_us_origin_07'),
                '08' => $this->language->get('text_us_origin_08'),
                '11' => $this->language->get('text_us_origin_11'),
                '12' => $this->language->get('text_us_origin_12'),
                '13' => $this->language->get('text_us_origin_13'),
                '14' => $this->language->get('text_us_origin_14'),
                '54' => $this->language->get('text_us_origin_54'),
                '59' => $this->language->get('text_us_origin_59'),
                '65' => $this->language->get('text_us_origin_65')
            ),
            // Canada Origin
            'CA' => array(
                '01' => $this->language->get('text_ca_origin_01'),
                '02' => $this->language->get('text_ca_origin_02'),
                '07' => $this->language->get('text_ca_origin_07'),
                '08' => $this->language->get('text_ca_origin_08'),
                '11' => $this->language->get('text_ca_origin_11'),
                '12' => $this->language->get('text_ca_origin_12'),
                '13' => $this->language->get('text_ca_origin_13'),
                '14' => $this->language->get('text_ca_origin_14'),
                '54' => $this->language->get('text_ca_origin_54'),
                '65' => $this->language->get('text_ca_origin_65')
            ),
            // European Union Origin
            'EU' => array(
                '07' => $this->language->get('text_eu_origin_07'),
                '08' => $this->language->get('text_eu_origin_08'),
                '11' => $this->language->get('text_eu_origin_11'),
                '54' => $this->language->get('text_eu_origin_54'),
                '65' => $this->language->get('text_eu_origin_65'),
                // next five services Poland domestic only
                '82' => $this->language->get('text_eu_origin_82'),
                '83' => $this->language->get('text_eu_origin_83'),
                '84' => $this->language->get('text_eu_origin_84'),
                '85' => $this->language->get('text_eu_origin_85'),
                '86' => $this->language->get('text_eu_origin_86')
            ),
            // Puerto Rico Origin
            'PR' => array(
                '01' => $this->language->get('text_eu_origin_01'),
                '02' => $this->language->get('text_eu_origin_02'),
                '03' => $this->language->get('text_eu_origin_03'),
                '07' => $this->language->get('text_ca_origin_07'),
                '08' => $this->language->get('text_ca_origin_08'),
                '14' => $this->language->get('text_eu_origin_14'),
                '54' => $this->language->get('text_other_origin_54'),
                '65' => $this->language->get('text_other_origin_65')
            ),
            // Mexico Origin
            'MX' => array(
                '07' => $this->language->get('text_mx_origin_07'),
                '08' => $this->language->get('text_mx_origin_08'),
                '54' => $this->language->get('text_mx_origin_54'),
                '65' => $this->language->get('text_mx_origin_65')
            ),
            // All other origins
            'other' => array(
                // service code 7 seems to be gone after January 2, 2007
                '07' => $this->language->get('text_other_origin_07'),
                '08' => $this->language->get('text_other_origin_08'),
                '11' => $this->language->get('text_other_origin_11'),
                '54' => $this->language->get('text_other_origin_54'),
                '65' => $this->language->get('text_other_origin_65')
            )
        );

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);

        $result = curl_exec($ch);
	    if(!$result){
	        $this->log->write('UPS Curl Error: '.curl_error($ch));
	    }
        curl_close($ch);
        $quote_data = array();

        if ($result) {
            $dom = new DOMDocument('1.0', 'UTF-8');
            $dom->loadXml($result);

	        /**
	         * @var $rating_service_selection_response DOMElement
	         * @var $response DOMElement
	         * @var $error DOMElement
	         */
            $rating_service_selection_response = $dom->getElementsByTagName('RatingServiceSelectionResponse')->item(0);
	        $response = $rating_service_selection_response->getElementsByTagName('Response')->item(0);

            $response_status_code = $response->getElementsByTagName('ResponseStatusCode');

            if ($response_status_code->item(0)->nodeValue != '1') {
                $error = $response->getElementsByTagName('Error')->item(0);
                $error_msg = $error->getElementsByTagName('ErrorCode')->item(0)->nodeValue;
                $error_msg .= ': ' . $error->getElementsByTagName('ErrorDescription')->item(0)->nodeValue;
            } else {
	            /**
                 * @var $rated_shipments DOMElement
                 */
                $rated_shipments = $rating_service_selection_response->getElementsByTagName('RatedShipment');


                foreach ($rated_shipments as $rated_shipment) {
	                /**
                     * @var $rated_shipment DOMElement
                     * @var $service DOMElement
                     * @var $total_charges DOMElement
                     */
                    $service = $rated_shipment->getElementsByTagName('Service')->item(0);
                    $code = $service->getElementsByTagName('Code')->item(0)->nodeValue;
                    $total_charges = $rated_shipment->getElementsByTagName('TotalCharges')->item(0);
                    $cost = $total_charges->getElementsByTagName('MonetaryValue')->item(0)->nodeValue;

                    if (!($code && $cost)) {
                        continue;
                    }

                    if ($this->config->get('default_ups_' . strtolower($this->config->get('default_ups_origin')) . '_' . $code)) {
                        $quote_data[$code] = array(
                            'id'           => 'default_ups.' . $code,
                            'title'        => $service_code[$this->config->get('default_ups_origin')][$code],
                            'cost'         => $this->currency->convert($cost, 'USD', $this->currency->getCode()),
                            'tax_class_id' => $this->config->get('default_ups_tax_class_id'),
                            'text'         => $this->currency->format($this->tax->calculate($this->currency->convert($cost, 'USD', $this->currency->getCode()), $this->config->get('default_ups_tax_class_id'), $this->config->get('config_tax')))
                        );
                    }
                }
            }
        }
        return array('quote_data'=>$quote_data, 'error_msg'=>$error_msg);
    }
}