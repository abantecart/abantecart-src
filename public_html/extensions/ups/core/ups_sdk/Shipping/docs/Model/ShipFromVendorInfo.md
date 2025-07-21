# ShipFromVendorInfo

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**vendor_collect_id_type_code** | **string** | Code that identifies the type of Vendor Collect ID Number. Valid Values - 0356 &#x3D; IOSS - 0357 &#x3D; VOEC - 0358 &#x3D; HMRC  Vendor Collect ID Number type code will be printed on commercial invoice if present. | 
**vendor_collect_id_number** | **string** | Shipper&#x27;s VAT Tax collection registration number to be entered by Shipper at time of shipment creation. Presence of this number as part of the shipment information implies the shipper has collected/paid the required VAT tax (outside of UPS/UPS systems). Vendor Colect ID Number will be printed on commercial invoice if present.  Sample Values:   &#x27;IMDEU1234567&#x27; (IOSS #),  &#x27;VOEC1234567&#x27; (VOEC #),  &#x27;GB1234567&#x27; (HMRC #)  Required if the shipment is subject to Vendor Collect ID collection | 
**consignee_type** | **string** | Consignee Type. 01 &#x3D; Business  02 &#x3D; Consumer NA &#x3D; Not Applicable | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

