# EEIInformationLicense

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**number** | **string** | Represents any one of the following values: export license number, exception code, CFR citation, KPC Number, ACM Number.  Applies to EEI form only.  Refer to EEI License Types and Exemptions in the Appendix  for valid values and formats. | [optional] 
**code** | **string** | The standard license code published by US government.  Refer to EEI License Codes in the Appendix for valid values.  Applies to EEI form only. It is required for EEIFilingOption code 3. It is optionally required for all other filing types; however, it is used to categorize each product as SDL or non-SDL.  It is also used to identify which piece of information is applicable. | [optional] 
**license_line_value** | **string** | The export monetary amount allowed per license. Required for a licensable product when the EEI form is selected. Format: Whole numbers only.  Applies to EEI form only. Required if EEIFilingOption code 1A (only for SDL shipments) or 3. | [optional] 
**eccn_number** | **string** | Product ECCN Number issued by BIS (Bureau of Industry and Security). If the license number is a commerce license, ECCN must be provided. The format is #A### or EAR99  Applies to EEI forms only. It is required for EEIFilingOption code 3. ECCN is required one of the following License Exception Codes is entered: CIV, CTP, ENC, GBS, KMI, LVS, TSR | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

