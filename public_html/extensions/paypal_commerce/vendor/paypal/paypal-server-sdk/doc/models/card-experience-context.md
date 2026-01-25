
# Card Experience Context

Customizes the payer experience during the 3DS Approval for payment.

## Structure

`CardExperienceContext`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `returnUrl` | `?string` | Optional | Describes the URL. | getReturnUrl(): ?string | setReturnUrl(?string returnUrl): void |
| `cancelUrl` | `?string` | Optional | Describes the URL. | getCancelUrl(): ?string | setCancelUrl(?string cancelUrl): void |

## Example (as JSON)

```json
{
  "return_url": "return_url2",
  "cancel_url": "cancel_url0"
}
```

