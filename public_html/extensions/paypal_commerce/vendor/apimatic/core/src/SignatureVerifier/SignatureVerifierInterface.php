<?php

declare(strict_types=1);

namespace Core\SignatureVerifier;

use Symfony\Component\HttpFoundation\Request;
use Core\SignatureVerifier\VerificationFailure;

interface SignatureVerifierInterface
{
    /**
     * Verifies the signature of a request.
     *
     * @param Request $request
     * @return VerificationFailure|true
     */
    public function verify(Request $request);
}
