# TrackingEventRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**tracking_number** | **string** | The package&#x27;s tracking number. | [optional] 
**local_activity_date** | **string** | The localized date of the activity. Format: YYYYMMDD | [optional] 
**local_activity_time** | **string** | The localized time of the activity. Format: HHMMSS (24 hr) | [optional] 
**activity_location** | [**\UPS\UPSTrackAlert\UPSTrackAlert\ActivityLocation**](ActivityLocation.md) |  | [optional] 
**activity_status** | [**\UPS\UPSTrackAlert\UPSTrackAlert\ActivityStatus**](ActivityStatus.md) |  | [optional] 
**scheduled_delivery_date** | **string** | Original scheduled delivery date of the package. Format: YYYYMMDD | [optional] 
**actual_delivery_date** | **string** | Actual delivery date of the package. Format: YYYYMMDD | [optional] 
**actual_delivery_time** | **string** | Actual delivery time of the package. Format: HHMMSS (24 hr) | [optional] 
**gmt_activity_date** | **string** | The GMT date of the activity. Format: YYYYMMDD | [optional] 
**gmt_activity_time** | **string** | The GMT time of the activity. Format: HHMMSS (24 hr) | [optional] 
**delivery_start_time** | **string** | The start time of a delivery. Format: HHMMSS (24 hr). | [optional] 
**delivery_end_time** | **string** | The end time of a window or the committed time or the delivered time. Format: HHMMSS (24 hr) | [optional] 
**delivery_time_description** | **string** | The date of this delivery detail. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

