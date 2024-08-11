# ShipmentChargeBillShipper

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**account_number** | **string** | UPS account number.  Must be the same UPS account number as the one provided in Shipper/ShipperNumber.   Either this element or one of the sibling elements CreditCard or AlternatePaymentMethod must be provided, but all of them may not be provided. | [optional] 
**credit_card** | [**\UPS\Shipping\Shipping\BillShipperCreditCard**](BillShipperCreditCard.md) |  | [optional] 
**alternate_payment_method** | **string** | Alternate Payment Method.  Valid value: 01&#x3D; PayPal  Only valid for forward shipments. It is not valid for Return or Import Control shipments.   This element or one of the sibling elements CreditCard or AccountNumber must be provided, but all of them may not be provided.   PayPal 01: Is only valid for forward shipments. It is not valid for Return or Import Control shipments.   This element or one of the sibling elements CreditCard or AccountNumber must be provided, but all of them may not be provided. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

