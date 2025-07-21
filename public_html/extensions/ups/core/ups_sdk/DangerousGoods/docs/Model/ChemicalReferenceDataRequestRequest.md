# ChemicalReferenceDataRequestRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**request_option** | **string[]** |  | [optional] 
**sub_version** | **string** | When UPS introduces new elements in the response that are not associated with new request elements, Subversion is used. This ensures backward compatibility.  To get such elements you need to have the right Subversion. The value of the subversion is explained in the Response element Description.  Format: YYMM &#x3D; Year and month of the release.  Example: 1801 &#x3D; 2018 January  Supported values: 1801 | [optional] 
**transaction_reference** | [**\UPS\DangerousGoods\DangerousGoods\RequestTransactionReference**](RequestTransactionReference.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

