# TimeInTransitRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**origin_country_code** | **string** | The country code of the origin shipment.  Valid Values:  Must conform to the ISO-defined, two-letter country or territory codes.  Refer to Country or Territory Codes in the Appendix above for valid values. | 
**origin_state_province** | **string** | The shipment origin state or province.  For U.S. addresses, the value must be a valid 2-character value (per U.S. Mail standards)   For non-U.S. addresses the full State or Province name should be provided. | [optional] 
**origin_city_name** | **string** | The shipment origin city. Required for International requests for those countries that do not utilize postal codes. | [optional] 
**origin_town_name** | **string** | The shipment origin town. Town is a subdivision of city. | [optional] 
**origin_postal_code** | **string** | Required for Domestic requests.  The shipment origin postal code.   Either the 5, or 9-digit US zip codes must be used for U.S. addresses.  For non-U.S. addresses, this is recommended for all countries that utilize postal codes. | [optional] 
**destination_country_code** | **string** | The country code of the destination.    Valid values:   Must conform to ISO-defined country codes. | [optional] 
**destination_state_province** | **string** | The shipment destination state or province.  For U.S. addresses, the value must be a valid 2-character value (per U.S. Mail standards).    For non-U.S. addresses the full State or Province name should be provided. | [optional] 
**destination_city_name** | **string** | The shipment destination city. Required for International Requests for those countries that do not utilize postal codes. | [optional] 
**destination_town_name** | **string** | The shipment destination town.  Town is a subdivision of city. | [optional] 
**destination_postal_code** | **string** | The shipment destination postal code.    Required for Domestic requests. Either 5, or 9-digit U.S. zip codes must be used for U.S. addresses.  For non-U.S. addresses, this is recommended for all countries that utilize postal codes. | [optional] 
**residential_indicator** | **string** | Indicates if address is residential or commercial. Required for Domestic requests.     Valid values: \&quot;01\&quot;, \&quot;02\&quot;     01 &#x3D; Residential   02 &#x3D; Commercial     Defaults to commercial for International Requests. | [optional] 
**ship_date** | **string** | The date the shipment is tendered to UPS for shipping (can be dropped off at UPS or picked up by UPS).  Allowed range is up to 60 days in future and 35 days in past. This date may or may not be the UPS business date.   Format is YYYY-MM-DD.    YYYY &#x3D; 4 digit year;   MM &#x3D; 2 digit month, valid values 01-12;   DD &#x3D; 2 digit day of month, valid values 01-31   If no value is provided, defaults to current system date. | [optional] 
**ship_time** | **string** | The time the shipment is tendered to UPS for shipping (can be dropped off at UPS or picked up by UPS).    Format is HH:MM:SS    Defaults to current time if not provided. | [optional] 
**weight** | **float** | The weight of the shipment. Required for International requests.     Note: If decimal values are used, valid values will be rounded to the tenths.      Note: Maximum value is 70 kilograms or 150 pounds. | [optional] 
**weight_unit_of_measure** | **string** | Required for International requests and when weight value is provided.     Valid Values: \&quot;LBS\&quot;, \&quot;KGS\&quot; | [optional] 
**shipment_contents_value** | **float** | The monetary value of shipment contents.     Required when origin country does not equal destination country and BillType is 03 (non-documented) or 04 (WWEF)     Required when origin country does not equal destination country, and destination country &#x3D; CA, and BillType &#x3D; 02 (document).     Note: If decimal values are used, valid values will be rounded to the tenths. | [optional] 
**shipment_contents_currency_code** | **string** | The unit of currency used for values. Required if ShipmentContentsValue is populated.   Valid value: must conform to ISO standards. | [optional] 
**bill_type** | **string** | Required for International Requests.   Valid values: \&quot;02\&quot;,\&quot;03\&quot;,\&quot;04\&quot;   02 - Document   03 - Non Document   04 - WWEF (Pallet) | [optional] 
**avv_flag** | **bool** | Used to bypass address validation when the address has already been validated by the calling application.      Valid values: true, false     Defaults to true   Note: not to be exposed to external customers. | [optional] 
**number_of_packages** | **int** | Sets the number of packages in shipment.  Default value is 1. | [optional] 
**drop_off_at_facility_indicator** | **int** | Sets the indicator for an international Freight Pallet shipment that is going to be dropped off by shipper to a UPS facility.  The indicator is used when the Bill Type is \&quot;04\&quot;.      Valid values: \&quot;0\&quot;, \&quot;1\&quot;.     0 &#x3D; WWDTProcessIF.PICKUP_BY_UPS   1 &#x3D; WWDTProcessIf.DROPOFF_BY_SHIPPER     The default value is \&quot;0\&quot; | [optional] 
**hold_for_pickup_indicator** | **int** | Sets the indicator for an international Freight Pallet shipment that is going to be pick-up by consignee in a destination facility.  The indicator is used when the Bill Type is \&quot;04\&quot;.      Valid values: \&quot;0\&quot;, \&quot;1\&quot;.     0 &#x3D; WWDTProcessIF.DELIVERY_BY_UPS   1 &#x3D; WWDTProcessIf.PICKUP_BY_CONSIGNEE     The default value is \&quot;0\&quot; | [optional] 
**return_unfilterd_services** | **bool** | Used to get back a full list of services - bypassing current WWDT business rules to remove services from the list being returned to clients for US domestic that are slower than UPS Ground.      Default value is false. | [optional] 
**max_list** | **int** | Sets the limit for the number of candidates returned in candidate list.      Default value is 200. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

