# RateRequestPickupType

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**code** | **string** | Pickup Type Code.  Valid values: 01 - Daily Pickup (Default - used when an invalid pickup type code is provided)03 - Customer Counter06 - One Time Pickup19 - Letter Center20 - Air Service CenterLength is not validated. When negotiated rates are requested, 07 (onCallAir) will be ignored.Refer to the Rate Types Table in the Appendix for rate type based on Pickup Type and Customer Classification Code. | 
**description** | **string** | Pickup Type Description.  Ignored if provided in the Request. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

