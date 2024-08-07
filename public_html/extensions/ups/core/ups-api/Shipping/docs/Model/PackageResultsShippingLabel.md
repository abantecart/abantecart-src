# PackageResultsShippingLabel

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**image_format** | [**\UPS\Shipping\Shipping\ShippingLabelImageFormat**](ShippingLabelImageFormat.md) |  | 
**graphic_image** | **string** | Base 64 encoded graphic image. | 
**graphic_image_part** | **string[]** | Base 64 encoded graphic image. Applicable only for Mail Innovations CN22 Combination Forward Label with more than 3 commodities.  **NOTE:** For versions &gt;&#x3D; v2403, this element will always be returned as an array. For requests using versions &lt; v2403, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**international_signature_graphic_image** | **string** | Base 64 encoded graphic image of the Warsaw text and signature box. EPL2, ZPL and SPL labels. The image will be returned for non-US based shipments. One image will be given per shipment and it will be in the first PackageResults container. | [optional] 
**html_image** | **string** | Base 64 encoded html browser image rendering software. This is only returned for gif and png image formats. | [optional] 
**pdf417** | **string** | PDF-417 is a two-dimensional barcode, which can store up to about 1,800 printable ASCII characters or 1,100 binary characters per symbol. The symbol is rectangular. The image is Base 64 encoded and returned if the LabelImageFormat code is GIF. Shipment with PRL return service only. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

