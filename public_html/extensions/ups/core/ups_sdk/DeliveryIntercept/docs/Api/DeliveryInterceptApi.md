# UPS\DeliveryIntercept\DeliveryInterceptApi

All URIs are relative to *https://wwwcie.ups.com/api/deliveryintercept/{version}*

Method | HTTP request | Description
------------- | ------------- | -------------
[**cancelExistingRequest**](DeliveryInterceptApi.md#cancelexistingrequest) | **POST** /cancel/{trackingNumber} | cancel an existing Delivery Intercept request
[**getCharges**](DeliveryInterceptApi.md#getcharges) | **POST** /charges/{trackingNumber} | charges by inquiry type
[**submitDeliverToAnotherAddressRequest**](DeliveryInterceptApi.md#submitdelivertoanotheraddressrequest) | **POST** /redirect/address/{trackingNumber} | Submission to request a new delivery address.
[**submitRescheduleDelivery**](DeliveryInterceptApi.md#submitrescheduledelivery) | **POST** /reschedule/{trackingNumber} | change to a new delivery date in the future.
[**submitReturnToSender**](DeliveryInterceptApi.md#submitreturntosender) | **POST** /return/{trackingNumber} | redirect the package back to the sender
[**submitWillCall**](DeliveryInterceptApi.md#submitwillcall) | **POST** /willcall/{trackingNumber} | redirect a package to a Customer center for pickup.

# **cancelExistingRequest**
> \UPS\DeliveryIntercept\DeliveryIntercept\MyChoiceCommonResponse cancelExistingRequest($trans_id, $transaction_src, $accept, $content_type, $tracking_number, $body, $loc)

cancel an existing Delivery Intercept request

Cancel a previously applied intercept on the package.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: OAuth2
$config = UPS\DeliveryIntercept\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\DeliveryIntercept\Request\DeliveryInterceptApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$trans_id = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | A unique value that will be used to identify the transaction for logging and troubleshooting purposes.
$transaction_src = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | Identifies the client/source application that is calling the API.
$accept = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | The Accept request HTTP header indicates which content types, expressed as MIME types, the client is able to understand.
$content_type = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | describes the content type to expect
$tracking_number = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | The number being tracked.  Each method defines if required.
$body = new \UPS\DeliveryIntercept\DeliveryIntercept\MyChoiceCommonRequest(); // \UPS\DeliveryIntercept\DeliveryIntercept\MyChoiceCommonRequest | 
$loc = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | The locale of the client application to ensure that translations on the response are in the proper language.

try {
    $result = $apiInstance->cancelExistingRequest($trans_id, $transaction_src, $accept, $content_type, $tracking_number, $body, $loc);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DeliveryInterceptApi->cancelExistingRequest: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **trans_id** | [****](../Model/.md)| A unique value that will be used to identify the transaction for logging and troubleshooting purposes. |
 **transaction_src** | [****](../Model/.md)| Identifies the client/source application that is calling the API. |
 **accept** | [****](../Model/.md)| The Accept request HTTP header indicates which content types, expressed as MIME types, the client is able to understand. |
 **content_type** | [****](../Model/.md)| describes the content type to expect |
 **tracking_number** | [****](../Model/.md)| The number being tracked.  Each method defines if required. |
 **body** | [**\UPS\DeliveryIntercept\DeliveryIntercept\MyChoiceCommonRequest**](../Model/MyChoiceCommonRequest.md)|  | [optional]
 **loc** | [****](../Model/.md)| The locale of the client application to ensure that translations on the response are in the proper language. | [optional]

### Return type

[**\UPS\DeliveryIntercept\DeliveryIntercept\MyChoiceCommonResponse**](../Model/MyChoiceCommonResponse.md)

### Authorization

[OAuth2](../../README.md#OAuth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getCharges**
> \UPS\DeliveryIntercept\DeliveryIntercept\MyChoiceCommonResponse getCharges($trans_id, $transaction_src, $accept, $content_type, $tracking_number, $body, $loc)

charges by inquiry type

Obtains intercept charges specified by the inquiry type.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: OAuth2
$config = UPS\DeliveryIntercept\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\DeliveryIntercept\Request\DeliveryInterceptApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$trans_id = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | A unique value that will be used to identify the transaction for logging and troubleshooting purposes.
$transaction_src = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | Identifies the client/source application that is calling the API.
$accept = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | The Accept request HTTP header indicates which content types, expressed as MIME types, the client is able to understand.
$content_type = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | describes the content type to expect
$tracking_number = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | The number being tracked.  Each method defines if required.
$body = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | 
$loc = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | The locale of the client application to ensure that translations on the response are in the proper language.

try {
    $result = $apiInstance->getCharges($trans_id, $transaction_src, $accept, $content_type, $tracking_number, $body, $loc);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DeliveryInterceptApi->getCharges: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **trans_id** | [****](../Model/.md)| A unique value that will be used to identify the transaction for logging and troubleshooting purposes. |
 **transaction_src** | [****](../Model/.md)| Identifies the client/source application that is calling the API. |
 **accept** | [****](../Model/.md)| The Accept request HTTP header indicates which content types, expressed as MIME types, the client is able to understand. |
 **content_type** | [****](../Model/.md)| describes the content type to expect |
 **tracking_number** | [****](../Model/.md)| The number being tracked.  Each method defines if required. |
 **body** | [****](../Model/.md)|  | [optional]
 **loc** | [****](../Model/.md)| The locale of the client application to ensure that translations on the response are in the proper language. | [optional]

### Return type

[**\UPS\DeliveryIntercept\DeliveryIntercept\MyChoiceCommonResponse**](../Model/MyChoiceCommonResponse.md)

### Authorization

[OAuth2](../../README.md#OAuth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **submitDeliverToAnotherAddressRequest**
> \UPS\DeliveryIntercept\DeliveryIntercept\MyChoiceDeliverToAnotherAddressResponse submitDeliverToAnotherAddressRequest($trans_id, $transaction_src, $accept, $content_type, $tracking_number, $body, $loc)

Submission to request a new delivery address.

Deliver to a different address than on the label originally created for the shipment.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: OAuth2
$config = UPS\DeliveryIntercept\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\DeliveryIntercept\Request\DeliveryInterceptApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$trans_id = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | A unique value that will be used to identify the transaction for logging and troubleshooting purposes.
$transaction_src = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | Identifies the client/source application that is calling the API.
$accept = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | The Accept request HTTP header indicates which content types, expressed as MIME types, the client is able to understand.
$content_type = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | describes the content type to expect
$tracking_number = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | The number being tracked.  Each method defines if required.
$body = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | 
$loc = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | The locale of the client application to ensure that translations on the response are in the proper language.

try {
    $result = $apiInstance->submitDeliverToAnotherAddressRequest($trans_id, $transaction_src, $accept, $content_type, $tracking_number, $body, $loc);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DeliveryInterceptApi->submitDeliverToAnotherAddressRequest: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **trans_id** | [****](../Model/.md)| A unique value that will be used to identify the transaction for logging and troubleshooting purposes. |
 **transaction_src** | [****](../Model/.md)| Identifies the client/source application that is calling the API. |
 **accept** | [****](../Model/.md)| The Accept request HTTP header indicates which content types, expressed as MIME types, the client is able to understand. |
 **content_type** | [****](../Model/.md)| describes the content type to expect |
 **tracking_number** | [****](../Model/.md)| The number being tracked.  Each method defines if required. |
 **body** | [****](../Model/.md)|  | [optional]
 **loc** | [****](../Model/.md)| The locale of the client application to ensure that translations on the response are in the proper language. | [optional]

### Return type

[**\UPS\DeliveryIntercept\DeliveryIntercept\MyChoiceDeliverToAnotherAddressResponse**](../Model/MyChoiceDeliverToAnotherAddressResponse.md)

### Authorization

[OAuth2](../../README.md#OAuth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **submitRescheduleDelivery**
> \UPS\DeliveryIntercept\DeliveryIntercept\MyChoiceRescheduleDeliveryResponse submitRescheduleDelivery($trans_id, $transaction_src, $accept, $content_type, $tracking_number, $body, $loc)

change to a new delivery date in the future.

Have the package delivered to the original address but on a different day in the future.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: OAuth2
$config = UPS\DeliveryIntercept\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\DeliveryIntercept\Request\DeliveryInterceptApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$trans_id = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | A unique value that will be used to identify the transaction for logging and troubleshooting purposes.
$transaction_src = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | Identifies the client/source application that is calling the API.
$accept = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | The Accept request HTTP header indicates which content types, expressed as MIME types, the client is able to understand.
$content_type = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | describes the content type to expect
$tracking_number = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | The number being tracked.  Each method defines if required.
$body = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | 
$loc = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | The locale of the client application to ensure that translations on the response are in the proper language.

try {
    $result = $apiInstance->submitRescheduleDelivery($trans_id, $transaction_src, $accept, $content_type, $tracking_number, $body, $loc);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DeliveryInterceptApi->submitRescheduleDelivery: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **trans_id** | [****](../Model/.md)| A unique value that will be used to identify the transaction for logging and troubleshooting purposes. |
 **transaction_src** | [****](../Model/.md)| Identifies the client/source application that is calling the API. |
 **accept** | [****](../Model/.md)| The Accept request HTTP header indicates which content types, expressed as MIME types, the client is able to understand. |
 **content_type** | [****](../Model/.md)| describes the content type to expect |
 **tracking_number** | [****](../Model/.md)| The number being tracked.  Each method defines if required. |
 **body** | [****](../Model/.md)|  | [optional]
 **loc** | [****](../Model/.md)| The locale of the client application to ensure that translations on the response are in the proper language. | [optional]

### Return type

[**\UPS\DeliveryIntercept\DeliveryIntercept\MyChoiceRescheduleDeliveryResponse**](../Model/MyChoiceRescheduleDeliveryResponse.md)

### Authorization

[OAuth2](../../README.md#OAuth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **submitReturnToSender**
> \UPS\DeliveryIntercept\DeliveryIntercept\MyChoiceReturnToSenderResponse submitReturnToSender($trans_id, $transaction_src, $accept, $content_type, $tracking_number, $body, $loc)

redirect the package back to the sender

Return the package to the original shipper prior to delivery.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: OAuth2
$config = UPS\DeliveryIntercept\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\DeliveryIntercept\Request\DeliveryInterceptApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$trans_id = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | A unique value that will be used to identify the transaction for logging and troubleshooting purposes.
$transaction_src = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | Identifies the client/source application that is calling the API.
$accept = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | The Accept request HTTP header indicates which content types, expressed as MIME types, the client is able to understand.
$content_type = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | describes the content type to expect
$tracking_number = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | The number being tracked.  Each method defines if required.
$body = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | 
$loc = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | The locale of the client application to ensure that translations on the response are in the proper language.

try {
    $result = $apiInstance->submitReturnToSender($trans_id, $transaction_src, $accept, $content_type, $tracking_number, $body, $loc);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DeliveryInterceptApi->submitReturnToSender: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **trans_id** | [****](../Model/.md)| A unique value that will be used to identify the transaction for logging and troubleshooting purposes. |
 **transaction_src** | [****](../Model/.md)| Identifies the client/source application that is calling the API. |
 **accept** | [****](../Model/.md)| The Accept request HTTP header indicates which content types, expressed as MIME types, the client is able to understand. |
 **content_type** | [****](../Model/.md)| describes the content type to expect |
 **tracking_number** | [****](../Model/.md)| The number being tracked.  Each method defines if required. |
 **body** | [****](../Model/.md)|  | [optional]
 **loc** | [****](../Model/.md)| The locale of the client application to ensure that translations on the response are in the proper language. | [optional]

### Return type

[**\UPS\DeliveryIntercept\DeliveryIntercept\MyChoiceReturnToSenderResponse**](../Model/MyChoiceReturnToSenderResponse.md)

### Authorization

[OAuth2](../../README.md#OAuth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **submitWillCall**
> \UPS\DeliveryIntercept\DeliveryIntercept\MyChoiceWillCallResponse submitWillCall($trans_id, $transaction_src, $accept, $content_type, $tracking_number, $body, $loc)

redirect a package to a Customer center for pickup.

Request your package to be held at or returned to a UPS facility where the receiver may claim the same-day.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: OAuth2
$config = UPS\DeliveryIntercept\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\DeliveryIntercept\Request\DeliveryInterceptApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$trans_id = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | A unique value that will be used to identify the transaction for logging and troubleshooting purposes.
$transaction_src = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | Identifies the client/source application that is calling the API.
$accept = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | The Accept request HTTP header indicates which content types, expressed as MIME types, the client is able to understand.
$content_type = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | describes the content type to expect
$tracking_number = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | The number being tracked.  Each method defines if required.
$body = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | 
$loc = new \UPS\DeliveryIntercept\DeliveryIntercept\null(); //  | The locale of the client application to ensure that translations on the response are in the proper language.

try {
    $result = $apiInstance->submitWillCall($trans_id, $transaction_src, $accept, $content_type, $tracking_number, $body, $loc);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DeliveryInterceptApi->submitWillCall: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **trans_id** | [****](../Model/.md)| A unique value that will be used to identify the transaction for logging and troubleshooting purposes. |
 **transaction_src** | [****](../Model/.md)| Identifies the client/source application that is calling the API. |
 **accept** | [****](../Model/.md)| The Accept request HTTP header indicates which content types, expressed as MIME types, the client is able to understand. |
 **content_type** | [****](../Model/.md)| describes the content type to expect |
 **tracking_number** | [****](../Model/.md)| The number being tracked.  Each method defines if required. |
 **body** | [****](../Model/.md)|  | [optional]
 **loc** | [****](../Model/.md)| The locale of the client application to ensure that translations on the response are in the proper language. | [optional]

### Return type

[**\UPS\DeliveryIntercept\DeliveryIntercept\MyChoiceWillCallResponse**](../Model/MyChoiceWillCallResponse.md)

### Authorization

[OAuth2](../../README.md#OAuth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

