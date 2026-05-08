
# Payment Token Request Payment Source

The payment method to vault with the instrument details.

## Structure

`PaymentTokenRequestPaymentSource`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `card` | [`?PaymentTokenRequestCard`](../../doc/models/payment-token-request-card.md) | Optional | A Resource representing a request to vault a Card. | getCard(): ?PaymentTokenRequestCard | setCard(?PaymentTokenRequestCard card): void |
| `token` | [`?VaultTokenRequest`](../../doc/models/vault-token-request.md) | Optional | The Tokenized Payment Source representing a Request to Vault a Token. | getToken(): ?VaultTokenRequest | setToken(?VaultTokenRequest token): void |

## Example (as JSON)

```json
{
  "card": {
    "name": "name6",
    "number": "number6",
    "expiry": "expiry4",
    "security_code": "security_code8",
    "brand": "CB_NATIONALE"
  },
  "token": {
    "id": "id6",
    "type": "SETUP_TOKEN"
  }
}
```

