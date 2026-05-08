
# Paypal Wallet Vault Instruction

## Structure

`PaypalWalletVaultInstruction`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `storeInVault` | [`?string(StoreInVaultInstruction)`](../../doc/models/store-in-vault-instruction.md) | Optional | Defines how and when the payment source gets vaulted.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[0-9A-Z_]+$` | getStoreInVault(): ?string | setStoreInVault(?string storeInVault): void |
| `description` | `?string` | Optional | The description displayed to PayPal consumer on the approval flow for PayPal, as well as on the PayPal payment token management experience on PayPal.com.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `128` | getDescription(): ?string | setDescription(?string description): void |
| `usagePattern` | [`?string(UsagePattern)`](../../doc/models/usage-pattern.md) | Optional | Expected business/pricing model for the billing agreement.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `30` | getUsagePattern(): ?string | setUsagePattern(?string usagePattern): void |
| `usageType` | [`string(PaypalPaymentTokenUsageType)`](../../doc/models/paypal-payment-token-usage-type.md) | Required | The usage type associated with the PayPal payment token.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[0-9A-Z_]+$` | getUsageType(): string | setUsageType(string usageType): void |
| `customerType` | [`?string(PaypalPaymentTokenCustomerType)`](../../doc/models/paypal-payment-token-customer-type.md) | Optional | The customer type associated with the PayPal payment token. This is to indicate whether the customer acting on the merchant / platform is either a business or a consumer.<br><br>**Default**: `PaypalPaymentTokenCustomerType::CONSUMER`<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[0-9A-Z_]+$` | getCustomerType(): ?string | setCustomerType(?string customerType): void |
| `permitMultiplePaymentTokens` | `?bool` | Optional | Create multiple payment tokens for the same payer, merchant/platform combination. Use this when the customer has not logged in at merchant/platform. The payment token thus generated, can then also be used to create the customer account at merchant/platform. Use this also when multiple payment tokens are required for the same payer, different customer at merchant/platform. This helps to identify customers distinctly even though they may share the same PayPal account. This only applies to PayPal payment source.<br><br>**Default**: `false` | getPermitMultiplePaymentTokens(): ?bool | setPermitMultiplePaymentTokens(?bool permitMultiplePaymentTokens): void |

## Example (as JSON)

```json
{
  "usage_type": "MERCHANT",
  "customer_type": "CONSUMER",
  "permit_multiple_payment_tokens": false,
  "store_in_vault": "ON_SUCCESS",
  "description": "description4",
  "usage_pattern": "UNSCHEDULED_PREPAID"
}
```

