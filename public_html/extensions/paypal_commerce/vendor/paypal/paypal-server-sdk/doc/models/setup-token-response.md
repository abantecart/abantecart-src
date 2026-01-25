
# Setup Token Response

Minimal representation of a cached setup token.

## Structure

`SetupTokenResponse`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `id` | `?string` | Optional | The PayPal-generated ID for the vaulted payment source. This ID should be stored on the merchant's server so the saved payment source can be used for future transactions.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[0-9a-zA-Z_-]+$` | getId(): ?string | setId(?string id): void |
| `customer` | [`?Customer`](../../doc/models/customer.md) | Optional | This object defines a customer in your system. Use it to manage customer profiles, save payment methods and contact details. | getCustomer(): ?Customer | setCustomer(?Customer customer): void |
| `status` | [`?string(PaymentTokenStatus)`](../../doc/models/payment-token-status.md) | Optional | The status of the payment token.<br><br>**Default**: `PaymentTokenStatus::CREATED`<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[0-9A-Z_]+$` | getStatus(): ?string | setStatus(?string status): void |
| `paymentSource` | [`?SetupTokenResponsePaymentSource`](../../doc/models/setup-token-response-payment-source.md) | Optional | The setup payment method details. | getPaymentSource(): ?SetupTokenResponsePaymentSource | setPaymentSource(?SetupTokenResponsePaymentSource paymentSource): void |
| `links` | [`?(LinkDescription[])`](../../doc/models/link-description.md) | Optional | An array of related [HATEOAS links](/api/rest/responses/#hateoas).<br><br>**Constraints**: *Minimum Items*: `1`, *Maximum Items*: `32` | getLinks(): ?array | setLinks(?array links): void |

## Example (as JSON)

```json
{
  "status": "CREATED",
  "id": "id6",
  "customer": {
    "id": "id0",
    "merchant_customer_id": "merchant_customer_id2"
  },
  "payment_source": {
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
    }
  },
  "links": [
    {
      "href": "href6",
      "rel": "rel0",
      "method": "HEAD"
    }
  ]
}
```

