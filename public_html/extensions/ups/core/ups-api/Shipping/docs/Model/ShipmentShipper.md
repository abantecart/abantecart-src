# ShipmentShipper

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**name** | **string** | Shippers company name.   For forward Shipment 35 characters are accepted, but only 30 characters will be printed on the label. | 
**attention_name** | **string** | Shippers Attention Name.   For forward Shipment 35 characters are accepted, but only 30 characters will be printed on the label.  Required if destination is international. Required if Invoice and CO International forms are requested and the ShipFrom address is not present. | [optional] 
**company_displayable_name** | **string** | Shipper&#x27;s CompanyDisplayableName.  The CompanyDisplayableName will be displayed in tracking results and notification messages in place of the name associated with the shipper account.  The original shipper account name will be displayed for all Return Services and Import Control Shipments.  This is available for Shipper accounts enabled by UPS and applies to Forward Shipments. | [optional] 
**tax_identification_number** | **string** | Shipper&#x27;s Tax Identification Number.  Conditionally required if EEI form (International forms) is requested and ship From is not mentioned. | [optional] 
**phone** | [**\UPS\Shipping\Shipping\ShipperPhone**](ShipperPhone.md) |  | [optional] 
**shipper_number** | **string** | Shipper&#x27;s six digit alphanumeric account number.  Must be associated with the UserId specified in the AccessRequest XML.   The account must be a valid UPS account number that is active.   For US, PR and CA accounts, the account must be either a daily pickup account, an occasional account, or a customer B.I.N account.   Drop Shipper accounts are valid for return service shipments only if the account is Trade Direct (TD) enabled.   All other accounts must be either a daily pickup account or an occasional account. | 
**fax_number** | **string** | Shipper&#x27;s Fax Number. | [optional] 
**e_mail_address** | **string** | Shipper&#x27;s email address.  Must be associated with the UserId specified in the AccessRequest XML. | [optional] 
**address** | [**\UPS\Shipping\Shipping\ShipperAddress**](ShipperAddress.md) |  | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

