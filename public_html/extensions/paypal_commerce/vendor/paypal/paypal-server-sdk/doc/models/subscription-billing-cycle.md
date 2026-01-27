
# Subscription Billing Cycle

The billing cycle details.

## Structure

`SubscriptionBillingCycle`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `pricingScheme` | [`?SubscriptionPricingScheme`](../../doc/models/subscription-pricing-scheme.md) | Optional | The pricing scheme details. | getPricingScheme(): ?SubscriptionPricingScheme | setPricingScheme(?SubscriptionPricingScheme pricingScheme): void |
| `frequency` | [`Frequency`](../../doc/models/frequency.md) | Required | The frequency of the billing cycle. | getFrequency(): Frequency | setFrequency(Frequency frequency): void |
| `tenureType` | [`string(TenureType)`](../../doc/models/tenure-type.md) | Required | The tenure type of the billing cycle. In case of a plan having trial cycle, only 2 trial cycles are allowed per plan.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `24`, *Pattern*: `^[A-Z_]+$` | getTenureType(): string | setTenureType(string tenureType): void |
| `sequence` | `int` | Required | The order in which this cycle is to run among other billing cycles. For example, a trial billing cycle has a `sequence` of `1` while a regular billing cycle has a `sequence` of `2`, so that trial cycle runs before the regular cycle.<br><br>**Constraints**: `>= 1`, `<= 99` | getSequence(): int | setSequence(int sequence): void |
| `totalCycles` | `?int` | Optional | The number of times this billing cycle gets executed. Trial billing cycles can only be executed a finite number of times (value between 1 and 999 for total_cycles). Regular billing cycles can be executed infinite times (value of 0 for total_cycles) or a finite number of times (value between 1 and 999 for total_cycles).<br><br>**Default**: `1`<br><br>**Constraints**: `>= 0`, `<= 999` | getTotalCycles(): ?int | setTotalCycles(?int totalCycles): void |

## Example (as JSON)

```json
{
  "frequency": {
    "interval_unit": "DAY",
    "interval_count": 1
  },
  "tenure_type": "REGULAR",
  "sequence": 30,
  "total_cycles": 1,
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
  }
}
```

