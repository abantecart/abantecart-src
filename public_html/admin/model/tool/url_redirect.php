<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ModelToolURLRedirect extends Model
{
    public const MATCH_TYPES = ['exact', 'prefix', 'regex', 'query'];
    public const ACTIONS = [301, 302, 403, 404, 410];
    /** the storefront model reads active rules from this cache key */
    public const CACHE_KEY = 'url_redirects';

    private const FIELDS = [
        'source',
        'source_host',
        'source_http_headers',
        'match_type',
        'target',
        'action',
        'keep_query',
        'priority',
        'status',
        'comment',
    ];

    /**
     * @param int $id
     *
     * @return array
     * @throws AException
     */
    public function getRedirect(int $id): array
    {
        $result = $this->db->query(
            "SELECT id, source, source_host, source_http_headers, match_type, target, action, keep_query,
                    priority, status, hits, last_hit, comment
             FROM " . $this->db->table('url_redirects') . "
             WHERE id = " . $id
        );
        return $result->row;
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws AException
     */
    public function getRedirects(array $data = []): array
    {
        $start = max(0, (int)($data['start'] ?? 0));
        $limit = max(1, (int)($data['limit'] ?? 20));
        return $this->db->query(
            $this->buildListSql($data)
            . " LIMIT " . $start . ", " . $limit
        )->rows;
    }

    /**
     * @param array $data
     *
     * @return int
     * @throws AException
     */
    public function getTotalRedirects(array $data = []): int
    {
        $result = $this->db->query(
            "SELECT COUNT(*) AS total
             FROM " . $this->db->table('url_redirects')
            . $this->buildWhereSql($data)
        );
        return (int)$result->row['total'];
    }

    /**
     * @param array $data
     *
     * @return int
     * @throws AException
     */
    public function addRedirect(array $data): int
    {
        $data = array_merge(
            [
                'source_host' => '',
                'source_http_headers' => '',
                'match_type'  => 'exact',
                'target'      => '',
                'action'      => 301,
                'keep_query'  => 0,
                'priority'    => 0,
                'status'      => 1,
                'comment'     => '',
            ],
            $data
        );
        $this->db->query(
            "INSERT INTO " . $this->db->table('url_redirects') . "
             SET " . $this->buildSetSql($this->prepareData($data))
        );
        $this->cache->remove(self::CACHE_KEY);
        return (int)$this->db->getLastId();
    }

    /**
     * @param int $id
     * @param array $data
     *
     * @return void
     * @throws AException
     */
    public function updateRedirect(int $id, array $data): void
    {
        $data = $this->prepareData($data);
        if (!$data) {
            return;
        }
        $this->db->query(
            "UPDATE " . $this->db->table('url_redirects') . "
             SET " . $this->buildSetSql($data) . "
             WHERE id = " . $id
        );
        $this->cache->remove(self::CACHE_KEY);
    }

    /**
     * @param int $id
     *
     * @return void
     * @throws AException
     */
    public function deleteRedirect(int $id): void
    {
        $this->db->query(
            "DELETE FROM " . $this->db->table('url_redirects') . "
             WHERE id = " . $id
        );
        $this->cache->remove(self::CACHE_KEY);
    }

    /**
     * @param array $data full record
     * @param int $id
     *
     * @return array errors keyed by field name
     * @throws AException
     */
    public function validateRedirect(array $data, int $id = 0): array
    {
        $errors = [];
        $data = $this->prepareData($data);
        $source = $data['source'] ?? '';
        $matchType = $data['match_type'] ?? '';
        $action = (int)($data['action'] ?? 0);

        if (!in_array($matchType, self::MATCH_TYPES, true)) {
            $errors['match_type'] = $this->language->get('error_match_type');
        }
        if (!in_array($action, self::ACTIONS, true)) {
            $errors['action'] = $this->language->get('error_action');
        }
        $headers = $data['source_http_headers'] ?? '';
        if ($source === '' && ($data['source_host'] ?? '') === '' && $headers === '') {
            // at least one of sources, source_host and source_http_headers is required
            $errors['source'] = $this->language->get('error_source');
            $errors['source_host'] = $this->language->get('error_source');
            $errors['source_http_headers'] = $this->language->get('error_source');
        } elseif (mb_strlen($source) > 2048) {
            $errors['source'] = $this->language->get('error_source');
        } elseif ($source === '') {
            // restricted by source_host only
        } elseif ($matchType === 'regex' && @preg_match(AUrlRedirect::regexPattern($source), '') === false) {
            $errors['source'] = $this->language->get('error_regex');
        } elseif ($matchType === 'query') {
            parse_str($source, $params);
            if (!$params) {
                $errors['source'] = $this->language->get('error_query');
            }
        }
        if ($headers !== '' && (mb_strlen($headers) > 2048 || AUrlRedirect::parseHeaders($headers) === null)) {
            $errors['source_http_headers'] = $this->language->get('error_http_headers');
        }
        $target = $data['target'] ?? '';
        if (in_array($action, AUrlRedirect::REDIRECT_ACTIONS, true) && $target === '') {
            $errors['target'] = $this->language->get('error_target');
        }
        if (mb_strlen($target) > 2048) {
            $errors['target'] = $this->language->get('error_target_length');
        }

        if (!$errors) {
            $result = $this->db->query(
                "SELECT id
                 FROM " . $this->db->table('url_redirects') . "
                 WHERE source = '" . $this->db->escape($source) . "'
                   AND source_host = '" . $this->db->escape($data['source_host'] ?? '') . "'
                   AND source_http_headers = '" . $this->db->escape($headers) . "'
                   AND match_type = '" . $this->db->escape($matchType) . "'
                   AND id != " . $id
            );
            if ($result->num_rows) {
                $errors['source'] = $this->language->get('error_duplicate');
            }
        }
        return $errors;
    }

    /**
     * Replaces the seo keyword in redirect targets when the keyword was renamed.
     * Only targets which point exactly to the keyword are changed,
     * i.e. the keyword is the last segment of the target path.
     *
     * @param string $oldKeyword
     * @param string $newKeyword
     *
     * @return int number of updated rules
     * @throws AException
     */
    public function replaceTargetKeyword(string $oldKeyword, string $newKeyword): int
    {
        $newKeyword = mb_strtolower($newKeyword);
        if ($oldKeyword === '' || $oldKeyword === $newKeyword) {
            return 0;
        }
        $result = $this->db->query(
            "SELECT id, target
             FROM " . $this->db->table('url_redirects') . "
             WHERE target LIKE '%" . $this->db->escape($oldKeyword, true) . "%'"
        );
        $updated = 0;
        foreach ($result->rows as $row) {
            $target = $this->replaceLastPathSegment($row['target'], $oldKeyword, $newKeyword);
            if ($target === null) {
                continue;
            }
            $this->db->query(
                "UPDATE " . $this->db->table('url_redirects') . "
                 SET target = '" . $this->db->escape($target) . "'
                 WHERE id = " . (int)$row['id']
            );
            $updated++;
        }
        if ($updated) {
            $this->cache->remove(self::CACHE_KEY);
        }
        return $updated;
    }

    /**
     * @param string $target
     * @param string $old
     * @param string $new
     *
     * @return string|null null when target does not point to the old keyword
     */
    private function replaceLastPathSegment(string $target, string $old, string $new): ?string
    {
        // split off "?query" and "#fragment" to keep them untouched
        $tailPos = strcspn($target, '?#');
        $path = substr($target, 0, $tailPos);
        $tail = substr($target, $tailPos);
        $trimmedPath = rtrim($path, '/');
        $trailingSlash = substr($path, strlen($trimmedPath));

        $slashPos = strrpos($trimmedPath, '/');
        $head = $slashPos === false ? '' : substr($trimmedPath, 0, $slashPos + 1);
        $segment = $slashPos === false ? $trimmedPath : substr($trimmedPath, $slashPos + 1);
        // "https://old-keyword" is a host, not a keyword
        if (str_ends_with($head, '//') || rawurldecode($segment) !== $old && mb_strtolower(rawurldecode($segment)) !== mb_strtolower($old)) {
            return null;
        }
        return $head . $new . $trailingSlash . $tail;
    }

    /**
     * Keeps known fields only and normalizes their values.
     *
     * @param array $data
     *
     * @return array
     */
    private function prepareData(array $data): array
    {
        $data = array_intersect_key($data, array_flip(self::FIELDS));
        foreach (['source', 'source_host', 'source_http_headers', 'target', 'match_type', 'comment'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = trim((string)$data[$field]);
            }
        }
        foreach (['action', 'priority'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = (int)$data[$field];
            }
        }
        foreach (['keep_query', 'status'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = (int)(bool)$data[$field];
            }
        }
        if (isset($data['source_host'])) {
            $host = mb_strtolower($data['source_host']);
            $host = preg_replace('~^[a-z][a-z0-9+.-]*://~', '', $host);
            // "[::1]:8080" or "example.com:8080/path" -> host only
            if (preg_match('~^(\[[^]]+]|[^:/]+)~', $host, $matches)) {
                $host = $matches[1];
            }
            $data['source_host'] = $host;
        }
        if (isset($data['source_http_headers'])) {
            // one "Name: value" per line, normalized line breaks
            $pairs = AUrlRedirect::parseHeaders($data['source_http_headers']);
            if ($pairs !== null) {
                $data['source_http_headers'] = implode("\n", array_map(fn($pair) => $pair[0] . ': ' . $pair[1], $pairs));
            }
        }
        if (isset($data['source'], $data['match_type'])) {
            $data['source'] = $this->normalizeSource($data['source'], $data['match_type']);
        }
        return $data;
    }

    /**
     * @param string $source
     * @param string $matchType
     *
     * @return string
     */
    private function normalizeSource(string $source, string $matchType): string
    {
        if ($matchType === 'query') {
            return ltrim($source, '?&');
        }
        if ($matchType === 'regex') {
            return $source;
        }
        // seo keyword "old-product" and path "/old-product/" are the same url.
        // Keywords are stored in lower case, so is the source
        $source = '/' . mb_strtolower(trim($source, '/'));
        return $source === '/' ? '' : $source;
    }

    /**
     * @param array $data prepared data
     *
     * @return string
     * @throws AException
     */
    private function buildSetSql(array $data): string
    {
        $set = [];
        foreach ($data as $field => $value) {
            $set[] = '`' . $field . "` = " . (is_int($value) ? $value : "'" . $this->db->escape($value) . "'");
        }
        return implode(', ', $set);
    }

    /**
     * @param array $data
     *
     * @return string
     */
    private function buildListSql(array $data): string
    {
        $allowedSort = ['id', 'source', 'action', 'status', 'hits', 'last_hit', 'priority'];
        $sort = in_array($data['sort'] ?? '', $allowedSort, true) ? $data['sort'] : 'priority';
        $order = ($data['order'] ?? '') === 'DESC' ? 'DESC' : 'ASC';
        return "SELECT id, source, source_host, source_http_headers, match_type, target, action, keep_query,
                       priority, status, hits, last_hit, comment
                FROM " . $this->db->table('url_redirects')
            . $this->buildWhereSql($data)
            . " ORDER BY `" . $sort . "` " . $order . ", id ASC";
    }

    /**
     * @param array $data
     *
     * @return string
     */
    private function buildWhereSql(array $data): string
    {
        $filter = $data['subsql_filter'] ?? '';
        return $filter ? ' WHERE ' . $filter : '';
    }
}
