# LandedCostResponseShipment

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**currency_code** | **string** | Specifies the Currency Code set at the commodity level. This currency is applicable for all duty, tax, VAT, and fee at the shipment and commodity level. | 
**import_country_code** | **string** | Specifies the Import/Ship-To/Destination/Final country of the shipment. Please check country list in the Appendices section. | 
**id** | **string** | Specifies the Shipment ID in the Landed Cost quote. | 
**brokerage_fee_items** | [**\UPS\LandedCost\LandedCost\BrokerageFeeItems[]**](BrokerageFeeItems.md) | An array of Brokerage fees. | 
**total_brokerage_fees** | **float** | Grand total of all applicable Brokerage fees. | 
**total_duties** | **float** | Total duty amount of this shipment. | 
**total_commodity_level_taxes_and_fees** | **float** | Total tax and other fees at commodity level. | 
**total_shipment_level_taxes_and_fees** | **float** | Total tax and other fees at shipment level. | 
**total_vat** | **float** | Total VAT of the shipment. | 
**total_duty_and_tax** | **float** | Grand total of the combined duty, VAT, tax, and other fees of all commodities in this shipment including shipment level taxes and fees. | 
**grand_total** | **float** | Sum of totalDutyAndTax + totalBrokerageFees | 
**shipment_items** | [**\UPS\LandedCost\LandedCost\ResponseShipmentItems[]**](ResponseShipmentItems.md) | An array of Landed Cost for all valid commodities. | 
**trans_id** | **string** | An identifier unique to the request. | [optional] 
**perf_stats** | [**\UPS\LandedCost\LandedCost\LandedCostResponseShipmentPerfStats**](LandedCostResponseShipmentPerfStats.md) |  | [optional] 
**al_version** | **int** | Version number of the instance that processed this request. Default is 1. | [optional] 
**errors** | [**\UPS\LandedCost\LandedCost\Errors**](Errors.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

