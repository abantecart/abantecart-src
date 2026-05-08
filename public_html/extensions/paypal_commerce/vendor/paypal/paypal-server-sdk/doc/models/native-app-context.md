
# Native App Context

Merchant provided, buyer's native app preferences to app switch to the PayPal consumer app.

## Structure

`NativeAppContext`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `osType` | [`?string(OsType)`](../../doc/models/os-type.md) | Optional | Operating System type of the device that the buyer is using.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `7`, *Pattern*: `^[A-Z_]+$` | getOsType(): ?string | setOsType(?string osType): void |
| `osVersion` | `?string` | Optional | Operating System version of the device that the buyer is using.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `64`, *Pattern*: `^.*$` | getOsVersion(): ?string | setOsVersion(?string osVersion): void |

## Example (as JSON)

```json
{
  "os_type": "ANDROID",
  "os_version": "os_version0"
}
```

