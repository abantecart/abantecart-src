
# Search Response

The search response information.

## Structure

`SearchResponse`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `transactionDetails` | [`?(TransactionDetails[])`](../../doc/models/transaction-details.md) | Optional | An array of transaction detail objects.<br><br>**Constraints**: *Minimum Items*: `1`, *Maximum Items*: `2147483647` | getTransactionDetails(): ?array | setTransactionDetails(?array transactionDetails): void |
| `accountNumber` | `?string` | Optional | The merchant account number.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[a-zA-Z0-9]*$` | getAccountNumber(): ?string | setAccountNumber(?string accountNumber): void |
| `startDate` | `?string` | Optional | The date and time, in [Internet date and time format](https://tools.ietf.org/html/rfc3339#section-5.6). Seconds are required while fractional seconds are optional. Note: The regular expression provides guidance but does not reject all invalid dates.<br><br>**Constraints**: *Minimum Length*: `20`, *Maximum Length*: `64`, *Pattern*: `^[0-9]{4}-(0[1-9]\|1[0-2])-(0[1-9]\|[1-2][0-9]\|3[0-1])[T,t]([0-1][0-9]\|2[0-3]):[0-5][0-9]:([0-5][0-9]\|60)([.][0-9]+)?([Zz]\|[+-][0-9]{2}:[0-9]{2})$` | getStartDate(): ?string | setStartDate(?string startDate): void |
| `endDate` | `?string` | Optional | The date and time, in [Internet date and time format](https://tools.ietf.org/html/rfc3339#section-5.6). Seconds are required while fractional seconds are optional. Note: The regular expression provides guidance but does not reject all invalid dates.<br><br>**Constraints**: *Minimum Length*: `20`, *Maximum Length*: `64`, *Pattern*: `^[0-9]{4}-(0[1-9]\|1[0-2])-(0[1-9]\|[1-2][0-9]\|3[0-1])[T,t]([0-1][0-9]\|2[0-3]):[0-5][0-9]:([0-5][0-9]\|60)([.][0-9]+)?([Zz]\|[+-][0-9]{2}:[0-9]{2})$` | getEndDate(): ?string | setEndDate(?string endDate): void |
| `lastRefreshedDatetime` | `?string` | Optional | The date and time, in [Internet date and time format](https://tools.ietf.org/html/rfc3339#section-5.6). Seconds are required while fractional seconds are optional. Note: The regular expression provides guidance but does not reject all invalid dates.<br><br>**Constraints**: *Minimum Length*: `20`, *Maximum Length*: `64`, *Pattern*: `^[0-9]{4}-(0[1-9]\|1[0-2])-(0[1-9]\|[1-2][0-9]\|3[0-1])[T,t]([0-1][0-9]\|2[0-3]):[0-5][0-9]:([0-5][0-9]\|60)([.][0-9]+)?([Zz]\|[+-][0-9]{2}:[0-9]{2})$` | getLastRefreshedDatetime(): ?string | setLastRefreshedDatetime(?string lastRefreshedDatetime): void |
| `page` | `?int` | Optional | A zero-relative index of transactions.<br><br>**Constraints**: `>= 0`, `<= 2147483647` | getPage(): ?int | setPage(?int page): void |
| `totalItems` | `?int` | Optional | The total number of transactions as an integer beginning with the specified `page` in the full result and not just in this response.<br><br>**Constraints**: `>= 0`, `<= 2147483647` | getTotalItems(): ?int | setTotalItems(?int totalItems): void |
| `totalPages` | `?int` | Optional | The total number of pages, as an `integer`, when the `total_items` is divided into pages of the specified `page_size`.<br><br>**Constraints**: `>= 0`, `<= 2147483647` | getTotalPages(): ?int | setTotalPages(?int totalPages): void |
| `links` | [`?(LinkDescription[])`](../../doc/models/link-description.md) | Optional | An array of request-related [HATEOAS links](/api/rest/responses/#hateoas-links).<br><br>**Constraints**: *Minimum Items*: `1`, *Maximum Items*: `32767` | getLinks(): ?array | setLinks(?array links): void |

## Example (as JSON)

```json
{
  "transaction_details": [
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
    },
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
    },
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
  ],
  "account_number": "account_number8",
  "start_date": "start_date2",
  "end_date": "end_date8",
  "last_refreshed_datetime": "last_refreshed_datetime4"
}
```

