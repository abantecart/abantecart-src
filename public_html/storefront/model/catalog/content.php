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
     * @param int $content_id
     * @param int|null $store_id
     * @param int $language_id
     * @return array
     * @throws AException
     */
    public function getContent($content_id, $store_id = null, $language_id = 0)
    {
        $content_id = (int)$content_id;
        $store_id = $store_id ?? (int)$this->config->get('config_store_id');
        $language_id = $language_id ?: (int)$this->config->get('storefront_language_id');
        $cache_key = 'content.'.$content_id.'.store_'.$store_id.'_lang_'.$language_id;
        $output = $this->cache->pull($cache_key);

        if ($output !== false) {
            return $output;
        }

        $sql = "SELECT DISTINCT i.*, id.*, COALESCE(i.publish_date, i.date_added) as publish_date
                FROM ".$this->db->table("contents")." i
                LEFT JOIN ".$this->db->table("content_descriptions")." id
                    ON (i.content_id = id.content_id AND id.language_id = '".$language_id."')
                LEFT JOIN ".$this->db->table("contents_to_stores")." i2s
                    ON (i.content_id = i2s.content_id)
                WHERE i.content_id = '".$content_id."' 
                    AND COALESCE(i2s.store_id,0) = '".$store_id."' 
                    AND COALESCE(i.publish_date, '1970-01-01') < now() 
                    AND COALESCE(i.expire_date, now()) >= now()
                AND i.status = '1'";
        $query = $this->db->query($sql);
        $output = $query->row;
        $this->cache->push($cache_key, $output);
        return $output;
    }

    /**
     * @return array
     * @throws AException
     */
    public function getContents(?array $data = [])
    {
        $store_id = (int)$this->config->get('config_store_id');
        $language_id = (int)$this->config->get('storefront_language_id');
        $cache_key = 'content.all.store_'.$store_id
            .'_lang_'.$language_id
            .md5(var_export($data,true));
        $output = $this->cache->pull($cache_key);
        if ($output === false) {
            $sql = "SELECT i.*, id.*
                FROM ".$this->db->table("contents")." i
                LEFT JOIN ".$this->db->table("content_descriptions")." id
                    ON (i.content_id = id.content_id
                            AND id.language_id = '".(int)$this->config->get('storefront_language_id')."')
                LEFT JOIN ".$this->db->table("contents_to_stores")." i2s 
                    ON (i.content_id = i2s.content_id)
                WHERE i.status = '1' 
                    AND COALESCE(i2s.store_id,0) = '".(int)$this->config->get('config_store_id')."'
                    AND COALESCE(i.publish_date, '1970-01-01') < NOW() AND COALESCE(i.expire_date, NOW()) >= NOW()";
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
            $output = $query->rows;
            $this->cache->push($cache_key, $output);
        }
        return $output;
    }

    /**
     * @param array $data
     * @return false|array
     * @throws AException
     */
    public function filterContents($data = [])
    {
        $language_id = (int)$data['content_language_id'] ?: (int)$this->config->get('storefront_language_id');
        $storeId = $this->config->get('config_store_id');
        $cacheKey = 'content.get.list.'.md5(var_export($data, true)).$language_id.$storeId;
        $output = $this->cache->pull($cacheKey);
        if( $output !== false ){
            return $output;
        }

        $filter = $data['filter'] ?? [];

        $sql = "SELECT " . $this->db->getSqlCalcTotalRows() . " DISTINCT c.*, c2s.*, cd.*,
                GROUP_CONCAT(`ct`.`tag` ORDER BY `ct`.`tag` ASC SEPARATOR ','  )  as `tags` 
                FROM " . $this->db->table('contents') . " c
                LEFT JOIN " . $this->db->table('contents_to_stores') . " c2s
                    ON c2s.content_id = c.content_id
                LEFT JOIN ".$this->db->table("content_descriptions")." cd
                    ON (c.content_id = cd.content_id AND cd.language_id = '". $language_id ."')
                LEFT JOIN ".$this->db->table("content_tags")." ct 
                    ON (c.content_id = ct.content_id AND ct.language_id = '". $language_id ."')
                WHERE c2s.store_id = " . $storeId . "
                    AND COALESCE(c.publish_date, '1970-01-01') < now() 
                    AND COALESCE(c.expire_date, now()) >= now()
                    AND c.status = '1'";

        if ($filter['parent_id']) {
            $sql .= " AND c.parent_content_id=" . (int)$filter['parent_id'] . " ";
        }

        if ($filter['tag']) {
            $sql .= " AND LCASE(ct.tag) = '".$this->db->escape(trim($filter['tag']))."' ";
        }

        $match = $filter['match'] ?? 'exact';
        if (isset($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            if ($match == 'any') {
                $sql .= " AND (";
                foreach ($keywords as $k => $keyword) {
                    $kw = $this->db->escape(strtolower($keyword), true);
                    $sql .= $k > 0 ? " OR" : "";
                    $sql .= " (LCASE(cd.title) LIKE '%".$kw."%'";
                    $sql .= " OR LCASE(cd.description) LIKE '%".$kw."%'";
                    $sql .= " OR LCASE(cd.content) LIKE '%".$kw."%')";
                }
                $sql .= " )";
            } else {
                if ($match == 'all') {
                    $sql .= " AND (";
                    foreach ($keywords as $k => $keyword) {
                        $kw = $this->db->escape(strtolower($keyword), true);
                        $sql .= $k > 0 ? " AND" : "";
                        $sql .= " (LCASE(cd.title) LIKE '%".$kw."%'";
                        $sql .= " OR LCASE(cd.description) LIKE '%".$kw."%'";
                        $sql .= " OR LCASE(cd.content) LIKE '%".$kw."%')";
                    }
                    $sql .= " )";
                } else {
                    if ($match == 'exact') {
                        $kw = $this->db->escape(strtolower($filter['keyword']), true);
                        $sql .= " AND (LCASE(cd.title) LIKE '%".$kw."%'";
                        $sql .= " OR LCASE(cd.description) LIKE '%".$kw."%'";
                        $sql .= " OR LCASE(cd.content) LIKE '%".$kw."%')";
                    }
                }
            }
        }

        $sql .= ' GROUP BY c.content_id ';

        $sort_data = [
            'default'       => 'c.sort_order',
            'name-ASC'      => 'cd.title',
            'name-DESC'     => 'cd.title',
            'date-DESC'     => 'COALESCE(c.publish_date, c.date_added)',
            'date-ASC'      => 'COALESCE(c.publish_date, c.date_added)',
        ];

        if (isset($data['sort']) && in_array($data['sort'], array_keys($sort_data))) {
            $sql .= " ORDER BY ".$sort_data[$data['sort']];
        } else {
            $sql .= " ORDER BY ".$sort_data['default'];
        }

        $parts = explode('-', $data['sort']);
        $order = $parts[1] ?: 'ASC';
        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ".$order;
        }

        if (isset($data['start']) || isset($data['limit'])) {
            $data['start'] = max($data['start'],0);
            if ($data['limit'] < 1) {
                $data['limit'] = 10;
            }
            $sql .= " LIMIT ".(int) $data['start'].",".(int) $data['limit'];
        }
        $query = $this->db->query($sql);
        $output = $query->rows;
        if ($output) {
            $output[0]['total_num_rows'] = $this->db->getTotalNumRows();
        }

        $this->cache->push($cacheKey, $output);
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
        if (!isset($parentId)) {
            return [];
        }
        $parentId = (int)$parentId;
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
     * @param int $content_id
     * @param int $language_id
     *
     * @return array
     * @throws AException
     */
    public function getContentTags(int $content_id, int $language_id)
    {
        $cacheKey = 'content.tags.' . $content_id . '.' . $language_id;
        $output = $this->cache->pull($cacheKey);
        if($output !== false) {
            return $output;
        }

        $tag_data = [];
        $query = $this->db->query(
            "SELECT * FROM ".$this->db->table("content_tags")." WHERE content_id = '".(int)$content_id."'"
        );

        foreach ($query->rows as $result) {
            $tag_data[$result['language_id']][] = $result['tag'];
        }
        $output = (array)$tag_data[$language_id];
        $this->cache->push($cacheKey, $output);
        return $output;
    }

    /**
     * Returns content data for listing blocks
     * @param array $content_ids
     *
     * @return array
     * @throws AException
     */
    public function getListingContent($content_ids, $limit){
        $includeIDs = [];
        foreach ($content_ids as $content_id) {
            if ($children = $this->getChildrenIDs($content_id)) {
                //exclude current parent if include all children
                $includeIDs = array_merge($includeIDs, $children);
            } else {
                //include only current content
                $includeIDs[] = $content_id;
            }
        }
        $filter = [
            'filter_ids' => $includeIDs,
            'limit' => $limit ?: null
        ];

        $contents = $this->getContents($filter);
        return  $contents;
    }
}
