
# Cart Information

The cart information.

## Structure

`CartInformation`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `itemDetails` | [`?(ItemDetails[])`](../../doc/models/item-details.md) | Optional | An array of item details.<br><br>**Constraints**: *Minimum Items*: `1`, *Maximum Items*: `32767` | getItemDetails(): ?array | setItemDetails(?array itemDetails): void |
| `taxInclusive` | `?bool` | Optional | Indicates whether the item amount or the shipping amount already includes tax.<br><br>**Default**: `false` | getTaxInclusive(): ?bool | setTaxInclusive(?bool taxInclusive): void |
| `paypalInvoiceId` | `?string` | Optional | The ID of the invoice. Appears for only PayPal-generated invoices.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `127`, *Pattern*: `^[a-zA-Z0-9_'\-., ":;\!?]*$` | getPaypalInvoiceId(): ?string | setPaypalInvoiceId(?string paypalInvoiceId): void |

## Example (as JSON)

```json
{
  "tax_inclusive": false,
  "item_details": [
    {
      "item_code": "item_code0",
      "item_name": "item_name8",
      "item_description": "item_description4",
      "item_options": "item_options2",
      "item_quantity": "item_quantity2"
    }
  ],
  "paypal_invoice_id": "paypal_invoice_id2"
}
```

