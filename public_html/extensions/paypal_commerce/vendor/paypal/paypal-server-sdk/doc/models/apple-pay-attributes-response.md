
# Apple Pay Attributes Response

Additional attributes associated with the use of Apple Pay.

## Structure

`ApplePayAttributesResponse`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `vault` | [`?VaultResponse`](../../doc/models/vault-response.md) | Optional | The details about a saved payment source. | getVault(): ?VaultResponse | setVault(?VaultResponse vault): void |

## Example (as JSON)

```json
{
  "vault": {
    "id": "id6",
    "status": "APPROVED",
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
}
```

