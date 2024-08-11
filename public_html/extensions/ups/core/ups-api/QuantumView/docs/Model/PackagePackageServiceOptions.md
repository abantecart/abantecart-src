# PackagePackageServiceOptions

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**cod** | [**\UPS\QuantumView\QuantumView\PackageServiceOptionsCOD**](PackageServiceOptionsCOD.md) |  | [optional] 
**insured_value** | [**\UPS\QuantumView\QuantumView\PackageServiceOptionsInsuredValue**](PackageServiceOptionsInsuredValue.md) |  | [optional] 
**earliest_delivery_time** | **string** | Earliest delivery time. Time format is HHMMSS. | [optional] 
**hazardous_materials_code** | **string** | Indicates if the package contains hazardous materials. Valid values: - 1 - Hazardous Material - 2 - Electronically billed hazardous material.  If present, only one package may exist in the shipment. | [optional] 
**hold_for_pickup** | **string** | A flag indicating if a package should be held for pickup. True if tag exists, false otherwise. | 
**add_shipping_charges_to_cod_indicator** | **string** | An indicator flag that represents a Collect on Delivery (COD) package. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

