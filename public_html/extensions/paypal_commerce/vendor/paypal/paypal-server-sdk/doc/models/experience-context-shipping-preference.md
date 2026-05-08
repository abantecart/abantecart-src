
# Experience Context Shipping Preference

The location from which the shipping address is derived., The shipping preference. This only applies to PayPal payment source., The shipping preference. This only applies to PayPal payment source., The location from which the shipping address is derived.

## Enumeration

`ExperienceContextShippingPreference`

## Fields

| Name | Description |
|  --- | --- |
| `GET_FROM_FILE` | Get the customer-provided shipping address on the PayPal site. |
| `NO_SHIPPING` | Redacts the shipping address from the PayPal site. Recommended for digital goods. |
| `SET_PROVIDED_ADDRESS` | Merchant sends the shipping address using purchase_units.shipping.address. The customer cannot change this address on the PayPal site. |

