# PackagePackaging

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**code** | **string** | Package types. Values are:  01 &#x3D; UPS Letter  02 &#x3D; Customer Supplied Package  03 &#x3D; Tube  04 &#x3D; PAK  21 &#x3D; UPS Express Box  24 &#x3D; UPS 25KG Box  25 &#x3D; UPS 10KG Box  30 &#x3D; Pallet  2a &#x3D; Small Express Box  2b &#x3D; Medium Express Box  2c &#x3D; Large Express Box  56 &#x3D; Flats  57 &#x3D; Parcels  58 &#x3D; BPM  59 &#x3D; First Class  60 &#x3D; Priority  61 &#x3D; Machineables  62 &#x3D; Irregulars  63 &#x3D; Parcel Post  64 &#x3D; BPM Parcel  65 &#x3D; Media Mail  66 &#x3D; BPM Flat  67 &#x3D; Standard Flat.   Note: Only packaging type code 02 is applicable to Ground Freight Pricing.   Package type 24, or 25 is only allowed for shipment without return service. Packaging type must be valid for all the following: ShipTo country or territory, ShipFrom country or territory, a shipment going from ShipTo country or territory to ShipFrom country or territory, all Accessorials at both the shipment and package level, and the shipment service type. UPS will not accept raw wood pallets and please refer the UPS packaging guidelines for pallets on UPS.com. | 
**description** | **string** | Description of packaging type. Examples are letter, customer supplied, express box. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

