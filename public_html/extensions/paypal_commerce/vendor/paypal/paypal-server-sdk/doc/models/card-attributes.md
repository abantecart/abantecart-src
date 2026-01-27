
# Card Attributes

Additional attributes associated with the use of this card.

## Structure

`CardAttributes`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `customer` | [`?CardCustomerInformation`](../../doc/models/card-customer-information.md) | Optional | The details about a customer in PayPal's system of record. | getCustomer(): ?CardCustomerInformation | setCustomer(?CardCustomerInformation customer): void |
| `vault` | [`?VaultInstructionBase`](../../doc/models/vault-instruction-base.md) | Optional | Basic vault instruction specification that can be extended by specific payment sources that supports vaulting. | getVault(): ?VaultInstructionBase | setVault(?VaultInstructionBase vault): void |
| `verification` | [`?CardVerification`](../../doc/models/card-verification.md) | Optional | The API caller can opt in to verify the card through PayPal offered verification services (e.g. Smart Dollar Auth, 3DS). | getVerification(): ?CardVerification | setVerification(?CardVerification verification): void |

## Example (as JSON)

```json
{
  "customer": {
    "id": "id0",
    "email_address": "email_address2",
    "phone": {
      "phone_type": "OTHER",
      "phone_number": {
        "national_number": "national_number6"
      }
    },
    "name": {
      "given_name": "given_name2",
      "surname": "surname8"
    },
    "merchant_customer_id": "merchant_customer_id2"
  },
  "vault": {
    "store_in_vault": "ON_SUCCESS"
  },
  "verification": {
    "method": "3D_SECURE"
  }
}
```

