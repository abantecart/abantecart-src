# QuantumViewResponseResponse

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**transaction_reference** | [**\UPS\QuantumView\QuantumView\ResponseTransactionReference**](ResponseTransactionReference.md) |  | 
**response_status_code** | **string** | Identifies the success or failure of the interchange.  1 &#x3D; Success, 0 &#x3D; Failure | 
**response_status_description** | **string** | &#x27;Success&#x27; or &#x27;Failure&#x27; | [optional] 
**error** | [**\UPS\QuantumView\QuantumView\ResponseError[]**](ResponseError.md) | If an error is encountered during the interchange, the Response contains an error. If the error is present, then the ErrorSeverity and ErrorCodes are required.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

