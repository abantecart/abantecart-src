# ShipFromAddress

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**address_line** | **string[]** | The origin street address including name and number (when applicable).  Length is not validated. | 
**city** | **string** | Origin city.  Required if country or territory does not utilize postal codes. Length is not validated. | [optional] 
**state_province_code** | **string** | Origin state code.  A StateProvinceCode and valid account number are required when requesting negotiated rates. Otherwise the StateProvinceCode is optional.  If the TaxInformationIndicator flag is present in the request, a StateProvinceCode must be entered for tax charges to be accurately calculated in the response. | [optional] 
**postal_code** | **string** | Origin postal code.  Required if country or territory utilizes postal codes (e.g. US and PR). | [optional] 
**country_code** | **string** | Origin country or territory code. Refer to the Supported Country or Territory Tables located in the Appendix.  Required, but defaults to US. | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

