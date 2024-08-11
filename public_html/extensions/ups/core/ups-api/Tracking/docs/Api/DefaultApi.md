# UPS\Tracking\DefaultApi

All URIs are relative to *https://wwwcie.ups.com/api*

Method | HTTP request | Description
------------- | ------------- | -------------
[**getSingleTrackResponseUsingGET**](DefaultApi.md#getsingletrackresponseusingget) | **GET** /track/v1/details/{inquiryNumber} | Tracking

# **getSingleTrackResponseUsingGET**
> \UPS\Tracking\Tracking\TrackApiResponse getSingleTrackResponseUsingGET($inquiry_number, $trans_id, $transaction_src, $locale, $return_signature, $return_milestones, $return_pod)

Tracking

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: oauth2
$config = UPS\Tracking\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\Tracking\Request\DefaultApi(
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
    echo 'Exception when calling DefaultApi->getSingleTrackResponseUsingGET: ', $e->getMessage(), PHP_EOL;
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

[oauth2](../../README.md#oauth2)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

