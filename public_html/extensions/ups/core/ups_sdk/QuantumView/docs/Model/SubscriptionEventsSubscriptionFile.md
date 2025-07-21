# SubscriptionEventsSubscriptionFile

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**file_name** | **string** | File name belonging to specific subscription requested by user. Format: YYMMDD_HHmmssnnn | 
**status_type** | [**\UPS\QuantumView\QuantumView\SubscriptionFileStatusType**](SubscriptionFileStatusType.md) |  | 
**manifest** | [**\UPS\QuantumView\QuantumView\SubscriptionFileManifest[]**](SubscriptionFileManifest.md) | Container represents all data that is relevant for the shipment, such as origin, destination, shipper, payment method etc. It will be returned when available.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**origin** | [**\UPS\QuantumView\QuantumView\SubscriptionFileOrigin[]**](SubscriptionFileOrigin.md) | Information about shipment&#x27;s origin.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**exception** | [**\UPS\QuantumView\QuantumView\SubscriptionFileException[]**](SubscriptionFileException.md) | Shipment exception data.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**delivery** | [**\UPS\QuantumView\QuantumView\SubscriptionFileDelivery[]**](SubscriptionFileDelivery.md) | Container for delivery information.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**generic** | [**\UPS\QuantumView\QuantumView\SubscriptionFileGeneric[]**](SubscriptionFileGeneric.md) | Container for generic record information.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

