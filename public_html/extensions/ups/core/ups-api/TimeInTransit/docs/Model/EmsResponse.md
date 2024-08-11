# EmsResponse

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**ship_date** | **string** | The date the shipment is tendered to UPS for shipping (can be dropped off at UPS or picked up by UPS).  This date may or may not be the UPS business date.     Valid Format: YYYY-MM-DD | 
**ship_time** | **string** | The time the shipment is tendered to UPS for shipping (can be dropped off at UPS or picked up by UPS).      Valid Format: HH:MM:SS | 
**service_level** | **string** | Service Levels being returned.     A &#x3D; all service levels.     Blank is the default for all Service Level values. | 
**bill_type** | **string** | Represents the shipment type.     Valid values: \&quot;02\&quot;,\&quot;03\&quot;,\&quot;04\&quot;,\&quot;07\&quot;   02 - Document   03 - Non-Document   04 - WWEF   07 - Pallet | 
**duty_type** | **string** | Populated with valid duty types for international transactions only.      Valid Duty Types: \&quot;01\&quot;,\&quot;02\&quot;,\&quot;03\&quot;,\&quot;04\&quot;,\&quot;05\&quot;,\&quot;06\&quot;,\&quot;07\&quot;,\&quot;08\&quot;,\&quot;09\&quot;   01 - Dutiable   02 - Non Dutiable   03 - Low Value   04 - Courier Remission   05 - Gift   06 - Military   07 - Exception   08 - Line Release   09 - Low Value | [optional] 
**residential_indicator** | **string** | residential Indicator that was sent in on the request.     Valid values: \&quot;01\&quot;,\&quot;02\&quot;     01 - Residential   02 - Commercial | 
**destination_country_name** | **string** | Destination country name value | 
**destination_country_code** | **string** | Destination country code, conforms to ISO-defined country codes. | 
**destination_postal_code** | **string** | The shipment destination postal code.  Required for US domestic requests.     Either 5, or 9-digit US zip codes must be used for U.S. addresses.  For non-US addresses, this is recommended for all countries that utilize postal codes. | [optional] 
**destination_postal_code_low** | **string** | The shipment destination postal code low range.  Value may or may not differ from destinationPostalCode.      Either 5, or 9-digit US zip codes must be used for U.S. addresses.  For non-US addresses, this is recommended for all countries that utilize postal codes. | [optional] 
**destination_postal_code_high** | **string** | The shipment destination postal code high range.  Value may or may not differ from destinationPostalCode.      Either 5, or 9-digit US zip codes must be used for U.S. addresses.  For non-US addresses, this is recommended for all countries that utilize postal codes. | [optional] 
**destination_state_province** | **string** | The shipment destination state or province.     For U.S. addresses, the value will be a valid 2-Character value (per U.S. Mail Standards).     For non-U.S. addresses the full State or Province name will be returned. | [optional] 
**destination_city_name** | **string** | The shipment destination city.     Required for International requests for those countries that do not utilize postal codes. | [optional] 
**origin_country_name** | **string** | Origin country name value | 
**origin_country_code** | **string** | Origin country code, conforms to ISO-defined country codes. | 
**origin_postal_code** | **string** | The shipment origin postal code.  Required for US domestic requests.     Either 5, or 9-digit US zip codes must be used for U.S. addresses.  For non-US addresses, this is recommended for all countries that utilize postal codes. | [optional] 
**origin_postal_code_low** | **string** | The shipment origin postal code low range.  Value may or may not differ from destinationPostalCode.      Either 5, or 9-digit US zip codes must be used for U.S. addresses.  For non-US addresses, this is recommended for all countries that utilize postal codes. | [optional] 
**origin_postal_code_high** | **string** | The shipment origin postal code high range.  Value may or may not differ from destinationPostalCode.      Either 5, or 9-digit US zip codes must be used for U.S. addresses.  For non-US addresses, this is recommended for all countries that utilize postal codes. | [optional] 
**origin_state_province** | **string** | The shipment origin state or province.     For U.S. addresses, the value will be a valid 2-Character value (per U.S. Mail Standards).     For non-U.S. addresses the full State or Province name will be returned. | [optional] 
**origin_city_name** | **string** | The shipment origin city.     Required for International requests for those countries that do not utilize postal codes. | [optional] 
**weight** | **string** | Shipment weight.  Value is only required for international shipment.      Defaults to 0.0 | [optional] 
**weight_unit_of_measure** | **string** | Returned on response when weight was present on the request. | [optional] 
**shipment_contents_value** | **string** | Shipment contents value. Value is only required for international shipment.     Defaults to 0.0 | [optional] 
**shipment_contents_currency_code** | **string** | Returned on response when shipmentContentsValue was present on the request. | [optional] 
**guarantee_suspended** | **bool** | Returns TRUE if the shipment dates fall within a defined peak date range. When the guarantee is suspended, it is suspended for all services in the response.      The logic for determining if guarantees are suspended applies per origin country.     The following will be used to determine if a shipment falls within a defined peak date range: shipDate (from the response), deliveryDate (from the response), server Date.     Defined peak date range (range for when guarantees are suspended) is inclusive of start and end dates. | 
**number_of_services** | **int** | Number of services being returned in the services array. | 
**services** | [**\UPS\TimeInTransit\TimeInTransit\Services[]**](Services.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

