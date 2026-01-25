
# Payment Source

The payment source definition.

## Structure

`PaymentSource`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `card` | [`?CardRequest`](../../doc/models/card-request.md) | Optional | The payment card to use to fund a payment. Can be a credit or debit card. Note: Passing card number, cvv and expiry directly via the API requires PCI SAQ D compliance. *PayPal offers a mechanism by which you do not have to take on the PCI SAQ D burden by using hosted fields - refer to this Integration Guide*. | getCard(): ?CardRequest | setCard(?CardRequest card): void |
| `token` | [`?Token`](../../doc/models/token.md) | Optional | The tokenized payment source to fund a payment. | getToken(): ?Token | setToken(?Token token): void |
| `paypal` | [`?PaypalWallet`](../../doc/models/paypal-wallet.md) | Optional | A resource that identifies a PayPal Wallet is used for payment. | getPaypal(): ?PaypalWallet | setPaypal(?PaypalWallet paypal): void |
| `bancontact` | [`?BancontactPaymentRequest`](../../doc/models/bancontact-payment-request.md) | Optional | Information needed to pay using Bancontact. | getBancontact(): ?BancontactPaymentRequest | setBancontact(?BancontactPaymentRequest bancontact): void |
| `blik` | [`?BlikPaymentRequest`](../../doc/models/blik-payment-request.md) | Optional | Information needed to pay using BLIK. | getBlik(): ?BlikPaymentRequest | setBlik(?BlikPaymentRequest blik): void |
| `eps` | [`?EpsPaymentRequest`](../../doc/models/eps-payment-request.md) | Optional | Information needed to pay using eps. | getEps(): ?EpsPaymentRequest | setEps(?EpsPaymentRequest eps): void |
| `giropay` | [`?GiropayPaymentRequest`](../../doc/models/giropay-payment-request.md) | Optional | Information needed to pay using giropay. | getGiropay(): ?GiropayPaymentRequest | setGiropay(?GiropayPaymentRequest giropay): void |
| `ideal` | [`?IdealPaymentRequest`](../../doc/models/ideal-payment-request.md) | Optional | Information needed to pay using iDEAL. | getIdeal(): ?IdealPaymentRequest | setIdeal(?IdealPaymentRequest ideal): void |
| `mybank` | [`?MybankPaymentRequest`](../../doc/models/mybank-payment-request.md) | Optional | Information needed to pay using MyBank. | getMybank(): ?MybankPaymentRequest | setMybank(?MybankPaymentRequest mybank): void |
| `p24` | [`?P24PaymentRequest`](../../doc/models/p24-payment-request.md) | Optional | Information needed to pay using P24 (Przelewy24). | getP24(): ?P24PaymentRequest | setP24(?P24PaymentRequest p24): void |
| `sofort` | [`?SofortPaymentRequest`](../../doc/models/sofort-payment-request.md) | Optional | Information needed to pay using Sofort. | getSofort(): ?SofortPaymentRequest | setSofort(?SofortPaymentRequest sofort): void |
| `trustly` | [`?TrustlyPaymentRequest`](../../doc/models/trustly-payment-request.md) | Optional | Information needed to pay using Trustly. | getTrustly(): ?TrustlyPaymentRequest | setTrustly(?TrustlyPaymentRequest trustly): void |
| `applePay` | [`?ApplePayRequest`](../../doc/models/apple-pay-request.md) | Optional | Information needed to pay using ApplePay. | getApplePay(): ?ApplePayRequest | setApplePay(?ApplePayRequest applePay): void |
| `googlePay` | [`?GooglePayRequest`](../../doc/models/google-pay-request.md) | Optional | Information needed to pay using Google Pay. | getGooglePay(): ?GooglePayRequest | setGooglePay(?GooglePayRequest googlePay): void |
| `venmo` | [`?VenmoWalletRequest`](../../doc/models/venmo-wallet-request.md) | Optional | Information needed to pay using Venmo. | getVenmo(): ?VenmoWalletRequest | setVenmo(?VenmoWalletRequest venmo): void |

## Example (as JSON)

```json
{
  "card": {
    "name": "name6",
    "number": "number6",
    "expiry": "expiry4",
    "security_code": "security_code8",
    "billing_address": {
      "address_line_1": "address_line_12",
      "address_line_2": "address_line_28",
      "admin_area_2": "admin_area_28",
      "admin_area_1": "admin_area_14",
      "postal_code": "postal_code0",
      "country_code": "country_code8"
    }
  },
  "token": {
    "id": "id6",
    "type": "BILLING_AGREEMENT"
  },
  "paypal": {
    "vault_id": "vault_id0",
    "email_address": "email_address0",
    "name": {
      "given_name": "given_name2",
      "surname": "surname8"
    },
    "phone": {
      "phone_type": "OTHER",
      "phone_number": {
        "national_number": "national_number6"
      }
    },
    "birth_date": "birth_date8"
  },
  "bancontact": {
    "name": "name0",
    "country_code": "country_code0",
    "experience_context": {
      "brand_name": "brand_name2",
      "locale": "locale6",
      "shipping_preference": "NO_SHIPPING",
      "return_url": "return_url4",
      "cancel_url": "cancel_url6"
    }
  },
  "blik": {
    "name": "name2",
    "country_code": "country_code2",
    "email": "email4",
    "experience_context": {
      "brand_name": "brand_name2",
      "locale": "locale6",
      "shipping_preference": "NO_SHIPPING",
      "return_url": "return_url4",
      "cancel_url": "cancel_url6"
    },
    "level_0": {
      "auth_code": "auth_code8"
    },
    "one_click": {
      "auth_code": "auth_code0",
      "consumer_reference": "consumer_reference2",
      "alias_label": "alias_label6",
      "alias_key": "alias_key4"
    }
  }
}
```

