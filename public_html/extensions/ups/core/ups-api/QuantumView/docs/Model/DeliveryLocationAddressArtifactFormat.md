# DeliveryLocationAddressArtifactFormat

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**consignee_name** | **string** | Consignee&#x27;s name at the location where package is delivered. | [optional] 
**street_number_low** | **string** | Street number where package is delivered. | [optional] 
**street_prefix** | **string** | Street prefix where package is delivered, e.g. N, SE. | [optional] 
**street_name** | **string** | Street name where package is delivered. | [optional] 
**street_type** | **string** | Street type where package is delivered. | [optional] 
**street_suffix** | **string** | Street suffix where package is delivered, e.g. N, SE. | [optional] 
**building_name** | **string** | Building name where package is delivered. | [optional] 
**address_extended_information** | [**\UPS\QuantumView\QuantumView\AddressArtifactFormatAddressExtendedInformation[]**](AddressArtifactFormatAddressExtendedInformation.md) | Container tag for additional address information where package is delivered.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**political_division3** | **string** | The neighborhood, town, barrio etc. | [optional] 
**political_division2** | **string** | City name where package is delivered. | [optional] 
**political_division1** | **string** | Abbreviated state or province name where package is delivered. | [optional] 
**country_code** | **string** | Abbreviated country or territory name where package is delivered. | [optional] 
**postcode_primary_low** | **string** | Postal Code where package is delivered. Required if the user does not submit the City, Alphanumeric State/Province address combination. | [optional] 
**postcode_extended_low** | **string** | 4 Digit postal code extension where package is delivered. Valid for US only. | [optional] 
**residential_address_indicator** | **string** | Residential address indicator for the location where package is delivered. The presence indicates residential address, the absence indicates a business address. | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

