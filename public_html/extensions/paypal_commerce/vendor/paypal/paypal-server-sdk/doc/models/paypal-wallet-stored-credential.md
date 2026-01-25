
# Paypal Wallet Stored Credential

Provides additional details to process a payment using the PayPal wallet billing agreement or a vaulted payment method that has been stored or is intended to be stored.

## Structure

`PaypalWalletStoredCredential`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `paymentInitiator` | [`string(PaymentInitiator)`](../../doc/models/payment-initiator.md) | Required | The person or party who initiated or triggered the payment.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[0-9A-Z_]+$` | getPaymentInitiator(): string | setPaymentInitiator(string paymentInitiator): void |
| `chargePattern` | [`?string(UsagePattern)`](../../doc/models/usage-pattern.md) | Optional | DEPRECATED. Expected business/pricing model for the billing agreement, Please use usage_pattern instead.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `30`, *Pattern*: `^[A-Z0-9_]+$` | getChargePattern(): ?string | setChargePattern(?string chargePattern): void |
| `usagePattern` | [`?string(UsagePattern)`](../../doc/models/usage-pattern.md) | Optional | Expected business/pricing model for the billing agreement.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `30`, *Pattern*: `^[A-Z0-9_]+$` | getUsagePattern(): ?string | setUsagePattern(?string usagePattern): void |
| `usage` | [`?string(StoredPaymentSourceUsageType)`](../../doc/models/stored-payment-source-usage-type.md) | Optional | Indicates if this is a `first` or `subsequent` payment using a stored payment source (also referred to as stored credential or card on file).<br><br>**Default**: `StoredPaymentSourceUsageType::DERIVED`<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[0-9A-Z_]+$` | getUsage(): ?string | setUsage(?string usage): void |

## Example (as JSON)

```json
{
  "payment_initiator": "CUSTOMER",
  "usage": "DERIVED",
  "charge_pattern": "IMMEDIATE",
  "usage_pattern": "IMMEDIATE"
}
```

