
# Order Authorize Response Payment Source

The payment source used to fund the payment.

## Structure

`OrderAuthorizeResponsePaymentSource`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `card` | [`?CardResponse`](../../doc/models/card-response.md) | Optional | The payment card to use to fund a payment. Card can be a credit or debit card. | getCard(): ?CardResponse | setCard(?CardResponse card): void |
| `paypal` | [`?PaypalWalletResponse`](../../doc/models/paypal-wallet-response.md) | Optional | The PayPal Wallet response. | getPaypal(): ?PaypalWalletResponse | setPaypal(?PaypalWalletResponse paypal): void |
| `applePay` | [`?ApplePayPaymentObject`](../../doc/models/apple-pay-payment-object.md) | Optional | Information needed to pay using ApplePay. | getApplePay(): ?ApplePayPaymentObject | setApplePay(?ApplePayPaymentObject applePay): void |
| `googlePay` | [`?GooglePayWalletResponse`](../../doc/models/google-pay-wallet-response.md) | Optional | Google Pay Wallet payment data. | getGooglePay(): ?GooglePayWalletResponse | setGooglePay(?GooglePayWalletResponse googlePay): void |
| `venmo` | [`?VenmoWalletResponse`](../../doc/models/venmo-wallet-response.md) | Optional | Venmo wallet response. | getVenmo(): ?VenmoWalletResponse | setVenmo(?VenmoWalletResponse venmo): void |

## Example (as JSON)

```json
{
  "card": {
    "name": "name6",
    "last_digits": "last_digits0",
    "brand": "CB_NATIONALE",
    "available_networks": [
      "DELTA"
    ],
    "type": "UNKNOWN"
  },
  "paypal": {
    "email_address": "email_address0",
    "account_id": "account_id4",
    "account_status": "VERIFIED",
    "name": {
      "given_name": "given_name2",
      "surname": "surname8"
    },
    "phone_type": "FAX"
  },
  "apple_pay": {
    "id": "id0",
    "token": "token6",
    "name": "name0",
    "email_address": "email_address8",
    "phone_number": {
      "national_number": "national_number6"
    }
  },
  "google_pay": {
    "name": "name8",
    "email_address": "email_address6",
    "phone_number": {
      "country_code": "country_code2",
      "national_number": "national_number6"
    },
    "card": {
      "name": "name6",
      "last_digits": "last_digits0",
      "type": "UNKNOWN",
      "brand": "CB_NATIONALE",
      "billing_address": {
        "address_line_1": "address_line_12",
        "address_line_2": "address_line_28",
        "admin_area_2": "admin_area_28",
        "admin_area_1": "admin_area_14",
        "postal_code": "postal_code0",
        "country_code": "country_code8"
      }
    }
  },
  "venmo": {
    "email_address": "email_address4",
    "account_id": "account_id8",
    "user_name": "user_name2",
    "name": {
      "given_name": "given_name2",
      "surname": "surname8"
    },
    "phone_number": {
      "national_number": "national_number6"
    }
  }
}
```

