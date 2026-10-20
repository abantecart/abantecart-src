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

namespace model;

use ACache;
use ADB;
use ALoader;
use ASession;
use ModelToolSeoUrl;
use phpunit\AbanteCartTestBootstrap;
use Registry;

/**
 * Admin model of SEO urls (url_aliases table). Test records contain "phpunit" in the keyword.
 *
 * @property ACache $cache
 * @property ADB $db
 * @property ALoader $load
 * @property ASession $session
 */
class ToolSeoUrlTest extends AbanteCartTestBootstrap
{
    /** fake language ids: url_aliases does not depend on the languages table */
    private const LANG_A = 9001;
    private const LANG_B = 9002;

    /** @var ModelToolSeoUrl */
    private $model;
    private $origContentLanguageId;

    protected function setUp(): void
    {
        // constants can be defined only once per process
        if (!defined('ABC_TEST_ROOT_PATH')) {
            parent::bootstrap();
        } else {
            $this->registry = Registry::getInstance();
        }
        $this->origContentLanguageId = $this->session->data['content_language_id'] ?? null;
        $this->setContentLanguage(self::LANG_A);
        $this->cleanup();
        $this->model = $this->load->model('tool/seo_url');
    }

    protected function tearDown(): void
    {
        $this->cleanup();
        $this->session->data['content_language_id'] = $this->origContentLanguageId;
    }

    private function cleanup(): void
    {
        $this->db->query(
            "DELETE FROM " . $this->db->table('url_aliases') . "
             WHERE keyword LIKE 'phpunit%' OR query LIKE 'phpunit%' OR query LIKE '%phpunit%'"
        );
        $this->db->query(
            "DELETE FROM " . $this->db->table('url_redirects') . "
             WHERE target LIKE '%phpunit%'"
        );
    }

    private function setContentLanguage(int $languageId): void
    {
        $this->session->data['content_language_id'] = $languageId;
    }

    private function rows(string $query): array
    {
        return $this->db->query(
            "SELECT url_alias_id, keyword, query, language_id
             FROM " . $this->db->table('url_aliases') . "
             WHERE query = '" . $this->db->escape($query) . "'
             ORDER BY language_id"
        )->rows;
    }

    private function add(string $keyword = 'phpunit-seo', string $query = 'phpunit_id=1'): int
    {
        return $this->model->addSeoUrl(['seo_keyword' => $keyword, 'query' => $query]);
    }

    public function testAddUsesContentLanguage(): void
    {
        // language_id from the input must be ignored
        $id = $this->model->addSeoUrl([
            'seo_keyword' => 'phpunit-seo',
            'query'       => 'phpunit_id=1',
            'language_id' => self::LANG_B,
        ]);
        $this->assertGreaterThan(0, $id);
        $rows = $this->rows('phpunit_id=1');
        $this->assertCount(1, $rows);
        $this->assertSame('phpunit-seo', $rows[0]['keyword']);
        $this->assertEquals(self::LANG_A, $rows[0]['language_id']);
        $this->assertEquals($id, $rows[0]['url_alias_id']);
    }

    public function testAddSameQueryTwiceDoesNotDuplicate(): void
    {
        $id = $this->add('phpunit-first');
        $id2 = $this->add('phpunit-second');
        $this->assertEquals($id, $id2);
        $rows = $this->rows('phpunit_id=1');
        $this->assertCount(1, $rows);
        $this->assertSame('phpunit-second', $rows[0]['keyword']);
    }

    public function testAddSameQueryForTwoLanguages(): void
    {
        $this->add('phpunit-en');
        $this->setContentLanguage(self::LANG_B);
        $this->add('phpunit-de');

        $rows = $this->rows('phpunit_id=1');
        $this->assertCount(2, $rows);
        $this->assertSame('phpunit-en', $rows[0]['keyword']);
        $this->assertSame('phpunit-de', $rows[1]['keyword']);
    }

