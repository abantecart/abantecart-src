<?php
declare(strict_types = 1);

namespace Middlewares\Utils;

use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionClass;
use ReflectionMethod;

/**
 * Resolve a callable using a container.
 */
class RequestHandlerContainer implements ContainerInterface
{
    /** @var array */
    protected $constructorArguments;

    public function __construct(array $constructorArguments = [])
    {
        $this->constructorArguments = $constructorArguments;
    }

    public function has($id): bool
    {
        $id = $this->split($id);

        if (is_string($id)) {
            return function_exists($id) || class_exists($id);
        }

        return class_exists($id[0]) && method_exists($id[0], $id[1]);
    }

    /**
     * @param  string                  $id
     * @return RequestHandlerInterface
     */
    public function get($id)
    {
        try {
            $handler = $this->resolve($id);

            if ($handler instanceof RequestHandlerInterface) {
                return $handler;
            }

            return new CallableHandler($handler);
        } catch (NotFoundExceptionInterface $exception) {
            throw $exception;
        } catch (Exception $exception) {
            $message = sprintf('Error getting the handler %s', $id);
            throw new class($message, 0, $exception) extends Exception implements ContainerExceptionInterface {
            };
        }
    }

    protected function resolve(string $handler)
    {
        $handler = $this->split($handler);

        if (is_string($handler)) {
            return function_exists($handler) ? $handler : $this->createClass($handler);
        }

        list($class, $method) = $handler;

        if ((new ReflectionMethod($class, $method))->isStatic()) {
            return $handler;
        }

        return [$this->createClass($class), $method];
    }

    /**
     * Returns the instance of a class.
     */
    protected function createClass(string $className): object
    {
        if (!class_exists($className)) {
            $message = sprintf('The class %s does not exists', $className);
            throw new class($message) extends Exception implements NotFoundExceptionInterface {
            };
        }

        $reflection = new ReflectionClass($className);

        if ($reflection->hasMethod('__construct')) {
            return $reflection->newInstanceArgs($this->constructorArguments);
        }

        return $reflection->newInstance();
    }

    /**
     * Slit a string to an array
     *
     * @return string|string[]
     */
    protected function split(string $string)
    {
        //ClassName/Service::method
        if (strpos($string, '::') === false) {
            return $string;
        }

        return explode('::', $string, 2);
    }
}
