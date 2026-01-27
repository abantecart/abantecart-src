
# Transactions List

The list transactions for a subscription request details.

## Structure

`TransactionsList`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `transactions` | [`?(SubscriptionTransactionDetails[])`](../../doc/models/subscription-transaction-details.md) | Optional | An array of transactions.<br><br>**Constraints**: *Minimum Items*: `0`, *Maximum Items*: `32767` | getTransactions(): ?array | setTransactions(?array transactions): void |
| `totalItems` | `?int` | Optional | The total number of items.<br><br>**Constraints**: `>= 0`, `<= 500000000` | getTotalItems(): ?int | setTotalItems(?int totalItems): void |
| `totalPages` | `?int` | Optional | The total number of pages.<br><br>**Constraints**: `>= 0`, `<= 100000000` | getTotalPages(): ?int | setTotalPages(?int totalPages): void |
| `links` | [`?(LinkDescription[])`](../../doc/models/link-description.md) | Optional | An array of request-related [HATEOAS links](/docs/api/reference/api-responses/#hateoas-links).<br><br>**Constraints**: *Minimum Items*: `1`, *Maximum Items*: `10` | getLinks(): ?array | setLinks(?array links): void |

## Example (as JSON)

```json
{
  "transactions": [
    {
      "status": "PARTIALLY_REFUNDED",
      "id": "id8",
      "amount_with_breakdown": {
        "gross_amount": {
          "currency_code": "currency_code4",
          "value": "value0"
        },
        "total_item_amount": {
          "currency_code": "currency_code8",
          "value": "value4"
        },
        "fee_amount": {
          "currency_code": "currency_code2",
          "value": "value4"
        },
        "shipping_amount": {
          "currency_code": "currency_code0",
          "value": "value6"
        },
        "tax_amount": {
          "currency_code": "currency_code2",
          "value": "value8"
        },
        "net_amount": {
          "currency_code": "currency_code6",
          "value": "value2"
        }
      },
      "payer_name": {
        "prefix": "prefix8",
        "given_name": "given_name2",
        "surname": "surname8",
        "middle_name": "middle_name0",
        "suffix": "suffix0"
      },
      "payer_email": "payer_email6",
      "time": "time8"
    }
  ],
  "total_items": 254,
  "total_pages": 34,
  "links": [
    {
      "href": "href6",
      "rel": "rel0",
      "method": "HEAD"
    },
    {
      "href": "href6",
      "rel": "rel0",
      "method": "HEAD"
    }
  ]
}
```

