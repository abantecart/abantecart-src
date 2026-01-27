
# Incentive Information

The incentive details.

## Structure

`IncentiveInformation`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `incentiveDetails` | [`?(IncentiveDetails[])`](../../doc/models/incentive-details.md) | Optional | An array of incentive details.<br><br>**Constraints**: *Minimum Items*: `1`, *Maximum Items*: `32767` | getIncentiveDetails(): ?array | setIncentiveDetails(?array incentiveDetails): void |

## Example (as JSON)

```json
{
  "incentive_details": [
    {
      "incentive_type": "incentive_type4",
      "incentive_code": "incentive_code0",
      "incentive_amount": {
        "currency_code": "currency_code4",
        "value": "value0"
      },
      "incentive_program_code": "incentive_program_code4"
    },
    {
      "incentive_type": "incentive_type4",
      "incentive_code": "incentive_code0",
      "incentive_amount": {
        "currency_code": "currency_code4",
        "value": "value0"
      },
      "incentive_program_code": "incentive_program_code4"
    },
    {
      "incentive_type": "incentive_type4",
      "incentive_code": "incentive_code0",
      "incentive_amount": {
        "currency_code": "currency_code4",
        "value": "value0"
      },
      "incentive_program_code": "incentive_program_code4"
    }
  ]
}
```

