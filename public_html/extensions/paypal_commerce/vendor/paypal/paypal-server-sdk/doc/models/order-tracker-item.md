
# Order Tracker Item

The details of the items in the shipment.

## Structure

`OrderTrackerItem`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `name` | `?string` | Optional | The item name or title.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `127` | getName(): ?string | setName(?string name): void |
| `quantity` | `?string` | Optional | The item quantity. Must be a whole number.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `10`, *Pattern*: `^[1-9][0-9]{0,9}$` | getQuantity(): ?string | setQuantity(?string quantity): void |
| `sku` | `?string` | Optional | The stock keeping unit (SKU) for the item. This can contain unicode characters.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `127` | getSku(): ?string | setSku(?string sku): void |
| `url` | `?string` | Optional | The URL to the item being purchased. Visible to buyer and used in buyer experiences.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `2048` | getUrl(): ?string | setUrl(?string url): void |
| `imageUrl` | `?string` | Optional | The URL of the item's image. File type and size restrictions apply. An image that violates these restrictions will not be honored.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `2048`, *Pattern*: `^(https:)([/\|.\|\w\|\s\|-])*\.(?:jpg\|gif\|png\|jpeg\|JPG\|GIF\|PNG\|JPEG)` | getImageUrl(): ?string | setImageUrl(?string imageUrl): void |
| `upc` | [`?UniversalProductCode`](../../doc/models/universal-product-code.md) | Optional | The Universal Product Code of the item. | getUpc(): ?UniversalProductCode | setUpc(?UniversalProductCode upc): void |

## Example (as JSON)

```json
{
  "name": "name6",
  "quantity": "quantity2",
  "sku": "sku2",
  "url": "url0",
  "image_url": "image_url2"
}
```

