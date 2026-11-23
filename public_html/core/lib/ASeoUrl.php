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

/**
 * Rewrites standard storefront URLs into SEO URLs (table url_aliases).
 * Shared by storefront model tool/seo_url and AHtml (works in admin and storefront).
 */
class ASeoUrl
{
    /** @var Registry */
    protected $registry;

    /**
     * @param Registry $registry
     */
    public function __construct($registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param string $link - URL
     *
     * @return string
     * @throws AException
     */
    public function rewrite($link)
    {
        $config = $this->registry->get('config');
        if ($config->get('enable_seo_url')) {
            $url_data = parse_url(str_replace('&amp;', '&', $link));

            $url = '';
            $httpQuery = [];
            parse_str($url_data['query'], $httpQuery);

            $language_id = (int)$config->get('storefront_language_id');

            foreach ($httpQuery as $key => $value) {
                $object_name = $param_key = '';
                switch ($key) {
                    case 'product_id':
                        $object_name = 'product';
                        $param_key = $key;
                        break;
                    case 'manufacturer_id':
                        $object_name = 'manufacturer';
                        $param_key = $key;
                        break;
                    case 'content_id':
                        $object_name = 'content';
                        $param_key = $key;
                        break;
                    case 'category_id':
                        $object_name = 'category';
                        $param_key = $key;
                        break;
                    case 'collection_id':
                        $object_name = 'collection';
                        $param_key = $key;
                        break;
                    case 'path':
                        $object_name = 'category';
                        $param_key = 'category_id';
                        //special case for subcategory
                        $value = explode('_', (string)$value);
                        end($value);
                        $value = current($value);
                        break;
                    default:
                }

                if (!$object_name) {
                    continue;
                }
                if(!is_array($value)) {
                    $keyword = $this->getSEOKeyword($object_name, $param_key, (int) $value, $language_id);
                }else{
                    $keyword = '';
                }
                if ($keyword) {
                    if ($object_name == 'content') {
                        if ($httpQuery['parent_id']) {
                            $parentKeyword = $this->getSEOKeyword($object_name, $param_key, (int)$httpQuery['parent_id'], $language_id);
                            if ($parentKeyword) {
                                $url .= '/' . $parentKeyword;
                            }
                        }
                        unset($httpQuery['parent_id']);
                    }

                    $url .= '/' . $keyword;
                    unset($httpQuery[$key]);
                }

            }

            if ($url) {
                unset($httpQuery['rt']);
                return $url_data['scheme']
                    . '://' . $url_data['host']
                    . (isset($url_data['port']) ? ':' . $url_data['port'] : '')
                    . str_replace('/index.php', '', $url_data['path'])
                    . $url
                    . ($httpQuery ? '?' . http_build_query($httpQuery) : '');
            } else {
                return $link;
            }
        } else {
            return $link;
        }
    }

    /**
     * @param string $object_name - product, category, manufacturer, content
     * @param string|null $param_key - product_id, category_id, manufacturer_id, content_id
     * @param int|null $param_value - id
     * @param int|null $language_id
     *
     * @return string
     * @throws AException
     */
    public function getSEOKeyword(string $object_name, ?string $param_key = '', ?int $param_value = 0, ?int $language_id = 1)
    {
        $db = $this->registry->get('db');
        $cache = $this->registry->get('cache');
        if ($this->registry->get('config')->get('config_cache_enable')) {
            $cache_key = 'url_aliases.' . $object_name . '.lang_' . (int)$language_id;
            $aliases = $cache->pull($cache_key);
            //if no cache - push
            if ($aliases === false) {
                $aliases = [];
                $sql = "SELECT query, keyword
					FROM " . $db->table('url_aliases') . "
					WHERE `query` LIKE '" . $db->escape($param_key, true) . "=%'
						AND language_id='" . $language_id . "'";
                $result = $db->query($sql);

                foreach ($result->rows as $row) {
                    $seo_query = $row['query'];
                    $parts = explode('=', $seo_query);
                    $aliases[$parts[1]] = $row['keyword'];
                }
                $cache->push($cache_key, $aliases);
            }
            $output = $aliases[$param_value] ?? '';
        } else {
            $sql = "SELECT keyword
					FROM " . $db->table('url_aliases') . "
					WHERE `query`='" . $db->escape($param_key) . "=" . $param_value . "'
						AND language_id='" . $language_id . "'";
            $result = $db->query($sql);
            $output = $result->row['keyword'];
        }

        return $output;
    }
}
