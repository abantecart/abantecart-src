# PackageServiceOptionsDryIce

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**regulation_set** | **string** | Regulation set for DryIce Shipment. Valid values: CFR &#x3D; For HazMat regulated by US Dept of Transportation within the U.S. or ground shipments to Canada,IATA &#x3D; For Worldwide Air movement.   The following values are valid: CFR and IATA. | 
**dry_ice_weight** | [**\UPS\Rating\Rating\DryIceDryIceWeight**](DryIceDryIceWeight.md) |  | 
**medical_use_indicator** | **string** | Presence/Absence Indicator. Any value inside is ignored. Relevant only in CFR regulation set. If present it is used to designate the Dry Ice is for any medical use and rates are adjusted for DryIce weight more than 2.5 KGS or 5.5 LBS. | [optional] 
**audit_required** | **string** | Presence/Absence Indicator. Any value inside is ignored. Indicates a Dry Ice audit will be performed per the Regulation Set requirements. Empty tag means indicator is present. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

