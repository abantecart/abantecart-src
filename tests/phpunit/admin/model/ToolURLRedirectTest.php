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
use ModelToolURLRedirect;
use phpunit\AbanteCartTestBootstrap;
use Registry;

/**
 * Admin model of redirect rules. Test records contain "phpunit" in the source.
 *
 * @property ADB $db
 * @property ALoader $load
 */
class ToolURLRedirectTest extends AbanteCartTestBootstrap
{
    /** @var ModelToolURLRedirect */
    private $model;

    protected function setUp(): void
    {
        // constants can be defined only once per process
        if (!defined('ABC_TEST_ROOT_PATH')) {
            parent::bootstrap();
        } else {
            $this->registry = Registry::getInstance();
        }
        $this->model = $this->load->model('tool/url_redirect');
    }

    protected function tearDown(): void
    {
        $this->db->query(
            "DELETE FROM " . $this->db->table('url_redirects') . "
             WHERE source LIKE '%phpunit%'"
        );
    }

    private function add(array $data): int
    {
        return $this->model->addRedirect(
            array_merge(['source' => '/phpunit-old', 'target' => '/phpunit-new'], $data)
        );
    }

    public function testAddNormalizesSourceAndHost(): void
    {
        $id = $this->add([
            'source'      => ' phpunit-old/ ',
            'source_host' => 'HTTPS://Shop.Example.com:8080/path',
            'keep_query'  => 'on',
        ]);
        $row = $this->model->getRedirect($id);
        $this->assertSame('/phpunit-old', $row['source']);
        $this->assertSame('shop.example.com', $row['source_host']);
        $this->assertSame('exact', $row['match_type']);
        $this->assertEquals(301, $row['action']);
        $this->assertEquals(1, $row['keep_query']);
        $this->assertEquals(1, $row['status']);
        $this->assertEquals(0, $row['hits']);

        $id = $this->add(['source' => '?rt=phpunit/page&id=1', 'match_type' => 'query']);
        $this->assertSame('rt=phpunit/page&id=1', $this->model->getRedirect($id)['source']);

        $id = $this->add(['source' => '^/phpunit/(\d+)/$', 'match_type' => 'regex']);
        $this->assertSame('^/phpunit/(\d+)/$', $this->model->getRedirect($id)['source']);
    }

    public function testUpdateAndDelete(): void
    {
        $id = $this->add([]);
        $this->model->updateRedirect($id, ['status' => 0, 'action' => 410, 'hits' => 100]);
        $row = $this->model->getRedirect($id);
        $this->assertEquals(0, $row['status']);
        $this->assertEquals(410, $row['action']);
        // counter is not editable
        $this->assertEquals(0, $row['hits']);

        $this->model->deleteRedirect($id);
        $this->assertSame([], $this->model->getRedirect($id));
    }

    /**
     * @dataProvider invalidProvider
     */
    public function testValidationErrors(array $data, string $field): void
    {
        $data = array_merge(
            ['source' => '/phpunit-valid', 'match_type' => 'exact', 'target' => '/new', 'action' => 301],
            $data
        );
        $this->assertArrayHasKey($field, $this->model->validateRedirect($data));
    }

    public static function invalidProvider(): array
    {
        return [
            'empty source'       => [['source' => ''], 'source'],
            'root source'        => [['source' => '/'], 'source'],
            'invalid regex'      => [['source' => '^/(phpunit', 'match_type' => 'regex'], 'source'],
            'query without pair' => [['source' => '&&', 'match_type' => 'query'], 'source'],
            'unknown match type' => [['match_type' => 'fuzzy'], 'match_type'],
            'unknown action'     => [['action' => 307], 'action'],
            'no target for 301'  => [['target' => ''], 'target'],
            'no target for 302'  => [['target' => '', 'action' => 302], 'target'],
        ];
    }

    public function testValidRules(): void
    {
        $valid = [
            ['source' => '/phpunit-a', 'match_type' => 'exact', 'target' => '/b', 'action' => 301],
            ['source' => 'phpunit-keyword', 'match_type' => 'exact', 'target' => '/b', 'action' => 302],
            ['source' => '/phpunit', 'match_type' => 'prefix', 'target' => '/b', 'action' => 301],
            ['source' => '^/phpunit/(\d+)$', 'match_type' => 'regex', 'target' => '/b/$1', 'action' => 301],
            ['source' => 'rt=phpunit/page', 'match_type' => 'query', 'target' => '/b', 'action' => 301],
            // target is not needed for blocking actions
            ['source' => '/phpunit-gone', 'match_type' => 'exact', 'target' => '', 'action' => 410],
        ];
        foreach ($valid as $data) {
            $this->assertSame([], $this->model->validateRedirect($data), $data['source']);
        }
    }

