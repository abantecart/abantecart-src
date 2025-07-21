# Milestones

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**category** | **string** | The milestone category. This will be present only when a milestone is in a COMPLETE state. | [optional] 
**code** | **string** | The milestone code. | [optional] 
**current** | **bool** | The indication if the milestone represents the current state of the package. Valid values: &#x27;true&#x27; this milestone is the current state of the package.  &#x27;false&#x27; this milestone is not current. | [optional] 
**description** | **string** | The milestone description. Note: this is not translated at this time and is returned in US English. | [optional] 
**linked_activity** | **string** | The 0-based index of the activity that triggered this milestone. This will be returned only when a milestone is in a COMPLETE state. For example the most recent activity on the response is index 0. | [optional] 
**state** | **string** | The milestone state. Valid values: &#x27;This milestone has already occurred&#x27;/&#x27;This milestone has not yet been completed&#x27;. | [optional] 
**sub_milestone** | [**\UPS\Tracking\Tracking\SubMilestone**](SubMilestone.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

