# ShipmentResultsForm

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**code** | **string** | Code that indicates the type of form.  Valid values: 01 - All Requested International Forms. | 
**description** | **string** | Description that indicates the type of form. Possible Values. All Requested International Forms. | 
**image** | [**\UPS\Shipping\Shipping\ShipmentResultsFormImage**](ShipmentResultsFormImage.md) |  | [optional] 
**form_group_id** | **string** | Unique Id for later retrieval of saved version of the completed international forms. Always returned when code &#x3D; 01. 01 represents international forms. | [optional] 
**form_group_id_name** | **string** | Contains description text which identifies the group of International forms. This element is part of both request and response. This element does not appear on the forms. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

