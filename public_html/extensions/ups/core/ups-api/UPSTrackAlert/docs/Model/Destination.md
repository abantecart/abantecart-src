# Destination

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**url** | **string** | It is an HTTP-based callback end point that is exposed by the client to receive event notification. This endpoint must be operational arround the clock to ensure no event notifications are missed. If this endpoint is not continuously available, incoming events will be lost. | 
**credential_type** | **string** | It is an open-entry field that indicates type of credentials supported by the client. | 
**credential** | **string** | It is an opaque string meant for client authentication. If for any reason this credential changes then any event notification will fail until a new subscription is made. | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

