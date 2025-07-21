# RatedShipmentRatedPackage

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**base_service_charge** | [**\UPS\Rating\Rating\RatedPackageBaseServiceCharge**](RatedPackageBaseServiceCharge.md) |  | [optional] 
**transportation_charges** | [**\UPS\Rating\Rating\RatedPackageTransportationCharges**](RatedPackageTransportationCharges.md) |  | [optional] 
**service_options_charges** | [**\UPS\Rating\Rating\RatedPackageServiceOptionsCharges**](RatedPackageServiceOptionsCharges.md) |  | [optional] 
**total_charges** | [**\UPS\Rating\Rating\RatedPackageTotalCharges**](RatedPackageTotalCharges.md) |  | [optional] 
**weight** | **string** | The weight of the package in the rated Package. | [optional] 
**billing_weight** | [**\UPS\Rating\Rating\RatedPackageBillingWeight**](RatedPackageBillingWeight.md) |  | [optional] 
**accessorial** | [**\UPS\Rating\Rating\RatedPackageAccessorial[]**](RatedPackageAccessorial.md) | The container for Accessorial indicators. This information would be returned only if ItemizedChargesRequested was present during Rate request. This is valid only for UPS Worldwide Express Freight and UPS Worldwide Express Freight Mid-day service request with Dry Ice or Oversize Pallet and SubVersion greater than or equal to 1707.  This is valid only for UPS Worldwide Express Freight and UPS Worldwide Express Freight Middday Service.  **NOTE:** For versions &gt;&#x3D; v2403, this element will always be returned as an array. For requests using versions &lt; v2403, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**itemized_charges** | [**\UPS\Rating\Rating\RatedPackageItemizedCharges[]**](RatedPackageItemizedCharges.md) | Itemized Charges are returned only when the subversion element is present and greater than or equal to &#x27;1607&#x27;.  These charges would be returned only when subversion is greater than or equal to 1607.  **NOTE:** For versions &gt;&#x3D; v2403, this element will always be returned as an array. For requests using versions &lt; v2403, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**negotiated_charges** | [**\UPS\Rating\Rating\RatedPackageNegotiatedCharges**](RatedPackageNegotiatedCharges.md) |  | [optional] 
**simple_rate** | [**\UPS\Rating\Rating\RatedPackageSimpleRate**](RatedPackageSimpleRate.md) |  | [optional] 
**rate_modifier** | [**\UPS\Rating\Rating\RatedPackageRateModifier[]**](RatedPackageRateModifier.md) | Container for returned Rate Modifier information. Applies only if SubVersion is 2205 or greater.  **NOTE:** For versions &gt;&#x3D; v2403, this element will always be returned as an array. For requests using versions &lt; v2403, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

