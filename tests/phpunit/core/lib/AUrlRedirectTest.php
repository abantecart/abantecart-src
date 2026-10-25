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

namespace lib;

use AUrlRedirect;
use phpunit\AbanteCartTestBootstrap;
use Registry;

/**
 * Matching of redirect rules (table url_redirects) without database and headers.
 */
class AUrlRedirectTest extends AbanteCartTestBootstrap
{
    private const STORE_URL = 'https://shop.test/';
    private const HOST = 'shop.test';

    protected function setUp(): void
    {
        // constants can be defined only once per process
        if (!defined('ABC_TEST_ROOT_PATH')) {
            parent::bootstrap();
        } else {
            $this->registry = Registry::getInstance();
        }
    }

    /**
     * @param array $rule
     *
     * @return array rule with defaults as it comes from the storefront model
     */
    private static function rule(array $rule): array
    {
        static $id = 0;
        return array_merge(
            [
                'id'          => ++$id,
                'source'      => '',
                'source_host' => '',
                'source_http_headers' => '',
                'match_type'  => 'exact',
                'target'      => '',
                'action'      => 301,
                'keep_query'  => 0,
            ],
            $rule
        );
    }

    /**
     * @param array $rules
     * @param string $uri
     * @param array $get
     * @param string $host
     * @param string $storeUrl
     *
     * @return array|null
     */
    private function resolve(
        array $rules,
        string $uri,
        array $get = [],
        string $host = self::HOST,
        string $storeUrl = self::STORE_URL
    ): ?array {
        return (new AUrlRedirect($rules, $storeUrl))->resolve($host, $uri, $get);
    }

    /**
     * @dataProvider redirectProvider
     */
    public function testRedirect(array $rule, string $uri, array $get, ?string $expectedUrl): void
    {
        $result = $this->resolve([self::rule($rule)], $uri, $get);
        if ($expectedUrl === null) {
            $this->assertNull($result);
            return;
        }
        $this->assertNotNull($result);
        $this->assertSame((int)$rule['action'] ?: 301, $result['action']);
        $this->assertSame($expectedUrl, $result['url']);
    }

