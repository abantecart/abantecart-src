
# Failed Payment Details

The details for the failed payment of the subscription.

## Structure

`FailedPaymentDetails`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `amount` | [`Money`](../../doc/models/money.md) | Required | The currency and amount for a financial transaction, such as a balance or payment due. | getAmount(): Money | setAmount(Money amount): void |
| `time` | `string` | Required | The date and time, in [Internet date and time format](https://tools.ietf.org/html/rfc3339#section-5.6). Seconds are required while fractional seconds are optional. Note: The regular expression provides guidance but does not reject all invalid dates.<br><br>**Constraints**: *Minimum Length*: `20`, *Maximum Length*: `64`, *Pattern*: `^[0-9]{4}-(0[1-9]\|1[0-2])-(0[1-9]\|[1-2][0-9]\|3[0-1])[T,t]([0-1][0-9]\|2[0-3]):[0-5][0-9]:([0-5][0-9]\|60)([.][0-9]+)?([Zz]\|[+-][0-9]{2}:[0-9]{2})$` | getTime(): string | setTime(string time): void |
| `reasonCode` | [`?string(ReasonCode)`](../../doc/models/reason-code.md) | Optional | The reason code for the payment failure.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `120`, *Pattern*: `^[A-Z_]+$` | getReasonCode(): ?string | setReasonCode(?string reasonCode): void |
| `nextPaymentRetryTime` | `?string` | Optional | The date and time, in [Internet date and time format](https://tools.ietf.org/html/rfc3339#section-5.6). Seconds are required while fractional seconds are optional. Note: The regular expression provides guidance but does not reject all invalid dates.<br><br>**Constraints**: *Minimum Length*: `20`, *Maximum Length*: `64`, *Pattern*: `^[0-9]{4}-(0[1-9]\|1[0-2])-(0[1-9]\|[1-2][0-9]\|3[0-1])[T,t]([0-1][0-9]\|2[0-3]):[0-5][0-9]:([0-5][0-9]\|60)([.][0-9]+)?([Zz]\|[+-][0-9]{2}:[0-9]{2})$` | getNextPaymentRetryTime(): ?string | setNextPaymentRetryTime(?string nextPaymentRetryTime): void |

## Example (as JSON)

```json
{
  "amount": {
    "currency_code": "currency_code6",
    "value": "value0"
  },
  "time": "time6",
  "reason_code": "TRANSACTION_RECEIVING_LIMIT_EXCEEDED",
  "next_payment_retry_time": "next_payment_retry_time6"
}
```

