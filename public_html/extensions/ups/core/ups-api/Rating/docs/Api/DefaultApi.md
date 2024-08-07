# UPS\Rating\DefaultApi

All URIs are relative to *https://wwwcie.ups.com/api*

Method | HTTP request | Description
------------- | ------------- | -------------
[**deprecatedRate**](DefaultApi.md#deprecatedrate) | **POST** /rating/{deprecatedVersion}/{requestoption} | Rating
[**rate**](DefaultApi.md#rate) | **POST** /rating/{version}/{requestoption} | Rating

# **deprecatedRate**
> \UPS\Rating\Rating\RATEResponseWrapper deprecatedRate($body, $deprecated_version, $requestoption, $trans_id, $transaction_src, $additionalinfo)

Rating

The Rating API is used when rating or shopping a shipment.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: oauth2
$config = UPS\Rating\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\Rating\Request\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \UPS\Rating\Rating\RATERequestWrapper(); // \UPS\Rating\Rating\RATERequestWrapper | Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click "Authorize" and enter your application credentials, then populate the required parameters above and click "Try it out".
$deprecated_version = "deprecated_version_example"; // string | Indicates Rate API to display the new release features in Rate API response based on Rate release. See the New section for the latest Rate release.  Valid values: - v1 - v1601 - v1607 - 1701 - 1707 - v2108 - v2205
$requestoption = "requestoption_example"; // string | Valid Values: - Rate = The server rates (The default Request option is Rate if a Request Option is not provided). - Shop = The server validates the shipment, and returns rates for all UPS products from the ShipFrom to the ShipTo addresses. - Ratetimeintransit = The server rates with transit time information - Shoptimeintransit = The server validates the shipment, and returns rates and transit times for all UPS products from the ShipFrom to the ShipTo addresses.  Rate is the only valid request option for UPS Ground Freight Pricing requests.
$trans_id = "trans_id_example"; // string | An identifier unique to the request. Length 32
$transaction_src = "testing"; // string | An identifier of the client/source application that is making the request.Length 512
$additionalinfo = "additionalinfo_example"; // string | Valid Values: timeintransit = The server rates with transit time information combined with requestoption in URL.Rate is the only valid request option for Ground Freight Pricing requests. Length 15

try {
    $result = $apiInstance->deprecatedRate($body, $deprecated_version, $requestoption, $trans_id, $transaction_src, $additionalinfo);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->deprecatedRate: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\UPS\Rating\Rating\RATERequestWrapper**](../Model/RATERequestWrapper.md)| Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click &quot;Authorize&quot; and enter your application credentials, then populate the required parameters above and click &quot;Try it out&quot;. |
 **deprecated_version** | **string**| Indicates Rate API to display the new release features in Rate API response based on Rate release. See the New section for the latest Rate release.  Valid values: - v1 - v1601 - v1607 - 1701 - 1707 - v2108 - v2205 |
 **requestoption** | **string**| Valid Values: - Rate &#x3D; The server rates (The default Request option is Rate if a Request Option is not provided). - Shop &#x3D; The server validates the shipment, and returns rates for all UPS products from the ShipFrom to the ShipTo addresses. - Ratetimeintransit &#x3D; The server rates with transit time information - Shoptimeintransit &#x3D; The server validates the shipment, and returns rates and transit times for all UPS products from the ShipFrom to the ShipTo addresses.  Rate is the only valid request option for UPS Ground Freight Pricing requests. |
 **trans_id** | **string**| An identifier unique to the request. Length 32 | [optional]
 **transaction_src** | **string**| An identifier of the client/source application that is making the request.Length 512 | [optional] [default to testing]
 **additionalinfo** | **string**| Valid Values: timeintransit &#x3D; The server rates with transit time information combined with requestoption in URL.Rate is the only valid request option for Ground Freight Pricing requests. Length 15 | [optional]

### Return type

[**\UPS\Rating\Rating\RATEResponseWrapper**](../Model/RATEResponseWrapper.md)

### Authorization

[oauth2](../../README.md#oauth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **rate**
> \UPS\Rating\Rating\RATEResponseWrapper rate($body, $version, $requestoption, $trans_id, $transaction_src, $additionalinfo)

Rating

The Rating API is used when rating or shopping a shipment.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: oauth2
$config = UPS\Rating\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\Rating\Request\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \UPS\Rating\Rating\RATERequestWrapper(); // \UPS\Rating\Rating\RATERequestWrapper | Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click "Authorize" and enter your application credentials, then populate the required parameters above and click "Try it out".
$version = "version_example"; // string | Indicates Rate API to display the new release features in Rate API response based on Rate release. See the New section for the latest Rate release.  Valid values: - v2403
$requestoption = "requestoption_example"; // string | Valid Values: - Rate = The server rates (The default Request option is Rate if a Request Option is not provided). - Shop = The server validates the shipment, and returns rates for all UPS products from the ShipFrom to the ShipTo addresses. - Ratetimeintransit = The server rates with transit time information - Shoptimeintransit = The server validates the shipment, and returns rates and transit times for all UPS products from the ShipFrom to the ShipTo addresses.  Rate is the only valid request option for UPS Ground Freight Pricing requests.
$trans_id = "trans_id_example"; // string | An identifier unique to the request. Length 32
$transaction_src = "testing"; // string | An identifier of the client/source application that is making the request.Length 512
$additionalinfo = "additionalinfo_example"; // string | Valid Values: timeintransit = The server rates with transit time information combined with requestoption in URL.Rate is the only valid request option for Ground Freight Pricing requests. Length 15

try {
    $result = $apiInstance->rate($body, $version, $requestoption, $trans_id, $transaction_src, $additionalinfo);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->rate: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\UPS\Rating\Rating\RATERequestWrapper**](../Model/RATERequestWrapper.md)| Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click &quot;Authorize&quot; and enter your application credentials, then populate the required parameters above and click &quot;Try it out&quot;. |
 **version** | **string**| Indicates Rate API to display the new release features in Rate API response based on Rate release. See the New section for the latest Rate release.  Valid values: - v2403 |
 **requestoption** | **string**| Valid Values: - Rate &#x3D; The server rates (The default Request option is Rate if a Request Option is not provided). - Shop &#x3D; The server validates the shipment, and returns rates for all UPS products from the ShipFrom to the ShipTo addresses. - Ratetimeintransit &#x3D; The server rates with transit time information - Shoptimeintransit &#x3D; The server validates the shipment, and returns rates and transit times for all UPS products from the ShipFrom to the ShipTo addresses.  Rate is the only valid request option for UPS Ground Freight Pricing requests. |
 **trans_id** | **string**| An identifier unique to the request. Length 32 | [optional]
 **transaction_src** | **string**| An identifier of the client/source application that is making the request.Length 512 | [optional] [default to testing]
 **additionalinfo** | **string**| Valid Values: timeintransit &#x3D; The server rates with transit time information combined with requestoption in URL.Rate is the only valid request option for Ground Freight Pricing requests. Length 15 | [optional]

### Return type

[**\UPS\Rating\Rating\RATEResponseWrapper**](../Model/RATEResponseWrapper.md)

### Authorization

[oauth2](../../README.md#oauth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

