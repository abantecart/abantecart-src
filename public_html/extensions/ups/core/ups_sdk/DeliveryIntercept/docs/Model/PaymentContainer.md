# PaymentContainer

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**payment_type** | [****](.md) | Conditional on the charges. Required if the charges are greater than zero.  | CODE  | DESCRIPTION       | | :--:  | :--               | | C     | Credit Card       | | A     | Consignee Account | | P     | PayPal            | | G     | GooglePay         | | I     | ApplePay          | | [optional] 
**payment_authorized** | [****](.md) | Indicates if the credit card / PayPal payment transaction is pre-authorized. TRUE - the payment transaction is pre-authorized. | [optional] 
**credit_card_info** | [**\UPS\DeliveryIntercept\DeliveryIntercept\PaymentContainerCreditCardInfo**](PaymentContainerCreditCardInfo.md) |  | [optional] 
**account_info** | [**\UPS\DeliveryIntercept\DeliveryIntercept\PaymentContainerAccountInfo**](PaymentContainerAccountInfo.md) |  | [optional] 
**paypal_account** | [**\UPS\DeliveryIntercept\DeliveryIntercept\PaymentContainerPaypalAccount**](PaymentContainerPaypalAccount.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

