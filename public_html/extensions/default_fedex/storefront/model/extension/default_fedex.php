<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

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

class ModelExtensionDefaultFedex extends Model {
	function getQuote($address) {
        ini_set("soap.wsdl_cache_enabled", "0");

        $this->load->language('default_fedex/default_fedex');
		
		if ($this->config->get('default_fedex_status')) {

			$taxes = $this->tax->getTaxes((int)$address['country_id'], (int)$address['zone_id']);
		
      		if (!$this->config->get('default_fedex_location_id')) {
        		$status = TRUE;
      		} elseif ($taxes) {
        		$status = TRUE;
      		} else {
        		$status = FALSE;
      		}
		} else {
			$status = FALSE;
		}
        if($status){
            $products = $this->cart->basicShippingProducts();
            if($products){
                $quote_data = $this->_processRequest($address, $products);
                $error_msg =  $quote_data['error_msg'];
                $quote_data =  $quote_data['quote_data'];
            }
            $special_ship_products = $this->cart->specialShippingProducts();
            foreach ($special_ship_products as $product) {
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

                } else {
                    $new_quote_data = $this->_processRequest( $address,  array($product));
                    $error_msg .=  $new_quote_data['error_msg'];
                    $new_quote_data =  $new_quote_data['quote_data'];

                }

                //merge data and accumulate shipping cost
                if ( $quote_data) {
                    foreach ($quote_data as $key => $value) {
                        if ($fixed_cost >= 0){
                                $quote_data[$key]['cost'] = (float)$quote_data[$key]['cost'] + $fixed_cost;
                            } else {
                                $quote_data[$key]['cost'] =  (float)$quote_data[$key]['cost'] + $new_quote_data[$key]['cost'];
                            }

                        $quote_data[$key]['text'] = $this->currency->format($quote_data[$key]['cost'], $this->currency->getCode(),1 );
                    }
                } else if ( $new_quote_data ) {
                    $quote_data = $new_quote_data['quote_data'];
                    $error_msg .=  $new_quote_data['error_msg'];
                }
            }

        }

