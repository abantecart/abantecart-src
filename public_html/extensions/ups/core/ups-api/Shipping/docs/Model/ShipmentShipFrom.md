# ShipmentShipFrom

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**name** | **string** | The ship from location&#x27;s name or company name.  35 characters are accepted, but for return Shipment only 30 characters will be printed on the label.  Required if ShipFrom tag is in the XML. | 
**attention_name** | **string** | The ship from Attention name.  35 characters are accepted, but for return Shipment only 30 characters will be printed on the label.  Required if ShipFrom tag is in the XML and Invoice or CO International forms is requested. If not present, will default to the Shipper Attention Name. | [optional] 
**company_displayable_name** | **string** | Not applicable for ShipFrom. | [optional] 
**tax_identification_number** | **string** | Company&#x27;s Tax Identification Number at the pick up location.  Conditionally required if EEI form (International forms) is requested.  Applies to EEI Form only. | [optional] 
**tax_id_type** | [**\UPS\Shipping\Shipping\ShipFromTaxIDType**](ShipFromTaxIDType.md) |  | [optional] 
**phone** | [**\UPS\Shipping\Shipping\ShipFromPhone**](ShipFromPhone.md) |  | [optional] 
**fax_number** | **string** | The Ship from fax number.  If Ship from country or territory is US 10 digits allowed, otherwise 1-15 digits allowed. | [optional] 
**address** | [**\UPS\Shipping\Shipping\ShipFromAddress**](ShipFromAddress.md) |  | 
**vendor_info** | [**\UPS\Shipping\Shipping\ShipFromVendorInfo**](ShipFromVendorInfo.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

