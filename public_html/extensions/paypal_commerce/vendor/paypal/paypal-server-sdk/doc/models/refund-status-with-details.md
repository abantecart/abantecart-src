
# Refund Status With Details

The refund status with details.

## Structure

`RefundStatusWithDetails`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `status` | [`?string(RefundStatus)`](../../doc/models/refund-status.md) | Optional | The status of the refund. | getStatus(): ?string | setStatus(?string status): void |
| `statusDetails` | [`?RefundStatusDetails`](../../doc/models/refund-status-details.md) | Optional | The details of the refund status. | getStatusDetails(): ?RefundStatusDetails | setStatusDetails(?RefundStatusDetails statusDetails): void |

## Example (as JSON)

```json
{
  "status": "PENDING",
  "status_details": {
    "reason": "ECHECK"
  }
}
```

