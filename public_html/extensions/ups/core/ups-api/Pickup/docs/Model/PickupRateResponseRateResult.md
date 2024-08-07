# PickupRateResponseRateResult

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**disclaimer** | [**\UPS\Pickup\Pickup\RateResultDisclaimer**](RateResultDisclaimer.md) |  | [optional] 
**rate_type** | **string** | Indicates the pickup is rated as same-day or future-day pickup. - SD &#x3D; Same-day Pickup - FD &#x3D; Future-day Pickup | [optional] 
**currency_code** | **string** | IATA currency codes for the pickup charge. Such as USD | 
**charge_detail** | [**\UPS\Pickup\Pickup\RateResultChargeDetail**](RateResultChargeDetail.md) |  | [optional] 
**tax_charges** | [**\UPS\Pickup\Pickup\RateResultTaxCharges**](RateResultTaxCharges.md) |  | [optional] 
**total_tax** | **string** | The sum of all taxes. | [optional] 
**grand_total_of_all_charge** | **string** | The grand total of each charge and applied tax. | [optional] 
**grand_total_of_all_incented_charge** | **string** | The grand total of each incented charge and applied tax. Only present if 1. UserLevelDiscountIndicator &#x3D; Y and User Level Promotion is applied to the pickup or 2 .if any incentive rate is applied to the pickup and SubVersion on the request is greater than or equal to 1707. | [optional] 
**pre_tax_total_charge** | **string** | Total of charges before taxes. Only present when tax details requested in input. | [optional] 
**pre_tax_total_incented_charge** | **string** | Total of incented charges before taxes. Only present if 1. UserLevelDiscountIndicator &#x3D; Y and User Level Promotion is applied to the pickup or 2 .if any incentive rate is applied to the pickup and SubVersion on the request is greater than or equal to 1707. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

