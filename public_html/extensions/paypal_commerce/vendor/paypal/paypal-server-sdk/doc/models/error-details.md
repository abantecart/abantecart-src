
# Error Details

The error details. Required for client-side `4XX` errors.

## Structure

`ErrorDetails`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `field` | `?string` | Optional | The field that caused the error. If this field is in the body, set this value to the field's JSON pointer value. Required for client-side errors. | getField(): ?string | setField(?string field): void |
| `value` | `?string` | Optional | The value of the field that caused the error. | getValue(): ?string | setValue(?string value): void |
| `location` | `?string` | Optional | The location of the field that caused the error. Value is `body`, `path`, or `query`.<br><br>**Default**: `'body'` | getLocation(): ?string | setLocation(?string location): void |
| `issue` | `string` | Required | The unique, fine-grained application-level error code. | getIssue(): string | setIssue(string issue): void |
| `links` | [`?(LinkDescription[])`](../../doc/models/link-description.md) | Optional | An array of request-related [HATEOAS links](/api/rest/responses/#hateoas-links) that are either relevant to the issue by providing additional information or offering potential resolutions.<br><br>**Constraints**: *Minimum Items*: `1`, *Maximum Items*: `4` | getLinks(): ?array | setLinks(?array links): void |
| `description` | `?string` | Optional | The human-readable description for an issue. The description can change over the lifetime of an API, so clients must not depend on this value. | getDescription(): ?string | setDescription(?string description): void |

## Example (as JSON)

```json
{
  "location": "body",
  "issue": "issue8",
  "field": "field0",
  "value": "value8",
  "links": [
    {
      "href": "href6",
      "rel": "rel0",
      "method": "HEAD"
    },
    {
      "href": "href6",
      "rel": "rel0",
      "method": "HEAD"
    }
  ],
  "description": "description4"
}
```

