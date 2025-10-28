# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [3.1.0] - 2025-03-21
### Fixed
- Support for PHP typing.

## [3.0.2] - 2024-12-05
### Fixed
- Support for PHP 8.4

## [3.0.1] - 2020-12-03
### Added
- Support for PHP 8.0

## [3.0.0] - 2019-11-29
### Added
- Support for webp responses under the `ImageFormatter` error handler
- Additional XML content types [#9]

### Changed
- Merged `JpegFormatter`, `GifFormatter` and `PngFormatter` in one `ImageFormatter`.
- By default, all formatters are used.

### Removed
- Support for PHP 7.0 and 7.1
- `defaultFormatter` option. De first value of the array of formatters will be used as default

## [2.0.0] - 2019-05-10
### Added
- Use `phpstan` as a dev dependency to detect bugs

### Changed
- Always catches exceptions
- Create separate classes for error formatters
- Allow any number of formatters to be used
- Allow any exception to define HTTP status code

### Removed
- Ability to handle responses with http error codes (400-599). A new package will be created for that. This package only handles exceptions.
- `HttpErrorException` class (that was simply an extension of `Middlewares\Utils\HttpErrorException`). You can use `Middlewares\Utils\HttpErrorException` directly.

## [1.2.0] - 2018-08-04
### Added
- PSR-17 support
- Added a first argument to the constructor of `ErrorHandlerDefault` to customize the `ResponseFactoryInterface`

## [1.1.0] - 2018-06-25
### Changed
- Use `HttpErrorException` from utils package

## [1.0.0] - 2018-01-26
### Added
- Improved testing and added code coverage reporting
- Added tests for PHP 7.2

### Changed
- Upgraded to the final version of PSR-15 `psr/http-server-middleware`

### Fixed
- Updated license year

## [0.9.0] - 2017-12-16
### Changed
- The request handler used to generate the response must implement `Interop\Http\Server\RequestHandlerInterface`. Removed support for callables.

### Removed
- Removed `arguments()` option.

## [0.8.0] - 2017-11-13
### Changed
- Replaced `http-interop/http-middleware` with  `http-interop/http-server-middleware`.

### Removed
- Removed support for PHP 5.x.

## [0.7.0] - 2017-09-21
### Changed
- Append `.dist` suffix to phpcs.xml and phpunit.xml files
- Changed the configuration of phpcs and php_cs
- Upgraded phpunit to the latest version and improved its config file
- Updated to `http-interop/http-middleware#0.5`

## [0.6.0] - 2017-03-26
### Changed
- Added `Middlewares\HttpErrorException` class to allow to pass data context to the error handler
- Changed the error handler signature. The attribute `error` contains an instance of `Middlewares\HttpErrorException` instead an array.
- Updated to `middlewares/utils#~0.11`

## [0.5.0] - 2017-02-05
### Changed
- Updated to `middlewares/utils#~0.9`

## [0.4.0] - 2016-12-26
### Changed
- Updated tests
- Updated to `http-interop/http-middleware#0.4`
- Updated `friendsofphp/php-cs-fixer#2.0`

## [0.3.0] - 2016-11-22
### Changed
- Updated to `http-interop/http-middleware#0.3`

## [0.2.0] - 2016-11-19
### Added
- New option `attribute()` to change the attribute name used to pass the error info to the handler.

### Changed
- Changed the handler signature to `function(ServerRequestInterface $request)`.
- The error info is passed to the handler using an array stored in the request attribute `error`.

## [0.1.0] - 2016-10-03
First version

[#9]: https://github.com/middlewares/error-handler/issues/9

[3.1.0]: https://github.com/middlewares/error-handler/compare/v3.0.2...v3.1.0
[3.0.2]: https://github.com/middlewares/error-handler/compare/v3.0.1...v3.0.2
[3.0.1]: https://github.com/middlewares/error-handler/compare/v3.0.0...v3.0.1
[3.0.0]: https://github.com/middlewares/error-handler/compare/v2.0.0...v3.0.0
[2.0.0]: https://github.com/middlewares/error-handler/compare/v1.2.0...v2.0.0
[1.2.0]: https://github.com/middlewares/error-handler/compare/v1.1.0...v1.2.0
[1.1.0]: https://github.com/middlewares/error-handler/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/middlewares/error-handler/compare/v0.9.0...v1.0.0
[0.9.0]: https://github.com/middlewares/error-handler/compare/v0.8.0...v0.9.0
[0.8.0]: https://github.com/middlewares/error-handler/compare/v0.7.0...v0.8.0
[0.7.0]: https://github.com/middlewares/error-handler/compare/v0.6.0...v0.7.0
[0.6.0]: https://github.com/middlewares/error-handler/compare/v0.5.0...v0.6.0
[0.5.0]: https://github.com/middlewares/error-handler/compare/v0.4.0...v0.5.0
[0.4.0]: https://github.com/middlewares/error-handler/compare/v0.3.0...v0.4.0
[0.3.0]: https://github.com/middlewares/error-handler/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/middlewares/error-handler/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/middlewares/error-handler/releases/tag/v0.1.0
