<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ModelToolSeoUrl extends Model
{
    /**
     * @param string $link - URL
     *
     * @return string
     */
    public function rewrite($link)
    {
        if ($this->config->get('enable_seo_url')) {
            $url_data = parse_url(str_replace('&amp;', '&', $link));

            $url = '';
            $data = array();
            parse_str($url_data['query'], $data);

            $language_id = (int)$this->config->get('storefront_language_id');

            foreach ($data as $key => $value) {

                $object_name = '';
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
                        $value = explode('_', $value);
                        end($value);
                        $value = current($value);
                        break;
                    default:
                }

                if (!$object_name) {
                    continue;
                }

                $keyword = $this->getSEOKeyword($object_name, $param_key, $value, $language_id);

                if ($keyword) {
                    $url .= '/'.$keyword;
                    unset($data[$key]);
                }

            }

            if ($url) {
                unset($data['rt']);

                $query = '';

                if ($data) {
                    foreach ($data as $key => $value) {
                        $query .= '&'.$key.'='.$value;
                    }

                    if ($query) {
                        $query = '?'.trim($query, '&');
                    }
                }

                return $url_data['scheme'].'://'.$url_data['host'].(isset($url_data['port']) ? ':'.$url_data['port'] : '').str_replace('/index.php', '', $url_data['path']).$url.$query;
            } else {
                return $link;
            }
        } else {
            return $link;
        }
    }

    /**
     * @param string $object_name - product, category, manufacturer, content
     * @param string $param_key   - product_id, category_id, manufacturer_id, content_id
     * @param int    $param_value - id
     * @param int    $language_id
     *
     * @return string
     */
    public function getSEOKeyword($object_name, $param_key, $param_value, $language_id)
    {

        $language_id = (int)$language_id;
        $param_value = (int)$param_value;

        if ($this->config->get('config_cache_enable')) {
            $cache_key = $object_name.'.url_aliases.lang_'.(int)$language_id;
            $aliases = $this->cache->pull($cache_key);
            //if no cache - push
            if ($aliases === false) {
                $aliases = array();
                $sql = "SELECT query, keyword
					FROM ".$this->db->table('url_aliases')."
					WHERE `query` LIKE '".$this->db->escape($param_key, true)."=%'
						AND language_id='".$language_id."'";
                $result = $this->db->query($sql);

                foreach ($result->rows as $row) {
                    $seo_query = $row['query'];
                    $parts = explode('=', $seo_query);
                    $aliases[$parts[1]] = $row['keyword'];
                }
                $this->cache->push($cache_key, $aliases);
            }
            $output = isset($aliases[$param_value]) ? $aliases[$param_value] : '';

        } else {
            $sql = "SELECT keyword
					FROM ".$this->db->table('url_aliases')."
					WHERE `query`='".$this->db->escape($param_key)."=".$param_value."'
						AND language_id='".$language_id."'";
            $result = $this->db->query($sql);
            $output = $result->row ? $result->row['keyword'] : '';
        }

        return $output;
    }
}
