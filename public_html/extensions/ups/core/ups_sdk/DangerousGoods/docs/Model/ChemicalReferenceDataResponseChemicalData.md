# ChemicalReferenceDataResponseChemicalData

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**chemical_detail** | [**\UPS\DangerousGoods\DangerousGoods\ChemicalDataChemicalDetail**](ChemicalDataChemicalDetail.md) |  | [optional] 
**proper_shipping_name_detail** | [**\UPS\DangerousGoods\DangerousGoods\ChemicalDataProperShippingNameDetail**](ChemicalDataProperShippingNameDetail.md) |  | [optional] 
**package_quantity_limit_detail** | [**\UPS\DangerousGoods\DangerousGoods\ChemicalDataPackageQuantityLimitDetail[]**](ChemicalDataPackageQuantityLimitDetail.md) | Container to hold Package Quantity Limit Detail information.  It will be returned if applies for a given chemical record.  **NOTE:** For versions &gt;&#x3D; v2403, this element will always be returned as an array. For requests using versions &lt; v2403, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

