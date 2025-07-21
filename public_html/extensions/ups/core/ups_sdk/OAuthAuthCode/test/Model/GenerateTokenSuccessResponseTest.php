<?php
/**
 * GenerateTokenSuccessResponseTest
 *
 * PHP version 5
 *
 * @category Class
 * @package  UPS\OAuthAuthCode
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * OAuth Authorization Code API
 *
 * The UPS OAuth Authorization Code API helps integrate UPS services into your business application for providing the service your application grants your customers. For example, you can create UPS shipping labels with shipping rates for merchants from within your application. Since your application will not have access to your customer's UPS login credentials, the OAuth authorization code flow is used to let your customer use their UPS credentials, within your application, in a simple and secure way.  The PKCE-enhanced Authorization Code Flow introduces a secret created by the calling application that can be verified by the authorization server; this secret is called the Code Verifier. Additionally, the calling app creates a transform value of the Code Verifier called the Code Challenge  and sends this value over HTTPS to retrieve an Authorization Code. This way, a malicious attacker can only intercept the Authorization Code, and they cannot exchange it for a token without the Code Verifier.  Key Business Values:   - **Enhanced Transaction Security**: The OAuth Authorization Code flow is more secure and reliable since the access token and the refresh token are never exposed in the browser's URL, thus reducing the risk of leakage or theft.   - **Operational Efficiency**: With the ability to obtain a refresh token when the token expires, your application can maintain a long-term and uninterrupted access to the protected resources, without requiring the user to re-authenticate or re-login.  Overview of steps in OAuth Authorization Code flow:  1. When user selects Login, the client application redirects to the authorization server's /authorize endpoint. 2. The Authorization Server authenticates the user by asking for their login credentials, and after successful login, the authorization server responds back to the application with an authorization code contained within a redirection URI. 3. The application then sends the authorization code and the redirection URI to the authorization server's /oauth/token endpoint. 4. The authorization server's /token endpoint verifies the authorization code and the application's client ID contained in the redirect URI, and responds with a with an access token, as well as a refresh token. 5. The Client application uses the access token to request information from an UPS API.  Overview of steps in OAuth Authorization Code PKCE flow: 1. When user selects Login, the client application redirects to the authorization server's /authorize endpoint with Code Challenge   - **Note:** Prior to redirecting to the authorization server, the application generates and **code_challenge** and **code_verifier** that are related in this way: code_challenge = BASE64URL-ENCODE(SHA256(ASCII(code_verifier))) 2. The Authorization Server authenticates the user by asking for their login credentials, and after successful login, the authorization server responds back to the application with an authorization code contained within a redirection URI. 4. The application then sends the authorization code , code_verifer and the redirection URI to the authorization server's /oauth/token endpoint.   - **Note:** When utlizing the PKCE flow, the BASIC Authorization header should **not** be included, just the **client_id** parameter in the body. 5. The authorization server's /token endpoint verifies the authorization code, code_verifier and the application's client ID contained in the redirect URI, and responds with a with an access token, as well as a refresh token. 6. The Client application uses the access token to request information from an UPS API.  - <a href=\"https://developer.ups.com/api/reference/oauth/authorization-code\" target=\"_blank\" rel=\"noopener\">Setting-up OAuth Authorization Code flow</a> - <a href=\"https://github.com/UPS-API/UPS-SDKs\" target=\"_blank\" rel=\"noopener noreferrer\">Accelerate API Integration with UPS SDKs</a></br>  </br><p>Try out UPS APIs with example requests using Postman. Explore API documentation and sample applications through GitHub.</p> <a href=\"https://god.gw.postman.com/run-collection/29542085-f15285f0-2ffd-4895-b9e4-fcf496f8059a?action=collection%2Ffork&source=rip_markdown&collection-url=entityId%3D29542085-f15285f0-2ffd-4895-b9e4-fcf496f8059a%26entityType%3Dcollection%26workspaceId%3D7e7595f0-4829-4f9a-aee1-75c126b9d417\" target=\"_blank\" rel=\"noopener noreferrer\">   <img src=\"https://run.pstmn.io/button.svg\" alt=\"Run In Postman\" style=\"width: 128px; height: 32px;\"></a> <a href=\"https://github.com/UPS-API\" target=\"_blank\" rel=\"noopener noreferrer\">   <img src=\"https://www.ups.com/assets/resources/webcontent/images/gitHubButton.svg\" alt=\"Open in GitHub \" style=\"width: 128px; height: 32px;\"> </a>
 *
 * OpenAPI spec version: 1.0
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 * Swagger Codegen version: 3.0.71
 */
/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Please update the test case below to test the model.
 */

namespace UPS\OAuthAuthCode;

use PHPUnit\Framework\TestCase;

/**
 * GenerateTokenSuccessResponseTest Class Doc Comment
 *
 * @category    Class
 * @description GenerateTokenSuccessResponse
 * @package     UPS\OAuthAuthCode
 * @author      Swagger Codegen team
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class GenerateTokenSuccessResponseTest extends TestCase
{

    /**
     * Setup before running any test case
     */
    public static function setUpBeforeClass(): void
    {
    }

    /**
     * Setup before running each test case
     */
    public function setUp(): void
    {
    }

    /**
     * Clean up after running each test case
     */
    public function tearDown(): void
    {
    }

    /**
     * Clean up after running all test cases
     */
    public static function tearDownAfterClass(): void
    {
    }

    /**
     * Test "GenerateTokenSuccessResponse"
     */
    public function testGenerateTokenSuccessResponse()
    {
    }

    /**
     * Test attribute "refresh_token_expires_in"
     */
    public function testPropertyRefreshTokenExpiresIn()
    {
    }

    /**
     * Test attribute "refresh_token_status"
     */
    public function testPropertyRefreshTokenStatus()
    {
    }

    /**
     * Test attribute "token_type"
     */
    public function testPropertyTokenType()
    {
    }

    /**
     * Test attribute "issued_at"
     */
    public function testPropertyIssuedAt()
    {
    }

    /**
     * Test attribute "client_id"
     */
    public function testPropertyClientId()
    {
    }

    /**
     * Test attribute "access_token"
     */
    public function testPropertyAccessToken()
    {
    }

    /**
     * Test attribute "refresh_token"
     */
    public function testPropertyRefreshToken()
    {
    }

    /**
     * Test attribute "scope"
     */
    public function testPropertyScope()
    {
    }

    /**
     * Test attribute "refresh_token_issued_at"
     */
    public function testPropertyRefreshTokenIssuedAt()
    {
    }

    /**
     * Test attribute "expires_in"
     */
    public function testPropertyExpiresIn()
    {
    }

    /**
     * Test attribute "refresh_count"
     */
    public function testPropertyRefreshCount()
    {
    }

    /**
     * Test attribute "status"
     */
    public function testPropertyStatus()
    {
    }
}
