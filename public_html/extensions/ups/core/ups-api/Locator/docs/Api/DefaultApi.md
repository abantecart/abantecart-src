# UPS\Locator\DefaultApi

All URIs are relative to *https://wwwcie.ups.com/api*

Method | HTTP request | Description
------------- | ------------- | -------------
[**locator**](DefaultApi.md#locator) | **POST** /locations/{version}/search/availabilities/{reqOption} | Locator
[**locator_0**](DefaultApi.md#locator_0) | **POST** /locations/{deprecatedVersion}/search/availabilities/{reqOption} | Locator

# **locator**
> \UPS\Locator\Locator\LOCATORResponseWrapper locator($body, $version, $req_option, $trans_id, $transaction_src, $locale)

Locator

The Locator API allows you to find UPS locations - such as drop-off points, retail locations, and UPS access points (third-party retail locations that offer UPS package drop-off, or delivery services). The API provides capabilities to search by location, services offered, program types, and related criteria. You can also retrieve hours of operation, location details, and additional UPS services offered at specific locations.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: oauth2
$config = UPS\Locator\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\Locator\Request\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \UPS\Locator\Locator\LOCATORRequestWrapper(); // \UPS\Locator\Locator\LOCATORRequestWrapper | Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click "Authorize" and enter your application credentials, then populate the required parameters above and click "Try it out".
$version = "version_example"; // string | Version of API  Valid values: - v2
$req_option = "req_option_example"; // string | Indicates the type of request. Valid values: 1-Locations (Drop Locations and Will call locations) 8-All available Additional Services 16-All available Program Types 24-All available Additional Services and Program types 32-All available Retail Locations 40-All available Retail Locations and Additional Services  48-All available Retail Locations and Program Types  56-All available Retail Locations, Additional Services and Program Types  64-Search for UPS Access Point Locations.
$trans_id = "trans_id_example"; // string | An identifier unique to the request. Length 32
$transaction_src = "testing"; // string | An identifier of the client/source application that is making the request.Length 512
$locale = "en_US"; // string | Locale of request

try {
    $result = $apiInstance->locator($body, $version, $req_option, $trans_id, $transaction_src, $locale);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->locator: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\UPS\Locator\Locator\LOCATORRequestWrapper**](../Model/LOCATORRequestWrapper.md)| Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click &quot;Authorize&quot; and enter your application credentials, then populate the required parameters above and click &quot;Try it out&quot;. |
 **version** | **string**| Version of API  Valid values: - v2 |
 **req_option** | **string**| Indicates the type of request. Valid values: 1-Locations (Drop Locations and Will call locations) 8-All available Additional Services 16-All available Program Types 24-All available Additional Services and Program types 32-All available Retail Locations 40-All available Retail Locations and Additional Services  48-All available Retail Locations and Program Types  56-All available Retail Locations, Additional Services and Program Types  64-Search for UPS Access Point Locations. |
 **trans_id** | **string**| An identifier unique to the request. Length 32 | [optional]
 **transaction_src** | **string**| An identifier of the client/source application that is making the request.Length 512 | [optional] [default to testing]
 **locale** | **string**| Locale of request | [optional] [default to en_US]

### Return type

[**\UPS\Locator\Locator\LOCATORResponseWrapper**](../Model/LOCATORResponseWrapper.md)

### Authorization

[oauth2](../../README.md#oauth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **locator_0**
> \UPS\Locator\Locator\LOCATORResponseWrapper locator_0($body, $deprecated_version, $req_option, $trans_id, $transaction_src, $locale)

Locator

The Locator API allows you to find UPS locations - such as drop-off points, retail locations, and UPS access points (third-party retail locations that offer UPS package drop-off, or delivery services). The API provides capabilities to search by location, services offered, program types, and related criteria. You can also retrieve hours of operation, location details, and additional UPS services offered at specific locations.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: oauth2
$config = UPS\Locator\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\Locator\Request\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \UPS\Locator\Locator\LOCATORRequestWrapper(); // \UPS\Locator\Locator\LOCATORRequestWrapper | Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click "Authorize" and enter your application credentials, then populate the required parameters above and click "Try it out".
$deprecated_version = "deprecated_version_example"; // string | Version of API  Valid values: - v1
$req_option = "req_option_example"; // string | Indicates the type of request. Valid values: 1-Locations (Drop Locations and Will call locations) 8-All available Additional Services 16-All available Program Types 24-All available Additional Services and Program types 32-All available Retail Locations 40-All available Retail Locations and Additional Services  48-All available Retail Locations and Program Types  56-All available Retail Locations, Additional Services and Program Types  64-Search for UPS Access Point Locations.
$trans_id = "trans_id_example"; // string | An identifier unique to the request. Length 32
$transaction_src = "testing"; // string | An identifier of the client/source application that is making the request.Length 512
$locale = "en_US"; // string | Locale of request

try {
    $result = $apiInstance->locator_0($body, $deprecated_version, $req_option, $trans_id, $transaction_src, $locale);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->locator_0: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\UPS\Locator\Locator\LOCATORRequestWrapper**](../Model/LOCATORRequestWrapper.md)| Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click &quot;Authorize&quot; and enter your application credentials, then populate the required parameters above and click &quot;Try it out&quot;. |
 **deprecated_version** | **string**| Version of API  Valid values: - v1 |
 **req_option** | **string**| Indicates the type of request. Valid values: 1-Locations (Drop Locations and Will call locations) 8-All available Additional Services 16-All available Program Types 24-All available Additional Services and Program types 32-All available Retail Locations 40-All available Retail Locations and Additional Services  48-All available Retail Locations and Program Types  56-All available Retail Locations, Additional Services and Program Types  64-Search for UPS Access Point Locations. |
 **trans_id** | **string**| An identifier unique to the request. Length 32 | [optional]
 **transaction_src** | **string**| An identifier of the client/source application that is making the request.Length 512 | [optional] [default to testing]
 **locale** | **string**| Locale of request | [optional] [default to en_US]

### Return type

[**\UPS\Locator\Locator\LOCATORResponseWrapper**](../Model/LOCATORResponseWrapper.md)

### Authorization

[oauth2](../../README.md#oauth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

