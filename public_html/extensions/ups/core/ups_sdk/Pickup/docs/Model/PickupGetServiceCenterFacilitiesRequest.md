# PickupGetServiceCenterFacilitiesRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**request** | [**\UPS\Pickup\Pickup\PickupGetServiceCenterFacilitiesRequestRequest**](PickupGetServiceCenterFacilitiesRequestRequest.md) |  | 
**pickup_piece** | [**\UPS\Pickup\Pickup\PickupGetServiceCenterFacilitiesRequestPickupPiece[]**](PickupGetServiceCenterFacilitiesRequestPickupPiece.md) |  | 
**origin_address** | [**\UPS\Pickup\Pickup\PickupGetServiceCenterFacilitiesRequestOriginAddress**](PickupGetServiceCenterFacilitiesRequestOriginAddress.md) |  | [optional] 
**destination_address** | [**\UPS\Pickup\Pickup\PickupGetServiceCenterFacilitiesRequestDestinationAddress**](PickupGetServiceCenterFacilitiesRequestDestinationAddress.md) |  | [optional] 
**locale** | **string** | Origin Country or Territory Locale.  Locale should be Origin Country. Example: en_US.  The Last 50 instruction will be send based on this locale. Locale is required if PoximityIndicator is present for Drop Off facilities. | 
**proximity_search_indicator** | **string** | Proximity Indicator. Indicates the user requested the proximity search for UPS Worldwide Express Freight and UPS Worldwide Express Freight Midday locations for the origin address and/or the airport code, and the sort code for destination address. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

