
# Item Details

The item details.

## Structure

`ItemDetails`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `itemCode` | `?string` | Optional | An item code that identifies a merchant's goods or service.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `1000`, *Pattern*: `^[a-zA-Z0-9_'\-., ":;\!?]*$` | getItemCode(): ?string | setItemCode(?string itemCode): void |
| `itemName` | `?string` | Optional | The item name.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `200`, *Pattern*: `^[a-zA-Z0-9_'\-., ":;\!?]*$` | getItemName(): ?string | setItemName(?string itemName): void |
| `itemDescription` | `?string` | Optional | The item description.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `2000`, *Pattern*: `^[a-zA-Z0-9_'\-., ":;\!?]*$` | getItemDescription(): ?string | setItemDescription(?string itemDescription): void |
| `itemOptions` | `?string` | Optional | The item options. Describes option choices on the purchase of the item in some detail.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `4000`, *Pattern*: `^[a-zA-Z0-9_'\-., ":;\!?]*$` | getItemOptions(): ?string | setItemOptions(?string itemOptions): void |
| `itemQuantity` | `?string` | Optional | The number of purchased units of goods or a service.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `4000`, *Pattern*: `^[a-zA-Z0-9_'\-., ":;\!?]*$` | getItemQuantity(): ?string | setItemQuantity(?string itemQuantity): void |
| `itemUnitPrice` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getItemUnitPrice(): ?Money | setItemUnitPrice(?Money itemUnitPrice): void |
| `itemAmount` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getItemAmount(): ?Money | setItemAmount(?Money itemAmount): void |
| `discountAmount` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getDiscountAmount(): ?Money | setDiscountAmount(?Money discountAmount): void |
| `adjustmentAmount` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getAdjustmentAmount(): ?Money | setAdjustmentAmount(?Money adjustmentAmount): void |
| `giftWrapAmount` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getGiftWrapAmount(): ?Money | setGiftWrapAmount(?Money giftWrapAmount): void |
| `taxPercentage` | `?string` | Optional | The percentage, as a fixed-point, signed decimal number. For example, define a 19.99% interest rate as `19.99`.<br><br>**Constraints**: *Pattern*: `^((-?[0-9]+)\|(-?([0-9]+)?[.][0-9]+))$` | getTaxPercentage(): ?string | setTaxPercentage(?string taxPercentage): void |
| `taxAmounts` | [`?(TaxAmount[])`](../../doc/models/tax-amount.md) | Optional | An array of tax amounts levied by a government on the purchase of goods or services.<br><br>**Constraints**: *Minimum Items*: `1`, *Maximum Items*: `32767` | getTaxAmounts(): ?array | setTaxAmounts(?array taxAmounts): void |
| `basicShippingAmount` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getBasicShippingAmount(): ?Money | setBasicShippingAmount(?Money basicShippingAmount): void |
| `extraShippingAmount` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getExtraShippingAmount(): ?Money | setExtraShippingAmount(?Money extraShippingAmount): void |
| `handlingAmount` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getHandlingAmount(): ?Money | setHandlingAmount(?Money handlingAmount): void |
| `insuranceAmount` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getInsuranceAmount(): ?Money | setInsuranceAmount(?Money insuranceAmount): void |
| `totalItemAmount` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getTotalItemAmount(): ?Money | setTotalItemAmount(?Money totalItemAmount): void |
| `invoiceNumber` | `?string` | Optional | The invoice number. An alphanumeric string that identifies a billing for a merchant.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `200`, *Pattern*: `^[a-zA-Z0-9_'\-., ":;\!?]*$` | getInvoiceNumber(): ?string | setInvoiceNumber(?string invoiceNumber): void |
| `checkoutOptions` | [`?(CheckoutOption[])`](../../doc/models/checkout-option.md) | Optional | An array of checkout options. Each option has a name and value.<br><br>**Constraints**: *Minimum Items*: `1`, *Maximum Items*: `32767` | getCheckoutOptions(): ?array | setCheckoutOptions(?array checkoutOptions): void |

## Example (as JSON)

```json
{
  "item_code": "item_code4",
  "item_name": "item_name2",
  "item_description": "item_description0",
  "item_options": "item_options4",
  "item_quantity": "item_quantity4"
}
```

