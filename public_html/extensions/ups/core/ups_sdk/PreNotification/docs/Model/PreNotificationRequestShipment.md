# PreNotificationRequestShipment

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**shipper_number** | **string** | Shipper&#x27;s six digit account number. | 
**shipment_identification_number** | **string** | 1Z Number of the first package in the shipment. | 
**ship_to_address** | [**\UPS\PreNotification\PreNotification\ShipmentShipToAddress**](ShipmentShipToAddress.md) |  | 
**ship_from_address** | [**\UPS\PreNotification\PreNotification\ShipmentShipFromAddress**](ShipmentShipFromAddress.md) |  | 
**pickup_date** | **string** | Date of the On Call Air Pickup. Format is YYYYMMDD | 
**service** | [**\UPS\PreNotification\PreNotification\ShipmentService**](ShipmentService.md) |  | 
**regulation_set** | **string** | The Regulatory set associated with every regulated shipment. It must be same across the shipment. Valid values are: - ADR – European Agreement concerning the International Carriage of Dangerous Goods by Road. - 49CFR – Title 49 of the United States Code of Federal Regulations. - IATA – International Air Transport Association (IATA) Dangerous Goods Regulations. | 
**package** | [**\UPS\PreNotification\PreNotification\ShipmentPackage[]**](ShipmentPackage.md) |  | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

