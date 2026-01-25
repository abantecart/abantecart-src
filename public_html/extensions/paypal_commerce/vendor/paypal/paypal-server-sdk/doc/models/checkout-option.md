
# Checkout Option

A checkout option as a name-and-value pair.

## Structure

`CheckoutOption`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `checkoutOptionName` | `?string` | Optional | The checkout option name, such as `color` or `texture`.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `200`, *Pattern*: `^[a-zA-Z0-9_'\-., ":;\!?]*$` | getCheckoutOptionName(): ?string | setCheckoutOptionName(?string checkoutOptionName): void |
| `checkoutOptionValue` | `?string` | Optional | The checkout option value. For example, the checkout option `color` might be `blue` or `red` while the checkout option `texture` might be `smooth` or `rippled`.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `200`, *Pattern*: `^[a-zA-Z0-9_'\-., ":;\!?]*$` | getCheckoutOptionValue(): ?string | setCheckoutOptionValue(?string checkoutOptionValue): void |

## Example (as JSON)

```json
{
  "checkout_option_name": "checkout_option_name6",
  "checkout_option_value": "checkout_option_value0"
}
```

