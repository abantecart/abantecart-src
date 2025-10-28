# middlewares/negotiation

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
![Testing][ico-ga]
[![Total Downloads][ico-downloads]][link-downloads]

Middleware using [wildurand/Negotiation](https://github.com/willdurand/Negotiation) to implement content negotiation. Contains the following components:

* [ContentType](#contenttype)
* [ContentLanguage](#contentlanguage)
* [ContentEncoding](#contentencoding)

## Requirements

* PHP >= 7.2
* A [PSR-7 http library](https://github.com/middlewares/awesome-psr15-middlewares#psr-7-implementations)
* A [PSR-15 middleware dispatcher](https://github.com/middlewares/awesome-psr15-middlewares#dispatcher)

## Installation

This package is installable and autoloadable via Composer as [middlewares/negotiation](https://packagist.org/packages/middlewares/negotiation).

```sh
composer require middlewares/negotiation
```

## Example

```php
Dispatcher::run([
    new Middlewares\ContentType(),
    new Middlewares\ContentLanguage(['en', 'gl', 'es']),
    new Middlewares\ContentEncoding(['gzip', 'deflate']),
]);
```

## ContentType

To detect the preferred mime type using the `Accept` header and the file extension and edit the header with this value. A `Content-Type` header is also added to the response if it's missing.

Define the formats to negotiate sorted by priority in the first argument. By default uses [these](src/formats_defaults.php)

```php
//Use the default types
$negotiator = new Middlewares\ContentType();

//Use only few types
$negotiator = new Middlewares\ContentType(['html', 'json']);

//Use only few types and configure some of them
$negotiator = new Middlewares\ContentType([
    'html',
    'json',
    'txt' => [
        'extension' => ['txt'],
        'mime-type' => ['text/plain'],
        'charset' => true,
    ]
]);
```

### errorResponse

If no format matches the negotiation, by default the middleware use the first value in the list of available formats (by default `text/html`). Use this option to return a `406` error. Optionally, you can provide a `Psr\Http\Message\ResponseFactoryInterface` that will be used to create the response. If it's not defined, [Middleware\Utils\Factory](https://github.com/middlewares/utils#factory) will be used to detect it automatically.

```php
$responseFactory = new MyOwnResponseFactory();

//Use default html format (the first provided) if no valid format was detected (By default)
$negotiator = new Middlewares\ContentType(['html', 'json']);

//Return a 406 response if no valid format was detected
$negotiator = (new Middlewares\ContentType(['html', 'json']))->errorResponse();

//Return a 406 response using a specific responseFactory if no valid format was detected
$negotiator = (new Middlewares\ContentType(['html', 'json']))->errorResponse($responseFactory);
```

### charsets

The available charsets to negotiate with the `Accept-Charset` header. By default is `UTF-8`.

```php
$negotiator = (new Middlewares\ContentType())->charsets(['UTF-8', 'ISO-8859-1']);
```

### noSniff

Adds the `X-Content-Type-Options: nosniff` header, to mitigating [MIME confusión attacks.](https://blog.mozilla.org/security/2016/08/26/mitigating-mime-confusion-attacks-in-firefox/). Enabled by default.

```php
//Disable noSniff header
$negotiator = (new Middlewares\ContentType())->noSniff(false);
```

### attribute

To store the format name (`json`, `html`, `css` etc) in an attribute of the `ServerRequest`.

## ContentLanguage

To detect the preferred language using the `Accept-Language` header or the path prefix and edit the header with this value. A `Content-Language` header is also added to the response if it's missing.

The first argument is an array with the available languages to negotiate sorted by priority. The first value will be used as default if no other languages is choosen in the negotiation.

```php
$request = Factory::createServerRequest('GET', '/')
    ->withHeader('Accept-Language', 'gl-es, es;q=0.8, en;q=0.7');

Dispatcher::run([
    new Middlewares\ContentLanguage(['es', 'en']),

    function ($request) {
        $language = $request->getHeaderLine('Accept-Language');

        switch ($language) {
            case 'es':
                return 'Hola mundo';
            case 'en':
                return 'Hello world';
        }
    }
], $request);
```

### usePath

By enabling this option, the base path will be used to detect the language. This is useful if you have different paths for each language, for example `/gl/foo` and `/en/foo`. 

Note: the language in the path has preference over the `Accept-Language` header.

```php
$request = Factory::createServerRequest('GET', '/en/hello-world');

Dispatcher::run([
    (new Middlewares\ContentLanguage(['es', 'en']))->usePath(),

    function ($request) {
        $language = $request->getHeaderLine('Accept-Language');

        switch ($language) {
            case 'es':
                return 'Hola mundo';
            case 'en':
                return 'Hello world';
        }
    }
], $request);
```

### redirect

Used to return a `302` responses redirecting to the path containing the language. This only works if `usePath` is enabled, so for example, if the request uri is `/welcome`, returns a redirection to `/en/welcome`.

```php
$responseFactory = new MyOwnResponseFactory();

//Use not only the Accept-Language header but also the path prefix to detect the language
$negotiator = (new Middlewares\ContentLanguage(['es', 'en']))->usePath();

//Returns a redirection with the language in the path if it's missing
$negotiator = (new Middlewares\ContentLanguage(['es', 'en']))->usePath()->redirect();

//Returns a redirection using a specific response factory
$negotiator = (new Middlewares\ContentLanguage(['es', 'en']))->usePath()->redirect($responseFactory);
```

## ContentEncoding

To detect the preferred encoding type using the `Accept-Encoding` header and edit the header with this value.

```php
$request = Factory::createServerRequest('GET', '/')
    ->withHeader('Accept-Encoding', 'gzip,deflate');

Dispatcher::run([
    new Middlewares\ContentEncoding(['gzip']),

    function ($request) {
        echo $request->getHeaderLine('Accept-Encoding'); //gzip
    }
], $request);
```

---

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes and [CONTRIBUTING](CONTRIBUTING.md) for contributing details.

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/middlewares/negotiation.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-ga]: https://github.com/middlewares/negotiation/workflows/testing/badge.svg
[ico-downloads]: https://img.shields.io/packagist/dt/middlewares/negotiation.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/middlewares/negotiation
[link-downloads]: https://packagist.org/packages/middlewares/negotiation
