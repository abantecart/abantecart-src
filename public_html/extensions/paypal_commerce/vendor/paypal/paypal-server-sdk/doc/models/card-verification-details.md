
# Card Verification Details

Card Verification details including the authorization details and 3D SECURE details.

## Structure

`CardVerificationDetails`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `networkTransactionId` | `?string` | Optional | DEPRECATED. This field is DEPRECATED. Please find the network transaction id data in the 'id' field under the 'network_transaction_reference' object instead of the 'verification' object.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `1024`, *Pattern*: `^[a-zA-Z0-9-_@.:&+=*^'~#!$%()]+$` | getNetworkTransactionId(): ?string | setNetworkTransactionId(?string networkTransactionId): void |
| `date` | `?string` | Optional | DEPRECATED. This field is DEPRECATED. Please find the date data in the 'date' field under the 'network_transaction_reference' object instead of the 'verification' object.<br><br>**Constraints**: *Minimum Length*: `4`, *Maximum Length*: `4`, *Pattern*: `^[0-9]+$` | getDate(): ?string | setDate(?string date): void |
| `network` | [`?string(CardBrand)`](../../doc/models/card-brand.md) | Optional | DEPRECATED. This field is DEPRECATED. Please find the network data in the 'network' field under the 'network_transaction_reference' object instead of the 'verification' object.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[A-Z_]+$` | getNetwork(): ?string | setNetwork(?string network): void |
| `time` | `?string` | Optional | DEPRECATED. This field is DEPRECATED. Please find the time data in the 'time' field under the 'network_transaction_reference' object instead of the 'verification' object.<br><br>**Constraints**: *Minimum Length*: `20`, *Maximum Length*: `64`, *Pattern*: `^[0-9]{4}-(0[1-9]\|1[0-2])-(0[1-9]\|[1-2][0-9]\|3[0-1])[T,t]([0-1][0-9]\|2[0-3]):[0-5][0-9]:([0-5][0-9]\|60)([.][0-9]+)?([Zz]\|[+-][0-9]{2}:[0-9]{2})$` | getTime(): ?string | setTime(?string time): void |
| `amount` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getAmount(): ?Money | setAmount(?Money amount): void |
| `processorResponse` | [`?CardVerificationProcessorResponse`](../../doc/models/card-verification-processor-response.md) | Optional | The processor response information for payment requests, such as direct credit card transactions. | getProcessorResponse(): ?CardVerificationProcessorResponse | setProcessorResponse(?CardVerificationProcessorResponse processorResponse): void |
| `threeDSecure` | `mixed` | Optional | DEPRECATED. This field is DEPRECATED. Please find the 3D secure authentication data in the 'three_d_secure' object under the 'authentication_result' object instead of the 'verification' object. | getThreeDSecure(): | setThreeDSecure( threeDSecure): void |

## Example (as JSON)

```json
{
  "network_transaction_id": "network_transaction_id4",
  "date": "date8",
  "network": "ACCEL",
  "time": "time2",
  "amount": {
    "currency_code": "currency_code6",
    "value": "value0"
  }
}
```