    public function testAddWithRouteBuildsStoredQuery(): void
    {
        $id = $this->model->addSeoUrl([
            'seo_keyword' => 'phpunit-route',
            'route'       => 'pages/phpunit/page',
            'query'       => 'phpunit_id=5',
        ]);
        $row = $this->model->getSeoUrl($id);
        $this->assertSame('pages/phpunit/page', $row['route']);
        $this->assertSame('phpunit_id=5', $row['query']);
        $this->assertSame('rt=pages/phpunit/page&phpunit_id=5', $this->rows('rt=pages/phpunit/page&phpunit_id=5')[0]['query']);
    }

    public function testGetSeoUrl(): void
    {
        $this->assertSame([], $this->model->getSeoUrl(0));

        $id = $this->add('phpunit-seo');
        $row = $this->model->getSeoUrl($id);
        $this->assertSame($id, $row['url_alias_id']);
        $this->assertSame('phpunit-seo', $row['seo_keyword']);
        $this->assertSame(self::LANG_A, $row['language_id']);
    }

    public function testGetSeoUrlOfOtherLanguageReturnsContentLanguageAlias(): void
    {
        $idA = $this->add('phpunit-en');
        $this->setContentLanguage(self::LANG_B);
        $idB = $this->add('phpunit-de');

        // requesting by the id of another language gives alias of the content language
        $row = $this->model->getSeoUrl($idA);
        $this->assertSame($idB, $row['url_alias_id']);
        $this->assertSame('phpunit-de', $row['seo_keyword']);
        $this->assertSame(self::LANG_B, $row['language_id']);

        $this->setContentLanguage(self::LANG_A);
        $row = $this->model->getSeoUrl($idB);
        $this->assertSame($idA, $row['url_alias_id']);
        $this->assertSame('phpunit-en', $row['seo_keyword']);
    }

    public function testGetSeoUrlWithoutAliasForContentLanguage(): void
    {
        $idA = $this->add('phpunit-en', 'phpunit_id=7');
        $this->setContentLanguage(self::LANG_B);

        $row = $this->model->getSeoUrl($idA);
        $this->assertNotEmpty($row, 'record must be found to be editable');
        $this->assertSame('', $row['seo_keyword']);
        $this->assertSame('phpunit_id=7', $row['query']);
        $this->assertSame(self::LANG_B, $row['language_id']);
    }

    public function testUpdateKeyword(): void
    {
        $id = $this->add('phpunit-old');
        $this->model->updateSeoUrl($id, ['seo_keyword' => 'phpunit-new']);

        $rows = $this->rows('phpunit_id=1');
        $this->assertCount(1, $rows);
        $this->assertSame('phpunit-new', $rows[0]['keyword']);
        $this->assertEquals($id, $rows[0]['url_alias_id']);
    }

    public function testUpdateQueryChangesRowById(): void
    {
        $id = $this->add('phpunit-seo', 'phpunit_id=1');
        $this->model->updateSeoUrl($id, ['query' => 'phpunit_id=2']);

        $this->assertSame([], $this->rows('phpunit_id=1'));
        $rows = $this->rows('phpunit_id=2');
        $this->assertCount(1, $rows);
        $this->assertEquals($id, $rows[0]['url_alias_id']);
        $this->assertSame('phpunit-seo', $rows[0]['keyword']);
    }

    public function testUpdateOfOtherLanguageRecordKeepsIt(): void
    {
        $idA = $this->add('phpunit-en');
        $this->setContentLanguage(self::LANG_B);
        // there is no alias for LANG_B yet: it is created, the LANG_A one stays as is
        $this->model->updateSeoUrl($idA, ['seo_keyword' => 'phpunit-de']);

        $rows = $this->rows('phpunit_id=1');
        $this->assertCount(2, $rows);
        $this->assertSame('phpunit-en', $rows[0]['keyword']);
        $this->assertEquals(self::LANG_A, $rows[0]['language_id']);
        $this->assertSame('phpunit-de', $rows[1]['keyword']);
        $this->assertEquals(self::LANG_B, $rows[1]['language_id']);

        // now alias for LANG_B exists: update by the id of LANG_A changes only LANG_B
        $this->model->updateSeoUrl($idA, ['seo_keyword' => 'phpunit-de2']);
        $rows = $this->rows('phpunit_id=1');
        $this->assertCount(2, $rows);
        $this->assertSame('phpunit-en', $rows[0]['keyword']);
        $this->assertSame('phpunit-de2', $rows[1]['keyword']);
    }

