
# Item

The details for the items to be purchased.

## Structure

`Item`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `name` | `string` | Required | The item name or title.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `127` | getName(): string | setName(string name): void |
| `unitAmount` | [`Money`](../../doc/models/money.md) | Required | The currency and amount for a financial transaction, such as a balance or payment due. | getUnitAmount(): Money | setUnitAmount(Money unitAmount): void |
| `tax` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getTax(): ?Money | setTax(?Money tax): void |
| `quantity` | `string` | Required | The item quantity. Must be a whole number.<br><br>**Constraints**: *Maximum Length*: `10`, *Pattern*: `^[1-9][0-9]{0,9}$` | getQuantity(): string | setQuantity(string quantity): void |
| `description` | `?string` | Optional | The detailed item description.<br><br>**Constraints**: *Maximum Length*: `2048` | getDescription(): ?string | setDescription(?string description): void |
| `sku` | `?string` | Optional | The stock keeping unit (SKU) for the item.<br><br>**Constraints**: *Maximum Length*: `127` | getSku(): ?string | setSku(?string sku): void |
| `url` | `?string` | Optional | The URL to the item being purchased. Visible to buyer and used in buyer experiences.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `2048` | getUrl(): ?string | setUrl(?string url): void |
| `category` | [`?string(ItemCategory)`](../../doc/models/item-category.md) | Optional | The item category type.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `20` | getCategory(): ?string | setCategory(?string category): void |
| `imageUrl` | `?string` | Optional | The URL of the item's image. File type and size restrictions apply. An image that violates these restrictions will not be honored.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `2048`, *Pattern*: `^(https:)([/\|.\|\w\|\s\|-])*\.(?:jpg\|gif\|png\|jpeg\|JPG\|GIF\|PNG\|JPEG)(\?.*)?$` | getImageUrl(): ?string | setImageUrl(?string imageUrl): void |
| `upc` | [`?UniversalProductCode`](../../doc/models/universal-product-code.md) | Optional | The Universal Product Code of the item. | getUpc(): ?UniversalProductCode | setUpc(?UniversalProductCode upc): void |
| `billingPlan` | [`?OrderBillingPlan`](../../doc/models/order-billing-plan.md) | Optional | Metadata for merchant-managed recurring billing plans. Valid only during the saved payment method token or billing agreement creation. | getBillingPlan(): ?OrderBillingPlan | setBillingPlan(?OrderBillingPlan billingPlan): void |

## Example (as JSON)

```json
{
  "name": "name2",
  "unit_amount": {
    "currency_code": "currency_code2",
    "value": "value8"
  },
  "tax": {
    "currency_code": "currency_code0",
    "value": "value6"
  },
  "quantity": "quantity8",
  "description": "description2",
  "sku": "sku8",
  "url": "url6",
  "category": "DIGITAL_GOODS"
}
```

