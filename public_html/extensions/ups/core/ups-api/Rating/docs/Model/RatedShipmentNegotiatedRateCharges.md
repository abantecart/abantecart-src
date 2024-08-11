# RatedShipmentNegotiatedRateCharges

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**itemized_charges** | [**\UPS\Rating\Rating\NegotiatedRateChargesItemizedCharges[]**](NegotiatedRateChargesItemizedCharges.md) | Itemized Charges are returned only when the subversion element is present and greater than or equal to &#x27;1601&#x27;.  These charges would be returned only when subversion is greater than or equal to 1601.  **NOTE:** For versions &gt;&#x3D; v2403, this element will always be returned as an array. For requests using versions &lt; v2403, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**tax_charges** | [**\UPS\Rating\Rating\NegotiatedRateChargesTaxCharges[]**](NegotiatedRateChargesTaxCharges.md) | TaxCharges container are returned only when TaxInformationIndicator is present in request. TaxCharges container contains Tax information for a given shipment.  **NOTE:** For versions &gt;&#x3D; v2403, this element will always be returned as an array. For requests using versions &lt; v2403, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**total_charge** | [**\UPS\Rating\Rating\NegotiatedRateChargesTotalCharge**](NegotiatedRateChargesTotalCharge.md) |  | 
**total_charges_with_taxes** | [**\UPS\Rating\Rating\NegotiatedRateChargesTotalChargesWithTaxes**](NegotiatedRateChargesTotalChargesWithTaxes.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