    public static function redirectProvider(): array
    {
        $exact = ['source' => '/old-product', 'target' => '/new-product'];
        $prefix = ['match_type' => 'prefix', 'source' => '/old-cat', 'target' => '/new-cat', 'action' => 302];
        $regex = ['match_type' => 'regex', 'source' => '^/item/(\d+)/(\w+)$', 'target' => '/product/$2-$1'];
        $query = [
            'match_type' => 'query',
            'source'     => 'rt=product/product&product_id=12',
            'target'     => '/new-product',
        ];
        $productGet = ['rt' => 'product/product', 'product_id' => '12'];

        return [
            // exact
            'exact path'                     => [$exact, '/old-product', [], 'https://shop.test/new-product'],
            'exact trailing slash'           => [$exact, '/old-product/', [], 'https://shop.test/new-product'],
            'exact letter case'              => [$exact, '/OLD-Product', [], 'https://shop.test/new-product'],
            'exact url-encoded path'         => [
                ['source' => '/старый товар', 'target' => '/new'], '/%D1%81%D1%82%D0%B0%D1%80%D1%8B%D0%B9%20%D1%82%D0%BE%D0%B2%D0%B0%D1%80', [], 'https://shop.test/new',
            ],
            'exact other path'               => [$exact, '/old-product-2', [], null],
            'exact sub path'                 => [$exact, '/old-product/more', [], null],
            'exact query dropped'            => [$exact, '/old-product?a=1', ['a' => '1'], 'https://shop.test/new-product'],
            'exact query kept'               => [
                $exact + ['keep_query' => 1], '/old-product?a=1&b=2', ['a' => '1', 'b' => '2'],
                'https://shop.test/new-product?a=1&b=2',
            ],
            'exact query kept, target query' => [
                ['source' => '/old', 'target' => '/new?x=1#top', 'keep_query' => 1], '/old?a=1', ['a' => '1'],
                'https://shop.test/new?x=1&a=1#top',
            ],
            'exact absolute target'          => [
                ['source' => '/old', 'target' => 'https://other.test/new'], '/old', [], 'https://other.test/new',
            ],
            'exact protocol-relative target' => [
                ['source' => '/old', 'target' => '//other.test/new'], '/old', [], '//other.test/new',
            ],
            'exact relative target w/o slash' => [
                ['source' => '/old', 'target' => 'new'], '/old', [], 'https://shop.test/new',
            ],
            // prefix
            'prefix equal to source'         => [$prefix, '/old-cat', [], 'https://shop.test/new-cat'],
            'prefix tail appended'           => [$prefix, '/old-cat/sub/item', [], 'https://shop.test/new-cat/sub/item'],
            'prefix tail encoded'            => [$prefix, '/old-cat/a%20b', [], 'https://shop.test/new-cat/a%20b'],
            'prefix letter case'             => [$prefix, '/OLD-CAT/Item', [], 'https://shop.test/new-cat/Item'],
            'prefix not a segment'           => [$prefix, '/old-category', [], null],
            'prefix tail before target query' => [
                ['match_type' => 'prefix', 'source' => '/old', 'target' => '/new/?x=1'], '/old/item', [],
                'https://shop.test/new/item?x=1',
            ],
            // regex
            'regex groups'                   => [$regex, '/item/15/shoes', [], 'https://shop.test/product/shoes-15'],
            'regex no match'                 => [$regex, '/item/abc/shoes', [], null],
            'regex missing group is empty'   => [
                ['match_type' => 'regex', 'source' => '^/blog/(.+)$', 'target' => '/news/$1$2'], '/blog/post', [],
                'https://shop.test/news/post',
            ],
            'regex case insensitive'         => [$regex, '/ITEM/15/shoes', [], 'https://shop.test/product/shoes-15'],
            'regex with delimiter char'      => [
                ['match_type' => 'regex', 'source' => '^/a~b$', 'target' => '/c'], '/a~b', [], 'https://shop.test/c',
            ],
            'regex invalid pattern'          => [
                ['match_type' => 'regex', 'source' => '^/(unclosed', 'target' => '/c'], '/(unclosed', [], null,
            ],
            // query
            'query match'                    => [$query, '/index.php?rt=product/product&product_id=12', $productGet, 'https://shop.test/new-product'],
            'query other params kept'        => [
                $query + ['keep_query' => 1], '/index.php', $productGet + ['utm' => 'x'],
                'https://shop.test/new-product?utm=x',
            ],
            'query other params dropped'     => [$query, '/index.php', $productGet + ['utm' => 'x'], 'https://shop.test/new-product'],
            'query any path'                 => [$query, '/some/page', $productGet, 'https://shop.test/new-product'],
            'query value differs'            => [$query, '/index.php', ['rt' => 'product/product', 'product_id' => '13'], null],
            'query param missing'            => [$query, '/index.php', ['rt' => 'product/product'], null],
            'query array value'              => [$query, '/index.php', ['rt' => 'product/product', 'product_id' => ['12']], null],
            'query without params'           => [$query, '/index.php', [], null],
        ];
    }

    /**
     * @dataProvider blockingActionProvider
     */
    public function testBlockingActions(int $action): void
    {
        $result = $this->resolve([self::rule(['source' => '/secret', 'action' => $action])], '/secret');
        $this->assertSame($action, $result['action']);
        $this->assertSame('', $result['url']);
    }

    public static function blockingActionProvider(): array
    {
        return ['403' => [403], '404' => [404], '410' => [410]];
    }

