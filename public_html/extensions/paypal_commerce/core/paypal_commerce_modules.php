<?php

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;

/**
 * @param string $accountId
 * @param string $secretKey
 * @param int $testMode
 *
 * @return PayPalHttpClient
 */
function getPaypalClient($accountId, $secretKey, $testMode = 0)
{
    if($testMode) {
        $env = new SandboxEnvironment($accountId, $secretKey);
    }else{
        $env = new ProductionEnvironment($accountId, $secretKey);
    }

    return new PayPalHttpClient($env);
}