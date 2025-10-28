<?php
declare(strict_types = 1);

namespace Middlewares\Utils;

use Exception;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use UnexpectedValueException;

/**
 * Simple class to execute callables as middlewares or request handlers.
 */
class CallableHandler implements MiddlewareInterface, RequestHandlerInterface
{
    /** @var callable */
    private $callable;

    /** @var ResponseFactoryInterface|null */
    private $responseFactory;

    public function __construct(callable $callable, ?ResponseFactoryInterface $responseFactory = null)
    {
        $this->callable = $callable;
        $this->responseFactory = $responseFactory;
    }

    /**
     * Process a server request and return a response.
     *
     * @see RequestHandlerInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->execute([$request]);
    }

    /**
     * Process a server request and return a response.
     *
     * @see MiddlewareInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->execute([$request, $handler]);
    }

    /**
     * Magic method to invoke the callable directly
     */
    public function __invoke(): ResponseInterface
    {
        return $this->execute(func_get_args());
    }

    /**
     * Execute the callable.
     */
    private function execute(array $arguments = []): ResponseInterface
    {
        ob_start();
        $level = ob_get_level();

        try {
            $return = call_user_func_array($this->callable, $arguments);

            if ($return instanceof ResponseInterface) {
                $response = $return;
                $return = '';
            } elseif (is_null($return)
                 || is_scalar($return)
                 || (is_object($return) && method_exists($return, '__toString'))
            ) {
                $responseFactory = $this->responseFactory ?: Factory::getResponseFactory();
                $response = $responseFactory->createResponse();
            } else {
                throw new UnexpectedValueException(
                    'The value returned must be scalar or an object with __toString method'
                );
            }

            while (ob_get_level() >= $level) {
                $return = ob_get_clean().$return;
            }

            $return = (string) $return;
            $body = $response->getBody();

            if ($return !== '' && $body->isWritable()) {
                $body->write($return);
            }

            return $response;
        } catch (Exception $exception) {
            while (ob_get_level() >= $level) {
                ob_end_clean();
            }

            throw $exception;
        }
    }
}
