# Transaction Search

Use the `/transactions` resource to list transactions and the `/balances` resource to list balances.

```php
$transactionSearchController = $client->getTransactionSearchController();
```

## Class Name

`TransactionSearchController`

## Methods

* [Search Transactions](../../doc/controllers/transaction-search.md#search-transactions)
* [Search Balances](../../doc/controllers/transaction-search.md#search-balances)


# Search Transactions

Lists transactions. Specify one or more query parameters to filter the transaction that appear in the response. Notes: If you specify one or more optional query parameters, the ending_balance response field is empty. It takes a maximum of three hours for executed transactions to appear in the list transactions call. This call lists transaction for the previous three years.

```php
function searchTransactions(array $options): ApiResponse
```

## Parameters

| Parameter | Type | Tags | Description |
|  --- | --- | --- | --- |
| `startDate` | `string` | Query, Required | Filters the transactions in the response by a start date and time, in [Internet date and time format](https://tools.ietf.org/html/rfc3339#section-5.6). Seconds are required. Fractional seconds are optional.<br><br>**Constraints**: *Minimum Length*: `20`, *Maximum Length*: `64`, *Pattern*: `^[0-9]{4}-(0[1-9]\|1[0-2])-(0[1-9]\|[1-2][0-9]\|3[0-1])[T,t]([0-1][0-9]\|2[0-3]):[0-5][0-9]:([0-5][0-9]\|60)([.][0-9]+)?([Zz]\|[+-][0-9]{2}:[0-9]{2})$` |
| `endDate` | `string` | Query, Required | Filters the transactions in the response by an end date and time, in [Internet date and time format](https://tools.ietf.org/html/rfc3339#section-5.6). Seconds are required. Fractional seconds are optional. The maximum supported range is 31 days.<br><br>**Constraints**: *Minimum Length*: `20`, *Maximum Length*: `64`, *Pattern*: `^[0-9]{4}-(0[1-9]\|1[0-2])-(0[1-9]\|[1-2][0-9]\|3[0-1])[T,t]([0-1][0-9]\|2[0-3]):[0-5][0-9]:([0-5][0-9]\|60)([.][0-9]+)?([Zz]\|[+-][0-9]{2}:[0-9]{2})$` |
| `transactionId` | `?string` | Query, Optional | Filters the transactions in the response by a PayPal transaction ID. A valid transaction ID is 17 characters long, except for an order ID, which is 19 characters long. Note: A transaction ID is not unique in the reporting system. The response can list two transactions with the same ID. One transaction can be balance affecting while the other is non-balance affecting.<br><br>**Constraints**: *Minimum Length*: `17`, *Maximum Length*: `19` |
| `transactionType` | `?string` | Query, Optional | Filters the transactions in the response by a PayPal transaction event code. See [Transaction event codes](/docs/integration/direct/transaction-search/transaction-event-codes/). |
| `transactionStatus` | `?string` | Query, Optional | Filters the transactions in the response by a PayPal transaction status code. Value is: Status code Description D PayPal or merchant rules denied the transaction. P The transaction is pending. The transaction was created but waits for another payment process to complete, such as an ACH transaction, before the status changes to S. S The transaction successfully completed without a denial and after any pending statuses. V A successful transaction was reversed and funds were refunded to the original sender. |
| `transactionAmount` | `?string` | Query, Optional | Filters the transactions in the response by a gross transaction amount range. Specify the range as `TO`, where ` ` is the lower limit of the gross PayPal transaction amount and ` ` is the upper limit of the gross transaction amount. Specify the amounts in lower denominations. For example, to search for transactions from $5.00 to $10.05, specify `[500 TO 1005]`. Note:The values must be URL encoded. |
| `transactionCurrency` | `?string` | Query, Optional | Filters the transactions in the response by a [three-character ISO-4217 currency code](/api/rest/reference/currency-codes/) for the PayPal transaction currency. |
| `paymentInstrumentType` | `?string` | Query, Optional | Filters the transactions in the response by a payment instrument type. Value is either: CREDITCARD. Returns a direct credit card transaction with a corresponding value. DEBITCARD. Returns a debit card transaction with a corresponding value. If you omit this parameter, the API does not apply this filter. |
| `storeId` | `?string` | Query, Optional | Filters the transactions in the response by a store ID. |
| `terminalId` | `?string` | Query, Optional | Filters the transactions in the response by a terminal ID. |
| `fields` | `?string` | Query, Optional | Indicates which fields appear in the response. Value is a single field or a comma-separated list of fields. The transaction_info value returns only the transaction details in the response. To include all fields in the response, specify fields=all. Valid fields are: transaction_info. The transaction information. Includes the ID of the PayPal account of the payee, the PayPal-generated transaction ID, the PayPal-generated base ID, the PayPal reference ID type, the transaction event code, the date and time when the transaction was initiated and was last updated, the transaction amounts including the PayPal fee, any discounts, insurance, the transaction status, and other information about the transaction. payer_info. The payer information. Includes the PayPal customer account ID and the payer's email address, primary phone number, name, country code, address, and whether the payer is verified or unverified. shipping_info. The shipping information. Includes the recipient's name, the shipping method for this order, the shipping address for this order, and the secondary address associated with this order. auction_info. The auction information. Includes the name of the auction site, the auction site URL, the ID of the customer who makes the purchase in the auction, and the date and time when the auction closes. cart_info. The cart information. Includes an array of item details, whether the item amount or the shipping amount already includes tax, and the ID of the invoice for PayPal-generated invoices. incentive_info. An array of incentive detail objects. Each object includes the incentive, such as a special offer or coupon, the incentive amount, and the incentive program code that identifies a merchant loyalty or incentive program. store_info. The store information. Includes the ID of the merchant store and the terminal ID for the checkout stand in the merchant store.<br><br>**Default**: `'transaction_info'` |
| `balanceAffectingRecordsOnly` | `?string` | Query, Optional | Indicates whether the response includes only balance-impacting transactions or all transactions. Value is either: Y. The default. The response includes only balance transactions. N. The response includes all transactions.<br><br>**Default**: `'Y'` |
| `pageSize` | `?int` | Query, Optional | The number of items to return in the response. So, the combination of `page=1` and `page_size=20` returns the first 20 items. The combination of `page=2` and `page_size=20` returns the next 20 items.<br><br>**Default**: `100`<br><br>**Constraints**: `>= 1`, `<= 500` |
| `page` | `?int` | Query, Optional | The zero-relative start index of the entire list of items that are returned in the response. So, the combination of `page=1` and `page_size=20` returns the first 20 items.<br><br>**Default**: `1`<br><br>**Constraints**: `>= 1`, `<= 2147483647` |

## Response Type

This method returns an [`ApiResponse`](../../doc/api-response.md) instance. The `getResult()` method on this instance returns the response data which is of type [`SearchResponse`](../../doc/models/search-response.md).

## Example Usage

```php
$collect = [
    'startDate' => 'start_date6',
    'endDate' => 'end_date0',
    'fields' => 'transaction_info',
    'balanceAffectingRecordsOnly' => 'Y',
    'pageSize' => 100,
    'page' => 1
];

$apiResponse = $transactionSearchController->searchTransactions($collect);
```

## Errors

| HTTP Status Code | Error Description | Exception Class |
|  --- | --- | --- |
| Default | The error response. | [`SearchErrorException`](../../doc/models/search-error-exception.md) |


# Search Balances

List all balances. Specify date time to list balances for that time that appear in the response. Notes: It takes a maximum of three hours for balances to appear in the list balances call. This call lists balances upto the previous three years.

```php
function searchBalances(array $options): ApiResponse
```

## Parameters

| Parameter | Type | Tags | Description |
|  --- | --- | --- | --- |
| `asOfTime` | `?string` | Query, Optional | List balances in the response at the date time provided, will return the last refreshed balance in the system when not provided.<br><br>**Constraints**: *Minimum Length*: `20`, *Maximum Length*: `64`, *Pattern*: `^[0-9]{4}-(0[1-9]\|1[0-2])-(0[1-9]\|[1-2][0-9]\|3[0-1])[T,t]([0-1][0-9]\|2[0-3]):[0-5][0-9]:([0-5][0-9]\|60)([.][0-9]+)?([Zz]\|[+-][0-9]{2}:[0-9]{2})$` |
| `currencyCode` | `?string` | Query, Optional | Filters the transactions in the response by a [three-character ISO-4217 currency code](/api/rest/reference/currency-codes/) for the PayPal transaction currency.<br><br>**Constraints**: *Minimum Length*: `3`, *Maximum Length*: `3` |

## Response Type

This method returns an [`ApiResponse`](../../doc/api-response.md) instance. The `getResult()` method on this instance returns the response data which is of type [`BalancesResponse`](../../doc/models/balances-response.md).

## Example Usage

```php
$collect = [];

$apiResponse = $transactionSearchController->searchBalances($collect);
```

## Errors

| HTTP Status Code | Error Description | Exception Class |
|  --- | --- | --- |
| 400 | The request is not well-formed, is syntactically incorrect, or violates schema. | [`DefaultErrorException`](../../doc/models/default-error-exception.md) |
| 403 | Authorization failed due to insufficient permissions. | [`DefaultErrorException`](../../doc/models/default-error-exception.md) |
| 500 | An internal server error occurred. | [`DefaultErrorException`](../../doc/models/default-error-exception.md) |
| Default | The error response. | [`DefaultErrorException`](../../doc/models/default-error-exception.md) |

