
# Paypal Wallet Vault Response

The details about a saved PayPal Wallet payment source.

## Structure

`PaypalWalletVaultResponse`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `id` | `?string` | Optional | The PayPal-generated ID for the saved payment source.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255` | getId(): ?string | setId(?string id): void |
| `status` | [`?string(PaypalWalletVaultStatus)`](../../doc/models/paypal-wallet-vault-status.md) | Optional | The vault status.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[0-9A-Z_]+$` | getStatus(): ?string | setStatus(?string status): void |
| `links` | [`?(LinkDescription[])`](../../doc/models/link-description.md) | Optional | An array of request-related HATEOAS links.<br><br>**Constraints**: *Minimum Items*: `1`, *Maximum Items*: `10` | getLinks(): ?array | setLinks(?array links): void |
| `customer` | [`?PaypalWalletCustomer`](../../doc/models/paypal-wallet-customer.md) | Optional | The details about a customer in PayPal's system of record. | getCustomer(): ?PaypalWalletCustomer | setCustomer(?PaypalWalletCustomer customer): void |

## Example (as JSON)

```json
{
  "id": "id0",
  "status": "CREATED",
  "links": [
    {
      "href": "href6",
      "rel": "rel0",
      "method": "HEAD"
    }
  ],
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
  }
}
```

