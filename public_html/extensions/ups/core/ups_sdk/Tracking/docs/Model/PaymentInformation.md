# PaymentInformation

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**amount** | **string** | The payment amount. This value will contain the amount in dollars and cents, separated by a period (.) Example: &#x27;1025.50&#x27;.9 | [optional] 
**currency** | **string** | The payment currency code (see API codes for possible values). | [optional] 
**id** | **string** | The payment internal ID. This may be used in other systems to retrieve additional information on the payment. | [optional] 
**paid** | **bool** | The indication for whether the payment is paid or not. Valid values: &#x27;true&#x27; the payment is paid. &#x27;false&#x27; the payment is not paid. | [optional] 
**payment_method** | **string** | The applicable payment methods. | [optional] 
**type** | **string** | The payment type. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

