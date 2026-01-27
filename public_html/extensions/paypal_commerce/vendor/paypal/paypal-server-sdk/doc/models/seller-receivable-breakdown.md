
# Seller Receivable Breakdown

The detailed breakdown of the capture activity. This is not available for transactions that are in pending state.

## Structure

`SellerReceivableBreakdown`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `grossAmount` | [`Money`](../../doc/models/money.md) | Required | The currency and amount for a financial transaction, such as a balance or payment due. | getGrossAmount(): Money | setGrossAmount(Money grossAmount): void |
| `paypalFee` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getPaypalFee(): ?Money | setPaypalFee(?Money paypalFee): void |
| `paypalFeeInReceivableCurrency` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getPaypalFeeInReceivableCurrency(): ?Money | setPaypalFeeInReceivableCurrency(?Money paypalFeeInReceivableCurrency): void |
| `netAmount` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getNetAmount(): ?Money | setNetAmount(?Money netAmount): void |
| `receivableAmount` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getReceivableAmount(): ?Money | setReceivableAmount(?Money receivableAmount): void |
| `exchangeRate` | [`?ExchangeRate`](../../doc/models/exchange-rate.md) | Optional | The exchange rate that determines the amount to convert from one currency to another currency. | getExchangeRate(): ?ExchangeRate | setExchangeRate(?ExchangeRate exchangeRate): void |
| `platformFees` | [`?(PlatformFee[])`](../../doc/models/platform-fee.md) | Optional | An array of platform or partner fees, commissions, or brokerage fees that associated with the captured payment.<br><br>**Constraints**: *Minimum Items*: `0`, *Maximum Items*: `1` | getPlatformFees(): ?array | setPlatformFees(?array platformFees): void |

## Example (as JSON)

```json
{
  "gross_amount": {
    "currency_code": "currency_code4",
    "value": "value0"
  },
  "paypal_fee": {
    "currency_code": "currency_code4",
    "value": "value2"
  },
  "paypal_fee_in_receivable_currency": {
    "currency_code": "currency_code2",
    "value": "value8"
  },
  "net_amount": {
    "currency_code": "currency_code6",
    "value": "value2"
  },
  "receivable_amount": {
    "currency_code": "currency_code2",
    "value": "value8"
  },
  "exchange_rate": {
    "source_currency": "source_currency4",
    "target_currency": "target_currency6",
    "value": "value6"
  }
}
```

