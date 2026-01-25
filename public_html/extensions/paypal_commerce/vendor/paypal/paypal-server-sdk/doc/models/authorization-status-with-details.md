
# Authorization Status With Details

The status fields and status details for an authorized payment.

## Structure

`AuthorizationStatusWithDetails`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `status` | [`?string(AuthorizationStatus)`](../../doc/models/authorization-status.md) | Optional | The status for the authorized payment. | getStatus(): ?string | setStatus(?string status): void |
| `statusDetails` | [`?AuthorizationStatusDetails`](../../doc/models/authorization-status-details.md) | Optional | The details of the authorized payment status. | getStatusDetails(): ?AuthorizationStatusDetails | setStatusDetails(?AuthorizationStatusDetails statusDetails): void |

## Example (as JSON)

```json
{
  "status": "VOIDED",
  "status_details": {
    "reason": "PENDING_REVIEW"
  }
}
```