    public function testBlockingActionWorksForEveryMatchType(): void
    {
        $rules = [
            self::rule(['match_type' => 'prefix', 'source' => '/private', 'action' => 403]),
            self::rule(['match_type' => 'regex', 'source' => '\.bak$', 'action' => 404]),
            self::rule(['match_type' => 'query', 'source' => 'rt=account/old', 'action' => 410]),
        ];
        $this->assertSame(403, $this->resolve($rules, '/private/file')['action']);
        $this->assertSame(404, $this->resolve($rules, '/db.bak')['action']);
        $this->assertSame(410, $this->resolve($rules, '/', ['rt' => 'account/old'])['action']);
    }

    public function testSourceHostMatchesReferer(): void
    {
        $rules = [self::rule(['source' => '/old', 'source_host' => 'google.com', 'target' => '/new'])];
        $resolve = fn(string $referer, string $ip = '') => (new AUrlRedirect($rules, self::STORE_URL))
            ->resolve(self::HOST, '/old', [], $referer, $ip);
        $this->assertNotNull($resolve('https://google.com/search?q=1'));
        $this->assertNotNull($resolve('HTTPS://Google.com:8080/'));
        $this->assertNull($resolve('https://www.google.com/'));
        $this->assertNull($resolve('https://other.test/'));
        $this->assertNull($resolve(''));
    }

    public function testSourceHostMatchesVisitorIp(): void
    {
        $rules = [self::rule(['source' => '/old', 'source_host' => '203.0.113.7', 'target' => '/new'])];
        $resolver = new AUrlRedirect($rules, self::STORE_URL);
        $this->assertNotNull($resolver->resolve(self::HOST, '/old', [], '', '203.0.113.7'));
        $this->assertNull($resolver->resolve(self::HOST, '/old', [], '', '203.0.113.8'));
        // the request host does not matter
        $this->assertNull($resolver->resolve('203.0.113.7', '/old', []));
    }

    public function testEmptySourceWithSourceHostMatchesAnyUrl(): void
    {
        $rules = [self::rule(['source' => '', 'source_host' => '203.0.113.7', 'target' => '/blocked', 'action' => 302])];
        $resolver = new AUrlRedirect($rules, self::STORE_URL);
        $result = $resolver->resolve(self::HOST, '/any/page', [], '', '203.0.113.7');
        $this->assertSame('https://shop.test/blocked', $result['url']);
        $this->assertNull($resolver->resolve(self::HOST, '/any/page', [], '', '10.0.0.1'));
    }

    /**
     * @dataProvider headersProvider
     */
    public function testSourceHttpHeaders(string $ruleHeaders, array $requestHeaders, bool $expected): void
    {
        $rules = [self::rule(['source' => '/old', 'source_http_headers' => $ruleHeaders, 'target' => '/new'])];
        $result = (new AUrlRedirect($rules, self::STORE_URL))
            ->resolve(self::HOST, '/old', [], '', '', $requestHeaders);
        $this->assertSame($expected, $result !== null);
    }

    public static function headersProvider(): array
    {
        $ua = ['User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1)'];
        return [
            'no header in rule'              => ['', [], true],
            'header found'                   => ['User-Agent: Googlebot', $ua, true],
            'name is case-insensitive'       => ['user-agent: Googlebot', ['USER-AGENT' => 'Googlebot/2.1'], true],
            'value is case-insensitive'      => ['User-Agent: googlebot', $ua, true],
            'value differs'                  => ['User-Agent: Bingbot', $ua, false],
            'header is absent'               => ['User-Agent: Googlebot', [], false],
            'presence only'                  => ['Accept-Language:', ['Accept-Language' => 'ru'], true],
            'presence only, absent'          => ['Accept-Language:', $ua, false],
            'all headers found'              => [
                "User-Agent: Googlebot\r\nAccept-Language: ru",
                $ua + ['Accept-Language' => 'ru-RU,ru;q=0.9'],
                true,
            ],
            'one of headers is absent'       => ["User-Agent: Googlebot\nAccept-Language: ru", $ua, false],
            'one of headers differs'         => [
                "User-Agent: Googlebot\nAccept-Language: ru",
                $ua + ['Accept-Language' => 'en'],
                false,
            ],
            'invalid rule headers never match' => ['not a header', $ua, false],
        ];
    }

