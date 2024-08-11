<?php
/*
 * $Id$
 *
 * AbanteCart, Ideal OpenSource Ecommerce Solution
 * http://www.AbanteCart.com
 *
 * Copyright © 2011-2024 Belavier Commerce LLC
 *
 * This source file is subject to Open Software License (OSL 3.0)
 * License details is bundled with this package in the file LICENSE.txt.
 * It is also available at this URL:
 * <http://www.opensource.org/licenses/OSL-3.0>
 *
 * UPGRADE NOTE:
 * Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 * versions in the future. If you wish to customize AbanteCart for your
 * needs please refer to http://www.AbanteCart.com for more information.
 */

if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

/**
 * Class ModelCatalogCategory
 */
class ModelCatalogCategory extends Model
{
    /**
     * @param $data
     *
     * @return int
     * @throws AException
     */
    public function addCategory($data)
    {
        $contentLangId = $this->language->getContentLanguageID();
        $this->db->query(
            "INSERT INTO ".$this->db->table("categories")." 
            SET parent_id = '".(int) $data['parent_id']."',
                sort_order = '".(int) $data['sort_order']."',
                status = '".(int) $data['status']."',
                date_modified = NOW(),
                date_added = NOW()"
        );
        $category_id = $this->db->getLastId();

        foreach ($data['category_description'] as $language_id => $value) {
            $this->language->replaceDescriptions(
                'category_descriptions',
                ['category_id' => (int) $category_id],
                [
                    $language_id => [
                        'name'             => $value['name'],
                        'meta_keywords'    => $value['meta_keywords'],
                        'meta_description' => $value['meta_description'],
                        'description'      => $value['description'],
                    ],
                ]
            );
        }

        if (isset($data['category_store'])) {
            foreach ($data['category_store'] as $store_id) {
                $this->db->query(
                    "INSERT INTO ".$this->db->table("categories_to_stores")." 
                    SET category_id = '".(int) $category_id."', 
                    store_id = '".(int) $store_id."'"
                );
            }
        }

        if ($data['keyword']) {
            $seo_key = SEOEncode($data['keyword'], 'category_id', $category_id);
        } else {
            //Default behavior to save SEO URL keyword from category name in default language
            /** @var ALanguageManager */
            $seo_key = SEOEncode(
                $data['category_description'][$this->language->getDefaultLanguageID()]['name'],
                'category_id',
                $category_id
            );
        }
        if ($seo_key) {
            $this->language->replaceDescriptions(
                'url_aliases',
                ['query' => "category_id=".(int) $category_id],
                [(int) $contentLangId => ['keyword' => $seo_key]]
            );
        } else {
            $this->db->query(
                "DELETE
                FROM ".$this->db->table("url_aliases")." 
                WHERE query = 'category_id=".(int) $category_id."'
                    AND language_id = '".(int) $contentLangId."'"
            );
        }

        $this->cache->remove('category');

        return $category_id;
    }

