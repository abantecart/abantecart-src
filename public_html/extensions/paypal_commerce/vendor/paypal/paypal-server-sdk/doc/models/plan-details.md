
# Plan Details

The plan details.

## Structure

`PlanDetails`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `productId` | `?string` | Optional | The ID for the product.<br><br>**Constraints**: *Minimum Length*: `22`, *Maximum Length*: `22`, *Pattern*: `^PROD-[A-Z0-9]*$` | getProductId(): ?string | setProductId(?string productId): void |
| `name` | `?string` | Optional | The plan name.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `127`, *Pattern*: `^.*$` | getName(): ?string | setName(?string name): void |
| `description` | `?string` | Optional | The detailed description of the plan.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `127`, *Pattern*: `^.*$` | getDescription(): ?string | setDescription(?string description): void |
| `billingCycles` | [`?(SubscriptionBillingCycle[])`](../../doc/models/subscription-billing-cycle.md) | Optional | An array of billing cycles for trial billing and regular billing. A plan can have at most two trial cycles and only one regular cycle.<br><br>**Constraints**: *Minimum Items*: `1`, *Maximum Items*: `12` | getBillingCycles(): ?array | setBillingCycles(?array billingCycles): void |
| `paymentPreferences` | [`?PaymentPreferences`](../../doc/models/payment-preferences.md) | Optional | The payment preferences for a subscription. | getPaymentPreferences(): ?PaymentPreferences | setPaymentPreferences(?PaymentPreferences paymentPreferences): void |
| `merchantPreferences` | [`?MerchantPreferences`](../../doc/models/merchant-preferences.md) | Optional | The merchant preferences for a subscription. | getMerchantPreferences(): ?MerchantPreferences | setMerchantPreferences(?MerchantPreferences merchantPreferences): void |
| `taxes` | [`?Taxes`](../../doc/models/taxes.md) | Optional | The tax details. | getTaxes(): ?Taxes | setTaxes(?Taxes taxes): void |
| `quantitySupported` | `?bool` | Optional | Indicates whether you can subscribe to this plan by providing a quantity for the goods or service.<br><br>**Default**: `false` | getQuantitySupported(): ?bool | setQuantitySupported(?bool quantitySupported): void |

## Example (as JSON)

```json
{
  "quantity_supported": false,
  "product_id": "product_id6",
  "name": "name8",
  "description": "description2",
  "billing_cycles": [
    {
      "pricing_scheme": {
        "version": 10,
        "fixed_price": {
          "currency_code": "currency_code4",
          "value": "value0"
        },
        "pricing_model": "VOLUME",
        "tiers": [
          {
            "starting_quantity": "starting_quantity8",
            "ending_quantity": "ending_quantity6",
            "amount": {
              "currency_code": "currency_code6",
              "value": "value0"
            }
          },
          {
            "starting_quantity": "starting_quantity8",
            "ending_quantity": "ending_quantity6",
            "amount": {
              "currency_code": "currency_code6",
              "value": "value0"
            }
          },
          {
            "starting_quantity": "starting_quantity8",
            "ending_quantity": "ending_quantity6",
            "amount": {
              "currency_code": "currency_code6",
              "value": "value0"
            }
          }
        ],
        "create_time": "create_time4"
      },
      "frequency": {
        "interval_unit": "DAY",
        "interval_count": 94
      },
      "tenure_type": "REGULAR",
      "sequence": 8,
      "total_cycles": 198
    },
    {
      "pricing_scheme": {
        "version": 10,
        "fixed_price": {
          "currency_code": "currency_code4",
          "value": "value0"
        },
        "pricing_model": "VOLUME",
        "tiers": [
          {
            "starting_quantity": "starting_quantity8",
            "ending_quantity": "ending_quantity6",
            "amount": {
              "currency_code": "currency_code6",
              "value": "value0"
            }
          },
          {
            "starting_quantity": "starting_quantity8",
            "ending_quantity": "ending_quantity6",
            "amount": {
              "currency_code": "currency_code6",
              "value": "value0"
            }
          },
          {
            "starting_quantity": "starting_quantity8",
            "ending_quantity": "ending_quantity6",
            "amount": {
              "currency_code": "currency_code6",
              "value": "value0"
            }
          }
        ],
        "create_time": "create_time4"
      },
      "frequency": {
        "interval_unit": "DAY",
        "interval_count": 94
      },
      "tenure_type": "REGULAR",
      "sequence": 8,
      "total_cycles": 198
    },
    {
      "pricing_scheme": {
        "version": 10,
        "fixed_price": {
          "currency_code": "currency_code4",
          "value": "value0"
        },
        "pricing_model": "VOLUME",
        "tiers": [
          {
            "starting_quantity": "starting_quantity8",
            "ending_quantity": "ending_quantity6",
            "amount": {
              "currency_code": "currency_code6",
              "value": "value0"
            }
          },
          {
            "starting_quantity": "starting_quantity8",
            "ending_quantity": "ending_quantity6",
            "amount": {
              "currency_code": "currency_code6",
              "value": "value0"
            }
          },
          {
            "starting_quantity": "starting_quantity8",
            "ending_quantity": "ending_quantity6",
            "amount": {
              "currency_code": "currency_code6",
              "value": "value0"
            }
          }
        ],
        "create_time": "create_time4"
      },
      "frequency": {
        "interval_unit": "DAY",
        "interval_count": 94
      },
      "tenure_type": "REGULAR",
      "sequence": 8,
      "total_cycles": 198
    }
  ],
  "payment_preferences": {
    "auto_bill_outstanding": false,
    "setup_fee": {
      "currency_code": "currency_code8",
      "value": "value4"
    },
    "setup_fee_failure_action": "CONTINUE",
    "payment_failure_threshold": 104
  }
}
```

