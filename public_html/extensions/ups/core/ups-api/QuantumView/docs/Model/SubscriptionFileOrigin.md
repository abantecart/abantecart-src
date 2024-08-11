# SubscriptionFileOrigin

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**package_reference_number** | [**\UPS\QuantumView\QuantumView\OriginPackageReferenceNumber[]**](OriginPackageReferenceNumber.md) | Package-level reference number.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**shipment_reference_number** | [**\UPS\QuantumView\QuantumView\OriginShipmentReferenceNumber[]**](OriginShipmentReferenceNumber.md) | Container tag for shipment reference number.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**shipper_number** | **string** | Shipper&#x27;s six digit alphanumeric account number. | 
**tracking_number** | **string** | Package&#x27;s 1Z tracking number. | 
**date** | **string** | Date that the package is picked up at the origin. Date format is YYYYMMDD. | 
**time** | **string** | Time that the package is picked up at the origin. Time format is HHMMSS. | 
**activity_location** | [**\UPS\QuantumView\QuantumView\OriginActivityLocation**](OriginActivityLocation.md) |  | [optional] 
**bill_to_account** | [**\UPS\QuantumView\QuantumView\OriginBillToAccount**](OriginBillToAccount.md) |  | [optional] 
**scheduled_delivery_date** | **string** | Scheduled delivery date for destination address. Date format is YYYYMMDD. | [optional] 
**scheduled_delivery_time** | **string** | Scheduled delivery time for destination address. Time format is HHMMSS. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

