# ShipmentReferenceNumber

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**bar_code_indicator** | **string** | If the indicator is present then the reference number&#x27;s value will be bar coded on the label.  This is an empty tag, any value inside is ignored. Only one shipment-level or package-level reference number can be bar coded per shipment. In order to barcode a reference number, its value must be no longer than 14 alphanumeric characters or 24 numeric characters and cannot contain spaces. | [optional] 
**code** | **string** | Shipment Reference number type code. The code specifies the Reference name. Refer to the Reference Number Code table.  Valid if the origin/destination pair is not US/US or PR/PR and character should be alpha-numeric. | [optional] 
**value** | **string** | Customer supplied reference number.  Valid if the origin/destination pair is not US/US or PR/PR | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

