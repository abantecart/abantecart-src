
# Payment Collection

The collection of payments, or transactions, for a purchase unit in an order. For example, authorized payments, captured payments, and refunds.

## Structure

`PaymentCollection`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `authorizations` | [`?(AuthorizationWithAdditionalData[])`](../../doc/models/authorization-with-additional-data.md) | Optional | An array of authorized payments for a purchase unit. A purchase unit can have zero or more authorized payments. | getAuthorizations(): ?array | setAuthorizations(?array authorizations): void |
| `captures` | [`?(OrdersCapture[])`](../../doc/models/orders-capture.md) | Optional | An array of captured payments for a purchase unit. A purchase unit can have zero or more captured payments. | getCaptures(): ?array | setCaptures(?array captures): void |
| `refunds` | [`?(Refund[])`](../../doc/models/refund.md) | Optional | An array of refunds for a purchase unit. A purchase unit can have zero or more refunds. | getRefunds(): ?array | setRefunds(?array refunds): void |

## Example (as JSON)

```json
{
  "authorizations": [
    {
      "status": "DENIED",
      "status_details": {
        "reason": "PENDING_REVIEW"
      },
      "id": "id2",
      "amount": {
        "currency_code": "currency_code6",
        "value": "value0"
      },
      "invoice_id": "invoice_id2"
    },
    {
      "status": "DENIED",
      "status_details": {
        "reason": "PENDING_REVIEW"
      },
      "id": "id2",
      "amount": {
        "currency_code": "currency_code6",
        "value": "value0"
      },
      "invoice_id": "invoice_id2"
    },
    {
      "status": "DENIED",
      "status_details": {
        "reason": "PENDING_REVIEW"
      },
      "id": "id2",
      "amount": {
        "currency_code": "currency_code6",
        "value": "value0"
      },
      "invoice_id": "invoice_id2"
    }
  ],
  "captures": [
    {
      "status": "REFUNDED",
      "status_details": {
        "reason": "VERIFICATION_REQUIRED"
      },
      "id": "id4",
      "amount": {
        "currency_code": "currency_code6",
        "value": "value0"
      },
      "invoice_id": "invoice_id4"
    },
    {
      "status": "REFUNDED",
      "status_details": {
        "reason": "VERIFICATION_REQUIRED"
      },
      "id": "id4",
      "amount": {
        "currency_code": "currency_code6",
        "value": "value0"
      },
      "invoice_id": "invoice_id4"
    }
  ],
  "refunds": [
    {
      "status": "CANCELLED",
      "status_details": {
        "reason": "ECHECK"
      },
      "id": "id8",
      "amount": {
        "currency_code": "currency_code6",
        "value": "value0"
      },
      "invoice_id": "invoice_id8"
    },
    {
      "status": "CANCELLED",
      "status_details": {
        "reason": "ECHECK"
      },
      "id": "id8",
      "amount": {
        "currency_code": "currency_code6",
        "value": "value0"
      },
      "invoice_id": "invoice_id8"
    }
  ]
}
```

