# AvailableLocationAttributesOptionCode

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**code** | **string** | The valid list of codes and description for Retail Locations or Additional Services or Pro-gram Types that are currently available in the database. This can be obtained by a separate type of request (Request Option 8, 16, 24, 32, 40, 48 and 56). | 
**description** | **string** | Description is only applicable for Program types and Additional Services. It is not provided with Location detail. It is only provided when the request is for All available additional ser-vices or all available Program types. Text will be displayed in the locale requested. | 
**name** | **string** | Name will indicate the name of Location/Retail Location or Additional Services or Program Types depending on the option code. Text will be displayed in the locale requested. | [optional] 
**category** | **string** | N/A | [optional] 
**transportation_pick_up_schedule** | [**\UPS\Locator\Locator\AvailableLocationAttributesOptionCodeTransportationPickUpSchedule**](AvailableLocationAttributesOptionCodeTransportationPickUpSchedule.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

