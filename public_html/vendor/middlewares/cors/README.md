# middlewares/cors

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
![Testing][ico-ga]
[![Total Downloads][ico-downloads]][link-downloads]

Middleware to implement Cross-Origin Resource Sharing (CORS) using [neomerx/cors-psr7](https://github.com/neomerx/cors-psr7).

## Requirements

* PHP >= 7.2
* A [PSR-7 http library](https://github.com/middlewares/awesome-psr15-middlewares#psr-7-implementations)
* A [PSR-15 middleware dispatcher](https://github.com/middlewares/awesome-psr15-middlewares#dispatcher)

## Installation

This package is installable and autoloadable via Composer as [middlewares/cors](https://packagist.org/packages/middlewares/cors).

```sh
composer require middlewares/cors
```

## Example

```php
use Neomerx\Cors\Strategies\Settings;
use Neomerx\Cors\Analyzer;

$settings = new Settings();
$settings->setServerOrigin('http', 'example.com', 123);

$analyzer = Analyzer::instance($settings);

$dispatcher = new Dispatcher([
    new Middlewares\Cors($analyzer)
]);

$response = $dispatcher->dispatch(new ServerRequest());
```

## Usage

You have to provide a `Neomerx\Cors\Contracts\AnalyzerInterface` to the constructor. See [neomerx/cors-psr7](https://github.com/neomerx/cors-psr7) for more info. Optionally, you can provide a `Psr\Http\Message\ResponseFactoryInterface` as the second argument to create the responses. If it's not defined, [Middleware\Utils\Factory](https://github.com/middlewares/utils#factory) will be used to detect it automatically.

```php
$analyzer = Analyzer::instance($settings);
$responseFactory = new MyOwnResponseFactory();

$cors = new Middlewares\Cors($analyzer, $responseFactory);
```

---

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes and [CONTRIBUTING](CONTRIBUTING.md) for contributing details.

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/middlewares/cors.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-ga]: https://github.com/middlewares/cors/workflows/testing/badge.svg
[ico-downloads]: https://img.shields.io/packagist/dt/middlewares/cors.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/middlewares/cors
[link-downloads]: https://packagist.org/packages/middlewares/cors
