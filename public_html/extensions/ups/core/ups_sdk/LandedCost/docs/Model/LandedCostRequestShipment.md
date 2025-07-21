# LandedCostRequestShipment

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **string** | Specifies the Shipment ID in the Landed Cost quote. It is an arbitrary string provided by the user of the API and will be  returned with the Landed Cost Quote to indicate which shipment the tariffs apply to. There are similar IDs associated with the Product and Order objects. | 
**import_country_code** | **string** | Specifies the Import/Ship-To/Destination/Final country of the shipment. Please check country list in the Appendix. | 
**import_province** | **string** | Province/State is supported only for a few countries such as Mexico, Canada, etc. Please check Province list in the Appendix | [optional] 
**ship_date** | **string** | Defaults to current date if not provided. Date format: YYYY-MM-DD. | [optional] 
**incoterms** | **string** | Supported Incoterm Values: 1. CFR - Cost &amp; Freight  2. CIF - Cost, Insurance &amp; Freight  3. CIP - Carriage and Insurance Paid-To  4. CPT - Carriage Paid-To  5. DAP - Delivered At Place  6. DAT - Delivered At Terminal  7. DDP - Delivered Duty Paid  8. DPU - Delivered at Place Unloaded  9. EXW - Ex Works  10. FAS - Free Alongside Ship  11. FCA - Free Carrier  12. FOB - Free On Board (Default) | [optional] [default to 'FOB']
**export_country_code** | **string** | Specifies the export/ship-from/origin country of the shipment. Please check country List in the Appendix section.  **Note:** Export country code must be different from the import country code. | 
**trans_modes** | **string** | The modes of transportation (in upper case). Supported Values:  1. INT_AIR 2.  INT_OCEAN  3. INT_RAIL  4. INT_TRUCK  5. DOM_AIR  6. DOM_OCEAN  7. DOM_RAIL  8. DOM_TRUCK   Default value will vary based on the import country. | [optional] 
**transport_cost** | **float** | Specifies the Freight charge or transport costs, which are used for tariff calculations. Landed cost result might have some dependency on the freight charges in some countries. Therefore, freight amount should be always provided for accurate Landed Cost result.   Allowed values:  1. Any non-negative floating-point number.  2. Numeric value with optional decimal value. | [optional] 
**shipment_type** | **string** | Specifies the shipment type such as Gift, Document, Commercial (Sale), etc.  Supported Shipment Types:  1. GIFT  2. COMMERCIAL  3. SALE  4. SAMPLE  5. REPAIR  6. RETURN  7. OTHER   Default value will vary and based on import country. | [optional] 
**shipment_items** | [**\UPS\LandedCost\LandedCost\RequestShipmentItems[]**](RequestShipmentItems.md) | Array of shipment item objects (commodities), that are in a shipment. | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

