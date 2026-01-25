
# Vault Response

The details about a saved payment source.

## Structure

`VaultResponse`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `id` | `?string` | Optional | The PayPal-generated ID for the saved payment source.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255` | getId(): ?string | setId(?string id): void |
| `status` | [`?string(VaultStatus)`](../../doc/models/vault-status.md) | Optional | The vault status.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[0-9A-Z_]+$` | getStatus(): ?string | setStatus(?string status): void |
| `customer` | [`?VaultCustomer`](../../doc/models/vault-customer.md) | Optional | This object represents a merchant’s customer, allowing them to store contact details, and track all payments associated with the same customer. | getCustomer(): ?VaultCustomer | setCustomer(?VaultCustomer customer): void |
| `links` | [`?(LinkDescription[])`](../../doc/models/link-description.md) | Optional | An array of request-related HATEOAS links.<br><br>**Constraints**: *Minimum Items*: `1`, *Maximum Items*: `10` | getLinks(): ?array | setLinks(?array links): void |

## Example (as JSON)

```json
{
  "id": "id2",
  "status": "CREATED",
  "customer": {
    "id": "id0",
    "name": {
      "given_name": "given_name2",
      "surname": "surname8"
    }
  },
  "links": [
    {
      "href": "href6",
      "rel": "rel0",
      "method": "HEAD"
    }
  ]
}
```

