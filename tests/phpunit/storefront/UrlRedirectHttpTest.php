<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2026 Belavier Commerce LLC
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

namespace storefront;

use ACache;
use ADB;
use PHPUnit\Framework\Attributes\DataProvider;
use phpunit\AbanteCartTestBootstrap;
use Registry;

/**
 * Redirect rules (table url_redirects) checked by real http requests to the running storefront.
 * Covers the whole chain: ARequest escaping -> ControllerCommonSeoUrl -> AUrlRedirect -> Location header.
 *
 * Runs only when the storefront url is set, e.g.:
 *   ABC_TEST_STORE_URL=https://ucb.hopto.org phpunit -c phpunit_mysql.xml storefront/UrlRedirectHttpTest.php
 * The storefront must use the same database and cache directory as the tests.
 * Test records contain "phpunit" in the source.
 *
 * @property ADB $db
 * @property ACache $cache
 */
class UrlRedirectHttpTest extends AbanteCartTestBootstrap
{
    /** the storefront model reads active rules from this cache key */
    private const CACHE_KEY = 'url_redirects';
    /** test rules are checked before the store rules */
    private const PRIORITY = -100000;

    private const RULES = [
        // another system appends its own utm_content which overrides ours
        [
            'source'     => '^/phpunit-nra\?((?:.*&)?)utm_content=MINE&(.*&)?utm_content=[^&]*(.*)$',
            'match_type' => 'regex',
            'target'     => '/phpunit-nra?$1$2utm_content=MINE$3',
            'action'     => 301,
        ],
        [
            'source'     => '^/phpunit-item/(\d+)$',
            'match_type' => 'regex',
            'target'     => '/phpunit-product/$1',
            'action'     => 301,
        ],
        [
            'source'     => '/phpunit-old',
            'match_type' => 'exact',
            'target'     => '/phpunit-new',
            'action'     => 302,
            'keep_query' => 1,
        ],
        // parameter value with a double quote: $_GET is html-escaped by ARequest
        [
            'source'     => '/phpunit-quote?name=a"b',
            'match_type' => 'exact',
            'target'     => '/phpunit-quote-found',
            'action'     => 301,
        ],
        [
            'source'     => 'phpunit=1&id=5',
            'match_type' => 'query',
            'target'     => '/phpunit-query-found',
            'action'     => 301,
        ],
        // header value with a double quote: $_SERVER is html-escaped by ARequest
        [
            'source'              => '/phpunit-header',
            'source_http_headers' => 'X-Phpunit: a"b',
            'match_type'          => 'exact',
            'target'              => '/phpunit-header-found',
            'action'              => 302,
        ],
        [
            'source'     => '/phpunit-gone',
            'match_type' => 'exact',
            'target'     => '',
            'action'     => 404,
        ],
    ];

    private string $storeUrl = '';

    protected function setUp(): void
    {
        $this->storeUrl = rtrim((string)getenv('ABC_TEST_STORE_URL'), '/');
        if ($this->storeUrl === '') {
            $this->markTestSkipped('Set ABC_TEST_STORE_URL to the storefront url to run http tests.');
        }
        if (!function_exists('curl_init')) {
            $this->markTestSkipped('php curl extension is required.');
        }
        // constants can be defined only once per process
        if (!defined('ABC_TEST_ROOT_PATH')) {
            parent::bootstrap();
        } else {
            $this->registry = Registry::getInstance();
        }
        $this->deleteTestRules();
        foreach (self::RULES as $rule) {
            $rule += ['source_http_headers' => '', 'keep_query' => 0];
            $this->db->query(
                "INSERT INTO " . $this->db->table('url_redirects') . "
                 SET source = '" . $this->db->escape($rule['source']) . "',
                     source_host = '',
                     source_http_headers = '" . $this->db->escape($rule['source_http_headers']) . "',
                     match_type = '" . $this->db->escape($rule['match_type']) . "',
                     target = '" . $this->db->escape($rule['target']) . "',
                     action = " . (int)$rule['action'] . ",
                     keep_query = " . (int)$rule['keep_query'] . ",
                     priority = " . self::PRIORITY . ",
                     status = 1,
                     comment = 'phpunit'"
            );
        }
        $this->clearCache();
    }

