# ShipmentServiceOptionsNotification

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**notification_code** | **string** | The type of notification requested.  Note: - QVN Exception notification and return notification are not applicable to GFP. - QV In-transit and Return Notifications are only valid for ImportControl and Return shipment. - QV In-transit Notification is allowed for return shipments only. - QV Ship Notification is allowed for forward moving shipments only.  Valid values: - 5 - QV In-transit Notification - 6 - QV Ship Notification - 7 - QV Exception Notification - 8 - QV Delivery Notification - 2 - Return Notification or Label Creation Notification - 012 - Alternate Delivery Location Notification - 013 - UAP Shipper Notification. | 
**e_mail** | [**\UPS\Shipping\Shipping\NotificationEMail**](NotificationEMail.md) |  | 
**voice_message** | [**\UPS\Shipping\Shipping\NotificationVoiceMessage**](NotificationVoiceMessage.md) |  | [optional] 
**text_message** | [**\UPS\Shipping\Shipping\NotificationTextMessage**](NotificationTextMessage.md) |  | [optional] 
**locale** | [**\UPS\Shipping\Shipping\NotificationLocale**](NotificationLocale.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