    public function testSourceOrSourceHostRequired(): void
    {
        $data = ['source' => '', 'source_host' => '', 'match_type' => 'exact', 'target' => '/x', 'action' => 301];
        $errors = $this->model->validateRedirect($data);
        $this->assertArrayHasKey('source', $errors);
        $this->assertArrayHasKey('source_host', $errors);
        $this->assertSame([], $this->model->validateRedirect(['source_host' => '203.0.113.7'] + $data));
        $this->assertSame([], $this->model->validateRedirect(['source' => '/phpunit-a'] + $data));
    }

    public function testSourceHttpHeadersValidation(): void
    {
        $data = ['source' => '', 'source_host' => '', 'match_type' => 'exact', 'target' => '/x', 'action' => 301];
        // headers alone are enough
        $this->assertSame([], $this->model->validateRedirect(['source_http_headers' => "User-Agent: bot\nAccept-Language: ru"] + $data));
        $this->assertSame([], $this->model->validateRedirect(['source_http_headers' => 'Accept-Language:'] + $data));
        foreach (['no colon', 'Bad Name: value', ': value', str_repeat('X-A: a' . "\n", 400)] as $invalid) {
            $errors = $this->model->validateRedirect(['source' => '/phpunit-a', 'source_http_headers' => $invalid] + $data);
            $this->assertArrayHasKey('source_http_headers', $errors, $invalid);
        }
        // empty headers with other fields are fine
        $this->assertSame([], $this->model->validateRedirect(['source' => '/phpunit-a', 'source_http_headers' => ''] + $data));
        $this->assertArrayHasKey('source_http_headers', $this->model->validateRedirect($data));
    }

    public function testSourceHttpHeadersNormalizedAndSaved(): void
    {
        $id = $this->add(['source_http_headers' => " User-Agent :  Googlebot \r\n\r\nAccept-Language: ru\r\n"]);
        $this->assertSame(
            "User-Agent: Googlebot\nAccept-Language: ru",
            $this->model->getRedirect($id)['source_http_headers']
        );
        $this->assertSame('', $this->model->getRedirect($this->add(['source' => '/phpunit-b']))['source_http_headers']);
    }

    public function testDuplicateConsidersHttpHeaders(): void
    {
        $this->add(['source' => '/phpunit-dup2', 'source_http_headers' => 'User-Agent: bot']);
        $data = ['source' => '/phpunit-dup2', 'match_type' => 'exact', 'target' => '/x', 'action' => 301];
        $this->assertArrayHasKey(
            'source',
            $this->model->validateRedirect($data + ['source_http_headers' => 'user-agent:  bot'])
        );
        $this->assertSame([], $this->model->validateRedirect($data + ['source_http_headers' => 'User-Agent: other']));
        $this->assertSame([], $this->model->validateRedirect($data));
    }

    public function testDuplicate(): void
    {
        $id = $this->add(['source' => '/phpunit-dup']);
        $data = ['source' => 'phpunit-dup/', 'match_type' => 'exact', 'target' => '/x', 'action' => 301];
        $this->assertArrayHasKey('source', $this->model->validateRedirect($data));
        // the rule itself is not a duplicate
        $this->assertSame([], $this->model->validateRedirect($data, $id));
        // same source for another referer/ip or match type is allowed
        $this->assertSame([], $this->model->validateRedirect($data + ['source_host' => 'other.test']));
        $this->assertSame([], $this->model->validateRedirect($data + ['source_host' => '203.0.113.7']));
        $this->assertSame([], $this->model->validateRedirect(['match_type' => 'prefix'] + $data));
    }

    public function testReplaceTargetKeyword(): void
    {
        $targets = [
            'phpunit-kw'                       => 'phpunit-new-kw',
            '/phpunit-kw'                      => '/phpunit-new-kw',
            '/phpunit-kw/'                     => '/phpunit-new-kw/',
            '/catalog/phpunit-kw?page=2#top'   => '/catalog/phpunit-new-kw?page=2#top',
            'https://shop.test/phpunit-kw?a=1' => 'https://shop.test/phpunit-new-kw?a=1',
            // not the keyword itself
            '/phpunit-kw-more'                 => '/phpunit-kw-more',
            '/phpunit-kw/sub'                  => '/phpunit-kw/sub',
            'https://phpunit-kw'               => 'https://phpunit-kw',
        ];
        $ids = [];
        $i = 0;
        foreach ($targets as $target => $expected) {
            $ids[$target] = $this->add(['source' => '/phpunit-src-' . $i++, 'target' => $target]);
        }

        $this->assertSame(5, $this->model->replaceTargetKeyword('phpunit-kw', 'phpunit-new-kw'));
        foreach ($targets as $target => $expected) {
            $this->assertSame($expected, $this->model->getRedirect($ids[$target])['target'], $target);
        }
        $this->assertSame(0, $this->model->replaceTargetKeyword('phpunit-kw', 'phpunit-kw'));
    }
}
