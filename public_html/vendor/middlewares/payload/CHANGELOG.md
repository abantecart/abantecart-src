# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [3.1.0] - 2025-03-21
### Added
- Support for PHP 8.4

## [3.0.2] - 2024-01-12
### Fixed
- Updated dependencies.

## [3.0.1] - 2020-12-02
### Added
- Support for PHP 8

## [3.0.0] - 2019-11-29
### Added
- New `XmlPayload` middleware [#9]
- Throw `JsonException` on error (Php ^7.3)

### Removed
- Support for PHP 7.0 and 7.1

## [2.1.1] - 2018-11-08
### Fixed
- Use `phpstan` as a dev dependency to detect bugs
- Fixed disabled associative config, that could return `null|array|object` instead only array [#8]

## [2.1.0] - 2018-08-04
### Added
- PSR-17 support

## [2.0.0] - 2018-06-25
### Changed
- Moved CSV parsing to `middlewares/csv-payload`
- Parsing errors will result in an exception being thrown
- Strict types added

## [1.0.0] - 2018-01-25
### Added
- Improved testing and added code coverage reporting
- Added tests for PHP 7.2

### Changed
- Upgraded to the final version of PSR-15 `psr/http-server-middleware`

### Fixed
- Updated license year

## [0.6.0] - 2017-11-13
### Changed
- Replaced `http-interop/http-middleware` with  `http-interop/http-server-middleware`.

### Removed
- Removed support for PHP 5.x.

## [0.5.0] - 2017-09-21
### Changed
- The `contentType()` argument is an array instead a string, allowing to assign multiple values
- Append `.dist` suffix to phpcs.xml and phpunit.xml files
- Changed the configuration of phpcs and php_cs
- Upgraded phpunit to the latest version and improved its config file
- Updated to `http-interop/http-middleware#0.5`

## [0.4.0] - 2017-02-05
### Added
- New option `contentType()` to configure the `Content-Type` request header
- Improve CsvPayload
  - New option `delimiter()` to configure the CSV delimiter character
  - New option `enclosure()` to configure the CSV enclosure character
  - New option `escape()` to configure the CSV escape character
- Fixed
  - CsvPayload: `StreamInterface` fixed left undetached

## [0.3.0] - 2016-12-26
### Changed
- Updated tests
- Updated to `http-interop/http-middleware#0.4`
- Updated `friendsofphp/php-cs-fixer#2.0`

## [0.2.0] - 2016-11-27
### Added
- New option `methods()` to configure the allowed methods
- New option `override()` to configure if the previous parsed body must be overrided

### Changed
- Updated to `http-interop/http-middleware#0.3`

## [0.1.0] - 2016-10-04
First version

[#8]: https://github.com/middlewares/payload/issues/8
[#9]: https://github.com/middlewares/payload/issues/9

[3.1.0]: https://github.com/middlewares/payload/compare/v3.0.2...v3.1.0
[3.0.2]: https://github.com/middlewares/payload/compare/v3.0.1...v3.0.2
[3.0.1]: https://github.com/middlewares/payload/compare/v3.0.0...v3.0.1
[3.0.0]: https://github.com/middlewares/payload/compare/v2.1.1...v3.0.0
[2.1.1]: https://github.com/middlewares/payload/compare/v2.1.0...v2.1.1
[2.1.0]: https://github.com/middlewares/payload/compare/v2.0.0...v2.1.0
[2.0.0]: https://github.com/middlewares/payload/compare/v1.0.0...v2.0.0
[1.0.0]: https://github.com/middlewares/payload/compare/v0.6.0...v1.0.0
[0.6.0]: https://github.com/middlewares/payload/compare/v0.5.0...v0.6.0
[0.5.0]: https://github.com/middlewares/payload/compare/v0.4.0...v0.5.0
[0.4.0]: https://github.com/middlewares/payload/compare/v0.3.0...v0.4.0
[0.3.0]: https://github.com/middlewares/payload/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/middlewares/payload/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/middlewares/payload/releases/tag/v0.1.0