    public function testUpdateOfUnknownIdDoesNothing(): void
    {
        $this->model->updateSeoUrl(0, ['seo_keyword' => 'phpunit-none', 'query' => 'phpunit_id=3']);
        $this->assertSame([], $this->rows('phpunit_id=3'));
    }

    public function testUpdateKeepsRedirectsWorking(): void
    {
        $id = $this->add('phpunit-old');
        /** @var \ModelToolRedirect $redirects */
        $redirects = $this->load->model('tool/redirect');
        $redirectId = $redirects->addRedirect(['source' => '/phpunit-src', 'target' => '/phpunit-old']);

        $this->model->updateSeoUrl($id, ['seo_keyword' => 'phpunit-new']);

        $this->assertStringContainsString('phpunit-new', $redirects->getRedirect($redirectId)['target']);
        $redirects->deleteRedirect($redirectId);
    }

    public function testDelete(): void
    {
        $id = $this->add();
        $this->model->deleteSeoUrl($id);
        $this->assertSame([], $this->model->getSeoUrl($id));
        $this->assertSame([], $this->rows('phpunit_id=1'));
    }

    /**
     * storefront keeps aliases in the "url_aliases" cache group (see storefront ModelToolSeoUrl::getSEOKeyword())
     */
    private function warmUpAliasCache(): string
    {
        $cacheKey = 'url_aliases.product.lang_' . self::LANG_A;
        $this->cache->push($cacheKey, ['900001' => 'phpunit-cached']);
        if ($this->cache->pull($cacheKey) === false) {
            $this->markTestSkipped('cache is not available');
        }
        return $cacheKey;
    }

    public function testAddClearsAliasCache(): void
    {
        $cacheKey = $this->warmUpAliasCache();
        $this->add();
        $this->assertFalse($this->cache->pull($cacheKey));
    }

    public function testUpdateClearsAliasCache(): void
    {
        $id = $this->add();
        $cacheKey = $this->warmUpAliasCache();
        $this->model->updateSeoUrl($id, ['seo_keyword' => 'phpunit-renamed']);
        $this->assertFalse($this->cache->pull($cacheKey));
    }

    public function testUpdateQueryClearsAliasCache(): void
    {
        $id = $this->add();
        $cacheKey = $this->warmUpAliasCache();
        $this->model->updateSeoUrl($id, ['query' => 'phpunit_id=2']);
        $this->assertFalse($this->cache->pull($cacheKey));
    }

    public function testDeleteClearsAliasCache(): void
    {
        $id = $this->add();
        $cacheKey = $this->warmUpAliasCache();
        $this->model->deleteSeoUrl($id);
        $this->assertFalse($this->cache->pull($cacheKey));
    }

    public function testValidateRequiredAndLimits(): void
    {
        $errors = $this->model->validateSeoUrl(['seo_keyword' => '', 'route' => '', 'query' => '']);
        $this->assertArrayHasKey('seo_keyword', $errors);
        $this->assertArrayHasKey('route', $errors);

        $errors = $this->model->validateSeoUrl([
            'seo_keyword' => str_repeat('a', 256),
            'query'       => 'phpunit_id=1',
        ]);
        $this->assertArrayHasKey('seo_keyword', $errors);

        $errors = $this->model->validateSeoUrl(['seo_keyword' => 'phpunit-x', 'route' => 'admin/secret']);
        $this->assertArrayHasKey('route', $errors);

        $errors = $this->model->validateSeoUrl(['seo_keyword' => 'phpunit-x', 'route' => 'pages/']);
        $this->assertArrayHasKey('route', $errors);

        $errors = $this->model->validateSeoUrl([
            'seo_keyword' => 'phpunit-x',
            'query'       => 'phpunit_id=' . str_repeat('1', 2048),
        ]);
        $this->assertArrayHasKey('query', $errors);

        $this->assertSame([], $this->model->validateSeoUrl([
            'seo_keyword' => 'phpunit-x',
            'route'       => 'responses/phpunit/page',
            'query'       => 'phpunit_id=1',
        ]));
    }

