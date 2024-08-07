# PackageChemicalRecord

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**chemical_record_identifier** | **string** | Identifies the Chemcial Record. | 
**reportable_quantity** | **string** | Required if CommodityRegulatedLevelCode &#x3D; LQ or FR and if the field applies to the material by regulation. If reportable quantity is met, RQ should be entered. | [optional] 
**class_division_number** | **string** | This is the hazard class associated to the specified commodity.  Required if CommodityRegulatedLevelCode is &#x27;LQ&#x27; or &#x27;FR&#x27; | [optional] 
**sub_risk_class** | **string** | Required if CommodityRegulatedLevelCode &#x3D; LQ or FR and if the field applies to the material by regulation.  Secondary hazardous characteristics of a package. (There can be more than one – separate each with a comma). | [optional] 
**id_number** | **string** | This is the ID number (UN/NA/ID) for the specified commodity.   Required if CommodityRegulatedLevelCode &#x3D; LR, LQ or FR and if the field applies to the material by regulation.   UN/NA/ID Identification Number assigned to the specified regulated good. (Include the UN/NA/ID as part of the entry). | [optional] 
**packaging_group_type** | **string** | This is the packing group category associated to the specified commodity.  Required if CommodityRegulatedLevelCode &#x3D; LQ or FR and if the field applies to the material by regulation. Must be shown in Roman Numerals.  Valid values are:  I II III  blank | [optional] 
**quantity** | **string** | Required if CommodityRegulatedLevelCode &#x3D; LQ or FR. The numerical value of the mass capacity of the regulated good. | [optional] 
**uom** | **string** | Required if CommodityRegulatedLevelCode &#x3D; LQ or FR. The unit of measure used for the mass capacity of the regulated good.    Example: ml, L, g, mg, kg, cylinder, pound, pint, quart, gallon, ounce etc. | [optional] 
**packaging_instruction_code** | **string** | The packing instructions related to the chemical record. Required if CommodityRegulatedLevelCode &#x3D; LQ or FR and if the field applies to the material by regulation. | [optional] 
**proper_shipping_name** | **string** | The Proper Shipping Name assigned by ADR, CFR or IATA.   Required if CommodityRegulatedLevelCode &#x3D; LR, LQ or FR. | [optional] 
**technical_name** | **string** | The technical name (when required) for the specified commodity.   Required if CommodityRegulatedLevelCode &#x3D; LQ or FR and if the field applies to the material by regulation. | [optional] 
**additional_description** | **string** | Additional remarks or special provision information. Required if CommodityRegulatedLevelCode &#x3D; LQ or FR and if the field applies to the material by regulation.  Additional information that may be required by regulation about a hazardous material, such as, “Limited Quantity”, DOT-SP numbers, EX numbers. | [optional] 
**packaging_type** | **string** | The package type code identifying the type of packaging used for the commodity. (Ex: Fiberboard Box).  Required if CommodityRegulatedLevelCode &#x3D; LQ or FR. | [optional] 
**hazard_label_required** | **string** | Defines the type of label that is required on the package for the commodity.   Not applicable if CommodityRegulatedLevelCode &#x3D; LR or EQ. | [optional] 
**packaging_type_quantity** | **string** | The number of pieces of the specific commodity.   Required if CommodityRegulatedLevelCode &#x3D; LQ or FR.  Valid values: 1 to 999 | [optional] 
**commodity_regulated_level_code** | **string** | Indicates the type of commodity.  Valid values: LR, FR, LQ, EQ  FR &#x3D; Fully Regulated LQ &#x3D; Limited Quantity EQ &#x3D; Excepted Quantity LR &#x3D; Lightly Regulated | 
**transport_category** | **string** | Transport Category.  Valid values: 0 to 4 | [optional] 
**tunnel_restriction_code** | **string** | Defines what is restricted to pass through a tunnel. | [optional] 
**all_packed_in_one_indicator** | **string** | Indicates the hazmat shipment/package is all packed in one. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

