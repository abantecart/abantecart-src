# ShipmentShipmentRatingOptions

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**negotiated_rates_indicator** | **string** | NegotiatedRatesIndicator -  Required to display two types of discounts: 1) Bids or Account Based Rates2) Web/Promotional Discounts BidsAccount Based Rates: If the indicator is present, the Shipper is authorized, and the Rating API XML Request is configured to return Negotiated Rates, then Negotiated Rates should be returned in the response. Web/Promotional Discounts: If the indicator is present, the Shipper is authorized for Web/Promotional Discounts then Negotiated Rates should be returned in the response. | [optional] 
**frs_shipment_indicator** | **string** | FRS Indicator. The indicator is required to obtain rates for UPS Ground Freight Pricing (GFP).  The account number must be enabled for GFP. | [optional] 
**rate_chart_indicator** | **string** | RateChartIndicator -  If present in a request, the response will contain a RateChart element. | [optional] 
**user_level_discount_indicator** | **string** | UserLevelDiscountIndicator - required to obtain rates for User Level Promotions.  This is required to obtain User Level Discounts. There must also be no ShipperNumber in the Shipper container. | [optional] 
**tpfc_negotiated_rates_indicator** | **string** | This indicator applies for a third party (3P) / Freight collect (FC) shipment only. For 3P/FC shipment if the shipper wishes to request for the negotiated rates of the third party then this indicator should be included in the request. If authorized the 3P/FC negotiated rates will be applied to the shipment and rates will be returned in response. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

