<?php

declare(strict_types=1);

namespace Core\Tests\Mocking\SignatureVerifier;

use Core\SignatureVerifier\VerificationFailure;

class MockVerificationFailure extends VerificationFailure
{
    public static function init(string $errorMessage): VerificationFailure
    {
        return new MockVerificationFailure($errorMessage);
    }
}