    /**
     * @param int $category_id
     * @param array $data
     *
     * @throws AException
     */
    public function editCategory($category_id, $data)
    {
        $fields = ['parent_id', 'sort_order', 'status'];
        $update = ['date_modified = NOW()'];
        foreach ($fields as $f) {
            if (isset($data[$f])) {
                $update[] = $f." = '".$this->db->escape($data[$f])."'";
            }
        }
        if (!empty($update)) {
            $this->db->query(
                "UPDATE ".$this->db->table("categories")." 
                SET ".implode(',', $update)." 
                WHERE category_id = '".(int) $category_id."'"
            );
        }

        if (!empty($data['category_description'])) {
            foreach ($data['category_description'] as $language_id => $value) {
                $update = [];
                if (isset($value['name'])) {
                    $update["name"] = $value['name'];
                }
                if (isset($value['description'])) {
                    $update["description"] = $value['description'];
                }
                if (isset($value['meta_keywords'])) {
                    $update["meta_keywords"] = $value['meta_keywords'];
                }
                if (isset($value['meta_description'])) {
                    $update["meta_description"] = $value['meta_description'];
                }
                if (!empty($update)) {
                    // insert or update
                    $this->language->replaceDescriptions(
                        'category_descriptions',
                        ['category_id' => (int) $category_id],
                        [$language_id => $update]
                    );
                }
            }
        }

        if (isset($data['category_store'])) {
            $this->db->query(
                "DELETE FROM ".$this->db->table("categories_to_stores")." 
                WHERE category_id = '".(int) $category_id."'"
            );
            foreach ($data['category_store'] as $store_id) {
                $this->db->query(
                    "INSERT INTO ".$this->db->table("categories_to_stores")." 
                    SET category_id = '".(int) $category_id."', 
                        store_id = '".(int) $store_id."'"
                );
            }
        }

        if (isset($data['keyword'])) {
            $data['keyword'] = SEOEncode($data['keyword']);
            if ($data['keyword']) {
                $this->language->replaceDescriptions(
                    'url_aliases',
                    ['query' => "category_id=".(int) $category_id],
                    [(int) $this->language->getContentLanguageID() => ['keyword' => $data['keyword']]]
                );
            } else {
                $this->db->query(
                    "DELETE
                    FROM ".$this->db->table("url_aliases")." 
                    WHERE query = 'category_id=".(int) $category_id."'
                       AND language_id = '".(int) $this->language->getContentLanguageID()."'"
                );
            }
        }

        $this->cache->remove(['product', 'collection', 'category','storefront_menu']);
    }

    /**
     * @param int $category_id
     *
     * @throws AException
     */
    public function deleteCategory($category_id)
    {
        $this->db->query(
            "DELETE FROM ".$this->db->table("categories")." 
            WHERE category_id = '".(int) $category_id."'"
        );
        $this->db->query(
            "DELETE FROM ".$this->db->table("category_descriptions")." 
            WHERE category_id = '".(int) $category_id."'"
        );
        $this->db->query(
            "DELETE FROM ".$this->db->table("categories_to_stores")." 
            WHERE category_id = '".(int) $category_id."'"
        );
        $this->db->query(
            "DELETE FROM ".$this->db->table("url_aliases")." 
            WHERE query = 'category_id=".(int) $category_id."'"
        );
        $this->db->query(
            "DELETE FROM ".$this->db->table("products_to_categories")."
             WHERE category_id = '".(int) $category_id."'"
        );

        //delete resources
        $rm = new AResourceManager();
        $resources = $rm->getResourcesList(
            [
                'object_name' => 'categories',
                'object_id'   => (int) $category_id,
            ]
        );
        foreach ($resources as $r) {
            $rm->unmapResource('categories', $category_id, $r['resource_id']);
            //if resource became orphan - delete it
            if (!$rm->isMapped($r['resource_id'])) {
                $rm->deleteResource($r['resource_id']);
            }
        }
        //remove layout
        $lm = new ALayoutManager();
        $lm->deletePageLayout('pages/product/category', 'path', $category_id);

        //delete children categories
        $query = $this->db->query(
            "SELECT category_id
            FROM ".$this->db->table("categories")."
            WHERE parent_id = '".(int) $category_id."'"
        );

        foreach ($query->rows as $result) {
            $this->deleteCategory($result['category_id']);
        }
        $this->cache->remove(['product', 'collection', 'category','storefront_menu']);
    }

    /**
     * @param int $category_id
     *
     * @return array
     * @throws AException
     */
    public function getCategory($category_id)
    {
        $query = $this->db->query(
            "SELECT DISTINCT *,
                (SELECT keyword
                FROM ".$this->db->table("url_aliases")." 
                WHERE query = 'category_id=".(int) $category_id."'
                    AND language_id='".(int) $this->language->getContentLanguageID()."' ) AS keyword
            FROM ".$this->db->table("categories")." 
            WHERE category_id = '".(int) $category_id."'"
        );
        return $query->row;
    }

