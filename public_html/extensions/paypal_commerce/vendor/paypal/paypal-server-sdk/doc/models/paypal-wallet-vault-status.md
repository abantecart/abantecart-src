
# Paypal Wallet Vault Status

The vault status.

## Enumeration

`PaypalWalletVaultStatus`

## Fields

| Name | Description |
|  --- | --- |
| `VAULTED` | The payment source has been saved in your customer's vault. This vault status reflects `/v3/vault` status. |
| `CREATED` | DEPRECATED. The payment source has been saved in your customer's vault. This status applies to deprecated integration patterns and will not be returned for v3/vault integrations. |
| `APPROVED` | Customer has approved the action of saving the specified payment_source into their vault. Use v3/vault/payment-tokens with given setup_token to save the payment source in the vault |

