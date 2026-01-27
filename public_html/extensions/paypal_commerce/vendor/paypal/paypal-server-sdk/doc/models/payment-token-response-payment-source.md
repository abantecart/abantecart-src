
# Payment Token Response Payment Source

The vaulted payment method details.

## Structure

`PaymentTokenResponsePaymentSource`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `card` | [`?CardPaymentTokenEntity`](../../doc/models/card-payment-token-entity.md) | Optional | Full representation of a Card Payment Token including network token. | getCard(): ?CardPaymentTokenEntity | setCard(?CardPaymentTokenEntity card): void |
| `paypal` | [`?PaypalPaymentToken`](../../doc/models/paypal-payment-token.md) | Optional | Full representation of a PayPal Payment Token. | getPaypal(): ?PaypalPaymentToken | setPaypal(?PaypalPaymentToken paypal): void |
| `venmo` | [`?VenmoPaymentToken`](../../doc/models/venmo-payment-token.md) | Optional | Full representation of a Venmo Payment Token. | getVenmo(): ?VenmoPaymentToken | setVenmo(?VenmoPaymentToken venmo): void |
| `applePay` | [`?ApplePayPaymentToken`](../../doc/models/apple-pay-payment-token.md) | Optional | A resource representing a response for Apple Pay. | getApplePay(): ?ApplePayPaymentToken | setApplePay(?ApplePayPaymentToken applePay): void |

## Example (as JSON)

```json
{
  "card": {
    "name": "name6",
    "last_digits": "last_digits0",
    "brand": "CB_NATIONALE",
    "expiry": "expiry4",
    "billing_address": {
      "address_line_1": "address_line_12",
      "address_line_2": "address_line_28",
      "admin_area_2": "admin_area_28",
      "admin_area_1": "admin_area_14",
      "postal_code": "postal_code0",
      "country_code": "country_code8"
    }
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
  }
}
```

