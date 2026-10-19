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

use AContentManager;
use phpunit\AbanteCartTestBootstrap;
use Registry;
use stdClass;

/**
 * AContentManager without database: db, language and cache are replaced by fakes placed into the Registry.
 * Original registry values are restored after every test.
 */
class AContentManagerTest extends AbanteCartTestBootstrap
{
    private const KEYS = ['db', 'language', 'cache', 'config'];
    private const CONTENT_LANGUAGE_ID = 2;

    /** @var array original registry values */
    private array $backup = [];
    /** @var stdClass fake db. ->queries - executed sql, ->results - [regex => rows] */
    private stdClass $db;
    /** @var stdClass fake language. ->calls - recorded calls */
    private stdClass $language;
    /** @var stdClass fake cache. ->removed - removed keys */
    private stdClass $cache;

    protected function setUp(): void
    {
        if (!defined('ABC_TEST_ROOT_PATH')) {
            parent::bootstrap();
        }
        $this->registry = Registry::getInstance();
        foreach (self::KEYS as $key) {
            $this->backup[$key] = $this->registry->get($key);
        }

        $this->db = new class extends stdClass {
            public array $queries = [];
            public array $results = [];

            public function table($name)
            {
                return 'ac_' . $name;
            }

            public function escape($value)
            {
                return addslashes((string) $value);
            }

            public function stringOrNull($value)
            {
                return (string) $value !== '' ? "'" . $this->escape($value) . "'" : 'NULL';
            }

            public function getLastId()
            {
                return 15;
            }

            public function query($sql)
            {
                $this->queries[] = $sql;
                $rows = [];
                foreach ($this->results as $pattern => $result) {
                    if (preg_match($pattern, $sql)) {
                        $rows = $result;
                        break;
                    }
                }
                return (object) [
                    'rows'     => $rows,
                    'row'      => $rows[0] ?? [],
                    'num_rows' => count($rows),
                ];
            }

            /**
             * @param string $regex
             *
             * @return array sql statements matching the regex
             */
            public function find(string $regex): array
            {
                return array_values(array_filter($this->queries, fn($sql) => preg_match($regex, $sql)));
            }
        };

        $this->language = new class(self::CONTENT_LANGUAGE_ID) extends stdClass {
            public array $calls = [];

            public function __construct(private int $contentLanguageId)
            {
            }

            public function getContentLanguageID()
            {
                return $this->contentLanguageId;
            }

            public function getAvailableLanguages()
            {
                return [['language_id' => 1], ['language_id' => 2]];
            }

            public function get($key)
            {
                return $key;
            }

            public function replaceDescriptions($table, $index, $data)
            {
                $this->calls[] = ['replaceDescriptions', $table, $index, $data];
            }

            public function saveTags($table, $index, $languageId, $tags)
            {
                $this->calls[] = ['saveTags', $table, $index, $languageId, $tags];
            }

            /**
             * @param string $method
             * @param string|null $table
             *
             * @return array recorded calls of the method (optionally of the table)
             */
            public function calls(string $method, ?string $table = null): array
            {
                return array_values(
                    array_filter(
                        $this->calls,
                        fn($call) => $call[0] === $method && ($table === null || $call[1] === $table)
                    )
                );
            }
        };

        $this->cache = new class extends stdClass {
            public array $removed = [];

            public function remove($key)
            {
                $this->removed[] = $key;
            }
        };

        $this->registry->set('db', $this->db);
        $this->registry->set('language', $this->language);
        $this->registry->set('cache', $this->cache);
    }

    protected function tearDown(): void
    {
        foreach ($this->backup as $key => $value) {
            $this->registry->set($key, $value);
        }
    }

    private function manager(): AContentManager
    {
        return new AContentManager();
    }

    /**
     * @return array [language id => replaced columns] for given table
     */
    private function replacedData(string $table): array
    {
        $output = [];
        foreach ($this->language->calls('replaceDescriptions', $table) as $call) {
            foreach ($call[3] as $languageId => $columns) {
                $output[$languageId] = $columns;
            }
        }
        return $output;
    }

    // ---------------------------------------------------------------- editContent

