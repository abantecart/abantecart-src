<?php

declare(strict_types=1);

namespace Core\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Core\SignatureVerifier\HmacSignatureVerifier;
use Core\Tests\Mocking\SignatureVerifier\MockVerificationFailure;

class SignatureVerifierTest extends TestCase
{
    private const SECRET_KEY = 'test_secret';
    private const SIGNATURE_HEADER = 'X-Signature';
    private const HMAC_ALGORITHM = 'sha256';
    private const ENCODING = 'hex';
    private const SIGNATURE_VALUE_TEMPLATE = 'sha256={digest}';

    private function createTemplateResolver(): callable
    {
        return function (Request $request): string {
            $method = $request->getMethod();
            $body = $request->getContent();
            $cookieHeader = $request->headers->get('Cookie', '');
            $xTimestampHeader = $request->headers->get('X-Timestamp', '');

            // Parse JSON body to get customer name
            $customerName = '';
            if (!empty($body)) {
                $data = json_decode($body, true);
                if (isset($data['customer']['name'])) {
                    $customerName = $data['customer']['name'];
                }
            }

            return "{$cookieHeader}:{$xTimestampHeader}:{$method}:{$body}:{$customerName}";
        };
    }

    private function createBaseRequest(array $headers = [], ?string $body = '{"foo":"bar"}'): Request
    {
        $defaultHeaders = [
            'Content-Type' => 'application/json',
            'X-Timestamp' => '1697051234',
            'X-Signature' => '',
        ];

        $mergedHeaders = array_merge($defaultHeaders, $headers);

        $request = Request::create(
            'https://api.example.com/resource',
            'POST',
            [],
            [],
            [],
            [],
            $body
        );

        foreach ($mergedHeaders as $key => $value) {
            $request->headers->set($key, $value);
        }

        return $request;
    }

    public function testShouldThrowErrorForEmptySecretKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('secretKey must be a non-empty string');
        $verifier = new HmacSignatureVerifier(
            '',
            [MockVerificationFailure::class, 'init'],
            self::SIGNATURE_HEADER,
            $this->createTemplateResolver()
        );
    }

    public function testShouldThrowErrorForEmptySignatureHeader(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('signatureHeader must be a non-empty string');
        $verifier = new HmacSignatureVerifier(
            self::SECRET_KEY,
            [MockVerificationFailure::class, 'init'],
            '',
            $this->createTemplateResolver()
        );
    }

    public function testShouldThrowErrorForInvalidAlgorithm(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('algorithm must be one of');
        $verifier = new HmacSignatureVerifier(
            self::SECRET_KEY,
            [MockVerificationFailure::class, 'init'],
            self::SIGNATURE_HEADER,
            $this->createTemplateResolver(),
            'invalid'
        );
    }

    public function testShouldThrowErrorForInvalidEncoding(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('encoding must be one of');
        $verifier = new HmacSignatureVerifier(
            self::SECRET_KEY,
            [MockVerificationFailure::class, 'init'],
            self::SIGNATURE_HEADER,
            $this->createTemplateResolver(),
            self::HMAC_ALGORITHM,
            'invalid'
        );
    }

    public function testShouldFailVerificationIfSignatureHeaderIsMissing(): void
    {
        $verifier = new HmacSignatureVerifier(
            self::SECRET_KEY,
            [MockVerificationFailure::class, 'init'],
            self::SIGNATURE_HEADER,
            $this->createTemplateResolver()
        );

        $request = $this->createBaseRequest(['X-Signature' => null]);
        $request->headers->remove('X-Signature');

        $result = $verifier->verify($request);

        $this->assertInstanceOf(MockVerificationFailure::class, $result);
        $this->assertEquals('Missing signature header', $result->getErrorMessage());
    }

    public function testShouldFailVerificationIfSignatureDoesNotMatch(): void
    {
        $verifier = new HmacSignatureVerifier(
            self::SECRET_KEY,
            [MockVerificationFailure::class, 'init'],
            self::SIGNATURE_HEADER,
            $this->createTemplateResolver()
        );

        $request = $this->createBaseRequest(['X-Signature' => 'sha256=invalidsignature']);

        $result = $verifier->verify($request);

        $this->assertInstanceOf(MockVerificationFailure::class, $result);
        $this->assertEquals('Signature mismatch', $result->getErrorMessage());
    }

    public function testShouldPassVerificationForValidSignature(): void
    {
        $verifier = new HmacSignatureVerifier(
            self::SECRET_KEY,
            [MockVerificationFailure::class, 'init'],
            self::SIGNATURE_HEADER,
            $this->createTemplateResolver(),
            self::HMAC_ALGORITHM,
            self::ENCODING,
            self::SIGNATURE_VALUE_TEMPLATE
        );

        $request = $this->createBaseRequest();
        $templateResolver = $this->createTemplateResolver();
        $signingData = $templateResolver($request);

        $hash = hash_hmac(self::HMAC_ALGORITHM, $signingData, self::SECRET_KEY, true);
        $digest = bin2hex($hash);
        $signature = str_replace('{digest}', $digest, self::SIGNATURE_VALUE_TEMPLATE);

        $request->headers->set('X-Signature', $signature);

        $result = $verifier->verify($request);

        $this->assertTrue($result);
    }

    public function testShouldPassVerificationForValidSignatureWithoutSignatureValueTemplate(): void
    {
        $verifier = new HmacSignatureVerifier(
            self::SECRET_KEY,
            [MockVerificationFailure::class, 'init'],
            self::SIGNATURE_HEADER,
            $this->createTemplateResolver()
        );

        $request = $this->createBaseRequest();
        $templateResolver = $this->createTemplateResolver();
        $signingData = $templateResolver($request);

        $hash = hash_hmac(self::HMAC_ALGORITHM, $signingData, self::SECRET_KEY, true);
        $digest = bin2hex($hash);

        $request->headers->set('X-Signature', $digest);

        $result = $verifier->verify($request);

        $this->assertTrue($result);
    }

    public function testShouldSupportSha512AndBase64Encoding(): void
    {
        $verifier = new HmacSignatureVerifier(
            self::SECRET_KEY,
            [MockVerificationFailure::class, 'init'],
            self::SIGNATURE_HEADER,
            $this->createTemplateResolver(),
            'sha512',
            'base64'
        );

        $request = $this->createBaseRequest();
        $templateResolver = $this->createTemplateResolver();
        $signingData = $templateResolver($request);

        $hash = hash_hmac('sha512', $signingData, self::SECRET_KEY, true);
        $digest = base64_encode($hash);

        $request->headers->set('X-Signature', $digest);

        $result = $verifier->verify($request);

        $this->assertTrue($result);
    }

    public function testShouldSupportHexEncoding(): void
    {
        $verifier = new HmacSignatureVerifier(
            self::SECRET_KEY,
            [MockVerificationFailure::class, 'init'],
            self::SIGNATURE_HEADER,
            $this->createTemplateResolver(),
            'sha512',
            'hex'
        );

        $request = $this->createBaseRequest();
        $templateResolver = $this->createTemplateResolver();
        $signingData = $templateResolver($request);

        $hash = hash_hmac('sha512', $signingData, self::SECRET_KEY, true);
        $digest = bin2hex($hash);

        $request->headers->set('X-Signature', $digest);

        $result = $verifier->verify($request);

        $this->assertTrue($result);
    }

    public function testShouldSupportBase64urlEncoding(): void
    {
        $verifier = new HmacSignatureVerifier(
            self::SECRET_KEY,
            [MockVerificationFailure::class, 'init'],
            self::SIGNATURE_HEADER,
            $this->createTemplateResolver(),
            'sha512',
            'base64url'
        );

        $request = $this->createBaseRequest();
        $templateResolver = $this->createTemplateResolver();
        $signingData = $templateResolver($request);

        $hash = hash_hmac('sha512', $signingData, self::SECRET_KEY, true);
        $base64 = base64_encode($hash);
        $digest = rtrim(strtr($base64, '+/', '-_'), '=');

        $request->headers->set('X-Signature', $digest);

        $result = $verifier->verify($request);

        $this->assertTrue($result);
    }

    public function testShouldUseCustomSignatureValueTemplate(): void
    {
        $customTemplate = 'sig:{digest}:end';

        $verifier = new HmacSignatureVerifier(
            self::SECRET_KEY,
            [MockVerificationFailure::class, 'init'],
            self::SIGNATURE_HEADER,
            $this->createTemplateResolver(),
            self::HMAC_ALGORITHM,
            self::ENCODING,
            $customTemplate
        );

        $request = $this->createBaseRequest();
        $templateResolver = $this->createTemplateResolver();
        $signingData = $templateResolver($request);

        $hash = hash_hmac(self::HMAC_ALGORITHM, $signingData, self::SECRET_KEY, true);
        $digest = bin2hex($hash);
        $signature = str_replace('{digest}', $digest, $customTemplate);

        $request->headers->set('X-Signature', $signature);

        $result = $verifier->verify($request);

        $this->assertTrue($result);
    }

    public function testShouldFailIfDifferentSecretKeyIsGiven(): void
    {
        $verifier = new HmacSignatureVerifier(
            'different-key',
            [MockVerificationFailure::class, 'init'],
            self::SIGNATURE_HEADER,
            $this->createTemplateResolver(),
            self::HMAC_ALGORITHM,
            self::ENCODING,
            self::SIGNATURE_VALUE_TEMPLATE
        );

        $request = $this->createBaseRequest();
        $templateResolver = $this->createTemplateResolver();
        $signingData = $templateResolver($request);

        // Sign with original secret key
        $hash = hash_hmac(self::HMAC_ALGORITHM, $signingData, self::SECRET_KEY, true);
        $digest = bin2hex($hash);
        $signature = str_replace('{digest}', $digest, self::SIGNATURE_VALUE_TEMPLATE);

        $request->headers->set('X-Signature', $signature);

        $result = $verifier->verify($request);

        $this->assertInstanceOf(MockVerificationFailure::class, $result);
        $this->assertEquals('Signature mismatch', $result->getErrorMessage());
    }

    public function testShouldUseBodyToSignIfTemplateResolverIsNotProvided(): void
    {
        $verifier = new HmacSignatureVerifier(
            'different-key',
            [MockVerificationFailure::class, 'init'],
            self::SIGNATURE_HEADER,
            null,
            self::HMAC_ALGORITHM,
            self::ENCODING,
            self::SIGNATURE_VALUE_TEMPLATE
        );

        $request = $this->createBaseRequest();
        $body = $request->getContent();

        // Sign with original secret key (different from verifier's key)
        $hash = hash_hmac(self::HMAC_ALGORITHM, $body, self::SECRET_KEY, true);
        $digest = bin2hex($hash);
        $signature = str_replace('{digest}', $digest, self::SIGNATURE_VALUE_TEMPLATE);

        $request->headers->set('X-Signature', $signature);

        $result = $verifier->verify($request);

        $this->assertInstanceOf(MockVerificationFailure::class, $result);
        $this->assertEquals('Signature mismatch', $result->getErrorMessage());
    }

    public function testShouldHandleVerificationWhenBothTemplateResolverAndBodyAreUndefined(): void
    {
        $verifier = new HmacSignatureVerifier(
            self::SECRET_KEY,
            [MockVerificationFailure::class, 'init'],
            self::SIGNATURE_HEADER,
            null,
            self::HMAC_ALGORITHM,
            self::ENCODING,
            self::SIGNATURE_VALUE_TEMPLATE
        );

        $request = $this->createBaseRequest([], '');

        $signingData = '';
        $hash = hash_hmac(self::HMAC_ALGORITHM, $signingData, self::SECRET_KEY, true);
        $digest = bin2hex($hash);
        $signature = str_replace('{digest}', $digest, self::SIGNATURE_VALUE_TEMPLATE);

        $request->headers->set('X-Signature', $signature);

        $result = $verifier->verify($request);

        $this->assertTrue($result);
    }

    public function testShouldFallbackToBodyIfTemplateResolverReturnsNull(): void
    {
        $templateResolver = function (Request $request): ?string {
            return null;
        };
        $verifier = new HmacSignatureVerifier(
            self::SECRET_KEY,
            [MockVerificationFailure::class, 'init'],
            self::SIGNATURE_HEADER,
            $templateResolver,
            self::HMAC_ALGORITHM,
            self::ENCODING,
            self::SIGNATURE_VALUE_TEMPLATE
        );
        $request = $this->createBaseRequest();
        $body = $request->getContent();
        $hash = hash_hmac(self::HMAC_ALGORITHM, $body, self::SECRET_KEY, true);
        $digest = bin2hex($hash);
        $signature = str_replace('{digest}', $digest, self::SIGNATURE_VALUE_TEMPLATE);
        $request->headers->set('X-Signature', $signature);
        $result = $verifier->verify($request);
        $this->assertTrue($result);
    }

    public function testShouldFallbackToBodyIfTemplateResolverReturnsEmptyString(): void
    {
        $templateResolver = function (Request $request): string {
            return '';
        };
        $verifier = new HmacSignatureVerifier(
            self::SECRET_KEY,
            [MockVerificationFailure::class, 'init'],
            self::SIGNATURE_HEADER,
            $templateResolver,
            self::HMAC_ALGORITHM,
            self::ENCODING,
            self::SIGNATURE_VALUE_TEMPLATE
        );
        $request = $this->createBaseRequest();
        $body = $request->getContent();
        $hash = hash_hmac(self::HMAC_ALGORITHM, $body, self::SECRET_KEY, true);
        $digest = bin2hex($hash);
        $signature = str_replace('{digest}', $digest, self::SIGNATURE_VALUE_TEMPLATE);
        $request->headers->set('X-Signature', $signature);
        $result = $verifier->verify($request);
        $this->assertTrue($result);
    }
}
