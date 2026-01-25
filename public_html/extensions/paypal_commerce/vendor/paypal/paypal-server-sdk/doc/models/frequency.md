
# Frequency

The frequency of the billing cycle.

## Structure

`Frequency`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `intervalUnit` | [`string(IntervalUnit)`](../../doc/models/interval-unit.md) | Required | The interval at which the subscription is charged or billed.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `24`, *Pattern*: `^[A-Z_]+$` | getIntervalUnit(): string | setIntervalUnit(string intervalUnit): void |
| `intervalCount` | `?int` | Optional | The number of intervals after which a subscriber is billed. For example, if the `interval_unit` is `DAY` with an `interval_count` of  `2`, the subscription is billed once every two days. The following table lists the maximum allowed values for the `interval_count` for each `interval_unit`: Interval unit Maximum interval count DAY 365 WEEK 52 MONTH 12 YEAR 1<br><br>**Default**: `1`<br><br>**Constraints**: `>= 1`, `<= 365` | getIntervalCount(): ?int | setIntervalCount(?int intervalCount): void |

## Example (as JSON)

```json
{
  "interval_unit": "DAY",
  "interval_count": 1
}
```