    public function testEditContentReturnsFalseWithoutId(): void
    {
        $this->assertFalse($this->manager()->editContent(0, ['status' => 1]));
        $this->assertSame([], $this->db->queries);
    }

    public function testEditContentUpdatesOnlyPassedColumns(): void
    {
        $this->assertTrue($this->manager()->editContent(5, ['status' => 1, 'sort_order' => 7, 'author' => "O'Neil"]));

        $update = $this->db->find('/^UPDATE/');
        $this->assertCount(1, $update);
        $this->assertStringContainsString('status = 1', $update[0]);
        $this->assertStringContainsString('sort_order = 7', $update[0]);
        $this->assertStringContainsString("author = 'O\\'Neil'", $update[0]);
        $this->assertStringNotContainsString('parent_content_id', $update[0]);
        $this->assertStringContainsString("content_id = '5'", $update[0]);
    }

    /**
     * Regression: partial update must not wipe descriptions and SEO keyword of the content
     */
    public function testEditContentWithoutTextFieldsDoesNotTouchDescriptionsAndKeyword(): void
    {
        $this->manager()->editContent(5, ['status' => 1]);

        $this->assertSame([], $this->language->calls('replaceDescriptions'));
        $this->assertSame([], $this->language->calls('saveTags'));
        $this->assertSame([], $this->db->find('/url_aliases/'));
    }

    public function testEditContentSavesOnlyPassedTextFieldsForContentLanguage(): void
    {
        $this->manager()->editContent(5, ['title' => 'New title', 'content' => '<p>text</p>']);

        $this->assertSame(
            [self::CONTENT_LANGUAGE_ID => ['title' => 'New title', 'content' => '<p>text</p>']],
            $this->replacedData('content_descriptions')
        );
        // keyword was not passed - alias stays as is
        $this->assertSame([], $this->replacedData('url_aliases'));
        $this->assertSame([], $this->db->find('/DELETE/'));
    }

    public function testEditContentWithEmptyKeywordRemovesAlias(): void
    {
        $this->manager()->editContent(5, ['keyword' => '']);

        $delete = $this->db->find('/^\s*DELETE/');
        $this->assertCount(1, $delete);
        $this->assertStringContainsString("query = 'content_id=5'", $delete[0]);
        $this->assertStringContainsString("language_id = '" . self::CONTENT_LANGUAGE_ID . "'", $delete[0]);
    }

    public function testEditContentSavesKeywordAndTags(): void
    {
        $this->manager()->editContent(5, ['keyword' => 'about us', 'tags' => 'a,b']);

        $this->assertSame(
            [self::CONTENT_LANGUAGE_ID => ['keyword' => 'about' . SEO_URL_SEPARATOR . 'us']],
            $this->replacedData('url_aliases')
        );
        $tags = $this->language->calls('saveTags');
        $this->assertCount(1, $tags);
        $this->assertSame(['content_id' => 5], $tags[0][2]);
        $this->assertSame(self::CONTENT_LANGUAGE_ID, $tags[0][3]);
        $this->assertSame('a,b', $tags[0][4]);
    }

    public function testEditContentWithLanguagesSavesEachLanguage(): void
    {
        $this->manager()->editContent(
            5,
            [
                'languages' => [
                    1 => ['title' => 'One', 'keyword' => 'one'],
                    2 => ['title' => 'Two', 'keyword' => 'two'],
                ],
            ]
        );

        $this->assertSame(
            [1 => ['title' => 'One'], 2 => ['title' => 'Two']],
            $this->replacedData('content_descriptions')
        );
        $this->assertSame(
            [1 => ['keyword' => 'one'], 2 => ['keyword' => 'two']],
            $this->replacedData('url_aliases')
        );
    }

    public function testEditContentReplacesStoresOnlyWhenPassed(): void
    {
        $this->manager()->editContent(5, ['status' => 1]);
        $this->assertSame([], $this->db->find('/contents_to_stores/'));

        $this->manager()->editContent(5, ['store_id' => [0, 2]]);
        $this->assertCount(1, $this->db->find('/^\s*DELETE.*contents_to_stores/s'));
        $this->assertCount(2, $this->db->find('/^\s*INSERT.*contents_to_stores/s'));
    }

