# UPS\OAuthClientCredentials\DefaultApi

All URIs are relative to *https://wwwcie.ups.com*

Method | HTTP request | Description
------------- | ------------- | -------------
[**createToken**](DefaultApi.md#createtoken) | **POST** /security/v1/oauth/token | Create Token

# **createToken**
> \UPS\OAuthClientCredentials\OAuthClientCredentials\TokenSuccessResponse createToken($grant_type, $x_merchant_id)

Create Token

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');
// Configure HTTP basic authorization: basicAuth
$config = UPS\OAuthClientCredentials\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new UPS\OAuthClientCredentials\Request\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$grant_type = "grant_type_example"; // string | 
$x_merchant_id = "x_merchant_id_example"; // string | 6-digit UPS account number.

try {
    $result = $apiInstance->createToken($grant_type, $x_merchant_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->createToken: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **grant_type** | **string**|  |
 **x_merchant_id** | **string**| 6-digit UPS account number. | [optional]

### Return type

[**\UPS\OAuthClientCredentials\OAuthClientCredentials\TokenSuccessResponse**](../Model/TokenSuccessResponse.md)

### Authorization

[basicAuth](../../README.md#basicAuth)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

