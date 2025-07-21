# AcceptanceAuditPreCheckRequestShipment

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**shipper_number** | **string** | Shipper&#x27;s six digit account number.  Your UPS Account Number must have correct Dangerous goods contract to successfully use this Webservice. | 
**ship_from_address** | [**\UPS\DangerousGoods\DangerousGoods\ShipmentShipFromAddress**](ShipmentShipFromAddress.md) |  | 
**ship_to_address** | [**\UPS\DangerousGoods\DangerousGoods\ShipmentShipToAddress**](ShipmentShipToAddress.md) |  | 
**service** | [**\UPS\DangerousGoods\DangerousGoods\ShipmentService**](ShipmentService.md) |  | 
**regulation_set** | **string** | The Regulatory set associated with every regulated shipment. It must be same across the shipment. Not required when the CommodityRegulatedLevelCode is EQ.  Valid values: ADR, 49CFR, IATA.  ADR &#x3D; Europe to Europe Ground Movement 49CFR &#x3D; HazMat regulated by US Dept. of Transportation within the U.S. or ground shipments to Canada    IATA&#x3D; Worldwide Air movement. | [optional] 
**package** | [**\UPS\DangerousGoods\DangerousGoods\ShipmentPackage[]**](ShipmentPackage.md) |  | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

