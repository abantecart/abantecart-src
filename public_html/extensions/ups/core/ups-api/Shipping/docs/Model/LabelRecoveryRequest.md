# LabelRecoveryRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**request** | [**\UPS\Shipping\Shipping\LabelRecoveryRequestRequest**](LabelRecoveryRequestRequest.md) |  | 
**label_specification** | [**\UPS\Shipping\Shipping\LabelRecoveryRequestLabelSpecification**](LabelRecoveryRequestLabelSpecification.md) |  | [optional] 
**translate** | [**\UPS\Shipping\Shipping\LabelRecoveryRequestTranslate**](LabelRecoveryRequestTranslate.md) |  | [optional] 
**label_delivery** | [**\UPS\Shipping\Shipping\LabelRecoveryRequestLabelDelivery**](LabelRecoveryRequestLabelDelivery.md) |  | [optional] 
**tracking_number** | **string** | Small Package Tracking Number. Required if Mail Innovations Tracking Number or ReferenceNumber/Value and ShipperNumber is not provided.  If only TrackingNumber is provided, the request will be treated as Small Package Shipment. Label Recovery will return label for Small Package Tracking Number. If both, TrackingNumber and MailInnovationsTrackingNumber are provided, the request will be treated as Dual Mail Innovations Return Shipment. Label Recovery will return two labels one each for - Small Package Tracking Number and Mail Innovations Return Tracking Number. | [optional] 
**mail_innovations_tracking_number** | **string** | Mail Innovations Tracking Number.  Required if Tracking Number or ReferenceNumber/Value is not populated.  If only MailInnovationsTrackingNumber is provided, the request will be treated as Single Mail Innovations Return Shipment. Label Recovery will return label for Mail Innovations Return Tracking Number. If both, TrackingNumber and MailInnovationsTrackingNumber are provided, the request will be treated as Dual Mail Innovations Return Shipment. Label Recovery will return two labels one each for - Small Package Tracking Number and Mail Innovations Return Tracking Number. | [optional] 
**reference_values** | [**\UPS\Shipping\Shipping\LabelRecoveryRequestReferenceValues**](LabelRecoveryRequestReferenceValues.md) |  | [optional] 
**locale** | **string** | Represents 5 character ISO Locale that allows the user to request Reference Number Code on Label, Label instructions, Receipt instructions (if available for given tracking number) and High Value Report (if available for given tracking number) in desired language.  Locale is specified by the combination of language code and country or territory code - 2 character language code and 2 character country code seperated by an underscore (&#x27;_&#x27;) character. Example - de_DE. Please refer to Appendix for supported values for Locale.  Either Translate container or Locale element can be present in a given request. Both can&#x27;t be requested together in same request. | [optional] 
**ups_premium_care_form** | [**\UPS\Shipping\Shipping\LabelRecoveryRequestUPSPremiumCareForm**](LabelRecoveryRequestUPSPremiumCareForm.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

