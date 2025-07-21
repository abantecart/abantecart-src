# LocationAttributeOptionCode

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**category** | **string** | Only applicabe for OptionType &#x3D; 03 (Additional Services). Valid values: - 06 - Non transportation - 07 - Transportation | [optional] 
**code** | **string** | These codes vary by country or territory. It is strongly recommended that clients contact UPS to retrieve the primary search indicator and the valid Location Types and Service Level Options for each country. Refer to Location Search Option Codes in the Appendix for additional information. | 
**description** | **string** | Description is only applicable for Location and Retail Location. The description for Program types and additional service is not provided with Location detail.  It is only provided when the request is for 8, 24, 40, 56-All available additional services or 16, 24, 48, 56-all available Program types. | 
**name** | **string** | Name will indicate the name of any Additional Services/ Program Types depending on the option code. Text will be displayed in the locale selected. | [optional] 
**transportation_pick_up_schedule** | [**\UPS\Locator\Locator\OptionCodeTransportationPickUpSchedule**](OptionCodeTransportationPickUpSchedule.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

