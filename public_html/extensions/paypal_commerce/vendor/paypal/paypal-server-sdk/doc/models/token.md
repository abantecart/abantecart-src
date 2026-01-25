
# Token

The tokenized payment source to fund a payment.

## Structure

`Token`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `id` | `string` | Required | The PayPal-generated ID for the token.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[0-9a-zA-Z_-]+$` | getId(): string | setId(string id): void |
| `type` | [`string(TokenType)`](../../doc/models/token-type.md) | Required | The tokenization method that generated the ID.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[0-9A-Z_-]+$` | getType(): string | setType(string type): void |

## Example (as JSON)

```json
{
  "id": "id6",
  "type": "BILLING_AGREEMENT"
}
```

