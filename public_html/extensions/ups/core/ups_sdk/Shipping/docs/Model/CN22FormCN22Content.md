# CN22FormCN22Content

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**cn22_content_quantity** | **string** | Total number of items associated with this content.  Required if the CN22 form container is present. | 
**cn22_content_description** | **string** | Detailed description of the content.  If the combined MI package and CN22 label is requested, only the first 30 characters will appear on the combined label.  Required if the CN22 form container is present. | 
**cn22_content_weight** | [**\UPS\Shipping\Shipping\CN22ContentCN22ContentWeight**](CN22ContentCN22ContentWeight.md) |  | 
**cn22_content_total_value** | **string** | Total value of the items associated with this content.  Required if the CN22 form container is present. | 
**cn22_content_currency_code** | **string** | Currently only USD is supported.  Required if the CN22 form container is present. | 
**cn22_content_country_of_origin** | **string** | Country or Territory of Origin from where the CN22 contents originated. | [optional] 
**cn22_content_tariff_number** | **string** | The tariff number associated with the CN22 contents. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

