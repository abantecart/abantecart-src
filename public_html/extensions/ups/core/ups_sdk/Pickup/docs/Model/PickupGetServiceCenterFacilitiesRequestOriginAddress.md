# PickupGetServiceCenterFacilitiesRequestOriginAddress

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**street_address** | **string** | Indicates the address of the shipper to allow for the nearest Drop off facility Search.  Conditionally required if proximitySearchIndicator is present. | [optional] 
**city** | **string** | Indicates the address of the shipper to allow for the nearest Drop off facility Search  Conditionally required if proximitySearchIndicator is present. | [optional] 
**state_province** | **string** | Indicates the address of the shipper to allow for the nearest Drop off facility Search.  Conditionally required if proximitySearchIndicator is present and if country or territory is US/CA/IE/HK. | [optional] 
**postal_code** | **string** | Indicates the address of the shipper to allow for the nearest Drop off facility Search  Conditionally required if proximitySearchIndicator is present and if country or territory has postal code.It does not apply to non-postal countries such as IE and HK. | [optional] 
**country_code** | **string** | Indicates the address of the shipper to allow for the nearest Drop off facility Search | 
**origin_search_criteria** | [**\UPS\Pickup\Pickup\OriginAddressOriginSearchCriteria**](OriginAddressOriginSearchCriteria.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

