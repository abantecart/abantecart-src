# LocationSearchCriteriaAccessPointSearch

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**public_access_point_id** | **string** | The Public Access Point ID to use for UPS Access Point Search. Once this parameter is present , address or geocode search is ignored. It cannot be combined with AccountNumber search parameter. | [optional] 
**access_point_status** | **string** | Status of UPS Access Point. Valid values are:  01-Active-available 07-Active-unavailable. | [optional] 
**account_number** | **string** | The account number to use for UPS Access Point Search in the country or territory. Used to locate a private network for the account. Once this parameter is present any access point address or geocode search is ignored. It cannot be combined with PublicAccessPointID search parameter. | [optional] 
**include_criteria** | [**\UPS\Locator\Locator\AccessPointSearchIncludeCriteria**](AccessPointSearchIncludeCriteria.md) |  | [optional] 
**exclude_from_result** | [**\UPS\Locator\Locator\AccessPointSearchExcludeFromResult**](AccessPointSearchExcludeFromResult.md) |  | [optional] 
**exact_match_indicator** | **string** | Presence of this tag represents that \&quot;AccessPointSearchByAddress\&quot; service is requested. The value of this tag is ignored. | [optional] 
**exist_indicator** | **string** | Presence of this tag represents that \&quot;AccessPointAvailability\&quot; service is requested. The value of this tag is ignored. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

