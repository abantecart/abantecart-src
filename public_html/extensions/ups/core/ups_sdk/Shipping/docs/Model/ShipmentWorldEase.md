# ShipmentWorldEase

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**destination_country_code** | **string** | The final destination country code. | 
**destination_postal_code** | **string** | The final destination postal code. | [optional] 
**gccn** | **string** | The Global Consolidation Clearance Number(GCCN) generated for the master shipment. This is required for child shipment. | [optional] 
**master_eu_consolidation_indicator** | **string** | 1 indicates a Master Consolidation request for the European Union. | [optional] 
**master_has_doc_box** | **string** | This field is a flag to indicate if the request is a master shipment. This is required for Master shipment only. If MasterHasDocBox is \&quot;0\&quot; then request is considered a master shipment. | [optional] 
**master_shipment_chg_type** | **string** | Code that indicates how shipping charges will be paid.  | Code  | Name                | Description:                                                              | | :--:  | :--                 | :--                                                                       | | CAF   | Cost And Freight    | Shipper pays to point of import, conignee pays balance.                   | | COL   | Freight Collect     | Consignee (with valid UPS account) pays all shipping charges              | | DDP   | Delivered Duty Paid | Shipper pays shipping and duty, consignee pays the Value Added Tax (VAT)  | | FOB   | Free On Board       | Shipper pays to point to export, consignee pays balance                   | | PRE   | Prepaid             | Shipper pays all shipping charges                                         | | SDT   | Free Domicile       | Child Shipper pays for shipping, duities and taxes                        | | 
**vendor_collect_id_number_exempt_indicator** | **string** | This field indicates if VendorCollectIDTypeCode and VendorCollectIDNumber should be exempt from validation. \&quot;0\&quot; indicates VendorCollectIDTypeCode and VendorCollectIDNumber fields are required. | [optional] 
**port_of_entry** | [**\UPS\Shipping\Shipping\ShipmentWorldEasePortOfEntry**](ShipmentWorldEasePortOfEntry.md) |  | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

