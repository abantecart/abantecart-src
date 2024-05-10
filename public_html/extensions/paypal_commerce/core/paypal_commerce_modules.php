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

function getNonce($uniqueID, $urlencoded = true)
{
    if($urlencoded) {
        $uniqueID = urlencode($uniqueID);
    }

    $len = mb_strlen($uniqueID);
    if($len>128){
        $uniqueID = mb_substr($uniqueID, 0, 128);
    }elseif($len<44){
        $uniqueID = str_pad($uniqueID, 44, '0', STR_PAD_RIGHT);
    }

    return $uniqueID;
}