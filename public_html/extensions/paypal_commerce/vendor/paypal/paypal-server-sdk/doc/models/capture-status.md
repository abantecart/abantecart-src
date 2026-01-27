
# Capture Status

The status of the captured payment.

## Enumeration

`CaptureStatus`

## Fields

| Name | Description |
|  --- | --- |
| `COMPLETED` | The funds for this captured payment were credited to the payee's PayPal account. |
| `DECLINED` | The funds could not be captured. |
| `PARTIALLY_REFUNDED` | An amount less than this captured payment's amount was partially refunded to the payer. |
| `PENDING` | The funds for this captured payment was not yet credited to the payee's PayPal account. For more information, see status.details. |
| `REFUNDED` | An amount greater than or equal to this captured payment's amount was refunded to the payer. |
| `FAILED` | There was an error while capturing payment. |

