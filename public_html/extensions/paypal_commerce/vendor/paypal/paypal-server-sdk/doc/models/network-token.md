
# Network Token

The Third Party Network token used to fund a payment.

## Structure

`NetworkToken`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `number` | `string` | Required | Third party network token number.<br><br>**Constraints**: *Minimum Length*: `13`, *Maximum Length*: `19`, *Pattern*: `^[0-9]{13,19}$` | getNumber(): string | setNumber(string number): void |
| `expiry` | `string` | Required | The year and month, in ISO-8601 `YYYY-MM` date format. See [Internet date and time format](https://tools.ietf.org/html/rfc3339#section-5.6).<br><br>**Constraints**: *Minimum Length*: `7`, *Maximum Length*: `7`, *Pattern*: `^[0-9]{4}-(0[1-9]\|1[0-2])$` | getExpiry(): string | setExpiry(string expiry): void |
| `cryptogram` | `?string` | Optional | An Encrypted one-time use value that's sent along with Network Token. This field is not required to be present for recurring transactions.<br><br>**Constraints**: *Minimum Length*: `28`, *Maximum Length*: `32`, *Pattern*: `^.*$` | getCryptogram(): ?string | setCryptogram(?string cryptogram): void |
| `eciFlag` | [`?string(EciFlag)`](../../doc/models/eci-flag.md) | Optional | Electronic Commerce Indicator (ECI). The ECI value is part of the 2 data elements that indicate the transaction was processed electronically. This should be passed on the authorization transaction to the Gateway/Processor.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[0-9A-Z_]+$` | getEciFlag(): ?string | setEciFlag(?string eciFlag): void |
| `tokenRequestorId` | `?string` | Optional | A TRID, or a Token Requestor ID, is an identifier used by merchants to request network tokens from card networks. A TRID is a precursor to obtaining a network token for a credit card primary account number (PAN), and will aid in enabling secure card on file (COF) payments and reducing fraud.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `11`, *Pattern*: `^[0-9A-Z_]+$` | getTokenRequestorId(): ?string | setTokenRequestorId(?string tokenRequestorId): void |

## Example (as JSON)

```json
{
  "number": "number0",
  "expiry": "expiry0",
  "cryptogram": "cryptogram2",
  "eci_flag": "ATTEMPTED_AUTHENTICATION_TRANSACTION",
  "token_requestor_id": "token_requestor_id8"
}
```

