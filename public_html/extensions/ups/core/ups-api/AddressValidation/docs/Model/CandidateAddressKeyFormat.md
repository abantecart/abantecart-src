# CandidateAddressKeyFormat

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**consignee_name** | **string** | Name of business, company or person. Not returned if user selects the RegionalRequestIndicator. | [optional] 
**attention_name** | **string** | Name of building. Not returned if user selects the RegionalRequestIndicator. | [optional] 
**address_line** | **string[]** | Address line (street number, street name and street type, and political division 1, political division 2 and postal code) used for street level information. Additional secondary information (apartment, suite, floor, etc.) Applicable to US and PR only.  Not returned if user selects the RegionalRequestIndicator. | [optional] 
**region** | **string** | Single entry containing in this order  Political Division 2, Political Division 1 and Post Code Primary Low and/or PostcodeExtendedLow. | [optional] 
**political_division2** | **string** | City or Town name. | [optional] 
**political_division1** | **string** | State/Province.  Returned if the location is within a State/Province/Territory. For International: returned if user enters valid Country or Territory Code, and City/postal code and it has a match.  For Domestic addresses, the value must be a valid 2-character value (per US Mail standards).  For International the full State or Province name will be returned. | [optional] 
**postcode_primary_low** | **string** | Low-end Postal Code. Returned for countries or territories with Postal Codes. May be alphanumeric. | [optional] 
**postcode_extended_low** | **string** | Low-end extended postal code in a range. Example in quotes: Postal Code 30076-&#x27;1234&#x27;.  Only returned in candidate list. May be alphanumeric | [optional] 
**urbanization** | **string** | Puerto Rico Political Division 3. Only Valid for Puerto Rico. | [optional] 
**country_code** | **string** | A country or territory code. Required to be returned. | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

