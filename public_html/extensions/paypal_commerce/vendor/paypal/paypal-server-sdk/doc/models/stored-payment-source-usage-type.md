
# Stored Payment Source Usage Type

Indicates if this is a `first` or `subsequent` payment using a stored payment source (also referred to as stored credential or card on file).

## Enumeration

`StoredPaymentSourceUsageType`

## Fields

| Name | Description |
|  --- | --- |
| `FIRST` | Indicates the Initial/First payment with a payment_source that is intended to be stored upon successful processing of the payment. |
| `SUBSEQUENT` | Indicates a payment using a stored payment_source which has been successfully used previously for a payment. |
| `DERIVED` | Indicates that PayPal will derive the value of `FIRST` or `SUBSEQUENT` based on data available to PayPal. |

