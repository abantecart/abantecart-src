# middlewares/error-handler

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
![Testing][ico-ga]
[![Total Downloads][ico-downloads]][link-downloads]

Middleware to catch and format errors encountered while handling the request.

## Requirements

* PHP >= 7.2
* A [PSR-7 http library](https://github.com/middlewares/awesome-psr15-middlewares#psr-7-implementations)
* A [PSR-15 middleware dispatcher](https://github.com/middlewares/awesome-psr15-middlewares#dispatcher)

## Installation

This package is installable and autoloadable via Composer as [middlewares/error-handler](https://packagist.org/packages/middlewares/error-handler).

```shell
composer require middlewares/error-handler
```

## Example

```php
use Middlewares\ErrorFormatter;
use Middlewares\ErrorHandler;
use Middlewares\Utils\Dispatcher;

// Create a new ErrorHandler instance
// Any number of formatters can be added. One will be picked based on the Accept
// header of the request. If no formatter matches, the first formatter in the array
// will be used.
$errorHandler = new ErrorHandler([
    new ErrorFormatter\HtmlFormatter(),
    new ErrorFormatter\ImageFormatter(),
    new ErrorFormatter\JsonFormatter(),
    new ErrorFormatter\PlainFormatter(),
    new ErrorFormatter\SvgFormatter(),
    new ErrorFormatter\XmlFormatter(),
]);

// ErrorHandler should always be the first middleware in the stack!
$dispatcher = new Dispatcher([
    $errorHandler,
    // ...
    function ($request) {
        throw HttpErrorException::create(404);
    }
]);

$request = $serverRequestFactory->createServerRequest('GET', '/');
$response = $dispatcher->dispatch($request);
```

## Usage

Add the [formatters](src/Formatter) to be used (instances of `Middlewares\ErrorFormatter\FormatterInterface`). If no formatters are provided, use all available.

```php
$errorHandler = new ErrorHandler([
    new ErrorFormatter\HtmlFormatter(),
    new ErrorFormatter\JsonFormatter()
]);
```

**Note:** If no formatter is found, the first value of the array will be used. In the example above, `HtmlFormatter`.

---

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes and [CONTRIBUTING](CONTRIBUTING.md) for contributing details.

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/middlewares/error-handler.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-ga]: https://github.com/middlewares/error-handler/workflows/testing/badge.svg
[ico-downloads]: https://img.shields.io/packagist/dt/middlewares/error-handler.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/middlewares/error-handler
[link-downloads]: https://packagist.org/packages/middlewares/error-handler
