
# Pricing Tier

The pricing tier details.

## Structure

`PricingTier`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `startingQuantity` | `string` | Required | The starting quantity for the tier.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `32`, *Pattern*: `^([0-9]+\|([0-9]+)?[.][0-9]+)$` | getStartingQuantity(): string | setStartingQuantity(string startingQuantity): void |
| `endingQuantity` | `?string` | Optional | The ending quantity for the tier. Optional for the last tier.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `32`, *Pattern*: `^([0-9]+\|([0-9]+)?[.][0-9]+)$` | getEndingQuantity(): ?string | setEndingQuantity(?string endingQuantity): void |
| `amount` | [`Money`](../../doc/models/money.md) | Required | The currency and amount for a financial transaction, such as a balance or payment due. | getAmount(): Money | setAmount(Money amount): void |

## Example (as JSON)

```json
{
  "starting_quantity": "starting_quantity4",
  "ending_quantity": "ending_quantity6",
  "amount": {
    "currency_code": "currency_code6",
    "value": "value0"
  }
}
```

