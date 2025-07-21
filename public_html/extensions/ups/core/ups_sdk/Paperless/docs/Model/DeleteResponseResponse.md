# DeleteResponseResponse

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**response_status** | [**\UPS\Paperless\Paperless\ResponseResponseStatus**](ResponseResponseStatus.md) |  | 
**alert** | [**\UPS\Paperless\Paperless\ResponseAlert[]**](ResponseAlert.md) | Alert Container.  There can be zero to many alert containers with code and description.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**transaction_reference** | [**\UPS\Paperless\Paperless\ResponseTransactionReference**](ResponseTransactionReference.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

