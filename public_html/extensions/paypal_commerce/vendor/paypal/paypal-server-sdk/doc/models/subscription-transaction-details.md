
# Subscription Transaction Details

The transaction details.

## Structure

`SubscriptionTransactionDetails`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `status` | [`?string(CaptureStatus)`](../../doc/models/capture-status.md) | Optional | The status of the captured payment. | getStatus(): ?string | setStatus(?string status): void |
| `id` | `string` | Required | The PayPal-generated transaction ID.<br><br>**Constraints**: *Minimum Length*: `3`, *Maximum Length*: `50` | getId(): string | setId(string id): void |
| `amountWithBreakdown` | [`SubscriptionAmountWithBreakdown`](../../doc/models/subscription-amount-with-breakdown.md) | Required | The breakdown details for the amount. Includes the gross, tax, fee, and shipping amounts. | getAmountWithBreakdown(): SubscriptionAmountWithBreakdown | setAmountWithBreakdown(SubscriptionAmountWithBreakdown amountWithBreakdown): void |
| `payerName` | [`?SubscriptionPayerName`](../../doc/models/subscription-payer-name.md) | Optional | The name of the party. | getPayerName(): ?SubscriptionPayerName | setPayerName(?SubscriptionPayerName payerName): void |
| `payerEmail` | `?string` | Optional | The internationalized email address. Note: Up to 64 characters are allowed before and 255 characters are allowed after the @ sign. However, the generally accepted maximum length for an email address is 254 characters. The pattern verifies that an unquoted @ sign exists.<br><br>**Constraints**: *Minimum Length*: `3`, *Maximum Length*: `254`, *Pattern*: ``^(?:[A-Za-z0-9!#$%&'*+/=?^_`{\|}~-]+(?:\.[A-Za-z0-9!#$%&'*+/=?^_`{\|}~-]+)*\|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]\|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[A-Za-z0-9](?:[A-Za-z0-9-]*[A-Za-z0-9])?\.)+[A-Za-z0-9](?:[A-Za-z0-9-]*[A-Za-z0-9])?\|\[(?:(?:25[0-5]\|2[0-4][0-9]\|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]\|2[0-4][0-9]\|[01]?[0-9][0-9]?\|[A-Za-z0-9-]*[A-Za-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]\|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])$`` | getPayerEmail(): ?string | setPayerEmail(?string payerEmail): void |
| `time` | `string` | Required | The date and time, in [Internet date and time format](https://tools.ietf.org/html/rfc3339#section-5.6). Seconds are required while fractional seconds are optional. Note: The regular expression provides guidance but does not reject all invalid dates.<br><br>**Constraints**: *Minimum Length*: `20`, *Maximum Length*: `64`, *Pattern*: `^[0-9]{4}-(0[1-9]\|1[0-2])-(0[1-9]\|[1-2][0-9]\|3[0-1])[T,t]([0-1][0-9]\|2[0-3]):[0-5][0-9]:([0-5][0-9]\|60)([.][0-9]+)?([Zz]\|[+-][0-9]{2}:[0-9]{2})$` | getTime(): string | setTime(string time): void |

## Example (as JSON)

```json
{
  "status": "REFUNDED",
  "id": "id4",
  "amount_with_breakdown": {
    "gross_amount": {
      "currency_code": "currency_code4",
      "value": "value0"
    },
    "total_item_amount": {
      "currency_code": "currency_code8",
      "value": "value4"
    },
    "fee_amount": {
      "currency_code": "currency_code2",
      "value": "value4"
    },
    "shipping_amount": {
      "currency_code": "currency_code0",
      "value": "value6"
    },
    "tax_amount": {
      "currency_code": "currency_code2",
      "value": "value8"
    },
    "net_amount": {
      "currency_code": "currency_code6",
      "value": "value2"
    }
  },
  "payer_name": {
    "prefix": "prefix8",
    "given_name": "given_name2",
    "surname": "surname8",
    "middle_name": "middle_name0",
    "suffix": "suffix0"
  },
  "payer_email": "payer_email2",
  "time": "time4"
}
```

