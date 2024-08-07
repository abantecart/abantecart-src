# UPS\LandedCost\DefaultApi

All URIs are relative to *https://wwwcie.ups.com/api*

Method | HTTP request | Description
------------- | ------------- | -------------
[**landedCost**](DefaultApi.md#landedcost) | **POST** /landedcost/{version}/quotes | Landed Cost Quote API

# **landedCost**
> \UPS\LandedCost\LandedCost\LandedCostResponse landedCost($body, $trans_id, $transaction_src, $version, $account_number)

Landed Cost Quote API

The Landed Cost Quote API allows you to estimate the all-inclusive cost of international shipments - including applicable duties, VAT, taxes, brokerage fees, and other fees. Required parameters include the currency and shipment details, such as the commodity ID, price, quantity, and country code of origin.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: oauth2
$config = UPS\LandedCost\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\LandedCost\Request\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \UPS\LandedCost\LandedCost\LandedCostRequest(); // \UPS\LandedCost\LandedCost\LandedCostRequest | Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click "Authorize" and enter your application credentials, then populate the required parameters above and click "Try it out".
$trans_id = "trans_id_example"; // string | An identifier unique to the request. Length: 32
$transaction_src = "testing"; // string | An identifier of the client/source application that is making the request. Length: 512
$version = "version_example"; // string | Version of the API.
$account_number = "account_number_example"; // string | The UPS account number.

try {
    $result = $apiInstance->landedCost($body, $trans_id, $transaction_src, $version, $account_number);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->landedCost: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\UPS\LandedCost\LandedCost\LandedCostRequest**](../Model/LandedCostRequest.md)| Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click &quot;Authorize&quot; and enter your application credentials, then populate the required parameters above and click &quot;Try it out&quot;. |
 **trans_id** | **string**| An identifier unique to the request. Length: 32 |
 **transaction_src** | **string**| An identifier of the client/source application that is making the request. Length: 512 | [default to testing]
 **version** | **string**| Version of the API. |
 **account_number** | **string**| The UPS account number. | [optional]

### Return type

[**\UPS\LandedCost\LandedCost\LandedCostResponse**](../Model/LandedCostResponse.md)

### Authorization

[oauth2](../../README.md#oauth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

