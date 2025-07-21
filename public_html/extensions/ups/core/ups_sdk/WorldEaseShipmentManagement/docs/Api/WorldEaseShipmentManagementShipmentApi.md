# UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagementShipmentApi

All URIs are relative to *https://wwwcie.ups.com/api/ship/{version}*

Method | HTTP request | Description
------------- | ------------- | -------------
[**deleteChildShipment**](WorldEaseShipmentManagementShipmentApi.md#deletechildshipment) | **DELETE** /child-shipment/{shipment-gccn}/{tracking-number} | Delete the child shipment
[**deleteMasterShipment**](WorldEaseShipmentManagementShipmentApi.md#deletemastershipment) | **DELETE** /master-shipment/{shipment-gccn} | Deletes the master shipment
[**saveCloseOutShipment**](WorldEaseShipmentManagementShipmentApi.md#savecloseoutshipment) | **POST** /master-shipment/closeout/{shipment-gccn} | Close Out Shipment

# **deleteChildShipment**
> \UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\DeleteResponse deleteChildShipment($shipment_gccn, $tracking_number, $trans_id, $transaction_src, $version)

Delete the child shipment

This is used to delete the child shipment record from the database

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: OAuth2
$config = UPS\WorldEaseShipmentManagement\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\WorldEaseShipmentManagement\Request\WorldEaseShipmentManagementShipmentApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$shipment_gccn = new \UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\null(); //  | The unique identifier of the shipment to identify the master shipment. It is also known as GCCN.
$tracking_number = new \UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\null(); //  | The identifier of the shipment to identify the child shipment. It is also known as 1Z Tracking Number.
$trans_id = new \UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\CustomTransactionId(); // \UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\CustomTransactionId | 
$transaction_src = new \UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\CustomTransactionSrc(); // \UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\CustomTransactionSrc | 
$version = new \UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\null(); //  | Indicates WorldEase Shipment Management API to display the new release features

try {
    $result = $apiInstance->deleteChildShipment($shipment_gccn, $tracking_number, $trans_id, $transaction_src, $version);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WorldEaseShipmentManagementShipmentApi->deleteChildShipment: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **shipment_gccn** | [****](../Model/.md)| The unique identifier of the shipment to identify the master shipment. It is also known as GCCN. |
 **tracking_number** | [****](../Model/.md)| The identifier of the shipment to identify the child shipment. It is also known as 1Z Tracking Number. |
 **trans_id** | [**\UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\CustomTransactionId**](../Model/.md)|  |
 **transaction_src** | [**\UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\CustomTransactionSrc**](../Model/.md)|  |
 **version** | [****](../Model/.md)| Indicates WorldEase Shipment Management API to display the new release features |

### Return type

[**\UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\DeleteResponse**](../Model/DeleteResponse.md)

### Authorization

[OAuth2](../../README.md#OAuth2)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteMasterShipment**
> \UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\DeleteResponse deleteMasterShipment($trans_id, $transaction_src, $shipment_gccn, $version, $body)

Deletes the master shipment

Deletes the specified master shipment from the system. Once deleted system cannot be recovered

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: OAuth2
$config = UPS\WorldEaseShipmentManagement\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\WorldEaseShipmentManagement\Request\WorldEaseShipmentManagementShipmentApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$trans_id = new \UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\CustomTransactionId(); // \UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\CustomTransactionId | 
$transaction_src = new \UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\CustomTransactionSrc(); // \UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\CustomTransactionSrc | 
$shipment_gccn = new \UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\null(); //  | The unique identifier of the shipment to delete the master shipment. It is also known as GCCN.
$version = new \UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\null(); //  | Indicates WorldEase Shipment Management API to display the new release features
$body = new \UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\PostRequest(); // \UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\PostRequest | 

try {
    $result = $apiInstance->deleteMasterShipment($trans_id, $transaction_src, $shipment_gccn, $version, $body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WorldEaseShipmentManagementShipmentApi->deleteMasterShipment: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **trans_id** | [**\UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\CustomTransactionId**](../Model/.md)|  |
 **transaction_src** | [**\UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\CustomTransactionSrc**](../Model/.md)|  |
 **shipment_gccn** | [****](../Model/.md)| The unique identifier of the shipment to delete the master shipment. It is also known as GCCN. |
 **version** | [****](../Model/.md)| Indicates WorldEase Shipment Management API to display the new release features |
 **body** | [**\UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\PostRequest**](../Model/PostRequest.md)|  | [optional]

### Return type

[**\UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\DeleteResponse**](../Model/DeleteResponse.md)

### Authorization

[OAuth2](../../README.md#OAuth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **saveCloseOutShipment**
> \UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\PostResponse saveCloseOutShipment($trans_id, $transaction_src, $shipment_gccn, $version, $body)

Close Out Shipment

Finalizes the shipment process by marking a package or set of packages as ready for dispatch, effectively ending the consolidation stage.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: OAuth2
$config = UPS\WorldEaseShipmentManagement\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\WorldEaseShipmentManagement\Request\WorldEaseShipmentManagementShipmentApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$trans_id = new \UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\CustomTransactionId(); // \UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\CustomTransactionId | 
$transaction_src = new \UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\CustomTransactionSrc(); // \UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\CustomTransactionSrc | 
$shipment_gccn = new \UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\null(); //  | The unique identifier of the shipment to close Out. It also known as GCCN.
$version = new \UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\null(); //  | Indicates WorldEase Shipment Management API to display the new release features
$body = new \UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\PostRequest(); // \UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\PostRequest | 

try {
    $result = $apiInstance->saveCloseOutShipment($trans_id, $transaction_src, $shipment_gccn, $version, $body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WorldEaseShipmentManagementShipmentApi->saveCloseOutShipment: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **trans_id** | [**\UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\CustomTransactionId**](../Model/.md)|  |
 **transaction_src** | [**\UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\CustomTransactionSrc**](../Model/.md)|  |
 **shipment_gccn** | [****](../Model/.md)| The unique identifier of the shipment to close Out. It also known as GCCN. |
 **version** | [****](../Model/.md)| Indicates WorldEase Shipment Management API to display the new release features |
 **body** | [**\UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\PostRequest**](../Model/PostRequest.md)|  | [optional]

### Return type

[**\UPS\WorldEaseShipmentManagement\WorldEaseShipmentManagement\PostResponse**](../Model/PostResponse.md)

### Authorization

[OAuth2](../../README.md#OAuth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

