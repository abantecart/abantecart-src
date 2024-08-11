# QuantumViewResponse

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**response** | [**\UPS\QuantumView\QuantumView\QuantumViewResponseResponse**](QuantumViewResponseResponse.md) |  | 
**quantum_view_events** | [**\UPS\QuantumView\QuantumView\QuantumViewResponseQuantumViewEvents**](QuantumViewResponseQuantumViewEvents.md) |  | 
**bookmark** | **string** | Bookmarks the file for next retrieval, It is a base64Encoded String. It contains the combination of SubscriberID + SubscriptionName + File Name if the request is for all data. It contains SubscriberID if the request is for unread data. When a response comes back with a bookmark it indicates that there is more data. To fetch the remaining data, the requester should come back with the bookmark added to the original request. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

