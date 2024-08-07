# ShipmentResultsFRSShipmentData

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**transportation_charges** | [**\UPS\Shipping\Shipping\FRSShipmentDataTransportationCharges**](FRSShipmentDataTransportationCharges.md) |  | 
**freight_density_rate** | [**\UPS\Shipping\Shipping\FRSShipmentDataFreightDensityRate**](FRSShipmentDataFreightDensityRate.md) |  | [optional] 
**handling_units** | [**\UPS\Shipping\Shipping\FRSShipmentDataHandlingUnits[]**](FRSShipmentDataHandlingUnits.md) | Handling Unit for Density based rating container.  **NOTE:** For versions &gt;&#x3D; v2403, this element will always be returned as an array. For requests using versions &lt; v2403, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

