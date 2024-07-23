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

/**
 * Class ModelCatalogCategory
 *
 * @property ModelCatalogProduct $model_catalog_product
 * @property ModelCatalogManufacturer $model_catalog_manufacturer
 */
class ModelCatalogCategory extends Model
{
    protected $category_id = 0;
    protected $thumbnails = [];
    protected $path = [];
    protected $selected_root_id = [];

    /**
     * @param int $category_id
     *
     * @return array
     * @throws AException
     * @throws AException
     */
    public function getCategory($category_id)
    {
        $store_id = (int)$this->config->get('config_store_id');
        $language_id = (int)$this->config->get('storefront_language_id');

        $cache_key = 'product.listing.category.' . (int)$category_id . '.store_' . $store_id . '_lang_' . $language_id;
        $cache = $this->cache->pull($cache_key);
        if ($cache === false) {
            $query = $this->db->query(
                "SELECT DISTINCT *,
                    (SELECT COUNT(p2c.product_id) as cnt
                     FROM " . $this->db->table('products_to_categories') . " p2c
                     INNER JOIN " . $this->db->table('products') . " p 
                        ON ( p.product_id = p2c.product_id 
                            AND p.status = '1'
                            AND COALESCE(p.date_available,'1970-01-01')< NOW() )
                     INNER JOIN " . $this->db->table('products_to_stores') . " s
                        ON (p.product_id = s.product_id AND s.store_id = ".$store_id.")
                     WHERE  p2c.category_id = c.category_id) as products_count
                FROM " . $this->db->table("categories") . " c
                LEFT JOIN " . $this->db->table("category_descriptions") . " cd 
                    ON (c.category_id = cd.category_id AND cd.language_id = '" . $language_id . "')
                LEFT JOIN " . $this->db->table("categories_to_stores") . " c2s 
                    ON (c.category_id = c2s.category_id)
                WHERE c.category_id = '" . (int)$category_id . "'
                    AND c2s.store_id = '" . $store_id . "'
                    AND c.status = '1'"
            );
            $cache = $query->row;
            $this->cache->push($cache_key, $cache);
        }

        return $cache;
    }

    /**
     * @param int|array $parent_id
     * @param array|null $data
     * @return array
     * @throws AException
     */
    public function getCategories(int|array $parent_id = 0, ?array $data = [])
    {
        $limit = (int)$data['limit'];
        $language_id = (int)$data['language_id'] ?: (int)$this->config->get('storefront_language_id');
        $store_id = $data['store_id'] ?? (int)$this->config->get('config_store_id');
        $store_id = (int)$store_id;
        $cache_key = 'category.list.'. md5(var_export(func_get_args(),true))
            . '.store_' . $store_id . '_limit_' . $limit. '_lang_' . $language_id;
        $output = $this->cache->pull($cache_key);
        if ($output !== false) {
            return $output;
        }

        $sql = "SELECT *
                FROM " . $this->db->table("categories") . " c
                LEFT JOIN " . $this->db->table("category_descriptions") . " cd 
                    ON (c.category_id = cd.category_id AND cd.language_id = '" . $language_id . "')
                LEFT JOIN " . $this->db->table("categories_to_stores") . " c2s 
                    ON (c.category_id = c2s.category_id)
                WHERE c2s.store_id = '" . $store_id . "' AND c.status = 1 ";
        if($data['filter']['category_id']){
            $sql .= " AND c.category_id IN (" . implode(',', array_map("intval",(array)$data['filter']['category_id'])) . ")";
        }else {
            if (is_int($parent_id) && $parent_id >= 0) {
                $sql .= " AND c.parent_id = '" . (int)$parent_id . "'";
            } elseif (is_array($parent_id)) {
                $sql .= " AND c.parent_id IN (" . implode(',', $parent_id) . ")";
            }
        }
        $sql .= " ORDER BY c.sort_order, LCASE(cd.name) " . ((int)$limit ? "LIMIT " . (int)$limit : '') . " ";

        $query = $this->db->query( $sql );
        $output = $query->rows;
        //optimized selection of product counts.
        $category_ids = array_column($output, 'category_id');
        if ($category_ids) {
            $pCount = [];
            foreach($category_ids as $cId) {
                 $pCount[$cId] = $this->getProductCount($cId, $data);
            }

            foreach ($output as &$category) {
                $category['product_count'] = (int)$pCount[$category['category_id']];
            }
        }

        $this->cache->push($cache_key, $output);
        return $output;
    }

