
# Search Error Exception

The error details.

## Structure

`SearchErrorException`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `name` | `string` | Required | The human-readable, unique name of the error. | getName(): string | setName(string name): void |
| `message` | `string` | Required | The message that describes the error. | getMessage(): string | setMessage(string message): void |
| `debugId` | `string` | Required | The PayPal internal ID. Used for correlation purposes. | getDebugId(): string | setDebugId(string debugId): void |
| `informationLink` | `?string` | Optional | The information link, or URI, that shows detailed information about this error for the developer. | getInformationLink(): ?string | setInformationLink(?string informationLink): void |
| `details` | [`?(TransactionSearchErrorDetails[])`](../../doc/models/transaction-search-error-details.md) | Optional | An array of additional details about the error. | getDetails(): ?array | setDetails(?array details): void |
| `links` | [`?(LinkDescription[])`](../../doc/models/link-description.md) | Optional | An array of request-related [HATEOAS links](/docs/api/reference/api-responses/#hateoas-links). | getLinks(): ?array | setLinks(?array links): void |
| `totalItems` | `?int` | Optional | The total number of transactions. Valid only for `RESULTSET_TOO_LARGE`.<br><br>**Constraints**: `>= 0`, `<= 2147483647` | getTotalItems(): ?int | setTotalItems(?int totalItems): void |
| `maximumItems` | `?int` | Optional | The maximum number of transactions. Valid only for `RESULTSET_TOO_LARGE`.<br><br>**Constraints**: `>= 0`, `<= 2147483647` | getMaximumItems(): ?int | setMaximumItems(?int maximumItems): void |

## Example (as JSON)

```json
{
  "name": "name8",
  "message": "message8",
  "debug_id": "debug_id6",
  "information_link": "information_link0",
  "details": [
    {
      "field": "field4",
      "value": "value2",
      "location": "location4",
      "issue": "issue6",
      "description": "description0"
    }
  ],
  "links": [
    {
      "href": "href6",
      "rel": "rel0",
      "method": "HEAD"
    }
  ],
  "total_items": 20,
  "maximum_items": 206
}
```

