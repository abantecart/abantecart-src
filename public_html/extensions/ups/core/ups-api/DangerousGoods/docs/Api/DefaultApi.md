# UPS\DangerousGoods\DefaultApi

All URIs are relative to *https://wwwcie.ups.com/api*

Method | HTTP request | Description
------------- | ------------- | -------------
[**acceptanceAuditPreCheck**](DefaultApi.md#acceptanceauditprecheck) | **POST** /dangerousgoods/{version}/acceptanceauditprecheck | Acceptance Audit Pre-check
[**chemicalReferenceData**](DefaultApi.md#chemicalreferencedata) | **POST** /dangerousgoods/{version}/chemicalreferencedata | Chemical Reference Data
[**deprecatedAcceptanceAuditPreCheck**](DefaultApi.md#deprecatedacceptanceauditprecheck) | **POST** /dangerousgoods/{deprecatedVersion}/acceptanceauditprecheck | Acceptance Audit Pre-check
[**deprecatedChemicalReferenceData**](DefaultApi.md#deprecatedchemicalreferencedata) | **POST** /dangerousgoods/{deprecatedVersion}/chemicalreferencedata | Chemical Reference Data

# **acceptanceAuditPreCheck**
> \UPS\DangerousGoods\DangerousGoods\DANGEROUSGOODSUTILITYAPCResponseWrapper acceptanceAuditPreCheck($body, $trans_id, $transaction_src, $version)

Acceptance Audit Pre-check

Enables shippers perform pre-checks before shipping dangerous goods using the chemical record identifier and the commodity's regulated level code.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: oauth2
$config = UPS\DangerousGoods\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\DangerousGoods\Request\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \UPS\DangerousGoods\DangerousGoods\DANGEROUSGOODSUTILITYAPCRequestWrapper(); // \UPS\DangerousGoods\DangerousGoods\DANGEROUSGOODSUTILITYAPCRequestWrapper | Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click "Authorize" and enter your application credentials, then populate the required parameters above and click "Try it out".
$trans_id = "trans_id_example"; // string | An identifier unique to the request. Length 32
$transaction_src = "testing"; // string | An identifier of the client/source application that is making the request.Length 512
$version = "version_example"; // string | API version  Valid values: - v2

try {
    $result = $apiInstance->acceptanceAuditPreCheck($body, $trans_id, $transaction_src, $version);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->acceptanceAuditPreCheck: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\UPS\DangerousGoods\DangerousGoods\DANGEROUSGOODSUTILITYAPCRequestWrapper**](../Model/DANGEROUSGOODSUTILITYAPCRequestWrapper.md)| Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click &quot;Authorize&quot; and enter your application credentials, then populate the required parameters above and click &quot;Try it out&quot;. |
 **trans_id** | **string**| An identifier unique to the request. Length 32 |
 **transaction_src** | **string**| An identifier of the client/source application that is making the request.Length 512 | [default to testing]
 **version** | **string**| API version  Valid values: - v2 |

### Return type

[**\UPS\DangerousGoods\DangerousGoods\DANGEROUSGOODSUTILITYAPCResponseWrapper**](../Model/DANGEROUSGOODSUTILITYAPCResponseWrapper.md)

### Authorization

[oauth2](../../README.md#oauth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **chemicalReferenceData**
> \UPS\DangerousGoods\DangerousGoods\DANGEROUSGOODSUTILITYResponseWrapper chemicalReferenceData($body, $trans_id, $transaction_src, $version)

Chemical Reference Data

The Chemical Reference Data endpoint of the Dangerous Goods API allows shippers look up hazardous material reference information by ID number and shipping name of the specified regulated good.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: oauth2
$config = UPS\DangerousGoods\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\DangerousGoods\Request\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \UPS\DangerousGoods\DangerousGoods\DANGEROUSGOODSUTILITYRequestWrapper(); // \UPS\DangerousGoods\DangerousGoods\DANGEROUSGOODSUTILITYRequestWrapper | Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click "Authorize" and enter your application credentials, then populate the required parameters above and click "Try it out".
$trans_id = "trans_id_example"; // string | An identifier unique to the request. Length 32
$transaction_src = "testing"; // string | An identifier of the client/source application that is making the request.Length 512
$version = "version_example"; // string | Version of the API.  Valid values: - v2403

try {
    $result = $apiInstance->chemicalReferenceData($body, $trans_id, $transaction_src, $version);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->chemicalReferenceData: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\UPS\DangerousGoods\DangerousGoods\DANGEROUSGOODSUTILITYRequestWrapper**](../Model/DANGEROUSGOODSUTILITYRequestWrapper.md)| Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click &quot;Authorize&quot; and enter your application credentials, then populate the required parameters above and click &quot;Try it out&quot;. |
 **trans_id** | **string**| An identifier unique to the request. Length 32 |
 **transaction_src** | **string**| An identifier of the client/source application that is making the request.Length 512 | [default to testing]
 **version** | **string**| Version of the API.  Valid values: - v2403 |

### Return type

[**\UPS\DangerousGoods\DangerousGoods\DANGEROUSGOODSUTILITYResponseWrapper**](../Model/DANGEROUSGOODSUTILITYResponseWrapper.md)

### Authorization

[oauth2](../../README.md#oauth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deprecatedAcceptanceAuditPreCheck**
> \UPS\DangerousGoods\DangerousGoods\DANGEROUSGOODSUTILITYAPCResponseWrapper deprecatedAcceptanceAuditPreCheck($body, $trans_id, $transaction_src, $deprecated_version)

Acceptance Audit Pre-check

Enables shippers perform pre-checks before shipping dangerous goods using the chemical record identifier and the commodity's regulated level code.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: oauth2
$config = UPS\DangerousGoods\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\DangerousGoods\Request\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \UPS\DangerousGoods\DangerousGoods\DANGEROUSGOODSUTILITYAPCRequestWrapper(); // \UPS\DangerousGoods\DangerousGoods\DANGEROUSGOODSUTILITYAPCRequestWrapper | Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click "Authorize" and enter your application credentials, then populate the required parameters above and click "Try it out".
$trans_id = "trans_id_example"; // string | An identifier unique to the request. Length 32
$transaction_src = "testing"; // string | An identifier of the client/source application that is making the request.Length 512
$deprecated_version = "deprecated_version_example"; // string | API version  Valid values: - v1

try {
    $result = $apiInstance->deprecatedAcceptanceAuditPreCheck($body, $trans_id, $transaction_src, $deprecated_version);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->deprecatedAcceptanceAuditPreCheck: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\UPS\DangerousGoods\DangerousGoods\DANGEROUSGOODSUTILITYAPCRequestWrapper**](../Model/DANGEROUSGOODSUTILITYAPCRequestWrapper.md)| Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click &quot;Authorize&quot; and enter your application credentials, then populate the required parameters above and click &quot;Try it out&quot;. |
 **trans_id** | **string**| An identifier unique to the request. Length 32 |
 **transaction_src** | **string**| An identifier of the client/source application that is making the request.Length 512 | [default to testing]
 **deprecated_version** | **string**| API version  Valid values: - v1 |

### Return type

[**\UPS\DangerousGoods\DangerousGoods\DANGEROUSGOODSUTILITYAPCResponseWrapper**](../Model/DANGEROUSGOODSUTILITYAPCResponseWrapper.md)

### Authorization

[oauth2](../../README.md#oauth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deprecatedChemicalReferenceData**
> \UPS\DangerousGoods\DangerousGoods\DANGEROUSGOODSUTILITYResponseWrapper deprecatedChemicalReferenceData($body, $trans_id, $transaction_src, $deprecated_version)

Chemical Reference Data

The Chemical Reference Data endpoint of the Dangerous Goods API allows shippers look up hazardous material reference information by ID number and shipping name of the specified regulated good.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: oauth2
$config = UPS\DangerousGoods\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\DangerousGoods\Request\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \UPS\DangerousGoods\DangerousGoods\DANGEROUSGOODSUTILITYRequestWrapper(); // \UPS\DangerousGoods\DangerousGoods\DANGEROUSGOODSUTILITYRequestWrapper | Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click "Authorize" and enter your application credentials, then populate the required parameters above and click "Try it out".
$trans_id = "trans_id_example"; // string | An identifier unique to the request. Length 32
$transaction_src = "testing"; // string | An identifier of the client/source application that is making the request.Length 512
$deprecated_version = "deprecated_version_example"; // string | Version of the API.  Valid values: - v1 - v1801.

try {
    $result = $apiInstance->deprecatedChemicalReferenceData($body, $trans_id, $transaction_src, $deprecated_version);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->deprecatedChemicalReferenceData: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\UPS\DangerousGoods\DangerousGoods\DANGEROUSGOODSUTILITYRequestWrapper**](../Model/DANGEROUSGOODSUTILITYRequestWrapper.md)| Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click &quot;Authorize&quot; and enter your application credentials, then populate the required parameters above and click &quot;Try it out&quot;. |
 **trans_id** | **string**| An identifier unique to the request. Length 32 |
 **transaction_src** | **string**| An identifier of the client/source application that is making the request.Length 512 | [default to testing]
 **deprecated_version** | **string**| Version of the API.  Valid values: - v1 - v1801. |

### Return type

[**\UPS\DangerousGoods\DangerousGoods\DANGEROUSGOODSUTILITYResponseWrapper**](../Model/DANGEROUSGOODSUTILITYResponseWrapper.md)

### Authorization

[oauth2](../../README.md#oauth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

