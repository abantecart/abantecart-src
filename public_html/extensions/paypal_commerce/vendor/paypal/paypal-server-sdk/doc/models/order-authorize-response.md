
# Order Authorize Response

The order authorize response.

## Structure

`OrderAuthorizeResponse`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `createTime` | `?string` | Optional | The date and time, in [Internet date and time format](https://tools.ietf.org/html/rfc3339#section-5.6). Seconds are required while fractional seconds are optional. Note: The regular expression provides guidance but does not reject all invalid dates.<br><br>**Constraints**: *Minimum Length*: `20`, *Maximum Length*: `64`, *Pattern*: `^[0-9]{4}-(0[1-9]\|1[0-2])-(0[1-9]\|[1-2][0-9]\|3[0-1])[T,t]([0-1][0-9]\|2[0-3]):[0-5][0-9]:([0-5][0-9]\|60)([.][0-9]+)?([Zz]\|[+-][0-9]{2}:[0-9]{2})$` | getCreateTime(): ?string | setCreateTime(?string createTime): void |
| `updateTime` | `?string` | Optional | The date and time, in [Internet date and time format](https://tools.ietf.org/html/rfc3339#section-5.6). Seconds are required while fractional seconds are optional. Note: The regular expression provides guidance but does not reject all invalid dates.<br><br>**Constraints**: *Minimum Length*: `20`, *Maximum Length*: `64`, *Pattern*: `^[0-9]{4}-(0[1-9]\|1[0-2])-(0[1-9]\|[1-2][0-9]\|3[0-1])[T,t]([0-1][0-9]\|2[0-3]):[0-5][0-9]:([0-5][0-9]\|60)([.][0-9]+)?([Zz]\|[+-][0-9]{2}:[0-9]{2})$` | getUpdateTime(): ?string | setUpdateTime(?string updateTime): void |
| `id` | `?string` | Optional | The ID of the order. | getId(): ?string | setId(?string id): void |
| `paymentSource` | [`?OrderAuthorizeResponsePaymentSource`](../../doc/models/order-authorize-response-payment-source.md) | Optional | The payment source used to fund the payment. | getPaymentSource(): ?OrderAuthorizeResponsePaymentSource | setPaymentSource(?OrderAuthorizeResponsePaymentSource paymentSource): void |
| `intent` | [`?string(CheckoutPaymentIntent)`](../../doc/models/checkout-payment-intent.md) | Optional | The intent to either capture payment immediately or authorize a payment for an order after order creation. | getIntent(): ?string | setIntent(?string intent): void |
| `payer` | [`?Payer`](../../doc/models/payer.md) | Optional | The customer who approves and pays for the order. The customer is also known as the payer. | getPayer(): ?Payer | setPayer(?Payer payer): void |
| `purchaseUnits` | [`?(PurchaseUnit[])`](../../doc/models/purchase-unit.md) | Optional | An array of purchase units. Each purchase unit establishes a contract between a customer and merchant. Each purchase unit represents either a full or partial order that the customer intends to purchase from the merchant.<br><br>**Constraints**: *Minimum Items*: `1`, *Maximum Items*: `10` | getPurchaseUnits(): ?array | setPurchaseUnits(?array purchaseUnits): void |
| `status` | [`?string(OrderStatus)`](../../doc/models/order-status.md) | Optional | The order status.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[0-9A-Z_]+$` | getStatus(): ?string | setStatus(?string status): void |
| `links` | [`?(LinkDescription[])`](../../doc/models/link-description.md) | Optional | An array of request-related HATEOAS links. To complete payer approval, use the `approve` link to redirect the payer. The API caller has 6 hours (default setting, this which can be changed by your account manager to 24/48/72 hours to accommodate your use case) from the time the order is created, to redirect your payer. Once redirected, the API caller has 6 hours for the payer to approve the order and either authorize or capture the order. If you are not using the PayPal JavaScript SDK to initiate PayPal Checkout (in context) ensure that you include `application_context.return_url` is specified or you will get "We're sorry, Things don't appear to be working at the moment" after the payer approves the payment. | getLinks(): ?array | setLinks(?array links): void |

## Example (as JSON)

```json
{
  "create_time": "create_time0",
  "update_time": "update_time4",
  "id": "id4",
  "payment_source": {
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
  },
  "intent": "CAPTURE"
}
```

