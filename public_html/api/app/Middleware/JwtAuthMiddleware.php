<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */

namespace app\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Psr\Clock\ClockInterface;

class JwtAuthMiddleware implements MiddlewareInterface
{
    private Configuration $config;
    private array $excludedPaths;
    private $psr17;

    public function __construct($psr17, string $secretKey, array $excludedPaths = [])
    {
        $this->psr17 = $psr17;
        $this->excludedPaths = $excludedPaths;
        
        $this->config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($secretKey)
        );
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $path = $request->getUri()->getPath();
        
        // Skip authentication for excluded paths (e.g., /api/login)
        foreach ($this->excludedPaths as $excludedPath) {
            if (str_starts_with($path, $excludedPath)) {
                return $handler->handle($request);
            }
        }

        // Get token from Authorization header
        $authHeader = $request->getHeaderLine('Authorization');
        
        if (empty($authHeader)) {
            return $this->unauthorizedResponse('Missing authorization header');
        }

        // Check for "Bearer <token>" format
        if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $this->unauthorizedResponse('Invalid authorization header format');
        }

        $tokenString = $matches[1];

        try {
            // Parse and validate token
            $token = $this->config->parser()->parse($tokenString);
            
            $constraints = $this->config->validationConstraints();
            
            if (!$this->config->validator()->validate($token, ...$constraints)) {
                return $this->unauthorizedResponse('Invalid token');
            }

            // Add user data to request attributes
            $request = $request->withAttribute('jwt_token', $token);
            $request = $request->withAttribute('user_id', $token->claims()->get('user_id'));
            $request = $request->withAttribute('username', $token->claims()->get('username'));
            
            return $handler->handle($request);
            
        } catch (\Exception $e) {
            return $this->unauthorizedResponse('Token validation failed: ' . $e->getMessage());
        }
    }

    private function unauthorizedResponse(string $message): ResponseInterface
    {
        $response = $this->psr17->createResponse(401);
        $response->getBody()->write(json_encode([
            'error' => [
                'code' => 'unauthorized',
                'message' => $message
            ]
        ], JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
