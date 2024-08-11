# NotificationEMail

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**e_mail_address** | **string[]** | Email address where the notification is sent.  Up to five email addresses are allowed for each type of Quantum View TM shipment notification. Up to two email address for return notification. | 
**undeliverable_e_mail_address** | **string** | The address where an undeliverable eMail message is sent if the eMail with the notification is undeliverable.  There can be only one UndeliverableEMailAddress for each shipment with Quantum View Shipment Notifications. | [optional] 
**from_e_mail_address** | **string** | The e-mail address specifies the Reply To E-mail address. The \&quot;From\&quot; field of the message header contains pkginfo@ups.com.  Valid for Return Notification only. | [optional] 
**from_name** | **string** | The name the email will appear to be from. Defaults to the Shipper Name.  The FromName must occur only once for each shipment with Quantum View Shipment Notifications. | [optional] 
**memo** | **string** | User defined text that will be included in the eMail.  The Memo must occur only once for each shipment with Quantum View Shipment Notifications. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

