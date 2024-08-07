# ExceptionUpdatedAddress

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**consignee_name** | **string** | Consignee&#x27;s name for package shipping address. It will be returned if there is any update due to exception. | [optional] 
**street_number_low** | **string** | Street number of updated shipping address. It will be returned if there is any update due to exception. | [optional] 
**street_prefix** | **string** | Street prefix of updated shipping address, e.g. N, SE. It will be returned if there is any update due to exception. | [optional] 
**street_name** | **string** | Street name of updated shipping address. It will be returned if there is any update due to exception. | [optional] 
**street_type** | **string** | Street type of updated shipping address, e.g. ST. It will be returned if there is any update due to exception. | [optional] 
**street_suffix** | **string** | Street suffix of updated shipping address, e.g. N, SE. It will be returned if there is any update due to exception. | [optional] 
**address_extended_information** | [**\UPS\QuantumView\QuantumView\UpdatedAddressAddressExtendedInformation[]**](UpdatedAddressAddressExtendedInformation.md) | Container for information about updated shipping address. It will be returned if there is any update due to exception.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**political_division3** | **string** | The neighborhood, town, barrio etc. It will be returned if there is any update due to exception. | [optional] 
**political_division2** | **string** | City name of updated shipping address. It will be returned if there is any update due to exception. | [optional] 
**political_division1** | **string** | Abbreviated state or province name of updated shipping address. It will be returned if there is any update due to exception. | [optional] 
**country_code** | **string** | Abbreviated country or territory name of updated shipping address. It will be returned if there is any update due to exception. | [optional] 
**postcode_primary_low** | **string** | Postal Code of updated shipping address. It will be returned if there is any update due to exception. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

