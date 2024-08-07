# ShipToAddress

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**consignee_name** | **string** | Consignee&#x27;s name. | [optional] 
**address_line1** | **string** | Address Line 1 of the Consignee. | [optional] 
**address_line2** | **string** | Address Line 2 of the Consignee. | [optional] 
**address_line3** | **string** | Address Line 3 of the Consignee. | [optional] 
**city** | **string** | Consignee&#x27;s City. | [optional] 
**state_province_code** | **string** | Consignee&#x27;s state or province code. Must be valid US state. If the consignee&#x27;s country or territory  is US or CA a two character code is required. Otherwise, the StateProvinceCode is optional. | [optional] 
**postal_code** | **string** | Consignee&#x27;s postal code. If the address is US then 5 or 9 digits are required. CA addresses must provide a 6 character postal code that has the format of A#A#A#, where A is a alphabetic character and # is numeric digit. Otherwise, 1 to 9 alphanumeric characters are allowed. | [optional] 
**country_code** | **string** | Consignee&#x27;s country or territory  code.  Valid values: CA,MX, PR, US, AT, BE, DE, DK, ES, FI, FR, GB, IE, IT, NL, PT, SE, MC and VA | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

