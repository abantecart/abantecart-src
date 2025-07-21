# AlertDetailElementLevelInformation

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**level** | **string** | Define type of element in request. Possible values are - - &#x27;H&#x27; for the header details level, - &#x27;S&#x27; for the shipment level, - &#x27;P&#x27; for the package level, - &#x27;C&#x27; for the commodity level. | 
**element_identifier** | [**\UPS\Rating\Rating\ElementLevelInformationElementIdentifier[]**](ElementLevelInformationElementIdentifier.md) | Contains more information about the type of element. Returned if Level is &#x27;P&#x27; or &#x27;C&#x27;.  **NOTE:** For versions &gt;&#x3D; v2403, this element will always be returned as an array. For requests using versions &lt; v2403, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