    public function testValidateUniquenessIsPerLanguage(): void
    {
        $id = $this->add('phpunit-seo', 'phpunit_id=1');

        // same keyword (any letter case) and same query in the same language
        $errors = $this->model->validateSeoUrl(['seo_keyword' => 'PHPUNIT-SEO', 'query' => 'phpunit_id=2']);
        $this->assertArrayHasKey('seo_keyword', $errors);
        $errors = $this->model->validateSeoUrl(['seo_keyword' => 'phpunit-other', 'query' => 'phpunit_id=1']);
        $this->assertArrayHasKey('query', $errors);

        // the record itself is not a conflict
        $this->assertSame(
            [],
            $this->model->validateSeoUrl(['seo_keyword' => 'phpunit-seo', 'query' => 'phpunit_id=1'], $id)
        );

        // other content language: the same keyword and query are allowed
        $this->setContentLanguage(self::LANG_B);
        $this->assertSame(
            [],
            $this->model->validateSeoUrl(['seo_keyword' => 'phpunit-seo', 'query' => 'phpunit_id=1'])
        );
        // and editing from another language does not conflict with the own alias of that language
        $idB = $this->add('phpunit-de', 'phpunit_id=1');
        $this->setContentLanguage(self::LANG_A);
        $this->assertSame(
            [],
            $this->model->validateSeoUrl(['seo_keyword' => 'phpunit-de3', 'query' => 'phpunit_id=1'], $idB)
        );
    }

    public function testListAndTotalNumRows(): void
    {
        $this->add('phpunit-b', 'phpunit_id=2');
        $this->add('phpunit-a', 'phpunit_id=1');
        $filter = "seo_keyword LIKE 'phpunit-%'";

        $list = $this->model->getSeoUrls(['subsql_filter' => $filter, 'sort' => 'seo_keyword', 'order' => 'ASC']);
        $this->assertEquals(2, $list[0]['total_num_rows']);
        $this->assertSame('phpunit-a', $list[0]['seo_keyword']);
        $this->assertSame('phpunit-b', $list[1]['seo_keyword']);

        $list = $this->model->getSeoUrls(['subsql_filter' => $filter, 'sort' => 'seo_keyword', 'order' => 'DESC', 'limit' => 1]);
        $this->assertSame('phpunit-b', $list[0]['seo_keyword']);

        // wrong sort field falls back to the keyword
        $list = $this->model->getSeoUrls(['subsql_filter' => $filter, 'sort' => 'bad; DROP TABLE x']);
        $this->assertSame('phpunit-a', $list[0]['seo_keyword']);
    }

    public function testListShowsOnlyContentLanguage(): void
    {
        $this->add('phpunit-en', 'phpunit_id=1');
        $this->setContentLanguage(self::LANG_B);
        $this->add('phpunit-de', 'phpunit_id=1');
        $this->add('phpunit-de2', 'phpunit_id=2');
        $filter = ['subsql_filter' => "seo_keyword LIKE 'phpunit-%'"];

        $list = $this->model->getSeoUrls($filter);
        $this->assertCount(2, $list);
        $this->assertEquals(2, $list[0]['total_num_rows']);

        $this->setContentLanguage(self::LANG_A);
        $list = $this->model->getSeoUrls($filter);
        $this->assertCount(1, $list);
        $this->assertSame('phpunit-en', $list[0]['seo_keyword']);
        $this->assertEquals(1, $list[0]['total_num_rows']);

        $this->setContentLanguage(9999);
        $this->assertSame([], $this->model->getSeoUrls($filter));
    }
}
