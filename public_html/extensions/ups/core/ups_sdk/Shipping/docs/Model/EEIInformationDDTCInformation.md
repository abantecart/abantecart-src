# EEIInformationDDTCInformation

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**itar_exemption_number** | **string** | The specific citation (exemption number) under the International Traffic in Arms Regulations (ITAR) from the Code of Federal Register (see 22 CFR 120-130) that exempts the shipment from the requirements for a license or other written authorization from the Directorate of Trade Controls (DDTC).  Refer to EEI License Codes in the Appendix for valid values.  Applies to EEI Form only. This field is applicable for EEIFiling option 1A and 3. | [optional] 
**usml_category_code** | **string** | Digit numeric code (e.g. 01-18, 20 or 21). Indicates the U.S. Munitions List (USML) category article, service or related technical data as it applies to the article reported.  Applies to EEI form only. It is required for EEIFilingOption code 3. | [optional] 
**eligible_party_indicator** | **string** | Presence/Absent indicator. Certification by the U.S. exporter that the exporter is an eligible party to participate in the defense trade. | [optional] 
**registration_number** | **string** | It is a unique registration code assigned to the registrant. The DDTC registration code consist of a letter prefix, M (assigned to a manufacturer and/or exporter) or K (assigned to a broker), followed by four or five digits (e.g. K-1234 or M12345).  It is required for EEIFilingOption code 3. | [optional] 
**quantity** | **string** | Export Quantity.  Applies to EEI form only. It is required for EEIFilingOption code 3. Only positive integer value is valid. | [optional] 
**unit_of_measurement** | [**\UPS\Shipping\Shipping\DDTCInformationUnitOfMeasurement**](DDTCInformationUnitOfMeasurement.md) |  | [optional] 
**significant_military_equipment_indicator** | **string** | Presence/ Absence Indicator.  Applies to EEI form only. | [optional] 
**acm_number** | **string** | Approved Community Member Number (ACM). It is required to be provided along with ITARExemptionNumber for some License code (SGB and SAU). The ACM# for the United Kingdom (License code SGB) must begin with UK followed by 9 numbers.  The ACM# for Australia (License Code SAU) must begin with DTT followed by 8 numbers.  Applies to EEI form only.  It is required for EEIFilingOption code 1A and 3. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

