
# Default Error Exception

The error details.

## Structure

`DefaultErrorException`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `name` | `string` | Required | The human-readable, unique name of the error. | getName(): string | setName(string name): void |
| `message` | `string` | Required | The message that describes the error. | getMessage(): string | setMessage(string message): void |
| `debugId` | `string` | Required | The PayPal internal ID. Used for correlation purposes. | getDebugId(): string | setDebugId(string debugId): void |
| `informationLink` | `?string` | Optional | The information link, or URI, that shows detailed information about this error for the developer. | getInformationLink(): ?string | setInformationLink(?string informationLink): void |
| `details` | [`?(TransactionSearchErrorDetails[])`](../../doc/models/transaction-search-error-details.md) | Optional | An array of additional details about the error. | getDetails(): ?array | setDetails(?array details): void |
| `links` | [`?(LinkDescription[])`](../../doc/models/link-description.md) | Optional | An array of request-related [HATEOAS links](/docs/api/reference/api-responses/#hateoas-links). | getLinks(): ?array | setLinks(?array links): void |

## Example (as JSON)

```json
{
  "name": "name2",
  "message": "message2",
  "debug_id": "debug_id8",
  "information_link": "information_link4",
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
  ]
}
```

