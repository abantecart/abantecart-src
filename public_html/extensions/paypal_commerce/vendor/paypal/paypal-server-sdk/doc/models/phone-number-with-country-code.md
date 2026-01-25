
# Phone Number With Country Code

The phone number in its canonical international [E.164 numbering plan format](https://www.itu.int/rec/T-REC-E.164/en)., The phone number, in its canonical international [E.164 numbering plan format](https://www.itu.int/rec/T-REC-E.164/en)., The phone number, in its canonical international [E.164 numbering plan format](https://www.itu.int/rec/T-REC-E.164/en).

## Structure

`PhoneNumberWithCountryCode`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `countryCode` | `string` | Required | The country calling code (CC), in its canonical international [E.164 numbering plan format](https://www.itu.int/rec/T-REC-E.164/en). The combined length of the CC and the national number must not be greater than 15 digits. The national number consists of a national destination code (NDC) and subscriber number (SN).<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `3`, *Pattern*: `^[0-9]{1,3}?$` | getCountryCode(): string | setCountryCode(string countryCode): void |
| `nationalNumber` | `string` | Required | The national number, in its canonical international [E.164 numbering plan format](https://www.itu.int/rec/T-REC-E.164/en). The combined length of the country calling code (CC) and the national number must not be greater than 15 digits. The national number consists of a national destination code (NDC) and subscriber number (SN).<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `14`, *Pattern*: `^[0-9]{1,14}?$` | getNationalNumber(): string | setNationalNumber(string nationalNumber): void |

## Example (as JSON)

```json
{
  "country_code": "country_code4",
  "national_number": "national_number8"
}
```

