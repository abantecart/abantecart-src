# LandedCostRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**currency_code** | **string** | Specifies the currency of transaction or purchase. | 
**trans_id** | **string** | Unique transaction ID for the request. | 
**allow_partial_landed_cost_result** | **bool** | An optional flag to indicate that partial landed cost calculations are acceptable to be used by upstream systems. When set to *false*, the system will return an error when at least one commodity in the shipment is invalid (all or none), and no results  will be sent back for that request. When set to *true*, the system will return partial calculations when applicable.  Valid values: true &#x3D; Partial Landed Cost result will return. false &#x3D; All or No result will return (default). | [optional] 
**alversion** | **int** | Version number of the instance that processed this request. This must match the major number of the corresponding ICD version. | 
**shipment** | [**\UPS\LandedCost\LandedCost\LandedCostRequestShipment**](LandedCostRequestShipment.md) |  | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

