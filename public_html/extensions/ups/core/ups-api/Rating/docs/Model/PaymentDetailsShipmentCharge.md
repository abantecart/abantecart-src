# PaymentDetailsShipmentCharge

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**type** | **string** | Values are 01 &#x3D; Transportation, 02 &#x3D; Duties and Taxes | 
**bill_shipper** | [**\UPS\Rating\Rating\ShipmentChargeBillShipper**](ShipmentChargeBillShipper.md) |  | [optional] 
**bill_receiver** | [**\UPS\Rating\Rating\ShipmentChargeBillReceiver**](ShipmentChargeBillReceiver.md) |  | [optional] 
**bill_third_party** | [**\UPS\Rating\Rating\ShipmentChargeBillThirdParty**](ShipmentChargeBillThirdParty.md) |  | [optional] 
**consignee_billed_indicator** | **string** | Consignee Billing payment option indicator. The presence indicates consignee billing option is selected. The absence indicates one of the other payment options is selected.  Empty Tag. This element or its sibling element, BillShipper, BillReceiver or BillThirdParty, must be present but no more than one can be present. This billing option is valid for a shipment charge type of Transportation only. Only applies to US/PR and PR/US shipment origins and destination. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

