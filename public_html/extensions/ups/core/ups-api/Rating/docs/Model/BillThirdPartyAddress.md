# BillThirdPartyAddress

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**address_line** | **string[]** | The origin street address including name and number (when applicable). | [optional] 
**city** | **string** | Origin city. | [optional] 
**state_province_code** | **string** | Origin state code. | [optional] 
**postal_code** | **string** | Origin postal code. The postal code must be the same as the UPS account pickup address postal code. Required for United States and Canadian UPS accounts and/or if the UPS account pickup address has a postal code. If the UPS account&#x27;s pickup country or territory is US or Puerto Rico, the postal code is 5 or 9 digits. The character &#x27;-&#x27; may be used to separate the first five digits and the last four digits. If the UPS account&#x27;s pickup country or territory is CA, the postal code is 6 alphanumeric characters whose format is A#A#A# where A is an uppercase letter and # is a digit. | [optional] 
**country_code** | **string** | Origin country or territory code. Refer to the Supported Country or Territory Tables located in the Appendix. | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

