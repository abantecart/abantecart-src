# ServiceCenterLocationPickupFacilities

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**name** | **string** | Name of the facility | 
**address** | [**\UPS\Pickup\Pickup\PickupFacilitiesAddress**](PickupFacilitiesAddress.md) |  | 
**slic** | **string** | SLIC code for the UPS Pickup facility | 
**type** | **string** | Freight or Package. | 
**timezone** | **string** | Facility&#x27;s Timezone. Format: - America/New_York - Asia/Hong_Kong - Europe/London | 
**phone** | **string** | Phone Number of the Pickup Facility | 
**fax** | **string** | Pickup Facilities Fax Number | 
**facility_time** | [**\UPS\Pickup\Pickup\PickupFacilitiesFacilityTime**](PickupFacilitiesFacilityTime.md) |  | [optional] 
**airport_code** | **string** | AirPort Code for destination/pickup facility.  Example: ATL (Atlanta) If Airport code is not present \&quot;---\&quot; will be returned. | [optional] 
**sort_code** | **string** | Sort Code for destination/pickup facility.  Example: V1 | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

