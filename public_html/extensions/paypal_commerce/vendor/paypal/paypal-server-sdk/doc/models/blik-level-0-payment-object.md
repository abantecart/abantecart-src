
# Blik Level 0 Payment Object

Information used to pay using BLIK level_0 flow.

## Structure

`BlikLevel0PaymentObject`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `authCode` | `string` | Required | The 6-digit code used to authenticate a consumer within BLIK.<br><br>**Constraints**: *Minimum Length*: `6`, *Maximum Length*: `6`, *Pattern*: `^[0-9]{6}$` | getAuthCode(): string | setAuthCode(string authCode): void |

## Example (as JSON)

```json
{
  "auth_code": "auth_code6"
}
```

