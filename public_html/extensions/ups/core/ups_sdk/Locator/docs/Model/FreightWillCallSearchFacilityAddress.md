# FreightWillCallSearchFacilityAddress

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**slic** | **string** | Facility SLIC. Required for Freight Will call search if FreightWillCallRequestType is 2. | [optional] 
**address_line** | **string[]** | Address line | [optional] 
**city** | **string** | City. Required for Freight Will call search if FreightWillCallRequestType is 3. | [optional] 
**postal_code_primary_low** | **string** | Postal code. Required for Freight Will call search if FreightWillCallRequestType is 1. | [optional] 
**postal_code_extended_low** | **string** | 4 Digit postal code extension. Valid for US only. | [optional] 
**state** | **string** | State. Required if FrieghtWillCallRequestType is 3 if State is available. | [optional] 
**country_code** | **string** | Country or territory code. | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

