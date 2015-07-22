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
if (!IS_ADMIN || !defined('DIR_CORE')){
	header('Location: static_pages/');
}

class ControllerResponsesExtensionDefaultFedex extends AController{

	public $data = array ();

	public function test(){

		$this->loadLanguage('default_fedex/default_fedex');
		/**
		 * @var ModelExtensionDefaultFedex $sf_model
		 */
		$json = array ();
		$required_fields = array (
				'key'        => 'default_fedex_key',
				'pass'       => 'default_fedex_password',
				'acc'        => 'default_fedex_account',
				'meter'      => 'default_fedex_meter',
				'address_1'  => 'default_fedex_address',
				'city'       => 'default_fedex_city',
				'zone_code'  => 'default_fedex_state',
				'postcode'   => 'default_fedex_zip',
				'iso_code_2' => 'default_fedex_country'
		);
		$address = array ();
		foreach ($required_fields as $k => $fld){
			if (!$this->config->get($fld)){
				$json['error'] = true;
				$json['message'] = 'Error: Please fill and save all required fields and try again.';
				break;
			}
			if (in_array($fld, array ('default_fedex_country', 'default_fedex_state')) && strlen($this->config->get($fld)) > 2){
				$json['error'] = true;
				$json['message'] = 'Error: Please check "State" and "Country" settings values. It must be two-letters code. ( ' . $this->config->get($fld).' )';
				break;
			}

			$address[$k] = $this->config->get($fld);
		}


		if ($json['error'] != true){

			$test_result = $this->_processRequest($address);

			if (!$test_result){
				$json['error'] = true;
				$json['message'] = 'Fedex Error: Wrong data was given. Please check your API Credentials and try again.' . "\n" . 'Also please note that Test mode is ' . $test_mode . '!';
			} else if ($test_result['error']){
				$json['error'] = true;
				$json['message'] = 'Fedex Error: ' . $test_result['error'] . "\n" . 'Please check your API Credentials and try again.' . "\n" . 'Also please note that Test mode is ' . $test_mode . '!';

			} else{
				$json['message'] = $this->language->get('text_connection_success');
				$json['error'] = false;
			}
		}

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($json));
	}

	private function _processRequest($address){
		require_once(DIR_EXT . 'default_fedex/core/lib/fedex_func.php');

		if ($this->config->get('default_fedex_test')){
			$path_to_wsdl = DIR_EXT . 'default_fedex/core/lib/RateService_v9_test.wsdl';
		} else{
			$path_to_wsdl = DIR_EXT . 'default_fedex/core/lib/RateService_v9.wsdl';
		}
		$client = new SoapClient($path_to_wsdl, array ('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information


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
		} else{
			$fedex_residential = false;
		}

		$fedex_addr = $this->config->get('default_fedex_address');
		$fedex_city = $this->config->get('default_fedex_city');
		$fedex_state = $this->config->get('default_fedex_state');
		$fedex_zip = $this->config->get('default_fedex_zip');
		$fedex_country = $this->config->get('default_fedex_country');
		$fedex_add_chrg = $this->config->get('default_fedex_add_chrg');

		//Recepient Info
		$shipping_address = $address;


		$request = array ();

		$product_weight = 1.00;
		$product_length = 1.00;
		$product_width = 1.00;
		$product_height = 1.00;


		$product_quantity = 1;
		$product_total = 1.0;

		//BUILD REQUEST START
		$request['WebAuthenticationDetail'] = array (
				'UserCredential' => array (
						'Key'      => $fedex_key,
						'Password' => $fedex_password)
		);
		$request['ClientDetail'] = array ('AccountNumber' => $fedex_account, 'MeterNumber' => $fedex_meter_id);
		$request['TransactionDetail'] = array ('CustomerTransactionId' => ' *** Rate Request v9 using PHP ***');
		$request['Version'] = array ('ServiceId' => 'crs', 'Major' => '9', 'Intermediate' => '0', 'Minor' => '0');
		$request['ReturnTransitAndCommit'] = true;
		$request['RequestedShipment']['DropoffType'] = 'REGULAR_PICKUP'; // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
		$request['RequestedShipment']['ShipTimestamp'] = date('c');
		//$request['RequestedShipment']['ServiceType'] = 'GROUND_HOME_DELIVERY'; // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
		$request['RequestedShipment']['PackagingType'] = 'YOUR_PACKAGING'; // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
		$request['RequestedShipment']['TotalInsuredValue'] = array ('Ammount' => $product_total, 'Currency' => 'USD');
		$request['RequestedShipment']['Shipper'] = array ('Address' => array (
				'StreetLines'         => array ($fedex_addr), // Origin details
				'City'                => $fedex_city,
				'StateOrProvinceCode' => $fedex_state,
				'PostalCode'          => $fedex_zip,
				'CountryCode'         => $fedex_country));

		$request['RequestedShipment']['Recipient'] = array (
				'Address' => array (
						'StreetLines'         => array ($shipping_address['address_1'], $shipping_address['address_2']),
						'City'                => $shipping_address['city'],
						'StateOrProvinceCode' => $shipping_address['zone_code'],
						'PostalCode'          => $shipping_address['postcode'],
						'CountryCode'         => $shipping_address['iso_code_2'],
						'Residential'         => $fedex_residential)
		);
		$request['RequestedShipment']['ShippingChargesPayment'] = array ('PaymentType' => 'SENDER',
		                                                                 'Payor'       => array ('AccountNumber' => $fedex_account,
		                                                                                         'CountryCode'   => 'US'));
		$request['RequestedShipment']['RateRequestTypes'] = 'ACCOUNT';
		$request['RequestedShipment']['RateRequestTypes'] = 'LIST';
		$request['RequestedShipment']['PackageCount'] = $product_quantity;
		$request['RequestedShipment']['PackageDetail'] = 'INDIVIDUAL_PACKAGES';  //  Or PACKAGE_SUMMARY

		for ($q = 0; $q < $product_quantity; $q++){
			$request['RequestedShipment']['RequestedPackageLineItems'] = array ('Weight'     => array ('Value' => $product_weight,
			                                                                                           'Units' => 'LB'),
			                                                                    'Dimensions' => array ('Length' => $product_length,
			                                                                                           'Width'  => $product_width,
			                                                                                           'Height' => $product_height,
			                                                                                           'Units'  => 'IN'));
		}


		$error_msg = '';
		try{
			if (setEndpoint('changeEndpoint')){
				$newLocation = $client->__setLocation(setEndpoint('endpoint'));
			}

			$response = $client->getRates($request);

			if ($response->HighestSeverity == 'FAILURE' || $response->HighestSeverity == 'ERROR'){
				$error_msg = $this->_get_notifications($response->Notifications);
			}

		} catch(SoapFault $exception){
			$error_text = 'Fault' . "<br>\n";
			$error_text .= "Code:" . $exception->faultcode . "\n";
			$error_text .= "String:" . $exception->faultstring . "\n";
			$error_text .= $client;
			$this->message->saveError('fedex extension soap error', $error_text);
			$this->log->write($error_text);
		}


		return array ('error' => $error_msg);
	}

	private function _get_notifications($notes){
		$strNotes = "";
		foreach ($notes as $noteKey => $note){
			if (is_string($note) && $noteKey == 'Message'){
				$strNotes .= $noteKey . ': ' . $note . '<br>';
			}
		}
		return $strNotes;
	}

}