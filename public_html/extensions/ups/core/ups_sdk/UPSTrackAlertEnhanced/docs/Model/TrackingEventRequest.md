# TrackingEventRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**tracking_number** | [****](.md) | The package&#x27;s tracking number. | [optional] 
**local_activity_date** | [****](.md) | The localized date of the activity. Format: YYYYMMDD | [optional] 
**local_activity_time** | [****](.md) | The localized time of the activity. Format: HHMMSS (24 hr) | [optional] 
**activity_location** | [**\UPS\UPSTrackAlertEnhanced\UPSTrackAlertEnhanced\ActivityLocation**](ActivityLocation.md) |  | [optional] 
**activity_status** | [**\UPS\UPSTrackAlertEnhanced\UPSTrackAlertEnhanced\ActivityStatus**](ActivityStatus.md) |  | [optional] 
**scheduled_delivery_date** | [****](.md) | Original scheduled delivery date of the package. Format: YYYYMMDD | [optional] 
**actual_delivery_date** | [****](.md) | Actual delivery date of the package. Format: YYYYMMDD (This field is blank until the delivery event occurs) | [optional] 
**actual_delivery_time** | [****](.md) | Actual delivery time of the package. Format: HHMMSS (24 hr) (This field is blank until the delivery event occurs) | [optional] 
**gmt_activity_date** | [****](.md) | The GMT date of the activity. Format: YYYYMMDD | [optional] 
**gmt_activity_time** | [****](.md) | The GMT time of the activity. Format: HHMMSS (24 hr) | [optional] 
**delivery_start_time** | [****](.md) | The start time of a delivery. Format: HHMMSS (24 hr). | [optional] 
**delivery_end_time** | [****](.md) | The end time of a window or the committed time or the delivered time. Format: HHMMSS (24 hr) | [optional] 
**delivery_time_description** | [****](.md) | The date of this delivery detail. | [optional] 
**delivery_photo** | [****](.md) | Base64 encoded image of the delivery photo (This field is blank until the delivery photo is made available). | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

