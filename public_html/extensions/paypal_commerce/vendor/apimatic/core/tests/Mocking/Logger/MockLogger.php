<?php

namespace Core\Tests\Mocking\Logger;

use PHPUnit\Framework\Assert;
use Psr\Log\AbstractLogger;

class MockLogger extends AbstractLogger
{
    /**
     * @var LogEntry[]
     */
    private $logEntries = [];

    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context = []): void
    {
        $this->logEntries[] = new LogEntry($level, $message, $context);
    }

    /**
     * Returns the count of entries logged via this logger.
     */
    public function countEntries(): int
    {
        return count($this->logEntries);
    }

    /**
     * Assert if the given log entries are same as the last added log entries.
     */
    public function assertLastEntries(LogEntry ...$logEntries): void
    {
        Assert::assertGreaterThanOrEqual(
            count($logEntries),
            count($this->logEntries),
            'Number of expected log entries are greater then actual log entries'
        );
        $reversedActual = array_reverse($this->logEntries);

        foreach (array_reverse($logEntries) as $index => $entry) {
            $entry->checkEquals($reversedActual[$index]);
        }
    }
}
