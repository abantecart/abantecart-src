# OriginAddressAddressKeyFormat

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**consignee_name** | **string** | Name. Not relevant for this tool | [optional] 
**address_line** | **string** | Address Line Information. The user may submit street level address information or provide Intersection information. | 
**address_line2** | **string** | Additional Address Line Information. | [optional] 
**address_line3** | **string** | Additional Address Line Information. | [optional] 
**political_division3** | **string** | Barrio or other sub-division of City | [optional] 
**political_division2** | **string** | City or Town. | 
**political_division1** | **string** | State or province | 
**postcode_primary_low** | **string** | Main postal code. Required if the user does not submit the City, State/Province address combination. | 
**postcode_extended_low** | **string** | 4 Digit postal code extension. Valid for US only. | [optional] 
**country_code** | **string** | Two-character country or territory abbreviation | 
**single_line_address** | **string** | Single line search information. Can contain values of origin address in a single line. Will override other origin address information.  Conditionally Required for Non-Postal Code Countries. Applicable Country Ireland (IE)  SingleLineAddress used for the lookup  SingleLineAddress (Format - CSV) (\\\&quot;Values:\\\&quot; + postalCode + city + state + address + landmark + phoneNumber) | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

