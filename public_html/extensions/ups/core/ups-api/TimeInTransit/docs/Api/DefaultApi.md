# UPS\TimeInTransit\DefaultApi

All URIs are relative to *https://wwwcie.ups.com/api*

Method | HTTP request | Description
------------- | ------------- | -------------
[**timeInTransit**](DefaultApi.md#timeintransit) | **POST** /shipments/{version}/transittimes | TimeInTransit

# **timeInTransit**
> \UPS\TimeInTransit\TimeInTransit\TimeInTransitResponse timeInTransit($body, $trans_id, $transaction_src, $version)

TimeInTransit

The Time In Transit API provides estimated delivery times for various UPS shipping services, between specified locations.  Key Business Values: - **Enhanced Customer Experience**: Allows businesses provide accurate delivery estimates to their customers, enhancing customer service. - **Operational Efficiency**: Helps in logistics planning by providing transit times for different UPS services.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: oauth2
$config = UPS\TimeInTransit\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\TimeInTransit\Request\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \UPS\TimeInTransit\TimeInTransit\TimeInTransitRequest(); // \UPS\TimeInTransit\TimeInTransit\TimeInTransitRequest | Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click "Authorize" and enter your application credentials, then populate the required parameters above and click "Try it out".
$trans_id = "trans_id_example"; // string | An identifier unique to the request. Length 32
$transaction_src = "testing"; // string | Identifies the clients/source application that is calling.  Length 512
$version = "version_example"; // string | API Version

try {
    $result = $apiInstance->timeInTransit($body, $trans_id, $transaction_src, $version);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->timeInTransit: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\UPS\TimeInTransit\TimeInTransit\TimeInTransitRequest**](../Model/TimeInTransitRequest.md)| Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click &quot;Authorize&quot; and enter your application credentials, then populate the required parameters above and click &quot;Try it out&quot;. |
 **trans_id** | **string**| An identifier unique to the request. Length 32 |
 **transaction_src** | **string**| Identifies the clients/source application that is calling.  Length 512 | [default to testing]
 **version** | **string**| API Version |

### Return type

[**\UPS\TimeInTransit\TimeInTransit\TimeInTransitResponse**](../Model/TimeInTransitResponse.md)

### Authorization

[oauth2](../../README.md#oauth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

