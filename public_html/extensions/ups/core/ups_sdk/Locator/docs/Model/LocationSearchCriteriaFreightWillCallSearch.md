# LocationSearchCriteriaFreightWillCallSearch

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**freight_will_call_request_type** | **string** | Valid values are:  1 - Postal Code 2 - Delivery SLIC 3 - Delivery City/State. 1: Freight Will Call Search based on Postal Code, this search is valid for Postal code countries. 2: Freight Will Call Search based on SLIC. 3: Freight Will Call Search based on City and/or State. This Search is valid for non-postal code Countries | 
**facility_address** | [**\UPS\Locator\Locator\FreightWillCallSearchFacilityAddress[]**](FreightWillCallSearchFacilityAddress.md) |  | 
**origin_or_destination** | **string** | OriginOrDestination is required for FreightWillCallRequestType 1 and type 3 . Valid values: 01-Origin facilities 02-Destination facilities. | 
**format_postal_code** | **string** | FormatPostalCode would be required in the request when FreightWillCallRequestType is 1. Valid values are: NFR-No format requested FR-format requested FS-format and search NVR-No validation requested. | 
**day_of_week_code** | **string** | Day Of week Code. Valid Values are 1 to 7.  1-Sunday 2-Monday  3-Tuesday  4-Wednesday 5-Thursday 6-Friday 7-Saturday. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