    public function testEditContentClearsCache(): void
    {
        $this->manager()->editContent(5, ['status' => 1]);
        $this->assertSame([['content', 'storefront_menu']], $this->cache->removed);
    }

    // ----------------------------------------------------------------- addContent

    public function testAddContentRejectsEmptyData(): void
    {
        $this->assertFalse($this->manager()->addContent([]));
        $this->assertSame([], $this->db->queries);
    }

    public function testAddContentInsertsRowAndReturnsId(): void
    {
        $id = $this->manager()->addContent(
            [
                'parent_content_id' => '3',
                'sort_order'        => '4',
                'status'            => '1',
                'author'            => 'Jane',
                'publish_date'      => '',
                'store_id'          => [0],
                'title'             => 'Hello',
            ]
        );

        $this->assertSame(15, $id);
        $insert = $this->db->find('/^\s*INSERT INTO ac_contents\s/');
        $this->assertCount(1, $insert);
        $this->assertStringContainsString("parent_content_id = '3'", $insert[0]);
        $this->assertStringContainsString("author = 'Jane'", $insert[0]);
        $this->assertStringContainsString('publish_date = NULL', $insert[0]);
        $this->assertSame([['content', 'storefront_menu']], $this->cache->removed);
    }

    public function testAddContentSavesAllLanguagesAndGeneratesKeywordFromTitle(): void
    {
        $this->manager()->addContent(['title' => 'My News', 'content' => 'body', 'store_id' => [0]]);

        $descriptions = $this->replacedData('content_descriptions');
        $this->assertSame(['title' => 'My News', 'content' => 'body'], array_intersect_key(
            $descriptions[1],
            ['title' => 1, 'content' => 1]
        ));
        $this->assertSame([1, 2], array_keys($descriptions));

        $aliases = $this->replacedData('url_aliases');
        $this->assertSame([1, 2], array_keys($aliases));
        $this->assertSame('my' . SEO_URL_SEPARATOR . 'news', $aliases[1]['keyword']);
    }

    public function testAddContentUsesGivenKeywordInsteadOfTitle(): void
    {
        $this->manager()->addContent(['title' => 'My News', 'keyword' => 'custom', 'store_id' => [0]]);

        $this->assertSame('custom', $this->replacedData('url_aliases')[1]['keyword']);
    }

    public function testAddContentWithLanguagesDoesNotDuplicateToOtherLanguages(): void
    {
        $this->manager()->addContent(
            ['languages' => [2 => ['title' => 'Only two', 'keyword' => 'only-two']], 'store_id' => [0]]
        );

        $this->assertSame([2], array_keys($this->replacedData('content_descriptions')));
    }

    public function testAddContentAcceptsScalarStoreId(): void
    {
        $this->manager()->addContent(['title' => 'x', 'store_id' => 3]);

        $insert = $this->db->find('/^\s*INSERT.*contents_to_stores/s');
        $this->assertCount(1, $insert);
        $this->assertStringContainsString("'15','3'", $insert[0]);
    }

    // ------------------------------------------------------------ editContentField

    public function testEditContentFieldIntColumnIsCasted(): void
    {
        $this->assertTrue($this->manager()->editContentField(5, 'status', '1; DROP TABLE x'));

        $update = $this->db->find('/^\s*UPDATE/');
        $this->assertCount(1, $update);
        $this->assertStringContainsString("`status`= '1'", $update[0]);
    }

    public function testEditContentFieldTextColumnGoesToDescriptionsOfContentLanguage(): void
    {
        $this->manager()->editContentField(5, 'title', 'Hi');

        $this->assertSame([self::CONTENT_LANGUAGE_ID => ['title' => 'Hi']], $this->replacedData('content_descriptions'));
        $this->assertSame([], $this->db->find('/^\s*UPDATE/'));
    }

    public function testEditContentFieldIgnoresUnknownColumns(): void
    {
        $this->manager()->editContentField(5, 'status` = 1, `author', 'x');

        $this->assertSame([], $this->db->queries);
    }

    public function testEditContentFieldReturnsFalseWithoutId(): void
    {
        $this->assertFalse($this->manager()->editContentField(0, 'status', 1));
    }

