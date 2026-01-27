
# Getting Started with PayPal Server SDK

## Introduction

### Important Notes

- **Available Features:** This SDK currently contains only 5 of PayPal's API endpoints. Additional endpoints and functionality will be added in the future.

### Information

The PayPal Server SDK provides integration access to the PayPal REST APIs. The API endpoints are divided into distinct controllers:

- Orders Controller: [Orders API v2](https://developer.paypal.com/docs/api/orders/v2/)
- Payments Controller: [Payments API v2](https://developer.paypal.com/docs/api/payments/v2)
- Vault Controller: [Payment Method Tokens API v3](https://developer.paypal.com/docs/api/payment-tokens/v3/) *Available in the US only.*
- Transaction Search Controller: [Transaction Search API v1](https://developer.paypal.com/docs/api/transaction-search/v1/)
- Subscriptions Controller: [Subscriptions API v1](https://developer.paypal.com/docs/api/subscriptions/v1/)

## Install the Package

Run the following command to install the package and automatically add the dependency to your composer.json file:

```bash
composer require "paypal/paypal-server-sdk:2.2.0"
```

Or add it to the composer.json file manually as given below:

```json
"require": {
    "paypal/paypal-server-sdk": "2.2.0"
}
```

You can also view the package at:
https://packagist.org/packages/paypal/paypal-server-sdk#2.2.0

## Initialize the API Client

**_Note:_** Documentation for the client can be found [here.](https://www.github.com/paypal/PayPal-PHP-Server-SDK/tree/2.2.0/doc/client.md)

The following parameters are configurable for the API Client:

| Parameter | Type | Description |
|  --- | --- | --- |
| environment | `Environment` | The API environment. <br> **Default: `Environment.SANDBOX`** |
| timeout | `int` | Timeout for API calls in seconds.<br>*Default*: `0` |
| enableRetries | `bool` | Whether to enable retries and backoff feature.<br>*Default*: `false` |
| numberOfRetries | `int` | The number of retries to make.<br>*Default*: `0` |
| retryInterval | `float` | The retry time interval between the endpoint calls.<br>*Default*: `1` |
| backOffFactor | `float` | Exponential backoff factor to increase interval between retries.<br>*Default*: `2` |
| maximumRetryWaitTime | `int` | The maximum wait time in seconds for overall retrying requests.<br>*Default*: `0` |
| retryOnTimeout | `bool` | Whether to retry on request timeout.<br>*Default*: `true` |
| httpStatusCodesToRetry | `array` | Http status codes to retry against.<br>*Default*: `408, 413, 429, 500, 502, 503, 504, 521, 522, 524` |
| httpMethodsToRetry | `array` | Http methods to retry against.<br>*Default*: `'GET', 'PUT'` |
| loggingConfiguration | [`LoggingConfigurationBuilder`](https://www.github.com/paypal/PayPal-PHP-Server-SDK/tree/2.2.0/doc/logging-configuration-builder.md) | Represents the logging configurations for API calls |
| proxyConfiguration | [`ProxyConfigurationBuilder`](https://www.github.com/paypal/PayPal-PHP-Server-SDK/tree/2.2.0/doc/proxy-configuration-builder.md) | Represents the proxy configurations for API calls |
| clientCredentialsAuth | [`ClientCredentialsAuth`](https://www.github.com/paypal/PayPal-PHP-Server-SDK/tree/2.2.0/doc/auth/oauth-2-client-credentials-grant.md) | The Credentials Setter for OAuth 2 Client Credentials Grant |

The API client can be initialized as follows:

```php
use PaypalServerSdkLib\Logging\LoggingConfigurationBuilder;
use PaypalServerSdkLib\Logging\RequestLoggingConfigurationBuilder;
use PaypalServerSdkLib\Logging\ResponseLoggingConfigurationBuilder;
use Psr\Log\LogLevel;
use PaypalServerSdkLib\Environment;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;

$client = PaypalServerSdkClientBuilder::init()
    ->clientCredentialsAuthCredentials(
        ClientCredentialsAuthCredentialsBuilder::init(
            'OAuthClientId',
            'OAuthClientSecret'
        )
    )
    ->environment(Environment::SANDBOX)
    ->loggingConfiguration(
        LoggingConfigurationBuilder::init()
            ->level(LogLevel::INFO)
            ->requestConfiguration(RequestLoggingConfigurationBuilder::init()->body(true))
            ->responseConfiguration(ResponseLoggingConfigurationBuilder::init()->headers(true))
    )
    ->build();
```

## Environments

The SDK can be configured to use a different environment for making API calls. Available environments are:

### Fields

| Name | Description |
|  --- | --- |
| Production | PayPal Live Environment |
| Sandbox | **Default** PayPal Sandbox Environment |

## Authorization

This API uses the following authentication schemes.

* [`Oauth2 (OAuth 2 Client Credentials Grant)`](https://www.github.com/paypal/PayPal-PHP-Server-SDK/tree/2.2.0/doc/auth/oauth-2-client-credentials-grant.md)

## List of APIs

* [Orders](https://www.github.com/paypal/PayPal-PHP-Server-SDK/tree/2.2.0/doc/controllers/orders.md)
* [Payments](https://www.github.com/paypal/PayPal-PHP-Server-SDK/tree/2.2.0/doc/controllers/payments.md)
* [Vault](https://www.github.com/paypal/PayPal-PHP-Server-SDK/tree/2.2.0/doc/controllers/vault.md)
* [Transaction Search](https://www.github.com/paypal/PayPal-PHP-Server-SDK/tree/2.2.0/doc/controllers/transaction-search.md)
* [Subscriptions](https://www.github.com/paypal/PayPal-PHP-Server-SDK/tree/2.2.0/doc/controllers/subscriptions.md)

## SDK Infrastructure

### Configuration

* [ProxyConfigurationBuilder](https://www.github.com/paypal/PayPal-PHP-Server-SDK/tree/2.2.0/doc/proxy-configuration-builder.md)
* [LoggingConfigurationBuilder](https://www.github.com/paypal/PayPal-PHP-Server-SDK/tree/2.2.0/doc/logging-configuration-builder.md)
* [RequestLoggingConfigurationBuilder](https://www.github.com/paypal/PayPal-PHP-Server-SDK/tree/2.2.0/doc/request-logging-configuration-builder.md)
* [ResponseLoggingConfigurationBuilder](https://www.github.com/paypal/PayPal-PHP-Server-SDK/tree/2.2.0/doc/response-logging-configuration-builder.md)

### HTTP

* [HttpRequest](https://www.github.com/paypal/PayPal-PHP-Server-SDK/tree/2.2.0/doc/http-request.md)

### Utilities

* [ApiResponse](https://www.github.com/paypal/PayPal-PHP-Server-SDK/tree/2.2.0/doc/api-response.md)

