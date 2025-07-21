# BSISV1Shipment

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**import_country_code** | [****](.md) | The ISO 3166 code of the country imported from. Click &lt;a href&#x3D;\&quot;https://developer.ups.com/api/reference/globalcheckout/appendix?loc&#x3D;en_US\&quot; target&#x3D;\&quot;_blank\&quot; rel&#x3D;\&quot;noopener noreferrer\&quot;&gt;here&lt;/a&gt; for more information. | 
**import_province** | [****](.md) | Specifies the Province taken from. Click &lt;a href&#x3D;\&quot;https://developer.ups.com/api/reference/globalcheckout/appendix?loc&#x3D;en_US\&quot; target&#x3D;\&quot;_blank\&quot; rel&#x3D;\&quot;noopener noreferrer\&quot;&gt;here&lt;/a&gt; for more information. | [optional] 
**ship_date** | [****](.md) | ShipDate of the Request (YYYY-MM-DD). | [optional] 
**export_country_code** | [****](.md) | The ISO 3166 code of the country exported to. | 
**trans_modes** | [****](.md) | The shipment mode of transportation. If not one of the listed values then it will default to the first one from the import country.  | Mode      | Description                                   | | :--:      | :--                                           | | DOM_AIR   | Domestic Air transportation                   | | DOM_OCEAN | Domestic Ocean transportation                 | | DOM_RAIL  | Domestic Rail transportation                  | | DOM_TRUCK | Domestic Truck transportation                 | | INT_AIR   | International Air transportation              | | INT_OCEAN | International Ocean transportation            | | INT_RAIL  | International Rail transportation             | | INT_TRUCK | International/Interstate Truck transportation | | [optional] 
**transport_cost** | [**\UPS\GlobalCheckout\GlobalCheckout\BSISV1ChargeDetail**](BSISV1ChargeDetail.md) | Specifies the Transport Costs, which are used for tariff calculations in some governments.  If needed and not provided then internal Rate call will be made to retrieve it. | [optional] 
**insurance_cost** | [**\UPS\GlobalCheckout\GlobalCheckout\BSISV1ChargeDetail**](BSISV1ChargeDetail.md) | Specifies the fee charged by UPS for insuring the package, which could be used for tariff calculations in some governments. This will be defaulted to 0 if needed and not provided. | [optional] 
**shipment_type** | [****](.md) | Specifies the shipment type.  | Type  | Description                                                       | | :--:  | :--                                                               | | GIFT  | GIFT                                                              |  | COMM  | Sale, Sample, Repair                                              | | OTHR  | Return, Other, and Intercompany Data, Anything else not supported | | PERS  | Personal                                                          | | [optional] 
**shipper_address** | [**\UPS\GlobalCheckout\GlobalCheckout\BSISV1Address**](BSISV1Address.md) | Shipper Address of request. Needed for internal rate call to calculate transportCost if that is needed and not provided. | 
**ship_to_address** | [****](.md) | ShipTo Address of request. Needed for internal rate call to calculate transportCost if that is needed and not provided. | 
**shipment_items** | [****](.md) | array of request ShipmentItems. | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

