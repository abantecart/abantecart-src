# QuantumViewEventsSubscriptionEvents

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**name** | **string** | A name uniquely defined associated to the Subscription ID, for each subscription. Required if the SubscriptionEvents container is present. | [optional] 
**number** | **string** | A number uniquely defined associated to the Subscriber ID, for each subscription. Required if the SubscriptionEvents container is present. | [optional] 
**subscription_status** | [**\UPS\QuantumView\QuantumView\SubscriptionEventsSubscriptionStatus**](SubscriptionEventsSubscriptionStatus.md) |  | 
**date_range** | [**\UPS\QuantumView\QuantumView\SubscriptionEventsDateRange**](SubscriptionEventsDateRange.md) |  | [optional] 
**subscription_file** | [**\UPS\QuantumView\QuantumView\SubscriptionEventsSubscriptionFile[]**](SubscriptionEventsSubscriptionFile.md) | Container holds all of the unread files associated with the subscription.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

