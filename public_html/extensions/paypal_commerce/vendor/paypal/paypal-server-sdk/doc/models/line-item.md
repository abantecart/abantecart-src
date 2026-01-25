
# Line Item

The line items for this purchase. If your merchant account has been configured for Level 3 processing this field will be passed to the processor on your behalf.

## Structure

`LineItem`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `name` | `string` | Required | The item name or title.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `127` | getName(): string | setName(string name): void |
| `quantity` | `string` | Required | The item quantity. Must be a whole number.<br><br>**Constraints**: *Maximum Length*: `10`, *Pattern*: `^[1-9][0-9]{0,9}$` | getQuantity(): string | setQuantity(string quantity): void |
| `description` | `?string` | Optional | The detailed item description.<br><br>**Constraints**: *Maximum Length*: `2048` | getDescription(): ?string | setDescription(?string description): void |
| `sku` | `?string` | Optional | The stock keeping unit (SKU) for the item.<br><br>**Constraints**: *Maximum Length*: `127` | getSku(): ?string | setSku(?string sku): void |
| `url` | `?string` | Optional | The URL to the item being purchased. Visible to buyer and used in buyer experiences.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `2048` | getUrl(): ?string | setUrl(?string url): void |
| `imageUrl` | `?string` | Optional | The URL of the item's image. File type and size restrictions apply. An image that violates these restrictions will not be honored.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `2048`, *Pattern*: `^(https:)([/\|.\|\w\|\s\|-])*\.(?:jpg\|gif\|png\|jpeg\|JPG\|GIF\|PNG\|JPEG)(\?.*)?$` | getImageUrl(): ?string | setImageUrl(?string imageUrl): void |
| `upc` | [`?UniversalProductCode`](../../doc/models/universal-product-code.md) | Optional | The Universal Product Code of the item. | getUpc(): ?UniversalProductCode | setUpc(?UniversalProductCode upc): void |
| `billingPlan` | [`?OrderBillingPlan`](../../doc/models/order-billing-plan.md) | Optional | Metadata for merchant-managed recurring billing plans. Valid only during the saved payment method token or billing agreement creation. | getBillingPlan(): ?OrderBillingPlan | setBillingPlan(?OrderBillingPlan billingPlan): void |
| `unitAmount` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getUnitAmount(): ?Money | setUnitAmount(?Money unitAmount): void |
| `tax` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getTax(): ?Money | setTax(?Money tax): void |
| `commodityCode` | `?string` | Optional | Code used to classify items purchased and track the total amount spent across various categories of products and services. Different corporate purchasing organizations may use different standards, but the United Nations Standard Products and Services Code (UNSPSC) is frequently used.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `12`, *Pattern*: `^[a-zA-Z0-9_'.-]*$` | getCommodityCode(): ?string | setCommodityCode(?string commodityCode): void |
| `discountAmount` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getDiscountAmount(): ?Money | setDiscountAmount(?Money discountAmount): void |
| `totalAmount` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getTotalAmount(): ?Money | setTotalAmount(?Money totalAmount): void |
| `unitOfMeasure` | `?string` | Optional | Unit of measure is a standard used to express the magnitude of a quantity in international trade. Most commonly used (but not limited to) examples are: Acre (ACR), Ampere (AMP), Centigram (CGM), Centimetre (CMT), Cubic inch (INQ), Cubic metre (MTQ), Fluid ounce (OZA), Foot (FOT), Hour (HUR), Item (ITM), Kilogram (KGM), Kilometre (KMT), Kilowatt (KWT), Liquid gallon (GLL), Liter (LTR), Pounds (LBS), Square foot (FTK).<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `12`, *Pattern*: `^[a-zA-Z0-9_'.-]*$` | getUnitOfMeasure(): ?string | setUnitOfMeasure(?string unitOfMeasure): void |

## Example (as JSON)

```json
{
  "name": "name8",
  "quantity": "quantity4",
  "description": "description8",
  "sku": "sku6",
  "url": "url2",
  "image_url": "image_url4",
  "upc": {
    "type": "UPC-B",
    "code": "code0"
  }
}
```

