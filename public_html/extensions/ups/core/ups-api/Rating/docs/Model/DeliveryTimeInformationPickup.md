# DeliveryTimeInformationPickup

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**date** | **string** | Shipment Date; The Pickup date is a Shipment Date and it is a required input field.  The user is allowed to query up to 35 days into the past and 60 days into the future. Format: YYYYMMDD  If a date is not provided, it will be defaulted to the current system date. | 
**time** | **string** | Reflects the time the package is tendered to UPS for shipping (can be dropped off at UPS or picked up by UPS).  Military Time Format HHMMSS or HHMM.   Invalid pickup time will not be validated. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

