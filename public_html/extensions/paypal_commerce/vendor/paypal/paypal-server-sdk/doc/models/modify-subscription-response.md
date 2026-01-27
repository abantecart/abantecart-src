
# Modify Subscription Response

The response to a request to update the quantity of the product or service in a subscription. You can also use this method to switch the plan and update the `shipping_amount` and `shipping_address` values for the subscription. This type of update requires the buyer's consent.

## Structure

`ModifySubscriptionResponse`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `planId` | `?string` | Optional | The unique PayPal-generated ID for the plan.<br><br>**Constraints**: *Minimum Length*: `26`, *Maximum Length*: `26`, *Pattern*: `^P-[A-Z0-9]*$` | getPlanId(): ?string | setPlanId(?string planId): void |
| `quantity` | `?string` | Optional | The quantity of the product or service in the subscription.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `32`, *Pattern*: `^([0-9]+\|([0-9]+)?[.][0-9]+)$` | getQuantity(): ?string | setQuantity(?string quantity): void |
| `shippingAmount` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getShippingAmount(): ?Money | setShippingAmount(?Money shippingAmount): void |
| `shippingAddress` | [`?ShippingDetails`](../../doc/models/shipping-details.md) | Optional | The shipping details. | getShippingAddress(): ?ShippingDetails | setShippingAddress(?ShippingDetails shippingAddress): void |
| `plan` | [`?PlanOverride`](../../doc/models/plan-override.md) | Optional | An inline plan object to customise the subscription. You can override plan level default attributes by providing customised values for the subscription in this object. | getPlan(): ?PlanOverride | setPlan(?PlanOverride plan): void |
| `planOverridden` | `?bool` | Optional | Indicates whether the subscription has overridden any plan attributes. | getPlanOverridden(): ?bool | setPlanOverridden(?bool planOverridden): void |
| `links` | [`?(LinkDescription[])`](../../doc/models/link-description.md) | Optional | An array of request-related [HATEOAS links](/docs/api/reference/api-responses/#hateoas-links). | getLinks(): ?array | setLinks(?array links): void |

## Example (as JSON)

```json
{
  "plan_id": "plan_id6",
  "quantity": "quantity0",
  "shipping_amount": {
    "currency_code": "currency_code0",
    "value": "value6"
  },
  "shipping_address": {
    "name": {
      "full_name": "full_name6"
    },
    "email_address": "email_address8",
    "phone_number": {
      "country_code": "country_code2",
      "national_number": "national_number6"
    },
    "type": "PICKUP_IN_STORE",
    "options": [
      {
        "id": "id2",
        "label": "label2",
        "type": "SHIPPING",
        "amount": {
          "currency_code": "currency_code6",
          "value": "value0"
        },
        "selected": false
      }
    ]
  },
  "plan": {
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
    },
    "taxes": {
      "percentage": "percentage8",
      "inclusive": false
    }
  }
}
```

