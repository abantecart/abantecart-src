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

    public function test()
    {
        //need to run connection check
        // Header Level Elements
        // Required Header Level Elements
        $this->loadLanguage('avatax_integration/avatax_integration');
        $serviceURL = $this->registry->get('config')->get('avatax_integration_service_url');
        $accountNumber = $this->registry->get('config')->get('avatax_integration_account_number');
        $licenseKey = $this->registry->get('config')->get('avatax_integration_license_key');

        $json = array(
            'message' => "Connection to Avatax server can not be established.\n"
                ."\nCheck your server configuration or contact your hosting provider.",
            'error'   => true,
        );

        if (!empty($serviceURL) && !empty($accountNumber) && !empty($licenseKey)) {
            try {
                $taxSvc = new AvaTax\TaxServiceRest($serviceURL, $accountNumber, $licenseKey);
                $geoTaxResult = $taxSvc->ping("");
                if ($geoTaxResult->getResultCode() != AvaTax\SeverityLevel::$Success) {
                    $warning = new AWarning('PingTest Result: '.$geoTaxResult->getResultCode().'.');
                    $warning->toLog()->toDebug();
                    $allMessages = "";
                    foreach ($geoTaxResult->getMessages() as $message) {
                        $allMessages .= $message->getSummary()."\n";
                    }
                    $json['message'] = "Connection to Avatax server can not be established.\n"
                        .$allMessages
                        ."\nCheck your server configuration or contact your hosting provider.";
                    $json['error'] = true;
                } else {
                    $json['message'] = $this->language->get('text_connection_success');
                    $json['error'] = false;
                }
            } catch (Exception $e) {
            }

        }

        $this->load->library('json');
        $this->response->setOutput(AJson::encode($json));
    }

}
