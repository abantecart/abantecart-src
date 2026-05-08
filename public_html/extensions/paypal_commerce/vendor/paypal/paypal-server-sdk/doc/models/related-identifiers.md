
# Related Identifiers

Identifiers related to a specific resource.

## Structure

`RelatedIdentifiers`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `orderId` | `?string` | Optional | Order ID related to the resource.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `20`, *Pattern*: `^[A-Z0-9]+$` | getOrderId(): ?string | setOrderId(?string orderId): void |
| `authorizationId` | `?string` | Optional | Authorization ID related to the resource.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `20`, *Pattern*: `^[A-Z0-9]+$` | getAuthorizationId(): ?string | setAuthorizationId(?string authorizationId): void |
| `captureId` | `?string` | Optional | Capture ID related to the resource.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `20`, *Pattern*: `^[A-Z0-9]+$` | getCaptureId(): ?string | setCaptureId(?string captureId): void |

## Example (as JSON)

```json
{
  "order_id": "order_id0",
  "authorization_id": "authorization_id8",
  "capture_id": "capture_id8"
}
```

