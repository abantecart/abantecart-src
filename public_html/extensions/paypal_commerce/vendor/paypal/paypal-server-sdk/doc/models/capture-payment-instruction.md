
# Capture Payment Instruction

Any additional payment instructions to be consider during payment processing. This processing instruction is applicable for Capturing an order or Authorizing an Order.

## Structure

`CapturePaymentInstruction`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `platformFees` | [`?(PlatformFee[])`](../../doc/models/platform-fee.md) | Optional | An array of platform or partner fees, commissions, or brokerage fees that associated with the captured payment.<br><br>**Constraints**: *Minimum Items*: `0`, *Maximum Items*: `1` | getPlatformFees(): ?array | setPlatformFees(?array platformFees): void |
| `disbursementMode` | [`?string(DisbursementMode)`](../../doc/models/disbursement-mode.md) | Optional | The funds that are held on behalf of the merchant.<br><br>**Default**: `DisbursementMode::INSTANT`<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `16`, *Pattern*: `^[A-Z_]+$` | getDisbursementMode(): ?string | setDisbursementMode(?string disbursementMode): void |
| `payeeReceivableFxRateId` | `?string` | Optional | FX identifier generated returned by PayPal to be used for payment processing in order to honor FX rate (for eligible integrations) to be used when amount is settled/received into the payee account.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `4000`, *Pattern*: `^.*$` | getPayeeReceivableFxRateId(): ?string | setPayeeReceivableFxRateId(?string payeeReceivableFxRateId): void |

## Example (as JSON)

```json
{
  "disbursement_mode": "INSTANT",
  "platform_fees": [
    {
      "amount": {
        "currency_code": "currency_code6",
        "value": "value0"
      },
      "payee": {
        "email_address": "email_address4",
        "merchant_id": "merchant_id6"
      }
    },
    {
      "amount": {
        "currency_code": "currency_code6",
        "value": "value0"
      },
      "payee": {
        "email_address": "email_address4",
        "merchant_id": "merchant_id6"
      }
    }
  ],
  "payee_receivable_fx_rate_id": "payee_receivable_fx_rate_id8"
}
```

