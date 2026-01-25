
# Confirm Order Request

Payer confirms the intent to pay for the Order using the provided payment source.

## Structure

`ConfirmOrderRequest`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `paymentSource` | [`PaymentSource`](../../doc/models/payment-source.md) | Required | The payment source definition. | getPaymentSource(): PaymentSource | setPaymentSource(PaymentSource paymentSource): void |
| `applicationContext` | [`?OrderConfirmApplicationContext`](../../doc/models/order-confirm-application-context.md) | Optional | Customizes the payer confirmation experience. | getApplicationContext(): ?OrderConfirmApplicationContext | setApplicationContext(?OrderConfirmApplicationContext applicationContext): void |

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
  },
  "application_context": {
    "brand_name": "brand_name8",
    "locale": "locale2",
    "return_url": "return_url0",
    "cancel_url": "cancel_url2",
    "stored_payment_source": {
      "payment_initiator": "CUSTOMER",
      "payment_type": "RECURRING",
      "usage": "FIRST",
      "previous_network_transaction_reference": {
        "id": "id6",
        "date": "date2",
        "network": "CONFIDIS",
        "acquirer_reference_number": "acquirer_reference_number8"
      }
    }
  }
}
```

