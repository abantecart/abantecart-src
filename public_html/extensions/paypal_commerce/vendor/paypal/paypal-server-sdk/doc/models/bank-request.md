
# Bank Request

A Resource representing a request to vault a Bank used for ACH Debit.

## Structure

`BankRequest`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `achDebit` | `mixed` | Optional | A Resource representing a request to vault a ACH Debit. | getAchDebit(): | setAchDebit( achDebit): void |
| `sepaDebit` | [`?SepaDebitRequest`](../../doc/models/sepa-debit-request.md) | Optional | An API resource denoting a request to securely store a SEPA Debit. | getSepaDebit(): ?SepaDebitRequest | setSepaDebit(?SepaDebitRequest sepaDebit): void |

## Example (as JSON)

```json
{
  "ach_debit": {
    "key1": "val1",
    "key2": "val2"
  },
  "sepa_debit": {
    "experience_context": {
      "locale": "locale6",
      "return_url": "return_url4",
      "cancel_url": "cancel_url6"
    }
  }
}
```

