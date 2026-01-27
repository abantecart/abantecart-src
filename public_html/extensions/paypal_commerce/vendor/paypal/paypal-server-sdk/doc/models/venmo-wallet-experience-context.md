
# Venmo Wallet Experience Context

Customizes the buyer experience during the approval process for payment with Venmo. Note: Partners and Marketplaces might configure shipping_preference during partner account setup, which overrides the request values.

## Structure

`VenmoWalletExperienceContext`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `brandName` | `?string` | Optional | The business name of the merchant. The pattern is defined by an external party and supports Unicode.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `127`, *Pattern*: `^.*$` | getBrandName(): ?string | setBrandName(?string brandName): void |
| `shippingPreference` | [`?string(VenmoWalletExperienceContextShippingPreference)`](../../doc/models/venmo-wallet-experience-context-shipping-preference.md) | Optional | The location from which the shipping address is derived.<br><br>**Default**: `VenmoWalletExperienceContextShippingPreference::GET_FROM_FILE`<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `24`, *Pattern*: `^[A-Z_]+$` | getShippingPreference(): ?string | setShippingPreference(?string shippingPreference): void |
| `orderUpdateCallbackConfig` | [`?CallbackConfiguration`](../../doc/models/callback-configuration.md) | Optional | CallBack Configuration that the merchant can provide to PayPal/Venmo. | getOrderUpdateCallbackConfig(): ?CallbackConfiguration | setOrderUpdateCallbackConfig(?CallbackConfiguration orderUpdateCallbackConfig): void |
| `userAction` | [`?string(VenmoWalletExperienceContextUserAction)`](../../doc/models/venmo-wallet-experience-context-user-action.md) | Optional | Configures a Continue or Pay Now checkout flow.<br><br>**Default**: `VenmoWalletExperienceContextUserAction::CONTINUE_`<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `8`, *Pattern*: `^[0-9A-Z_]+$` | getUserAction(): ?string | setUserAction(?string userAction): void |

## Example (as JSON)

```json
{
  "shipping_preference": "GET_FROM_FILE",
  "user_action": "CONTINUE",
  "brand_name": "brand_name6",
  "order_update_callback_config": {
    "callback_events": [
      "SHIPPING_OPTIONS",
      "SHIPPING_ADDRESS",
      "SHIPPING_OPTIONS"
    ],
    "callback_url": "callback_url6"
  }
}
```

