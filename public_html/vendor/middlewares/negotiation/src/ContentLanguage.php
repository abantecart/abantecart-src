<?php
declare(strict_types = 1);

namespace Middlewares;

use Middlewares\Utils\Factory;
use Negotiation\LanguageNegotiator;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ContentLanguage implements MiddlewareInterface
{
    use NegotiationTrait;

    /**
     * @var array<string> Allowed languages
     */
    private $languages = [];

    /**
     * @var bool Use the path to detect the language
     */
    private $usePath = false;

    /**
     * @var ResponseFactoryInterface|null
     */
    private $responseFactory;

    /**
     * Define de available languages.
     *
     * @param array<string> $languages
     */
    public function __construct(array $languages)
    {
        $this->languages = $languages;
    }

    /**
     * Use the base path to detect the current language.
     */
    public function usePath(bool $usePath = true): self
    {
        $this->usePath = $usePath;

        return $this;
    }

    /**
     * Whether returns a 302 response to the new path.
     * Note: This only works if usePath is true.
     */
    public function redirect(?ResponseFactoryInterface $responseFactory = null): self
    {
        $this->responseFactory = $responseFactory ?: Factory::getResponseFactory();

        return $this;
    }

    /**
     * Process a server request and return a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uri = $request->getUri();
        $language = $this->detectFromPath($uri->getPath());

        if ($language === null) {
            $language = $this->detectFromHeader($request);

            if ($this->responseFactory && $this->usePath) {
                $location = $uri->withPath(str_replace('//', '/', $language.'/'.$uri->getPath()));

                return $this->responseFactory->createResponse(302)
                    ->withHeader('Location', (string) $location);
            }
        }

        $response = $handler->handle($request->withHeader('Accept-Language', (string) $language));

        if (!$response->hasHeader('Content-Language')) {
            return $response->withHeader('Content-Language', (string) $language);
        }

        return $response;
    }

    /**
     * Returns the language from the first part of the path if it's in the allowed languages.
     */
    private function detectFromPath(string $path): ?string
    {
        if (!$this->usePath) {
            return null;
        }

        $dirs = explode('/', ltrim($path, '/'), 2);
        $first = strtolower((string) array_shift($dirs));

        if (!empty($first) && in_array($first, $this->languages, true)) {
            return $first;
        }

        return null;
    }

    /**
     * Returns the format using the Accept-Language header.
     */
    private function detectFromHeader(ServerRequestInterface $request): ?string
    {
        $accept = $request->getHeaderLine('Accept-Language');
        $language = $this->negotiateHeader($accept, new LanguageNegotiator(), $this->languages);

        if (empty($language)) {
            return isset($this->languages[0]) ? $this->languages[0] : null;
        }

        return $language;
    }
}
