
# Order Application Context Shipping Preference

DEPRECATED. DEPRECATED. The shipping preference: Displays the shipping address to the customer. Enables the customer to choose an address on the PayPal site. Restricts the customer from changing the address during the payment-approval process. .  The fields in `application_context` are now available in the `experience_context` object under the `payment_source` which supports them (eg. `payment_source.paypal.experience_context.shipping_preference`). Please specify this field in the `experience_context` object instead of the `application_context` object.

## Enumeration

`OrderApplicationContextShippingPreference`

## Fields

| Name | Description |
|  --- | --- |
| `GET_FROM_FILE` | Use the customer-provided shipping address on the PayPal site. |
| `NO_SHIPPING` | Redact the shipping address from the PayPal site. Recommended for digital goods. |
| `SET_PROVIDED_ADDRESS` | Use the merchant-provided address. The customer cannot change this address on the PayPal site. |

