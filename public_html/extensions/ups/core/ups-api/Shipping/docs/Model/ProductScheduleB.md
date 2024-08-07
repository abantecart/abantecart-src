# ProductScheduleB

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**number** | **string** | A unique 10-digit commodity classification code for the item being exported. (To classify a commodity access the following Web page: http://www.census.gov/foreign-trade/schedules/b/#search).  Applies to EEI forms only. Has to be 10 characters. | 
**quantity** | **string[]** | The count of how many Schedule B units of the current good are in the shipment (EEI only). For example, if the Schedule B unit of measure is dozens and eight dozen, is being shipped, indicate 8 in this field.  Applies to EEI forms only. Conditionally required for EEI forms if ScheduleB UnitOfMeasurement is not X. Should be Numeric. Valid characters are 0 -9. | [optional] 
**unit_of_measurement** | [**\UPS\Shipping\Shipping\ScheduleBUnitOfMeasurement[]**](ScheduleBUnitOfMeasurement.md) |  | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

