
# Vault Instruction

Base vaulting specification. The object can be extended for specific use cases within each payment_source that supports vaulting.

## Structure

`VaultInstruction`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `storeInVault` | [`string(StoreInVaultInstruction)`](../../doc/models/store-in-vault-instruction.md) | Required | Defines how and when the payment source gets vaulted.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[0-9A-Z_]+$` | getStoreInVault(): string | setStoreInVault(string storeInVault): void |

## Example (as JSON)

```json
{
  "store_in_vault": "ON_SUCCESS"
}
```

