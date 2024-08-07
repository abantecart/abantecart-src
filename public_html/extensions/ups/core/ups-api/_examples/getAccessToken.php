<?php
require_once ('vendor/autoload.php');

//YOUR ACCOUNT NUMBER (6 characters)
$accNumber = '******';
//UPS API Credentials (obtain after APP creation)
$clientId = '***YOUR_UPS_API_CLIENT_ID***';
$password = '***YOUR_UPS_API_PASSWORD***';

$config = \UPS\OAuthClientCredentials\Configuration::getDefaultConfiguration()
    ->setUsername($clientId)
    ->setPassword($password);

$apiInstance = new \UPS\OAuthClientCredentials\Request\DefaultApi(
// If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
// This is optional, `GuzzleHttp\Client` will be used as default.
    new \GuzzleHttp\Client(),
    $config
);
$grant_type = "client_credentials"; // string |
$x_merchant_id = $accNumber; // string | Client merchant ID

try {
    $result = $apiInstance->createToken($grant_type, $x_merchant_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->generateToken: ', $e->getMessage(), PHP_EOL;
}