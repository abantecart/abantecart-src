
# Card Verification Processor Response

The processor response information for payment requests, such as direct credit card transactions.

## Structure

`CardVerificationProcessorResponse`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `avsCode` | [`?string(AvsCode)`](../../doc/models/avs-code.md) | Optional | The address verification code for Visa, Discover, Mastercard, or American Express transactions. | getAvsCode(): ?string | setAvsCode(?string avsCode): void |
| `cvvCode` | [`?string(CvvCode)`](../../doc/models/cvv-code.md) | Optional | The card verification value code for for Visa, Discover, Mastercard, or American Express. | getCvvCode(): ?string | setCvvCode(?string cvvCode): void |

## Example (as JSON)

```json
{
  "avs_code": "E",
  "cvv_code": "All others"
}
```

