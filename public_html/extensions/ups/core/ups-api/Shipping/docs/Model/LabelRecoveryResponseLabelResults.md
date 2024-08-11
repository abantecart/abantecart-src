# LabelRecoveryResponseLabelResults

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**tracking_number** | **string** | Package Tracking number.  Package 1Z number. Returned only if TrackingNumber or Combination of Reference Number and Shipper Number present in request. | [optional] 
**label_image** | [**\UPS\Shipping\Shipping\LabelResultsLabelImage**](LabelResultsLabelImage.md) |  | [optional] 
**mail_innovations_tracking_number** | **string** | Mail Innovations Tracking Number.  Applicable for Single Mail Innovations Returns and Dual Mail Innovations Returns shipment. Returned only if MailInnovationsTrackingNumber is provided in request. | [optional] 
**mail_innovations_label_image** | [**\UPS\Shipping\Shipping\LabelResultsMailInnovationsLabelImage**](LabelResultsMailInnovationsLabelImage.md) |  | [optional] 
**receipt** | [**\UPS\Shipping\Shipping\LabelResultsReceipt**](LabelResultsReceipt.md) |  | [optional] 
**form** | [**\UPS\Shipping\Shipping\LabelResultsForm**](LabelResultsForm.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

