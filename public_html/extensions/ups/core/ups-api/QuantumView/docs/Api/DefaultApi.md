# UPS\QuantumView\DefaultApi

All URIs are relative to *https://wwwcie.ups.com/api*

Method | HTTP request | Description
------------- | ------------- | -------------
[**quantumView**](DefaultApi.md#quantumview) | **POST** /quantumview/{version}/events | Quantum View
[**quantumView_0**](DefaultApi.md#quantumview_0) | **POST** /quantumview/{deprecatedVersion}/events | Quantum View

# **quantumView**
> \UPS\QuantumView\QuantumView\QUANTUMVIEWResponseWrapper quantumView($body, $version)

Quantum View

Get Quantum View Response

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: oauth2
$config = UPS\QuantumView\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\QuantumView\Request\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \UPS\QuantumView\QuantumView\QUANTUMVIEWRequestWrapper(); // \UPS\QuantumView\QuantumView\QUANTUMVIEWRequestWrapper | Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click "Authorize" and enter your application credentials, then populate the required parameters above and click "Try it out".
$version = "version_example"; // string | Version of API.  Valid values: - v2

try {
    $result = $apiInstance->quantumView($body, $version);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->quantumView: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\UPS\QuantumView\QuantumView\QUANTUMVIEWRequestWrapper**](../Model/QUANTUMVIEWRequestWrapper.md)| Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click &quot;Authorize&quot; and enter your application credentials, then populate the required parameters above and click &quot;Try it out&quot;. |
 **version** | **string**| Version of API.  Valid values: - v2 |

### Return type

[**\UPS\QuantumView\QuantumView\QUANTUMVIEWResponseWrapper**](../Model/QUANTUMVIEWResponseWrapper.md)

### Authorization

[oauth2](../../README.md#oauth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **quantumView_0**
> \UPS\QuantumView\QuantumView\QUANTUMVIEWResponseWrapper quantumView_0($body, $deprecated_version)

Quantum View

Get Quantum View Response

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: oauth2
$config = UPS\QuantumView\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\QuantumView\Request\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \UPS\QuantumView\QuantumView\QUANTUMVIEWRequestWrapper(); // \UPS\QuantumView\QuantumView\QUANTUMVIEWRequestWrapper | Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click "Authorize" and enter your application credentials, then populate the required parameters above and click "Try it out".
$deprecated_version = "deprecated_version_example"; // string | Version of API.  Valid values: - v1

try {
    $result = $apiInstance->quantumView_0($body, $deprecated_version);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->quantumView_0: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\UPS\QuantumView\QuantumView\QUANTUMVIEWRequestWrapper**](../Model/QUANTUMVIEWRequestWrapper.md)| Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click &quot;Authorize&quot; and enter your application credentials, then populate the required parameters above and click &quot;Try it out&quot;. |
 **deprecated_version** | **string**| Version of API.  Valid values: - v1 |

### Return type

[**\UPS\QuantumView\QuantumView\QUANTUMVIEWResponseWrapper**](../Model/QUANTUMVIEWResponseWrapper.md)

### Authorization

[oauth2](../../README.md#oauth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

