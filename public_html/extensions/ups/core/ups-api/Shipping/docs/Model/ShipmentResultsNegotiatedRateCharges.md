# ShipmentResultsNegotiatedRateCharges

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**itemized_charges** | [**\UPS\Shipping\Shipping\NegotiatedRateChargesItemizedCharges[]**](NegotiatedRateChargesItemizedCharges.md) | Itemized Charges are returned only when the Subversion element is present and greater than or equal to 1601.  Negotiated itemized charges are only returned for certain contract-only shipments as well as Worldwide Express Freight, Ground Freight Pricing, and Hazmat movements.  **NOTE:** For versions &gt;&#x3D; v2403, this element will always be returned as an array. For requests using versions &lt; v2403, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**tax_charges** | [**\UPS\Shipping\Shipping\NegotiatedRateChargesTaxCharges[]**](NegotiatedRateChargesTaxCharges.md) | TaxCharges container are returned only when TaxInformationIndicator is present in request. TaxCharges container contains Tax information for a given shipment.  **NOTE:** For versions &gt;&#x3D; v2403, this element will always be returned as an array. For requests using versions &lt; v2403, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**total_charge** | [**\UPS\Shipping\Shipping\NegotiatedRateChargesTotalCharge**](NegotiatedRateChargesTotalCharge.md) |  | [optional] 
**total_charges_with_taxes** | [**\UPS\Shipping\Shipping\NegotiatedRateChargesTotalChargesWithTaxes**](NegotiatedRateChargesTotalChargesWithTaxes.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

