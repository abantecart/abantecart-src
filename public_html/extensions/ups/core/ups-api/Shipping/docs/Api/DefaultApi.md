# UPS\Shipping\DefaultApi

All URIs are relative to *https://wwwcie.ups.com/api*

Method | HTTP request | Description
------------- | ------------- | -------------
[**deprecatedShipment**](DefaultApi.md#deprecatedshipment) | **POST** /shipments/{deprecatedVersion}/ship | Shipment
[**deprecatedVoidShipment**](DefaultApi.md#deprecatedvoidshipment) | **DELETE** /shipments/{deprecatedVersion}/void/cancel/{shipmentidentificationnumber} | Void Shipment
[**labelRecovery**](DefaultApi.md#labelrecovery) | **POST** /labels/{version}/recovery | Label Recovery
[**shipment**](DefaultApi.md#shipment) | **POST** /shipments/{version}/ship | Shipment
[**voidShipment**](DefaultApi.md#voidshipment) | **DELETE** /shipments/{version}/void/cancel/{shipmentidentificationnumber} | Void Shipment

# **deprecatedShipment**
> \UPS\Shipping\Shipping\SHIPResponseWrapper deprecatedShipment($body, $deprecated_version, $trans_id, $transaction_src, $additionaladdressvalidation)

Shipment

The Shipping API makes UPS shipping services available to client applications that communicate with UPS  using the Internet

### Example
```php
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
$deprecated_version = "deprecated_version_example"; // string | Indicates Ship API to display the new release features in Ship API response based on Ship release.  Valid values: - v1 - v1601 - v1607 - v1701 - v1707 - v1801 - v1807 - v2108 - v2205
$trans_id = "trans_id_example"; // string | An identifier unique to the request. Length 32
$transaction_src = "testing"; // string | An identifier of the client/source application that is making the request.Length 512
$additionaladdressvalidation = "additionaladdressvalidation_example"; // string | Valid Values:  city = validation will include city.Length 15

try {
    $result = $apiInstance->deprecatedShipment($body, $deprecated_version, $trans_id, $transaction_src, $additionaladdressvalidation);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->deprecatedShipment: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\UPS\Shipping\Shipping\SHIPRequestWrapper**](../Model/SHIPRequestWrapper.md)| Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click &quot;Authorize&quot; and enter your application credentials, then populate the required parameters above and click &quot;Try it out&quot;. |
 **deprecated_version** | **string**| Indicates Ship API to display the new release features in Ship API response based on Ship release.  Valid values: - v1 - v1601 - v1607 - v1701 - v1707 - v1801 - v1807 - v2108 - v2205 |
 **trans_id** | **string**| An identifier unique to the request. Length 32 | [optional]
 **transaction_src** | **string**| An identifier of the client/source application that is making the request.Length 512 | [optional] [default to testing]
 **additionaladdressvalidation** | **string**| Valid Values:  city &#x3D; validation will include city.Length 15 | [optional]

### Return type

[**\UPS\Shipping\Shipping\SHIPResponseWrapper**](../Model/SHIPResponseWrapper.md)

### Authorization

[oauth2](../../README.md#oauth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deprecatedVoidShipment**
> \UPS\Shipping\Shipping\VOIDSHIPMENTResponseWrapper deprecatedVoidShipment($deprecated_version, $shipmentidentificationnumber, $trans_id, $transaction_src, $trackingnumber)

Void Shipment

The Void Shipping API is used to cancel the previously scheduled shipment

### Example
```php
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
$deprecated_version = "deprecated_version_example"; // string | API Version.  Valid values: - v1
$shipmentidentificationnumber = "shipmentidentificationnumber_example"; // string | The shipment's identification number  Alpha-numeric. Must pass 1Z rules. Must be  upper case. Length 18
$trans_id = "trans_id_example"; // string | An identifier unique to the request. Length 32
$transaction_src = "testing"; // string | An identifier of the client/source application that is making the request.Length 512
$trackingnumber = "trackingnumber_example"; // string | The package's tracking number. You may have  up to 20 different tracking numbers listed. If more than one tracking number, pass this  value as: trackingnumber=  [\"1ZISUS010330563105\",\"1ZISUS01033056310 8\"] with a coma separating each number. Alpha-numeric. Must pass 1Z rules. Must be  upper case. Length 18

try {
    $result = $apiInstance->deprecatedVoidShipment($deprecated_version, $shipmentidentificationnumber, $trans_id, $transaction_src, $trackingnumber);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->deprecatedVoidShipment: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **deprecated_version** | **string**| API Version.  Valid values: - v1 |
 **shipmentidentificationnumber** | **string**| The shipment&#x27;s identification number  Alpha-numeric. Must pass 1Z rules. Must be  upper case. Length 18 |
 **trans_id** | **string**| An identifier unique to the request. Length 32 | [optional]
 **transaction_src** | **string**| An identifier of the client/source application that is making the request.Length 512 | [optional] [default to testing]
 **trackingnumber** | **string**| The package&#x27;s tracking number. You may have  up to 20 different tracking numbers listed. If more than one tracking number, pass this  value as: trackingnumber&#x3D;  [\&quot;1ZISUS010330563105\&quot;,\&quot;1ZISUS01033056310 8\&quot;] with a coma separating each number. Alpha-numeric. Must pass 1Z rules. Must be  upper case. Length 18 | [optional]

### Return type

[**\UPS\Shipping\Shipping\VOIDSHIPMENTResponseWrapper**](../Model/VOIDSHIPMENTResponseWrapper.md)

### Authorization

[oauth2](../../README.md#oauth2)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **labelRecovery**
> \UPS\Shipping\Shipping\LABELRECOVERYResponseWrapper labelRecovery($body, $version, $trans_id, $transaction_src)

Label Recovery

The Label Shipping API allows us to retrieve forward and return labels.

### Example
```php
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
$body = new \UPS\Shipping\Shipping\LABELRECOVERYRequestWrapper(); // \UPS\Shipping\Shipping\LABELRECOVERYRequestWrapper | Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click "Authorize" and enter your application credentials, then populate the required parameters above and click "Try it out".
$version = "version_example"; // string | When UPS introduces new elements in the  response that are not associated with new  request elements, Subversion is used. This  ensures backward compatibility.  v1  original features of the application. No  support for CODTurn-inPage, HighValueReport  or InternationalForms features returned in the  response v1701  includes support for CODTurn-inPage  features returned in the response. V1903  Length 5
$trans_id = "trans_id_example"; // string | An identifier unique to the request. Length 32
$transaction_src = "testing"; // string | An identifier of the client/source application that is making the request.Length 512

try {
    $result = $apiInstance->labelRecovery($body, $version, $trans_id, $transaction_src);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->labelRecovery: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\UPS\Shipping\Shipping\LABELRECOVERYRequestWrapper**](../Model/LABELRECOVERYRequestWrapper.md)| Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click &quot;Authorize&quot; and enter your application credentials, then populate the required parameters above and click &quot;Try it out&quot;. |
 **version** | **string**| When UPS introduces new elements in the  response that are not associated with new  request elements, Subversion is used. This  ensures backward compatibility.  v1  original features of the application. No  support for CODTurn-inPage, HighValueReport  or InternationalForms features returned in the  response v1701  includes support for CODTurn-inPage  features returned in the response. V1903  Length 5 |
 **trans_id** | **string**| An identifier unique to the request. Length 32 | [optional]
 **transaction_src** | **string**| An identifier of the client/source application that is making the request.Length 512 | [optional] [default to testing]

### Return type

[**\UPS\Shipping\Shipping\LABELRECOVERYResponseWrapper**](../Model/LABELRECOVERYResponseWrapper.md)

### Authorization

[oauth2](../../README.md#oauth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **shipment**
> \UPS\Shipping\Shipping\SHIPResponseWrapper shipment($body, $version, $trans_id, $transaction_src, $additionaladdressvalidation)

Shipment

The Shipping API makes UPS shipping services available to client applications that communicate with UPS  using the Internet

### Example
```php
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
$version = "version_example"; // string | Indicates Ship API to display the new release features in Ship API response based on Ship release.  Valid values: - v2403
$trans_id = "trans_id_example"; // string | An identifier unique to the request. Length 32
$transaction_src = "testing"; // string | An identifier of the client/source application that is making the request.Length 512
$additionaladdressvalidation = "additionaladdressvalidation_example"; // string | Valid Values:  city = validation will include city.Length 15

try {
    $result = $apiInstance->shipment($body, $version, $trans_id, $transaction_src, $additionaladdressvalidation);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->shipment: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\UPS\Shipping\Shipping\SHIPRequestWrapper**](../Model/SHIPRequestWrapper.md)| Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click &quot;Authorize&quot; and enter your application credentials, then populate the required parameters above and click &quot;Try it out&quot;. |
 **version** | **string**| Indicates Ship API to display the new release features in Ship API response based on Ship release.  Valid values: - v2403 |
 **trans_id** | **string**| An identifier unique to the request. Length 32 | [optional]
 **transaction_src** | **string**| An identifier of the client/source application that is making the request.Length 512 | [optional] [default to testing]
 **additionaladdressvalidation** | **string**| Valid Values:  city &#x3D; validation will include city.Length 15 | [optional]

### Return type

[**\UPS\Shipping\Shipping\SHIPResponseWrapper**](../Model/SHIPResponseWrapper.md)

### Authorization

[oauth2](../../README.md#oauth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **voidShipment**
> \UPS\Shipping\Shipping\VOIDSHIPMENTResponseWrapper voidShipment($version, $shipmentidentificationnumber, $trans_id, $transaction_src, $trackingnumber)

Void Shipment

The Void Shipping API is used to cancel the previously scheduled shipment

### Example
```php
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
$version = "version_example"; // string | API Version  Valid values: - v2403
$shipmentidentificationnumber = "shipmentidentificationnumber_example"; // string | The shipment's identification number  Alpha-numeric. Must pass 1Z rules. Must be  upper case. Length 18
$trans_id = "trans_id_example"; // string | An identifier unique to the request. Length 32
$transaction_src = "testing"; // string | An identifier of the client/source application that is making the request.Length 512
$trackingnumber = "trackingnumber_example"; // string | The package's tracking number. You may have  up to 20 different tracking numbers listed. If more than one tracking number, pass this  value as: trackingnumber=  [\"1ZISUS010330563105\",\"1ZISUS01033056310 8\"] with a coma separating each number. Alpha-numeric. Must pass 1Z rules. Must be  upper case. Length 18

try {
    $result = $apiInstance->voidShipment($version, $shipmentidentificationnumber, $trans_id, $transaction_src, $trackingnumber);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->voidShipment: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **version** | **string**| API Version  Valid values: - v2403 |
 **shipmentidentificationnumber** | **string**| The shipment&#x27;s identification number  Alpha-numeric. Must pass 1Z rules. Must be  upper case. Length 18 |
 **trans_id** | **string**| An identifier unique to the request. Length 32 | [optional]
 **transaction_src** | **string**| An identifier of the client/source application that is making the request.Length 512 | [optional] [default to testing]
 **trackingnumber** | **string**| The package&#x27;s tracking number. You may have  up to 20 different tracking numbers listed. If more than one tracking number, pass this  value as: trackingnumber&#x3D;  [\&quot;1ZISUS010330563105\&quot;,\&quot;1ZISUS01033056310 8\&quot;] with a coma separating each number. Alpha-numeric. Must pass 1Z rules. Must be  upper case. Length 18 | [optional]

### Return type

[**\UPS\Shipping\Shipping\VOIDSHIPMENTResponseWrapper**](../Model/VOIDSHIPMENTResponseWrapper.md)

### Authorization

[oauth2](../../README.md#oauth2)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

