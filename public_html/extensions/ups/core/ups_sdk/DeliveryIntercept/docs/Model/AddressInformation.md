# AddressInformation

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**address_line1** | [****](.md) | Line 1 of the street address. Required if streetAddressParsedIndicator is FALSE; Required when setting Billing Address. | [optional] 
**address_line2** | [****](.md) | Line 2 of the street address | [optional] 
**address_line3** | [****](.md) | Line 3 of the street | [optional] 
**city** | [****](.md) | The city name of the address | 
**state** | [****](.md) | The state code of the address | [optional] 
**postal_code** | [****](.md) | The postal code of the address (should be provided for postal countries) | [optional] 
**country_code** | [****](.md) | The ISO country code of the address | 
**building_floor** | [****](.md) |  | [optional] 
**public_location_id** | [****](.md) |  | [optional] 
**first_name** | [****](.md) | Required when the user choses to enter a new card providing the Billing address in CreditCardInformation . | [optional] 
**last_name** | [****](.md) | Required when the user choses to enter a new card providing the Billing address in CreditCardInformation object. | [optional] 
**full_name** | [****](.md) | Full person name | [optional] 
**phone_number** | [****](.md) |  | [optional] 
**activity_slic_number** | [****](.md) | When provided, the Will Call center lookup will be done using this identifier. The lookup will only be done using the postalCode when this field is blank. | [optional] 
**street_address_parsed_indicator** | [****](.md) | Set it to true when clients send in parsed street address for redirects. | [optional] 
**parsed_street_address** | [**\UPS\DeliveryIntercept\DeliveryIntercept\AddressInformationParsedStreetAddress**](AddressInformationParsedStreetAddress.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

