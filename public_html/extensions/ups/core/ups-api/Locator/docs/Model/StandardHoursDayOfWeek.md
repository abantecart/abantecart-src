# StandardHoursDayOfWeek

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**day** | **string** | Day of week.  Valid values:  1-Sunday 2-Monday 3-Tuesday 4-Wednesday 5-Thursday 6-Friday 7-Saturday. | 
**open_hours** | **string** | Open time of a location in military format (HHMM) e.g. 930, 1700, 1845 etc. with exception for midnight. For midnight the time will be returned as 0. | [optional] 
**close_hours** | **string** | Close time of a location in military format (HHMM) e.g. 930, 1700, 1845 etc. with exception for midnight. For midnight the time will be returned as 0. | [optional] 
**latest_drop_off_hours** | **string** | LatestDropOffHours for Hour Type 50. Latest Drop Off time of a location in military format (HHMM) e.g. 930, 1700, 1845 etc. with exception for midnight. For midnight the time will be returned as 0. | [optional] 
**prep_hours** | **string** | PrepHours for Hour Type 51. Prep Hours of a location in military format (HHMM) e.g. 930, 1700, 1845 etc. with exception for midnight. For midnight the time will be returned as 0. | [optional] 
**closed_indicator** | **string** | Presence absence Indicator. Indicator present means location is closed. | [optional] 
**open24_hours_indicator** | **string** | Presence/ Absence Indicator. Presence denotes  for the given day, if the location is open 24 hours. Absence denotes the location is not open for 24 hours on the given day. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

