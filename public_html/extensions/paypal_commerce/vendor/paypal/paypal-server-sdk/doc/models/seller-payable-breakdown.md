
# Seller Payable Breakdown

The breakdown of the refund.

## Structure

`SellerPayableBreakdown`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `grossAmount` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getGrossAmount(): ?Money | setGrossAmount(?Money grossAmount): void |
| `paypalFee` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getPaypalFee(): ?Money | setPaypalFee(?Money paypalFee): void |
| `paypalFeeInReceivableCurrency` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getPaypalFeeInReceivableCurrency(): ?Money | setPaypalFeeInReceivableCurrency(?Money paypalFeeInReceivableCurrency): void |
| `netAmount` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getNetAmount(): ?Money | setNetAmount(?Money netAmount): void |
| `netAmountInReceivableCurrency` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getNetAmountInReceivableCurrency(): ?Money | setNetAmountInReceivableCurrency(?Money netAmountInReceivableCurrency): void |
| `platformFees` | [`?(PlatformFee[])`](../../doc/models/platform-fee.md) | Optional | An array of platform or partner fees, commissions, or brokerage fees for the refund.<br><br>**Constraints**: *Minimum Items*: `0`, *Maximum Items*: `1` | getPlatformFees(): ?array | setPlatformFees(?array platformFees): void |
| `netAmountBreakdown` | [`?(NetAmountBreakdownItem[])`](../../doc/models/net-amount-breakdown-item.md) | Optional | An array of breakdown values for the net amount. Returned when the currency of the refund is different from the currency of the PayPal account where the payee holds their funds. | getNetAmountBreakdown(): ?array | setNetAmountBreakdown(?array netAmountBreakdown): void |
| `totalRefundedAmount` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getTotalRefundedAmount(): ?Money | setTotalRefundedAmount(?Money totalRefundedAmount): void |

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
  "net_amount_in_receivable_currency": {
    "currency_code": "currency_code8",
    "value": "value4"
  }
}
```

