
# Plan

The merchant level Recurring Billing plan metadata for the Billing Agreement.

## Structure

`Plan`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `billingCycles` | [`BillingCycle[]`](../../doc/models/billing-cycle.md) | Required | An array of billing cycles for trial billing and regular billing. A plan can have at most two trial cycles and only one regular cycle.<br><br>**Constraints**: *Minimum Items*: `1`, *Maximum Items*: `3` | getBillingCycles(): array | setBillingCycles(array billingCycles): void |
| `oneTimeCharges` | [`OneTimeCharge`](../../doc/models/one-time-charge.md) | Required | The one-time charge info at the time of checkout. | getOneTimeCharges(): OneTimeCharge | setOneTimeCharges(OneTimeCharge oneTimeCharges): void |
| `name` | `?string` | Optional | Name of the recurring plan.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `127`, *Pattern*: `^[A-Za-z0-9() +',.:-]+$` | getName(): ?string | setName(?string name): void |

## Example (as JSON)

```json
{
  "billing_cycles": [
    {
      "tenure_type": "REGULAR",
      "total_cycles": 1,
      "sequence": 1,
      "pricing_scheme": {
        "price": {
          "currency_code": "currency_code8",
          "value": "value4"
        },
        "pricing_model": "AUTO_RELOAD",
        "reload_threshold_amount": {
          "currency_code": "currency_code0",
          "value": "value6"
        }
      },
      "start_date": "start_date6"
    }
  ],
  "one_time_charges": {
    "setup_fee": {
      "currency_code": "currency_code8",
      "value": "value4"
    },
    "shipping_amount": {
      "currency_code": "currency_code0",
      "value": "value6"
    },
    "taxes": {
      "currency_code": "currency_code6",
      "value": "value2"
    },
    "product_price": {
      "currency_code": "currency_code6",
      "value": "value2"
    },
    "subtotal": {
      "currency_code": "currency_code2",
      "value": "value8"
    },
    "total_amount": {
      "currency_code": "currency_code2",
      "value": "value8"
    }
  },
  "name": "name8"
}
```

