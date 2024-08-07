# SubscriptionFileDelivery

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**package_reference_number** | [**\UPS\QuantumView\QuantumView\DeliveryPackageReferenceNumber[]**](DeliveryPackageReferenceNumber.md) | Package-level reference number.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**shipment_reference_number** | [**\UPS\QuantumView\QuantumView\DeliveryShipmentReferenceNumber[]**](DeliveryShipmentReferenceNumber.md) | Container tag for shipment reference number.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**shipper_number** | **string** | Shipper&#x27;s six digit alphanumeric account number. | 
**tracking_number** | **string** | Package&#x27;s 1Z tracking number. | 
**date** | **string** | Date that the package is delivered. Date format is YYYYMMDD. | 
**time** | **string** | Time that the package is delivered. Time format is HHMMSS | 
**driver_release** | **string** | Information about driver release note / signature. | [optional] 
**activity_location** | [**\UPS\QuantumView\QuantumView\DeliveryActivityLocation**](DeliveryActivityLocation.md) |  | [optional] 
**delivery_location** | [**\UPS\QuantumView\QuantumView\DeliveryDeliveryLocation**](DeliveryDeliveryLocation.md) |  | [optional] 
**cod** | [**\UPS\QuantumView\QuantumView\DeliveryCOD**](DeliveryCOD.md) |  | [optional] 
**bill_to_account** | [**\UPS\QuantumView\QuantumView\DeliveryBillToAccount**](DeliveryBillToAccount.md) |  | [optional] 
**last_pickup_date** | **string** | Last pickup by Date from the UPS Access Point Location. | [optional] 
**access_point_location_id** | **string** | UPS Access Point Location ID. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

