# IncludeCriteriaSearchFilter

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**dcr_indicator** | **string** | DCR/DCO Availability indicator for UPS Access Point. Either this indicator is present or not present. Presence indicates a search for access points with DCR. Any data in the element is ignored. | [optional] 
**shipping_availability_indicator** | **string** | Shipping Availability indicator for UPS Access Point. Either this indicator is present or not present. Presence indicates a search of access points with shipping availability. Any data in it is ignored. | [optional] 
**shipper_preparation_delay** | **string** | Value for the number of days to check for shipping availability from the current day. When this value is present, ShippingAvailabilityIndicator is implied implicitly. | [optional] 
**click_and_collect_sort_with_distance** | **string** | This contains the distance (in given UnitOfMeasurement) wherin to sort the click and collect access point locations above other access point locations when a UPS Access Point candidate list is obtained in search by address or geocode search. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

