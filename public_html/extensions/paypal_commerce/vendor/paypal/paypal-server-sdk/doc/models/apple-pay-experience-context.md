
# Apple Pay Experience Context

Customizes the payer experience during the approval process for the payment.

## Structure

`ApplePayExperienceContext`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `returnUrl` | `string` | Required | Describes the URL. | getReturnUrl(): string | setReturnUrl(string returnUrl): void |
| `cancelUrl` | `string` | Required | Describes the URL. | getCancelUrl(): string | setCancelUrl(string cancelUrl): void |

## Example (as JSON)

```json
{
  "return_url": "return_url6",
  "cancel_url": "cancel_url8"
}
```

