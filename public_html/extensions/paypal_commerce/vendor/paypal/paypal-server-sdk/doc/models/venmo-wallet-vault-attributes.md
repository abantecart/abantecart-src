
# Venmo Wallet Vault Attributes

Resource consolidating common request and response attirbutes for vaulting Venmo Wallet.

## Structure

`VenmoWalletVaultAttributes`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `storeInVault` | [`string(StoreInVaultInstruction)`](../../doc/models/store-in-vault-instruction.md) | Required | Defines how and when the payment source gets vaulted.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[0-9A-Z_]+$` | getStoreInVault(): string | setStoreInVault(string storeInVault): void |
| `description` | `?string` | Optional | The description displayed to Venmo consumer on the approval flow for Venmo, as well as on the Venmo payment token management experience on Venmo.com.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `128`, *Pattern*: `^[a-zA-Z0-9_'\-., :;\!?"]*$` | getDescription(): ?string | setDescription(?string description): void |
| `usagePattern` | [`?string(VenmoPaymentTokenUsagePattern)`](../../doc/models/venmo-payment-token-usage-pattern.md) | Optional | Expected business/pricing model for the billing agreement.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `30`, *Pattern*: `^[0-9A-Z_]+$` | getUsagePattern(): ?string | setUsagePattern(?string usagePattern): void |
| `usageType` | [`string(VenmoPaymentTokenUsageType)`](../../doc/models/venmo-payment-token-usage-type.md) | Required | The usage type associated with the Venmo payment token.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[0-9A-Z_]+$` | getUsageType(): string | setUsageType(string usageType): void |
| `customerType` | [`?string(VenmoPaymentTokenCustomerType)`](../../doc/models/venmo-payment-token-customer-type.md) | Optional | The customer type associated with the Venmo payment token. This is to indicate whether the customer acting on the merchant / platform is either a business or a consumer.<br><br>**Default**: `VenmoPaymentTokenCustomerType::CONSUMER`<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[0-9A-Z_]+$` | getCustomerType(): ?string | setCustomerType(?string customerType): void |
| `permitMultiplePaymentTokens` | `?bool` | Optional | Create multiple payment tokens for the same payer, merchant/platform combination. Use this when the customer has not logged in at merchant/platform. The payment token thus generated, can then also be used to create the customer account at merchant/platform. Use this also when multiple payment tokens are required for the same payer, different customer at merchant/platform. This helps to identify customers distinctly even though they may share the same Venmo account.<br><br>**Default**: `false` | getPermitMultiplePaymentTokens(): ?bool | setPermitMultiplePaymentTokens(?bool permitMultiplePaymentTokens): void |

## Example (as JSON)

```json
{
  "store_in_vault": "ON_SUCCESS",
  "usage_type": "MERCHANT",
  "customer_type": "CONSUMER",
  "permit_multiple_payment_tokens": false,
  "description": "description6",
  "usage_pattern": "RECURRING_PREPAID"
}
```

