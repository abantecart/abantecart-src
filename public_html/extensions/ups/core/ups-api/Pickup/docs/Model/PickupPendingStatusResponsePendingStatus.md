# PickupPendingStatusResponsePendingStatus

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**pickup_type** | **string** | Specify the type of pending pickup. - 01 &#x3D; on-callPickup | 
**service_date** | **string** | Local service date. Format: yyyyMMdd - yyyy &#x3D; Year applicable - MM &#x3D; 01-12 - dd &#x3D; 01-31 | 
**prn** | **string** | Returned PRN | 
**gwn_status_code** | **string** | Status code for Smart Pickup. | [optional] 
**on_call_status_code** | **string** | A unique string identifier to identify a success pre-notification processing. Only available if end result is success. | [optional] 
**pickup_status_message** | **string** | The status for on-callpickup.  PickupPendingStatusResponse will only display incomplete status for today and tomorrow only. - 002 and 012 are the most common responses. - 001 &#x3D; Received at dispatch - 002 &#x3D; Dispatched to driver - 003 &#x3D; Order successfully completed - 004 &#x3D; Order unsuccessfully completed - 005 &#x3D; Missed commit – Updated ETA supplied by driver - 007 &#x3D; Cancelled - 008 &#x3D; Order has invalid order status - 012 &#x3D; Your pickup request is being processed | 
**billing_code** | **string** | Pickup billing classification for on call - 01 &#x3D; Regular - 02 &#x3D; Return - 03 &#x3D; Alternate Address (Not supported for now) | [optional] 
**contact_name** | **string** | on-callpickup contact name | [optional] 
**reference_number** | **string** | Customer provided reference number for on-call pickup | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

