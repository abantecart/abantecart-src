
# Venmo Wallet Response

Venmo wallet response.

## Structure

`VenmoWalletResponse`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `emailAddress` | `?string` | Optional | The internationalized email address. Note: Up to 64 characters are allowed before and 255 characters are allowed after the @ sign. However, the generally accepted maximum length for an email address is 254 characters. The pattern verifies that an unquoted @ sign exists.<br><br>**Constraints**: *Minimum Length*: `3`, *Maximum Length*: `254`, *Pattern*: ``(?:[a-zA-Z0-9!#$%&'*+/=?^_`{\|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+/=?^_`{\|}~-]+)*\|(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]\|\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\|\[(?:(?:(2(5[0-5]\|[0-4][0-9])\|1[0-9][0-9]\|[1-9]?[0-9]))\.){3}(?:(2(5[0-5]\|[0-4][0-9])\|1[0-9][0-9]\|[1-9]?[0-9])\|[a-zA-Z0-9-]*[a-zA-Z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]\|\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])`` | getEmailAddress(): ?string | setEmailAddress(?string emailAddress): void |
| `accountId` | `?string` | Optional | The PayPal payer ID, which is a masked version of the PayPal account number intended for use with third parties. The account number is reversibly encrypted and a proprietary variant of Base32 is used to encode the result.<br><br>**Constraints**: *Minimum Length*: `13`, *Maximum Length*: `13`, *Pattern*: `^[2-9A-HJ-NP-Z]{13}$` | getAccountId(): ?string | setAccountId(?string accountId): void |
| `userName` | `?string` | Optional | The Venmo user name chosen by the user, also know as a Venmo handle.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `50`, *Pattern*: `^[-a-zA-Z0-9_]*$` | getUserName(): ?string | setUserName(?string userName): void |
| `name` | [`?Name`](../../doc/models/name.md) | Optional | The name of the party. | getName(): ?Name | setName(?Name name): void |
| `phoneNumber` | [`?PhoneNumber`](../../doc/models/phone-number.md) | Optional | The phone number in its canonical international [E.164 numbering plan format](https://www.itu.int/rec/T-REC-E.164/en). | getPhoneNumber(): ?PhoneNumber | setPhoneNumber(?PhoneNumber phoneNumber): void |
| `address` | [`?Address`](../../doc/models/address.md) | Optional | The portable international postal address. Maps to [AddressValidationMetadata](https://github.com/googlei18n/libaddressinput/wiki/AddressValidationMetadata) and HTML 5.1 [Autofilling form controls: the autocomplete attribute](https://www.w3.org/TR/html51/sec-forms.html#autofilling-form-controls-the-autocomplete-attribute). | getAddress(): ?Address | setAddress(?Address address): void |
| `returnFlow` | [`?string(ReturnFlow)`](../../doc/models/return-flow.md) | Optional | Merchant preference on how the buyer can navigate back to merchant website post approving the transaction on the Venmo App.<br><br>**Default**: `ReturnFlow::AUTO`<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `6`, *Pattern*: `^[A-Z_]+$` | getReturnFlow(): ?string | setReturnFlow(?string returnFlow): void |
| `attributes` | [`?VenmoWalletAttributesResponse`](../../doc/models/venmo-wallet-attributes-response.md) | Optional | Additional attributes associated with the use of a Venmo Wallet. | getAttributes(): ?VenmoWalletAttributesResponse | setAttributes(?VenmoWalletAttributesResponse attributes): void |

## Example (as JSON)

```json
{
  "return_flow": "AUTO",
  "email_address": "email_address6",
  "account_id": "account_id8",
  "user_name": "user_name2",
  "name": {
    "given_name": "given_name2",
    "surname": "surname8"
  },
  "phone_number": {
    "national_number": "national_number6"
  }
}
```

