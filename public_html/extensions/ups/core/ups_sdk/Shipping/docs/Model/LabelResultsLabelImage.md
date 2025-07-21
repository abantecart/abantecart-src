# LabelResultsLabelImage

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**label_image_format** | [**\UPS\Shipping\Shipping\LabelImageLabelImageFormat**](LabelImageLabelImageFormat.md) |  | 
**graphic_image** | **string** | Base 64 encoded graphic image. | 
**html_image** | **string** | Base 64 encoded html browser image rendering software. This is only returned for GIF image formats. | [optional] 
**pdf417** | **string** | PDF-417 is a two-dimensional barcode, which can store up to about 1,800 printable ASCII characters or 1,100 binary characters per symbol. The symbol is rectangular.   The PDF417 image will be returned when the shipment is trans-border and the service option is one of the following: Standard Express, Saver Express Plus. The image is Base 64 encoded and only returned for GIF image format. | [optional] 
**international_signature_graphic_image** | **string** | Base 64 encoded graphic image of the Warsaw text and signature box. | [optional] 
**url** | **string** | This is only returned if the label link is requested to be returned and only at the first package result  Applicable for following types of shipments: Print/Electronic Return Label Print/Electronic Import Control Label Forward shipment except for Mail Innovations Forward | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

