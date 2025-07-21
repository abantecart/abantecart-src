# RequestShipmentItems

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**commodity_id** | **string** | Commodity ID is used to associate tariffs with product in the output. Should be unique for each commodity in a request. It is an arbitrary string provided by the user of the API that will be returned with the Landed Cost Quote to indicate which commodity the tariffs apply to. | 
**gross_weight** | **float** | Specifies the gross weight of the commodity as any non-negative value. | [optional] 
**gross_weight_unit** | **string** | Specifies the units of the gross weight. Required if GrossWeight is used. If GrossWeight is not specified, this value must not be set to anything but null. Supported values: LB, KG | [optional] 
**price_each** | **float** | Specifies the price for each commodity unit in the settlement currency. The total price of the entire number of shipmentItems may not exceed 999999999999.99 | 
**commodity_currency_code** | **string** | Specifies the Currency Code used for commodity price. All commodities must have the same currency code. | 
**quantity** | **int** | Specifies the number of product units to be shipped. The total price of the entire number of shipmentItems may not exceed 999999999999.99, 1 or greater than 1 | 
**uom** | **string** | Specifies unit of measure. Check UOM List in the Appendices section. | 
**hs_code** | **string** | Specifies a valid HS or HTS code for the shipment&#x27;s destination or import country. This field is required if description is not provided. | [optional] 
**description** | **string** | This field is populated with description of the commodity. This field is required if hsCode is not provided. | [optional] 
**origin_country_code** | **string** | Country of Manufacture or origin. | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

