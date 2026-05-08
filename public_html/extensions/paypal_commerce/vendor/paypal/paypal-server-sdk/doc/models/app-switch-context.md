
# App Switch Context

Merchant provided details of the native app or mobile web browser to facilitate buyer's app switch to the PayPal consumer app.

## Structure

`AppSwitchContext`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `nativeApp` | [`?NativeAppContext`](../../doc/models/native-app-context.md) | Optional | Merchant provided, buyer's native app preferences to app switch to the PayPal consumer app. | getNativeApp(): ?NativeAppContext | setNativeApp(?NativeAppContext nativeApp): void |
| `mobileWeb` | [`?MobileWebContext`](../../doc/models/mobile-web-context.md) | Optional | Buyer's mobile web browser context to app switch to the PayPal consumer app. | getMobileWeb(): ?MobileWebContext | setMobileWeb(?MobileWebContext mobileWeb): void |

## Example (as JSON)

```json
{
  "native_app": {
    "os_type": "IOS",
    "os_version": "os_version0"
  },
  "mobile_web": {
    "return_flow": "AUTO",
    "buyer_user_agent": "buyer_user_agent8"
  }
}
```

