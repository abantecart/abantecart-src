
# Eci Flag

Electronic Commerce Indicator (ECI). The ECI value is part of the 2 data elements that indicate the transaction was processed electronically. This should be passed on the authorization transaction to the Gateway/Processor.

## Enumeration

`EciFlag`

## Fields

| Name | Description |
|  --- | --- |
| `MASTERCARD_NON_3D_SECURE_TRANSACTION` | Mastercard non-3-D Secure transaction. |
| `MASTERCARD_ATTEMPTED_AUTHENTICATION_TRANSACTION` | Mastercard attempted authentication transaction. |
| `MASTERCARD_FULLY_AUTHENTICATED_TRANSACTION` | Mastercard fully authenticated transaction. |
| `FULLY_AUTHENTICATED_TRANSACTION` | VISA, AMEX, JCB, DINERS CLUB fully authenticated transaction. |
| `ATTEMPTED_AUTHENTICATION_TRANSACTION` | VISA, AMEX, JCB, DINERS CLUB attempted authentication transaction. |
| `NON_3D_SECURE_TRANSACTION` | VISA, AMEX, JCB, DINERS CLUB non-3-D Secure transaction. |

