# ShipmentPackage

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**tracking_number** | **string** | The packages tracking number. | 
**package_weight** | [**\UPS\PreNotification\PreNotification\PackagePackageWeight**](PackagePackageWeight.md) |  | 
**transportation_mode** | **string** | Declares that a package was prepared according to ground, passenger aircraft, or cargo aircraft only. Only required when the CommodityRegulatedLevelCode is FR or LQ.  Valid entries include: GND, CAO, PAX. | [optional] 
**void_indicator** | **string** | Indicator to specify that a Dangerous Goods package is voided. True if VoidIndicator tag exists; false otherwise. | [optional] 
**package_points** | **string** | Regulated Commodity Transport Package Score Quantity | [optional] 
**chemical_record** | [**\UPS\PreNotification\PreNotification\PackageChemicalRecord[]**](PackageChemicalRecord.md) |  | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

