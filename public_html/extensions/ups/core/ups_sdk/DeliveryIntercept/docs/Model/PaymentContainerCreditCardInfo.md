# PaymentContainerCreditCardInfo

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**billing_address** | [**\UPS\DeliveryIntercept\DeliveryIntercept\AddressInformation**](AddressInformation.md) |  | [optional] 
**platform_type** | [****](.md) | Platform type associated with the source application.   | VALUE  | DESCRIPTION                  | | :--    | :--                          | | AU     | M.UPS.COM                    | | GG    | UPSXMLONLINETOOLS            | | 72     | WEB-INTERNETBASEDAPPLICATION | | XK     | UPSMOBILEFORANDROID          | | XL     | UPSMOBILEFORIOS              |  | XM     | UPSSOCIALFACEBOOKCLIENT&#x3D;XM   | | [optional] 
**currency_code** | [****](.md) | Default value is “USD”. The currency code associated with the charges. | [optional] 
**iobb** | [****](.md) | Used on request only for non-authorized payment. Used for fraud checking/scoring.  | S.NO. | DESCRIPTION                                 | | :--:  | :--                                         | | 1     | iobb value is labeled “EMPTY”               | | 2     | tmid is populated with the Threat Metrix ID | | [optional] 
**default_account** | [****](.md) | Default value is FALSE. Indicator if this is the default payment in the user&#x27;s profile. | [optional] 
**displayable_number** | [****](.md) | Displayable account number. | [optional] 
**amount** | [****](.md) | The total transaction amount to authorize based on the charges. | [optional] 
**account_name** | [****](.md) | The account name associated with this payment. | [optional] 
**guid_code** | [****](.md) | The guid generated after the transaction payment authorization, acts as the authorization code. | [optional] 
**tokenized_account_number** | [****](.md) | The encrypted card account number. Request required for non-authorized cards | [optional] 
**card_type** | [****](.md) | The card type is required for non-authorized cards and new cards. | [optional] 
**expire_year** | [****](.md) | The card expiration date year. Format- YYYY | [optional] 
**expire_month** | [****](.md) | The card expiration date month. Format- MM | [optional] 
**saved_card** | [****](.md) | Default value is FALSE. Indicates if this is a new card and should be saved to the user&#x27;s profile. | 
**verification_code** | [****](.md) | The card specific certification / verification code. | [optional] 
**token_obj** | [**\UPS\DeliveryIntercept\DeliveryIntercept\PaymentContainerCreditCardInfoTokenObj**](PaymentContainerCreditCardInfoTokenObj.md) |  | [optional] 
**security_code_validated** | [****](.md) | Default value is TRUE. Indicates if the cvv verification code validation should be performed. | [optional] 
**ups_account_number** | [****](.md) | Account number associated with this card. | [optional] 
**payment_token_flag** | [****](.md) | Default value is FALSE. Payment token flag indicator. | [optional] 
**card_holder_name** | [****](.md) | Card account name used when saving a new card to the user&#x27;s profile. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

