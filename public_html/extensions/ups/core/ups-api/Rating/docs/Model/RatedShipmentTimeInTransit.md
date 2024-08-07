# RatedShipmentTimeInTransit

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**pickup_date** | **string** | The date the user requests UPS to pickup the package from the origin. Format: YYYYMMDD. In the event this Pickup date differs from the Pickup date in the Estimated Arrival Container, a warning will be returned.  In the event this Pickup date differs from the Pickup date in the Estimated Arrival Container, a warning will be returned. | 
**documents_only_indicator** | **string** | If the indicator is present then the shipment was processed as Document Only. | [optional] 
**package_bill_type** | **string** | Package bill type for the shipment. Valid values:02 - Document only 03 - Non-Document04 - Pallet | [optional] 
**service_summary** | [**\UPS\Rating\Rating\TimeInTransitServiceSummary**](TimeInTransitServiceSummary.md) |  | 
**auto_duty_code** | **string** | Required output for International requests. If Documents indicator is set for Non-document a duty is automatically calculated. The possible values to be returned are: 01 - Dutiable02 - Non-Dutiable03 - Low-value04 - Courier Remission05 - Gift06 - Military07 - Exception08 - Line Release09 - Section 321 low value. | [optional] 
**disclaimer** | **string** | The Disclaimer is provided based upon the origin and destination country or territory codes provided in the request document. The possible disclaimers that can be returned are available in the Service Guaranteed Disclaimers table. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

