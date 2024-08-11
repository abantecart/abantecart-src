# SubscriptionFileGeneric

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**activity_type** | **string** | Unique identifier that defines the type of activity. - VM &#x3D; Void for Manifest - UR &#x3D; Undeliverable Returns - IR &#x3D; Invoice Removal Successful - TC &#x3D; Transport Company USPS scan PS &#x3D; &#x27;Postal Service Possession Scan&#x27; - FN &#x3D; UPS Access Point/Alternate Delivery Location Email Notification Failure - DS &#x3D; Destination Scan - AG &#x3D; Package is in transit to a UPS facility - RE &#x3D; UPS Returns Exchange - RP &#x3D; Retail Pickup - UD &#x3D; Updated delivery date - OD &#x3D; Out for Delivery - SD &#x3D; Scheduled for Delivery - FM &#x3D; Tendered to FMP - PT &#x3D; UPS Courier Handoff (Package Tendered) DIALS -VX - PC &#x3D; UPS Courier Confirmation – XPLD -VX | 
**tracking_number** | **string** | Package&#x27;s tracking number. | 
**shipper_number** | **string** | Shipper&#x27;s alphanumeric account number. | [optional] 
**shipment_reference_number** | [**\UPS\QuantumView\QuantumView\GenericShipmentReferenceNumber[]**](GenericShipmentReferenceNumber.md) | Container tag for shipment reference number.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**package_reference_number** | [**\UPS\QuantumView\QuantumView\GenericPackageReferenceNumber[]**](GenericPackageReferenceNumber.md) | Package-level reference number.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**service** | [**\UPS\QuantumView\QuantumView\GenericService**](GenericService.md) |  | [optional] 
**activity** | [**\UPS\QuantumView\QuantumView\GenericActivity**](GenericActivity.md) |  | [optional] 
**bill_to_account** | [**\UPS\QuantumView\QuantumView\GenericBillToAccount**](GenericBillToAccount.md) |  | [optional] 
**ship_to** | [**\UPS\QuantumView\QuantumView\GenericShipTo**](GenericShipTo.md) |  | [optional] 
**rescheduled_delivery_date** | **string** | If Activity Type is \&quot;DS\&quot; or \&quot;UD\&quot;, this element will contain Rescheduled Delivery Date. Format will be YYYYMMDD.  If Activity Type is \&quot;OD\&quot;, this element will contain Rescheduled Delivery Date. Format will be YYYYMMDD.  If Activity Type is \&quot;SD\&quot;, this element will contain agreed upon date with Customer for delivery Date. Format will be YYYYMMDD. | [optional] 
**failure_notification** | [**\UPS\QuantumView\QuantumView\GenericFailureNotification**](GenericFailureNotification.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