    /**
     * @param int $parent_id
     * @param array $data
     * @return int
     * @throws AException
     */
    function getProductCount($parent_id, $data = [])
    {
        $store_id = $data['store_id'] ?? (int)$this->config->get('config_store_id');
        $store_id = (int)$store_id;
        $cache_key = 'category.product.count.'.$parent_id.'.' . md5(var_export($data,true)).'.store_' . $store_id;
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
                    ON p.product_id = s.product_id AND s.store_id=" . $store_id;

        $data['filter']['manufacturer_id'] = filterIntegerIdList((array)$data['filter']['manufacturer_id']);
        if ($data['filter']['manufacturer_id']) {
            $sql .= " AND p.manufacturer_id IN (" . implode(',', (array)$data['filter']['manufacturer_id']) . ") ";
        }

        $sql .= " WHERE p2c.category_id in (" . implode(",", $idList) . ") 
                        AND p.status = '1' AND COALESCE(p.date_available,'1970-01-01')< NOW()";
        if($data['filter']['rating']) {
            $sql .= " AND ( SELECT FLOOR(AVG(r.rating))
                         FROM " . $this->db->table("reviews") . " r
                         WHERE p.product_id = r.product_id AND status = 1
                         GROUP BY r.product_id 
                 ) IN (" . implode(',', array_map('intval', (array)$data['filter']['rating'])) . ") ";
        }

