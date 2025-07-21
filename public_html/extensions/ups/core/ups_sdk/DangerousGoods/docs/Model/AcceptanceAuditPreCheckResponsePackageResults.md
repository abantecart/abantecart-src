# AcceptanceAuditPreCheckResponsePackageResults

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**package_identifier** | **string** | Identifies the package containing Dangerous Goods. | 
**accessible_indicator** | **string** | Indicates if a package is crew accessible or not.  Y &#x3D; Package is crew accessible. N &#x3D; Package is not crew accessible. | [optional] 
**europe_bu_indicator** | **string** | Indicates if origin country or territory is in the Europe Business Unit.    Y &#x3D; Origin country or territory is in the Europe Business Unit. N &#x3D; Origin country or territory is not in the Europe Business Unit. | [optional] 
**chemical_record_results** | [**\UPS\DangerousGoods\DangerousGoods\PackageResultsChemicalRecordResults[]**](PackageResultsChemicalRecordResults.md) | Chemical Records Results container.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

