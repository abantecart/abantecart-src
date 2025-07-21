# MyChoiceCommonResponse

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**transaction_id** | [****](.md) | A unique value that will be used to identify the transaction for logging and troubleshooting purposes. | 
**status_code** | [****](.md) | API response status code, Internal code regarding the success or failure of the operation | 
**status_msg** | [****](.md) | API response status message, Internal message regarding the success or failure of the operation | [optional] 
**sub_status_code** | [****](.md) | A new status code for adding granularity to the existing status code structure | [optional] 
**success** | [****](.md) | Indicates if the transaction is considered successful. | 
**errors** | [****](.md) | A list that contains the errors occurred during the processing of transaction. This array is unbounded. | [optional] 
**warnings** | [****](.md) | A map containing warning codes and descriptions as key/value pairs. | [optional] 
**charge_info** | [****](.md) | For each 1z there will exist a ChargeInfo object. These are the charges that were applied. This array is unbounded. | [optional] 
**mycagreement_upto_date** | [****](.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

