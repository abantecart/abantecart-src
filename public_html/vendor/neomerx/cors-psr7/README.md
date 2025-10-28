[![Code Coverage](https://scrutinizer-ci.com/g/neomerx/cors-psr7/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/neomerx/cors-psr7/?branch=master)
[![License](https://img.shields.io/packagist/l/neomerx/cors-psr7.svg)](https://packagist.org/packages/neomerx/cors-psr7)

## Description

This package has framework-agnostic [Cross-Origin Resource Sharing](http://www.w3.org/TR/cors/) (CORS) implementation. It is compliant with [PSR-7](http://www.php-fig.org/psr/psr-7/) HTTP message interfaces.

Why this package?

- Implementation is based on [CORS specification](http://www.w3.org/TR/cors/).
- Works with [PSR-7 HTTP message interfaces](http://www.php-fig.org/psr/psr-7/).
- Supports debug mode with [PSR-3 Logger Interface](http://www.php-fig.org/psr/psr-3/).
- Flexible, modular and extensible solution.
- High code quality. **100%** test coverage.
- Free software license [Apache 2.0](LICENSE).

## Sample usage

The package is designed to be used as a middleware. Typical usage

```php
use Neomerx\Cors\Analyzer;
use Psr\Http\Message\RequestInterface;
use Neomerx\Cors\Contracts\AnalysisResultInterface;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param RequestInterface $request
     * @param Closure          $next
     *
     * @return mixed
     */
    public function handle(RequestInterface $request, Closure $next)
    {
        $cors = Analyzer::instance($this->getCorsSettings())->analyze($request);
        
        switch ($cors->getRequestType()) {
            case AnalysisResultInterface::ERR_NO_HOST_HEADER:
            case AnalysisResultInterface::ERR_ORIGIN_NOT_ALLOWED:
            case AnalysisResultInterface::ERR_METHOD_NOT_SUPPORTED:
            case AnalysisResultInterface::ERR_HEADERS_NOT_SUPPORTED:
                // return 4XX HTTP error
                return ...;

            case AnalysisResultInterface::TYPE_PRE_FLIGHT_REQUEST:
                $corsHeaders = $cors->getResponseHeaders();
                // return 200 HTTP with $corsHeaders
                return ...;

            case AnalysisResultInterface::TYPE_REQUEST_OUT_OF_CORS_SCOPE:
                // call next middleware handler
                return $next($request);
            
            default:
                // actual CORS request
                $response    = $next($request);
                $corsHeaders = $cors->getResponseHeaders();
                
                // add CORS headers to Response $response
                ...
                return $response;
        }
    }
}
```

### Settings

Analyzer accepts settings in `Analyzer::instance($settings)` which must implement `AnalysisStrategyInterface`. You can use default implementation `\Neomerx\Cors\Strategies\Settings` to set the analyzer up.

For example,

```php
use Neomerx\Cors\Strategies\Settings;

$settings = (new Settings())
    ->setServerOrigin('https', 'api.example.com', 443)
    ->setPreFlightCacheMaxAge(0)
    ->setCredentialsSupported()
    ->setAllowedOrigins(['https://www.example.com', ...]) // or enableAllOriginsAllowed()
    ->setAllowedMethods(['GET', 'POST', 'DELETE', ...])   // or enableAllMethodsAllowed()
    ->setAllowedHeaders(['X-Custom-Header', ...])         // or enableAllHeadersAllowed()
    ->setExposedHeaders(['X-Custom-Header', ...])
    ->disableAddAllowedMethodsToPreFlightResponse()
    ->disableAddAllowedHeadersToPreFlightResponse()
    ->enableCheckHost();

$cors = Analyzer::instance($settings)->analyze($request);
```

Settings could be cached which improves performance. If you already have settings configured as in the example above you can get internal settings state as

```php
/** @var array $dataToCache */
$dataToCache = $settings->getData();
```

Cached state should be used as

```php
$settings = (new Settings())->setData($dataFromCache);
$cors     = Analyzer::instance($settings)->analyze($request);
```

## Install

```
composer require neomerx/cors-psr7
```

## Debug Mode

Debug logging will provide a detailed step-by-step description of how requests are handled. In order to activate it a [PSR-3 compatible Logger](http://www.php-fig.org/psr/psr-3/) should be set to `Analyzer`.

```php
/** @var \Psr\Log\LoggerInterface $logger */
$logger   = ...;

$analyzer = Analyzer::instance($settings);
$analyzer->setLogger($logger)
$cors     = $analyzer->analyze($request);
```

## Advanced Usage

There are many possible strategies for handling cross and same origin requests which might and might not depend on data from requests.

This built-in strategy `Settings` implements simple settings identical for all requests (same list of allowed origins, same allowed methods for all requests and etc).

However you can customize such behaviour. For example you can send different sets of allowed methods depending on request. This might be helpful when you have some kind of Access Control System and wish to differentiate response based on request (for example on its origin). You can either implement `AnalysisStrategyInterface` from scratch or override methods in `Settings` class if only a minor changes are needed to `Settings`. The new strategy could be sent to `Analyzer` constructor or `Analyzer::instance` method could be used for injection.

Example

```php
class CustomMethodsSettings extends Settings
{
    public function getRequestAllowedMethods(RequestInterface $request): string
    {
        // An external Access Control System could be used to determine
        // which methods are allowed for this request.
        
        return ...;
    }
}

$cors = Analyzer::instance(new CustomMethodsSettings())->analyze($request);
```

## Testing

```
composer test
```

## Questions?

Do not hesitate to check [issues](https://github.com/neomerx/cors-psr7/issues) or post a new one.

## Contributing

If you have spotted any compliance issues with the [CORS Recommendation](http://www.w3.org/TR/cors/) please post an [issue](https://github.com/neomerx/cors-psr7/issues). Pull requests for documentation and code improvements (PSR-2, tests) are welcome.

## Versioning

This package is using [Semantic Versioning](http://semver.org/).

## License

Apache License (Version 2.0). Please see [License File](LICENSE) for more information.
