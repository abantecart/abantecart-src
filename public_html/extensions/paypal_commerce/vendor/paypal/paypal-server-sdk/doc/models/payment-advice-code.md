
# Payment Advice Code

The declined payment transactions might have payment advice codes. The card networks, like Visa and Mastercard, return payment advice codes.

## Enumeration

`PaymentAdviceCode`

## Fields

| Name | Description |
|  --- | --- |
| `PAYMENTADVICE_01` | For Mastercard, expired card account upgrade or portfolio sale conversion. Obtain new account information before next billing cycle. |
| `PAYMENTADVICE_02` | For Mastercard, over credit limit or insufficient funds. Retry the transaction 72 hours later. For Visa, the card holder wants to stop only one specific payment in the recurring payment relationship. The merchant must NOT resubmit the same transaction. The merchant can continue the billing process in the subsequent billing period. |
| `PAYMENTADVICE_03` | For Mastercard, account closed as fraudulent. Obtain another type of payment from customer due to account being closed or fraud. Possible reason: Account closed as fraudulent. For Visa, the card holder wants to stop all recurring payment transactions for a specific merchant. Stop recurring payment requests. |
| `PAYMENTADVICE_04` | For Mastercard, token requirements not fulfilled for this token type. |
| `PAYMENTADVICE_21` | For Mastercard, the card holder has been unsuccessful at canceling recurring payment through merchant. Stop recurring payment requests. For Visa, all recurring payments were canceled for the card number requested. Stop recurring payment requests. |
| `PAYMENTADVICE_22` | For Mastercard, merchant does not qualify for product code. |
| `PAYMENTADVICE_24` | For Mastercard, retry after 1 hour. |
| `PAYMENTADVICE_25` | For Mastercard, retry after 24 hours. |
| `PAYMENTADVICE_26` | For Mastercard, retry after 2 days. |
| `PAYMENTADVICE_27` | For Mastercard, retry after 4 days. |
| `PAYMENTADVICE_28` | For Mastercard, retry after 6 days. |
| `PAYMENTADVICE_29` | For Mastercard, retry after 8 days. |
| `PAYMENTADVICE_30` | For Mastercard, retry after 10 days . |
| `PAYMENTADVICE_40` | For Mastercard, consumer non-reloadable prepaid card. |
| `PAYMENTADVICE_43` | For Mastercard, consumer multi-use virtual card number. |

