
# Link Description

The request-related [HATEOAS link](/api/rest/responses/#hateoas-links) information.

## Structure

`LinkDescription`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `href` | `string` | Required | The complete target URL. To make the related call, combine the method with this [URI Template-formatted](https://tools.ietf.org/html/rfc6570) link. For pre-processing, include the `$`, `(`, and `)` characters. The `href` is the key HATEOAS component that links a completed call with a subsequent call. | getHref(): string | setHref(string href): void |
| `rel` | `string` | Required | The [link relation type](https://tools.ietf.org/html/rfc5988#section-4), which serves as an ID for a link that unambiguously describes the semantics of the link. See [Link Relations](https://www.iana.org/assignments/link-relations/link-relations.xhtml). | getRel(): string | setRel(string rel): void |
| `method` | [`?string(LinkHttpMethod)`](../../doc/models/link-http-method.md) | Optional | The HTTP method required to make the related call. | getMethod(): ?string | setMethod(?string method): void |

## Example (as JSON)

```json
{
  "href": "href2",
  "rel": "rel6",
  "method": "PUT"
}
```

