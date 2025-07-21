# LocatorRequestLocationSearchCriteria

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**search_option** | [**\UPS\Locator\Locator\LocationSearchCriteriaSearchOption[]**](LocationSearchCriteriaSearchOption.md) |  | [optional] 
**maximum_list_size** | **string** | If present, indicates the maximum number of locations the client wishes to receive in response; ranges from 1 to 50 with a default value of 5. | [optional] 
**search_radius** | **string** | Defines the maximum radius the user wishes to search for a UPS location. If the user does not specify, the default value is 100 miles. Whole numbers only.   Valid values are: 5-100 for UnitOfMeasure MI 5-150 for UnitOfMesaure KM | [optional] 
**service_search** | [**\UPS\Locator\Locator\LocationSearchCriteriaServiceSearch**](LocationSearchCriteriaServiceSearch.md) |  | [optional] 
**freight_will_call_search** | [**\UPS\Locator\Locator\LocationSearchCriteriaFreightWillCallSearch**](LocationSearchCriteriaFreightWillCallSearch.md) |  | [optional] 
**access_point_search** | [**\UPS\Locator\Locator\LocationSearchCriteriaAccessPointSearch**](LocationSearchCriteriaAccessPointSearch.md) |  | [optional] 
**open_time_criteria** | [**\UPS\Locator\Locator\LocationSearchCriteriaOpenTimeCriteria**](LocationSearchCriteriaOpenTimeCriteria.md) |  | [optional] 
**brexit_filter** | **string** | Brexit Filter. Applicable for country code GB; Pass the PostalCode for the address in the location search if Brexit functionality is desired. UAPs with postal code starts with BT returned when brexit filter starts with BT, else UAPs returned with non BT postal code. Applicable for UAP and Proximal building search. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

