# RateRequestRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**request_option** | **string** | Used to define the request type. Valid Values: - Rate &#x3D; The server rates (The default Request option is Rate if a Request Option is not provided). - Shop &#x3D; The server validates the shipment, and returns rates for all UPS products from the ShipFrom to the ShipTo addresses. - Ratetimeintransit &#x3D; The server rates with transit time information - Shoptimeintransit &#x3D; The server validates the shipment, and returns rates and transit times for all UPS products from the ShipFrom to the ShipTo addresses.  Rate is the only valid request option for UPS Ground Freight Pricing requests. | 
**sub_version** | **string** | Indicates Rate API to display the new release features in Rate API response based on Rate release. See the What&#x27;s New section for the latest Rate release. Supported values: 1601, 1607, 1701, 1707, 2108, 2205 | [optional] 
**transaction_reference** | [**\UPS\Rating\Rating\RequestTransactionReference**](RequestTransactionReference.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

