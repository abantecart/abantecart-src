
# Sepa Debit Experience Context

Customizes the payer experience during the approval process for the SEPA Debit payment.

## Structure

`SepaDebitExperienceContext`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `locale` | `?string` | Optional | The [language tag](https://tools.ietf.org/html/bcp47#section-2) for the language in which to localize the error-related strings, such as messages, issues, and suggested actions. The tag is made up of the [ISO 639-2 language code](https://www.loc.gov/standards/iso639-2/php/code_list.php), the optional [ISO-15924 script tag](https://www.unicode.org/iso15924/codelists.html), and the [ISO-3166 alpha-2 country code](/api/rest/reference/country-codes/) or [M49 region code](https://unstats.un.org/unsd/methodology/m49/).<br><br>**Constraints**: *Minimum Length*: `2`, *Maximum Length*: `10`, *Pattern*: `^[a-z]{2}(?:-[A-Z][a-z]{3})?(?:-(?:[A-Z]{2}\|[0-9]{3}))?$` | getLocale(): ?string | setLocale(?string locale): void |
| `returnUrl` | `string` | Required | Describes the URL. | getReturnUrl(): string | setReturnUrl(string returnUrl): void |
| `cancelUrl` | `string` | Required | Describes the URL. | getCancelUrl(): string | setCancelUrl(string cancelUrl): void |

## Example (as JSON)

```json
{
  "locale": "locale8",
  "return_url": "return_url6",
  "cancel_url": "cancel_url8"
}
```

