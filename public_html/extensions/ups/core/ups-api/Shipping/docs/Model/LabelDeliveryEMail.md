# LabelDeliveryEMail

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**e_mail_address** | **string** | The destination eMail address for the Label Delivery. | 
**undeliverable_e_mail_address** | **string** | The address where an undeliverable email message is sent if the Label Delivery email is undeliverable.  Defaults to FromEMailAddress. | [optional] 
**from_e_mail_address** | **string** | The e-mail address specifies the Reply To E-mail address. The \&quot;From\&quot; field of the message header contains pkginfo@ups.com. | [optional] 
**from_name** | **string** | The \&quot;FrontName\&quot; is the name from which notification will appear. Defaults to the \&quot;Shipper Name\&quot;. | [optional] 
**memo** | **string** | User defined text that will be included in the email. | [optional] 
**subject** | **string** | The eMails subject. Defaults to the Label Delivery Notification plus the shipment ID. Defaults to text provided by UPS. | [optional] 
**subject_code** | **string** | Specifies a reference code and reference number to display in the subject of the Label Delivery notification.  When the subject code is provided, the subject will contain the following: UPS Label Delivery, the reference code (the reference code will be mapped to the corresponding ANSI value) and the reference number.  The valid subject codes are: - 01 - Shipment Reference Number 1, - 02 - Shipment Reference Number 2, - 03 - package Reference Number 1, - 04 - package Reference Number 2, - 05 - package Reference Number 3, - 06 - package Reference Number 4, - 07 - package Reference Number 5, - 08 - Subject Text (Return Notification only).  If the subject code tag is not provided and the subject text is provided, the subject of the notification will be the subject text.  If the subject text is provided, and the subject code tag exists, then the subject code value must be 08. If Subject code is 08, subject text must exist. If a subject code is provided that refers to a nonexistent reference number, the subject will default to the shipment identification number. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

