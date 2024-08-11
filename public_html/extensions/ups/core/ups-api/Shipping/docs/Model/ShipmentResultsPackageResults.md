# ShipmentResultsPackageResults

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**tracking_number** | **string** | Package 1Z number.   For Mail Innovations shipments, please use the USPSPICNumber when tracking packages (a non-1Z number Mail Manifest Id is returned). | 
**rate_modifier** | [**\UPS\Shipping\Shipping\PackageResultsRateModifier**](PackageResultsRateModifier.md) |  | [optional] 
**base_service_charge** | [**\UPS\Shipping\Shipping\PackageResultsBaseServiceCharge**](PackageResultsBaseServiceCharge.md) |  | [optional] 
**service_options_charges** | [**\UPS\Shipping\Shipping\PackageResultsServiceOptionsCharges**](PackageResultsServiceOptionsCharges.md) |  | [optional] 
**shipping_label** | [**\UPS\Shipping\Shipping\PackageResultsShippingLabel**](PackageResultsShippingLabel.md) |  | [optional] 
**shipping_receipt** | [**\UPS\Shipping\Shipping\PackageResultsShippingReceipt**](PackageResultsShippingReceipt.md) |  | [optional] 
**uspspic_number** | **string** | USPSPICNumber is USPS Package Identification; it should be used for tracking Mail Innovations shipments. | [optional] 
**cn22_number** | **string** | USPS defined CN22 ID number format varies based on destination country or territory.  Not applicable as of Jan 2015.  Mail Innovations shipments US to VI, PR, and GU are not considered international. | [optional] 
**accessorial** | [**\UPS\Shipping\Shipping\PackageResultsAccessorial[]**](PackageResultsAccessorial.md) | The container for Accessorial indicators. This information would be returned only for UPS Worldwide Express Freight and UPS Worldwide Express Freight Mid-day service request with Dry Ice or Oversize Pallet and SubVersion greater than or equal to 1707. This is valid only for UPS Worldwide Express Freight and UPS Worldwide Express Freight Mid-day service.  **NOTE:** For versions &gt;&#x3D; v2403, this element will always be returned as an array. For requests using versions &lt; v2403, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**simple_rate** | [**\UPS\Shipping\Shipping\PackageResultsSimpleRate**](PackageResultsSimpleRate.md) |  | [optional] 
**form** | [**\UPS\Shipping\Shipping\PackageResultsForm**](PackageResultsForm.md) |  | [optional] 
**itemized_charges** | [**\UPS\Shipping\Shipping\PackageResultsItemizedCharges[]**](PackageResultsItemizedCharges.md) | Itemized Charges are returned only when the subversion element is present and greater than or equal to 1607. Package level itemized charges are only returned for US domestic movements.  **NOTE:** For versions &gt;&#x3D; v2403, this element will always be returned as an array. For requests using versions &lt; v2403, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**negotiated_charges** | [**\UPS\Shipping\Shipping\PackageResultsNegotiatedCharges**](PackageResultsNegotiatedCharges.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

