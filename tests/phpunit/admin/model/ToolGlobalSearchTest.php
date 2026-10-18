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

use ADB;
use ALoader;
use ASession;
use ModelToolGlobalSearch;
use PHPUnit\Framework\Attributes\DataProvider;
use phpunit\AbanteCartTestBootstrap;
use Registry;

/**
 * Admin model of the global search.
 * Values are stored html-encoded, so the search must find both encoded and plain needles, case insensitive.
 *
 * @property ADB $db
 * @property ALoader $load
 * @property ASession $session
 */
class ToolGlobalSearchTest extends AbanteCartTestBootstrap
{
    /** language that is not active, aliases of it must not be found */
    private const INACTIVE_LANGUAGE = 9001;

    /** @var ModelToolGlobalSearch */
    private $model;
    private $activeLanguageId;
    private $origStoreId;
    private $customerGroupId;

    protected function setUp(): void
    {
        // constants can be defined only once per process
        if (!defined('ABC_TEST_ROOT_PATH')) {
            parent::bootstrap();
        } else {
            $this->registry = Registry::getInstance();
        }
        $this->origStoreId = $this->session->data['current_store_id'] ?? null;
        $this->session->data['current_store_id'] = 0;
        $this->model = $this->load->model('tool/global_search');
        $this->activeLanguageId = (int)current($this->language->getActiveLanguages())['language_id'];
        $this->cleanup();

        $this->alias('phpunit_id=1', 'phpunit-Mixed-Case', $this->activeLanguageId);
        $this->alias('phpunit_id=2', 'phpunit-a&amp;b', $this->activeLanguageId);
        $this->alias('phpunit_id=3', 'phpunit-hidden-language', self::INACTIVE_LANGUAGE);
        $this->createFixtures();
    }

    protected function tearDown(): void
    {
        $this->cleanup();
        $this->session->data['current_store_id'] = $this->origStoreId;
    }

    private function cleanup(): void
    {
        $this->db->query(
            "DELETE FROM " . $this->db->table('url_aliases') . "
             WHERE keyword LIKE 'phpunit-%'"
        );
        $this->db->query("DELETE FROM " . $this->db->table('manufacturers') . " WHERE name LIKE 'phpunit%'");
        $this->db->query("DELETE FROM " . $this->db->table('messages') . " WHERE title LIKE 'phpunit%'");
        $this->db->query("DELETE FROM " . $this->db->table('customers') . " WHERE loginname LIKE 'phpunit_%'");
        $this->db->query("DELETE FROM " . $this->db->table('language_definitions') . " WHERE block = 'phpunit'");
        $this->db->query(
            "DELETE pd FROM " . $this->db->table('product_descriptions') . " pd
             INNER JOIN " . $this->db->table('products') . " p ON p.product_id = pd.product_id
             WHERE p.model LIKE 'PHPUNIT-%'"
        );
        $this->db->query("DELETE FROM " . $this->db->table('products') . " WHERE model LIKE 'PHPUNIT-%'");
    }

    /**
     * Values are stored html-encoded, as the admin forms do it
     */
    private function createFixtures(): void
    {
        $this->customerGroupId = (int)$this->db->query(
            "SELECT customer_group_id FROM " . $this->db->table('customer_groups') . " LIMIT 1"
        )->row['customer_group_id'];

        $this->db->query(
            "INSERT INTO " . $this->db->table('manufacturers') . "
             SET name = 'phpunit Brand &amp; Sons', sort_order = 0"
        );
        $this->db->query(
            "INSERT INTO " . $this->db->table('messages') . "
             SET title = 'phpunit Message', message = 'Don&#039;t PANIC &lt;b&gt;now&lt;/b&gt;'"
        );
        $this->db->query(
            "INSERT INTO " . $this->db->table('customers') . "
             SET loginname = 'phpunit_login', firstname = 'phpunitJohn', lastname = 'O&#039;Brien',
                 email = 'phpunit_login@example.com', status = 1, customer_group_id = " . $this->customerGroupId
        );
        $this->db->query(
            "INSERT INTO " . $this->db->table('language_definitions') . "
             SET block = 'phpunit', language_id = " . $this->activeLanguageId . ",
                 language_key = 'phpunit_key_text', language_value = 'Caf&eacute; Menu'"
        );
        $this->db->query(
            "INSERT INTO " . $this->db->table('products') . "
             SET model = 'PHPUNIT-Model-X1', sku = 'PHPUNIT-SKU-9', location = '', stock_status_id = 0,
                 manufacturer_id = 0, tax_class_id = 0, date_available = NOW()"
        );
        $productId = (int)$this->db->getLastId();
        $this->db->query(
            "INSERT INTO " . $this->db->table('product_descriptions') . "
             SET product_id = " . $productId . ", language_id = " . $this->activeLanguageId . ",
                 name = 'phpunit Tom &amp; Jerry', meta_keywords = '', meta_description = '',
                 description = '', blurb = ''"
        );
    }

