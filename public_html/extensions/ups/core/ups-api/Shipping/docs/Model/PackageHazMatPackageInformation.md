# PackageHazMatPackageInformation

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**all_packed_in_one_indicator** | **string** | Presence/Absence Indicator. Any value is ignored. Presence indicates if multiple, different hazmat/chemicals are contained within one box in a package  When number of Hazmat containers in a package is more than one, either AllPackedInOneIndicator or OverPackedIndicator is needed | [optional] 
**over_packed_indicator** | **string** | Presence/Absence Indicator. Any value is ignored. Presence indicates that one or more hazmat/chemicals are in separate boxes in a package.  When number of Hazmat containers in a package is more than one, either AllPackedInOneIndicator or OverPackedIndicator is needed | [optional] 
**q_value** | **string** | When a HazMat shipment specifies AllPackedInOneIndicator and the regulation set for that shipment is IATA, Ship API must require the shipment to specify a Q-Value with exactly one of the following values: 0.1; 0.2; 0.3; 0.4; 0.5; 0.6; 0.7; 0.8; 0.9; 1.0 | [optional] 
**outer_packaging_type** | **string** | This field is used for the Outer Hazmat packaging type.  Ex. FIBERBOARD BOX, WOOD(EN) BOX, PLASTIC JERRICAN, METAL BOX, STEEL DRUM, OTHER, PLASTIC BOX, PLASTIC DRUM, STYROFOAM BOX, CYLINDERS, ENVIROTAINER, PLYWOOD BOX, ALUMINUM DRUM, ALUMINUM CYLINDERS, PLASTIC PAIL, PLYWOOD DRUM, FIBER DRUM, STEEL JERRICAN, ALUMINUM JERRICAN, STEEL BOX, CARTON, ALUMINUM BOX | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

