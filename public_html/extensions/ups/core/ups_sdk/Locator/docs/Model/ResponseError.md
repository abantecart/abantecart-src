# ResponseError

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**error_severity** | **string** | Describes the severity of the error.  For additional information, refer to Locator Error Codes in the Appendix. | 
**error_code** | **string** | A numeric value that describes the error. Each tool defines a range of error codes.  For additional information, refer to Locator Error Codes in the Appendix. | 
**error_description** | **string** | Describes the error code. | [optional] 
**minimum_retry_seconds** | **string** | Number of seconds to wait until retry.   This field is populated on special conditions of the Transient Error only, as defined by the service.  A number between 1 and 86400 (24 hours) | [optional] 
**error_location** | [**\UPS\Locator\Locator\ErrorErrorLocation[]**](ErrorErrorLocation.md) | Identifies the element in error.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**error_digest** | **string[]** | The contents of the element in error.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

