# ShipperAddress

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**address_line1** | **string** | Address Line 1 of the Shipper. | [optional] 
**address_line2** | **string** | Address Line 2 of the Shipper. Usually room/floor information. | [optional] 
**address_line3** | **string** | Address Line 3 of the shipper. Usually department information. | [optional] 
**city** | **string** | Shipper&#x27;s City. | [optional] 
**state_province_code** | **string** | Shipper&#x27;s state or province code. Must be valid US state. If the Shipper&#x27;s country or territory is US or CA a two character code is required, otherwise the StateProvinceCode is optional. | [optional] 
**postal_code** | **string** | Shipper&#x27;s postal code. If the address is US then 5 or 9 digits are required. CA addresses must provide a 6 character postal code that has the format of A#A#A#, where A is a alphabetic character and # is numeric digit. Otherwise, 1 to 9 alphanumeric characters are allowed. | [optional] 
**country_code** | **string** | Shipper&#x27;s country or territory code.  Valid values: CA,MX, PR, US, AT, BE, DE, DK, ES, FI, FR, GB, IE, IT, NL, PT, SE, MC and VA | [optional] 
**residential_address_indicator** | **string** | If tag is present, then the address is residential address. Pickup location residential address indicator. The presence indicates residential address, the absence indicates a business address. | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

