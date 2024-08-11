# ResponseShipmentItems

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**commodity_id** | **string** | Specifies the commodity ID. | 
**hs_code** | **string** | Specifies the HTS code of the commodity. | 
**commodity_duty** | **float** | Duty amount for this commodity. | 
**total_commodity_tax_and_fee** | **float** | Total tax and other fees for this commodity (excluding commodity duty and VAT). | [optional] 
**commodity_vat** | **float** | VAT amount for this commodity. | 
**total_commodity_duty_and_tax** | **float** | Sum of commodity duty, VAT, tax, and other fees for this commodity. | 
**commodity_currency_code** | **string** | Specifies the currency code used for commodity&#x27;s price. | 
**is_calculable** | **bool** | True/False. Indicates if Landed Cost can successful calculated for this commodity. | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

