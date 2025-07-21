# DeliveryTime

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**end_time** | **string** | The end time of a window or the committed time or the delivered time. Only returned when the type is “EDW” or “CDW” or “IDW” or “CMT” or “DEL”. Format: HHMMSS (24 hr) | [optional] 
**start_time** | **string** | The start time of a delivery. Only returned when the type is “EDW” or “CDW” or “IDW”. Format: HHMMSS (24 hr). | [optional] 
**type** | **string** | The date of this delivery detail. Valid values:  EOD - End of Day CMT - Commit Time EDW - Estimated Delivery Window ** CDW - Confirmed Delivery Window ** IDW - Imminent Delivery Window ** DEL - Delivered Time | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

