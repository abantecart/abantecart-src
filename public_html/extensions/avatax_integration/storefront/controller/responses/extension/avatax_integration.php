<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/

require DIR_EXTENSIONS.'avatax_integration/core/vendor/autoload.php';

class ControllerResponsesExtensionAvataxIntegration extends AController
{

    public $data = array();

    public function test_address()
    {
        if (!$this->config->get('avatax_integration_status')) {
            return null;
        }

        // Header Level Elements
        // Required Header Level Elements
        $serviceURL = $this->config->get('avatax_integration_service_url');
        $accountNumber = $this->config->get('avatax_integration_account_number');
        $licenseKey = $this->config->get('avatax_integration_license_key');

        $countryForValidate = $this->config->get('avatax_integration_address_validation_countries');
        if ($countryForValidate == 'Both') {
            $countryISO = "US,CA";
        } else {
            $countryISO = $countryForValidate;
        }

        $this->loadLanguage('avatax_integration/avatax_integration');
        $this->loadModel('account/address');
        $addressSvc = new AvaTax\AddressServiceRest($serviceURL, $accountNumber, $licenseKey);
        $address = new AvaTax\Address();
        $customerAddress = $this->model_account_address->getAddress(
            $this->session->data['shipping_address_id']
        );

        if (strpos($countryISO, $customerAddress['iso_code_2']) >= 0) {
            // Required Request Parameters
            $address->setLine1($customerAddress['address_1']);
            $address->setCity($customerAddress['city']);
            $address->setRegion($customerAddress['zone_code']);

            // Optional Request Parameters
            $address->setLine2($customerAddress['address_2']);
            //$address->setLine3("AsdfsTTN Accounts Payable");
            $address->setCountry($customerAddress['iso_code_2']);
            $address->setPostalCode($customerAddress['postcode']);

            $validateRequest = new AvaTax\ValidateRequest();
            $validateRequest->setAddress($address);
            $validateResult = $addressSvc->Validate($validateRequest);

            if ($this->config->get('avatax_integration_logging') == 1) {
                $message = print_r($validateRequest, true);
                $warning = new AWarning('AVATAX address validation request: '.$message);
                $warning->toLog()->toDebug();
                $message = print_r($validateResult, true);
                $warning = new AWarning('AVATAX address validation reply: '.$message);
                $warning->toLog()->toDebug();
            }

            $json = array();

            if ($validateResult->getResultCode() != AvaTax\SeverityLevel::$Success) {
                //$warning = new AWarning('PingTest Result: ' . $validateResult->getResultCode() . '.');
                //$warning->toLog()->toDebug();
                $allMessages = "";
                foreach ($validateResult->getMessages() as $message) {
                    $allMessages .= $message->getSummary()."\n";
                }
                $json['message'] = strtoupper($allMessages);
                $json['error'] = true;
            } else {
                $json['message'] = strtoupper($this->language->get('avatax_integration_address_validation_success'));
                $json['error'] = false;
            }
        } else {
            $json['message'] = "";
            $json['error'] = false;
        }
        $this->load->library('json');
        $this->response->setOutput(AJson::encode($json));
    }
}