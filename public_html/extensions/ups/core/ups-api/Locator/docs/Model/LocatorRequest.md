# LocatorRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**request** | [**\UPS\Locator\Locator\LocatorRequestRequest**](LocatorRequestRequest.md) |  | 
**origin_address** | [**\UPS\Locator\Locator\LocatorRequestOriginAddress**](LocatorRequestOriginAddress.md) |  | 
**translate** | [**\UPS\Locator\Locator\LocatorRequestTranslate**](LocatorRequestTranslate.md) |  | 
**unit_of_measurement** | [**\UPS\Locator\Locator\LocatorRequestUnitOfMeasurement**](LocatorRequestUnitOfMeasurement.md) |  | [optional] 
**location_id** | **string[]** | Location ID is the identification number of the UPS affiliated location. | [optional] 
**location_search_criteria** | [**\UPS\Locator\Locator\LocatorRequestLocationSearchCriteria**](LocatorRequestLocationSearchCriteria.md) |  | [optional] 
**sort_criteria** | [**\UPS\Locator\Locator\LocatorRequestSortCriteria**](LocatorRequestSortCriteria.md) |  | [optional] 
**allow_all_confidence_levels** | **string** | Indicator to allow confidence level in search. | [optional] 
**search_option_code** | **string** | Valid values:  01-Proximity Search Details 02-Address Search Details 03-Proximity Search Summary 04-Address Search Summary 05-Freight Will Call Search.  Either OptionType 03 or 04 is required. | [optional] 
**service_geo_unit** | [**\UPS\Locator\Locator\LocatorRequestServiceGeoUnit**](LocatorRequestServiceGeoUnit.md) |  | [optional] 
**freight_indicator** | **string** | FreightIndicator. Required for Freight Location Search. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

