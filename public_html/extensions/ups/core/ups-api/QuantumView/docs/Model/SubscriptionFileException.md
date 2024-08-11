# SubscriptionFileException

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**package_reference_number** | [**\UPS\QuantumView\QuantumView\ExceptionPackageReferenceNumber[]**](ExceptionPackageReferenceNumber.md) | Package-level reference number.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**shipment_reference_number** | [**\UPS\QuantumView\QuantumView\ExceptionShipmentReferenceNumber[]**](ExceptionShipmentReferenceNumber.md) | Container tag for shipment reference number.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**shipper_number** | **string** | Shipper&#x27;s six digit alphanumeric account number. | 
**tracking_number** | **string** | Package&#x27;s 1Z tracking number. | 
**date** | **string** | Date that the package is delivered. Date format is YYYYMMDD. | 
**time** | **string** | Time that the package is delivered. Time format is HHMMSS | 
**updated_address** | [**\UPS\QuantumView\QuantumView\ExceptionUpdatedAddress**](ExceptionUpdatedAddress.md) |  | [optional] 
**status_code** | **string** | Code for status of updating shipping address issue. | [optional] 
**status_description** | **string** | Description for status of updating shipping address issue. | [optional] 
**reason_code** | **string** | Code for reason of updating shipping address issue. | [optional] 
**reason_description** | **string** | Description for reason of updating shipping address issue. | [optional] 
**resolution** | [**\UPS\QuantumView\QuantumView\ExceptionResolution**](ExceptionResolution.md) |  | [optional] 
**rescheduled_delivery_date** | **string** | Rescheduled delivery date for updated shipping address. Date format is YYYYMMDD. | [optional] 
**rescheduled_delivery_time** | **string** | Rescheduled delivery time for updated shipping address. Time format is HHMMSS | [optional] 
**activity_location** | [**\UPS\QuantumView\QuantumView\ExceptionActivityLocation**](ExceptionActivityLocation.md) |  | [optional] 
**bill_to_account** | [**\UPS\QuantumView\QuantumView\ExceptionBillToAccount**](ExceptionBillToAccount.md) |  | [optional] 
**access_point_location_id** | **string** | The UPS Access Point Location ID. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

