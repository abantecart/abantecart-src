# PackageServiceOptionsDryIce

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**regulation_set** | **string** | Regulation set for dryIce Shipment. Valid values: CFR &#x3D; HazMat regulated by US Dept. of Transportation within the U.S. or ground shipments to Canada, IATA&#x3D; Worldwide Air movement.  The following values are valid: IATA, CFR. | 
**dry_ice_weight** | [**\UPS\Shipping\Shipping\DryIceDryIceWeight**](DryIceDryIceWeight.md) |  | 
**medical_use_indicator** | **string** | Presence/Absence Indicator. Any value inside is ignored. Relevant only in CFR regulation set. If present it is used to designate the dry Ice is for any medical use and rates are adjusted for DryIce weight more than 2.5 Kgs or 5.7 Lbs. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

