# RateResultChargeDetail

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**charge_code** | **string** | Indicates the general charge type - A &#x3D; ACCESSORIAL TYPE - B &#x3D; BASE CHARGE TYPE - S &#x3D; SURCHARGE TYPE | 
**charge_description** | **string** | Description of each charge.The possible descriptions are: - BASE CHARGE - EXTENDED AREA SURCHARGE - FUEL SURCHARGE - REMOTE AREA SURCHARGE - RESIDENTIAL SURCHARGE - SATURDAY ON-CALL STOP CHARGE | [optional] 
**charge_amount** | **string** | Monetary value of the charge. | 
**incented_amount** | **string** | Monetary value of the incented charge. Only present if 1. UserLevelDiscountIndicator &#x3D; Y and User Level Promotion is applied to the pickup or 2 .if any incentive rate is applied to the pickup and SubVersion on the request is greater than or equal to 1707. | [optional] 
**tax_amount** | **string** | Monetary value of the tax if apply. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

