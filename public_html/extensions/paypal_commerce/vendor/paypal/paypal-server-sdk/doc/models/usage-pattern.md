
# Usage Pattern

Expected business/pricing model for the billing agreement., Expected business/charge model for the billing agreement.

## Enumeration

`UsagePattern`

## Fields

| Name | Description |
|  --- | --- |
| `IMMEDIATE` | On-demand instant payments – non-recurring, pre-paid, variable amount, variable frequency. |
| `DEFERRED` | Pay after use, non-recurring post-paid, variable amount, irregular frequency. |
| `RECURRING_PREPAID` | Pay upfront fixed or variable amount on a fixed date before the goods/service is delivered. |
| `RECURRING_POSTPAID` | Pay on a fixed date based on usage or consumption after the goods/service is delivered. |
| `THRESHOLD_PREPAID` | Charge payer when the set amount is reached or monthly billing cycle, whichever comes first, before the goods/service is delivered. |
| `THRESHOLD_POSTPAID` | Charge payer when the set amount is reached or monthly billing cycle, whichever comes first, after the goods/service is delivered. |
| `SUBSCRIPTION_PREPAID` | Subscription plan where the "amount due" and the "billing frequency" are fixed, and there is no defined duration with the payment due before the good/service is delivered. |
| `SUBSCRIPTION_POSTPAID` | Subscription plan where the "amount due" and the "billing frequency" are fixed, and there is no defined duration with the payment due after the goods/services are delivered. |
| `UNSCHEDULED_PREPAID` | Unscheduled card on file plan where the merchant can bill buyer upfront based on an agreed logic, but "amount due" and "frequency" can vary. Inclusive of automatic reload plans. |
| `UNSCHEDULED_POSTPAID` | Unscheduled card on file plan where the merchant can bill buyer based on an agreed logic, but "amount due" and "frequency" can vary. Inclusive of automatic reload plans. |
| `INSTALLMENT_PREPAID` | Merchant-managed installment plan when the "amount" to be paid and the "billing frequency" are fixed, but there is a defined number of payments with the payment due before the good/service is delivered. |
| `INSTALLMENT_POSTPAID` | Merchant-managed installment plan when the "amount" to be paid and the "billing frequency" are fixed, but there is a defined number of payments with the payment due after the goods/services are delivered. |

