# ShipmentPackage

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**packaging_type** | [**\UPS\Rating\Rating\PackagePackagingType**](PackagePackagingType.md) |  | [optional] 
**dimensions** | [**\UPS\Rating\Rating\PackageDimensions**](PackageDimensions.md) |  | [optional] 
**dim_weight** | [**\UPS\Rating\Rating\PackageDimWeight**](PackageDimWeight.md) |  | [optional] 
**package_weight** | [**\UPS\Rating\Rating\PackagePackageWeight**](PackagePackageWeight.md) |  | [optional] 
**commodity** | [**\UPS\Rating\Rating\PackageCommodity**](PackageCommodity.md) |  | [optional] 
**large_package_indicator** | **string** | This element does not require a value and if one is entered it will be ignored.  If present, it indicates the shipment will be categorized as a Large Package. | [optional] 
**package_service_options** | [**\UPS\Rating\Rating\PackagePackageServiceOptions**](PackagePackageServiceOptions.md) |  | [optional] 
**additional_handling_indicator** | **string** | A flag indicating if the packages require additional handling. True if AdditionalHandlingIndicator tag exists; false otherwise. Additional Handling indicator indicates it&#x27;s a non-corrugated package.  Empty Tag. | [optional] 
**simple_rate** | [**\UPS\Rating\Rating\PackageSimpleRate**](PackageSimpleRate.md) |  | [optional] 
**ups_premier** | [**\UPS\Rating\Rating\PackageUPSPremier**](PackageUPSPremier.md) |  | [optional] 
**oversize_indicator** | **string** | Presence/Absence Indicator. Any value inside is ignored. It indicates if packge is oversized.  Applicable for UPS Worldwide Economy DDU service | [optional] 
**minimum_billable_weight_indicator** | **string** | Presence/Absence Indicator. Any value inside is ignored. It indicates if packge is qualified for minimum billable weight.  Applicable for UPS Worldwide Economy DDU service | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