    /**
     * @param int $parent_id
     * @param int|array $store_id
     *
     * @return array
     * @throws AException
     */
    public function getCategories($parent_id = null, $store_id = null)
    {
        $store_id = is_array($store_id) ? array_map('intval', $store_id) : $store_id;
        $language_id = $this->language->getContentLanguageID();
        $cacheKey = 'category.'.$parent_id
            .'.store_'.md5(serialize($store_id))
            .'_lang_'.$language_id;
        $category_data = $this->cache->pull($cacheKey);

        if ($category_data === false) {
            $category_data = [];
            if ($store_id === null) {
                $sql = "SELECT c.*, s.name as store_name ";
            } else {
                $sql = "SELECT c.*, cs.*, s.name as store_name ";
            }

            $sql .= " FROM ".$this->db->table("categories")." c
                    LEFT JOIN ".$this->db->table("category_descriptions")." cd
                    ON (c.category_id = cd.category_id) ";

            if ($store_id === null) {
                $sql .= " LEFT JOIN ".$this->db->table("categories_to_stores")." cs 
                               ON (c.category_id = cs.category_id)";
            } else {
                $sql .= " RIGHT JOIN ".$this->db->table("categories_to_stores")." cs 
                                ON (c.category_id = cs.category_id AND ";
                if (is_array($store_id) && !empty($store_id)) {
                    $sql .= "store_id IN (".implode(', ', $store_id).")";
                } else {
                    $sql .= "store_id = ".(int) $store_id;
                }
                $sql .= ") ";
            }

            $sql .= " 
                    LEFT JOIN ".$this->db->table('stores')." s 
                       ON s.store_id = cs.store_id 
                    WHERE c.parent_id = '".(int) $parent_id."'
                        AND cd.language_id = '".(int) $language_id."'
                    ORDER BY c.sort_order, cd.name ASC";
            $query = $this->db->query($sql);

            foreach ($query->rows as $result) {
                $category_data[] = [
                    'category_id' => $result['category_id'],
                    'parent_id'   => $result['parent_id'],
                    'name'        => $this->getPath($result['category_id'], $language_id),
                    'status'      => $result['status'],
                    'sort_order'  => $result['sort_order'],
                    'store_name'  => $result['store_name'] ?? '',
                ];

                $category_data = array_merge(
                    $category_data,
                    $this->getCategories($result['category_id'], $store_id)
                );
            }

            $this->cache->push($cacheKey, $category_data);
        }

        return $category_data;
    }

    /**
     * @param array $data
     * @param string $mode
     *
     * @return array|int
     * @throws AException
     */
    public function getCategoriesData($data, $mode = 'default')
    {
        $language_id = (int) $data['language_id'] ?: (int) $this->language->getContentLanguageID();
        $store_id = (int)( $data['store_id'] ?: $this->config->get('current_store_id'));


        if ($mode == 'total_only') {
            $total_sql = 'count(*) as total';
        } else {
            $total_sql = $this->db->getSqlCalcTotalRows() ." *,
                          c.category_id ";
        }
        $where = (isset($data['parent_id']) ? "WHERE c.parent_id = '".(int) $data['parent_id']."'" : '');
        $sql = "SELECT ".$total_sql."
                FROM ".$this->db->table('categories')." c
                LEFT JOIN ".$this->db->table('category_descriptions')." cd
                    ON (c.category_id = cd.category_id AND cd.language_id = '".$language_id."')
                INNER JOIN ".$this->db->table('categories_to_stores')." cs
                    ON (c.category_id = cs.category_id AND cs.store_id = '".$store_id."')
                ".$where;

        if (!empty($data['subsql_filter'])) {
            $sql .= ($where ? " AND " : 'WHERE ').$data['subsql_filter'];
        }

        //If for total, we're done building the query
        if ($mode == 'total_only') {
            $query = $this->db->query($sql);
            return $query->row['total'];
        }

        $sort_data = [
            'name'       => 'cd.name',
            'status'     => 'c.status',
            'sort_order' => 'c.sort_order',
        ];

        if (isset($data['sort']) && in_array($data['sort'], array_keys($sort_data))) {
            $sql .= " ORDER BY ".$data['sort'];
        } else {
            $sql .= " ORDER BY c.sort_order, cd.name ";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT ".(int) $data['start'].",".(int) $data['limit'];
        }

        $query = $this->db->query($sql);
        $total_num_rows = $this->db->getTotalNumRows();
        $category_data = [];
        foreach ($query->rows as $result) {
            $category_data[] = [
                'category_id'    => $result['category_id'],
                'name'           => $this->getPath($result['category_id'], $language_id),
                'basename'       => $result['name'],
                'status'         => $result['status'],
                'sort_order'     => $result['sort_order'],
                'products_count' => $this->getProductCount($result['category_id']),
                'total_num_rows' => $total_num_rows
            ];
        }
        return $category_data;
    }

    /**
     * @param int $parent_id
     * @return int
     * @throws AException
     */
    function getProductCount($parent_id)
    {
        $store_id = (int)$this->session->data['current_store_id'];
        $cache_key = 'category.admin.product.count.'.$parent_id.'.' . '.store_' . $store_id;
        $output = $this->cache->pull($cache_key);
        if ($output !== false) {
            return $output;
        }
        $idList = array_merge([$parent_id], $this->getChildrenIDs($parent_id));
        $sql = "SELECT count(DISTINCT p.product_id) as product_count
                FROM " . $this->db->table('products_to_categories') . " p2c
                INNER JOIN " . $this->db->table('products') . " p 
                    ON p.product_id = p2c.product_id
                INNER JOIN " . $this->db->table('products_to_stores') . " s 
                    ON (p.product_id = s.product_id AND s.store_id=" . $store_id.")
                WHERE p2c.category_id in (" . implode(",", $idList) . ") ";

        $query = $this->db->query($sql);
        $output = (int)$query->row['product_count'];
        $this->cache->push($cache_key, $output);
        return $output;
    }

    /**
     * @return array
     * @throws AException
     */
    public function getParents()
    {
        $query = $this->db->query(
            "SELECT DISTINCT c.parent_id, cd.name
             FROM ".$this->db->table("categories")." c
             LEFT JOIN ".$this->db->table("categories")." c1 
                ON (c.parent_id = c1.category_id)
             LEFT JOIN ".$this->db->table("category_descriptions")." cd 
                ON (c1.category_id = cd.category_id)
             WHERE cd.language_id = '".(int) $this->language->getContentLanguageID()."'
             ORDER BY c.sort_order, cd.name ASC"
        );
        $result = [];
        foreach ($query->rows as $r) {
            $result[$r['parent_id']] = $r['name'];
        }

        return $result;
    }

    /**
     * @return array
     * @throws AException
     */
    public function getLeafCategories()
    {
        $query = $this->db->query(
            "SELECT t1.category_id as category_id 
            FROM ".$this->db->table("categories")." AS t1 
            LEFT JOIN ".$this->db->table("categories")." as t2
                ON t1.category_id = t2.parent_id 
            WHERE t2.category_id IS NULL"
        );
        $result = [];
        foreach ($query->rows as $r) {
            $result[$r['category_id']] = $r['category_id'];
        }

        return $result;
    }

    /**
     * @param int $category_id
     * @param int $language_id
     *
     * @return string
     * @throws AException
     */
    public function getPath($category_id, $language_id = 0)
    {
        $category_id = (int) $category_id;
        $language_id = (int) $language_id;
        if (!$language_id) {
            $language_id = (int) $this->language->getContentLanguageID();
        }
        $query = $this->db->query(
            "SELECT name, parent_id
            FROM ".$this->db->table("categories")." c
            LEFT JOIN ".$this->db->table("category_descriptions")." cd
                ON (c.category_id = cd.category_id)
            WHERE c.category_id = '".(int) $category_id."' 
                AND cd.language_id = '".$language_id."'
            ORDER BY c.sort_order, cd.name ASC"
        );

        $category_info = $query->row;

        if ($category_info['parent_id']) {
            return $this->getPath(
                    $category_info['parent_id'],
                    $language_id
                ).$this->language->get('text_separator').$category_info['name'];
        } else {
            return $category_info['name'];
        }
    }

    /**
     * @param int $category_id
     *
     * @return array
     * @throws AException
     */
    public function getCategoryDescriptions($category_id)
    {
        $category_description_data = [];

        $query = $this->db->query(
            "SELECT * 
            FROM ".$this->db->table("category_descriptions")." 
            WHERE category_id = '".(int) $category_id."'"
        );

        foreach ($query->rows as $result) {
            $category_description_data[$result['language_id']] = [
                'name'             => $result['name'],
                'meta_keywords'    => $result['meta_keywords'],
                'meta_description' => $result['meta_description'],
                'description'      => $result['description'],
            ];
        }

        return $category_description_data;
    }

    /**
     * @param int $category_id
     *
     * @return array
     * @throws AException
     */
    public function getCategoryStores($category_id)
    {
        $category_store_data = [];
        $rows = $this->getCategoryStoresInfo($category_id);
        foreach ($rows as $result) {
            $category_store_data[] = $result['store_id'];
        }

        return $category_store_data;
    }

    /**
     * @param int $category_id
     *
     * @return array
     * @throws AException
     */
    public function getCategoryStoresInfo($category_id)
    {
        $query = $this->db->query(
            "SELECT c2s.*,
                    s.name as store_name,
                    ss.`value` as store_url,
                    sss.`value` as store_ssl_url
            FROM ".$this->db->table("categories_to_stores")." c2s
            LEFT JOIN ".$this->db->table("stores")." s ON s.store_id = c2s.store_id
            LEFT JOIN ".$this->db->table("settings")." ss
                ON (ss.store_id = c2s.store_id AND ss.`key`='config_url')
            LEFT JOIN ".$this->db->table("settings")." sss
                ON (sss.store_id = c2s.store_id AND sss.`key`='config_ssl_url')
            WHERE category_id = '".(int) $category_id."'"
        );
        return $query->rows;
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws AException
     */
    public function getTotalCategories($data = [])
    {
        return $this->getCategoriesData($data, 'total_only');
    }

    /**
     * @param int|array $parentId
     * @param bool $active_only
     * @return array
     * @throws AException
     */
    public function getChildrenIDs(int|array $parentId, $active_only = false)
    {
        if (is_array($parentId) && !$parentId) {
            return [];
        }
        $storeId = $this->config->get('config_store_id');
        $cacheKey = 'category.admin.children.' . $storeId . md5(var_export($parentId,true)).'.'.(int)$active_only;
        $cache = $this->cache->pull($cacheKey);
        if ($cache !== false) {
            return $cache;
        }

        $sql = "SELECT c.category_id
                FROM " . $this->db->table('categories') . " c
                LEFT JOIN " . $this->db->table('categories_to_stores') . " c2s
                    ON c2s.category_id = c.category_id
                WHERE c2s.store_id = " . $storeId;
        if(is_int($parentId) && $parentId >= 0){
            $sql .= " AND c.parent_id = '" . (int)$parentId . "'";
        }elseif(is_array($parentId)){
            $sql .= " AND c.parent_id IN (" . implode(',',$parentId) . ")";
        }

        if ($active_only) {
            $sql .= " AND c.status=1";
        }
        $result = $this->db->query($sql);
        $output = [];

        foreach ($result->rows as $category) {
            $output[] = (int)$category['category_id'];
            $output = array_merge($output, $this->getChildrenIDs((int)$category['category_id'],$active_only));
        }
        $this->cache->push($cacheKey, $output);
        return $output;
    }
}
