
# Setup Token Request Payment Source

The payment method to vault with the instrument details.

## Structure

`SetupTokenRequestPaymentSource`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `card` | [`?SetupTokenRequestCard`](../../doc/models/setup-token-request-card.md) | Optional | A Resource representing a request to vault a Card. | getCard(): ?SetupTokenRequestCard | setCard(?SetupTokenRequestCard card): void |
| `paypal` | [`?VaultPaypalWalletRequest`](../../doc/models/vault-paypal-wallet-request.md) | Optional | A resource representing a request to vault PayPal Wallet. | getPaypal(): ?VaultPaypalWalletRequest | setPaypal(?VaultPaypalWalletRequest paypal): void |
| `venmo` | [`?VaultVenmoRequest`](../../doc/models/vault-venmo-request.md) | Optional | A resource representing a request to vault Venmo. | getVenmo(): ?VaultVenmoRequest | setVenmo(?VaultVenmoRequest venmo): void |
| `applePay` | [`?VaultApplePayRequest`](../../doc/models/vault-apple-pay-request.md) | Optional | A resource representing a request to vault Apple Pay. | getApplePay(): ?VaultApplePayRequest | setApplePay(?VaultApplePayRequest applePay): void |
| `token` | [`?VaultTokenRequest`](../../doc/models/vault-token-request.md) | Optional | The Tokenized Payment Source representing a Request to Vault a Token. | getToken(): ?VaultTokenRequest | setToken(?VaultTokenRequest token): void |
| `bank` | [`?BankRequest`](../../doc/models/bank-request.md) | Optional | A Resource representing a request to vault a Bank used for ACH Debit. | getBank(): ?BankRequest | setBank(?BankRequest bank): void |

## Example (as JSON)

```json
{
  "card": {
    "name": "name6",
    "number": "number6",
    "expiry": "expiry4",
    "security_code": "security_code8",
    "brand": "CB_NATIONALE"
  },
  "paypal": {
    "description": "description2",
    "usage_pattern": "THRESHOLD_PREPAID",
    "shipping": {
      "name": {
        "full_name": "full_name6"
      },
      "email_address": "email_address2",
      "phone_number": {
        "country_code": "country_code2",
        "national_number": "national_number6"
      },
      "type": "SHIPPING",
      "address": {
        "address_line_1": "address_line_16",
        "address_line_2": "address_line_26",
        "admin_area_2": "admin_area_20",
        "admin_area_1": "admin_area_12",
        "postal_code": "postal_code8",
        "country_code": "country_code6"
      }
    },
    "permit_multiple_payment_tokens": false,
    "usage_type": "MERCHANT"
  },
  "venmo": {
    "description": "description6",
    "usage_pattern": "UNSCHEDULED_PREPAID",
    "shipping": {
      "name": {
        "full_name": "full_name6"
      },
      "email_address": "email_address2",
      "phone_number": {
        "country_code": "country_code2",
        "national_number": "national_number6"
      },
      "type": "SHIPPING",
      "address": {
        "address_line_1": "address_line_16",
        "address_line_2": "address_line_26",
        "admin_area_2": "admin_area_20",
        "admin_area_1": "admin_area_12",
        "postal_code": "postal_code8",
        "country_code": "country_code6"
      }
    },
    "permit_multiple_payment_tokens": false,
    "usage_type": "MERCHANT"
  },
  "apple_pay": {
    "token": "token6",
    "card": {
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
  "token": {
    "id": "id6",
    "type": "SETUP_TOKEN"
  }
}
```

