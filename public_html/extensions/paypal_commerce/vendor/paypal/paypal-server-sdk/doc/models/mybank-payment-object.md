
# Mybank Payment Object

Information used to pay using MyBank.

## Structure

`MybankPaymentObject`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `name` | `?string` | Optional | The full name representation like Mr J Smith.<br><br>**Constraints**: *Minimum Length*: `3`, *Maximum Length*: `300` | getName(): ?string | setName(?string name): void |
| `countryCode` | `?string` | Optional | The [two-character ISO 3166-1 code](/api/rest/reference/country-codes/) that identifies the country or region. Note: The country code for Great Britain is GB and not UK as used in the top-level domain names for that country. Use the `C2` country code for China worldwide for comparable uncontrolled price (CUP) method, bank card, and cross-border transactions.<br><br>**Constraints**: *Minimum Length*: `2`, *Maximum Length*: `2`, *Pattern*: `^([A-Z]{2}\|C2)$` | getCountryCode(): ?string | setCountryCode(?string countryCode): void |
| `bic` | `?string` | Optional | The business identification code (BIC). In payments systems, a BIC is used to identify a specific business, most commonly a bank.<br><br>**Constraints**: *Minimum Length*: `8`, *Maximum Length*: `11`, *Pattern*: `^[A-Z-a-z0-9]{4}[A-Z-a-z]{2}[A-Z-a-z0-9]{2}([A-Z-a-z0-9]{3})?$` | getBic(): ?string | setBic(?string bic): void |
| `ibanLastChars` | `?string` | Optional | The last characters of the IBAN used to pay.<br><br>**Constraints**: *Minimum Length*: `4`, *Maximum Length*: `34`, *Pattern*: `[a-zA-Z0-9]{4}` | getIbanLastChars(): ?string | setIbanLastChars(?string ibanLastChars): void |

## Example (as JSON)

```json
{
  "name": "name4",
  "country_code": "country_code4",
  "bic": "bic6",
  "iban_last_chars": "iban_last_chars2"
}
```

