
# Money

The currency and amount for a financial transaction, such as a balance or payment due.

## Structure

`Money`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `currencyCode` | `string` | Required | The [three-character ISO-4217 currency code](/api/rest/reference/currency-codes/) that identifies the currency.<br><br>**Constraints**: *Minimum Length*: `3`, *Maximum Length*: `3` | getCurrencyCode(): string | setCurrencyCode(string currencyCode): void |
| `value` | `string` | Required | The value, which might be: An integer for currencies like `JPY` that are not typically fractional. A decimal fraction for currencies like `TND` that are subdivided into thousandths. For the required number of decimal places for a currency code, see [Currency Codes](/api/rest/reference/currency-codes/).<br><br>**Constraints**: *Maximum Length*: `32`, *Pattern*: `^((-?[0-9]+)\|(-?([0-9]+)?[.][0-9]+))$` | getValue(): string | setValue(string value): void |

## Example (as JSON)

```json
{
  "currency_code": "currency_code6",
  "value": "value2"
}
```

