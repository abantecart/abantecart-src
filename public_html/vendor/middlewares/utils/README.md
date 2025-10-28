# middlewares/utils

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
![Testing][ico-ga]
[![Total Downloads][ico-downloads]][link-downloads]

Common utilities used by the middlewares' packages:

* [Factory](#factory)
* [Dispatcher](#dispatcher)
* [CallableHandler](#callablehandler)
* [HttpErrorException](#httperrorexception)

## Installation

This package is installable and autoloadable via Composer as [middlewares/utils](https://packagist.org/packages/middlewares/utils).

```sh
composer require middlewares/utils
```

## Factory

Used to create PSR-7 and PSR-17 instances.
Detects automatically [Diactoros](https://github.com/laminas/laminas-diactoros), [Guzzle](https://github.com/guzzle/psr7), [Slim](https://github.com/slimphp/Slim), [Nyholm/psr7](https://github.com/Nyholm/psr7) and [Sunrise](https://github.com/sunrise-php) but you can register a different factory using the [psr/http-factory](https://github.com/php-fig/http-factory) interface.

```php
use Middlewares\Utils\Factory;
use Middlewares\Utils\FactoryDiscovery;

// Create PSR-7 instances
$request = Factory::createRequest('GET', '/');
$serverRequest = Factory::createServerRequest('GET', '/');
$response = Factory::createResponse(200);
$stream = Factory::createStream('Hello world');
$uri = Factory::createUri('http://example.com');
$uploadedFile = Factory::createUploadedFile($stream);

// Get PSR-17 instances (factories)
$requestFactory = Factory::getRequestFactory();
$serverRequestFactory = Factory::getServerRequestFactory();
$responseFactory = Factory::getResponseFactory();
$streamFactory = Factory::getStreamFactory();
$uriFactory = Factory::getUriFactory();
$uploadedFileFactory = Factory::getUploadedFileFactory();

// By default, use the FactoryDiscovery class that detects diactoros, guzzle, slim, nyholm and sunrise (in this order of priority),
// but you can change it and add other libraries

Factory::setFactory(new FactoryDiscovery(
    'MyApp\Psr17Factory',
    FactoryDiscovery::SLIM,
    FactoryDiscovery::GUZZLE,
    FactoryDiscovery::DIACTOROS
));

//And also register directly an initialized factory
Factory::getFactory()->setResponseFactory(new FooResponseFactory());

$fooResponse = Factory::createResponse();
```

## Dispatcher

Minimalist PSR-15 compatible dispatcher. Used for testing purposes.

```php
use Middlewares\Utils\Dispatcher;

$response = Dispatcher::run([
    new Middleware1(),
    new Middleware2(),
    new Middleware3(),
    function ($request, $next) {
        $response = $next->handle($request);
        return $response->withHeader('X-Foo', 'Bar');
    }
]);
```

## CallableHandler

To resolve and execute a callable. It can be used as a middleware, server request handler or a callable:

```php
use Middlewares\Utils\CallableHandler;

$callable = new CallableHandler(function () {
    return 'Hello world';
});

$response = $callable();

echo $response->getBody(); //Hello world
```

## HttpErrorException

General purpose exception used to represent HTTP errors.

```php
use Middlewares\Utils\HttpErrorException;

try {
    $context = ['problem' => 'Something bad happened'];
    throw HttpErrorException::create(500, $context);
} catch (HttpErrorException $exception) {
    $context = $exception->getContext();
}
```

---

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes and [CONTRIBUTING](CONTRIBUTING.md) for contributing details.

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/middlewares/utils.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-ga]: https://github.com/middlewares/utils/workflows/testing/badge.svg
[ico-downloads]: https://img.shields.io/packagist/dt/middlewares/utils.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/middlewares/utils
[link-downloads]: https://packagist.org/packages/middlewares/utils
