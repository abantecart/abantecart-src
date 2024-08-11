# ContactsProducer

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**option** | **string** | The text associated with the code will be printed in the producer section instead of producer contact information.  Use attached List if more than one producer&#x27;s good is included on the Certificate, attach a list of additional producers, including the legal name, address (including country or territory), and legal tax identification number, cross-referenced to the goods described in the Description of Goods field.  Applies to NAFTA CO.   Valid values:  01 - AVAILABLE TO CUSTOMS UPON REQUEST 02 - SAME AS EXPORTER 03 - ATTACHED LIST 04 - UNKNOWN | [optional] 
**company_name** | **string** | Company Name or the Individual name of the Producer.  Applies to NAFTA CO.  Only applicable when producer option is empty or not present. Conditionally required for: NAFTA CO, when Producer option is not specified. | [optional] 
**tax_identification_number** | **string** | Tax ID of the Producer.  Applies to NAFTA CO. Only applicable when producer option is empty or not present | [optional] 
**address** | [**\UPS\Shipping\Shipping\ProducerAddress**](ProducerAddress.md) |  | [optional] 
**attention_name** | **string** | Contact name at the Producer location.  Applies to NAFTA CO. | [optional] 
**phone** | [**\UPS\Shipping\Shipping\ProducerPhone**](ProducerPhone.md) |  | [optional] 
**e_mail_address** | **string** | Producer email address.  Applies to NAFTA CO. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

