# PackageServiceOptionsDeclaredValue

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**currency_code** | **string** | The IATA currency code associated with the declared value amount for the package.  Required if a value for the package declared value amount exists in the MonetaryValue tag. Must match one of the IATA currency codes. Length is not validated. UPS does not support all international currency codes. Refer to Currency Codes in the Appendix for a list of valid codes. | 
**monetary_value** | **string** | The monetary value for the declared value amount associated with the package.  Max value of 5,000 USD for Local and 50,000 USD for Remote. Absolute maximum value is 21474836.47 | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

