# ShipToAddress

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**address_line** | **string[]** | Address Line of the consignee. Only first two Address Lines will be printed on the label. | 
**city** | **string** | Consignee&#x27;s city. 30 characters are accepted, but only 15 characters will be printed on the label. | 
**state_province_code** | **string** | Consignee&#x27;s state or province code. Required for US or Canada.  If destination is US or CA, then the value must be a valid US State/ Canadian Province code.  If the country or territory is Ireland, the StateProvinceCode will contain the county. | [optional] 
**postal_code** | **string** | Consignee&#x27;s postal code.  If the ShipTo country or territory is US or Puerto Rico, 5 or 9 digits are required.  If the ShipTo country or territory is CA, then the postal code is required and must be 6 alphanumeric characters whose format is A#A#A# where A is an uppercase letter and # is a digit.  Otherwise optional. For all other countries or territories the postal code is optional and must be no more than 9 alphanumeric characters long. | [optional] 
**country_code** | **string** | Consignee&#x27;s country or territory code.  Must be a valid UPS Billing country or territory code. For Return Shipment the country or territory code must meet the following conditions: - At least two of the following country or territory codes are the same: ShipTo, ShipFrom, and Shipper. - None of the following country or territory codes are the same and are a member of the EU: ShipTo, ShipFrom, and Shipper. - If any of the two following country or territory codes: ShipTo/ ShipFrom/ Shipper are members in EU otherwise check if the shipper has Third country or territory Contract. | 
**residential_address_indicator** | **string** | This field is a flag to indicate if the receiver is a residential location.  True if ResidentialAddressIndicator tag exists.  This is an empty tag, any value inside is ignored. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

