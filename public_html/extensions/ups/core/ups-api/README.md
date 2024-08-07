# PHP SDK to UPS RESTful API

The UPS PHP SDK provides convenient access to the UPS RESTful API from
applications written in the PHP language. It includes a batch of sets of
classes for API resources that initialize themselves dynamically from API
responses which makes it compatible with a wide range of versions of the UPS
API.
This SDK based on [official UPS API Documentation Repository](https://github.com/UPS-API/api-documentation)
and generated with [Swagger-Codegen Tool](https://swagger.io/tools/swagger-codegen/)

## Requirements

PHP 7.4 and later.

## Composer

You can install the bindings via [Composer](http://getcomposer.org/). To install all supported APIs run the following command:

```bash
composer require abantecart/ups-php
```
If you prefer to install not all APIs you should to run this command inside selected API directory.



To use the bindings, use Composer's [autoload](https://getcomposer.org/doc/01-basic-usage.md#autoloading):

```php
require_once 'vendor/autoload.php';
```


## Dependencies

The bindings require the following extensions in order to work properly:

-   [`guzzle`](https://github.com/guzzle/guzzle)
-   [`curl`](https://secure.php.net/manual/en/book.curl.php)
-   [`json`](https://secure.php.net/manual/en/book.json.php)
-   [`mbstring`](https://secure.php.net/manual/en/book.mbstring.php) (Multibyte String)

If you use Composer, these dependencies should be handled automatically.

## Getting Started

Generate Access Token:

```php
//YOUR ACCOUNT NUMBER (6 characters)
$accNumber = '******';
//UPS API Credentials (obtain after APP creation)
$clientId = '***YOUR_UPS_API_CLIENT_ID***';
$password = '***YOUR_UPS_API_PASSWORD***';

$config = \UPS\OAuthClientCredentials\Configuration::getDefaultConfiguration()
    ->setUsername($clientId)
    ->setPassword($password);

$apiInstance = new \UPS\OAuthClientCredentials\Request\DefaultApi(
// If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
// This is optional, `GuzzleHttp\Client` will be used as default.
    new \GuzzleHttp\Client(),
    $config
);
$grant_type = "client_credentials"; // string |
$x_merchant_id = $accNumber; // string | Client merchant ID

try {
    $result = $apiInstance->createToken($grant_type, $x_merchant_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->generateToken: ', $e->getMessage(), PHP_EOL;
}
```

Rating request:

```php
// Configure OAuth2 access token for authorization: oauth2
$config = UPS\Rating\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\Rating\Request\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \UPS\Rating\Rating\RATERequestWrapper(); // \UPS\Rating\Rating\RATERequestWrapper | Generate sample code for popular API requests by selecting an example below. To view a full sample request and response, first click "Authorize" and enter your application credentials, then populate the required parameters above and click "Try it out".
$version = "v1"; // string | Indicates Rate API to display the new release features in Rate API response based on Rate release. See the New section for the latest Rate release. Supported values: v1, v1601, v1607, v1701, v1707, v2108, v2205. Length 5
$requestoption = "Shop"; // string | Valid Values: Rate = The server rates (The default Request option is Rate if a Request Option is not provided). Shop = The server validates the shipment, and returns rates for all UPS products from the ShipFrom to the ShipTo addresses. Rate is the only valid request option for Ground Freight Pricing requests. . Length 10
$trans_id = "trans_id_example"; // string | An identifier unique to the request. Length 32
$transaction_src = "testing"; // string | An identifier of the client/source application that is making the request.Length 512
$additionalinfo = ""; // string | Valid Values: timeintransit = The server rates with transit time information combined with requestoption in URL.Rate is the only valid request option for Ground Freight Pricing requests. Length 15

try {
    $result = $apiInstance->rate($body, $version, $requestoption, $trans_id, $transaction_src, $additionalinfo);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->rate: ', $e->getMessage(), PHP_EOL;
}
```

## Documentation

See the [UPS API docs](https://developer.ups.com/catalog?loc=en_US).


