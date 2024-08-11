<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: oauth2
$config = UPS\Shipping\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\Shipping\Request\DefaultApi(
// If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
// This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \UPS\Shipping\Shipping\SHIPRequestWrapper(); // \UPS\Shipping\Shipping\SHIPRequestWrapper | Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click "Authorize" and enter your application credentials, then populate the required parameters above and click "Try it out".
$version = "version_example"; // string | Indicates Ship API to display the new release features in  Rate API response based on Ship release. See the New  section for the latest Ship release. Supported values: v1, v1601, v1607, v1701, v1707, v1801, v1807, v2108, v2205 . Length 5
$trans_id = "trans_id_example"; // string | An identifier unique to the request. Length 32
$transaction_src = "testing"; // string | An identifier of the client/source application that is making the request.Length 512
$additionaladdressvalidation = "additionaladdressvalidation_example"; // string | Valid Values:  city = validation will include city.Length 15

try {
    $result = $apiInstance->shipment($body, $version, $trans_id, $transaction_src, $additionaladdressvalidation);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->shipment: ', $e->getMessage(), PHP_EOL;
}
