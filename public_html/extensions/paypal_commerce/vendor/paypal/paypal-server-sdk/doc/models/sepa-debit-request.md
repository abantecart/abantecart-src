
# Sepa Debit Request

An API resource denoting a request to securely store a SEPA Debit.

## Structure

`SepaDebitRequest`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `experienceContext` | [`?SepaDebitExperienceContext`](../../doc/models/sepa-debit-experience-context.md) | Optional | Customizes the payer experience during the approval process for the SEPA Debit payment. | getExperienceContext(): ?SepaDebitExperienceContext | setExperienceContext(?SepaDebitExperienceContext experienceContext): void |

## Example (as JSON)

```json
{
  "experience_context": {
    "locale": "locale6",
    "return_url": "return_url4",
    "cancel_url": "cancel_url6"
  }
}
```

