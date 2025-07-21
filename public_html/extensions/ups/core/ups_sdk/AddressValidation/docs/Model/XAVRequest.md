# XAVRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**request** | [**\UPS\AddressValidation\AddressValidation\XAVRequestRequest**](XAVRequestRequest.md) |  | 
**regional_request_indicator** | **string** | If this indicator is present then either the region element or any combination of Political Division 1, Political Division 2, PostcodePrimaryLow and the PostcodeExtendedLow fields will be recognized for validation in addition to the urbanization element.  If this tag is present, US and PR street level address validation will not occur. The default is to provide street level address validation.  Not valid with the address classification request option. | [optional] 
**maximum_candidate_list_size** | **string** | The maximum number of Candidates to return for this request.  - Valid values: 0 - 50 - Default: 15 | [optional] 
**address_key_format** | [**\UPS\AddressValidation\AddressValidation\XAVRequestAddressKeyFormat**](XAVRequestAddressKeyFormat.md) |  | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

