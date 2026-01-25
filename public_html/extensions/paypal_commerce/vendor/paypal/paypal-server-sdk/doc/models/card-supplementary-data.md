
# Card Supplementary Data

Merchants and partners can add Level 2 and 3 data to payments to reduce risk and payment processing costs. For more information about processing payments, see checkout or multiparty checkout.

## Structure

`CardSupplementaryData`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `level2` | [`?Level2CardProcessingData`](../../doc/models/level-2-card-processing-data.md) | Optional | The level 2 card processing data collections. If your merchant account has been configured for Level 2 processing this field will be passed to the processor on your behalf. Please contact your PayPal Technical Account Manager to define level 2 data for your business. | getLevel2(): ?Level2CardProcessingData | setLevel2(?Level2CardProcessingData level2): void |
| `level3` | [`?Level3CardProcessingData`](../../doc/models/level-3-card-processing-data.md) | Optional | The level 3 card processing data collections, If your merchant account has been configured for Level 3 processing this field will be passed to the processor on your behalf. Please contact your PayPal Technical Account Manager to define level 3 data for your business. | getLevel3(): ?Level3CardProcessingData | setLevel3(?Level3CardProcessingData level3): void |

## Example (as JSON)

```json
{
  "level_2": {
    "invoice_id": "invoice_id4",
    "tax_total": {
      "currency_code": "currency_code4",
      "value": "value0"
    }
  },
  "level_3": {
    "shipping_amount": {
      "currency_code": "currency_code0",
      "value": "value6"
    },
    "duty_amount": {
      "currency_code": "currency_code6",
      "value": "value2"
    },
    "discount_amount": {
      "currency_code": "currency_code2",
      "value": "value8"
    },
    "shipping_address": {
      "address_line_1": "address_line_10",
      "address_line_2": "address_line_20",
      "admin_area_2": "admin_area_24",
      "admin_area_1": "admin_area_16",
      "postal_code": "postal_code2",
      "country_code": "country_code0"
    },
    "ships_from_postal_code": "ships_from_postal_code4"
  }
}
```

