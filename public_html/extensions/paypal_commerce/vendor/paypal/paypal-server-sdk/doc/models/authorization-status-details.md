
# Authorization Status Details

The details of the authorized payment status.

## Structure

`AuthorizationStatusDetails`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `reason` | [`?string(AuthorizationIncompleteReason)`](../../doc/models/authorization-incomplete-reason.md) | Optional | The reason why the authorized status is `PENDING`.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `64`, *Pattern*: `^[A-Z_]+$` | getReason(): ?string | setReason(?string reason): void |

## Example (as JSON)

```json
{
  "reason": "PENDING_REVIEW"
}
```

