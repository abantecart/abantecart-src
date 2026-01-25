
# Simple Postal Address Coarse Grained

A simple postal address with coarse-grained fields. Do not use for an international address. Use for backward compatibility only. Does not contain phone.

## Structure

`SimplePostalAddressCoarseGrained`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `line1` | `string` | Required | The first line of the address. For example, number or street. | getLine1(): string | setLine1(string line1): void |
| `line2` | `?string` | Optional | The second line of the address. For example, suite or apartment number. | getLine2(): ?string | setLine2(?string line2): void |
| `city` | `string` | Required | The city name. | getCity(): string | setCity(string city): void |
| `state` | `?string` | Optional | The [code](/docs/api/reference/state-codes/) for a US state or the equivalent for other countries. Required for transactions if the address is in one of these countries: [Argentina](/docs/api/reference/state-codes/#argentina), [Brazil](/docs/api/reference/state-codes/#brazil), [Canada](/docs/api/reference/state-codes/#canada), [China](/docs/api/reference/state-codes/#china), [India](/docs/api/reference/state-codes/#india), [Italy](/docs/api/reference/state-codes/#italy), [Japan](/docs/api/reference/state-codes/#japan), [Mexico](/docs/api/reference/state-codes/#mexico), [Thailand](/docs/api/reference/state-codes/#thailand), or [United States](/docs/api/reference/state-codes/#usa). Maximum length is 40 single-byte characters. | getState(): ?string | setState(?string state): void |
| `countryCode` | `string` | Required | The [two-character ISO 3166-1 code](/docs/integration/direct/rest/country-codes/) that identifies the country or region. Note: The country code for Great Britain is GB and not UK as used in the top-level domain names for that country. Use the `C2` country code for China worldwide for comparable uncontrolled price (CUP) method, bank card, and cross-border transactions.<br><br>**Constraints**: *Minimum Length*: `2`, *Maximum Length*: `2`, *Pattern*: `^([A-Z]{2}\|C2)$` | getCountryCode(): string | setCountryCode(string countryCode): void |
| `postalCode` | `?string` | Optional | The postal code, which is the zip code or equivalent. Typically required for countries with a postal code or an equivalent. See [postal code](https://en.wikipedia.org/wiki/Postal_code). | getPostalCode(): ?string | setPostalCode(?string postalCode): void |

## Example (as JSON)

```json
{
  "line1": "line14",
  "line2": "line26",
  "city": "city2",
  "state": "state8",
  "country_code": "country_code2",
  "postal_code": "postal_code4"
}
```

