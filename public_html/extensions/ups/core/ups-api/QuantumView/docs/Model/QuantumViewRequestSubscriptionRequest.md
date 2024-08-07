# QuantumViewRequestSubscriptionRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**name** | **string** | Name of subscription requested by user, as one type of request criteria. Required when the customer wants to request data for a specific subscription name. Subscription name consists of up to 21 alphanumerics. | [optional] 
**date_time_range** | [**\UPS\QuantumView\QuantumView\SubscriptionRequestDateTimeRange**](SubscriptionRequestDateTimeRange.md) |  | [optional] 
**file_name** | **string[]** | File name of specific subscription requested by user. Format: YYMMDD_HHmmssnnn. (nnn - sequence number: usually &#x3D; 001) | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

