# InternationalFormsEEIFilingOption

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**code** | **string** | Required for EEI Form.  Applicable for EEI form. Valid values:  1 - Shipper filed, 2 - AES Direct,  3 - UPS filed. | 
**e_mail_address** | **string** | Email Address where the notification is sent. Valid for UPS filed (option 3), Shipper filed (option 1- A , 1-C)  Applicable for EEI form. | [optional] 
**description** | **string** | Optional Description of Filing Code.  Applicable for EEI form. | [optional] 
**ups_filed** | [**\UPS\Shipping\Shipping\EEIFilingOptionUPSFiled**](EEIFilingOptionUPSFiled.md) |  | [optional] 
**shipper_filed** | [**\UPS\Shipping\Shipping\EEIFilingOptionShipperFiled**](EEIFilingOptionShipperFiled.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

