# RateResponseResponse

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**response_status** | [**\UPS\Rating\Rating\ResponseResponseStatus**](ResponseResponseStatus.md) |  | 
**alert** | [**\UPS\Rating\Rating\ResponseAlert[]**](ResponseAlert.md) | Alert Container.  There can be zero to many alert containers with code and description.  **NOTE:** For versions &gt;&#x3D; v2403, this element will always be returned as an array. For requests using versions &lt; v2403, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**alert_detail** | [**\UPS\Rating\Rating\ResponseAlertDetail[]**](ResponseAlertDetail.md) | Alert Detail Container. Currently applies to and returned only for request containing HazMat and SubVersion greater than or equal to 1701.  **NOTE:** For versions &gt;&#x3D; v2403, this element will always be returned as an array. For requests using versions &lt; v2403, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**transaction_reference** | [**\UPS\Rating\Rating\ResponseTransactionReference**](ResponseTransactionReference.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

