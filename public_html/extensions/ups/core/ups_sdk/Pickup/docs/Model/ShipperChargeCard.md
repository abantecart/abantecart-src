# ShipperChargeCard

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**card_holder_name** | **string** | Charge card holder name. If the name is not provided, defaults to \&quot;No Name Provided\&quot;. | [optional] 
**card_type** | **string** | Charge card type. Valid values: - 01 &#x3D; American Express - 03 &#x3D; Discover - 04 &#x3D; Mastercard - 06 &#x3D; VISA  Discover card Pickup country US only. | 
**card_number** | **string** | Charge card number.  For Privileged clients, this element must be tokenized card number. | 
**expiration_date** | **string** | Credit card expiration date. Format: yyyyMM yyyy &#x3D; 4 digit year, valid value current year - 10 years. MM &#x3D; 2 digit month, valid values 01-12 | 
**security_code** | **string** | Three or four digits that can be found either on top of credit card number or on the back of credit card.  Number of digits varies for different type of credit card.  Valid values are 3 or 4 digits. Security code is required if credit card information is provided. | 
**card_address** | [**\UPS\Pickup\Pickup\ChargeCardCardAddress**](ChargeCardCardAddress.md) |  | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