    public function testSourceHttpHeadersWithEmptySource(): void
    {
        $rules = [self::rule(['source' => '', 'source_http_headers' => 'User-Agent: badbot', 'action' => 403])];
        $resolver = new AUrlRedirect($rules, self::STORE_URL);
        $this->assertSame(403, $resolver->resolve(self::HOST, '/any', [], '', '', ['User-Agent' => 'BadBot/1'])['action']);
        $this->assertNull($resolver->resolve(self::HOST, '/any', [], '', '', ['User-Agent' => 'Firefox']));
    }

    public function testParseHeaders(): void
    {
        $this->assertSame([], AUrlRedirect::parseHeaders(" \n\r\n"));
        $this->assertSame(
            [['User-Agent', 'Mozilla/5.0 (X11; Linux)'], ['Accept', ''], ['X-Test', 'a:b']],
            AUrlRedirect::parseHeaders("User-Agent:  Mozilla/5.0 (X11; Linux) \r\nAccept:\n\nX-Test : a:b")
        );
        foreach (['no colon', ': value', 'Bad Name: value', "X-A: va\x01lue", 'Имя: value'] as $invalid) {
            $this->assertNull(AUrlRedirect::parseHeaders($invalid), $invalid);
        }
    }

    public function testFirstMatchedRuleWins(): void
    {
        $rules = [
            self::rule(['source' => '/other', 'target' => '/first']),
            self::rule(['match_type' => 'prefix', 'source' => '/old', 'target' => '/second']),
            self::rule(['source' => '/old', 'target' => '/third']),
        ];
        $result = $this->resolve($rules, '/old');
        $this->assertSame($rules[1]['id'], $result['rule']['id']);
        $this->assertSame('https://shop.test/second', $result['url']);
    }

    public function testRuleToTheSamePageIsSkipped(): void
    {
        $rules = [
            self::rule(['source' => '/loop', 'target' => '/loop/']),
            self::rule(['source' => '/loop', 'target' => '/next']),
        ];
        $result = $this->resolve($rules, '/loop');
        $this->assertSame($rules[1]['id'], $result['rule']['id']);
        $this->assertNull($this->resolve([$rules[0]], '/loop'));
    }

    public function testStoreInSubfolder(): void
    {
        $storeUrl = 'https://shop.test/store/';
        $rules = [self::rule(['source' => '/old', 'target' => '/new'])];
        $result = $this->resolve($rules, '/store/old', [], self::HOST, $storeUrl);
        $this->assertSame('https://shop.test/store/new', $result['url']);
        // folder name must match the whole segment
        $this->assertNull($this->resolve($rules, '/storefront/old', [], self::HOST, $storeUrl));
    }

    public function testStoreAliasPath(): void
    {
        // seo_prefix of sub-path-based multistore is a part of the store url
        $storeUrl = 'https://shop.test/' . 'second/';
        $rules = [self::rule(['source' => '/old', 'target' => '/new'])];
        $result = $this->resolve($rules, '/second/old', [], self::HOST, $storeUrl);
        $this->assertSame('https://shop.test/second/new', $result['url']);
    }

    public function testIndexFileIsStoreRoot(): void
    {
        $rules = [self::rule(['match_type' => 'prefix', 'source' => '/old', 'target' => '/new'])];
        $result = $this->resolve($rules, '/index.php/old/item');
        $this->assertSame('https://shop.test/new/item', $result['url']);
    }

    public function testNoRules(): void
    {
        $this->assertNull($this->resolve([], '/anything'));
    }

    public function testUnknownMatchType(): void
    {
        $this->assertNull($this->resolve([self::rule(['match_type' => 'fuzzy', 'source' => '/old'])], '/old'));
    }
}
