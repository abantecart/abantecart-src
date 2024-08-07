# ManifestPackage

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**activity** | [**\UPS\QuantumView\QuantumView\PackageActivity[]**](PackageActivity.md) | Information about package delivery activity.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**description** | **string** | Description of package merchandise. | [optional] 
**dimensions** | [**\UPS\QuantumView\QuantumView\PackageDimensions**](PackageDimensions.md) |  | [optional] 
**dimensional_weight** | [**\UPS\QuantumView\QuantumView\PackageDimensionalWeight**](PackageDimensionalWeight.md) |  | [optional] 
**package_weight** | [**\UPS\QuantumView\QuantumView\PackagePackageWeight**](PackagePackageWeight.md) |  | [optional] 
**large_package** | **string** | Values for LargePackage are: - 1 - Oversize 1 - 2 - Oversize 2 - 4 - Large package | [optional] 
**tracking_number** | **string** | Package&#x27;s tracking number. | [optional] 
**reference_number** | [**\UPS\QuantumView\QuantumView\PackageReferenceNumber[]**](PackageReferenceNumber.md) | Container tag for information about the package-level reference number.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**package_service_options** | [**\UPS\QuantumView\QuantumView\PackagePackageServiceOptions**](PackagePackageServiceOptions.md) |  | [optional] 
**ups_premium_care_indicator** | **string** | Presence of the tag indicates UPSPremiumCare applies to this package | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

