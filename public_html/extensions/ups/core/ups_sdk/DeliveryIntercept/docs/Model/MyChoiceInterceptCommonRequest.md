# MyChoiceInterceptCommonRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**special_instructions** | [****](.md) | Special Instructions sent to the driver. | [optional] 
**original_public_location_id** | [****](.md) | The original UAP public location ID where the package is currently located or in-transit to. Format- UXXXXXXXX  (&#x27;U&#x27; followed by 8 numbers) | [optional] 
**charge_info** | [****](.md) | A list containing the charges for each tracking number in the request. This array is unbounded. | [optional] 
**udi_contact_info** | [**\UPS\DeliveryIntercept\DeliveryIntercept\ContactInfo**](ContactInfo.md) |  | [optional] 
**iobb** | [****](.md) | iOvations Black Box data. Used for fraud checking/scoring.   | S.NO. | DESCRIPTION                                 | | :--:  | :--                                         | | 1    | iobb value is labeled “EMPTY”               | | 2.   | tmid is populated with the Threat Metrix ID | | [optional] 
**tmx_session_id** | [****](.md) | Threat Matrix session ID. Used for fraud checking/scoring. | [optional] 
**payment_container** | [**\UPS\DeliveryIntercept\DeliveryIntercept\PaymentContainer**](PaymentContainer.md) |  | [optional] 
**eb_pld_sequence_number** | [****](.md) | Sequence number for the rackingNumber within the shipment. Used to update SII shipper history. | [optional] 
**eb_pl_dshipment_key** | [****](.md) |  | [optional] 
**lead_tracking_number** | [****](.md) | Tracking Number for the lead package in the shipment. Used to update SII shipper history. | [optional] 
**multi_tracking_numbers** | [****](.md) | This unbounded array is required only for the API version v3. It is used to process multiple tracking numbers with the same request. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

