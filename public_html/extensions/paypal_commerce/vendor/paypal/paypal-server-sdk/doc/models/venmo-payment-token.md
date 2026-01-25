
# Venmo Payment Token

Full representation of a Venmo Payment Token.

## Structure

`VenmoPaymentToken`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `description` | `?string` | Optional | The description displayed to the consumer on the approval flow for a digital wallet, as well as on the merchant view of the payment token management experience. exp: PayPal.com.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `128`, *Pattern*: `^.*$` | getDescription(): ?string | setDescription(?string description): void |
| `usagePattern` | [`?string(UsagePattern)`](../../doc/models/usage-pattern.md) | Optional | Expected business/charge model for the billing agreement.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `30`, *Pattern*: `^[0-9A-Z_]+$` | getUsagePattern(): ?string | setUsagePattern(?string usagePattern): void |
| `shipping` | [`?VaultedDigitalWalletShippingDetails`](../../doc/models/vaulted-digital-wallet-shipping-details.md) | Optional | The shipping details. | getShipping(): ?VaultedDigitalWalletShippingDetails | setShipping(?VaultedDigitalWalletShippingDetails shipping): void |
| `permitMultiplePaymentTokens` | `?bool` | Optional | Create multiple payment tokens for the same payer, merchant/platform combination. Use this when the customer has not logged in at merchant/platform. The payment token thus generated, can then also be used to create the customer account at merchant/platform. Use this also when multiple payment tokens are required for the same payer, different customer at merchant/platform. This helps to identify customers distinctly even though they may share the same PayPal account. This only applies to PayPal payment source.<br><br>**Default**: `false` | getPermitMultiplePaymentTokens(): ?bool | setPermitMultiplePaymentTokens(?bool permitMultiplePaymentTokens): void |
| `usageType` | [`?string(PaypalPaymentTokenUsageType)`](../../doc/models/paypal-payment-token-usage-type.md) | Optional | The usage type associated with a digital wallet payment token.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[0-9A-Z_]+$` | getUsageType(): ?string | setUsageType(?string usageType): void |
| `customerType` | [`?string(PaypalPaymentTokenCustomerType)`](../../doc/models/paypal-payment-token-customer-type.md) | Optional | The customer type associated with a digital wallet payment token. This is to indicate whether the customer acting on the merchant / platform is either a business or a consumer.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[0-9A-Z_]+$` | getCustomerType(): ?string | setCustomerType(?string customerType): void |
| `emailAddress` | `?string` | Optional | The internationalized email address.<blockquote><strong>Note:</strong> Up to 64 characters are allowed before and 255 characters are allowed after the <code>@</code> sign. However, the generally accepted maximum length for an email address is 254 characters. The pattern verifies that an unquoted <code>@</code> sign exists.</blockquote><br><br>**Constraints**: *Minimum Length*: `3`, *Maximum Length*: `254`, *Pattern*: ``(?:[a-zA-Z0-9!#$%&'*+/=?^_`{\|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+/=?^_`{\|}~-]+)*\|(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]\|\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\|\[(?:(?:(2(5[0-5]\|[0-4][0-9])\|1[0-9][0-9]\|[1-9]?[0-9]))\.){3}(?:(2(5[0-5]\|[0-4][0-9])\|1[0-9][0-9]\|[1-9]?[0-9])\|[a-zA-Z0-9-]*[a-zA-Z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]\|\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])`` | getEmailAddress(): ?string | setEmailAddress(?string emailAddress): void |
| `payerId` | `?string` | Optional | The account identifier for a PayPal account.<br><br>**Constraints**: *Minimum Length*: `13`, *Maximum Length*: `13`, *Pattern*: `^[2-9A-HJ-NP-Z]{13}$` | getPayerId(): ?string | setPayerId(?string payerId): void |
| `name` | [`?Name`](../../doc/models/name.md) | Optional | The name of the party. | getName(): ?Name | setName(?Name name): void |
| `phone` | [`?PhoneWithType`](../../doc/models/phone-with-type.md) | Optional | The phone information. | getPhone(): ?PhoneWithType | setPhone(?PhoneWithType phone): void |
| `address` | [`?Address`](../../doc/models/address.md) | Optional | The portable international postal address. Maps to [AddressValidationMetadata](https://github.com/googlei18n/libaddressinput/wiki/AddressValidationMetadata) and HTML 5.1 [Autofilling form controls: the autocomplete attribute](https://www.w3.org/TR/html51/sec-forms.html#autofilling-form-controls-the-autocomplete-attribute). | getAddress(): ?Address | setAddress(?Address address): void |
| `userName` | `?string` | Optional | The Venmo username, as chosen by the user.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `50`, *Pattern*: `^[-a-zA-Z0-9_]*$` | getUserName(): ?string | setUserName(?string userName): void |

## Example (as JSON)

```json
{
  "permit_multiple_payment_tokens": false,
  "description": "description4",
  "usage_pattern": "INSTALLMENT_PREPAID",
  "shipping": {
    "name": {
      "full_name": "full_name6"
    },
    "email_address": "email_address2",
    "phone_number": {
      "country_code": "country_code2",
      "national_number": "national_number6"
    },
    "type": "SHIPPING",
    "address": {
      "address_line_1": "address_line_16",
      "address_line_2": "address_line_26",
      "admin_area_2": "admin_area_20",
      "admin_area_1": "admin_area_12",
      "postal_code": "postal_code8",
      "country_code": "country_code6"
    }
  },
  "usage_type": "MERCHANT"
}
```

