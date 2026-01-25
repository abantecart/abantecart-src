
# Incentive Details

The incentive details.

## Structure

`IncentiveDetails`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `incentiveType` | `?string` | Optional | The type of incentive, such as a special offer or coupon.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `500`, *Pattern*: `^[a-zA-Z0-9_'\-., ":;\!?]*$` | getIncentiveType(): ?string | setIncentiveType(?string incentiveType): void |
| `incentiveCode` | `?string` | Optional | The code that identifies an incentive, such as a coupon.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `200`, *Pattern*: `^[a-zA-Z0-9_'\-., ":;\!?]*$` | getIncentiveCode(): ?string | setIncentiveCode(?string incentiveCode): void |
| `incentiveAmount` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getIncentiveAmount(): ?Money | setIncentiveAmount(?Money incentiveAmount): void |
| `incentiveProgramCode` | `?string` | Optional | The incentive program code that identifies a merchant loyalty or incentive program.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `100`, *Pattern*: `^[a-zA-Z0-9_'\-., ":;\!?]*$` | getIncentiveProgramCode(): ?string | setIncentiveProgramCode(?string incentiveProgramCode): void |

## Example (as JSON)

```json
{
  "incentive_type": "incentive_type4",
  "incentive_code": "incentive_code0",
  "incentive_amount": {
    "currency_code": "currency_code4",
    "value": "value0"
  },
  "incentive_program_code": "incentive_program_code4"
}
```

