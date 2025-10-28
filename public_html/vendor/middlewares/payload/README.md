# middlewares/payload

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
![Testing][ico-ga]
[![Total Downloads][ico-downloads]][link-downloads]

Parses the body of the request if it's not parsed and the method is POST, PUT or DELETE. It contains the following components to support different formats:

* [JsonPayload](#jsonpayload)
* [UrlEncodePayload](#urlencodepayload)
* [CsvPayload](#csvpayload)
* [XmlPayload](#xmlpayload)

Failure to parse the body will result in a `Middlewares\Utils\HttpErrorException` being thrown. See [middlewares/utils](https://github.com/middlewares/utils#httperrorexception) for additional details.

## Requirements

* PHP >= 7.2
* A [PSR-7 http library](https://github.com/middlewares/awesome-psr15-middlewares#psr-7-implementations)
* A [PSR-15 middleware dispatcher](https://github.com/middlewares/awesome-psr15-middlewares#dispatcher)

## Installation

This package is installable and autoloadable via Composer as [middlewares/payload](https://packagist.org/packages/middlewares/payload).

```sh
composer require middlewares/payload
```

## JsonPayload

Parses the JSON payload of the request.

```php
Dispatcher::run([
    (new Middlewares\JsonPayload())
        ->associative(false)
        ->depth(64)
]);

$response = $dispatcher->dispatch(new ServerRequest());
```

Contains the following options to configure the [json_decode](http://php.net/manual/en/function.json-decode.php) function:

### associative

Enabled by default, convert the objects into associative arrays.

```php
//Disable associative arrays
$payload = (new Middlewares\JsonPayload())->associative(false);
```

### depth

To configure the recursion depth option of json_decode. By default is `512`.

### options

To pass the bitmask of json_decode options: `JSON_BIGINT_AS_STRING` (enabled by default), `JSON_OBJECT_AS_ARRAY`, `JSON_THROW_ON_ERROR`.

### methods

To configure the allowed methods. By default only the requests with the method `POST, PUT, PATCH, DELETE, COPY, LOCK, UNLOCK` are handled.

```php
//Parse json only with POST and PUT requests
$payload = (new Middlewares\JsonPayload())->methods(['POST', 'PUT']);
```

### contentType

To configure all `Content-Type` headers allowed in the request. By default is `application/json`

```php
//Parse json only in request with these two Content-Type values
$payload = (new Middlewares\JsonPayload())->contentType(['application/json', 'text/json']);
```

### override

To override the previous parsed body if exists (`false` by default)


## UrlEncodePayload

Parses the url-encoded payload of the request.

```php
Dispatcher::run([
    new Middlewares\UrlEncodePayload()
]);
```

### methods

To configure the allowed methods. By default only the requests with the method `POST, PUT, PATCH, DELETE, COPY, LOCK, UNLOCK` are handled.

### contentType

To configure all Content-Type headers allowed in the request. By default is `application/x-www-form-urlencoded`

### override

To override the previous parsed body if exists (`false` by default)


## CsvPayload

CSV payloads are supported by the [middlewares/csv-payload](https://packagist.org/packages/middlewares/csv-payload) package.


## XmlPayload

Parses the XML payload of the request. Parsed body will return an instance of [SimpleXMLElement](https://www.php.net/manual/en/class.simplexmlelement.php).

### methods

To configure the allowed methods. By default only the requests with the method `POST, PUT, PATCH, DELETE, COPY, LOCK, UNLOCK` are handled.

### contentType

To configure all Content-Type headers allowed in the request. By default is `text/xml`, `application/xml` and `application/x-xml`.

### override

To override the previous parsed body if exists (`false` by default)

---

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes and [CONTRIBUTING](CONTRIBUTING.md) for contributing details.

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/middlewares/payload.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-ga]: https://github.com/middlewares/payload/workflows/testing/badge.svg
[ico-downloads]: https://img.shields.io/packagist/dt/middlewares/payload.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/middlewares/payload
[link-downloads]: https://packagist.org/packages/middlewares/payload
