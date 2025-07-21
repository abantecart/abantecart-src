# ChemicalReferenceDataRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**request** | [**\UPS\DangerousGoods\DangerousGoods\ChemicalReferenceDataRequestRequest**](ChemicalReferenceDataRequestRequest.md) |  | 
**id_number** | **string** | This is the ID number (UN/NA/ID) for the specified commodity. UN/NA/ID Identification Number assigned to the specified regulated good. (Include the UN/NA/ID as part of the entry).  At least one of the information - IDNumber or ProperShippingName should be provided to retrieve Chemical Reference Data. | [optional] 
**proper_shipping_name** | **string** | The Proper Shipping Name assigned by ADR, CFR or IATA.   At least one of the information - IDNumber or ProperShippingName should be provided to retrieve Chemical Reference Data. | [optional] 
**shipper_number** | **string** | Shipper&#x27;s six digit account number.  Your UPS Account Number must have correct Dangerous goods contract to successfully use this Webservice. | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

