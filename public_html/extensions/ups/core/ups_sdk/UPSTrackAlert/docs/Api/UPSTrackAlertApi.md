# UPS\UPSTrackAlert\UPSTrackAlertApi

All URIs are relative to *https://wwwcie.ups.com/api/track/{version}*

Method | HTTP request | Description
------------- | ------------- | -------------
[**processSubscriptionTypeForTrackingNumber**](UPSTrackAlertApi.md#processsubscriptiontypefortrackingnumber) | **POST** /subscription/standard/package | API to create subscriptions by tracking numbers.

# **processSubscriptionTypeForTrackingNumber**
> \UPS\UPSTrackAlert\UPSTrackAlert\TrackSubsServiceResponse processSubscriptionTypeForTrackingNumber($trans_id, $transaction_src, $version, $body)

API to create subscriptions by tracking numbers.

This endpoint takes a list of tracking numbers and creates a subscription for each. Clients must provide the tracking numbers in the correct format.  Upon success it should return: - List of valid tracking number for which subscription created. - List of invalid tracking number for which subscription not created.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: OAuth2
$config = UPS\UPSTrackAlert\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\UPSTrackAlert\Request\UPSTrackAlertApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$trans_id = new \UPS\UPSTrackAlert\UPSTrackAlert\null(); //  | An identifier unique to the request.
$transaction_src = new \UPS\UPSTrackAlert\UPSTrackAlert\null(); //  | Identifies the client/source application that is calling.
$version = new \UPS\UPSTrackAlert\UPSTrackAlert\null(); //  | version
$body = new \UPS\UPSTrackAlert\UPSTrackAlert\TrackSubsServiceRequest(); // \UPS\UPSTrackAlert\UPSTrackAlert\TrackSubsServiceRequest | 

try {
    $result = $apiInstance->processSubscriptionTypeForTrackingNumber($trans_id, $transaction_src, $version, $body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling UPSTrackAlertApi->processSubscriptionTypeForTrackingNumber: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **trans_id** | [****](../Model/.md)| An identifier unique to the request. |
 **transaction_src** | [****](../Model/.md)| Identifies the client/source application that is calling. |
 **version** | [****](../Model/.md)| version |
 **body** | [**\UPS\UPSTrackAlert\UPSTrackAlert\TrackSubsServiceRequest**](../Model/TrackSubsServiceRequest.md)|  | [optional]

### Return type

[**\UPS\UPSTrackAlert\UPSTrackAlert\TrackSubsServiceResponse**](../Model/TrackSubsServiceResponse.md)

### Authorization

[OAuth2](../../README.md#OAuth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

