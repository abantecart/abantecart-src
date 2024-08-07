# Package

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**access_point_information** | [**\UPS\Tracking\Tracking\AccessPointInformation**](AccessPointInformation.md) |  | [optional] 
**activity** | [**\UPS\Tracking\Tracking\Activity[]**](Activity.md) |  | [optional] 
**additional_attributes** | **string[]** | The list of additional attributes that may be associated with the package. Presence of any element indicates the package has that attribute. | [optional] 
**additional_services** | **string[]** | The list of additional services that may be associated with the package. Presence of any element indicates that the package has that service. | [optional] 
**alternate_tracking_number** | [**\UPS\Tracking\Tracking\AlternateTrackingNumber[]**](AlternateTrackingNumber.md) |  | [optional] 
**current_status** | [**\UPS\Tracking\Tracking\Status**](Status.md) |  | [optional] 
**delivery_date** | [**\UPS\Tracking\Tracking\DeliveryDate[]**](DeliveryDate.md) |  | [optional] 
**delivery_information** | [**\UPS\Tracking\Tracking\DeliveryInformation**](DeliveryInformation.md) |  | [optional] 
**delivery_time** | [**\UPS\Tracking\Tracking\DeliveryTime**](DeliveryTime.md) |  | [optional] 
**milestones** | [**\UPS\Tracking\Tracking\Milestones[]**](Milestones.md) | milestones | [optional] 
**package_address** | [**\UPS\Tracking\Tracking\PackageAddress[]**](PackageAddress.md) |  | [optional] 
**package_count** | **int** | The total number of packages in the shipment. Note that this number may be greater than the number of returned packages in the response. In such cases subsequent calls are needed to get additional packages. | [optional] 
**payment_information** | [**\UPS\Tracking\Tracking\PaymentInformation[]**](PaymentInformation.md) |  | [optional] 
**reference_number** | [**\UPS\Tracking\Tracking\ReferenceNumber[]**](ReferenceNumber.md) |  | [optional] 
**service** | [**\UPS\Tracking\Tracking\Service**](Service.md) |  | [optional] 
**status_code** | **string** |  | [optional] 
**status_description** | **string** | The activity status description. Note: this field will be translated based on the locale provided in the request. | [optional] 
**suppression_indicators** | **string[]** | Contains values which signify that certain data should be suppressed or hidden. Valid values: Tracking activity details should be hidden. Note: this is mainly intended for use by UPS.com applications. | [optional] 
**tracking_number** | **string** |  | [optional] 
**weight** | [**\UPS\Tracking\Tracking\Weight**](Weight.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

