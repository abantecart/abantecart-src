
# Seller Protection

The level of protection offered as defined by [PayPal Seller Protection for Merchants](https://www.paypal.com/us/webapps/mpp/security/seller-protection).

## Structure

`SellerProtection`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `status` | [`?string(SellerProtectionStatus)`](../../doc/models/seller-protection-status.md) | Optional | Indicates whether the transaction is eligible for seller protection. For information, see [PayPal Seller Protection for Merchants](https://www.paypal.com/us/webapps/mpp/security/seller-protection). | getStatus(): ?string | setStatus(?string status): void |
| `disputeCategories` | [`?(string(DisputeCategory)[])`](../../doc/models/dispute-category.md) | Optional | An array of conditions that are covered for the transaction. | getDisputeCategories(): ?array | setDisputeCategories(?array disputeCategories): void |

## Example (as JSON)

```json
{
  "status": "NOT_ELIGIBLE",
  "dispute_categories": [
    "ITEM_NOT_RECEIVED"
  ]
}
```

