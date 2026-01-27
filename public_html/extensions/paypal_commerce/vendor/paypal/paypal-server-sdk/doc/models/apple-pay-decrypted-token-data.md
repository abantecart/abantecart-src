
# Apple Pay Decrypted Token Data

Information about the Payment data obtained by decrypting Apple Pay token.

## Structure

`ApplePayDecryptedTokenData`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `transactionAmount` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getTransactionAmount(): ?Money | setTransactionAmount(?Money transactionAmount): void |
| `tokenizedCard` | [`ApplePayTokenizedCard`](../../doc/models/apple-pay-tokenized-card.md) | Required | The payment card to use to fund a payment. Can be a credit or debit card. | getTokenizedCard(): ApplePayTokenizedCard | setTokenizedCard(ApplePayTokenizedCard tokenizedCard): void |
| `deviceManufacturerId` | `?string` | Optional | Apple Pay Hex-encoded device manufacturer identifier. The pattern is defined by an external party and supports Unicode.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `2000`, *Pattern*: `^.*$` | getDeviceManufacturerId(): ?string | setDeviceManufacturerId(?string deviceManufacturerId): void |
| `paymentDataType` | [`?string(ApplePayPaymentDataType)`](../../doc/models/apple-pay-payment-data-type.md) | Optional | Indicates the type of payment data passed, in case of Non China the payment data is 3DSECURE and for China it is EMV.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `16`, *Pattern*: `^[0-9A-Z_]+$` | getPaymentDataType(): ?string | setPaymentDataType(?string paymentDataType): void |
| `paymentData` | [`?ApplePayPaymentData`](../../doc/models/apple-pay-payment-data.md) | Optional | Information about the decrypted apple pay payment data for the token like cryptogram, eci indicator. | getPaymentData(): ?ApplePayPaymentData | setPaymentData(?ApplePayPaymentData paymentData): void |

## Example (as JSON)

```json
{
  "transaction_amount": {
    "currency_code": "currency_code6",
    "value": "value2"
  },
  "tokenized_card": {
    "name": "name4",
    "number": "number2",
    "expiry": "expiry2",
    "card_type": "VISA",
    "type": "UNKNOWN"
  },
  "device_manufacturer_id": "device_manufacturer_id2",
  "payment_data_type": "3DSECURE",
  "payment_data": {
    "cryptogram": "cryptogram6",
    "eci_indicator": "eci_indicator0",
    "emv_data": "emv_data0",
    "pin": "pin4"
  }
}
```

