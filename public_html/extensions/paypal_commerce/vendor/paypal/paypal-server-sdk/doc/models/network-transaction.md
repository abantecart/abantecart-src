
# Network Transaction

Reference values used by the card network to identify a transaction.

## Structure

`NetworkTransaction`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `id` | `?string` | Optional | Transaction reference id returned by the scheme. For Visa and Amex, this is the "Tran id" field in response. For MasterCard, this is the "BankNet reference id" field in response. For Discover, this is the "NRID" field in response. The pattern we expect for this field from Visa/Amex/CB/Discover is numeric, Mastercard/BNPP is alphanumeric and Paysecure is alphanumeric with special character -.<br><br>**Constraints**: *Minimum Length*: `9`, *Maximum Length*: `36`, *Pattern*: `^[a-zA-Z0-9-_@.:&+=*^'~#!$%()]+$` | getId(): ?string | setId(?string id): void |
| `date` | `?string` | Optional | The date that the transaction was authorized by the scheme. This field may not be returned for all networks. MasterCard refers to this field as "BankNet reference date". For some specific networks, such as MasterCard and Discover, this date field is mandatory when the `previous_network_transaction_reference_id` is passed.<br><br>**Constraints**: *Minimum Length*: `4`, *Maximum Length*: `4`, *Pattern*: `^[0-9]+$` | getDate(): ?string | setDate(?string date): void |
| `network` | [`?string(CardBrand)`](../../doc/models/card-brand.md) | Optional | The card network or brand. Applies to credit, debit, gift, and payment cards.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[A-Z_]+$` | getNetwork(): ?string | setNetwork(?string network): void |
| `acquirerReferenceNumber` | `?string` | Optional | Reference ID issued for the card transaction. This ID can be used to track the transaction across processors, card brands and issuing banks.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `36`, *Pattern*: `^[a-zA-Z0-9]+$` | getAcquirerReferenceNumber(): ?string | setAcquirerReferenceNumber(?string acquirerReferenceNumber): void |

## Example (as JSON)

```json
{
  "id": "id0",
  "date": "date4",
  "network": "CETELEM",
  "acquirer_reference_number": "acquirer_reference_number8"
}
```

