
# Paypal Wallet Context Shipping Preference

The location from which the shipping address is derived.

## Enumeration

`PaypalWalletContextShippingPreference`

## Fields

| Name | Description |
|  --- | --- |
| `GET_FROM_FILE` | Get the customer-provided shipping address on the PayPal site. |
| `NO_SHIPPING` | Removes the shipping address information from the API response and the Paypal site. However, the shipping.phone_number and shipping.email_address fields will still be returned to allow for digital goods delivery. |
| `SET_PROVIDED_ADDRESS` | Get the merchant-provided address. The customer cannot change this address on the PayPal site. If merchant does not pass an address, customer can choose the address on PayPal pages. |

