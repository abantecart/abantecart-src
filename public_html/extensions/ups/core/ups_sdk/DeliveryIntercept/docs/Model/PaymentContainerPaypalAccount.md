# PaymentContainerPaypalAccount

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**billing_address** | [**\UPS\DeliveryIntercept\DeliveryIntercept\AddressInformation**](AddressInformation.md) |  | [optional] 
**platform_type** | [****](.md) | Request required for non-authorized payment. Platform type associated with the source application. | [optional] 
**currency_code** | [****](.md) | Request required for non-authorized payment. Default value is USD. The currency code associated with the charges. If not present, the currency will be retrieved from the charges response when applicable. | [optional] 
**iobb** | [****](.md) | Used on request only for non-authorized payment. Used for fraud checking/scoring   | S.NO. | DESCRIPTION                                 | | :--:  | :--                                         | | 1    | iobb value is labeled “EMPTY”               | | 2.   | tmid is populated with the Threat Metrix ID | | [optional] 
**default_account** | [****](.md) | Indicator if this is the default payment in the user&#x27;s profile. TRUE - account is the default payment . FALSE - account is not the default payment. | [optional] 
**displayable_number** | [****](.md) | Displayable account number. | [optional] 
**amount** | [****](.md) | Request required for non-authorized payment. The total transaction amount to authorize based on the charges. If not present, the amount will be retrieved from the charges response when applicable. | [optional] 
**account_name** | [****](.md) | The account name associated with this payment. | [optional] 
**guid_code** | [****](.md) | Request required for pre-authorized payment. The guid generated after the transaction payment authorization, acts as the authorization code. | [optional] 
**pay_pal_billing_agreement_id** | [****](.md) | Request required for non-authorized payment. Tokenized billing agreement number. | [optional] 
**pay_pal_payer_id** | [****](.md) | Request required for non-authorized payment. Tokenized payer account identifier. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

