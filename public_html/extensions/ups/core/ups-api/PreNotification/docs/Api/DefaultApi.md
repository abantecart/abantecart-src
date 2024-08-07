# UPS\PreNotification\DefaultApi

All URIs are relative to *https://wwwcie.ups.com/api*

Method | HTTP request | Description
------------- | ------------- | -------------
[**preNotification**](DefaultApi.md#prenotification) | **POST** /dangerousgoods/{version}/prenotification | Pre-Notification
[**preNotification_0**](DefaultApi.md#prenotification_0) | **POST** /dangerousgoods/{deprecatedVersion}/prenotification | Pre-Notification

# **preNotification**
> \UPS\PreNotification\PreNotification\PRENOTIFICATIONResponseWrapper preNotification($body, $version, $trans_id, $transaction_src)

Pre-Notification

The Pre-Notification API allows customer applications to inform UPS operations of Dangerous Goods shipments as they are processed and will enter the UPS transportation network prior to an upload of manifest information at the end of the day.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: oauth2
$config = UPS\PreNotification\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\PreNotification\Request\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \UPS\PreNotification\PreNotification\PRENOTIFICATIONRequestWrapper(); // \UPS\PreNotification\PreNotification\PRENOTIFICATIONRequestWrapper | Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click "Authorize" and enter your application credentials, then populate the required parameters above and click "Try it out".
$version = "version_example"; // string | Version of API.  Valid values: - v2
$trans_id = "trans_id_example"; // string | An identifier unique to the request. Length 32
$transaction_src = "testing"; // string | An identifier of the client/source application that is making the request.Length 512

try {
    $result = $apiInstance->preNotification($body, $version, $trans_id, $transaction_src);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->preNotification: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\UPS\PreNotification\PreNotification\PRENOTIFICATIONRequestWrapper**](../Model/PRENOTIFICATIONRequestWrapper.md)| Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click &quot;Authorize&quot; and enter your application credentials, then populate the required parameters above and click &quot;Try it out&quot;. |
 **version** | **string**| Version of API.  Valid values: - v2 |
 **trans_id** | **string**| An identifier unique to the request. Length 32 | [optional]
 **transaction_src** | **string**| An identifier of the client/source application that is making the request.Length 512 | [optional] [default to testing]

### Return type

[**\UPS\PreNotification\PreNotification\PRENOTIFICATIONResponseWrapper**](../Model/PRENOTIFICATIONResponseWrapper.md)

### Authorization

[oauth2](../../README.md#oauth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **preNotification_0**
> \UPS\PreNotification\PreNotification\PRENOTIFICATIONResponseWrapper preNotification_0($body, $deprecated_version, $trans_id, $transaction_src)

Pre-Notification

The Pre-Notification API allows customer applications to inform UPS operations of Dangerous Goods shipments as they are processed and will enter the UPS transportation network prior to an upload of manifest information at the end of the day.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: oauth2
$config = UPS\PreNotification\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\PreNotification\Request\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \UPS\PreNotification\PreNotification\PRENOTIFICATIONRequestWrapper(); // \UPS\PreNotification\PreNotification\PRENOTIFICATIONRequestWrapper | Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click "Authorize" and enter your application credentials, then populate the required parameters above and click "Try it out".
$deprecated_version = "deprecated_version_example"; // string | Version of API.  Valid values: - v1
$trans_id = "trans_id_example"; // string | An identifier unique to the request. Length 32
$transaction_src = "testing"; // string | An identifier of the client/source application that is making the request.Length 512

try {
    $result = $apiInstance->preNotification_0($body, $deprecated_version, $trans_id, $transaction_src);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->preNotification_0: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\UPS\PreNotification\PreNotification\PRENOTIFICATIONRequestWrapper**](../Model/PRENOTIFICATIONRequestWrapper.md)| Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click &quot;Authorize&quot; and enter your application credentials, then populate the required parameters above and click &quot;Try it out&quot;. |
 **deprecated_version** | **string**| Version of API.  Valid values: - v1 |
 **trans_id** | **string**| An identifier unique to the request. Length 32 | [optional]
 **transaction_src** | **string**| An identifier of the client/source application that is making the request.Length 512 | [optional] [default to testing]

### Return type

[**\UPS\PreNotification\PreNotification\PRENOTIFICATIONResponseWrapper**](../Model/PRENOTIFICATIONResponseWrapper.md)

### Authorization

[oauth2](../../README.md#oauth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

