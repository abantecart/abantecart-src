
# Paypal Wallet Response

The PayPal Wallet response.

## Structure

`PaypalWalletResponse`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `emailAddress` | `?string` | Optional | The internationalized email address. Note: Up to 64 characters are allowed before and 255 characters are allowed after the @ sign. However, the generally accepted maximum length for an email address is 254 characters. The pattern verifies that an unquoted @ sign exists.<br><br>**Constraints**: *Minimum Length*: `3`, *Maximum Length*: `254`, *Pattern*: ``(?:[a-zA-Z0-9!#$%&'*+/=?^_`{\|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+/=?^_`{\|}~-]+)*\|(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]\|\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\|\[(?:(?:(2(5[0-5]\|[0-4][0-9])\|1[0-9][0-9]\|[1-9]?[0-9]))\.){3}(?:(2(5[0-5]\|[0-4][0-9])\|1[0-9][0-9]\|[1-9]?[0-9])\|[a-zA-Z0-9-]*[a-zA-Z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]\|\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])`` | getEmailAddress(): ?string | setEmailAddress(?string emailAddress): void |
| `accountId` | `?string` | Optional | The PayPal payer ID, which is a masked version of the PayPal account number intended for use with third parties. The account number is reversibly encrypted and a proprietary variant of Base32 is used to encode the result.<br><br>**Constraints**: *Minimum Length*: `13`, *Maximum Length*: `13`, *Pattern*: `^[2-9A-HJ-NP-Z]{13}$` | getAccountId(): ?string | setAccountId(?string accountId): void |
| `accountStatus` | [`?string(PaypalWalletAccountVerificationStatus)`](../../doc/models/paypal-wallet-account-verification-status.md) | Optional | The account status indicates whether the buyer has verified the financial details associated with their PayPal account.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[A-Z_]+$` | getAccountStatus(): ?string | setAccountStatus(?string accountStatus): void |
| `name` | [`?Name`](../../doc/models/name.md) | Optional | The name of the party. | getName(): ?Name | setName(?Name name): void |
| `phoneType` | [`?string(PhoneType)`](../../doc/models/phone-type.md) | Optional | The phone type. | getPhoneType(): ?string | setPhoneType(?string phoneType): void |
| `phoneNumber` | [`?PhoneNumber`](../../doc/models/phone-number.md) | Optional | The phone number in its canonical international [E.164 numbering plan format](https://www.itu.int/rec/T-REC-E.164/en). | getPhoneNumber(): ?PhoneNumber | setPhoneNumber(?PhoneNumber phoneNumber): void |
| `birthDate` | `?string` | Optional | The stand-alone date, in [Internet date and time format](https://tools.ietf.org/html/rfc3339#section-5.6). To represent special legal values, such as a date of birth, you should use dates with no associated time or time-zone data. Whenever possible, use the standard `date_time` type. This regular expression does not validate all dates. For example, February 31 is valid and nothing is known about leap years.<br><br>**Constraints**: *Minimum Length*: `10`, *Maximum Length*: `10`, *Pattern*: `^[0-9]{4}-(0[1-9]\|1[0-2])-(0[1-9]\|[1-2][0-9]\|3[0-1])$` | getBirthDate(): ?string | setBirthDate(?string birthDate): void |
| `businessName` | `?string` | Optional | The business name of the PayPal account holder (populated for business accounts only)<br><br>**Constraints**: *Minimum Length*: `0`, *Maximum Length*: `300`, *Pattern*: `^.*$` | getBusinessName(): ?string | setBusinessName(?string businessName): void |
| `taxInfo` | [`?TaxInfo`](../../doc/models/tax-info.md) | Optional | The tax ID of the customer. The customer is also known as the payer. Both `tax_id` and `tax_id_type` are required. | getTaxInfo(): ?TaxInfo | setTaxInfo(?TaxInfo taxInfo): void |
| `address` | [`?Address`](../../doc/models/address.md) | Optional | The portable international postal address. Maps to [AddressValidationMetadata](https://github.com/googlei18n/libaddressinput/wiki/AddressValidationMetadata) and HTML 5.1 [Autofilling form controls: the autocomplete attribute](https://www.w3.org/TR/html51/sec-forms.html#autofilling-form-controls-the-autocomplete-attribute). | getAddress(): ?Address | setAddress(?Address address): void |
| `attributes` | [`?PaypalWalletAttributesResponse`](../../doc/models/paypal-wallet-attributes-response.md) | Optional | Additional attributes associated with the use of a PayPal Wallet. | getAttributes(): ?PaypalWalletAttributesResponse | setAttributes(?PaypalWalletAttributesResponse attributes): void |
| `storedCredential` | [`?PaypalWalletStoredCredential`](../../doc/models/paypal-wallet-stored-credential.md) | Optional | Provides additional details to process a payment using the PayPal wallet billing agreement or a vaulted payment method that has been stored or is intended to be stored. | getStoredCredential(): ?PaypalWalletStoredCredential | setStoredCredential(?PaypalWalletStoredCredential storedCredential): void |
| `experienceStatus` | [`?string(ExperienceStatus)`](../../doc/models/experience-status.md) | Optional | This field indicates the status of PayPal's Checkout experience throughout the order lifecycle. The values reflect the current stage of the checkout process.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[A-Z_]+$` | getExperienceStatus(): ?string | setExperienceStatus(?string experienceStatus): void |

## Example (as JSON)

```json
{
  "email_address": "email_address8",
  "account_id": "account_id2",
  "account_status": "VERIFIED",
  "name": {
    "given_name": "given_name2",
    "surname": "surname8"
  },
  "phone_type": "OTHER"
}
```

