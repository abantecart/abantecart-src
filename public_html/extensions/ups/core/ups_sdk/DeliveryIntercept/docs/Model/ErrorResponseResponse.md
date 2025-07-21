# ErrorResponseResponse

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**transaction_id** | [****](.md) | A unique value that will be used to identify the transaction for logging and troubleshooting purposes. | 
**status_code** | [****](.md) | API response status code, Internal code regarding the success or failure of the operation | 
**sub_status_code** | [****](.md) | A new status code for adding granularity to the existing status code structure | [optional] 
**success** | [****](.md) | Indicates if the transaction is considered successful. | [optional] 
**errors** | [****](.md) | Will only be returned if the HTTP statusCode isn&#x27;t &#x27;200&#x27;(success). A list of one or more validation errors. On the API version v3 the first element of the errors array contains the statusCode and scoring statusMessage field. This array is unbounded. | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

