<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

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

		$method_data = array();
		
		if ($status) {
			if (!$this->config->get('default_fedex_test')) {
				$url = 'gateway.fedex.com/GatewayDC';
			} else {
				$url = 'gatewaybeta.fedex.com/GatewayDC';
			}
				
			$quote_data = array();

			$xml  = '<?xml version="1.0" encoding="UTF-8" ?>';
			$xml .= '<FDXRateRequest xmlns:api="http://www.fedex.com/fsmapi" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="FDXRateRequest.xsd">';
			$xml .= '	<RequestHeader>';
			$xml .= '		<CustomerTransactionIdentifier>Express Rate</CustomerTransactionIdentifier>';
			$xml .= '		<AccountNumber>' . $this->config->get('default_fedex_account') . '</AccountNumber>';
			$xml .= '		<MeterNumber>' . $this->config->get('default_fedex_meter') . '</MeterNumber>';
			$xml .= '		<CarrierCode>' . 'FDXG' . '</CarrierCode>';
			$xml .= '	</RequestHeader>';
			$xml .= '	<DropoffType>' . 'REGULARPICKUP' . '</DropoffType>';
			$xml .= '	<Service>' . 'FEDEXGROUND' . '</Service>';
			$xml .= '	<Packaging>' . 'YOURPACKAGING' . '</Packaging>';
			$xml .= '	<WeightUnits>' . $this->currency->getCode($this->config->get('config_weight_class_id')) . '</WeightUnits>';
			$xml .= '	<Weight>' . number_format($this->cart->getWeight(), 1, '.', '') . '</Weight>';
			$xml .= '	<OriginAddress>';
			$xml .= '		<StateOrProvinceCode>' . 'LANCS' . '</StateOrProvinceCode>';
			$xml .= '		<PostalCode>' . 'FY5 4NN' . '</PostalCode>';
			$xml .= '		<CountryCode>' . 'GB' . '</CountryCode>';
			$xml .= '	</OriginAddress>';
			$xml .= '	<DestinationAddress>';
			$xml .= '		<StateOrProvinceCode>' . $address['zone_code'] . '</StateOrProvinceCode>';
			$xml .= '		<PostalCode>' . $address['postcode'] . '</PostalCode>';
			$xml .= '		<CountryCode>' . $address['iso_code_2'] . '</CountryCode>';
			$xml .= '	</DestinationAddress>';
			$xml .= '	<Payment>';
			$xml .= '		<PayorType>' . 'SENDER' . '</PayorType>';
			$xml .= '	</Payment>';
			$xml .= '	<PackageCount>' . ceil(bcdiv(number_format($this->cart->getWeight(), 1, '.', ''), '150', 3)) . '</PackageCount>';
			$xml .= '</FDXRateRequest>';
		   
		    $header = array();
			
			$header[] = 'Host: ' . $url;
			$header[] = 'MIME-Version: 1.0';
			$header[] = 'Content-type: multipart/mixed; boundary=----doc';
			$header[] = 'Accept: text/xml';
			$header[] = 'Content-length: '. strlen($xml);
			$header[] = 'Cache-Control: no-cache';
			$header[] = 'Connection: close' . "\r\n";
			$header[] = $xml;

			$ch = curl_init();
			//Disable certificate check.
			// uncomment the next line if you get curl error 60: error setting certificate verify locations
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			// uncommenting the next line is most likely not necessary in case of error 60
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			//-------------------------
			//curl_setopt($ch, CURLOPT_CAINFO, "c:/ca-bundle.crt");
			//-------------------------
			//curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_PORT, 443);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 4);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
				
			$response = curl_exec($ch);

			//echo 'error: '. curl_errno($ch);
			curl_close($ch);


      		$quote_data['default_fedex'] = array(
        		'id'           => 'default_fedex.default_fedex',
        		'title'        => $this->language->get('text_description'),
        		'cost'         => $this->config->get('default_fedex_cost'),
        		'tax_class_id' => $this->config->get('default_fedex_tax_class_id'),
				'text'         => $this->currency->format($this->tax->calculate($this->config->get('default_fedex_cost'), $this->config->get('default_fedex_tax_class_id'), $this->config->get('config_tax')))
      		);

      		$method_data = array(
        		'id'         => 'default_fedex',
        		'title'      => $this->language->get('text_title'),
        		'quote'      => $quote_data,
				'sort_order' => $this->config->get('default_fedex_sort_order'),
        		'error'      => FALSE
      		);
		}
	
		return $method_data;
	}
}
?>