# UPS\UPSTrackAlert\UPSTrackAlertApi

All URIs are relative to *https://wwwcie.ups.com/api/track/{version}*

Method | HTTP request | Description
------------- | ------------- | -------------
[**processSubscriptionTypeForTrackingNumber**](UPSTrackAlertApi.md#processsubscriptiontypefortrackingnumber) | **POST** /subscription/{type}/package | API to create subscriptions by tracking numbers.

# **processSubscriptionTypeForTrackingNumber**
> \UPS\UPSTrackAlert\UPSTrackAlert\TrackSubsServiceResponse processSubscriptionTypeForTrackingNumber($trans_id, $transaction_src, $type, $body)

API to create subscriptions by tracking numbers.

This endpoint takes a list of tracking numbers and creates a subscription for each. Clients must provide the tracking numbers in the correct format.  Upon success it should return: - List of valid tracking number for which subscription created. - List of invalid tracking number for which subscription not created.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: oauth2
$config = UPS\UPSTrackAlert\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\UPSTrackAlert\Request\UPSTrackAlertApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$trans_id = "trans_id_example"; // string | An identifier unique to the request.
$transaction_src = "transaction_src_example"; // string | Identifies the client/source application that is calling.
$type = "type_example"; // string | - 'Standard' - Represents a standard subscription type that provides near real time updates on tracking status.
$body = new \UPS\UPSTrackAlert\UPSTrackAlert\TrackSubsServiceRequest(); // \UPS\UPSTrackAlert\UPSTrackAlert\TrackSubsServiceRequest | 

try {
    $result = $apiInstance->processSubscriptionTypeForTrackingNumber($trans_id, $transaction_src, $type, $body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling UPSTrackAlertApi->processSubscriptionTypeForTrackingNumber: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **trans_id** | **string**| An identifier unique to the request. |
 **transaction_src** | **string**| Identifies the client/source application that is calling. |
 **type** | **string**| - &#x27;Standard&#x27; - Represents a standard subscription type that provides near real time updates on tracking status. |
 **body** | [**\UPS\UPSTrackAlert\UPSTrackAlert\TrackSubsServiceRequest**](../Model/TrackSubsServiceRequest.md)|  | [optional]

### Return type

[**\UPS\UPSTrackAlert\UPSTrackAlert\TrackSubsServiceResponse**](../Model/TrackSubsServiceResponse.md)

### Authorization

[oauth2](../../README.md#oauth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

