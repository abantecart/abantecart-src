# PickupGetServiceCenterFacilitiesRequestDestinationAddress

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**city** | **string** | Indicates the address of the consignee to allow for the nearest Pickup facility Search.  Required for non-postal country Ireland (IE). | [optional] 
**state_province** | **string** | Indicates the address of the consignee to allow for the nearest Pickup facility Search. 1 &#x3D; District code for Hong Kong (HK) 2 &#x3D; County for Ireland (IE) 3 &#x3D; State or province for all the postal countries  Required for non-postal countries including HK and IE. | [optional] 
**postal_code** | **string** | Indicates the address of the consignee to allow for the nearest Pickup facility Search  It does not apply to non-postal countries. Example: IE and HK. | [optional] 
**country_code** | **string** | The pickup country or territory code as defined by ISO-3166. Please check check separate pickup country or territory list to find out all the pickup eligible countries. | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

