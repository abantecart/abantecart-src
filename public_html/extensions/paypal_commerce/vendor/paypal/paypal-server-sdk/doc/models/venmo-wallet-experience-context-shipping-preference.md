
# Venmo Wallet Experience Context Shipping Preference

The location from which the shipping address is derived.

## Enumeration

`VenmoWalletExperienceContextShippingPreference`

## Fields

| Name | Description |
|  --- | --- |
| `GET_FROM_FILE` | Get the customer-provided shipping address on the PayPal site. |
| `NO_SHIPPING` | Redacts the shipping address from the PayPal site. Recommended for digital goods. |
| `SET_PROVIDED_ADDRESS` | Get the merchant-provided address. The customer cannot change this address on the PayPal site. If merchant does not pass an address, customer can choose the address on PayPal pages. |

