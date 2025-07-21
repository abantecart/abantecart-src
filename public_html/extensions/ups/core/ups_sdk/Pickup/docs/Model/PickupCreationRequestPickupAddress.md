# PickupCreationRequestPickupAddress

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**company_name** | **string** | Company name | 
**contact_name** | **string** | Name of contact person | 
**address_line** | **string[]** | Detailed street address. For Jan. 2010 release, only one AddressLine is allowed | 
**room** | **string** | Room number | [optional] 
**floor** | **string** | Floor number | [optional] 
**city** | **string** | City or equivalent | 
**state_province** | **string** | State or province for postal countries; county for Ireland (IE) and district code for Hong Kong (HK) | [optional] 
**urbanization** | **string** | - Barrio for Mexico (MX) - Urbanization for Puerto Rico (PR) - Shire for United Kingdom (UK) | [optional] 
**postal_code** | **string** | Postal code or equivalent for postal countries | [optional] 
**country_code** | **string** | The pickup country or territory code as defined by ISO-3166.  Refer to Country or Territory Codes in the Appendix for valid values. | 
**residential_indicator** | **string** | Indicates if the pickup address is commercial or residential.  Valid values: Y &#x3D; Residential address N &#x3D; Non-residential (Commercial) address (default) | 
**pickup_point** | **string** | The specific spot to pickup at the address. | [optional] 
**phone** | [**\UPS\Pickup\Pickup\PickupAddressPhone**](PickupAddressPhone.md) |  | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

