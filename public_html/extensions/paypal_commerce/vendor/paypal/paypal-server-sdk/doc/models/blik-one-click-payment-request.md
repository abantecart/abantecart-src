
# Blik One Click Payment Request

Information used to pay using BLIK one-click flow.

## Structure

`BlikOneClickPaymentRequest`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `authCode` | `?string` | Optional | The 6-digit code used to authenticate a consumer within BLIK.<br><br>**Constraints**: *Minimum Length*: `6`, *Maximum Length*: `6`, *Pattern*: `^[0-9]{6}$` | getAuthCode(): ?string | setAuthCode(?string authCode): void |
| `consumerReference` | `string` | Required | The merchant generated, unique reference serving as a primary identifier for accounts connected between Blik and a merchant.<br><br>**Constraints**: *Minimum Length*: `3`, *Maximum Length*: `64`, *Pattern*: `^[ -~]{3,64}$` | getConsumerReference(): string | setConsumerReference(string consumerReference): void |
| `aliasLabel` | `?string` | Optional | A bank defined identifier used as a display name to allow the payer to differentiate between multiple registered bank accounts.<br><br>**Constraints**: *Minimum Length*: `8`, *Maximum Length*: `35`, *Pattern*: `^[ -~]{8,35}$` | getAliasLabel(): ?string | setAliasLabel(?string aliasLabel): void |
| `aliasKey` | `?string` | Optional | A Blik-defined identifier for a specific Blik-enabled bank account that is associated with a given merchant. Used only in conjunction with a Consumer Reference.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `19`, *Pattern*: `^[0-9]+$` | getAliasKey(): ?string | setAliasKey(?string aliasKey): void |

## Example (as JSON)

```json
{
  "auth_code": "auth_code8",
  "consumer_reference": "consumer_reference6",
  "alias_label": "alias_label2",
  "alias_key": "alias_key6"
}
```

