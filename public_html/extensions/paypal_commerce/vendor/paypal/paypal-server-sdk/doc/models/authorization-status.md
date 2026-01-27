
# Authorization Status

The status for the authorized payment.

## Enumeration

`AuthorizationStatus`

## Fields

| Name | Description |
|  --- | --- |
| `CREATED` | The authorized payment is created. No captured payments have been made for this authorized payment. |
| `CAPTURED` | The authorized payment has one or more captures against it. The sum of these captured payments is greater than the amount of the original authorized payment. |
| `DENIED` | PayPal cannot authorize funds for this authorized payment. |
| `PARTIALLY_CAPTURED` | A captured payment was made for the authorized payment for an amount that is less than the amount of the original authorized payment. |
| `VOIDED` | The authorized payment was voided. No more captured payments can be made against this authorized payment. |
| `PENDING` | The created authorization is in pending state. For more information, see status.details. |

