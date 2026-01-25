
# Payment Token Status

The status of the payment token.

## Enumeration

`PaymentTokenStatus`

## Fields

| Name | Description |
|  --- | --- |
| `CREATED` | A setup token is initialized with minimal information, more data must be added to the setup-token to be vaulted |
| `PAYER_ACTION_REQUIRED` | A contingency on payer approval is required before the payment method can be saved. |
| `APPROVED` | Setup token is ready to be vaulted. If a buyer approval contigency was returned, it is has been approved. |
| `VAULTED` | The payment token has been vaulted. |
| `TOKENIZED` | A vaulted payment method token has been tokenized for short term (one time) use. |

