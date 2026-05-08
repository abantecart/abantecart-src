<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2026 Belavier Commerce LLC
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

use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\ApiHelper;
use PaypalServerSdkLib\Environment;
use PaypalServerSdkLib\Logging\LoggingConfigurationBuilder;
use PaypalServerSdkLib\Logging\RequestLoggingConfigurationBuilder;
use PaypalServerSdkLib\Logging\ResponseLoggingConfigurationBuilder;
use PaypalServerSdkLib\Models\Order;
use PaypalServerSdkLib\PaypalServerSdkClient;
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;
use Psr\Log\LogLevel;

/**
 * @param string $accountId
 * @param string $secretKey
 * @param int $testMode
 *
 * @return PaypalServerSdkClient
 * @throws AException
 */

function getPaypalClient(
    string $accountId,
    string $secretKey,
    int    $testMode = 0
)
{
    $client = PaypalServerSdkClientBuilder::init()
                  ->clientCredentialsAuthCredentials(
                      ClientCredentialsAuthCredentialsBuilder::init($accountId, $secretKey)
                  )
                  ->environment($testMode ? Environment::SANDBOX : Environment::PRODUCTION);

    if (Registry::getInstance()?->get('config')?->get('paypal_commerce_debug_logging')) {
        $logger = new DebugPayPalFileLogger(DIR_LOGS . '/paypal-debug.log', true);
        $client->loggingConfiguration(
            LoggingConfigurationBuilder::init()
                ->logger($logger)
                ->level(LogLevel::INFO)
                ->requestConfiguration(RequestLoggingConfigurationBuilder::init()->body(true))
                ->responseConfiguration(
                    ResponseLoggingConfigurationBuilder::init()->headers(true)
                )
        );
    }

    return $client->build();
}

/**
 * @param string $uniqueID
 * @param bool $urlencoded
 *
 * @return string
 */
function getNonce(string $uniqueID, bool $urlencoded = true)
{
    if ($urlencoded) {
        $uniqueID = urlencode($uniqueID);
    }

    $len = mb_strlen($uniqueID);
    if ($len > 128) {
        $uniqueID = mb_substr($uniqueID, 0, 128);
    } elseif ($len < 44) {
        $uniqueID = str_pad($uniqueID, 44, '0', STR_PAD_RIGHT);
    }
    return $uniqueID;
}

/**
 * Normalize PayPal SDK order result to a typed Order model.
 *
 * @param mixed $result
 *
 * @return Order|null
 */
function paypalNormalizeOrderResult($result): ?Order
{
    if ($result instanceof Order) {
        return $result;
    }
    if (!is_array($result)) {
        return null;
    }

    try {
        $mappedResult = ApiHelper::getJsonHelper()->mapClass($result, Order::class);
        return $mappedResult instanceof Order ? $mappedResult : null;
    } catch (Exception|Error) {
        return null;
    }
}
