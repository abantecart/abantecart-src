# PackageChemicalRecord

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**reportable_quantity** | **string** | Indicates whether or not a material being transported meets the definition of a hazardous material and meets or exceeds a reportable quantity threshold. If reportable quantity is met, \&quot;RQ\&quot; should be entered.  Any other value will be  interpreted as \&quot;Non Reportable\&quot; quantity. | [optional] 
**class_division_number** | **string** | This is the hazard class associated to the specified commodity. Required if CommodityRegulatedLevelCode is &#x27;LQ&#x27; or &#x27;FR&#x27; | [optional] 
**sub_risk_class** | **string** | Secondary hazardous characteristics of a package. (There can be more than one – separate each with a comma.) | [optional] 
**id_number** | **string** | This is the ID number (UN/NA/ID) for the specified commodity.  UN/NA/ID Identification Number assigned to the specified regulated good. (Include the UN/NA/ID as part of the entry). | [optional] 
**packaging_group_type** | **string** | This is the packing group category associated to the specified commodity. Must be shown in Roman Numerals. Valid values are: - I - II - III - blank | [optional] 
**quantity** | **string** | Required if CommodityRegulatedLevelCode &#x3D; LQ or FR. The numerical value of the mass capacity of the regulated good. | [optional] 
**uom** | **string** | Required if CommodityRegulatedLevelCode &#x3D; LQ or FR. The unit of measure used for the mass capacity of the regulated good. | [optional] 
**packaging_instruction_code** | **string** | The packing instructions related to the chemical record. | [optional] 
**emergency_phone** | **string** | 24 Hour Emergency Phone Number of the shipper.  Valid values for this field are (0) through (9) with trailing blanks.  For numbers within the U.S., the layout is 1, area code, 7-digit number. For all other countries the layout is country code, area code, number.                                                                                                                                                                                                                                                                       The following are restricted in the phone number period \&quot;.\&quot;, dash \&quot;-\&quot;, plus sign \&quot;+\&quot; and conventional parentheses \&quot;(\&quot; and \&quot;)\&quot;, \&quot;EXT\&quot; or \&quot;OPT\&quot;  The following are restricted in the phone number period \&quot;.\&quot;, dash \&quot;-\&quot;, plus sign \&quot;+\&quot; and conventional parentheses \&quot;(\&quot; and \&quot;)\&quot;, \&quot;EXT\&quot; or \&quot;OPT\&quot; | [optional] 
**emergency_contact** | **string** | The emergency information, contact name and/or contract number, required to be communicated when a call is placed to the EmergencyPhoneNumber. | [optional] 
**proper_shipping_name** | **string** | The Proper Shipping Name assigned by ADR, CFR or IATA. Required if CommodityRegulatedLevelCode &#x3D; LQ or FR. | [optional] 
**technical_name** | **string** | The technical name (when required) for the specified commodity. | [optional] 
**additional_description** | **string** | Additional remarks or special provision information. | [optional] 
**packaging_type** | **string** | The type of package used to contain the regulated good. (Ex: Fiberboard Box). | [optional] 
**hazard_label_required** | **string** | Defines the type of label that is required on the package for the commodity. | [optional] 
**packaging_type_quantity** | **string** | The number of pieces of the specific commodity. Required if CommodityRegulatedLevelCode &#x3D; LQ or FR.  Valid values are 1 to 999. | [optional] 
**commodity_regulated_level_code** | **string** | Indicates the type of commodity, Fully Regulated (FR), Limited Quantity (LQ), Lightly Regulated (LR)  Valid values are LR, FR and LQ. | 
**transport_category** | **string** | Transport Category. Valid values are 0 to 4. | [optional] 
**tunnel_restriction_code** | **string** | Defines what is restricted to pass through a tunnel. | [optional] 
**q_value** | **string** | When a HazMat shipment specifies AllPackedInOneIndicator and the regulation set for that shipment is IATA,  Q-Value specifies exactly one of the following values: 0.1; 0.2; 0.3; 0.4; 0.5; 0.6; 0.7; 0.8; 0.9; 1.0  Valid values are : 0.1; 0.2; 0.3; 0.4; 0.5; 0.6; 0.7; 0.8; 0.9; 1.0 | [optional] 
**over_packed_indicator** | **string** | Presence/Absence Indicator. Any value is ignored. Presence indicates that shipment is overpack. | [optional] 
**all_packed_in_one_indicator** | **string** | Presence/Absence Indicator. Any value is ignored. Presence indicates if multiple, different hazmat/chemicals are contained within one box in a package | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

