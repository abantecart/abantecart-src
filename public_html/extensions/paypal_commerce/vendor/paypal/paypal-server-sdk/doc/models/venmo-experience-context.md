
# Venmo Experience Context

A resource representing an experience context of vault a venmo account.

## Structure

`VenmoExperienceContext`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `brandName` | `?string` | Optional | The label that overrides the business name in the PayPal account on the PayPal site. The pattern is defined by an external party and supports Unicode.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `127`, *Pattern*: `^.*$` | getBrandName(): ?string | setBrandName(?string brandName): void |
| `shippingPreference` | [`?string(ExperienceContextShippingPreference)`](../../doc/models/experience-context-shipping-preference.md) | Optional | The shipping preference. This only applies to PayPal payment source.<br><br>**Default**: `ExperienceContextShippingPreference::GET_FROM_FILE`<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[0-9A-Z_]+$` | getShippingPreference(): ?string | setShippingPreference(?string shippingPreference): void |
| `vaultInstruction` | [`?string(VaultInstructionAction)`](../../doc/models/vault-instruction-action.md) | Optional | DEPRECATED. Vault Instruction on action to be performed after a successful payer approval.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[A-Z_]+$` | getVaultInstruction(): ?string | setVaultInstruction(?string vaultInstruction): void |
| `userAction` | [`?string(VaultUserAction)`](../../doc/models/vault-user-action.md) | Optional | User Action on action to be performed after a successful payer approval.<br><br>**Default**: `VaultUserAction::CONTINUE_`<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[A-Z_]+$` | getUserAction(): ?string | setUserAction(?string userAction): void |

## Example (as JSON)

```json
{
  "shipping_preference": "GET_FROM_FILE",
  "user_action": "CONTINUE",
  "brand_name": "brand_name0",
  "vault_instruction": "ON_CREATE_PAYMENT_TOKENS"
}
```

