# AddressBookEntry

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**ims_addr_identifier** | [****](.md) | The IMS Entry Number of the address, required when retrieving or updating an IMS address book entry. Required when specifying an existing address book entry to be used when processing a request. | [optional] 
**address_nick_name** | [****](.md) | The nickname string assigned to the address entry. Required when creating or updating an IMS address book entry. | [optional] 
**address_info** | [**\UPS\DeliveryIntercept\DeliveryIntercept\AddressInformation**](AddressInformation.md) | Address object, include line1, line2, line 3 state, postal code and country code. Required when processing a request if imsAddressIdentifier is not present. | [optional] 
**name_or_company_name** | [****](.md) | The name of the company or person customer address.(Required When calling submitAddress() API  Empty String  can be passed) | [optional] 
**contact_name** | [****](.md) | The contact name at the customer address. | [optional] 
**phone_number** | [****](.md) | The contact phone number at the customer address.(Required When calling submitAddress() API  Empty String  can be passed) | [optional] 
**phone_ext** | [****](.md) | The phone extension number at the customer address.(Required When calling submitAddress() API  Empty String  can be passed) | [optional] 
**email_address** | [****](.md) | Email Address at the customer address. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