    /**
     * @return string[] titles found in the section
     */
    private function found(string $category, string $keyword): array
    {
        $result = $this->model->getResult($category, $keyword, 'suggest')['result'];
        return array_column($result, 'title');
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

    /**
     * @return string[] keywords found in the seo_urls section
     */
    private function seoUrls(string $keyword): array
    {
        $result = $this->model->getResult('seo_urls', $keyword, 'suggest')['result'];
        return array_column($result, 'title');
    }

    public function testSeoUrlsAreInSearchSources(): void
    {
        $this->assertSame('seo_url', $this->model->getSearchSources('a')['seo_urls']);
    }

    public function testSeoUrlsCaseInsensitive(): void
    {
        $this->assertSame(['phpunit-Mixed-Case'], $this->seoUrls('mixed-CASE'));
    }

    public function testSeoUrlsFindEncodedEntities(): void
    {
        // plain and encoded needles find the same stored value
        $this->assertSame(['phpunit-a&amp;b'], $this->seoUrls('a&b'));
        $this->assertSame(['phpunit-a&amp;b'], $this->seoUrls('A&AMP;B'));
    }

    public function testSeoUrlsOnlyActiveLanguages(): void
    {
        $this->assertSame([], $this->seoUrls('hidden-language'));
    }

    public function testSeoUrlsListingLinksToEditForm(): void
    {
        $result = $this->model->getResult('seo_urls', 'phpunit-mixed')['result'];
        $this->assertCount(1, $result);
        $this->assertStringContainsString('tool/seo_url/update', $result[0]['href']);
        $this->assertStringContainsString('url_alias_id=', $result[0]['href']);
        $this->assertStringContainsString('phpunit-Mixed-Case', $result[0]['text']);
    }

    public function testSeoUrlsPaging(): void
    {
        $this->alias('phpunit_id=4', 'phpunit-page-1', $this->activeLanguageId);
        $this->alias('phpunit_id=5', 'phpunit-page-2', $this->activeLanguageId);
        $this->alias('phpunit_id=6', 'phpunit-page-3', $this->activeLanguageId);
        $this->alias('phpunit_id=7', 'phpunit-page-4', $this->activeLanguageId);

        // suggestion mode returns only 3 rows, but the total is full
        $result = $this->model->getResult('seo_urls', 'phpunit-page', 'suggest')['result'];
        $this->assertCount(3, $result);
        $this->assertEquals(4, $result[0]['total_num_rows']);
    }

    public static function textVariantsProvider(): array
    {
        return [
            // category, searched text, expected title of the found record
            'manufacturer, plain ampersand'   => ['manufacturers', 'Brand & Sons', 'phpunit Brand &amp; Sons'],
            'manufacturer, encoded ampersand' => ['manufacturers', 'Brand &amp; Sons', 'phpunit Brand &amp; Sons'],
            'manufacturer, upper case'        => ['manufacturers', 'BRAND & SONS', 'phpunit Brand &amp; Sons'],
            'manufacturer, lower case'        => ['manufacturers', 'brand &amp; sons', 'phpunit Brand &amp; Sons'],
            'manufacturer, part of word'      => ['manufacturers', 'rand', 'phpunit Brand &amp; Sons'],
            'product name, plain'             => ['products', 'tom & jerry', 'phpunit Tom &amp; Jerry'],
            'product name, encoded'           => ['products', 'Tom &amp; Jerry', 'phpunit Tom &amp; Jerry'],
            'product name, upper case'        => ['products', 'TOM & JERRY', 'phpunit Tom &amp; Jerry'],
            'product model'                   => ['products', 'phpunit-model-x1', 'phpunit Tom &amp; Jerry'],
            'product sku, upper case'         => ['products', 'PHPUNIT-SKU-9', 'phpunit Tom &amp; Jerry'],
            'message title'                   => ['messages', 'PHPUNIT message', 'phpunit Message'],
            'message, plain quote'            => ['messages', "don't panic", 'phpunit Message'],
            'message, encoded quote'          => ['messages', 'Don&#039;t', 'phpunit Message'],
            'message, plain tag'              => ['messages', '<b>now</b>', 'phpunit Message'],
            'message, encoded tag'            => ['messages', '&lt;B&gt;NOW', 'phpunit Message'],
            'customer, plain quote'           => ["customers", "o'brien", 'phpunitJohn O&#039;Brien'],
            'customer, encoded quote'         => ['customers', 'O&#039;BRIEN', 'phpunitJohn O&#039;Brien'],
            'customer, first and last name'   => ['customers', 'PHPUNITJOHN o\'brien', 'phpunitJohn O&#039;Brien'],
            'customer, email'                 => ['customers', 'PHPUNIT_LOGIN@EXAMPLE', 'phpunitJohn O&#039;Brien'],
            'language value, plain char'      => ['languages', 'café', 'phpunit_key_text'],
            'language value, encoded'         => ['languages', 'Caf&eacute; menu', 'phpunit_key_text'],
            'language key with spaces'        => ['languages', 'PHPUNIT KEY TEXT', 'phpunit_key_text'],
        ];
    }

    #[DataProvider('textVariantsProvider')]
    public function testFindsTextInAnyVariant(string $category, string $keyword, string $title): void
    {
        $this->assertContains($title, $this->found($category, $keyword), $category . ': ' . $keyword);
    }

    public function testDoesNotFindOtherText(): void
    {
        foreach (['manufacturers', 'products', 'messages', 'customers', 'languages', 'seo_urls'] as $category) {
            $this->assertSame([], $this->found($category, 'phpunit-nothing-like-this'), $category);
        }
    }

    public function testListingBuildsTitleForEachRecordSeparately(): void
    {
        $this->db->query(
            "INSERT INTO " . $this->db->table('manufacturers') . "
             SET name = 'phpunit Second &amp; Brand', sort_order = 0"
        );
        $result = $this->model->getResult('manufacturers', 'phpunit')['result'];
        $this->assertCount(2, $result);
        foreach ($result as $row) {
            $this->assertStringContainsString('catalog/manufacturer/update', $row['href']);
            $this->assertStringContainsString('manufacturer_id=', $row['href']);
        }
        // the title attribute is built per record: text of the previous one must not be added to the next one
        foreach ($result as $row) {
            $this->assertSame(1, substr_count($row['text'], 'amp;amp;'), $row['text']);
        }
    }

    /**
     * Every section must produce a valid sql with plain, encoded and quoted needles
     */
    public function testAllSectionsRunWithoutErrors(): void
    {
        foreach (array_keys($this->model->results_controllers) as $category) {
            foreach (['phpunit', 'a&b', "o'brien", 'John Smith'] as $keyword) {
                foreach (['listing', 'suggest'] as $mode) {
                    $output = $this->model->getResult($category, $keyword, $mode);
                    $this->assertSame($category, $output['search_category']);
                    $this->assertIsArray($output['result'], $category . ' / ' . $keyword);
                }
            }
        }
    }
}
