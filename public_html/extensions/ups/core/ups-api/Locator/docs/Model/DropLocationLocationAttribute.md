# DropLocationLocationAttribute

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**option_type** | [**\UPS\Locator\Locator\LocationAttributeOptionType**](LocationAttributeOptionType.md) |  | 
**option_code** | [**\UPS\Locator\Locator\LocationAttributeOptionCode[]**](LocationAttributeOptionCode.md) | Option code is a container that contains the information of a particular type of Location or retail location or additional service or program type that the drop location contains.  If the OptionType is Location or Retail Location Type there will be one code since each location has only one location type or retail location type.  If the Option type is additional services or program types there can be one or more option codes.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

