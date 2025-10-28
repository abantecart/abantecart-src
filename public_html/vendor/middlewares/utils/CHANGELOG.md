# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [4.0.2] - 2025-01-23
### Fixed
- PHP 8.4 deprecation errors (implicitly nullable parameter) [#30]

## [4.0.1] - 2024-11-21
### Fixed
- Support for Php 8.4 [#29].

## [4.0.0] - 2023-12-17
### Added
- Support for psr/http-message 2.x (if you don't use slim/psr7 or sunrise/http-message as factory)

### Changed
- Updated dependencies and tests

### Removed
- Support for PHP prior to 8.1.

## [3.3.0] - 2021-07-04
### Added
- Support for psr/container v2.x [#26]

## [3.2.0] - 2020-11-30
### Fixed
- Added support for PHP 8 [#22] [#23]

## [3.1.0] - 2020-01-19
### Changed
- Zend Diactoros is deprecated, switched to Laminas Diactoros [#20], [#21].
  THIS IS A BREAKING CHANGE, so, if you want to keep using Zend Diactoros, you should configure the `Factory` as follows:
  ```php
  Factory::setFactory(
    new FactoryDiscovery([
        'request' => 'Zend\Diactoros\RequestFactory',
        'response' => 'Zend\Diactoros\ResponseFactory',
        'serverRequest' => 'Zend\Diactoros\ServerRequestFactory',
        'stream' => 'Zend\Diactoros\StreamFactory',
        'uploadedFile' => 'Zend\Diactoros\UploadedFileFactory',
        'uri' => 'Zend\Diactoros\UriFactory',
    ])
  );
  ```

## [3.0.1] - 2019-11-29
### Fixed
- Moved a dependency to dev
- Updated docs

## [3.0.0] - 2019-11-29
### Added
- Added `FactoryInterface` that returns all PSR-17 factories
- Added `FactoryDiscovery` class to discover automatically PSR-17 implementation libraries
- Added `Factory::getFactory()` and `Factory::setFactory()` to set manually PSR-17 factories
- Added `Factory::getResponseFactory()`
- Added `Factory::getRequestFactory()`
- Added `Factory::getServerRequestFactory()`
- Added `Factory::getStreamFactory()`
- Added `Factory::getUriFactory()`
- Added `Factory::getUploadedFileFactory()`
- Added `Sunrise` to the list of factories detected automatically

### Removed
- Support for PHP 7.0 and 7.1
- `Factory::setStrategy`
- `HttpErrorException::setContext` method, to make the exception class inmutable
- Traits `HasResponseFactory` and `HasStreamFactory`

## [2.2.0] - 2019-03-05
### Added
- `Middlewares\Utils\Dispatcher` implements `RequestHandlerInterface` [#16], [#17]

## [2.1.1] - 2018-08-11
### Added
- Added `Nyholm\Psr7` to the list of factories detected automatically

## [2.1.0] - 2018-08-02
### Added
- New trait `HasResponseFactory` used by many middlewares that need to configure the PSR-17 response factory.
- New trait `HasStreamFactory` used by many middlewares that need to configure the PSR-17 stream factory.

## [2.0.0] - 2018-08-01
### Added
- New methods added to `Factory` to return PSR-17 factories: `getResponseFactory`, `getServerRequestFactory`, `getStreamFactory` and `getUriFactory`.
- New method `Factory::setStrategies()` to configure the priority order of the Diactoros, Guzzle and Slim factories or register new classes.
- Added a second argument to `Callablehandler` constructor to pass a response factory

### Changed
- Exchanged abandoned `http-interop/http-factory` with `psr/http-factory`
- Changed the signature of `Factory::createServerRequest()` to be aligned with PSR-17
- Changed the signature of `Factory::createStream()` to be aligned with PSR-17
- Changed the signature of `Factory::createResponse()` to be aligned with PSR-17

## [1.2.0] - 2018-07-17
### Changed
- Updated `http-interop/http-factory` to `0.4`

## [1.1.0] - 2018-06-25
### Added
- Imported `HttpErrorException` from error handler middleware

## [1.0.0] - 2018-01-24
### Changed
- Replaced `http-interop/http-server-middleware` with `psr/http-server-middleware`.

### Removed
- Removed `Middlewares\Utils\Helpers` because contains just one helper and it's no longer needed.

## [0.14.0] - 2017-12-16
### Added
- New class `RequestHandlerContainer` implementing PSR-11 to resolve handlers in any format (classes, callables) and return PSR-15 `RequestHandlerInterface` instances. This can be used to resolve router handlers, for example.

### Changed
- The signature of `CallableHandler` was simplified. Removed `$resolver` and `$arguments` in the constructor.

### Removed
- Deleted all callable resolvers classes. Use the `RequestHandlerContainer`, or any other PSR-11 implementation.

## [0.13.0] - 2017-11-16
### Changed
- The minimum PHP version supported is 7.0
- Replaced `http-interop/http-middleware` with `http-interop/http-server-middleware`.
- Changed `Middlewares\Utils\CallableHandler` signature. Now it is instantiable and can be used as middleware and server request handler.

### Removed
- `Middlewares\Utils\CallableMiddleware`. Use `Middlewares\Utils\CallableHandler` instead.

## [0.12.0] - 2017-09-18
### Changed
- Append `.dist` suffix to phpcs.xml and phpunit.xml files
- Changed the configuration of phpcs and php_cs
- Upgraded phpunit to the latest version and improved its config file
- Updated `http-interop/http-middleware` to `0.5`

## [0.11.1] - 2017-05-06
### Changed
- `Middlewares\Utils\CallableHandler` expects one of the following values returned by the callable:
  * A `Psr\Http\Message\ResponseInterface`
  * `null` or scalar
  * an object with `__toString` method implemented
  Otherwise, throws an `UnexpectedValueException`
- `Middlewares\Helpers::fixContentLength` only modifies or removes the `Content-Length` header, but does not add it if didn't exist previously.

## [0.11.0] - 2017-03-25
### Added
- New class `Middlewares\Utils\Helpers` with common helpers to manipulate PSR-7 messages
- New helper `Middlewares\Utils\Helpers::fixContentLength` used to add/modify/remove the `Content-Length` header of a http message.

### Changed
- Updated `http-interop/http-factory` to `0.3`

## [0.10.1] - 2017-02-27
### Fixed
- Fixed changelog file

## [0.10.0] - 2017-02-27
### Changed
- Replaced deprecated `container-interop` by `psr/contaienr` (PSR-11).
- `Middlewares\Utils\Dispatcher` throws exceptions if the middlewares does not implement `Interop\Http\ServerMiddleware\MiddlewareInterface` or does not return an instance of `Psr\Http\Message\ResponseInterface`.
- Moved the default factories to `Middlewares\Utils\Factory` namespace.
- Minor code improvements.

## [0.9.0] - 2017-02-05
### Added
- Callable resolves to create callables from various representations

### Removed
- `Middlewares\Utils\CallableHandler::resolve`

## [0.8.0] - 2016-12-22
### Changed
- Updated `http-interop/http-middleware` to `0.4`
- Updated `friendsofphp/php-cs-fixer` to `2.0`

## [0.7.0] - 2016-12-06
### Added
- New static helper `Middlewares\Utils\Dispatcher::run` to create and dispatch a request easily

## [0.6.1] - 2016-12-06
### Fixed
- Ensure that the body of the serverRequest is writable and seekable.

## [0.6.0] - 2016-12-06
### Added
- ServerRequest factory
- `Middlewares\Utils\Dispatcher` accepts `Closure` as middleware components

### Changed
- `Middlewares\Utils\Dispatcher` creates automatically a response if the stack is exhausted

## [0.5.0] - 2016-11-22
### Added
- `Middlewares\Utils\CallableMiddleware` class, to create middlewares from callables
- `Middlewares\Utils\Dispatcher` class, to execute the middleware stack and return a response.

## [0.4.0] - 2016-11-13
### Changed
- Updated `http-interop/http-factory` to `0.2`

## [0.3.1] - 2016-10-03
### Fixed
- Bug in CallableHandler that resolve to the declaring class of a method instead the final class.

## [0.3.0] - 2016-10-03
### Added
- `Middlewares\Utils\CallableHandler` class, allowing to resolve and execute callables safely.

## [0.2.0] - 2016-10-01
### Added
- Uri factory

## [0.1.0] - 2016-09-30
### Added
- Response factory
- Stream factory

[#16]: https://github.com/middlewares/utils/issues/16
[#17]: https://github.com/middlewares/utils/issues/17
[#20]: https://github.com/middlewares/utils/issues/20
[#21]: https://github.com/middlewares/utils/issues/21
[#22]: https://github.com/middlewares/utils/issues/22
[#23]: https://github.com/middlewares/utils/issues/23
[#26]: https://github.com/middlewares/utils/issues/26
[#29]: https://github.com/middlewares/utils/issues/29
[#30]: https://github.com/middlewares/utils/issues/30

[4.0.2]: https://github.com/middlewares/utils/compare/v4.0.1...v4.0.2
[4.0.1]: https://github.com/middlewares/utils/compare/v4.0.0...v4.0.1
[4.0.0]: https://github.com/middlewares/utils/compare/v3.3.0...v4.0.0
[3.3.0]: https://github.com/middlewares/utils/compare/v3.2.0...v3.3.0
[3.2.0]: https://github.com/middlewares/utils/compare/v3.1.0...v3.2.0
[3.1.0]: https://github.com/middlewares/utils/compare/v3.0.1...v3.1.0
[3.0.1]: https://github.com/middlewares/utils/compare/v3.0.0...v3.0.1
[3.0.0]: https://github.com/middlewares/utils/compare/v2.2.0...v3.0.0
[2.2.0]: https://github.com/middlewares/utils/compare/v2.1.1...v2.2.0
[2.1.1]: https://github.com/middlewares/utils/compare/v2.1.0...v2.1.1
[2.1.0]: https://github.com/middlewares/utils/compare/v2.0.0...v2.1.0
[2.0.0]: https://github.com/middlewares/utils/compare/v1.2.0...v2.0.0
[1.2.0]: https://github.com/middlewares/utils/compare/v1.1.0...v1.2.0
[1.1.0]: https://github.com/middlewares/utils/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/middlewares/utils/compare/v0.14.0...v1.0.0
[0.14.0]: https://github.com/middlewares/utils/compare/v0.13.0...v0.14.0
[0.13.0]: https://github.com/middlewares/utils/compare/v0.12.0...v0.13.0
[0.12.0]: https://github.com/middlewares/utils/compare/v0.11.1...v0.12.0
[0.11.1]: https://github.com/middlewares/utils/compare/v0.11.0...v0.11.1
[0.11.0]: https://github.com/middlewares/utils/compare/v0.10.1...v0.11.0
[0.10.1]: https://github.com/middlewares/utils/compare/v0.10.0...v0.10.1
[0.10.0]: https://github.com/middlewares/utils/compare/v0.9.0...v0.10.0
[0.9.0]: https://github.com/middlewares/utils/compare/v0.8.0...v0.9.0
[0.8.0]: https://github.com/middlewares/utils/compare/v0.7.0...v0.8.0
[0.7.0]: https://github.com/middlewares/utils/compare/v0.6.1...v0.7.0
[0.6.1]: https://github.com/middlewares/utils/compare/v0.6.0...v0.6.1
[0.6.0]: https://github.com/middlewares/utils/compare/v0.5.0...v0.6.0
[0.5.0]: https://github.com/middlewares/utils/compare/v0.4.0...v0.5.0
[0.4.0]: https://github.com/middlewares/utils/compare/v0.3.1...v0.4.0
[0.3.1]: https://github.com/middlewares/utils/compare/v0.3.0...v0.3.1
[0.3.0]: https://github.com/middlewares/utils/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/middlewares/utils/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/middlewares/utils/releases/tag/v0.1.0
