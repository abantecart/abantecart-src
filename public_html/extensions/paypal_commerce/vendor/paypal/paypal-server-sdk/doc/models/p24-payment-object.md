
# P24 Payment Object

Information used to pay using P24(Przelewy24).

## Structure

`P24PaymentObject`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `name` | `?string` | Optional | The full name representation like Mr J Smith.<br><br>**Constraints**: *Minimum Length*: `3`, *Maximum Length*: `300` | getName(): ?string | setName(?string name): void |
| `email` | `?string` | Optional | The internationalized email address. Note: Up to 64 characters are allowed before and 255 characters are allowed after the @ sign. However, the generally accepted maximum length for an email address is 254 characters. The pattern verifies that an unquoted @ sign exists.<br><br>**Constraints**: *Minimum Length*: `3`, *Maximum Length*: `254`, *Pattern*: ``^(?:[A-Za-z0-9!#$%&'*+/=?^_`{\|}~-]+(?:\.[A-Za-z0-9!#$%&'*+/=?^_`{\|}~-]+)*\|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]\|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[A-Za-z0-9](?:[A-Za-z0-9-]*[A-Za-z0-9])?\.)+[A-Za-z0-9](?:[A-Za-z0-9-]*[A-Za-z0-9])?\|\[(?:(?:25[0-5]\|2[0-4][0-9]\|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]\|2[0-4][0-9]\|[01]?[0-9][0-9]?\|[A-Za-z0-9-]*[A-Za-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]\|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])$`` | getEmail(): ?string | setEmail(?string email): void |
| `countryCode` | `?string` | Optional | The [two-character ISO 3166-1 code](/api/rest/reference/country-codes/) that identifies the country or region. Note: The country code for Great Britain is GB and not UK as used in the top-level domain names for that country. Use the `C2` country code for China worldwide for comparable uncontrolled price (CUP) method, bank card, and cross-border transactions.<br><br>**Constraints**: *Minimum Length*: `2`, *Maximum Length*: `2`, *Pattern*: `^([A-Z]{2}\|C2)$` | getCountryCode(): ?string | setCountryCode(?string countryCode): void |
| `paymentDescriptor` | `?string` | Optional | P24 generated payment description.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `2000` | getPaymentDescriptor(): ?string | setPaymentDescriptor(?string paymentDescriptor): void |
| `methodId` | `?string` | Optional | Numeric identifier of the payment scheme or bank used for the payment.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `300` | getMethodId(): ?string | setMethodId(?string methodId): void |
| `methodDescription` | `?string` | Optional | Friendly name of the payment scheme or bank used for the payment.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `2000` | getMethodDescription(): ?string | setMethodDescription(?string methodDescription): void |

## Example (as JSON)

```json
{
  "name": "name4",
  "email": "email2",
  "country_code": "country_code4",
  "payment_descriptor": "payment_descriptor8",
  "method_id": "method_id8"
}
```

