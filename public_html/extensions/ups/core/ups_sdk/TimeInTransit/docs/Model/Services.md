# Services

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**service_level** | **string** | Service level code     Valid domestic service codes: \&quot;1DMS\&quot;,\&quot;1DAS\&quot;,\&quot;1DM\&quot;,\&quot;1DA\&quot;,\&quot;1DP\&quot;,\&quot;2DM\&quot;,\&quot;2DA\&quot;,\&quot;3DS\&quot;,\&quot;GND\&quot;.      Valid International service codes (not a complete list) ,\&quot;01\&quot;,\&quot;02\&quot;,\&quot;03\&quot;,\&quot;05\&quot;,\&quot;08\&quot;,\&quot;09\&quot;,\&quot;10\&quot;,\&quot;11\&quot;,\&quot;18\&quot;,\&quot;19\&quot;,\&quot;20\&quot;,\&quot;21\&quot;,\&quot;22\&quot;,\&quot;23\&quot;,\&quot;24\&quot;,\&quot;25\&quot;,\&quot;26\&quot;,\&quot;28\&quot;,\&quot;29\&quot;,\&quot;33\&quot;,\&quot;68\&quot;. | 
**service_level_description** | **string** | Service name. Examples are: UPS Next Day Air, UPS Ground, UPS Expedited, UPS Worldwide Express Freight | 
**ship_date** | **string** | The date the shipment is tendered to UPS for shipping (can be dropped off at UPS or picked up by UPS).  This date may or may not be the UPS business date.     Valid Format: YYYY-MM-DD | 
**delivery_date** | **string** | Scheduled delivery date.     Valid format: YYYY-MM-DD | 
**commit_time** | **string** | Scheduled commit time.     For international shipments the value always come back from SE (OPSYS data) but for domestic, value may be used from NRF commit time.      Valid format: HH:MM:SS | 
**delivery_time** | **string** | Scheduled Delivery Time, value may be later then commit time.     Valid format: HH:MM:SS | 
**delivery_day_of_week** | **string** | Three character scheduled delivery day of week.     Valid values: \&quot;MON\&quot;,\&quot;TUE\&quot;,\&quot;WED\&quot;,\&quot;THU\&quot;,\&quot;FRI\&quot;, \&quot;SAT\&quot; | 
**next_day_pickup_indicator** | **string** | Returns a \&quot;1\&quot; if the requested shipped on date was changed. This data is available only for international transactions.     When this flag is set, WWDTDisclaimer.getNextDayDisclaimer method could be called to return the next day disclaimer message. | 
**saturday_pickup_indicator** | **string** | Returns \&quot;1\&quot; if Saturday Pickup is available for an extra charge otherwise it will return \&quot;0\&quot;.     When this flag is set, WWDTDisclaimer.getSaturdayPickupDisclaimer method could be called to return the Saturday pickup extra charge message | 
**saturday_delivery_date** | **string** | Delivery date of Saturday Delivery     Valid Format: YYYY-MM-DD | [optional] 
**saturday_delivery_time** | **string** | Delivery time of Saturday deliver     Valid format: HH:MM:SS | [optional] 
**service_remarks_text** | **string** | Service remarks text. The contents of this field will represent text that the back end application/function needs to display to clarify the time in transit calculation. | [optional] 
**guarantee_indicator** | **string** | Return \&quot;1\&quot; Guaranteed, or \&quot;0\&quot; Not Guaranteed based on below conditions:     If the ship date, delivery date, and system date are not within a defined peak date range, and a value for service guarantee is available in SE (OPSYS data) that will be returned.     If the ship date or delivery date or system date are within a defined peak date range and the service is within the list of services to remove guarantees for, \&quot;0\&quot; wil be returned. | 
**total_transit_days** | **int** | Available for International requests. Number of calendar days from origin location to destination location.  TotalTransitDays &#x3D; BusinessTransitDays + RestDaysCount + HolidayCount.     Defaults to 0. | 
**business_transit_days** | **int** | Returns the number of UPS business days from origin location to destination location. | 
**rest_days_count** | **int** | Returns the number of rest days encountered at the origin location.  this data is available only for international transactions.     Defaults to 0. | 
**holiday_count** | **int** | Returns the number of holidays encountered at the origin and destination location, if it effects the time and transit.  This data is available only for international transactions.     Defaults to 0. | 
**delay_count** | **int** | Returns the number of delay needed for customs encounter at the origin or destination location.  This data is available only for international transactions.      Defaults to 0. | 
**pickup_date** | **string** | Planned pickup date.     Note: This value may not equal the shipped on value requested.  This could happen when the requested shipped on date is a holiday or for locations needing 24 hour notice before a pickup could be made. | 
**pickup_time** | **string** | Latest possible pickup time. This data is available only for international transactions. If the package was not actually picked by UPS before this time, the services will not meet the guarantee commitment. | 
**cstccutoff_time** | **string** | Latest time a customer can contact UPS CST to be notified for requesting a pickup. This data is available only for international transactions. If customer does not notify UPS for a pickup before this time, the services will not meet the guarantee commitment. | 
**poddate** | **string** | Returns the date proof of delivery information would be available.  This data is available only for international transactions. | [optional] 
**poddays** | **int** | Returns the number of days proof of delivery information will be available.  This data is available only for international transactions. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

