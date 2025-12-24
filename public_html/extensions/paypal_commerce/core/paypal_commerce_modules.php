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

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;

/**
 * @param string $accountId
 * @param string $secretKey
 * @param int $testMode
 * @return PayPalHttpClient|DebugPayPalHttpClient
 * @throws AException
 */

function getPaypalClient( string $accountId, string $secretKey, int $testMode = 0): DebugPayPalHttpClient|PayPalHttpClient
{
    if ($testMode) {
        $env = new SandboxEnvironment($accountId, $secretKey);
    } else {
        $env = new ProductionEnvironment($accountId, $secretKey);
    }

    if (Registry::getInstance()?->get('config')?->get('paypal_commerce_debug_logging')) {
        return new DebugPayPalHttpClient($env, true);
    }

    return new PayPalHttpClient($env);
}

function getNonce($uniqueID, $urlencoded = true)
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