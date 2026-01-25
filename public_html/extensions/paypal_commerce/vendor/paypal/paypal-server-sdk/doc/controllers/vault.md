# Vault

Use the `/vault` resource to create, retrieve, and delete payment and setup tokens.

```php
$vaultController = $client->getVaultController();
```

## Class Name

`VaultController`

## Methods

* [Create Payment Token](../../doc/controllers/vault.md#create-payment-token)
* [List Customer Payment Tokens](../../doc/controllers/vault.md#list-customer-payment-tokens)
* [Get Payment Token](../../doc/controllers/vault.md#get-payment-token)
* [Delete Payment Token](../../doc/controllers/vault.md#delete-payment-token)
* [Create Setup Token](../../doc/controllers/vault.md#create-setup-token)
* [Get Setup Token](../../doc/controllers/vault.md#get-setup-token)


# Create Payment Token

Creates a Payment Token from the given payment source and adds it to the Vault of the associated customer.

```php
function createPaymentToken(array $options): ApiResponse
```

## Parameters

| Parameter | Type | Tags | Description |
|  --- | --- | --- | --- |
| `body` | [`PaymentTokenRequest`](../../doc/models/payment-token-request.md) | Body, Required | Payment Token creation with a financial instrument and an optional customer_id. |
| `paypalRequestId` | `?string` | Header, Optional | The server stores keys for 3 hours.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `108`, *Pattern*: `^.*$` |

## Response Type

This method returns an [`ApiResponse`](../../doc/api-response.md) instance. The `getResult()` method on this instance returns the response data which is of type [`PaymentTokenResponse`](../../doc/models/payment-token-response.md).

## Example Usage

```php
$collect = [
    'body' => PaymentTokenRequestBuilder::init(
        PaymentTokenRequestPaymentSourceBuilder::init()->build()
    )->build()
];

$apiResponse = $vaultController->createPaymentToken($collect);
```

## Errors

| HTTP Status Code | Error Description | Exception Class |
|  --- | --- | --- |
| 400 | Request is not well-formed, syntactically incorrect, or violates schema. | [`ErrorException`](../../doc/models/error-exception.md) |
| 403 | Authorization failed due to insufficient permissions. | [`ErrorException`](../../doc/models/error-exception.md) |
| 404 | Request contains reference to resources that do not exist. | [`ErrorException`](../../doc/models/error-exception.md) |
| 422 | The requested action could not be performed, semantically incorrect, or failed business validation. | [`ErrorException`](../../doc/models/error-exception.md) |
| 500 | An internal server error has occurred. | [`ErrorException`](../../doc/models/error-exception.md) |


# List Customer Payment Tokens

Returns all payment tokens for a customer.

```php
function listCustomerPaymentTokens(array $options): ApiResponse
```

## Parameters

| Parameter | Type | Tags | Description |
|  --- | --- | --- | --- |
| `customerId` | `string` | Query, Required | A unique identifier representing a specific customer in merchant's/partner's system or records.<br><br>**Constraints**: *Minimum Length*: `7`, *Maximum Length*: `36`, *Pattern*: `^[0-9a-zA-Z_-]+$` |
| `pageSize` | `?int` | Query, Optional | A non-negative, non-zero integer indicating the maximum number of results to return at one time.<br><br>**Default**: `5`<br><br>**Constraints**: `>= 1`, `<= 5` |
| `page` | `?int` | Query, Optional | A non-negative, non-zero integer representing the page of the results.<br><br>**Default**: `1`<br><br>**Constraints**: `>= 1`, `<= 10` |
| `totalRequired` | `?bool` | Query, Optional | A boolean indicating total number of items (total_items) and pages (total_pages) are expected to be returned in the response.<br><br>**Default**: `false` |

## Response Type

This method returns an [`ApiResponse`](../../doc/api-response.md) instance. The `getResult()` method on this instance returns the response data which is of type [`CustomerVaultPaymentTokensResponse`](../../doc/models/customer-vault-payment-tokens-response.md).

## Example Usage

```php
$collect = [
    'customerId' => 'customer_id8',
    'pageSize' => 5,
    'page' => 1,
    'totalRequired' => false
];

$apiResponse = $vaultController->listCustomerPaymentTokens($collect);
```

## Errors

| HTTP Status Code | Error Description | Exception Class |
|  --- | --- | --- |
| 400 | Request is not well-formed, syntactically incorrect, or violates schema. | [`ErrorException`](../../doc/models/error-exception.md) |
| 403 | Authorization failed due to insufficient permissions. | [`ErrorException`](../../doc/models/error-exception.md) |
| 500 | An internal server error has occurred. | [`ErrorException`](../../doc/models/error-exception.md) |


# Get Payment Token

Returns a readable representation of vaulted payment source associated with the payment token id.

```php
function getPaymentToken(string $id): ApiResponse
```

## Parameters

| Parameter | Type | Tags | Description |
|  --- | --- | --- | --- |
| `id` | `string` | Template, Required | ID of the payment token.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `36`, *Pattern*: `^[0-9a-zA-Z_-]+$` |

## Response Type

This method returns an [`ApiResponse`](../../doc/api-response.md) instance. The `getResult()` method on this instance returns the response data which is of type [`PaymentTokenResponse`](../../doc/models/payment-token-response.md).

## Example Usage

```php
$id = 'id0';

$apiResponse = $vaultController->getPaymentToken($id);
```

## Errors

| HTTP Status Code | Error Description | Exception Class |
|  --- | --- | --- |
| 403 | Authorization failed due to insufficient permissions. | [`ErrorException`](../../doc/models/error-exception.md) |
| 404 | The specified resource does not exist. | [`ErrorException`](../../doc/models/error-exception.md) |
| 422 | The requested action could not be performed, semantically incorrect, or failed business validation. | [`ErrorException`](../../doc/models/error-exception.md) |
| 500 | An internal server error has occurred. | [`ErrorException`](../../doc/models/error-exception.md) |


# Delete Payment Token

Delete the payment token associated with the payment token id.

```php
function deletePaymentToken(string $id): ApiResponse
```

## Parameters

| Parameter | Type | Tags | Description |
|  --- | --- | --- | --- |
| `id` | `string` | Template, Required | ID of the payment token.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `36`, *Pattern*: `^[0-9a-zA-Z_-]+$` |

## Response Type

This method returns an [`ApiResponse`](../../doc/api-response.md) instance.

## Example Usage

```php
$id = 'id0';

$apiResponse = $vaultController->deletePaymentToken($id);
```

## Errors

| HTTP Status Code | Error Description | Exception Class |
|  --- | --- | --- |
| 400 | Request is not well-formed, syntactically incorrect, or violates schema. | [`ErrorException`](../../doc/models/error-exception.md) |
| 403 | Authorization failed due to insufficient permissions. | [`ErrorException`](../../doc/models/error-exception.md) |
| 500 | An internal server error has occurred. | [`ErrorException`](../../doc/models/error-exception.md) |


# Create Setup Token

Creates a Setup Token from the given payment source and adds it to the Vault of the associated customer.

```php
function createSetupToken(array $options): ApiResponse
```

## Parameters

| Parameter | Type | Tags | Description |
|  --- | --- | --- | --- |
| `body` | [`SetupTokenRequest`](../../doc/models/setup-token-request.md) | Body, Required | Setup Token creation with a instrument type optional financial instrument details and customer_id. |
| `paypalRequestId` | `?string` | Header, Optional | The server stores keys for 3 hours.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `108`, *Pattern*: `^.*$` |

## Response Type

This method returns an [`ApiResponse`](../../doc/api-response.md) instance. The `getResult()` method on this instance returns the response data which is of type [`SetupTokenResponse`](../../doc/models/setup-token-response.md).

## Example Usage

```php
$collect = [
    'body' => SetupTokenRequestBuilder::init(
        SetupTokenRequestPaymentSourceBuilder::init()->build()
    )->build()
];

$apiResponse = $vaultController->createSetupToken($collect);
```

## Errors

| HTTP Status Code | Error Description | Exception Class |
|  --- | --- | --- |
| 400 | Request is not well-formed, syntactically incorrect, or violates schema. | [`ErrorException`](../../doc/models/error-exception.md) |
| 403 | Authorization failed due to insufficient permissions. | [`ErrorException`](../../doc/models/error-exception.md) |
| 422 | The requested action could not be performed, semantically incorrect, or failed business validation. | [`ErrorException`](../../doc/models/error-exception.md) |
| 500 | An internal server error has occurred. | [`ErrorException`](../../doc/models/error-exception.md) |


# Get Setup Token

Returns a readable representation of temporarily vaulted payment source associated with the setup token id.

```php
function getSetupToken(string $id): ApiResponse
```

## Parameters

| Parameter | Type | Tags | Description |
|  --- | --- | --- | --- |
| `id` | `string` | Template, Required | ID of the setup token.<br><br>**Constraints**: *Minimum Length*: `7`, *Maximum Length*: `36`, *Pattern*: `^[0-9a-zA-Z_-]+$` |

## Response Type

This method returns an [`ApiResponse`](../../doc/api-response.md) instance. The `getResult()` method on this instance returns the response data which is of type [`SetupTokenResponse`](../../doc/models/setup-token-response.md).

## Example Usage

```php
$id = 'id0';

$apiResponse = $vaultController->getSetupToken($id);
```

## Errors

| HTTP Status Code | Error Description | Exception Class |
|  --- | --- | --- |
| 403 | Authorization failed due to insufficient permissions. | [`ErrorException`](../../doc/models/error-exception.md) |
| 404 | The specified resource does not exist. | [`ErrorException`](../../doc/models/error-exception.md) |
| 422 | The requested action could not be performed, semantically incorrect, or failed business validation. | [`ErrorException`](../../doc/models/error-exception.md) |
| 500 | An internal server error has occurred. | [`ErrorException`](../../doc/models/error-exception.md) |

