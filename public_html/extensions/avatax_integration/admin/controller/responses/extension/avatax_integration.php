<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */

use Avalara\AvaTaxClient;

class ControllerResponsesExtensionAvataxIntegration extends AController
{

    public function test()
    {
        //need to run connection check
        // Header Level Elements
        // Required Header Level Elements
        $this->loadLanguage('avatax_integration/avatax_integration');
        $testMode = $this->config->get('avatax_integration_test_mode');
        $accountNumber = $this->config->get('avatax_integration_account_number');
        $licenseKey = $this->config->get('avatax_integration_license_key');

        $json = [
            'message' => "Connection to Avatax server can not be established.\n"
                ."\nCheck your server configuration or contact your hosting provider.",
            'error'   => true,
        ];

        if ($accountNumber && $licenseKey) {
            try {
                $client = new AvaTaxClient('AbanteCart', VERSION, SERVER_NAME, $testMode ? 'sandbox' : '');
                $client->withLicenseKey($accountNumber, $licenseKey);

                $pingResult = $client->ping();

                if (!$pingResult->authenticated) {
                    $warning = new AWarning('PingTest Result: ' . $pingResult->status . '.');
                    $warning->toLog()->toDebug();

                    $json['message'] = "Connection to Avatax server cannot be established.<br>Response: " .
                        var_export($pingResult, true) .
                        "<br>Check your server configuration or contact your hosting provider.";
                    $json['error'] = true;
                } else {
                    $json['message'] = $this->language->get('text_connection_success');
                    $json['error'] = false;
                }
            } catch (\Exception $e) {
                $this->log->write('Avatax Error: '.$e->getMessage().PHP_EOL.$e->getTraceAsString());
                $json['message'] = "An error occurred while connecting to the Avalara service: " . $e->getMessage();
                $json['error'] = true;
            }
        }

        $this->load->library('json');
        $this->response->setOutput(AJson::encode($json));
    }

}
