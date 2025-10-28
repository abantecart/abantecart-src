<?php
declare(strict_types = 1);

namespace Middlewares;

use InvalidArgumentException;
use Middlewares\Utils\Factory;
use Negotiation\CharsetNegotiator;
use Negotiation\Negotiator;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ContentType implements MiddlewareInterface
{
    use NegotiationTrait;

    /**
     * @var array<string,array<string,string[]|bool>> Available formats with the mime types
     */
    private $formats;

    /**
     * @var string Attribute name to store the format
     */
    private $attribute;

    /**
     * @var string
     */
    private $defaultFormat;

    /**
     * @var array<string> Available charsets
     */
    private $charsets = ['UTF-8'];

    /**
     * @var bool Include X-Content-Type-Options: nosniff
     */
    private $nosniff = true;

    /**
     * @var ResponseFactoryInterface|null
     */
    private $responseFactory;

    /**
     * Return the default formats.
     *
     * @return array<string,array<string,string[]|bool>>
     */
    public static function getDefaultFormats(): array
    {
        return require __DIR__.'/formats_defaults.php';
    }

    /**
     * Return the default formats.
     *
     * @param  array<array<array|mixed>|string>|null     $formats
     * @return array<string,array<string,string[]|bool>>
     */
    private static function getFormats(?array $formats = null): array
    {
        $defaults = static::getDefaultFormats();

        if (empty($formats)) {
            return $defaults;
        }

        $results = [];

        foreach ($formats as $name => $config) {
            if (is_array($config)) {
                $results[$name] = $config;
                continue;
            }

            if (!isset($defaults[$config])) {
                throw new InvalidArgumentException(
                    sprintf('Invalid format name %s', $config)
                );
            }

            $results[$config] = $defaults[$config];
        }

        return $results;
    }

    /**
     * Define de available formats.
     *
     * @param string[] $formats
     */
    public function __construct(?array $formats = null)
    {
        $this->formats = self::getFormats($formats);
        $this->defaultFormat = (string) key($this->formats);
    }

    /**
     * Return an error response (406) if not format has been found
     */
    public function errorResponse(?ResponseFactoryInterface $responseFactory = null): self
    {
        $this->responseFactory = $responseFactory ?: Factory::getResponseFactory();

        return $this;
    }

    /**
     * Save the format name in a server request attribute.
     */
    public function attribute(string $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * Set the available charsets. The first value will be used as default
     *
     * @param array<string> $charsets
     */
    public function charsets(array $charsets): self
    {
        $this->charsets = $charsets;

        return $this;
    }

    /**
     * Configure the nosniff option.
     */
    public function nosniff(bool $nosniff = true): self
    {
        $this->nosniff = $nosniff;

        return $this;
    }

    /**
     * Process a server request and return a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $format = $this->detectFromExtension($request) ?: $this->detectFromHeader($request);

        if ($format === null) {
            if ($this->responseFactory) {
                return $this->responseFactory->createResponse(406);
            }

            $format = $this->defaultFormat;
        }

        /** @phpstan-ignore-next-line */
        $contentType = $this->formats[$format]['mime-type'][0];
        $charset = $this->detectCharset($request) ?: current($this->charsets);

        $request = $request
            ->withHeader('Accept', $contentType)
            ->withHeader('Accept-Charset', (string) $charset);

        if ($this->attribute) {
            $request = $request->withAttribute($this->attribute, $format);
        }

        $response = $handler->handle($request);

        if (!$response->hasHeader('Content-Type')) {
            $needCharset = !empty($this->formats[$format]['charset']);

            if ($needCharset) {
                $contentType .= '; charset='.$charset;
            }

            $response = $response->withHeader('Content-Type', $contentType);
        }

        if ($this->nosniff && !$response->hasHeader('X-Content-Type-Options')) {
            $response = $response->withHeader('X-Content-Type-Options', 'nosniff');
        }

        return $response;
    }

    /**
     * Returns the format using the file extension.
     */
    private function detectFromExtension(ServerRequestInterface $request): ?string
    {
        $extension = strtolower(pathinfo($request->getUri()->getPath(), PATHINFO_EXTENSION));

        if (empty($extension)) {
            return null;
        }

        foreach ($this->formats as $format => $data) {
            /** @var string[] $formatExtension */
            $formatExtension = $data['extension'];

            if (in_array($extension, $formatExtension, true)) {
                return $format;
            }
        }

        return null;
    }

    /**
     * Returns the format using the Accept header.
     */
    private function detectFromHeader(ServerRequestInterface $request): ?string
    {
        if (!$request->hasHeader('Accept')) {
            return $this->defaultFormat;
        }
        $headers = call_user_func_array('array_merge', array_column($this->formats, 'mime-type'));
        $accept = $request->getHeaderLine('Accept');
        $mime = $this->negotiateHeader($accept, new Negotiator(), $headers);

        if ($mime !== null) {
            foreach ($this->formats as $format => $data) {
                /** @var string[] $formtMimeType */
                $formtMimeType = $data['mime-type'];

                if (in_array($mime, $formtMimeType, true)) {
                    return $format;
                }
            }
        }

        return null;
    }

    /**
     * Returns the charset accepted.
     */
    private function detectCharset(ServerRequestInterface $request): ?string
    {
        $accept = $request->getHeaderLine('Accept-Charset');

        return $this->negotiateHeader($accept, new CharsetNegotiator(), $this->charsets);
    }
}
