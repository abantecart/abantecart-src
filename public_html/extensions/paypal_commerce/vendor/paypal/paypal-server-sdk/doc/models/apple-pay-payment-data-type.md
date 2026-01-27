
# Apple Pay Payment Data Type

Indicates the type of payment data passed, in case of Non China the payment data is 3DSECURE and for China it is EMV.

## Enumeration

`ApplePayPaymentDataType`

## Fields

| Name | Description |
|  --- | --- |
| `ENUM_3DSECURE` | The card was authenticated using 3D Secure (3DS) authentication scheme. While using this value make sure to populate cryptogram and eci_indicator as part of payment data.. |
| `EMV` | The card was authenticated using EMV method, which is applicable for China. While using this value make sure to pass emv_data and pin as part of payment data. |

