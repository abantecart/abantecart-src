# ShipToAddress

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**address_line** | **string[]** | Destination street address including name and number (when applicable).  Max Occurrence can be 3. Length is not validated. | 
**city** | **string** | Destination city.  Required if country or territory does not utilize postal codes. Length is not validated. | [optional] 
**state_province_code** | **string** | Destination state code. | [optional] 
**postal_code** | **string** | Destination postal code.  Required if country or territory utilizes postal codes (i.e. US and PR). | [optional] 
**country_code** | **string** | Destination country or territory code. Refer to the Supported Country or Territory Tables located in the Appendix. | 
**residential_address_indicator** | **string** | Residential Address flag. This field is a flag to indicate if the destination is a residential location. True if ResidentialAddressIndicator tag exists; false otherwise. This element does not require a value and if one is entered it will be ignored.  Note: When requesting TimeInTransit information, this indicator must be passed to determine if Three Day Select or Ground shipment is eligible for Saturday Delivery at no charge. If this indicator is not present, address will be considered as commercial. Empty Tag. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

