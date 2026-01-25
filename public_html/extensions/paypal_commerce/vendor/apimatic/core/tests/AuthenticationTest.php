<?php

namespace Core\Tests;

use Core\Authentication\Auth;
use Core\Exceptions\AuthValidationException;
use Core\Request\Request;
use Core\Tests\Mocking\Authentication\HeaderAuthManager;
use Core\Tests\Mocking\MockHelper;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class AuthenticationTest extends TestCase
{
    public function testHeaderAuthWithoutGroupAndValidation()
    {
        $request = new Request('http://localhost:3000');
        $auth = new HeaderAuthManager(null, "accessToken");
        $auth->apply($request);

        $this->assertEquals([], $request->getHeaders());
    }

    public function testHeaderAuth()
    {
        $request = new Request('http://localhost:3000');
        $auth = Auth::or('header');
        MockHelper::getClient()->validateAuth($auth)->apply($request);

        $this->assertEquals([
            'token' => 'someAuthToken',
            'authorization' => 'accessToken'
        ], $request->getHeaders());
    }

    public function testHeaderAuthWithMissingField()
    {
        $this->expectException(AuthValidationException::class);
        $this->expectExceptionMessage("Following authentication credentials are required:" .
            "\n-> Missing required header field: authorization");

        $auth = Auth::or('headerWithNull');
        MockHelper::getClient()->validateAuth($auth);
    }

    public function testHeaderAuthWithEmptyField()
    {
        $this->expectException(AuthValidationException::class);
        $this->expectExceptionMessage("Following authentication credentials are required:" .
            "\n-> Missing required header field: token");

        $auth = Auth::or('headerWithEmpty');
        MockHelper::getClient()->validateAuth($auth);
    }

    public function testHeaderOrQueryAuth1()
    {
        $request = new Request('http://localhost:3000');
        $auth = Auth::or('header', 'query');
        MockHelper::getClient()->validateAuth($auth)->apply($request);

        $this->assertEquals([
            'token' => 'someAuthToken',
            'authorization' => 'accessToken'
        ], $request->getHeaders());
        $this->assertEquals(
            'http://localhost:3000',
            $request->getQueryUrl()
        );
    }

    public function testHeaderOrQueryAuth2()
    {
        $request = new Request('http://localhost:3000');
        $auth = Auth::or('query', 'header');
        MockHelper::getClient()->validateAuth($auth)->apply($request);

        $this->assertEquals([], $request->getHeaders());
        $this->assertEquals(
            'http://localhost:3000?token=someAuthToken&authorization=accessToken',
            $request->getQueryUrl()
        );
    }

    public function testHeaderWithMissingFieldOrQueryAuth()
    {
        $request = new Request('http://localhost:3000');
        $auth = Auth::or('headerWithNull', 'query');
        MockHelper::getClient()->validateAuth($auth)->apply($request);

        $this->assertEquals([], $request->getHeaders());
        $this->assertEquals(
            'http://localhost:3000?token=someAuthToken&authorization=accessToken',
            $request->getQueryUrl()
        );
    }

    public function testHeaderWithEmptyFieldOrQueryAuth()
    {
        $request = new Request('http://localhost:3000');
        $auth = Auth::or('headerWithEmpty', 'query');
        MockHelper::getClient()->validateAuth($auth)->apply($request);

        $this->assertEquals([], $request->getHeaders());
        $this->assertEquals(
            'http://localhost:3000?token=someAuthToken&authorization=accessToken',
            $request->getQueryUrl()
        );
    }

    public function testHeaderOrQueryAuthWithMissingFields()
    {
        $this->expectException(AuthValidationException::class);
        $this->expectExceptionMessage("Following authentication credentials are required:" .
            "\n-> Missing required header field: authorization" .
            "\n-> Missing required query field: token");

        $auth = Auth::or('headerWithNull', 'queryWithNull');
        MockHelper::getClient()->validateAuth($auth);
    }

    public function testHeaderAndQueryAuth()
    {
        $request = new Request('http://localhost:3000');
        $auth = Auth::and('header', 'query');
        MockHelper::getClient()->validateAuth($auth)->apply($request);

        $this->assertEquals([
            'token' => 'someAuthToken',
            'authorization' => 'accessToken'
        ], $request->getHeaders());
        $this->assertEquals(
            'http://localhost:3000?token=someAuthToken&authorization=accessToken',
            $request->getQueryUrl()
        );
    }

    public function testHeaderWithMissingFieldAndQueryAuth()
    {
        $this->expectException(AuthValidationException::class);
        $this->expectExceptionMessage("Following authentication credentials are required:" .
            "\n-> Missing required header field: authorization");

        $auth = Auth::and('headerWithNull', 'query');
        MockHelper::getClient()->validateAuth($auth);
    }

    public function testHeaderWithEmptyFieldAndQueryAuth()
    {
        $this->expectException(AuthValidationException::class);
        $this->expectExceptionMessage("Following authentication credentials are required:" .
            "\n-> Missing required header field: token");

        $auth = Auth::and('headerWithEmpty', 'query');
        MockHelper::getClient()->validateAuth($auth);
    }

    public function testHeaderAndQueryAuthWithMissingFields()
    {
        $this->expectException(AuthValidationException::class);
        $this->expectExceptionMessage("Following authentication credentials are required:" .
            "\n-> Missing required header field: authorization");

        $auth = Auth::and('headerWithNull', 'queryWithNull');
        MockHelper::getClient()->validateAuth($auth);
    }

    public function testFormOrHeaderAndQueryAuthWithMissingFields()
    {
        $request = new Request('http://localhost:3000');
        $auth = Auth::or('form', Auth::and('header', 'queryWithNull'));
        MockHelper::getClient()->validateAuth($auth)->apply($request);

        $this->assertEquals([
            'token' => 'someAuthToken',
            'authorization' => 'accessToken'
        ], $request->getParameters());
        $this->assertEquals([], $request->getHeaders());
        $this->assertEquals('http://localhost:3000', $request->getQueryUrl());
    }

    public function testFormOrHeaderOrQueryAuthWithMissingFields1()
    {
        $request = new Request('http://localhost:3000');
        $auth = Auth::or('form', Auth::or('header', 'queryWithNull'));
        MockHelper::getClient()->validateAuth($auth)->apply($request);

        $this->assertEquals([
            'token' => 'someAuthToken',
            'authorization' => 'accessToken'
        ], $request->getParameters());
        $this->assertEquals([], $request->getHeaders());
        $this->assertEquals('http://localhost:3000', $request->getQueryUrl());
    }

    public function testFormOrHeaderOrQueryAuthWithMissingFields2()
    {
        $request = new Request('http://localhost:3000');
        $auth = Auth::or(Auth::or('header', 'queryWithNull'), 'form');
        MockHelper::getClient()->validateAuth($auth)->apply($request);

        $this->assertEquals([
            'token' => 'someAuthToken',
            'authorization' => 'accessToken'
        ], $request->getHeaders());
        $this->assertEquals('http://localhost:3000', $request->getQueryUrl());
    }

    public function testFormAndHeaderWithNullOrHeaderOrQueryWithNull()
    {
        $request = new Request('http://localhost:3000');
        $auth = Auth::or(Auth::and('form', 'headerWithNull'), Auth::or('header', 'queryWithNull'));
        MockHelper::getClient()->validateAuth($auth)->apply($request);

        $this->assertEquals([], $request->getParameters());
        $this->assertEquals([
            'token' => 'someAuthToken',
            'authorization' => 'accessToken'
        ], $request->getHeaders());
        $this->assertEquals('http://localhost:3000', $request->getQueryUrl());
    }

    public function testFormOrHeaderWithNullAndHeaderOrQueryWithNull()
    {
        $request = new Request('http://localhost:3000');
        $auth = Auth::and(Auth::or('form', 'headerWithNull', 'formWithNull'), Auth::or('queryWithNull', 'header'));
        MockHelper::getClient()->validateAuth($auth)->apply($request);

        $this->assertEquals([
            'token' => 'someAuthToken',
            'authorization' => 'accessToken'
        ], $request->getParameters());
        $this->assertEquals([
            'token' => 'someAuthToken',
            'authorization' => 'accessToken'
        ], $request->getHeaders());
        $this->assertEquals('http://localhost:3000', $request->getQueryUrl());
    }

    public function testFormOrHeaderWithNullAndHeaderAndQueryWithNull()
    {
        $this->expectException(AuthValidationException::class);
        $this->expectExceptionMessage("Following authentication credentials are required:" .
            "\n-> Missing required query field: token");

        $auth = Auth::and(Auth::or('form', 'headerWithNull'), Auth::and('header', 'queryWithNull'));
        MockHelper::getClient()->validateAuth($auth);
    }

    public function testInvalidAuthName()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("AuthManager not found with name: \"myAuth\"");

        MockHelper::getClient()->validateAuth(Auth::or('myAuth'));
    }
}
