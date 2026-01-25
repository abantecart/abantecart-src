
# Shipping Option

The options that the payee or merchant offers to the payer to ship or pick up their items.

## Structure

`ShippingOption`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `id` | `string` | Required | A unique ID that identifies a payer-selected shipping option.<br><br>**Constraints**: *Maximum Length*: `127` | getId(): string | setId(string id): void |
| `label` | `string` | Required | A description that the payer sees, which helps them choose an appropriate shipping option. For example, `Free Shipping`, `USPS Priority Shipping`, `Expédition prioritaire USPS`, or `USPS yōuxiān fā huò`. Localize this description to the payer's locale.<br><br>**Constraints**: *Maximum Length*: `127` | getLabel(): string | setLabel(string label): void |
| `type` | [`?string(ShippingType)`](../../doc/models/shipping-type.md) | Optional | A classification for the method of purchase fulfillment. | getType(): ?string | setType(?string type): void |
| `amount` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getAmount(): ?Money | setAmount(?Money amount): void |
| `selected` | `bool` | Required | If the API request sets `selected = true`, it represents the shipping option that the payee or merchant expects to be pre-selected for the payer when they first view the `shipping.options` in the PayPal Checkout experience. As part of the response if a `shipping.option` contains `selected=true`, it represents the shipping option that the payer selected during the course of checkout with PayPal. Only one `shipping.option` can be set to `selected=true`. | getSelected(): bool | setSelected(bool selected): void |

## Example (as JSON)

```json
{
  "id": "id4",
  "label": "label4",
  "type": "SHIPPING",
  "amount": {
    "currency_code": "currency_code6",
    "value": "value0"
  },
  "selected": false
}
```

