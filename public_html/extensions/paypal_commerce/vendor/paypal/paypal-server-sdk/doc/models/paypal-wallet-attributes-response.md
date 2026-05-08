
# Paypal Wallet Attributes Response

Additional attributes associated with the use of a PayPal Wallet.

## Structure

`PaypalWalletAttributesResponse`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `vault` | [`?PaypalWalletVaultResponse`](../../doc/models/paypal-wallet-vault-response.md) | Optional | The details about a saved PayPal Wallet payment source. | getVault(): ?PaypalWalletVaultResponse | setVault(?PaypalWalletVaultResponse vault): void |
| `cobrandedCards` | [`?(CobrandedCard[])`](../../doc/models/cobranded-card.md) | Optional | An array of merchant cobranded cards used by buyer to complete an order. This array will be present if a merchant has onboarded their cobranded card with PayPal and provided corresponding label(s).<br><br>**Constraints**: *Minimum Items*: `0`, *Maximum Items*: `25` | getCobrandedCards(): ?array | setCobrandedCards(?array cobrandedCards): void |

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
  },
  "cobranded_cards": [
    {
      "labels": [
        "labels4",
        "labels3"
      ],
      "payee": {
        "email_address": "email_address4",
        "merchant_id": "merchant_id6"
      },
      "amount": {
        "currency_code": "currency_code6",
        "value": "value0"
      }
    },
    {
      "labels": [
        "labels4",
        "labels3"
      ],
      "payee": {
        "email_address": "email_address4",
        "merchant_id": "merchant_id6"
      },
      "amount": {
        "currency_code": "currency_code6",
        "value": "value0"
      }
    }
  ]
}
```

