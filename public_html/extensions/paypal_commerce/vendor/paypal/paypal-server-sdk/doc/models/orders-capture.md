
# Orders Capture

A captured payment.

## Structure

`OrdersCapture`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `status` | [`?string(CaptureStatus)`](../../doc/models/capture-status.md) | Optional | The status of the captured payment. | getStatus(): ?string | setStatus(?string status): void |
| `statusDetails` | [`?CaptureStatusDetails`](../../doc/models/capture-status-details.md) | Optional | The details of the captured payment status. | getStatusDetails(): ?CaptureStatusDetails | setStatusDetails(?CaptureStatusDetails statusDetails): void |
| `id` | `?string` | Optional | The PayPal-generated ID for the captured payment. | getId(): ?string | setId(?string id): void |
| `amount` | [`?Money`](../../doc/models/money.md) | Optional | The currency and amount for a financial transaction, such as a balance or payment due. | getAmount(): ?Money | setAmount(?Money amount): void |
| `invoiceId` | `?string` | Optional | The API caller-provided external invoice number for this order. Appears in both the payer's transaction history and the emails that the payer receives. | getInvoiceId(): ?string | setInvoiceId(?string invoiceId): void |
| `customId` | `?string` | Optional | The API caller-provided external ID. Used to reconcile API caller-initiated transactions with PayPal transactions. Appears in transaction and settlement reports.<br><br>**Constraints**: *Maximum Length*: `255` | getCustomId(): ?string | setCustomId(?string customId): void |
| `networkTransactionReference` | [`?NetworkTransaction`](../../doc/models/network-transaction.md) | Optional | Reference values used by the card network to identify a transaction. | getNetworkTransactionReference(): ?NetworkTransaction | setNetworkTransactionReference(?NetworkTransaction networkTransactionReference): void |
| `sellerProtection` | [`?SellerProtection`](../../doc/models/seller-protection.md) | Optional | The level of protection offered as defined by [PayPal Seller Protection for Merchants](https://www.paypal.com/us/webapps/mpp/security/seller-protection). | getSellerProtection(): ?SellerProtection | setSellerProtection(?SellerProtection sellerProtection): void |
| `finalCapture` | `?bool` | Optional | Indicates whether you can make additional captures against the authorized payment. Set to `true` if you do not intend to capture additional payments against the authorization. Set to `false` if you intend to capture additional payments against the authorization.<br><br>**Default**: `false` | getFinalCapture(): ?bool | setFinalCapture(?bool finalCapture): void |
| `sellerReceivableBreakdown` | [`?SellerReceivableBreakdown`](../../doc/models/seller-receivable-breakdown.md) | Optional | The detailed breakdown of the capture activity. This is not available for transactions that are in pending state. | getSellerReceivableBreakdown(): ?SellerReceivableBreakdown | setSellerReceivableBreakdown(?SellerReceivableBreakdown sellerReceivableBreakdown): void |
| `disbursementMode` | [`?string(DisbursementMode)`](../../doc/models/disbursement-mode.md) | Optional | The funds that are held on behalf of the merchant.<br><br>**Default**: `DisbursementMode::INSTANT`<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `16`, *Pattern*: `^[A-Z_]+$` | getDisbursementMode(): ?string | setDisbursementMode(?string disbursementMode): void |
| `links` | [`?(LinkDescription[])`](../../doc/models/link-description.md) | Optional | An array of related [HATEOAS links](/docs/api/reference/api-responses/#hateoas-links). | getLinks(): ?array | setLinks(?array links): void |
| `processorResponse` | [`?ProcessorResponse`](../../doc/models/processor-response.md) | Optional | The processor response information for payment requests, such as direct credit card transactions. | getProcessorResponse(): ?ProcessorResponse | setProcessorResponse(?ProcessorResponse processorResponse): void |
| `createTime` | `?string` | Optional | The date and time, in [Internet date and time format](https://tools.ietf.org/html/rfc3339#section-5.6). Seconds are required while fractional seconds are optional. Note: The regular expression provides guidance but does not reject all invalid dates.<br><br>**Constraints**: *Minimum Length*: `20`, *Maximum Length*: `64`, *Pattern*: `^[0-9]{4}-(0[1-9]\|1[0-2])-(0[1-9]\|[1-2][0-9]\|3[0-1])[T,t]([0-1][0-9]\|2[0-3]):[0-5][0-9]:([0-5][0-9]\|60)([.][0-9]+)?([Zz]\|[+-][0-9]{2}:[0-9]{2})$` | getCreateTime(): ?string | setCreateTime(?string createTime): void |
| `updateTime` | `?string` | Optional | The date and time, in [Internet date and time format](https://tools.ietf.org/html/rfc3339#section-5.6). Seconds are required while fractional seconds are optional. Note: The regular expression provides guidance but does not reject all invalid dates.<br><br>**Constraints**: *Minimum Length*: `20`, *Maximum Length*: `64`, *Pattern*: `^[0-9]{4}-(0[1-9]\|1[0-2])-(0[1-9]\|[1-2][0-9]\|3[0-1])[T,t]([0-1][0-9]\|2[0-3]):[0-5][0-9]:([0-5][0-9]\|60)([.][0-9]+)?([Zz]\|[+-][0-9]{2}:[0-9]{2})$` | getUpdateTime(): ?string | setUpdateTime(?string updateTime): void |

## Example (as JSON)

```json
{
  "final_capture": false,
  "disbursement_mode": "INSTANT",
  "status": "REFUNDED",
  "status_details": {
    "reason": "VERIFICATION_REQUIRED"
  },
  "id": "id2",
  "amount": {
    "currency_code": "currency_code6",
    "value": "value0"
  },
  "invoice_id": "invoice_id2"
}
```

