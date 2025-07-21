# GeocodeCandidateAddressKeyFormat

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**consignee_name** | **string** | Name. Not relevant for candidate list. | [optional] 
**address_line** | **string** | Address Line Information. The address level or Intersection information must be returned if provided in the request. The AddressLine will be a combination of up to 3 separate address lines, each separated by a new line character. | 
**political_division3** | **string** | Subdivision within a City. e.g., a Barrio. | [optional] 
**political_division2** | **string** | City. | 
**political_division1** | **string** | State/Province. | 
**postcode_primary_low** | **string** | Postal Code. | 
**postcode_extended_low** | **string** | 4 Digit postal code extension. Valid for US only. | [optional] 
**country_code** | **string** | A country or territory code. Valid values for candidates to be returned are: US-United States (meaning US 50) | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

