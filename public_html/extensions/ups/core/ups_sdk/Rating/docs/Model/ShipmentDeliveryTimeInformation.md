# ShipmentDeliveryTimeInformation

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**package_bill_type** | **string** | Valid values are: - 02 - Document only - 03 - Non-Document - 04 - WWEF Pallet - 07 - Domestic Pallet  If 04 is included, Worldwide Express Freight and UPS Worldwide Express Freight Midday services (if applicable) will be included in the response. | 
**pickup** | [**\UPS\Rating\Rating\DeliveryTimeInformationPickup**](DeliveryTimeInformationPickup.md) |  | [optional] 
**return_contract_services** | [**\UPS\Rating\Rating\DeliveryTimeInformationReturnContractServices[]**](DeliveryTimeInformationReturnContractServices.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

