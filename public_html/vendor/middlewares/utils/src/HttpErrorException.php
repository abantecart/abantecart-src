<?php
declare(strict_types = 1);

namespace Middlewares\Utils;

use Exception;
use RuntimeException;
use Throwable;

class HttpErrorException extends Exception
{
    /** @var array<int, string> */
    private static $phrases = [
        // CLIENT ERROR
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        444 => 'Connection Closed Without Response',
        451 => 'Unavailable For Legal Reasons',
        // SERVER ERROR
        499 => 'Client Closed Request',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
        599 => 'Network Connect Timeout Error',
    ];

    /** @var array */
    private $context = [];

    /**
     * Create and returns a new instance
     *
     * @param int $code A valid http error code
     */
    public static function create(int $code = 500, array $context = [], ?Throwable $previous = null): self
    {
        if (!isset(self::$phrases[$code])) {
            throw new RuntimeException("Http error not valid ({$code})");
        }

        $exception = new static(self::$phrases[$code], $code, $previous);
        $exception->context = $context;

        return $exception;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
