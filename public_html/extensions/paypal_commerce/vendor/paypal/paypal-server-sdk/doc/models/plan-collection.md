
# Plan Collection

The list of plans with details.

## Structure

`PlanCollection`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `plans` | [`?(BillingPlan[])`](../../doc/models/billing-plan.md) | Optional | An array of plans.<br><br>**Constraints**: *Minimum Items*: `0`, *Maximum Items*: `32767` | getPlans(): ?array | setPlans(?array plans): void |
| `totalItems` | `?int` | Optional | The total number of items.<br><br>**Constraints**: `>= 0`, `<= 500000000` | getTotalItems(): ?int | setTotalItems(?int totalItems): void |
| `totalPages` | `?int` | Optional | The total number of pages.<br><br>**Constraints**: `>= 0`, `<= 100000000` | getTotalPages(): ?int | setTotalPages(?int totalPages): void |
| `links` | [`?(LinkDescription[])`](../../doc/models/link-description.md) | Optional | An array of request-related [HATEOAS links](/docs/api/reference/api-responses/#hateoas-links).<br><br>**Constraints**: *Minimum Items*: `1`, *Maximum Items*: `10` | getLinks(): ?array | setLinks(?array links): void |

## Example (as JSON)

```json
{
  "plans": [
    {
      "id": "id4",
      "product_id": "product_id0",
      "name": "name4",
      "status": "INACTIVE",
      "description": "description4"
    }
  ],
  "total_items": 158,
  "total_pages": 194,
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
    },
    {
      "href": "href6",
      "rel": "rel0",
      "method": "HEAD"
    }
  ]
}
```