    // -------------------------------------------------------------------- getters

    public function testGetContentReturnsFalseWithoutId(): void
    {
        $this->assertFalse($this->manager()->getContent(0));
    }

    public function testGetContentAddsKeywordAndTags(): void
    {
        $this->db->results = [
            '/FROM ac_contents i/'    => [['content_id' => 5, 'title' => 'T']],
            '/FROM ac_url_aliases/'   => [['keyword' => 'my-t']],
            '/FROM ac_content_tags/'  => [
                ['language_id' => 2, 'tag' => 'a'],
                ['language_id' => 2, 'tag' => 'b'],
            ],
        ];

        $content = $this->manager()->getContent(5, 2);

        $this->assertSame('T', $content['title']);
        $this->assertSame('my-t', $content['keyword']);
        $this->assertSame('a,b', $content['tags']);
    }

    public function testGetContentOfMissingContentIsEmptyArray(): void
    {
        $this->assertSame([], $this->manager()->getContent(5, 2));
    }

    public function testGetContentTagsGroupsTagsByLanguage(): void
    {
        $this->db->results = [
            '/content_tags/' => [
                ['language_id' => 1, 'tag' => 'x'],
                ['language_id' => 2, 'tag' => 'a'],
                ['language_id' => 2, 'tag' => 'b'],
            ],
        ];

        $this->assertSame([1 => 'x', 2 => 'a,b'], $this->manager()->getContentTags(5));
        $this->assertSame('a,b', $this->manager()->getContentTags(5, 2));
    }

    public function testIsParent(): void
    {
        $this->assertFalse($this->manager()->isParent(5));

        $this->db->results = ['/parent_content_id/' => [['content_id' => 6]]];
        $this->assertTrue($this->manager()->isParent(5));
    }

    public function testGetLeafContents(): void
    {
        $this->db->results = ['/IS NULL/' => [['content_id' => 4], ['content_id' => 9]]];

        $this->assertSame([4 => 4, 9 => 9], $this->manager()->getLeafContents());
    }

    // ------------------------------------------------------------ buildContentTree

    private static function contents(): array
    {
        return [
            1 => ['content_id' => 1, 'parent_content_id' => 0, 'title' => 'A', 'status' => 1],
            2 => ['content_id' => 2, 'parent_content_id' => 1, 'title' => 'B', 'status' => 1],
            3 => ['content_id' => 3, 'parent_content_id' => 2, 'title' => 'C', 'status' => 0],
            4 => ['content_id' => 4, 'parent_content_id' => 0, 'title' => 'D', 'status' => 1],
        ];
    }

    public function testBuildContentTreeIsFlatListWithChildrenAndIndent(): void
    {
        $tree = $this->manager()->buildContentTree(self::contents(), 0, 0);

        $this->assertSame([1, 2, 3, 4], array_keys($tree));
        $this->assertSame([2, 3], $tree[1]['children']);
        $this->assertSame([3], $tree[2]['children']);
        $this->assertSame([], $tree[4]['children']);
        $this->assertSame('A', $tree[1]['title']);
        $this->assertSame('&nbsp;&nbsp;&nbsp;&nbsp;B', $tree[2]['title']);
        $this->assertSame(str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', 2) . 'C', $tree[3]['title']);
    }

    public function testBuildContentTreeSkipsDisabledWhenRequested(): void
    {
        $tree = $this->manager()->buildContentTree(self::contents(), 0, 0, true);

        $this->assertSame([1, 2, 4], array_keys($tree));
        $this->assertSame([2], $tree[1]['children']);
        $this->assertSame([], $tree[2]['children']);
    }

    public function testGetContentsForSelectStartsWithTopLevel(): void
    {
        $this->registry->set('config', new class {
            public function get($key)
            {
                return 0;
            }

            public function has($key)
            {
                return true;
            }
        });
        $this->db->results = [
            '/FROM ac_contents i/' => array_values(self::contents()),
        ];

        $list = $this->manager()->getContentsForSelect();

        $this->assertSame(0, array_key_first($list));
        $this->assertSame([], $list[0]['children']);
        $this->assertSame([1, 2, 3, 4], array_slice(array_keys($list), 1));
    }
}
