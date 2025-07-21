# ShipperAddress

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**address_line** | **string[]** | The Shipper street address including name and number (when applicable). Up to three occurrences are allowed; only the first is printed on the label.  35 characters are accepted, but for the first occurrence, only 30 characters will be printed on the label for return shipments. | 
**city** | **string** | Shipper&#x27;s City.   For forward Shipment 30 characters are accepted, but only 15 characters will be printed on the label. | 
**state_province_code** | **string** | Shipper&#x27;s state or province code.  For forward Shipment 5 characters are accepted, but only 2 characters will be printed on the label.  For US, PR and CA accounts, the account must be either a daily pickup account, an occasional account, or a customer B.I.N account. | [optional] 
**postal_code** | **string** | Shipper&#x27;s postal code. | [optional] 
**country_code** | **string** | Shipper&#x27;s country or territory code.  Refer to country or territory Codes in the Appendix for valid values.  Drop Shipper accounts are valid for return service shipments only if the account is Trade Direct (TD) enabled. | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

