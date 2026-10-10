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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ModelToolSeoUrl extends Model
{
    private const ROUTES = [
        'product_id'      => 'pages/product/product',
        'category_id'     => 'pages/product/category',
        'manufacturer_id' => 'pages/product/manufacturer',
        'content_id'      => 'pages/content/content',
        'collection_id'   => 'pages/product/collection',
        'check_seo'       => 'pages/index/check_seo',
    ];

    /** storefront controllers can be called only from these sections */
    private const ROUTE_PREFIXES = ['pages/', 'responses/'];

    /**
     * @param int $urlAliasId
     *
     * @return array
     * @throws AException
     */
    public function getSeoUrl(int $urlAliasId): array
    {
        $result = $this->db->query(
            "SELECT url_alias_id, keyword, query, language_id
             FROM " . $this->db->table('url_aliases') . "
             WHERE url_alias_id = '" . $urlAliasId . "'"
        );

        return $result->row ? $this->prepareRow($result->row) : [];
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws AException
     */
    public function getSeoUrls(array $data = []): array
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
    public function getTotalSeoUrls(array $data = []): int
    {
        $result = $this->db->query(
            "SELECT COUNT(*) AS total 
             FROM (" . $this->buildBaseSql() . ") AS seo_urls"
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
    public function addSeoUrl(array $data): int
    {
        $this->db->query(
            "INSERT INTO " . $this->db->table('url_aliases') . "
             SET keyword = '" . $this->db->escape($data['seo_keyword']) . "',
                 query = '" . $this->db->escape($this->buildStoredQuery($data)) . "',
                 language_id = " . $this->getLanguageId($data)
        );
        return (int)$this->db->getLastId();
    }

    /**
     * @param int $urlAliasId
     * @param array $data
     *
     * @return void
     * @throws AException
     */
    public function updateSeoUrl(int $urlAliasId, array $data): void
    {
        $current = $this->getSeoUrl($urlAliasId);
        if (!$current) {
            return;
        }
        $data = array_merge($current, $data);
        $data['language_id'] = $this->getLanguageId($data);
        $this->db->query(
            "UPDATE " . $this->db->table('url_aliases') . "
             SET keyword = '" . $this->db->escape($data['seo_keyword']) . "',
                 query = '" . $this->db->escape($this->buildStoredQuery($data)) . "',
                 language_id = " . $data['language_id'] . "
             WHERE url_alias_id = " . $urlAliasId
        );
    }

    /**
     * @param int $urlAliasId
     *
     * @return void
     * @throws AException
     */
    public function deleteSeoUrl(int $urlAliasId): void
    {
        $this->db->query(
            "DELETE FROM " . $this->db->table('url_aliases') . "
             WHERE url_alias_id = " . $urlAliasId
        );
    }

    /**
     * @param array $data
     * @param int $urlAliasId
     *
     * @return array
     * @throws AException
     */
    public function validateSeoUrl(array $data, int $urlAliasId = 0): array
    {
        $errors = [];
        $keyword = trim((string)($data['seo_keyword'] ?? ''));
        $route = trim((string)($data['route'] ?? ''));
        $query = trim((string)($data['query'] ?? ''));
        $storedQuery = $this->buildStoredQuery($data);
        $languageId = $this->getLanguageId($data);

        if ($keyword === '' || mb_strlen($keyword) > 255) {
            $errors['seo_keyword'] = $this->language->get('error_seo_keyword');
        }
        // at least one of them is needed to know which page to open
        if ($route === '' && $query === '') {
            $errors['route'] = $this->language->get('error_route_or_query');
        }
        if ($route !== '' && !$this->hasAllowedPrefix($route)) {
            $errors['route'] = $this->language->get('error_route_prefix');
        }
        if (mb_strlen($storedQuery) > 2048) {
            $errors['query'] = $this->language->get('error_query_length');
        }

        if (!$errors) {
            $result = $this->db->query(
                "SELECT url_alias_id, keyword, query
                 FROM " . $this->db->table('url_aliases') . "
                 WHERE language_id = '" . $languageId . "'
                   AND (keyword = '" . $this->db->escape($keyword) . "'
                        OR query = '" . $this->db->escape($storedQuery) . "')
                   AND url_alias_id != '" . $urlAliasId . "'"
            );
            foreach ($result->rows as $row) {
                // keyword is unique regardless of letter case (see table collation),
                // query is unique by its exact md5 hash
                if (mb_strtolower($row['keyword']) === mb_strtolower($keyword)) {
                    $errors['seo_keyword'] = $this->language->get('error_seo_keyword_exists');
                }
                if ($row['query'] === $storedQuery) {
                    $errors['query'] = $this->language->get('error_query_exists');
                }
            }
        }
        return $errors;
    }

    /**
     * @param string $route
     *
     * @return bool
     */
    private function hasAllowedPrefix(string $route): bool
    {
        $route = ltrim($route, '/');
        foreach (self::ROUTE_PREFIXES as $prefix) {
            // prefix alone, e.g. "pages/", is not a route yet
            if (str_starts_with($route, $prefix) && strlen($route) > strlen($prefix)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $data
     *
     * @return string
     */
    private function buildListSql(array $data): string
    {
        $allowedSort = ['seo_keyword', 'route', 'query'];
        $sort = in_array($data['sort'] ?? '', $allowedSort, true) ? $data['sort'] : 'seo_keyword';
        $order = ($data['order'] ?? '') === 'DESC' ? 'DESC' : 'ASC';
        return 'SELECT * FROM (' . $this->buildBaseSql() . ') AS seo_urls'
            . $this->buildWhereSql($data)
            . ' ORDER BY `' . $sort . '` ' . $order;
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

    /**
     * @return string
     */
    private function buildBaseSql(): string
    {
        $routeCases = [];
        foreach (self::ROUTES as $key => $route) {
            // "_" is a wildcard in LIKE, so escape it to match the key literally
            $likeKey = str_replace('_', '\\_', $key);
            $routeCases[] = "WHEN `query` LIKE '" . $likeKey . "=%' THEN '" . $route . "'";
        }
        return "SELECT url_alias_id,
                       keyword AS seo_keyword,
                       CASE " . implode(' ', $routeCases) . "
                           WHEN `query` LIKE 'rt=%'
                           THEN SUBSTRING_INDEX(SUBSTRING_INDEX(`query`, '&', 1), '=', -1)
                           ELSE ''
                       END AS route,
                       CASE WHEN `query` LIKE 'rt=%'
                           THEN IF(LOCATE('&', `query`), SUBSTRING(`query`, LOCATE('&', `query`) + 1), '')
                           ELSE `query`
                       END AS query,
                       language_id
                FROM " . $this->db->table('url_aliases');
    }

    /**
     * @param array $row
     *
     * @return array
     */
    private function prepareRow(array $row): array
    {
        parse_str($row['query'], $parts);
        $route = $parts['rt'] ?? '';
        if ($route !== '') {
            unset($parts['rt']);
            $query = http_build_query($parts);
        } else {
            $key = (string)array_key_first($parts);
            $route = self::ROUTES[$key] ?? '';
            $query = $row['query'];
        }
        return [
            'url_alias_id' => (int)$row['url_alias_id'],
            'seo_keyword'  => $row['keyword'],
            'route'        => $route,
            'query'        => $query,
            'language_id'  => (int)$row['language_id'],
        ];
    }

    /**
     * @param array $data
     *
     * @return string
     */
    private function buildStoredQuery(array $data): string
    {
        $route = trim((string)($data['route'] ?? ''), '/');
        $query = ltrim(trim((string)($data['query'] ?? '')), '?&');
        if ($route === '') {
            return $query;
        }
        parse_str($query, $parts);
        $firstKey = (string)array_key_first($parts);
        if ((self::ROUTES[$firstKey] ?? null) === $route) {
            return $query;
        }
        // keep slashes readable: "rt=product/product" instead of "rt=product%2Fproduct"
        $encodedRoute = str_replace('%2F', '/', rawurlencode($route));
        return 'rt=' . $encodedRoute . ($query !== '' ? '&' . $query : '');
    }

    /**
     * @param array $data
     *
     * @return int
     */
    private function getLanguageId(array $data): int
    {
        return (int)($data['language_id'] ?? 0) ?: (int)$this->language->getContentLanguageID();
    }
}