    protected function tearDown(): void
    {
        if ($this->registry) {
            $this->deleteTestRules();
            $this->clearCache();
        }
    }

    private function deleteTestRules(): void
    {
        $this->db->query(
            "DELETE FROM " . $this->db->table('url_redirects') . "
             WHERE source LIKE '%phpunit%'"
        );
    }

    /**
     * The storefront keeps active rules in the file cache.
     */
    private function clearCache(): void
    {
        $enabled = $this->cache->isCacheEnabled();
        $this->cache->enableCache();
        $this->cache->remove(self::CACHE_KEY);
        if (!$enabled) {
            $this->cache->disableCache();
        }
    }

    /**
     * @param string $uri
     * @param array $headers lines "Name: value"
     *
     * @return array [http code, location without scheme and host]
     */
    private function request(string $uri, array $headers = []): array
    {
        $ch = curl_init($this->storeUrl . $uri);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 30,
        ]);
        $response = curl_exec($ch);
        $this->assertNotFalse($response, 'curl error: ' . curl_error($ch));
        $code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $location = (string)curl_getinfo($ch, CURLINFO_REDIRECT_URL);
        curl_close($ch);

        if ($location !== '') {
            // the storefront builds absolute urls from its own config, compare path and query only
            $query = parse_url($location, PHP_URL_QUERY);
            $location = parse_url($location, PHP_URL_PATH) . ($query !== null ? '?' . $query : '');
        }
        return [$code, $location];
    }

    #[DataProvider('redirectProvider')]
    public function testRedirect(string $uri, array $headers, int $expectedCode, string $expectedLocation): void
    {
        [$code, $location] = $this->request($uri, $headers);
        $this->assertSame($expectedCode, $code, $uri);
        $this->assertSame($expectedLocation, $location, $uri);
    }

    public static function redirectProvider(): array
    {
        return [
            'repeated utm_content'              => [
                '/phpunit-nra?utm_content=MINE&utm_content=12345', [], 301, '/phpunit-nra?utm_content=MINE',
            ],
            'repeated utm_content, other params' => [
                '/phpunit-nra?a=1&utm_content=MINE&b=2&utm_content=xyz&c=3', [], 301,
                '/phpunit-nra?a=1&b=2&utm_content=MINE&c=3',
            ],
            'regex by path'                     => ['/phpunit-item/42', [], 301, '/phpunit-product/42'],
            'exact, query string kept'          => ['/phpunit-old?x=1&y=2', [], 302, '/phpunit-new?x=1&y=2'],
            'exact, path case-insensitive'      => ['/PHPUNIT-OLD', [], 302, '/phpunit-new'],
            'exact, quote in parameter'         => ['/phpunit-quote?name=a%22b', [], 301, '/phpunit-quote-found'],
            'query'                             => ['/index.php?phpunit=1&id=5', [], 301, '/phpunit-query-found'],
            'http header with quote'            => [
                '/phpunit-header', ['X-Phpunit: a"b'], 302, '/phpunit-header-found',
            ],
            'not found action'                  => ['/phpunit-gone', [], 404, ''],
        ];
    }

    #[DataProvider('noRedirectProvider')]
    public function testNoRedirect(string $uri, array $headers): void
    {
        [, $location] = $this->request($uri, $headers);
        // the page itself may not exist, it is enough that no rule redirects
        $this->assertSame('', $location, $uri);
    }

    public static function noRedirectProvider(): array
    {
        return [
            // our value only: no loop
            'single utm_content'      => ['/phpunit-nra?utm_content=MINE', []],
            'foreign utm_content'     => ['/phpunit-nra?utm_content=12345', []],
            'regex no match'          => ['/phpunit-item/abc', []],
            'query param missing'     => ['/index.php?phpunit=1', []],
            'quote value differs'     => ['/phpunit-quote?name=ab', []],
            'http header missing'     => ['/phpunit-header', []],
            'http header differs'     => ['/phpunit-header', ['X-Phpunit: other']],
        ];
    }
}
