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
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ModelToolSeoUrl extends Model
{
    /**
     * @param string|null $url - URL
     *
     * @return string
     * @throws AException
     */
    public function rewrite(?string $url = '')
    {
        if (!$url) {
            return '';
        }
        if (!$this->config->get('enable_seo_url')) {
            return $url;
        }
        $parsedUrl = parse_url(str_replace('&amp;', '&', $url));

        $httpQuery = [];
        parse_str($parsedUrl['query'], $httpQuery);
        
        $parentId = $httpQuery['parent_id'];
        unset(
            $httpQuery['parent_id'],
            $httpQuery['currency'],
            $httpQuery['language_id']
        );

        //replace path with category_id
        if(isset($httpQuery['path'])) {
            $ids = explode('_', $httpQuery['path']);
            $httpQuery['category_id'] = end($ids);
            unset($httpQuery['path']);
        }

        if (isset($httpQuery['rt']) ) {
            if (str_starts_with($httpQuery['rt'], 'p/')) {
                $httpQuery['rt'] = 'pages/' . ltrim($httpQuery['rt'], 'p/');
            } elseif (str_starts_with($httpQuery['rt'], 'r/')) {
                $httpQuery['rt'] = 'responses/' . ltrim($httpQuery['rt'], 'r/');
            } elseif (str_starts_with($httpQuery['rt'], 'a/')) {
                $httpQuery['rt'] = 'api/' . ltrim($httpQuery['rt'], 'r/');
            }elseif (
                !str_starts_with($httpQuery['rt'], 'pages/')
                && !str_starts_with($httpQuery['rt'], 'responses/')
                && !str_starts_with($httpQuery['rt'], 'api/')
            ) {
                $httpQuery['rt'] = 'pages/' . $httpQuery['rt'];
            }
        }

        $language_id = (int)$this->config->get('storefront_language_id');
        //create url encoded query string
        $keyword = $this->getSEOKeyword(http_build_query($httpQuery, '', '&', PHP_QUERY_RFC3986), $language_id);

        if (!$keyword) {
            return $url;
        }

        if (isset($httpQuery['rt'])) {
            unset($httpQuery['rt']);
        }
        //add parent content before keyword
        if(isset($httpQuery['content_id']) && (int)$parentId) {
            $parentKeyword = $this->getSEOKeyword(
                'content_id=' . (int) $parentId,
                $language_id
            );
            if ($parentKeyword) {
                $keyword .= $parentKeyword . '/' . $keyword;
            }                    
        }
        unset(
            $httpQuery['content_id'],
            $httpQuery['category_id'],
            $httpQuery['product_id'],
            $httpQuery['manufacturer_id'],
            $httpQuery['collection_id']
        );
        
        return $parsedUrl['scheme']
            . '://' . $parsedUrl['host']
            . (isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '')
            . str_replace('/index.php', '', $parsedUrl['path'])
            . '/' . $keyword
            . ($httpQuery ? '?' . http_build_query($httpQuery) : '');
    }

    /**
     * @param string $url
     * @param int|null $language_id
     *
     * @return string
     * @throws AException
     */
    public function getSEOKeyword(string $url, ?int $language_id = 1): string
    {
        $parsedUrl = parse_url(str_replace('&amp;', '&', $url));
        $queryString = $parsedUrl['query'] ?? ltrim($url, '?');

        $params = [];
        parse_str($queryString, $params);

        if (!$params) {
            return '';
        }
        
        $cacheKey = 'url_aliases.' . md5($url) . '.lang_' . (int)$language_id;
        $output = $this->cache->pull($cacheKey);

        if ($output !== false) {
            return $output;
        }

        $queryParts = ["`query` = '" . $this->db->escape($url) . "'"];
        if(isset($params['rt']) && count($params) > 1) {
            unset($params['rt']);
            $queryParts[] = "`query` = '" . $this->db->escape(http_build_query($params, '', '&', PHP_QUERY_RFC3986)) . "'";
        }

        $sql = "SELECT keyword
                FROM " . $this->db->table('url_aliases') . "
                WHERE (" . implode(' OR ', $queryParts) . ")
                    AND language_id='" . (int)$language_id . "'
                ORDER BY FIELD(
                        `query`,
                        " . implode(
                            ', ',
                            array_map(
                                static fn (string $condition): string => "'" . substr($condition, strpos($condition, "'") + 1, -1) . "'",
                                $queryParts
                            )
                        ) . "
                    )
                LIMIT 1";

        $result = $this->db->query($sql);
        $output = $result->row['keyword'] ?? '';

        $this->cache->push($cacheKey, $output);

        return $output;
    }
}
