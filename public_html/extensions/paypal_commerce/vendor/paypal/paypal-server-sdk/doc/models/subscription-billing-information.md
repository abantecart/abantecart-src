
# Subscription Billing Information

The billing details for the subscription. If the subscription was or is active, these fields are populated.

## Structure

`SubscriptionBillingInformation`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `outstandingBalance` | [`Money`](../../doc/models/money.md) | Required | The currency and amount for a financial transaction, such as a balance or payment due. | getOutstandingBalance(): Money | setOutstandingBalance(Money outstandingBalance): void |
| `cycleExecutions` | [`?(CycleExecution[])`](../../doc/models/cycle-execution.md) | Optional | The trial and regular billing executions.<br><br>**Constraints**: *Minimum Items*: `0`, *Maximum Items*: `3` | getCycleExecutions(): ?array | setCycleExecutions(?array cycleExecutions): void |
| `lastPayment` | [`?LastPaymentDetails`](../../doc/models/last-payment-details.md) | Optional | The details for the last payment. | getLastPayment(): ?LastPaymentDetails | setLastPayment(?LastPaymentDetails lastPayment): void |
| `nextBillingTime` | `?string` | Optional | The date and time, in [Internet date and time format](https://tools.ietf.org/html/rfc3339#section-5.6). Seconds are required while fractional seconds are optional. Note: The regular expression provides guidance but does not reject all invalid dates.<br><br>**Constraints**: *Minimum Length*: `20`, *Maximum Length*: `64`, *Pattern*: `^[0-9]{4}-(0[1-9]\|1[0-2])-(0[1-9]\|[1-2][0-9]\|3[0-1])[T,t]([0-1][0-9]\|2[0-3]):[0-5][0-9]:([0-5][0-9]\|60)([.][0-9]+)?([Zz]\|[+-][0-9]{2}:[0-9]{2})$` | getNextBillingTime(): ?string | setNextBillingTime(?string nextBillingTime): void |
| `finalPaymentTime` | `?string` | Optional | The date and time, in [Internet date and time format](https://tools.ietf.org/html/rfc3339#section-5.6). Seconds are required while fractional seconds are optional. Note: The regular expression provides guidance but does not reject all invalid dates.<br><br>**Constraints**: *Minimum Length*: `20`, *Maximum Length*: `64`, *Pattern*: `^[0-9]{4}-(0[1-9]\|1[0-2])-(0[1-9]\|[1-2][0-9]\|3[0-1])[T,t]([0-1][0-9]\|2[0-3]):[0-5][0-9]:([0-5][0-9]\|60)([.][0-9]+)?([Zz]\|[+-][0-9]{2}:[0-9]{2})$` | getFinalPaymentTime(): ?string | setFinalPaymentTime(?string finalPaymentTime): void |
| `failedPaymentsCount` | `int` | Required | The number of consecutive payment failures. Resets to `0` after a successful payment. If this reaches the `payment_failure_threshold` value, the subscription updates to the `SUSPENDED` state.<br><br>**Constraints**: `>= 0`, `<= 999` | getFailedPaymentsCount(): int | setFailedPaymentsCount(int failedPaymentsCount): void |
| `lastFailedPayment` | [`?FailedPaymentDetails`](../../doc/models/failed-payment-details.md) | Optional | The details for the failed payment of the subscription. | getLastFailedPayment(): ?FailedPaymentDetails | setLastFailedPayment(?FailedPaymentDetails lastFailedPayment): void |

## Example (as JSON)

```json
{
  "outstanding_balance": {
    "currency_code": "currency_code8",
    "value": "value4"
  },
  "cycle_executions": [
    {
      "tenure_type": "REGULAR",
      "sequence": 64,
      "cycles_completed": 110,
      "cycles_remaining": 14,
      "current_pricing_scheme_version": 99,
      "total_cycles": 254
    },
    {
      "tenure_type": "REGULAR",
      "sequence": 64,
      "cycles_completed": 110,
      "cycles_remaining": 14,
      "current_pricing_scheme_version": 99,
      "total_cycles": 254
    }
  ],
  "last_payment": {
    "amount": {
      "currency_code": "currency_code6",
      "value": "value0"
    },
    "time": "time2"
  },
  "next_billing_time": "next_billing_time0",
  "final_payment_time": "final_payment_time4",
  "failed_payments_count": 70,
  "last_failed_payment": {
    "amount": {
      "currency_code": "currency_code6",
      "value": "value0"
    },
    "time": "time4",
    "reason_code": "PAYER_CANNOT_PAY",
    "next_payment_retry_time": "next_payment_retry_time6"
  }
}
```

