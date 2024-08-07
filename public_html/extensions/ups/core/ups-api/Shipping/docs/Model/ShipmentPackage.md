# ShipmentPackage

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**description** | **string** | Merchandise description of package.  Required for shipment with return service. | [optional] 
**pallet_description** | **string** | Description of articles &amp; special marks. Applicable for Air Freight only | [optional] 
**num_of_pieces** | **string** | Number of Pieces. Applicable for Air Freight only | [optional] 
**unit_price** | **string** | Unit price of the commodity. Applicable for Air Freight only  Limit to 2 digit after the decimal. The maximum length of the field is 12 including &#x27;.&#x27; and can hold up to 2 decimal place. (e.g. 999999999.99) | [optional] 
**packaging** | [**\UPS\Shipping\Shipping\PackagePackaging**](PackagePackaging.md) |  | 
**dimensions** | [**\UPS\Shipping\Shipping\PackageDimensions**](PackageDimensions.md) |  | [optional] 
**dim_weight** | [**\UPS\Shipping\Shipping\PackageDimWeight**](PackageDimWeight.md) |  | [optional] 
**package_weight** | [**\UPS\Shipping\Shipping\PackagePackageWeight**](PackagePackageWeight.md) |  | [optional] 
**large_package_indicator** | **string** | Presence of the indicator mentions that the package is Large Package.  This is an empty tag, any value inside is ignored. | [optional] 
**oversize_indicator** | **string** | Presence/Absence Indicator. Any value is ignored. If present, indicates that the package is over size.   Applicable for UPS Worldwide Economy DDU service. | [optional] 
**minimum_billable_weight_indicator** | **string** | Presence/Absence Indicator. Any value is ignored. If present, indicates that the package is qualified for minimum billable weight.   Applicable for UPS Worldwide Economy DDU service. | [optional] 
**reference_number** | [**\UPS\Shipping\Shipping\PackageReferenceNumber[]**](PackageReferenceNumber.md) |  | [optional] 
**additional_handling_indicator** | **string** | Additional Handling Required. The presence indicates additional handling is required, the absence indicates no additional handling is required. Additional Handling indicator indicates it&#x27;s a non-corrugated package. | [optional] 
**simple_rate** | [**\UPS\Shipping\Shipping\PackageSimpleRate**](PackageSimpleRate.md) |  | [optional] 
**ups_premier** | [**\UPS\Shipping\Shipping\PackageUPSPremier**](PackageUPSPremier.md) |  | [optional] 
**package_service_options** | [**\UPS\Shipping\Shipping\PackagePackageServiceOptions**](PackagePackageServiceOptions.md) |  | [optional] 
**commodity** | [**\UPS\Shipping\Shipping\PackageCommodity**](PackageCommodity.md) |  | [optional] 
**haz_mat_package_information** | [**\UPS\Shipping\Shipping\PackageHazMatPackageInformation**](PackageHazMatPackageInformation.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

