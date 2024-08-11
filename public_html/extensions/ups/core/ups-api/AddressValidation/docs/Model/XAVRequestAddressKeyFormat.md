# XAVRequestAddressKeyFormat

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**consignee_name** | **string** | Name of business, company or person. Ignored if user selects the RegionalRequestIndicator. | [optional] 
**attention_name** | **string** | Name of the building. Ignored if user selects the RegionalRequestIndicator. | [optional] 
**address_line** | **string[]** | Address line (street number, street name and street type) used for street level information. Additional secondary information (apartment, suite, floor, etc.). Applicable to US and PR only. Ignored if user selects the RegionalRequestIndicator. | [optional] 
**region** | **string** | If this node is present the following tags will be ignored:  - Political Division 2 - Political Division 1 - PostcodePrimaryLow - PostcodeExtendedLow  Valid only for US or PR origins only.  Using this tag for non US/PR origins may cause address format errors. | [optional] 
**political_division2** | **string** | City or Town name. | [optional] 
**political_division1** | **string** | State or Province/Territory name. | [optional] 
**postcode_primary_low** | **string** | Postal Code. | [optional] 
**postcode_extended_low** | **string** | 4 digit Postal Code extension. For US use only. | [optional] 
**urbanization** | **string** | Puerto Rico Political Division 3. Only Valid for Puerto Rico. | [optional] 
**country_code** | **string** | Country or Territory Code. For a list of valid values, refer to the Address Validation API Supported Countries or Territories table in the Appendix. | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

