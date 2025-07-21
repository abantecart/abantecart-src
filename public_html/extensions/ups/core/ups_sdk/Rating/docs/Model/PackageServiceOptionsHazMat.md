# PackageServiceOptionsHazMat

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**package_identifier** | **string** | Identifies the package containing Dangerous Goods.  Required if SubVersion is greater than or equal to 1701. | [optional] 
**q_value** | **string** | QValue is required when a HazMat shipment specifies AllPackedInOneIndicator and the regulation set for that shipment is IATA.   Applies only if SubVersion is greater than or equal to 1701. Valid values are : 0.1; 0.2; 0.3; 0.4; 0.5; 0.6; 0.7; 0.8; 0.9; 1.0 | [optional] 
**over_packed_indicator** | **string** | Presence/Absence Indicator. Any value is ignored. Presence indicates that shipment is overpack.  Applies only if SubVersion is greater than or equal to 1701. | [optional] 
**all_packed_in_one_indicator** | **string** | Presence/Absence Indicator. Any value is ignored. Indicates the hazmat shipment/package is all packed in one.  Applies only if SubVersion is greater than or equal to 1701. | [optional] 
**haz_mat_chemical_record** | [**\UPS\Rating\Rating\HazMatHazMatChemicalRecord[]**](HazMatHazMatChemicalRecord.md) |  | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

