
# Bin Details

Bank Identification Number (BIN) details used to fund a payment.

## Structure

`BinDetails`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `bin` | `?string` | Optional | The Bank Identification Number (BIN) signifies the number that is being used to identify the granular level details (except the PII information) of the card.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `25`, *Pattern*: `^[0-9]+$` | getBin(): ?string | setBin(?string bin): void |
| `issuingBank` | `?string` | Optional | The issuer of the card instrument.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `64` | getIssuingBank(): ?string | setIssuingBank(?string issuingBank): void |
| `binCountryCode` | `?string` | Optional | The [two-character ISO 3166-1 code](/api/rest/reference/country-codes/) that identifies the country or region. Note: The country code for Great Britain is GB and not UK as used in the top-level domain names for that country. Use the `C2` country code for China worldwide for comparable uncontrolled price (CUP) method, bank card, and cross-border transactions.<br><br>**Constraints**: *Minimum Length*: `2`, *Maximum Length*: `2`, *Pattern*: `^([A-Z]{2}\|C2)$` | getBinCountryCode(): ?string | setBinCountryCode(?string binCountryCode): void |
| `products` | `?(string[])` | Optional | The type of card product assigned to the BIN by the issuer. These values are defined by the issuer and may change over time. Some examples include: PREPAID_GIFT, CONSUMER, CORPORATE.<br><br>**Constraints**: *Minimum Items*: `1`, *Maximum Items*: `256`, *Minimum Length*: `1`, *Maximum Length*: `255` | getProducts(): ?array | setProducts(?array products): void |

## Example (as JSON)

```json
{
  "bin": "bin0",
  "issuing_bank": "issuing_bank0",
  "bin_country_code": "bin_country_code4",
  "products": [
    "products8",
    "products9",
    "products0"
  ]
}
```

