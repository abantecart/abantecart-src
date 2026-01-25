
# Cancel Subscription Request

The cancel subscription request details.

## Structure

`CancelSubscriptionRequest`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `reason` | `string` | Required | The reason for the cancellation of a subscription.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `128`, *Pattern*: `^.*$` | getReason(): string | setReason(string reason): void |

## Example (as JSON)

```json
{
  "reason": "reason8"
}
```

