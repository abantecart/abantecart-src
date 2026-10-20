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

namespace storefront\model\tool;

use ACache;
use AConfig;
use ADB;
use ModelToolSeoUrl;
use phpunit\AbanteCartTestBootstrap;

/**
 * Storefront model of SEO urls: rewrite() of links and getSEOKeyword().
 *
 * Admin and storefront have the model with the same class name (ModelToolSeoUrl) and the same registry key,
 * so every test runs in its own php process and creates the storefront model directly.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 *
 * @property ADB $db
 * @property AConfig $config
 * @property ACache $cache
 */
class ModelToolSeoUrlTest extends AbanteCartTestBootstrap
{
    /** fake language ids: url_aliases does not depend on the languages table */
    private const LANG_A = 9001;
    private const LANG_B = 9002;
    private const LINK = 'http://localhost:8080/index.php?rt=';

    /** @var ModelToolSeoUrl */
    private $model;
    private $origConfig = [];

    protected function setUp(): void
    {
        parent::bootstrap();
        require_once ABC_TEST_ROOT_PATH . '/storefront/model/tool/seo_url.php';
        $this->model = new ModelToolSeoUrl($this->registry);

        foreach (['enable_seo_url', 'storefront_language_id', 'config_cache_enable'] as $key) {
            $this->origConfig[$key] = $this->config->get($key);
        }
        $this->config->set('enable_seo_url', 1);
        $this->config->set('storefront_language_id', self::LANG_A);
        $this->config->set('config_cache_enable', 0);

        $this->cleanup();

        $this->alias('product_id=900001', 'phpunit-product', self::LANG_A);
        $this->alias('product_id=900001', 'phpunit-produkt', self::LANG_B);
        $this->alias('category_id=900002', 'phpunit-cat', self::LANG_A);
        $this->alias('category_id=900003', 'phpunit-subcat', self::LANG_A);
        $this->alias('manufacturer_id=900004', 'phpunit-brand', self::LANG_A);
        $this->alias('content_id=900005', 'phpunit-parent-page', self::LANG_A);
        $this->alias('content_id=900006', 'phpunit-child-page', self::LANG_A);
        $this->alias('collection_id=900007', 'phpunit-collection', self::LANG_A);
    }

    protected function tearDown(): void
    {
        $this->cleanup();
        foreach ($this->origConfig as $key => $value) {
            $this->config->set($key, $value);
        }
        parent::tearDown();
    }

    private function cleanup(): void
    {
        $this->db->query(
            "DELETE FROM " . $this->db->table('url_aliases') . "
             WHERE keyword LIKE 'phpunit-%'"
        );
        $this->cache->remove('url_aliases');
    }

    private function alias(string $query, string $keyword, int $languageId): void
    {
        $this->db->query(
            "INSERT INTO " . $this->db->table('url_aliases') . "
             SET query = '" . $this->db->escape($query) . "',
                 keyword = '" . $this->db->escape($keyword) . "',
                 language_id = " . $languageId
        );
    }

    private function link(string $query): string
    {
        return self::LINK . $query;
    }

    // ---------------------------------------------------------------- rewrite()

    public function testRewriteIsOffWhenSeoUrlDisabled(): void
    {
        $this->config->set('enable_seo_url', 0);
        $link = $this->link('product/product&product_id=900001');
        $this->assertSame($link, $this->model->rewrite($link));
    }

    public function testRewriteProduct(): void
    {
        $this->assertSame(
            'http://localhost:8080/phpunit-product',
            $this->model->rewrite($this->link('product/product&product_id=900001'))
        );
    }

    public function testRewriteDecodesAmpersandEntity(): void
    {
        $this->assertSame(
            'http://localhost:8080/phpunit-product',
            $this->model->rewrite(self::LINK . 'product/product&amp;product_id=900001')
        );
    }

    public function testRewriteKeepsPathAndOtherParameters(): void
    {
        $this->assertSame(
            'http://localhost:8080/shop/phpunit-product?sort=p.price&page=2',
            $this->model->rewrite(
                'http://localhost:8080/shop/index.php?rt=product/product&product_id=900001&sort=p.price&page=2'
            )
        );
    }

    /**
     * @dataProvider objectProvider
     */
    public function testRewriteObjects(string $query, string $expected): void
    {
        $this->assertSame(
            'http://localhost:8080/' . $expected,
            $this->model->rewrite($this->link($query))
        );
    }

