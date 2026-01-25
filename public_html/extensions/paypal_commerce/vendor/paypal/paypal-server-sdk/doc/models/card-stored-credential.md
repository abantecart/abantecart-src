
# Card Stored Credential

Provides additional details to process a payment using a `card` that has been stored or is intended to be stored (also referred to as stored_credential or card-on-file). Parameter compatibility: `payment_type=ONE_TIME` is compatible only with `payment_initiator=CUSTOMER`. `usage=FIRST` is compatible only with `payment_initiator=CUSTOMER`. `previous_transaction_reference` or `previous_network_transaction_reference` is compatible only with `payment_initiator=MERCHANT`. Only one of the parameters - `previous_transaction_reference` and `previous_network_transaction_reference` - can be present in the request.

## Structure

`CardStoredCredential`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `paymentInitiator` | [`string(PaymentInitiator)`](../../doc/models/payment-initiator.md) | Required | The person or party who initiated or triggered the payment.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[0-9A-Z_]+$` | getPaymentInitiator(): string | setPaymentInitiator(string paymentInitiator): void |
| `paymentType` | [`string(StoredPaymentSourcePaymentType)`](../../doc/models/stored-payment-source-payment-type.md) | Required | Indicates the type of the stored payment_source payment.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[0-9A-Z_]+$` | getPaymentType(): string | setPaymentType(string paymentType): void |
| `usage` | [`?string(StoredPaymentSourceUsageType)`](../../doc/models/stored-payment-source-usage-type.md) | Optional | Indicates if this is a `first` or `subsequent` payment using a stored payment source (also referred to as stored credential or card on file).<br><br>**Default**: `StoredPaymentSourceUsageType::DERIVED`<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[0-9A-Z_]+$` | getUsage(): ?string | setUsage(?string usage): void |
| `previousNetworkTransactionReference` | [`?NetworkTransaction`](../../doc/models/network-transaction.md) | Optional | Reference values used by the card network to identify a transaction. | getPreviousNetworkTransactionReference(): ?NetworkTransaction | setPreviousNetworkTransactionReference(?NetworkTransaction previousNetworkTransactionReference): void |

## Example (as JSON)

```json
{
  "payment_initiator": "CUSTOMER",
  "payment_type": "ONE_TIME",
  "usage": "DERIVED",
  "previous_network_transaction_reference": {
    "id": "id6",
    "date": "date2",
    "network": "CONFIDIS",
    "acquirer_reference_number": "acquirer_reference_number8"
  }
}
```

