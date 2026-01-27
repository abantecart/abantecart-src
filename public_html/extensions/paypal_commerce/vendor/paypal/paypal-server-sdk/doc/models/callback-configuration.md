
# Callback Configuration

CallBack Configuration that the merchant can provide to PayPal/Venmo.

## Structure

`CallbackConfiguration`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `callbackEvents` | [`string(CallbackEvents)[]`](../../doc/models/callback-events.md) | Required | An array of callback events merchant can subscribe to for the corresponding callback url.<br><br>**Constraints**: *Minimum Items*: `1`, *Maximum Items*: `5`, *Unique Items Required*, *Minimum Length*: `1`, *Maximum Length*: `256`, *Pattern*: `^[0-9A-Z_]+$` | getCallbackEvents(): array | setCallbackEvents(array callbackEvents): void |
| `callbackUrl` | `string` | Required | Merchant provided CallBack url.PayPal/Venmo will use this url to call the merchant back when the events occur .PayPal/Venmo expects a secured url usually in the https format.merchant can append the cart id or other params part of the url as query or path params.<br><br>**Constraints**: *Minimum Length*: `10`, *Maximum Length*: `2040`, *Pattern*: `^.*$` | getCallbackUrl(): string | setCallbackUrl(string callbackUrl): void |

## Example (as JSON)

```json
{
  "callback_events": [
    "SHIPPING_OPTIONS",
    "SHIPPING_ADDRESS",
    "SHIPPING_OPTIONS"
  ],
  "callback_url": "callback_url2"
}
```

