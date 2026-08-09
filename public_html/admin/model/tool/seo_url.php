<?php
/*
 * AbanteCart, Ideal OpenSource Ecommerce Solution
 * Copyright © 2011-2026 Belavier Commerce LLC
 * Licensed under the Open Software License (OSL 3.0)
 */
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ModelToolSeoUrl extends Model
{
    private const ROUTES = [
        'product_id'      => 'product/product',
        'category_id'     => 'product/category',
        'manufacturer_id' => 'product/manufacturer',
        'content_id'      => 'content/content',
        'collection_id'   => 'product/collection',
        'check_seo'       => 'index/check_seo',
    ];

    public function getSeoUrl(int $urlAliasId): array
    {
        $result = $this->db->query(
            "SELECT url_alias_id, keyword, query, language_id
             FROM " . $this->db->table('url_aliases') . "
             WHERE url_alias_id = '" . $urlAliasId . "'"
        );

        return $result->row ? $this->prepareRow($result->row) : [];
    }

    public function getSeoUrls(array $data = []): array
    {
        $rows = $this->db->query(
            $this->buildListSql($data)
            . " LIMIT " . max(0, (int)($data['start'] ?? 0))
            . ", " . max(1, (int)($data['limit'] ?? 20))
        )->rows;

        return $rows;
    }

    public function getTotalSeoUrls(array $data = []): int
    {
        $result = $this->db->query(
            'SELECT COUNT(*) AS total FROM (' . $this->buildBaseSql() . ') AS seo_urls'
            . ($data['subsql_filter'] ? ' WHERE ' . $data['subsql_filter'] : '')
        );
        return (int)$result->row['total'];
    }

    public function addSeoUrl(array $data): int
    {
        $this->db->query(
            "INSERT INTO " . $this->db->table('url_aliases') . "
             SET keyword = '" . $this->db->escape($data['seo_keyword']) . "',
                 query = '" . $this->db->escape($this->buildStoredQuery($data)) . "',
                 language_id = '" . (int)($data['language_id'] ?: $this->language->getContentLanguageID()) . "'"
        );
        return (int)$this->db->getLastId();
    }

    public function updateSeoUrl(int $urlAliasId, array $data): void
    {
        $current = $this->getSeoUrl($urlAliasId);
        if (!$current) {
            return;
        }
        $data = array_merge($current, $data);
        $this->db->query(
            "UPDATE " . $this->db->table('url_aliases') . "
             SET keyword = '" . $this->db->escape($data['seo_keyword']) . "',
                 query = '" . $this->db->escape($this->buildStoredQuery($data)) . "',
                 language_id = '" . (int)$data['language_id'] . "'
             WHERE url_alias_id = '" . $urlAliasId . "'"
        );
    }

    public function deleteSeoUrl(int $urlAliasId): void
    {
        $this->db->query(
            "DELETE FROM " . $this->db->table('url_aliases') . "
             WHERE url_alias_id = '" . $urlAliasId . "'"
        );
    }

    public function validateSeoUrl(array $data, int $urlAliasId = 0): array
    {
        $errors = [];
        $keyword = trim((string)($data['seo_keyword'] ?? ''));
        $route = trim((string)($data['route'] ?? ''));
        $query = trim((string)($data['query'] ?? ''));
        $languageId = (int)($data['language_id'] ?: $this->language->getContentLanguageID());

        if ($keyword === '' || mb_strlen($keyword) > 255) {
            $errors['seo_keyword'] = $this->language->get('error_seo_keyword');
        }
        if ($route === '') {
            $errors['route'] = $this->language->get('error_route');
        }
        if (mb_strlen($this->buildStoredQuery($data)) > 2048) {
            $errors['query'] = $this->language->get('error_query_length');
        }

        if (!$errors) {
            $storedQuery = $this->buildStoredQuery($data);
            $result = $this->db->query(
                "SELECT url_alias_id, keyword, query
                 FROM " . $this->db->table('url_aliases') . "
                 WHERE language_id = '" . $languageId . "'
                   AND (keyword = '" . $this->db->escape($keyword) . "'
                        OR query = '" . $this->db->escape($storedQuery) . "')
                   AND url_alias_id != '" . $urlAliasId . "'"
            );
            foreach ($result->rows as $row) {
                if ($row['keyword'] === $keyword) {
                    $errors['seo_keyword'] = $this->language->get('error_seo_keyword_exists');
                }
                if ($row['query'] === $storedQuery) {
                    $errors['query'] = $this->language->get('error_query_exists');
                }
            }
        }
        return $errors;
    }

    private function buildListSql(array $data): string
    {
        $allowedSort = ['seo_keyword', 'route', 'query'];
        $sort = in_array($data['sort'] ?? '', $allowedSort, true) ? $data['sort'] : 'seo_keyword';
        $order = ($data['order'] ?? '') === 'DESC' ? 'DESC' : 'ASC';
        return 'SELECT * FROM (' . $this->buildBaseSql() . ') AS seo_urls'
            . ($data['subsql_filter'] ? ' WHERE ' . $data['subsql_filter'] : '')
            . ' ORDER BY `' . $sort . '` ' . $order;
    }

    private function buildBaseSql(): string
    {
        $routeCases = [];
        foreach (self::ROUTES as $key => $route) {
            $routeCases[] = "WHEN `query` LIKE '" . $key . "=%' THEN '" . $route . "'";
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

    private function buildStoredQuery(array $data): string
    {
        $route = trim((string)($data['route'] ?? ''), '/');
        $query = ltrim(trim((string)($data['query'] ?? '')), '?&');
        parse_str($query, $parts);
        $firstKey = (string)array_key_first($parts);
?????
        if ((self::ROUTES[$firstKey] ?? null) === $route) {
            return $query;
        }
        return 'rt=' . rawurlencode($route) . ($query !== '' ? '&' . $query : '');
    }
}
