# AccessPointSearchExcludeFromResult

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**business_classification_code** | **string[]** | This contains the business classification code to exclude from UPS Access Point Search by address or geocode. Multiple codes can are possible in separate elements. Please refer to Appendix D for detailed business codes. | [optional] 
**business_name** | **string** | This contains the business name to exclude from UPS Access Point Search by address or geocode. Partial names are accepted. | [optional] 
**radius** | **string** | Public Access points within Radius (in specified Unit of Measure) of any included private access points will be excluded from the results. Valid only if at least one IncludeCriteria/MerchantAccountNumber is provided. | [optional] 
**postal_code_list** | [**\UPS\Locator\Locator\ExcludeFromResultPostalCodeList**](ExcludeFromResultPostalCodeList.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

