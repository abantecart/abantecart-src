# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [2.2.0] - 2025-03-21
### Added
- Better typing support.

## [2.1.2] - 2025-03-14
### Fixed
- Php 8.4 compatibility [#12]

## [2.1.1] - 2024-03-24
### Fixed
- Allow middlewares/utils 4.0

## [2.1.0] - 2022-01-08
### Added
- New option `attribute()` for `ContentType` middleware, to save the format name in a `ServerRequest` attribute [#10].

## [2.0.2] - 2020-12-02
### Added
- Support for PHP 8

## [2.0.1] - 2020-03-07
### Fixed
- If no Accept header field is present, returns the default format instead a `406` response, according to the specs [#6], [#8]

## [2.0.0] - 2019-11-29
### Added
- The array of formats passed to `ContentType` middleware can contain plain values, for example `new Middlewares\ContentType(['html', 'json'])`, so it's not required to provide the full data for each format (the list of headers, extensions, etc).

### Changed
- The option `ContentLanguage::redirect()` accepts a `ResponseFactoryInterface` as argument.
- The option `ContentType::errorResponse()` accepts a `ResponseFactoryInterface` as argument.

### Removed
- Support for PHP 7.0 and 7.1
- Option `responseFactory` in `ContentLanguage` and `ContentType` middlewares

## [1.1.0] - 2018-08-04
### Added
- PSR-17 support
- New option `responseFactory` in `ContentLanguage` and `ContentType` middlewares

## [1.0.0] - 2018-01-25
### Added
- Improved testing and added code coverage reporting
- Added tests for PHP 7.2

### Changed
- Upgraded to the final version of PSR-15 `psr/http-server-middleware`
- Changed namespace of `Middlewares\NegotiationTrait` (from `Middlewares\Utils\NegotiationTrait`)

### Fixed
- Updated license year

## [0.5.0] - 2017-11-13
### Changed
- Replaced `http-interop/http-middleware` with  `http-interop/http-server-middleware`.

### Removed
- Removed support for PHP 5.x.

## [0.4.0] - 2017-09-21
### Added
- **ContentType:** New static function `ContentType::getDefaultFormats()` that returns the default formats used.
- **ContentType:** New option `useDefault` option to enable/disable the default format. By default is enabled. If it's disabled, a 406 response is returned when no content-type is found.

### Changed
- Append `.dist` suffix to phpcs.xml and phpunit.xml files
- Changed the configuration of phpcs and php_cs
- Upgraded phpunit to the latest version and improved its config file

### Removed
- **ContentType:** Removed the `defaultFormat` option and use always the first element in the formats list.

## [0.3.1] - 2017-05-18
### Added
- Added `kml` to the list of the default formats

## [0.3.0] - 2016-12-26
### Added
- New method `ContentType::charsets()` to define the available charsets and negotiate the `Accept-Charset` header.

### Changed
- Updated tests
- Updated to `http-interop/http-middleware#0.4`
- Updated `friendsofphp/php-cs-fixer#2.0`

### Fixed
- Only text-based formats (html, text, css, etc) adds the `charset=[charset]` sufix to `Content-Type` header.

## [0.2.0] - 2016-11-22
### Added
- New `ContentType::noSniff()` option to add the `X-Content-Type-Options: nosniff` header (enabled by default)

### Changed
- Updated to `http-interop/http-middleware#0.3`

### Fixed
- *ContentEncoding* middleware removes the `Accept-Encoding` header if it does not match with any available option.

## [0.1.0] - 2016-10-01
First version

[#6]: https://github.com/middlewares/negotiation/issues/6
[#8]: https://github.com/middlewares/negotiation/issues/8
[#10]: https://github.com/middlewares/negotiation/issues/10
[#12]: https://github.com/middlewares/negotiation/issues/12

[2.2.0]: https://github.com/middlewares/negotiation/compare/v2.1.2...v2.2.0
[2.1.2]: https://github.com/middlewares/negotiation/compare/v2.1.1...v2.1.2
[2.1.1]: https://github.com/middlewares/negotiation/compare/v2.1.0...v2.1.1
[2.1.0]: https://github.com/middlewares/negotiation/compare/v2.0.2...v2.1.0
[2.0.2]: https://github.com/middlewares/negotiation/compare/v2.0.1...v2.0.2
[2.0.1]: https://github.com/middlewares/negotiation/compare/v2.0.0...v2.0.1
[2.0.0]: https://github.com/middlewares/negotiation/compare/v1.1.0...v2.0.0
[1.1.0]: https://github.com/middlewares/negotiation/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/middlewares/negotiation/compare/v0.5.0...v1.0.0
[0.5.0]: https://github.com/middlewares/negotiation/compare/v0.4.0...v0.5.0
[0.4.0]: https://github.com/middlewares/negotiation/compare/v0.3.1...v0.4.0
[0.3.1]: https://github.com/middlewares/negotiation/compare/v0.3.0...v0.3.1
[0.3.0]: https://github.com/middlewares/negotiation/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/middlewares/negotiation/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/middlewares/negotiation/releases/tag/v0.1.0
