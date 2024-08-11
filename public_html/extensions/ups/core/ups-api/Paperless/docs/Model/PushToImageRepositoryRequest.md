# PushToImageRepositoryRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**request** | [**\UPS\Paperless\Paperless\PushToImageRepositoryRequestRequest**](PushToImageRepositoryRequestRequest.md) |  | 
**shipper_number** | **string** | The Shipper&#x27;s UPS Account Number.  Your UPS Account Number must have &#x27;Upload Forms Created Offline&#x27; enabled to use this webservice. | 
**forms_history_document_id** | [**\UPS\Paperless\Paperless\PushToImageRepositoryRequestFormsHistoryDocumentID**](PushToImageRepositoryRequestFormsHistoryDocumentID.md) |  | 
**forms_group_id** | **string** | FormsGroupID would be required in Push Request if user needs to update uploaded DocumentID(s) in Forms History. | [optional] 
**shipment_identifier** | **string** | Shipment Identifier is required for this request. | 
**shipment_date_and_time** | **string** | The date and time of the processed shipment. Required only for small package shipments. The valid format is yyyy-MM-dd-HH.mm.ss | [optional] 
**shipment_type** | **string** | Valid values are: 1 &#x3D; small package, 2 &#x3D; freight. | 
**prq_confirmation_number** | **string** | PRQ Confirmation being specified by client. Required for freight shipments. | [optional] 
**tracking_number** | **string[]** | UPS Tracking Number associated with this shipment. Required only for small package shipment. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

