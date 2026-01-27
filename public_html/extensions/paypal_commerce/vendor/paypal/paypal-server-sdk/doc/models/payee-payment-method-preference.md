
# Payee Payment Method Preference

The merchant-preferred payment methods.

## Enumeration

`PayeePaymentMethodPreference`

## Fields

| Name | Description |
|  --- | --- |
| `UNRESTRICTED` | Accepts any type of payment from the customer. |
| `IMMEDIATE_PAYMENT_REQUIRED` | Accepts only immediate payment from the customer. For example, credit card, PayPal balance, or instant ACH. Ensures that at the time of capture, the payment does not have the `pending` status. |

