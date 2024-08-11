# Shipment

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**inquiry_number** | **string** | inquiryNumber | [optional] 
**package** | [**\UPS\Tracking\Tracking\Package[]**](Package.md) |  | [optional] 
**user_relation** | **string[]** | The relationship of the user to the package(s) in the shipment. No value means that the user has no relationship to the package. Note that this check is only done when the request contains the &#x27;Username&#x27; and package rights checking is performed. Valid values:&lt;br /&gt;&#x27;MYC_HOME&#x27; - My Choice for Home&lt;br /&gt;&#x27;MYC_BUS_OUTBOUND&#x27; - My Choice for Business Outbound&lt;br /&gt;&#x27;MYC_BUS_INBOUND&#x27; - My Choice for Business Inbound&lt;br /&gt;&#x27;SHIPPER&#x27; - Shipper | [optional] 
**warnings** | [**\UPS\Tracking\Tracking\Warning[]**](Warning.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

