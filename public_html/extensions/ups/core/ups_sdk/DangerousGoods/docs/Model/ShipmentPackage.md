# ShipmentPackage

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**package_identifier** | **string** | Identifies the package containing Dangerous Goods. | 
**package_weight** | [**\UPS\DangerousGoods\DangerousGoods\PackagePackageWeight**](PackagePackageWeight.md) |  | 
**q_value** | **string** | This is required when a HazMat shipment specifies AllPackedInOneIndicator and the regulation set for that shipment is IATA.   Valid values: 0.1; 0.2; 0.3; 0.4; 0.5; 0.6; 0.7; 0.8; 0.9; 1.0 | [optional] 
**over_packed_indicator** | **string** | Presence/Absence Indicator. Any value is ignored. Presence indicates that shipment is over pack. | [optional] 
**transportation_mode** | **string** | The method of transport by which a shipment is approved to move and the regulations associated with that method.    Only required when the CommodityRegulatedLevelCode is FR or LQ.  Valid entries include: GND, CAO, and PAX. | [optional] 
**emergency_phone** | **string** | 24 Hour Emergency Phone Number of the shipper.  Valid values for this field are (0) through (9) with trailing blanks.  For numbers within the U.S., the layout is 1, area code, 7-digit number. For all other countries or territories the layout is country or territory code, area code, number.  The Emergency Phone Number can only include the following allowable characters “period “.”, dash “-“, plus sign “+” and conventional parentheses “(“ and “)”, “EXT or OPT”  Required when (TDG regulation set and CommodityRegulatedLevelCode &#x3D; FR) | [optional] 
**emergency_contact** | **string** | The emergency information, contact name and/or contract number, required to be communicated when a call is placed to the EmergencyPhone.  The information is required if there is a value in the EmergencyPhone field above and the shipment is with a US50 or PR origin and/or destination and the RegulationSet is IATA. | [optional] 
**chemical_record** | [**\UPS\DangerousGoods\DangerousGoods\PackageChemicalRecord[]**](PackageChemicalRecord.md) |  | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

