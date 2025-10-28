<?php
declare(strict_types = 1);

namespace Middlewares;

use Exception;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\MiddlewareInterface;

class JsonPayload extends Payload implements MiddlewareInterface
{
    /**
     * @var string[]
     */
    protected $contentType = ['application/json'];

    /**
     * @var bool
     */
    private $associative = true;

    /**
     * @var int
     */
    private $depth = 512;

    /**
     * @var int
     */
    private $options = 0;

    /**
     * Configure the returned object to be converted into an array instead of an object.
     *
     * @see http://php.net/manual/en/function.json-decode.php
     */
    public function associative(bool $associative = true): self
    {
        $this->associative = $associative;

        return $this;
    }

    /**
     * Configure the recursion depth.
     *
     * @see http://php.net/manual/en/function.json-decode.php
     */
    public function depth(int $depth): self
    {
        $this->depth = $depth;

        return $this;
    }

    /**
     * Configure the decode options.
     *
     * @see http://php.net/manual/en/function.json-decode.php
     */
    public function options(int $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return array<mixed>|object|null
     */
    protected function parse(StreamInterface $stream)
    {
        $json = trim((string) $stream);

        if ($json === '') {
            return $this->associative ? [] : null;
        }

        $data = defined('JSON_THROW_ON_ERROR') ? $this->parseWithException($json) : $this->parseWithoutException($json);

        if ($this->associative) {
            return $data ?? [];
        }

        return $data;
    }

    /**

     * @return mixed
     */
    protected function parseWithException(string $json)
    {
        /* @phpstan-ignore-next-line */
        return json_decode($json, $this->associative, $this->depth, $this->options | JSON_THROW_ON_ERROR);
    }

    /**
     * @return mixed
     */
    protected function parseWithoutException(string $json)
    {
        /** @phpstan-ignore-next-line */
        $data = json_decode($json, $this->associative, $this->depth, $this->options);
        $code = json_last_error();

        if ($code !== JSON_ERROR_NONE) {
            throw new Exception(sprintf('JSON: %s', json_last_error_msg()), $code);
        }

        return $data;
    }
}
