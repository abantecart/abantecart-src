<?php

namespace Core\Tests\Mocking\Logger;

use PHPUnit\Framework\Assert;

class LogEntry
{
    public $level;
    public $message;
    public $context;

    public function __construct(string $level, string $message, array $context)
    {
        $this->level = $level;
        $this->message = $message;
        $this->context = $context;
    }

    public function checkEquals(LogEntry $other): void
    {
        Assert::assertEquals(
            [
                $this->level,
                $this->message,
                $this->context
            ],
            [
                $other->level,
                $other->message,
                $other->context
            ],
            'LogEntry did not match'
        );
    }
}