        $query = $this->db->query($sql);
        $output = (int)$query->row['product_count'];
        $this->cache->push($cache_key, $output);
        return $output;
    }

    /**
     * @param array $data
     * @param string $mode
     *
     * @return array|int
     * @throws AException
     * @throws AException
     */
    public function getCategoriesData($data, $mode = 'default')
    {
        if ($data['language_id']) {
            $language_id = (int)$data['language_id'];
        } else {
            $language_id = (int)$this->config->get('storefront_language_id');
        }

        if ($data['store_id']) {
            $store_id = (int)$data['store_id'];
        } else {
            $store_id = (int)$this->config->get('config_store_id');
        }

        $cache_key = 'category.list.data.' . $mode
            . '.store_' . $store_id
            . '_lang_' . $language_id. md5(var_export($data, true));
        $cache = $this->cache->pull($cache_key);

        if ($cache !== false) {
            return $cache;
        }

        if ($mode == 'total_only') {
            $total_sql = 'count(*) as total';
        } else {
            $total_sql = "*, c.category_id ";
        }
        $where = (isset($data['parent_id']) ? " c.parent_id = '" . (int)$data['parent_id'] . "'" : '');
        //filter result by given ids array
        if ($data['filter_ids']) {
            $ids = filterIntegerIdList($data['filter_ids']);
            if($ids) {
                $where = " c.category_id IN (" . implode(', ', $ids) . ")";
            }
        }

        $where = $where ? 'WHERE ' . $where : '';

        $sql = "SELECT " . $total_sql . "
                FROM " . $this->db->table('categories') . " c
                LEFT JOIN " . $this->db->table('category_descriptions') . " cd
                    ON (c.category_id = cd.category_id AND cd.language_id = '" . $language_id . "')
                INNER JOIN " . $this->db->table('categories_to_stores') . " cs
                    ON (c.category_id = cs.category_id AND cs.store_id = '" . $store_id . "')
                " . $where;

        if (!empty($data['subsql_filter'])) {
            $sql .= ($where ? " AND " : 'WHERE ') . $data['subsql_filter'];
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
            $sql .= " ORDER BY " . $data['sort'];
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

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);
        $output = $query->rows;
        foreach($output as &$row){
            $row['products_count'] = $this->getProductCount($row['category_id']);
        }
        $this->cache->push($cache_key, $output);
        return $output;

    }

    /**
     * @return array
     * @throws AException
     * @throws AException
     */
    public function getAllCategories(?array $data = [])
    {
        return $this->getCategories(-1, $data);
    }

    /**
     * @param int|array $parent_id
     *
     * @return int
     * @throws AException
     */
    public function getTotalCategoriesByCategoryId(int|array$parent_id = 0)
    {
        $cacheKey = 'category.' . md5(var_export($parent_id, true)) . '.total';
        $output = $this->cache->pull($cacheKey);
        if($output!==false){
            return $output;
        }
        if( is_array($parent_id) ){
            $parent_id = filterIntegerIdList( $parent_id ) ?: [0];
        }

        $sql = "SELECT COUNT(*) AS total
                FROM " . $this->db->table("categories") . " c
                LEFT JOIN " . $this->db->table("categories_to_stores") . " c2s 
                    ON (c.category_id = c2s.category_id)
                WHERE c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
                    AND c.status = 1 ";
        if(is_int($parent_id)){
            $sql .= " AND c.parent_id = '" . (int)$parent_id . "'";
        }else{
            $sql .= " AND c.parent_id IN (" . implode(',',$parent_id) . ")";
        }

        $query = $this->db->query( $sql );
        $output = $query->row['total'];
        $this->cache->push($cacheKey,$output);
        return $output;
    }

    /**
     * Get Total products in categories
     *
     * @param array $categories
     *
     * @return int
     * @throws AException
     * @throws AException
     */
    public function getCategoriesProductsCount($categories = [])
    {
        $cacheKey = 'category.product.count.'.md5(var_export($categories, true));
        $output = $this->cache->pull($cacheKey);
        if($output!==false){
            return $output;
        }
        $categories = filterIntegerIdList($categories);
        if ($categories) {
            $query = $this->db->query(
                "SELECT COUNT(DISTINCT p2c.product_id) AS total
                FROM " . $this->db->table("products_to_categories") . " p2c
                INNER JOIN " . $this->db->table('products') . " p
                    ON p.product_id = p2c.product_id
                WHERE p.status = '1' 
                    AND COALESCE(p.date_available,'1970-01-01')< NOW()
                    AND p2c.category_id IN (" . implode(', ', $categories) . ");"
            );
            $output = (int)$query->row['total'];
        } else {
            $output = 0;
        }
        $this->cache->push($cacheKey,$output);
        return $output;
    }

    /**
     * Get Manufactures for specified categories
     *
     * @param array|null $categories
     * @param array|null $data
     * @return array
     * @throws AException
     */
    public function getCategoriesBrands(?array $categories = [], ?array $data = [])
    {
        $categories = filterIntegerIdList($categories);
        $data['filter']['rating'] = filterIntegerIdList((array)$data['filter']['rating']);
        $data['filter']['manufacturer_id'] = filterIntegerIdList((array)$data['filter']['manufacturer_id']);

        $cacheKey = 'category.brands.' . md5(var_export(func_get_args(),true));
        $cache = $this->cache->pull($cacheKey);
        if ($cache !== false) {
            return $cache;
        }
        $sql = "SELECT DISTINCT p.manufacturer_id, m.name, COUNT(p.product_id) as product_count
                FROM " . $this->db->table('manufacturers') . " as  m ";
        $sql .= "INNER JOIN " . $this->db->table('products') . " p 
                    ON (p.manufacturer_id = m.manufacturer_id
                        AND p.product_id 
                            IN (SELECT DISTINCT p2c.product_id
                               FROM " . $this->db->table('products_to_categories') . " p2c
                               INNER JOIN " . $this->db->table('products') . " p 
                                    ON ( p.product_id = p2c.product_id 
                                        AND p.status = '1'
                                        AND COALESCE(p.date_available,'1970-01-01')< NOW() )";
        if($categories) {
            $sql .= "WHERE p2c.category_id IN (" . implode(', ', $categories) . ") ";
        }
        $sql .= ") )";

        $sql .= " WHERE LENGTH(m.name)>0 ";
        if($data['filter']['rating']) {
            $sql .= " AND ( SELECT FLOOR(AVG(r.rating))
                         FROM " . $this->db->table("reviews") . " r
                         WHERE p.product_id = r.product_id AND status = 1
                         GROUP BY r.product_id 
                 ) IN (" . implode(',', array_map('intval', (array)$data['filter']['rating'])) . ") ";
        }

        if($data['filter']['manufacturer_id']){
            $sql .= " AND m.manufacturer_id IN (".implode(',',(array)$data['filter']['manufacturer_id']).") ";
        }

        $sql .= " GROUP BY p.manufacturer_id, m.name
                 ORDER BY m.name";
        $query = $this->db->query($sql);
        $output = $query->rows;
        $this->cache->push($cacheKey, $output);
        return $output;
    }

    /**
     * @param int $category_id
     *
     * @return string
     * @throws AException
     * @throws AException
     */
    public function buildPath($category_id)
    {
        $cacheKey = 'category.path.'.$category_id;
        $output = $this->cache->pull($cacheKey);
        if($output !== false){
            return $output;
        }

        $query = $this->db->query(
            "SELECT c.category_id, c.parent_id
            FROM " . $this->db->table("categories") . " c
            WHERE c.category_id = '" . (int)$category_id . "'
            ORDER BY c.sort_order"
        );

        $category_info = $query->row;
        if ($category_info['parent_id']) {
            $output = $this->buildPath($category_info['parent_id']) . "_" . $category_info['category_id'];
        } else {
            $output = $category_info['category_id'];
        }
        $this->cache->push($cacheKey, $output);
        return $output;
    }

    /**
     * @param int|array $parentId
     *
     * @param string $mode - can be empty or "active_only"
     * @param bool $dig
     * @return array
     * @throws AException
     */
    public function getChildrenIDs(int|array $parentId, $mode = 'active_only', $dig = true)
    {
        if (is_array($parentId) && !$parentId) {
            return [];
        }
        $cacheKey = 'category.children.' . md5(var_export($parentId,true)) . '.' . preformatTextID($mode);
        $cache = $this->cache->pull($cacheKey);
        if ($cache !== false) {
            return $cache;
        }

        $storeId = $this->config->get('config_store_id');

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

        if ($mode == 'active_only') {
            $sql .= " AND c.status=1";
        }
        $result = $this->db->query($sql);
        $output = [];
        if($dig) {
            foreach ($result->rows as $category) {
                $output[] = (int)$category['category_id'];
                $output = array_merge($output, $this->getChildrenIDs((int)$category['category_id']));
            }
        }else{
            $output = array_column($result->rows, 'category_id');
        }
        $this->cache->push($cacheKey, $output);
        return $output;
    }

    /** Function builds one dimensional category tree based on given array
     *
     * @param array $categoryList
     * @param array $data
     *
     * @return array
     * @throws AException
     */
    public function buildCategoryTree($categoryList = [], $data = [])
    {
        $cacheKey = 'category.build.tree.'.var_export(array_merge($categoryList,$data), true);
        $output = $this->cache->pull($cacheKey);
        if($output !== false){
            return $output;
        }
        $categoryListIds = array_map('intval', array_column($categoryList,'category_id'));
        $categoryList =  array_combine($categoryListIds, $categoryList );
        $output = [];
        foreach ($categoryList as $category) {
            if(in_array((int)$category['category_id'],(array)$this->data['processed_childrenIds'])
                || in_array((int)$category['parent_id'], $categoryListIds)
            ){
                continue;
            }
            if(!isset($category['product_count'])) {
                $category['product_count'] = $this->getProductCount($category['category_id'], $data);
            }
            if(!$category['product_count']){
                continue;
            }

            $path = $this->buildPath($category['category_id']);
            $category['path'] = $path;
            $path = explode("_", $path);
            array_pop($path);
            $category['parents'] = $path ?: [0];

            $childrenIds = !$data['root_level'] ? $this->getChildrenIDs($category['category_id'], 'active_only',true) : [];
            if($childrenIds) {
                $data['filter'] = array_merge((array)$data['filter'], [ 'category_id'     => $childrenIds ]);
                $cList = $this->getAllCategories($data );
                $childrenList = $this->buildCategoryTree($cList, $data);
                $category['children'] = $childrenList ?: [];
                $this->data['processed_childrenIds'] = array_merge((array)$this->data['processed_childrenIds'],$childrenIds);
                $this->data['processed_childrenIds'][] = $category['category_id'];
            }
            $output[] = $category;
        }
        $this->cache->push($cacheKey, $output);
        return $output;
    }

    /** Function builds one multi-dimensional (nested) category tree for menu
     *
     * @param int $parent_id
     *
     * @return array
     * @throws AException
     */
    public function buildNestedCategoryList($parent_id = 0, ?array $options = [])
    {
        $output = [];
        foreach ($this->data['all_categories'] as $category) {
            $category['current'] = false;

            if ((int)$category['parent_id'] != (int)$parent_id) {
                continue;
            }

            $category['children'] = $this->buildNestedCategoryList($category['category_id']);
            $thumbnail = $this->thumbnails[$category['category_id']];
            $category['thumb'] = $thumbnail['thumb_url'];
            $category['icon'] = $thumbnail['resource_id'];
            //get product counts from children levels.
            if (count($category['children']) && !$options['no_sum_children']) {
                $category['product_count'] += array_sum(array_column($category['children'], 'product_count'));
            }
            $category['href'] = $this->html->getSEOURL('product/category', '&path=' . $category['path'], '&encode');
            //mark current category
            if (in_array($category['category_id'], $this->path)) {
                $category['current'] = true;
            }
            $output[] = $category;
        }
        return $output;
    }

    /**
     * @param array $categoryIds
     * @param int|null $limit
     * @return array|mixed
     * @throws AException
     */
    public function getFeaturedCategoryProducts(array $categoryIds, ?int $limit = 2)
    {
        if (!$categoryIds) {
            return [];
        }
        $language_id = (int)$this->config->get('storefront_language_id');
        $store_id = (int)$this->config->get('config_store_id');
        $cacheKey = 'product.category.featured.' . $language_id . $store_id . $limit;
        $output = $this->cache->pull($cacheKey);
        if ($output !== false) {
            return $output;
        }
        $output = [];
        foreach ($categoryIds as $categoryId) {
            $categoryId = (int)$categoryId;
            if (!$categoryId) {
                continue;
            }

            $output[$categoryId] = [];

            $sql = "SELECT '1' as featured, pd.*, p.*
                    FROM " . $this->db->table("products_featured") . " f
                    INNER JOIN " . $this->db->table("products_to_categories") . " p2c 
                        ON (p2c.product_id = f.product_id AND p2c.category_id = '" . $categoryId . "')
                    INNER JOIN " . $this->db->table("products_to_stores") . " p2s 
                        ON (f.product_id = p2s.product_id AND p2s.store_id = '" . $store_id . "') 
                    INNER JOIN " . $this->db->table("products") . " p
                        ON (f.product_id = p.product_id AND p.status='1' AND p.date_available <= NOW())
                    LEFT JOIN " . $this->db->table("product_descriptions") . " pd
                        ON (f.product_id = pd.product_id AND pd.language_id = '" . $language_id . "')
                    ORDER BY p.sort_order, p.date_available DESC 
                    LIMIT " . $limit;
            $output[$categoryId] = $this->db->query($sql)->rows;

            //if  category have no featured products
            if (count($output[$categoryId]) < $limit) {
                $categoryIds = $this->getChildrenIDs($categoryId);
                $categoryIds[] = (int)$categoryId;
                $sql = "SELECT pd.*, p.*, (CASE WHEN pf.product_id IS NOT NULL THEN 1 ELSE 0 END) as `featured`
                        FROM " . $this->db->table("products") . " p                            
                        INNER JOIN " . $this->db->table("products_to_stores") . " p2s 
                            ON (p.product_id = p2s.product_id AND p2s.store_id = '" . $store_id . "')
                        INNER JOIN " . $this->db->table("products_to_categories") . " p2c 
                            ON (p2c.product_id = p.product_id AND p2c.category_id IN (".(implode(',', $categoryIds)).")) 
                        LEFT JOIN " . $this->db->table("products_featured") . " pf
                            ON (pf.product_id = p.product_id )
                        LEFT JOIN " . $this->db->table("product_descriptions") . " pd
                            ON (p.product_id = pd.product_id AND pd.language_id = '" . $language_id . "')
                        WHERE pf.product_id IS NULL AND p.status='1' AND p.date_available <= NOW()
                        ORDER BY (CASE WHEN pf.product_id IS NOT NULL THEN 1 ELSE 0 END), p.sort_order, p.date_available DESC 
                        LIMIT " . $limit;
                $output[$categoryId] += $this->db->query($sql)->rows;
            }
        }
        $this->cache->push($cacheKey, $output);
        return $output;
    }


    /**
     * Get Total products in categories
     *
     * @param array $productIds
     *
     * @return array
     * @throws AException
     */
    public function getCategoriesOfProducts($productIds = [])
    {
        $productIds = filterIntegerIdList($productIds);

        if (!$productIds) {
            return [];
        }
        $cacheKey = 'product.categories.' . implode(',', $productIds);
        $output = $this->cache->pull($cacheKey);
        if($output !== false){
            return $output;
        }

        $sql = "SELECT p.product_id, c.category_id, cd.name 
            FROM " . $this->db->table("products_to_categories") . " p2c
            INNER JOIN " . $this->db->table('products') . " p
                ON p.product_id = p2c.product_id
            INNER JOIN " . $this->db->table('categories') . " c
                ON c.category_id = p2c.category_id AND c.status = 1
            LEFT JOIN " . $this->db->table("category_descriptions") . " cd
                ON (cd.category_id = p2c.category_id 
                    AND cd.language_id = " . (int)$this->language->getLanguageID() .")                    
            WHERE p.status = 1  
                AND COALESCE(p.date_available,'1970-01-01')< NOW()
                AND p.product_id IN (" . implode(', ', $productIds) . ")
            ORDER BY cd.name;";

        $query = $this->db->query( $sql );
        $output = $query->rows;
        $this->cache->push($cacheKey, $output);
        return $output;
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
            $language_id = (int) $this->language->getLanguageID();
        }
        $cacheKey = 'category.get.path.'.$category_id.'.'.$language_id;
        $output = $this->cache->pull($cacheKey);
        if($output !== false){
            return $output;
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
            $output = $this->getPath(
                    $category_info['parent_id'],
                    $language_id
                ).$this->language->get('text_separator').$category_info['name'];
        } else {
            $output = $category_info['name'];
        }
        $this->cache->push($cacheKey, $output);
        return $output;
    }
}
