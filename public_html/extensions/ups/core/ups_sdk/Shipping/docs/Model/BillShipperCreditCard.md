# BillShipperCreditCard

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**type** | **string** | Valid values: - 01 &#x3D; American Express - 03 &#x3D; Discover - 04 &#x3D; MasterCard - 05 &#x3D; Optima - 06 &#x3D; VISA - 07 &#x3D; Bravo - 08 &#x3D; Diners Club - 13 &#x3D; Dankort - 14 &#x3D; Hipercard - 15 &#x3D; JCB - 17 &#x3D; Postepay - 18 &#x3D; UnionPay/ExpressPay - 19 &#x3D; Visa Electron - 20 &#x3D; VPAY - 21 &#x3D; Carte Bleue | 
**number** | **string** | Credit Card number. | 
**expiration_date** | **string** | Format is MMYYYY where MM is the 2 digit month and YYYY is the 4 digit year.  Valid month values are 01-12 and valid year values are Present Year - (Present Year + 10 years) | 
**security_code** | **string** | Three or four digits that can be found either on top of credit card number or on the back of credit card. Number of digits varies for different type of credit card.  Valid values are 3 or 4 digits. It is required to provide the security code if credit card information is provided and when the ShipFrom countries or territories are other than the below mentioned countries or territories. Argentina, Bahamas, Costa Rica, Dominican Republic, Guatemala, Panama, Puerto Rico and Russia. | 
**address** | [**\UPS\Shipping\Shipping\CreditCardAddress**](CreditCardAddress.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

