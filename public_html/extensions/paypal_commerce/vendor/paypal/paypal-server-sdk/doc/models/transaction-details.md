
# Transaction Details

The transaction details.

## Structure

`TransactionDetails`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `transactionInfo` | [`?TransactionInformation`](../../doc/models/transaction-information.md) | Optional | The transaction information. | getTransactionInfo(): ?TransactionInformation | setTransactionInfo(?TransactionInformation transactionInfo): void |
| `payerInfo` | [`?PayerInformation`](../../doc/models/payer-information.md) | Optional | The payer information. | getPayerInfo(): ?PayerInformation | setPayerInfo(?PayerInformation payerInfo): void |
| `shippingInfo` | [`?ShippingInformation`](../../doc/models/shipping-information.md) | Optional | The shipping information. | getShippingInfo(): ?ShippingInformation | setShippingInfo(?ShippingInformation shippingInfo): void |
| `cartInfo` | [`?CartInformation`](../../doc/models/cart-information.md) | Optional | The cart information. | getCartInfo(): ?CartInformation | setCartInfo(?CartInformation cartInfo): void |
| `storeInfo` | [`?StoreInformation`](../../doc/models/store-information.md) | Optional | The store information. | getStoreInfo(): ?StoreInformation | setStoreInfo(?StoreInformation storeInfo): void |
| `auctionInfo` | [`?AuctionInformation`](../../doc/models/auction-information.md) | Optional | The auction information. | getAuctionInfo(): ?AuctionInformation | setAuctionInfo(?AuctionInformation auctionInfo): void |
| `incentiveInfo` | [`?IncentiveInformation`](../../doc/models/incentive-information.md) | Optional | The incentive details. | getIncentiveInfo(): ?IncentiveInformation | setIncentiveInfo(?IncentiveInformation incentiveInfo): void |

## Example (as JSON)

```json
{
  "transaction_info": {
    "paypal_account_id": "paypal_account_id4",
    "transaction_id": "transaction_id0",
    "paypal_reference_id": "paypal_reference_id2",
    "paypal_reference_id_type": "ODR",
    "transaction_event_code": "transaction_event_code6"
  },
  "payer_info": {
    "account_id": "account_id2",
    "email_address": "email_address2",
    "phone_number": {
      "country_code": "country_code2",
      "national_number": "national_number6",
      "extension_number": "extension_number8"
    },
    "address_status": "address_status2",
    "payer_status": "payer_status2"
  },
  "shipping_info": {
    "name": "name0",
    "method": "method4",
    "address": {
      "line1": "line18",
      "line2": "line20",
      "city": "city6",
      "state": "state2",
      "country_code": "country_code6",
      "postal_code": "postal_code8"
    },
    "secondary_shipping_address": {
      "line1": "line16",
      "line2": "line28",
      "city": "city4",
      "state": "state0",
      "country_code": "country_code4",
      "postal_code": "postal_code6"
    }
  },
  "cart_info": {
    "item_details": [
      {
        "item_code": "item_code0",
        "item_name": "item_name8",
        "item_description": "item_description4",
        "item_options": "item_options2",
        "item_quantity": "item_quantity2"
      },
      {
        "item_code": "item_code0",
        "item_name": "item_name8",
        "item_description": "item_description4",
        "item_options": "item_options2",
        "item_quantity": "item_quantity2"
      }
    ],
    "tax_inclusive": false,
    "paypal_invoice_id": "paypal_invoice_id6"
  },
  "store_info": {
    "store_id": "store_id2",
    "terminal_id": "terminal_id6"
  }
}
```

