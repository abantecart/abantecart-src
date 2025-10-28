<?php
declare(strict_types = 1);

namespace Middlewares\ErrorFormatter;

use Middlewares\Utils\Factory;
use Middlewares\Utils\HttpErrorException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Throwable;

abstract class AbstractFormatter implements FormatterInterface
{
    /** @var ResponseFactoryInterface */
    protected $responseFactory;

    /** @var StreamFactoryInterface */
    protected $streamFactory;

    /** @var string[] */
    protected $contentTypes = [];

    public function __construct(
        ?ResponseFactoryInterface $responseFactory = null,
        ?StreamFactoryInterface $streamFactory = null
    ) {
        $this->responseFactory = $responseFactory ?? Factory::getResponseFactory();
        $this->streamFactory = $streamFactory ?? Factory::getStreamFactory();
    }

    public function isValid(Throwable $error, ServerRequestInterface $request): bool
    {
        return $this->getContentType($request) ? true : false;
    }

    abstract protected function format(Throwable $error, string $contentType): string;

    public function handle(Throwable $error, ServerRequestInterface $request): ResponseInterface
    {
        $contentType = $this->getContentType($request) ?: $this->contentTypes[0];
        $response = $this->responseFactory->createResponse($this->errorStatus($error));
        $body = $this->streamFactory->createStream($this->format($error, $contentType));

        return $response->withBody($body)->withHeader('Content-Type', $contentType);
    }

    protected function errorStatus(Throwable $error): int
    {
        if ($error instanceof HttpErrorException) {
            return $error->getCode();
        }

        if (method_exists($error, 'getStatusCode')) {
            return $error->getStatusCode();
        }

        return 500;
    }

    protected function getContentType(ServerRequestInterface $request): ?string
    {
        $accept = $request->getHeaderLine('Accept');

        foreach ($this->contentTypes as $type) {
            if (stripos($accept, $type) !== false) {
                return $type;
            }
        }

        return null;
    }
}
