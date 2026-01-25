
# Balances Response

The balances response information.

## Structure

`BalancesResponse`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `balances` | [`?(BalanceInformation[])`](../../doc/models/balance-information.md) | Optional | An array of balance detail objects.<br><br>**Constraints**: *Minimum Items*: `0`, *Maximum Items*: `200` | getBalances(): ?array | setBalances(?array balances): void |
| `accountId` | `?string` | Optional | The PayPal payer ID, which is a masked version of the PayPal account number intended for use with third parties. The account number is reversibly encrypted and a proprietary variant of Base32 is used to encode the result.<br><br>**Constraints**: *Minimum Length*: `13`, *Maximum Length*: `13`, *Pattern*: `^[2-9A-HJ-NP-Z]{13}$` | getAccountId(): ?string | setAccountId(?string accountId): void |
| `asOfTime` | `?string` | Optional | The date and time, in [Internet date and time format](https://tools.ietf.org/html/rfc3339#section-5.6). Seconds are required while fractional seconds are optional. Note: The regular expression provides guidance but does not reject all invalid dates.<br><br>**Constraints**: *Minimum Length*: `20`, *Maximum Length*: `64`, *Pattern*: `^[0-9]{4}-(0[1-9]\|1[0-2])-(0[1-9]\|[1-2][0-9]\|3[0-1])[T,t]([0-1][0-9]\|2[0-3]):[0-5][0-9]:([0-5][0-9]\|60)([.][0-9]+)?([Zz]\|[+-][0-9]{2}:[0-9]{2})$` | getAsOfTime(): ?string | setAsOfTime(?string asOfTime): void |
| `lastRefreshTime` | `?string` | Optional | The date and time, in [Internet date and time format](https://tools.ietf.org/html/rfc3339#section-5.6). Seconds are required while fractional seconds are optional. Note: The regular expression provides guidance but does not reject all invalid dates.<br><br>**Constraints**: *Minimum Length*: `20`, *Maximum Length*: `64`, *Pattern*: `^[0-9]{4}-(0[1-9]\|1[0-2])-(0[1-9]\|[1-2][0-9]\|3[0-1])[T,t]([0-1][0-9]\|2[0-3]):[0-5][0-9]:([0-5][0-9]\|60)([.][0-9]+)?([Zz]\|[+-][0-9]{2}:[0-9]{2})$` | getLastRefreshTime(): ?string | setLastRefreshTime(?string lastRefreshTime): void |

## Example (as JSON)

```json
{
  "balances": [
    {
      "currency": "currency0",
      "primary": false,
      "total_balance": {
        "currency_code": "currency_code6",
        "value": "value2"
      },
      "available_balance": {
        "currency_code": "currency_code8",
        "value": "value4"
      },
      "withheld_balance": {
        "currency_code": "currency_code2",
        "value": "value8"
      }
    },
    {
      "currency": "currency0",
      "primary": false,
      "total_balance": {
        "currency_code": "currency_code6",
        "value": "value2"
      },
      "available_balance": {
        "currency_code": "currency_code8",
        "value": "value4"
      },
      "withheld_balance": {
        "currency_code": "currency_code2",
        "value": "value8"
      }
    },
    {
      "currency": "currency0",
      "primary": false,
      "total_balance": {
        "currency_code": "currency_code6",
        "value": "value2"
      },
      "available_balance": {
        "currency_code": "currency_code8",
        "value": "value4"
      },
      "withheld_balance": {
        "currency_code": "currency_code2",
        "value": "value8"
      }
    }
  ],
  "account_id": "account_id0",
  "as_of_time": "as_of_time2",
  "last_refresh_time": "last_refresh_time0"
}
```

