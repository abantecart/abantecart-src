
# Setup Token Request

Setup Token Request where the `source` defines the type of instrument to be stored.

## Structure

`SetupTokenRequest`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `customer` | [`?Customer`](../../doc/models/customer.md) | Optional | This object defines a customer in your system. Use it to manage customer profiles, save payment methods and contact details. | getCustomer(): ?Customer | setCustomer(?Customer customer): void |
| `paymentSource` | [`SetupTokenRequestPaymentSource`](../../doc/models/setup-token-request-payment-source.md) | Required | The payment method to vault with the instrument details. | getPaymentSource(): SetupTokenRequestPaymentSource | setPaymentSource(SetupTokenRequestPaymentSource paymentSource): void |

## Example (as JSON)

```json
{
  "customer": {
    "id": "id0",
    "merchant_customer_id": "merchant_customer_id2"
  },
  "payment_source": {
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
}
```