    public static function objectProvider(): array
    {
        return [
            'category'     => ['product/category&category_id=900002', 'phpunit-cat'],
            'manufacturer' => ['product/manufacturer&manufacturer_id=900004', 'phpunit-brand'],
            'content'      => ['content/content&content_id=900005', 'phpunit-parent-page'],
            'collection'   => ['product/collection&collection_id=900007', 'phpunit-collection'],
        ];
    }

    public function testRewriteCategoryPathUsesLastCategory(): void
    {
        $this->assertSame(
            'http://localhost:8080/phpunit-subcat',
            $this->model->rewrite($this->link('product/category&path=900002_900003'))
        );
    }

    public function testRewriteContentWithParent(): void
    {
        $this->assertSame(
            'http://localhost:8080/phpunit-parent-page/phpunit-child-page',
            $this->model->rewrite($this->link('content/content&content_id=900006&parent_id=900005'))
        );
        // parent without keyword is just skipped
        $this->assertSame(
            'http://localhost:8080/phpunit-child-page',
            $this->model->rewrite($this->link('content/content&content_id=900006&parent_id=999999'))
        );
    }

    public function testRewriteProductInCategory(): void
    {
        $this->assertSame(
            'http://localhost:8080/phpunit-subcat/phpunit-product',
            $this->model->rewrite($this->link('product/product&path=900002_900003&product_id=900001'))
        );
    }

    public function testRewriteWithoutKeywordReturnsLinkAsIs(): void
    {
        $link = $this->link('product/product&product_id=999999');
        $this->assertSame($link, $this->model->rewrite($link));

        $link = $this->link('index/home');
        $this->assertSame($link, $this->model->rewrite($link));

        // array value cannot have a keyword
        $link = self::LINK . 'product/product&product_id[]=900001';
        $this->assertSame($link, $this->model->rewrite($link));
    }

    public function testRewriteUsesStorefrontLanguage(): void
    {
        $link = $this->link('product/product&product_id=900001');

        $this->config->set('storefront_language_id', self::LANG_B);
        $this->assertSame('http://localhost:8080/phpunit-produkt', $this->model->rewrite($link));

        // no alias of the category for this language: link stays as is
        $link = $this->link('product/category&category_id=900002');
        $this->assertSame($link, $this->model->rewrite($link));
    }

    // ------------------------------------------------------------ getSEOKeyword()

    public function testGetSEOKeywordPerLanguage(): void
    {
        $this->assertSame(
            'phpunit-product',
            $this->model->getSEOKeyword('product', 'product_id', 900001, self::LANG_A)
        );
        $this->assertSame(
            'phpunit-produkt',
            $this->model->getSEOKeyword('product', 'product_id', 900001, self::LANG_B)
        );
        $this->assertEmpty($this->model->getSEOKeyword('product', 'product_id', 999999, self::LANG_A));
        $this->assertEmpty($this->model->getSEOKeyword('category', 'category_id', 900002, self::LANG_B));
    }

    public function testGetSEOKeywordFromCache(): void
    {
        $this->config->set('config_cache_enable', 1);
        $this->assertSame(
            'phpunit-product',
            $this->model->getSEOKeyword('product', 'product_id', 900001, self::LANG_A)
        );
        $this->assertSame(
            'phpunit-produkt',
            $this->model->getSEOKeyword('product', 'product_id', 900001, self::LANG_B)
        );
        $this->assertEmpty($this->model->getSEOKeyword('product', 'product_id', 999999, self::LANG_A));

        // the second call is served from the cache even when the table has changed
        $this->db->query(
            "UPDATE " . $this->db->table('url_aliases') . "
             SET keyword = 'phpunit-changed'
             WHERE query = 'product_id=900001' AND language_id = " . self::LANG_A
        );
        $this->assertSame(
            'phpunit-product',
            $this->model->getSEOKeyword('product', 'product_id', 900001, self::LANG_A)
        );
        // rewrite() works through the same cache
        $this->assertSame(
            'http://localhost:8080/phpunit-product',
            $this->model->rewrite($this->link('product/product&product_id=900001'))
        );

        // admin side drops the whole "url_aliases" cache group after any change of aliases
        $this->cache->remove('url_aliases');
        $this->assertSame(
            'phpunit-changed',
            $this->model->getSEOKeyword('product', 'product_id', 900001, self::LANG_A)
        );
    }
}
