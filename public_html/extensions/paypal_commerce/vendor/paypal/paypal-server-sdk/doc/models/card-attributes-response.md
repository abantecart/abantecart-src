
# Card Attributes Response

Additional attributes associated with the use of this card.

## Structure

`CardAttributesResponse`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `vault` | [`?CardVaultResponse`](../../doc/models/card-vault-response.md) | Optional | The details about a saved Card payment source. | getVault(): ?CardVaultResponse | setVault(?CardVaultResponse vault): void |

## Example (as JSON)

```json
{
  "vault": {
    "id": "id6",
    "status": "APPROVED",
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
}
```

