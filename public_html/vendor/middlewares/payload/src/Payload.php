<?php
declare(strict_types = 1);

namespace Middlewares;

use Exception;
use Middlewares\Utils\HttpErrorException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class Payload
{
    /**
     * @var string[]
     */
    protected $contentType;

    /**
     * @var bool
     */
    protected $override = false;

    /**
     * @var string[]
     */
    protected $methods = ['POST', 'PUT', 'PATCH', 'DELETE', 'COPY', 'LOCK', 'UNLOCK'];

    /**
     * Configure the Content-Type.
     *
     * @param string[] $contentType
     */
    public function contentType(array $contentType): self
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * Configure the methods allowed.
     *
     * @param string[] $methods
     */
    public function methods(array $methods): self
    {
        $this->methods = $methods;

        return $this;
    }

    /**
     * Configure if the parsed body overrides the previous value.
     */
    public function override(bool $override = true): self
    {
        $this->override = $override;

        return $this;
    }

    /**
     * Process a server request and return a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->checkRequest($request)) {
            try {
                $request = $request->withParsedBody($this->parse($request->getBody()));
            } catch (Exception $exception) {
                throw HttpErrorException::create(400, [], $exception);
            }
        }

        return $handler->handle($request);
    }

    /**
     * Parse the body.
     *
     * @return mixed
     */
    abstract protected function parse(StreamInterface $stream);

    /**
     * Check whether the request payload need to be processed
     */
    private function checkRequest(ServerRequestInterface $request): bool
    {
        if ($request->getParsedBody() && !$this->override) {
            return false;
        }

        if (!in_array($request->getMethod(), $this->methods, true)) {
            return false;
        }

        $contentType = $request->getHeaderLine('Content-Type');

        foreach ($this->contentType as $allowedType) {
            if (stripos($contentType, $allowedType) === 0) {
                return true;
            }
        }

        return false;
    }
}
