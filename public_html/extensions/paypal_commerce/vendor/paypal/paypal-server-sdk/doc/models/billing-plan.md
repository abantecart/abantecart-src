
# Billing Plan

The plan details.

## Structure

`BillingPlan`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `id` | `?string` | Optional | The unique PayPal-generated ID for the plan.<br><br>**Constraints**: *Minimum Length*: `26`, *Maximum Length*: `26`, *Pattern*: `^P-[A-Z0-9]*$` | getId(): ?string | setId(?string id): void |
| `productId` | `?string` | Optional | The ID for the product.<br><br>**Constraints**: *Minimum Length*: `22`, *Maximum Length*: `22`, *Pattern*: `^PROD-[A-Z0-9]*$` | getProductId(): ?string | setProductId(?string productId): void |
| `name` | `?string` | Optional | The plan name.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `127`, *Pattern*: `^.*$` | getName(): ?string | setName(?string name): void |
| `status` | [`?string(SubscriptionPlanStatus)`](../../doc/models/subscription-plan-status.md) | Optional | The plan status.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `24`, *Pattern*: `^[A-Z_]+$` | getStatus(): ?string | setStatus(?string status): void |
| `description` | `?string` | Optional | The detailed description of the plan.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `127`, *Pattern*: `^.*$` | getDescription(): ?string | setDescription(?string description): void |
| `billingCycles` | [`?(SubscriptionBillingCycle[])`](../../doc/models/subscription-billing-cycle.md) | Optional | An array of billing cycles for trial billing and regular billing. A plan can have at most two trial cycles and only one regular cycle.<br><br>**Constraints**: *Minimum Items*: `1`, *Maximum Items*: `12` | getBillingCycles(): ?array | setBillingCycles(?array billingCycles): void |
| `paymentPreferences` | [`?PaymentPreferences`](../../doc/models/payment-preferences.md) | Optional | The payment preferences for a subscription. | getPaymentPreferences(): ?PaymentPreferences | setPaymentPreferences(?PaymentPreferences paymentPreferences): void |
| `merchantPreferences` | [`?MerchantPreferences`](../../doc/models/merchant-preferences.md) | Optional | The merchant preferences for a subscription. | getMerchantPreferences(): ?MerchantPreferences | setMerchantPreferences(?MerchantPreferences merchantPreferences): void |
| `taxes` | [`?Taxes`](../../doc/models/taxes.md) | Optional | The tax details. | getTaxes(): ?Taxes | setTaxes(?Taxes taxes): void |
| `quantitySupported` | `?bool` | Optional | Indicates whether you can subscribe to this plan by providing a quantity for the goods or service.<br><br>**Default**: `false` | getQuantitySupported(): ?bool | setQuantitySupported(?bool quantitySupported): void |
| `createTime` | `?string` | Optional | The date and time, in [Internet date and time format](https://tools.ietf.org/html/rfc3339#section-5.6). Seconds are required while fractional seconds are optional. Note: The regular expression provides guidance but does not reject all invalid dates.<br><br>**Constraints**: *Minimum Length*: `20`, *Maximum Length*: `64`, *Pattern*: `^[0-9]{4}-(0[1-9]\|1[0-2])-(0[1-9]\|[1-2][0-9]\|3[0-1])[T,t]([0-1][0-9]\|2[0-3]):[0-5][0-9]:([0-5][0-9]\|60)([.][0-9]+)?([Zz]\|[+-][0-9]{2}:[0-9]{2})$` | getCreateTime(): ?string | setCreateTime(?string createTime): void |
| `updateTime` | `?string` | Optional | The date and time, in [Internet date and time format](https://tools.ietf.org/html/rfc3339#section-5.6). Seconds are required while fractional seconds are optional. Note: The regular expression provides guidance but does not reject all invalid dates.<br><br>**Constraints**: *Minimum Length*: `20`, *Maximum Length*: `64`, *Pattern*: `^[0-9]{4}-(0[1-9]\|1[0-2])-(0[1-9]\|[1-2][0-9]\|3[0-1])[T,t]([0-1][0-9]\|2[0-3]):[0-5][0-9]:([0-5][0-9]\|60)([.][0-9]+)?([Zz]\|[+-][0-9]{2}:[0-9]{2})$` | getUpdateTime(): ?string | setUpdateTime(?string updateTime): void |
| `links` | [`?(LinkDescription[])`](../../doc/models/link-description.md) | Optional | An array of request-related [HATEOAS links](/docs/api/reference/api-responses/#hateoas-links).<br><br>**Constraints**: *Minimum Items*: `1`, *Maximum Items*: `10` | getLinks(): ?array | setLinks(?array links): void |

## Example (as JSON)

```json
{
  "quantity_supported": false,
  "id": "id0",
  "product_id": "product_id4",
  "name": "name0",
  "status": "CREATED",
  "description": "description0"
}
```

