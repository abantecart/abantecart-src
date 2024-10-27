<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ModelCatalogContent extends Model
{
    /**
     * @param $content_id
     *
     * @return array
     */
    public function getContent($content_id, $store_id = null, $language_id = 0)
    {
        $content_id = (int)$content_id;
        $store_id = $store_id ?? (int)$this->config->get('config_store_id');
        $language_id = $language_id ?: (int)$this->config->get('storefront_language_id');
        $cache_key = 'content.'.$content_id.'.store_'.$store_id.'_lang_'.$language_id;
        $cache = $this->cache->pull($cache_key);

        if ($cache !== false) {
            return $cache;
        }

        $cache = [];
        $sql = "SELECT DISTINCT i.*, id.*
				FROM ".$this->db->table("contents")." i
				LEFT JOIN ".$this->db->table("content_descriptions")." id
					ON (i.content_id = id.content_id AND id.language_id = '".$language_id."')
				LEFT JOIN ".$this->db->table("contents_to_stores")." i2s
					ON (i.content_id = i2s.content_id)
				WHERE i.content_id = '".$content_id."' AND COALESCE(i2s.store_id,0) = '".$store_id."' AND i.status = '1'";
        $query = $this->db->query($sql);

        if ($query->num_rows) {
            $cache = $query->row;
        }
        $this->cache->push($cache_key, $cache);

        return $cache;
    }

    /**
     * @return array
     * @throws AException
     */
    public function getContents($data = [])
    {
        $store_id = (int)$this->config->get('config_store_id');
        $language_id = (int)$this->config->get('storefront_language_id');
        $cache_key = 'content.all.store_'.$store_id
            .'_lang_'.$language_id
            .md5(var_export($data,true));
        $output = $this->cache->pull($cache_key);
        if ($output === false) {
            $output = [];
            $sql = "SELECT i.*, id.*
                FROM ".$this->db->table("contents")." i
                LEFT JOIN ".$this->db->table("content_descriptions")." id
                        ON (i.content_id = id.content_id
                                AND id.language_id = '".(int)$this->config->get('storefront_language_id')."')";
            $sql .= "LEFT JOIN ".$this->db->table("contents_to_stores")." i2s ON (i.content_id = i2s.content_id)";
            $sql .= "WHERE i.status = '1' ";
            $sql .= " AND COALESCE(i2s.store_id,0) = '".(int)$this->config->get('config_store_id')."'";
            $sql .= " AND COALESCE(i.publish_date, '1970-01-01') < now() AND COALESCE(i.expire_date, now()) >= now()";
            //filter result by given ids array
            if ($data['filter_ids']) {
                $ids = array_unique(array_map('intval', (array)$data['filter_ids']));
                $sql .= " AND i.content_id IN (" . implode(', ', $ids) . ")";
            }

            if ($data['new']) {
                $sql .= " ORDER BY i.publish_date DESC";
            } else {
                $sql .= " ORDER BY i.parent_content_id, i.sort_order, LCASE(id.title) ASC";
            }
            if ($data['limit']) {
                $sql .= " LIMIT " . (int)$data['limit'];
            }

            $query = $this->db->query($sql);

            if ($query->num_rows) {
                $output = $query->rows;
            }
            $this->cache->push($cache_key, $output);
        }
        return $output;
    }

    /**
     * @param int $parentId
     *
     * @param string $mode - can be empty or "active_only"
     *
     * @return array
     * @throws AException
     * @throws AException
     */
    public function getChildrenIDs($parentId, $mode = 'active_only')
    {
        $parentId = (int)$parentId;
        if (!$parentId) {
            return [];
        }
        $cacheKey = 'content.children.id.' . $parentId . '.' . preformatTextID($mode);
        $cache = $this->cache->pull($cacheKey);
        if (isset($cache) && $cache !== false) {
            return $cache;
        }

        $storeId = $this->config->get('config_store_id');

        $sql = "SELECT c.content_id
                FROM " . $this->db->table('contents') . " c
                LEFT JOIN " . $this->db->table('contents_to_stores') . " c2s
                    ON c2s.content_id = c.content_id
                WHERE c2s.store_id = " . (int)$storeId . "
                    AND c.parent_content_id=" . (int)$parentId;
        if ($mode == 'active_only') {
            $sql .= " AND c.status=1";
        }

        $result = $this->db->query($sql);
        $output = [];
        foreach ($result->rows as $content) {
            $output[] = (int)$content['content_id'];
            $output = array_merge($output, $this->getChildrenIDs($content['content_id']));
        }
        $this->cache->push($cacheKey, $output);
        return $output;
    }

    /**
     * @param int $parentId
     *
     * @param array $params
     *
     * @return array
     */
    public function getChildren($parentId, $params = [])
    {
        $sort_data = [
            'defailt'       => 'c.sort_order',
            'name-ASC'      => 'cd.title',
            'name-DESC'     => 'cd.title',
            'date-DESC'     => 'c.publish_date',
            'date-ASC'      => 'c.publish_date',
        ];

        $parentId = (int)$parentId;
        if (!$parentId) {
            return [];
        }
        $cacheKey = 'content.children.' . $parentId;
        $cache = $this->cache->pull($cacheKey);
        if (isset($cache) && $cache !== false) {
            return $cache;
        }
        $sort = $sort_data[$params['sort']] ?: $sort_data['defailt'];
        $parts = explode('-', $params['sort']);
        $order = $parts[1] ?: 'ASC';
        $limit = abs((int) $params['limit']?:10);
        $page = abs((int)$params['page']?:1);
        $tag = $params['tag']?:'';
        $start = abs((int)($page - 1) * $limit);
        $language_id = $params['language_id'] ?: (int)$this->config->get('storefront_language_id');
        $store_id = $params['store_id'] ?? (int)$this->config->get('config_store_id');

        $sql = "SELECT " . $this->db->getSqlCalcTotalRows() . " DISTINCT c.*, c.*, c2s.*, cd.*, ct.* 
                FROM " . $this->db->table('contents') . " c
                LEFT JOIN " . $this->db->table('contents_to_stores') . " c2s
                    ON c2s.content_id = c.content_id
                LEFT JOIN ".$this->db->table("content_descriptions")." cd
                        ON (c.content_id = cd.content_id
                                AND cd.language_id = '". $language_id ."')
                LEFT JOIN ".$this->db->table("content_tags")." ct 
                    ON (c.content_id = ct.content_id)
                WHERE c2s.store_id = " . (int)$store_id . "
                    AND COALESCE(c.publish_date, '1970-01-01') < now() AND COALESCE(c.expire_date, now()) >= now()
                    AND c.parent_content_id=" . $parentId;

        if ($tag) {
            $sql .= " AND LCASE(ct.tag) = '".$this->db->escape(trim($tag))."' ";
        }
        $sql .= ' GROUP BY c.content_id ';

        $sql .= " ORDER BY ".$sort." ".$order;


        if ($start < 0) {
            $start = 0;
        }

        $sql .= " LIMIT ".$start.",".$limit;
        $result = $this->db->query($sql);
        $result->rows[0]['total_num_rows'] = $this->db->getTotalNumRows();
        return $result->rows;
    }

    /**
     * @param int $content_id
     * @param int $language_id
     *
     * @return array
     */
    public function getContentTags($content_id, $language_id = 0)
    {
        $language_id = (int) $language_id;
        $tag_data = [];
        $tagStr = [];

        $query = $this->db->query(
            "SELECT *
            FROM ".$this->db->table("content_tags")." 
            WHERE content_id = '".(int)$content_id."'"
        );

        foreach ($query->rows as $result) {
            $tag_data[$result['language_id']][] = $result['tag'];
        }

        return $tag_data[$language_id];
    }

}