        if($quote_data || $error_msg){
            $title = $this->language->get('text_title');
            $method_data = array(
                'id'         => 'default_fedex',
                'title'      => $title,
                'quote'      => $quote_data,
                'sort_order' => $this->config->get('default_fedex_sort_order'),
                'error'      => $error_msg
            );
        }
        return $method_data;
	}



    private function _processRequest($address, $products){
        require_once(DIR_EXT . 'default_fedex/core/lib/fedex_func.php');

		if($this->config->get('default_fedex_test')){
        	$path_to_wsdl = DIR_EXT . 'default_fedex/core/lib/RateService_v9_test.wsdl';
		}else{
			$path_to_wsdl = DIR_EXT . 'default_fedex/core/lib/RateService_v9.wsdl';
		}
        $client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information


            //Fedex Key
            $fedex_key = $this->config->get('default_fedex_key');
            //Fedex Password
            $fedex_password = $this->config->get('default_fedex_password');
            //Fedex Meter Id
            $fedex_meter_id = $this->config->get('default_fedex_meter');
            //Fedex Account
            $fedex_account = $this->config->get('default_fedex_account');
            //Quote Type Residential or commercial
            $fedex_quote = $this->config->get('default_fedex_quote_type');

            if ($fedex_quote == 'residential'){
                $fedex_residential = true;
            } else {
                $fedex_residential = false;
            }

            $fedex_addr = $this->config->get('default_fedex_address');
            $fedex_city = $this->config->get('default_fedex_city');
            $fedex_state = $this->config->get('default_fedex_state');
            $fedex_zip = $this->config->get('default_fedex_zip');
            $fedex_country = $this->config->get('default_fedex_country');
            $fedex_add_chrg = $this->config->get('default_fedex_add_chrg');

			if(strlen($fedex_state)>2){
				$this->messages->saveError('Fedex US error!','Fedex US Shipping Extension won\'t work because state code length must be 2 letters. Please check settings form on #admin#rt=extension/extensions/edit&extension=default_fedex');
			}
			if(strlen($fedex_country)!=2){
				$this->messages->saveError('Fedex US error!','Fedex US Shipping Extension won\'t work because country code length must be 2 letters. Please check settings form on #admin#rt=extension/extensions/edit&extension=default_fedex');
			}

            //Recepient Info

            $shipping_address = $address;

            $ground_quote = 0;
            $first_overnight_quote = 0;
            $priority_overnight_quote = 0;
            $standard_overnight_quote = 0;
            $two_day_quote = 0;
            $express_saver_quote = 0;


            //Products info
            if ($products) {

                foreach ($products as $result) {
                    $request = array();

                    $product_weight = $this->weight->convert($result['weight'], $this->config->get('config_weight_class'), 'lb');
                    $product_weight = ($product_weight < 0.1 ? 0.1 : $product_weight);

                    $product_length = $this->length->convert($result['length'], $this->config->get('config_length_class'), 'in');
                    $product_width = $this->length->convert($result['width'], $this->config->get('config_length_class'), 'in');
                    $product_height = $this->length->convert($result['height'], $this->config->get('config_length_class'), 'in');


                    $product_quantity = $result['quantity'];
                    $product_total	  = $result['total'];

                    //BUILD REQUEST START
                    $request['WebAuthenticationDetail'] = array('UserCredential' =>
                    array('Key' => $fedex_key, 'Password' => $fedex_password));
                    $request['ClientDetail'] = array('AccountNumber' => $fedex_account, 'MeterNumber' => $fedex_meter_id);
                    $request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Rate Request v9 using PHP ***');
                    $request['Version'] = array('ServiceId' => 'crs', 'Major' => '9', 'Intermediate' => '0', 'Minor' => '0');
                    $request['ReturnTransitAndCommit'] = true;
                    $request['RequestedShipment']['DropoffType'] = 'REGULAR_PICKUP'; // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
                    $request['RequestedShipment']['ShipTimestamp'] = date('c');
                    //$request['RequestedShipment']['ServiceType'] = 'GROUND_HOME_DELIVERY'; // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
                    $request['RequestedShipment']['PackagingType'] = 'YOUR_PACKAGING'; // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
                    $request['RequestedShipment']['TotalInsuredValue']=array('Ammount'=> $product_total,'Currency'=>'USD');
                    $request['RequestedShipment']['Shipper'] = array('Address' => array(
                        'StreetLines' => array($fedex_addr), // Origin details
                        'City' => $fedex_city,
                        'StateOrProvinceCode' => $fedex_state,
                        'PostalCode' => $fedex_zip,
                        'CountryCode' => $fedex_country));

                    $request['RequestedShipment']['Recipient'] = array(
                        'Address' => array(
                            'StreetLines' => array($shipping_address['address_1'],$shipping_address['address_2']),
                            'City' => $shipping_address['city'],
                            'StateOrProvinceCode' => $shipping_address['zone_code'],
                            'PostalCode' => $shipping_address['postcode'],
                            'CountryCode' => $shipping_address['iso_code_2'],
                            'Residential' => $fedex_residential)
                    );
                    $request['RequestedShipment']['ShippingChargesPayment'] = array('PaymentType' => 'SENDER',
                        'Payor' => array('AccountNumber' => $fedex_account,
                            'CountryCode' => 'US'));
                    $request['RequestedShipment']['RateRequestTypes'] = 'ACCOUNT';
                    $request['RequestedShipment']['RateRequestTypes'] = 'LIST';
                    $request['RequestedShipment']['PackageCount'] = $product_quantity ;
                    $request['RequestedShipment']['PackageDetail'] = 'INDIVIDUAL_PACKAGES';  //  Or PACKAGE_SUMMARY

                    for ( $q=0 ; $q< $product_quantity ;$q++){
                        $request['RequestedShipment']['RequestedPackageLineItems']= array('Weight' => array('Value' => $product_weight,
                            'Units' => 'LB'),
                            'Dimensions' => array('Length' => $product_length,
                                'Width' => $product_width,
                                'Height' => $product_height,
                                'Units' => 'IN'));
                    }



                    try{
                        if(setEndpoint('changeEndpoint')){
                            $newLocation = $client->__setLocation(setEndpoint('endpoint'));
                        }

                        $response = $client->getRates($request);

                        if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR' ){

                            if ( count(	$response->RateReplyDetails ) > 1 ){
                                foreach ($response->RateReplyDetails as $rateReply){
                                    if($rateReply->ServiceType=='FEDEX_GROUND' || $rateReply->ServiceType=='GROUND_HOME_DELIVERY' ){
                                        $ground_quote = $ground_quote + $product_quantity * ( number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",") ) ;
                                    } else if($rateReply->ServiceType== 'FIRST_OVERNIGHT' ){
                                        $first_overnight_quote = $first_overnight_quote + $product_quantity * ( number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",") ) ;
                                    } else if($rateReply->ServiceType== 'PRIORITY_OVERNIGHT' ){
                                        $priority_overnight_quote = $priority_overnight_quote + $product_quantity * ( number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",") ) ;
                                    } else if($rateReply->ServiceType== 'STANDARD_OVERNIGHT' ){
                                        $standard_overnight_quote = $standard_overnight_quote + $product_quantity * ( number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",") );
                                    } else if($rateReply->ServiceType== 'FEDEX_2_DAY' ){
                                        $two_day_quote = $two_day_quote + $product_quantity * ( number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",") ) ;
                                    } else if($rateReply->ServiceType== 'FEDEX_EXPRESS_SAVER' ){
                                        $express_saver_quote = $express_saver_quote + $product_quantity * ( number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",") ) ;
                                    }
                                }
                            }else{
                                $rateReply = $response->RateReplyDetails;
                                if($rateReply->ServiceType=='FEDEX_GROUND' || $rateReply->ServiceType=='GROUND_HOME_DELIVERY' ){
                                    $ground_quote = $ground_quote + $product_quantity * ( number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",") ) ;
                                } else if($rateReply->ServiceType== 'FIRST_OVERNIGHT' ){
                                    $first_overnight_quote = $first_overnight_quote + $product_quantity * ( number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",") ) ;
                                } else if($rateReply->ServiceType== 'PRIORITY_OVERNIGHT' ){
                                    $priority_overnight_quote = $priority_overnight_quote + $product_quantity * ( number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",") ) ;
                                } else if($rateReply->ServiceType== 'STANDARD_OVERNIGHT' ){
                                    $standard_overnight_quote = $standard_overnight_quote + $product_quantity * ( number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",") ) ;
                                } else if($rateReply->ServiceType== 'FEDEX_2_DAY' ){
                                    $two_day_quote = $two_day_quote + $product_quantity * ( number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",") );
                                } else if($rateReply->ServiceType== 'FEDEX_EXPRESS_SAVER' ){
                                    $express_saver_quote = $express_saver_quote + $product_quantity * ( number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",") ) ;
                                }
                            }
                        }else{
                            $error_msg = $this->getNotifications($response -> Notifications);
                        }

                    }catch (SoapFault $exception) {
                        $error_text = 'Fault' . "<br>\n";
                        $error_text .= "Code:".$exception->faultcode."\n";
                        $error_text .= "String:".$exception->faultstring."\n";
                        $error_text .= $client;
                        $this->message->saveError('fedex extension soap error', $error_text);
                        $this->log->write($error_text);
                    }
                }

            }


            if ($first_overnight_quote > 0 && $this->config->get('default_fedex_default_fedex_us_01') > 0 ) {
                $first_overnight_quote = $first_overnight_quote + $fedex_add_chrg;
                $quote_data['FEDEX_FIRST_OVERNIGHT'] = array(
                    'id'           => 'default_fedex.'.'FEDEX_FIRST_OVERNIGHT',
                    'title'        => 'Fedex First Overnight',
                    'cost'         => $this->currency->convert($first_overnight_quote, 'USD', $this->currency->getCode()),
                    'tax_class_id' => 0,
                    'text'         => $this->currency->format($this->currency->convert($first_overnight_quote, 'USD', $this->currency->getCode()))
                );
            }

            if ($priority_overnight_quote > 0  && $this->config->get('default_fedex_default_fedex_us_02') > 0 ) {
                $priority_overnight_quote = $priority_overnight_quote + $fedex_add_chrg;
                $quote_data['FEDEX_PRIORITY_OVERNIGHT'] = array(
                    'id'           => 'default_fedex.'.'FEDEX_PRIORITY_OVERNIGHT',
                    'title'        => 'Fedex Priority Overnight',
                    'cost'         => $this->currency->convert($priority_overnight_quote, 'USD', $this->currency->getCode()),
                    'tax_class_id' => 0,
                    'text'         => $this->currency->format($this->currency->convert($priority_overnight_quote, 'USD', $this->currency->getCode()))
                );
            }

            if ($standard_overnight_quote > 0 && $this->config->get('default_fedex_default_fedex_us_03') > 0 ) {
                $standard_overnight_quote = $standard_overnight_quote + $fedex_add_chrg;
                $quote_data['FEDEX_STANDARD_OVERNIGHT'] = array(
                    'id'           => 'default_fedex.'.'FEDEX_STANDARD_OVERNIGHT',
                    'title'        => 'Fedex Standard Overnight',
                    'cost'         => $this->currency->convert($standard_overnight_quote, 'USD', $this->currency->getCode()),
                    'tax_class_id' => 0,
                    'text'         => $this->currency->format($this->currency->convert($standard_overnight_quote, 'USD', $this->currency->getCode()))
                );
            }

            if ($two_day_quote > 0  && $this->config->get('default_fedex_default_fedex_us_04') > 0) {
                $two_day_quote = $two_day_quote + $fedex_add_chrg;
                $quote_data['FEDEX_2_DAY'] = array(
                    'id'           => 'default_fedex.'.'FEDEX_2_DAY',
                    'title'        => 'Fedex 2 Day',
                    'cost'         => $this->currency->convert($two_day_quote, 'USD', $this->currency->getCode()),
                    'tax_class_id' => 0,
                    'text'         => $this->currency->format($this->currency->convert($two_day_quote, 'USD', $this->currency->getCode()))
                );
            }

            if ($express_saver_quote > 0 && $this->config->get('default_fedex_default_fedex_us_05') > 0 ) {
                $express_saver_quote = $express_saver_quote + $fedex_add_chrg;
                $quote_data['FEDEX_EXPRESS_SAVER'] = array(
                    'id'           => 'default_fedex.'.'FEDEX_EXPRESS_SAVER',
                    'title'        => 'Fedex Express Saver',
                    'cost'         => $this->currency->convert($express_saver_quote, 'USD', $this->currency->getCode()),
                    'tax_class_id' => 0,
                    'text'         => $this->currency->format($this->currency->convert($express_saver_quote, 'USD', $this->currency->getCode()))
                );
            }

            if ($ground_quote > 0 && $this->config->get('default_fedex_default_fedex_us_06') > 0 ) {
                $ground_quote = $ground_quote + $fedex_add_chrg;
                $quote_data['FEDEX_GROUND'] = array(
                    'id'           => 'default_fedex.'.'FEDEX_GROUND',
                    'title'        => 'Fedex Ground',
                    'cost'         => $this->currency->convert($ground_quote, 'USD', $this->currency->getCode()),
                    'tax_class_id' => 0,
                    'text'         => $this->currency->format($this->currency->convert($ground_quote, 'USD', $this->currency->getCode()))
                );
            }

        return array('quote_data'=>$quote_data, 'error_msg'=>$error_msg);
    }

    function getNotifications($notes){
        $strNotes = "";
        foreach($notes as $noteKey => $note){
            if(is_string($note) && $noteKey == 'Message'){
                $strNotes .=  $noteKey . ': ' . $note . '<br>';
            }
        }
        return $strNotes;
    }
}
