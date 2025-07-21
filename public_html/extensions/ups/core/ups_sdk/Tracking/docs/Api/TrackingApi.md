# UPS\Tracking\TrackingApi

All URIs are relative to *https://wwwcie.ups.com/api*

Method | HTTP request | Description
------------- | ------------- | -------------
[**getSingleTrackResponseUsingGET**](TrackingApi.md#getsingletrackresponseusingget) | **GET** /track/v1/details/{inquiryNumber} | Tracking
[**referenceTrackingAPI**](TrackingApi.md#referencetrackingapi) | **GET** /track/v1/reference/details/{referenceNumber} | Track by Reference Number

# **getSingleTrackResponseUsingGET**
> \UPS\Tracking\Tracking\TrackApiResponse getSingleTrackResponseUsingGET($inquiry_number, $trans_id, $transaction_src, $locale, $return_signature, $return_milestones, $return_pod)

Tracking

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: OAuth2
$config = UPS\Tracking\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\Tracking\Request\TrackingApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$inquiry_number = "inquiry_number_example"; // string | The tracking number for which tracking information is requested. Each inquiry number must be between 7 and 34 characters in length.
$trans_id = "trans_id_example"; // string | An identifier unique to the request.
$transaction_src = "testing"; // string | Identifies the client/source application that is calling
$locale = "en_US"; // string | Language and country code of the user, separated by an underscore. Default value is 'en_US'
$return_signature = "false"; // string | Indicator requesting that the delivery signature image be included as part of the response (by default the image will not be returned). Returns image bytecodes of the signature.
$return_milestones = "false"; // string | returnMilestones
$return_pod = "false"; // string | Return Proof of Delivery

try {
    $result = $apiInstance->getSingleTrackResponseUsingGET($inquiry_number, $trans_id, $transaction_src, $locale, $return_signature, $return_milestones, $return_pod);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling TrackingApi->getSingleTrackResponseUsingGET: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **inquiry_number** | **string**| The tracking number for which tracking information is requested. Each inquiry number must be between 7 and 34 characters in length. |
 **trans_id** | **string**| An identifier unique to the request. |
 **transaction_src** | **string**| Identifies the client/source application that is calling | [default to testing]
 **locale** | **string**| Language and country code of the user, separated by an underscore. Default value is &#x27;en_US&#x27; | [optional] [default to en_US]
 **return_signature** | **string**| Indicator requesting that the delivery signature image be included as part of the response (by default the image will not be returned). Returns image bytecodes of the signature. | [optional] [default to false]
 **return_milestones** | **string**| returnMilestones | [optional] [default to false]
 **return_pod** | **string**| Return Proof of Delivery | [optional] [default to false]

### Return type

[**\UPS\Tracking\Tracking\TrackApiResponse**](../Model/TrackApiResponse.md)

### Authorization

[OAuth2](../../README.md#OAuth2)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **referenceTrackingAPI**
> \UPS\Tracking\Tracking\TrackApiResponse referenceTrackingAPI($reference_number, $trans_id, $transaction_src, $locale, $from_pick_up_date, $to_pick_up_date, $ref_num_type)

Track by Reference Number

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: OAuth2
$config = UPS\Tracking\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\Tracking\Request\TrackingApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$reference_number = "reference_number_example"; // string | The reference number for which tracking information is requested.
$trans_id = "trans_id_example"; // string | An identifier unique to the request.
$transaction_src = "testing"; // string | Identifies the client/source application that is calling
$locale = "en_US"; // string | Language and country code of the user, separated by an underscore. Default value is 'en_US'
$from_pick_up_date = "currentDate-14"; // string | The tracking information for the above reference number will be searched from this date
$to_pick_up_date = "currentDate"; // string | The tracking information for the above reference number will be searched till this date
$ref_num_type = "SmallPackage. Valid values: SmallPackage, fgv"; // string | The Reference number type which will define the tracking information is related to small package or fgv

try {
    $result = $apiInstance->referenceTrackingAPI($reference_number, $trans_id, $transaction_src, $locale, $from_pick_up_date, $to_pick_up_date, $ref_num_type);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling TrackingApi->referenceTrackingAPI: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **reference_number** | **string**| The reference number for which tracking information is requested. |
 **trans_id** | **string**| An identifier unique to the request. |
 **transaction_src** | **string**| Identifies the client/source application that is calling | [default to testing]
 **locale** | **string**| Language and country code of the user, separated by an underscore. Default value is &#x27;en_US&#x27; | [optional] [default to en_US]
 **from_pick_up_date** | **string**| The tracking information for the above reference number will be searched from this date | [optional] [default to currentDate-14]
 **to_pick_up_date** | **string**| The tracking information for the above reference number will be searched till this date | [optional] [default to currentDate]
 **ref_num_type** | **string**| The Reference number type which will define the tracking information is related to small package or fgv | [optional] [default to SmallPackage. Valid values: SmallPackage, fgv]

### Return type

[**\UPS\Tracking\Tracking\TrackApiResponse**](../Model/TrackApiResponse.md)

### Authorization

[OAuth2](../../README.md#OAuth2)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

