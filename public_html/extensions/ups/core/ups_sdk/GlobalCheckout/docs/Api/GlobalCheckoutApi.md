# UPS\GlobalCheckout\GlobalCheckoutApi

All URIs are relative to *https://wwwcie.ups.com/api/brokerage/{version}*

Method | HTTP request | Description
------------- | ------------- | -------------
[**createGuaranteedQuote**](GlobalCheckoutApi.md#createguaranteedquote) | **POST** /content/glc/request-quote | returns a guaranteed landed cost quote

# **createGuaranteedQuote**
> \UPS\GlobalCheckout\GlobalCheckout\BSISV1QuoteResponse createGuaranteedQuote($trans_id, $transaction_src, $accept, $content_type, $body, $registration_id)

returns a guaranteed landed cost quote

This endpoint requests guaranteed quotes for landed cost duties and taxes.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: OAuth2
$config = UPS\GlobalCheckout\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\GlobalCheckout\Request\GlobalCheckoutApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$trans_id = new \UPS\GlobalCheckout\GlobalCheckout\null(); //  | An identifier unique to the request.
$transaction_src = new \UPS\GlobalCheckout\GlobalCheckout\null(); //  | Identifies the client/source application that is calling.
$accept = new \UPS\GlobalCheckout\GlobalCheckout\null(); //  | The Accept request HTTP header indicates which content types, expressed as MIME types, the client is able to understand.
$content_type = new \UPS\GlobalCheckout\GlobalCheckout\null(); //  | The Content-Type header provides the client with the actual content/media type of the returned content.
$body = new \UPS\GlobalCheckout\GlobalCheckout\BSISV1QuoteRequest(); // \UPS\GlobalCheckout\GlobalCheckout\BSISV1QuoteRequest | 
$registration_id = new \UPS\GlobalCheckout\GlobalCheckout\null(); //  | The Customer Registration Identifier used to validate the shipper account.  If not passed then it will be obtained with the OAuth token's UUID.

try {
    $result = $apiInstance->createGuaranteedQuote($trans_id, $transaction_src, $accept, $content_type, $body, $registration_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling GlobalCheckoutApi->createGuaranteedQuote: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **trans_id** | [****](../Model/.md)| An identifier unique to the request. |
 **transaction_src** | [****](../Model/.md)| Identifies the client/source application that is calling. |
 **accept** | [****](../Model/.md)| The Accept request HTTP header indicates which content types, expressed as MIME types, the client is able to understand. |
 **content_type** | [****](../Model/.md)| The Content-Type header provides the client with the actual content/media type of the returned content. |
 **body** | [**\UPS\GlobalCheckout\GlobalCheckout\BSISV1QuoteRequest**](../Model/BSISV1QuoteRequest.md)|  | [optional]
 **registration_id** | [****](../Model/.md)| The Customer Registration Identifier used to validate the shipper account.  If not passed then it will be obtained with the OAuth token&#x27;s UUID. | [optional]

### Return type

[**\UPS\GlobalCheckout\GlobalCheckout\BSISV1QuoteResponse**](../Model/BSISV1QuoteResponse.md)

### Authorization

[OAuth2](../../README.md#OAuth2)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

