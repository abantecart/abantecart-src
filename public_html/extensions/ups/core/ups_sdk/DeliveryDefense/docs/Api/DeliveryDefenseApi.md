# UPS\DeliveryDefense\DeliveryDefenseApi

All URIs are relative to *https://wwwcie.ups.com/api/deliverydefense/external/v1.0*

Method | HTTP request | Description
------------- | ------------- | -------------
[**score**](DeliveryDefenseApi.md#score) | **POST** /address/score | Get Verified Address Score

# **score**
> \UPS\DeliveryDefense\DeliveryDefense\Success score($body)

Get Verified Address Score

This API cleans and verifies inputted addresses to enhance the accuracy of generating an Address Confidence score. The returned data includes the cleaned address, the Address Confidence score, and an indication of whether the address is commercial or residential.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: OAuth2
$config = UPS\DeliveryDefense\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\DeliveryDefense\Request\DeliveryDefenseApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \UPS\DeliveryDefense\DeliveryDefense\AddressRequestBody(); // \UPS\DeliveryDefense\DeliveryDefense\AddressRequestBody | 

try {
    $result = $apiInstance->score($body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DeliveryDefenseApi->score: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\UPS\DeliveryDefense\DeliveryDefense\AddressRequestBody**](../Model/AddressRequestBody.md)|  | [optional]

### Return type

[**\UPS\DeliveryDefense\DeliveryDefense\Success**](../Model/Success.md)

### Authorization

[OAuth2](../../README.md#OAuth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

