# PackagePackageServiceOptions

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**delivery_confirmation** | [**\UPS\Rating\Rating\PackageServiceOptionsDeliveryConfirmation**](PackageServiceOptionsDeliveryConfirmation.md) |  | [optional] 
**access_point_cod** | [**\UPS\Rating\Rating\PackageServiceOptionsAccessPointCOD**](PackageServiceOptionsAccessPointCOD.md) |  | [optional] 
**cod** | [**\UPS\Rating\Rating\PackageServiceOptionsCOD**](PackageServiceOptionsCOD.md) |  | [optional] 
**declared_value** | [**\UPS\Rating\Rating\PackageServiceOptionsDeclaredValue**](PackageServiceOptionsDeclaredValue.md) |  | [optional] 
**shipper_declared_value** | [**\UPS\Rating\Rating\PackageServiceOptionsShipperDeclaredValue**](PackageServiceOptionsShipperDeclaredValue.md) |  | [optional] 
**shipper_release_indicator** | **string** | The presence indicates that the package may be released by driver without a signature from the consignee.  Empty Tag. Only available for US50/PR to US50/PR packages without return service. | [optional] 
**proactive_indicator** | **string** | Any value associated with this element will be ignored. If present, the package is rated for UPS Proactive Response and proactive package tracking.Contractual accessorial for health care companies to allow package monitoring throughout the UPS system.  Shippers account needs to have valid contract for UPS Proactive Response. | [optional] 
**refrigeration_indicator** | **string** | Presence/Absence Indicator. Any value is ignored. If present, indicates that the package contains an item that needs refrigeration.  Shippers account needs to have a valid contract for Refrigeration. | [optional] 
**insurance** | [**\UPS\Rating\Rating\PackageServiceOptionsInsurance**](PackageServiceOptionsInsurance.md) |  | [optional] 
**ups_premium_care_indicator** | **string** | The UPSPremiumCareIndicator indicates special handling is required for shipment having controlled substances.  Empty Tag means indicator is present.  Valid only for Canada to Canada movements.  Available for the following Return Services: - Returns Exchange (available with a contract) - Print Return Label - Print and Mail - Electronic Return Label - Return Service Three Attempt  May be requested with following UPS services: - UPS Express® Early - UPS Express - UPS Express Saver - UPS Standard.  Not available for packages with the following: - Delivery Confirmation - Signature Required - Delivery Confirmation - Adult Signature Required. | [optional] 
**haz_mat** | [**\UPS\Rating\Rating\PackageServiceOptionsHazMat**](PackageServiceOptionsHazMat.md) |  | [optional] 
**dry_ice** | [**\UPS\Rating\Rating\PackageServiceOptionsDryIce**](PackageServiceOptionsDryIce.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

