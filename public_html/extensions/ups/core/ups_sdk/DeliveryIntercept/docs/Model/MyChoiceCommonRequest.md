# MyChoiceCommonRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**client_id** | [****](.md) | A unique value that identifies the client application. | [optional] 
**transaction_id** | [****](.md) | A unique value that will be used to identify the transaction for logging and troubleshooting purposes | [optional] 
**source** | [****](.md) | A unique value that identifies where the request originated from. | [optional] 
**loc** | [****](.md) | The locale of the client application. This must be set to ensure that translations on the response are in the proper language. | [optional] 
**address_token** | [****](.md) | wherever tracking number is required, or when a method defines if required. | [optional] 
**client_ip** | [****](.md) | The IP of the remote client | 
**tracking_number** | [****](.md) | The number being tracked. | [optional] 
**consignee_address_country_code** | [****](.md) |  | [optional] 
**consignee_address_zip_code** | [****](.md) |  | [optional] 
**info_notice_number** | [****](.md) | Infonotice number or BCDN number | [optional] 
**notification_request_type** | [****](.md) |  | [optional] 
**request_type** | [****](.md) | The request type of the intercept being performed. Each method defines if required.  | VALUE | DESCRIPTION                     | | :--:  | :--                             | | WC   | WILL_CALL                       | | SC   | SAME_DAY_WILL_CALL              | | FD   | FUTURE_DELIVERY                 | | AA   | ALTERNATE_ADDRESS               | | UR   | UPS_RETAIL_LOCATION             | | RA   | REDELIVER_TO_MY_ADDRESS         | | RS   | RETURN_TO_SENDER                | | AC   | ADDRESS_CORRECTION              | | UGR   | UPGRADE_TO_GROUND               | | UFD   | UPGRADE_AND_FUTURE_DELIVERY     | | UWC   | UPGRADE_AND_WILL_CALL           | | UUR   | UPGRADE_AND_UPS_RETAIL_LOCATION | | UAA   | UPGRADE_AND_ALTERNATE_ADDRESS   | | XUR   | DELIVER_TO_UAP_NEXT_DAY         | | [optional] 
**requester_contact_info** | [**\UPS\DeliveryIntercept\DeliveryIntercept\ContactInfo**](ContactInfo.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

