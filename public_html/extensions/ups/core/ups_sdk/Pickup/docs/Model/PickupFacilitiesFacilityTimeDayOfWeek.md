# PickupFacilitiesFacilityTimeDayOfWeek

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**day** | **string** | Day of the week. Mon-Sun | 
**earliest_drop_offor_pickup** | **string** | Earliest time that a customer can pick up a package. | [optional] 
**latest_drop_offor_pickup** | **string** | Latest time that a customer can pick up a package. | [optional] 
**open_hours** | **string** | Facility Open Hours. The latest local open time. Format: HHmm - Hour: 0-23 - Minute: 0-59 | 
**close_hours** | **string** | Facility Close Hours. The latest local close time. Format: HHmm - Hour: 0-23 - Minute: 0-59 | 
**prep_time** | **string** | Preparation time for hold for pickup  Conditionally required if request is for hold for pickup. | [optional] 
**last_drop** | **string** | Latest time a package, requiring preparation can be dropped off (Close time - Prep time). | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

