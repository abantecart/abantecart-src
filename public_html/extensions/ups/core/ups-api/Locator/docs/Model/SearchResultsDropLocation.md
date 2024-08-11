# SearchResultsDropLocation

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**location_id** | **string** | The location ID that corresponds to the UPS location. Do not expose the Location ID. | 
**origin_or_destination** | **string** | OriginOrDestination will returned for FreightWillCallRequestType 1 Postal based and 3 City and/or State based search.   OriginOrDestination will be 01 for origin facilities and 02 for Destination facilities | 
**ivr** | [**\UPS\Locator\Locator\DropLocationIVR**](DropLocationIVR.md) |  | 
**geocode** | [**\UPS\Locator\Locator\DropLocationGeocode**](DropLocationGeocode.md) |  | 
**address_key_format** | [**\UPS\Locator\Locator\DropLocationAddressKeyFormat**](DropLocationAddressKeyFormat.md) |  | 
**phone_number** | **string[]** | The UPS locations Phone number. A phone number of the location will be returned.  10 digits allowed for US, otherwise 1..15 digits allowed.  The phone number will be returned as a string.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | 
**fax_number** | **string** | The UPS location&#x27;s Fax number. A fax number of the location will be returned when available.  10 digits allowed for US, otherwise 1..15 digits allowed. The fax number will be returned as string. | [optional] 
**e_mail_address** | **string** | Email address of the UPS location. Returned when available. | [optional] 
**location_attribute** | [**\UPS\Locator\Locator\DropLocationLocationAttribute[]**](DropLocationLocationAttribute.md) | OptionType is a container that indicates the type of the location attribute.  There are 4 types of attributes.  They are: Location, Retail Location, Additional Services and Program Type.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | 
**distance** | [**\UPS\Locator\Locator\DropLocationDistance**](DropLocationDistance.md) |  | 
**special_instructions** | [**\UPS\Locator\Locator\DropLocationSpecialInstructions[]**](DropLocationSpecialInstructions.md) | Walking directions.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**latest_ground_drop_off_time** | **string[]** | The latest ground time the users can Drop-off the package at the location to be picked up. The time information is based on the time at the UPS location.  When a user specifies a Drop-off Time and Ground as the Service Type, the locations that have latest Drop-off times equal to or later than the specified Drop-off time and service type are returned.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**latest_air_drop_off_time** | **string[]** | The latest airtime the users can Drop-off the package at the location to be picked up. The time information is based on the time at the UPS location.  When a user specifies a Drop-off Time and Air as the Service Type, the locations that have latest Drop-off times equal to or later than the specified Drop-off time and service type are returned.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**additional_charge_indicator** | **string** | Presence or Absence Indicator. If present, Indicates if the UPS location would have an additional charge. ASO locations will require an additional charge. | [optional] 
**standard_hours_of_operation** | **string** | The standard hours of operation of the drop location will be returned when available. The location&#x27;s time may differ because of holidays. | [optional] 
**non_standard_hours_of_operation** | **string** | The non-standard hours of operation of the drop location. The location&#x27;s time may differ because of holidays, weekends, or other factors that are beyond the locations control. Seven days preceding a given holiday the Non Standard Hours Of Operation will be returned along with the standard hours of operation if available. | [optional] 
**will_call_hours_of_operation** | **string** | The will call hours of operation of the drop location will be returned when available. The location&#x27;s time may differ because of holidays. | [optional] 
**number** | **string** | The center number of the drop location if it is The UPS store. | [optional] 
**home_page_url** | **string** | The home page URL of the drop location if it is The UPS store. | [optional] 
**comments** | **string** | Comments returned about the location. Text will be displayed in English or the locale given in the request. If Country Code is FR, and locale passed in the request is \&quot;fr_FR\&quot; then text will be displayed in French language, else comment will be displayed in English language. | [optional] 
**additional_comments** | [**\UPS\Locator\Locator\DropLocationAdditionalComments**](DropLocationAdditionalComments.md) |  | [optional] 
**disclaimer** | **string[]** | Textual disclaimer about the drop location.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**slic** | **string** | SLIC. | [optional] 
**timezone** | **string** | TimeZone. | 
**facility_type** | **string** | PKG/FRT. | [optional] 
**operating_hours** | [**\UPS\Locator\Locator\DropLocationOperatingHours**](DropLocationOperatingHours.md) |  | [optional] 
**localized_instruction** | [**\UPS\Locator\Locator\DropLocationLocalizedInstruction[]**](DropLocationLocalizedInstruction.md) | LocalizedInstruction container. Applicable for SearchOptionCode 01, 02, 03.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**promotion_information** | [**\UPS\Locator\Locator\DropLocationPromotionInformation[]**](DropLocationPromotionInformation.md) | Container to hold any promotion text for the location. Text will be displayed in English or the locale given in the request.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**sort_code** | [**\UPS\Locator\Locator\DropLocationSortCode**](DropLocationSortCode.md) |  | [optional] 
**service_offering_list** | [**\UPS\Locator\Locator\DropLocationServiceOfferingList**](DropLocationServiceOfferingList.md) |  | [optional] 
**display_phone_number_indicator** | **string** | Valid Values:  0-Do not display phone number 1-Display phone number.  This indicator will be returned only for the contact type Telephone number. This indicator is used by the clients to determine whether to display the telephone number to the end user. | [optional] 
**access_point_information** | [**\UPS\Locator\Locator\DropLocationAccessPointInformation**](DropLocationAccessPointInformation.md) |  | [optional] 
**location_image** | [**\UPS\Locator\Locator\DropLocationLocationImage**](DropLocationLocationImage.md) |  | [optional] 
**location_new_indicator** | **string** | Indicator for new location. | [optional] 
**promotional_link_url** | **string** | Promotional link URL for specific location. | [optional] 
**featured_rank** | **string** | Feature Ranking values: Null or blank - Location is not featured.  1 - Featured Location ranked number 1. 2 - Featured Location ranked number 2. | [optional] 
**will_call_location_indicator** | **string** | Will Call Location Indicator values: - Y – Signifies a Will Call location that serves the customers address. - N - Signifies it is not a Will Call location.  Will Call locations are only returned with a \\\&quot;Y\\\&quot; indicator if the request included EnhancedSearchOption code 10. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

