
# Tax Info

The tax ID of the customer. The customer is also known as the payer. Both `tax_id` and `tax_id_type` are required.

## Structure

`TaxInfo`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `taxId` | `string` | Required | The customer's tax ID value.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `14`, *Pattern*: `([a-zA-Z0-9])` | getTaxId(): string | setTaxId(string taxId): void |
| `taxIdType` | [`string(TaxIdType)`](../../doc/models/tax-id-type.md) | Required | The customer's tax ID type.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `14`, *Pattern*: `^[A-Z0-9_]+$` | getTaxIdType(): string | setTaxIdType(string taxIdType): void |

## Example (as JSON)

```json
{
  "tax_id": "tax_id0",
  "tax_id_type": "BR_CPF"
}
```

