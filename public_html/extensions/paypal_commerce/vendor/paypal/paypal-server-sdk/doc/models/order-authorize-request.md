
# Order Authorize Request

The authorization of an order request.

## Structure

`OrderAuthorizeRequest`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `paymentSource` | [`?OrderAuthorizeRequestPaymentSource`](../../doc/models/order-authorize-request-payment-source.md) | Optional | The payment source definition. | getPaymentSource(): ?OrderAuthorizeRequestPaymentSource | setPaymentSource(?OrderAuthorizeRequestPaymentSource paymentSource): void |

## Example (as JSON)

```json
{
  "payment_source": {
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
    "apple_pay": {
      "id": "id0",
      "name": "name0",
      "email_address": "email_address8",
      "phone_number": {
        "national_number": "national_number6"
      },
      "decrypted_token": {
        "transaction_amount": {
          "currency_code": "currency_code6",
          "value": "value2"
        },
        "tokenized_card": {
          "name": "name4",
          "number": "number2",
          "expiry": "expiry2",
          "card_type": "VISA",
          "type": "UNKNOWN"
        },
        "device_manufacturer_id": "device_manufacturer_id6",
        "payment_data_type": "3DSECURE",
        "payment_data": {
          "cryptogram": "cryptogram6",
          "eci_indicator": "eci_indicator0",
          "emv_data": "emv_data0",
          "pin": "pin4"
        }
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
      },
      "decrypted_token": {
        "message_id": "message_id0",
        "message_expiration": "message_expiration2",
        "payment_method": "CARD",
        "card": {
          "name": "name6",
          "number": "number6",
          "expiry": "expiry4",
          "last_digits": "last_digits0",
          "type": "UNKNOWN"
        },
        "authentication_method": "PAN_ONLY",
        "cryptogram": "cryptogram6",
        "eci_indicator": "eci_indicator0"
      }
    }
  }
}
```

