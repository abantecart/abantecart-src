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
     * @param int $parent_id
     * @param int $limit
     *
     * @return array
     * @throws AException
     * @throws AException
     */
    public function getCategories($parent_id = 0, $limit = 0)
    {
        $language_id = (int)$this->config->get('storefront_language_id');
        $store_id = (int)$this->config->get('config_store_id');
        $cache_key = 'category.list.' . $parent_id
            . '.store_' . $store_id
            . '_limit_' . $limit
            . '_lang_' . $language_id;
        $cache = $this->cache->pull($cache_key);

        if ($cache === false) {
            $query = $this->db->query(
                "SELECT *
                FROM " . $this->db->table("categories") . " c
                LEFT JOIN " . $this->db->table("category_descriptions") . " cd 
                    ON (c.category_id = cd.category_id AND cd.language_id = '" . $language_id . "')
                LEFT JOIN " . $this->db->table("categories_to_stores") . " c2s 
                    ON (c.category_id = c2s.category_id)
                WHERE " . ($parent_id < 0 ? "" : "c.parent_id = '" . (int)$parent_id . "' AND ") . "
                     c2s.store_id = '" . $store_id . "' AND c.status = '1'
                ORDER BY c.sort_order, LCASE(cd.name)
                " . ((int)$limit ? "LIMIT " . (int)$limit : '') . " "
            );
            $cache = $query->rows;
            //optimized selection of product counts.
            $category_ids = array_column($cache, 'category_id');
            if (count($category_ids)) {
                $query = $this->db->query(
                    "SELECT p2c.category_id as category_id, count(*) as product_count
                    FROM " . $this->db->table('products_to_categories') . " p2c
                    INNER JOIN " . $this->db->table('products') . " p 
                        ON p.product_id = p2c.product_id
                    WHERE p2c.category_id in (" . implode(",", $category_ids) . ") 
                        AND p.status = '1' 
                        AND COALESCE(p.date_available,'1970-01-01')< NOW()
                    GROUP BY p2c.category_id "
                );

                foreach ($query->rows as $row) {
                    foreach ($cache as $i => $category) {
                        if ($row['category_id'] == $category['category_id']) {
                            $cache[$i]['product_count'] = $row['product_count'];
                        }
                    }
                }
            }

            $this->cache->push($cache_key, $cache);
        }
        return $cache;
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

        $cache_key = 'category.list.' . $mode
            . '.store_' . $store_id
            . '_lang_' . $language_id. md5(var_export($data, true));
        $cache = $this->cache->pull($cache_key);

        if ($cache !== false) {
            return $cache;
        }

        if ($mode == 'total_only') {
            $total_sql = 'count(*) as total';
        } else {
            $total_sql = "*,
                          c.category_id,
                          (SELECT count(*) as cnt
                            FROM " . $this->db->table('products_to_categories') . " p2c
                            INNER JOIN " . $this->db->table('products') . " p ON p.product_id = p2c.product_id
                            WHERE p2c.category_id = c.category_id 
                                AND p.status = '1' 
                                AND COALESCE(p.date_available,'1970-01-01')< NOW() ) as products_count ";
        }
        $where = (isset($data['parent_id']) ? " c.parent_id = '" . (int)$data['parent_id'] . "'" : '');
        //filter result by given ids array
        if ($data['filter_ids']) {
            $ids = array_unique(array_map('intval', (array)$data['filter_ids']));
            $where = " c.category_id IN (" . implode(', ', $ids) . ")";
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
        $this->cache->push($cache_key, $query->rows);
        return $query->rows;

    }

    /**
     * @return array
     * @throws AException
     * @throws AException
     */
    public function getAllCategories()
    {
        return $this->getCategories(-1);
    }

    /**
     * @param int $parent_id
     *
     * @return int
     * @throws AException
     * @throws AException
     */
    public function getTotalCategoriesByCategoryId($parent_id = 0)
    {
        $query = $this->db->query(
            "SELECT COUNT(*) AS total
            FROM " . $this->db->table("categories") . " c
            LEFT JOIN " . $this->db->table("categories_to_stores") . " c2s 
                ON (c.category_id = c2s.category_id)
            WHERE c.parent_id = '" . (int)$parent_id . "'
                AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
                AND c.status = '1'"
        );

        return $query->row['total'];
    }

    /**
     * @param int $parent_id
     * @param string $path
     *
     * @return array
     * @throws AException
     * @throws AException
     * @deprecated since 1.1.7
     *
     */
    public function getCategoriesDetails($parent_id = 0, $path = '')
    {
        $language_id = (int)$this->config->get('storefront_language_id');
        $store_id = (int)$this->config->get('config_store_id');

        $resource = new AResource('image');
        $cache_key = 'category.details.' . $parent_id . '.store_' . $store_id . '_lang_' . $language_id;
        $categories = $this->cache->pull($cache_key);
        if ($categories !== false) {
            return $categories;
        }
        $categories = [];

        $this->load->model('catalog/product');
        $this->load->model('catalog/manufacturer');

        $results = $this->getCategories($parent_id);
        foreach ($results as $result) {
            if (!$path) {
                $new_path = $result['category_id'];
            } else {
                $new_path = $path . '_' . $result['category_id'];
            }

            $prods = $brands = [];

            if ($parent_id == 0) {
                $data['filter'] = [];
                $data['filter']['category_id'] = $result['category_id'];
                $data['filter']['status'] = 1;

                $prods = $this->model_catalog_product->getProducts($data);

                foreach ($prods as $prod) {
                    if ($prod['manufacturer_id']) {
                        $brand = $this->model_catalog_manufacturer->getManufacturer($prod['manufacturer_id']);
                        $brands[$prod['manufacturer_id']] = [
                            'name' => $brand['name'],
                            'href' => $this->html->getSEOURL(
                                'product/manufacturer', '&manufacturer_id=' . $brand['manufacturer_id'], '&encode'
                            ),
                        ];
                    }
                }
            }

            $thumbnail = $resource->getMainThumb(
                'categories',
                $result['category_id'],
                $this->config->get('config_image_category_width'),
                $this->config->get('config_image_category_height')
            );

            $categories[] = [
                'category_id'   => $result['category_id'],
                'name'          => $result['name'],
                'children'      => $this->getCategoriesDetails($result['category_id'], $new_path),
                'href'          => $this->html->getSEOURL('product/category', '&path=' . $new_path, '&encode'),
                'brands'        => $brands,
                'product_count' => count($prods),
                'thumb'         => $thumbnail['thumb_url'],
            ];
        }
        $this->cache->push($cache_key, $categories);
        return $categories;
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
        $categories = array_map('intval', (array)$categories);
        $categories = array_unique($categories);
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
        return $output;
    }

    /**
     * Get Manufactures for specified categories
     *
     * @param array $categories
     *
     * @return array
     * @throws AException
     * @throws AException
     */
    public function getCategoriesBrands($categories = [])
    {
        $categories = (array)$categories;
        foreach ($categories as &$val) {
            $val = (int)$val;
        }
        unset($val);

        $categories = array_unique($categories);
        if ($categories) {
            $sql = "SELECT p.manufacturer_id, m.name, COUNT(p.product_id) as product_count
                    FROM " . $this->db->table('products') . " p
                    LEFT JOIN " . $this->db->table('manufacturers') . " m ON p.manufacturer_id = m.manufacturer_id
                    WHERE p.product_id IN (SELECT DISTINCT p2c.product_id
                                           FROM " . $this->db->table('products_to_categories') . " p2c
                                           INNER JOIN " . $this->db->table('products') . " p 
                                                ON ( p.product_id = p2c.product_id 
                                                    AND p.status = '1'
                                                    AND COALESCE(p.date_available,'1970-01-01')< NOW() )
                                           WHERE p2c.category_id IN (" . implode(', ', $categories) . "))
                    AND LENGTH(m.name)>0
                    GROUP BY p.manufacturer_id, m.name
                    ORDER BY m.name";
            $query = $this->db->query($sql);
            $output = $query->rows;
        } else {
            $output = [];
        }
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
        $query = $this->db->query(
            "SELECT c.category_id, c.parent_id
            FROM " . $this->db->table("categories") . " c
            WHERE c.category_id = '" . (int)$category_id . "'
            ORDER BY c.sort_order"
        );

        $category_info = $query->row;
        if ($category_info['parent_id']) {
            return $this->buildPath($category_info['parent_id']) . "_" . $category_info['category_id'];
        } else {
            return $category_info['category_id'];
        }
    }

    /**
     * @param int $categoryId
     *
     * @param string $mode - can be empty or "active_only"
     *
     * @return array
     * @throws AException
     * @throws AException
     */
    public function getChildrenIDs($categoryId, $mode = 'active_only')
    {
        $categoryId = (int)$categoryId;
        if (!$categoryId) {
            return [];
        }
        $cacheKey = 'category.children.' . $categoryId . '.' . preformatTextID($mode);
        $cache = $this->cache->pull($cacheKey);
        if (isset($cache) && $cache !== false) {
            return $cache;
        }

        $storeId = $this->config->get('config_store_id');

        $sql = "SELECT c.category_id
                FROM " . $this->db->table('categories') . " c
                LEFT JOIN " . $this->db->table('categories_to_stores') . " c2s
                    ON c2s.category_id = c.category_id
                WHERE c2s.store_id = " . $storeId . "
                    AND c.parent_id=" . $categoryId;
        if ($mode == 'active_only') {
            $sql .= " AND c.status=1";
        }
        $result = $this->db->query($sql);
        $output = [];
        foreach ($result->rows as $category) {
            $output[] = (int)$category['category_id'];
            $output = array_merge($output, $this->getChildrenIDs($category['category_id']));
        }
        $this->cache->push($cacheKey, $output);
        return $output;
    }

    /** Function builds one dimensional category tree based on given array
     *
     * @param array $all_categories
     * @param int $parent_id
     * @param string $path
     *
     * @return array
     * @throws AException
     */
    public function buildCategoryTree($all_categories = [], $parent_id = 0, $path = '')
    {
        $output = [];
        foreach ($all_categories as $category) {
            if ((int)$parent_id != $category['parent_id']) {
                continue;
            }
            $category['path'] = $path ? $path . '_' . $category['category_id'] : $category['category_id'];
            $category['parents'] = explode("_", $category['path']);
            //dig into level
            $category['level'] = sizeof($category['parents']) - 1;
            if ($category['category_id'] == $this->category_id) {
                //mark root
                $this->selected_root_id = $category['parents'][0];
            }
            $output[] = $category;
            $output = array_merge(
                $output, $this->buildCategoryTree($all_categories, $category['category_id'], $category['path'])
            );
        }
        if ($parent_id == 0) {
            //place result into memory for future usage (for menu. see below)
            $this->data['all_categories'] = $output;
            // cut list and expand only selected tree branch
            $cutted_tree = [];
            foreach ($output as $category) {
                if ($category['parent_id'] != 0 && !in_array($this->selected_root_id, $category['parents'])) {
                    continue;
                }
                $category['href'] = $this->html->getSEOURL('product/category', '&path=' . $category['path'], '&encode');
                $cutted_tree[] = $category;
            }
            return $cutted_tree;
        } else {
            return $output;
        }
    }

    /** Function builds one multi-dimensional (nested) category tree for menu
     *
     * @param int $parent_id
     *
     * @return array
     * @throws AException
     */
    public function buildNestedCategoryList($parent_id = 0)
    {
        $output = [];
        foreach ($this->data['all_categories'] as $category) {
            $category['current'] = false;
            if ($category['parent_id'] != $parent_id) {
                continue;
            }
            $category['children'] = $this->buildNestedCategoryList($category['category_id']);
            $thumbnail = $this->thumbnails[$category['category_id']];
            $category['thumb'] = $thumbnail['thumb_url'];
            $category['icon'] = $thumbnail['resource_id'];
            //get product counts from children levels.
            if (count($category['children'])) {
                foreach ($category['children'] as $child) {
                    $category['product_count'] += $child['product_count'];
                }
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
                $sql = "SELECT pd.*, p.*
                        FROM " . $this->db->table("products") . " p                            
                        INNER JOIN " . $this->db->table("products_to_stores") . " p2s 
                            ON (p.product_id = p2s.product_id AND p2s.store_id = '" . $store_id . "')
                        INNER JOIN " . $this->db->table("products_to_categories") . " p2c 
                            ON (p2c.product_id = p.product_id AND p2c.category_id = '" . $categoryId . "') 
                        LEFT JOIN " . $this->db->table("products_featured") . " pf
                            ON (pf.product_id = p.product_id )
                        LEFT JOIN " . $this->db->table("product_descriptions") . " pd
                            ON (p.product_id = pd.product_id AND pd.language_id = '" . $language_id . "')
                        WHERE pf.product_id IS NULL AND p.status='1' AND p.date_available <= NOW()
                        ORDER BY p.sort_order, p.date_available DESC 
                        LIMIT " . $limit;
                $output[$categoryId] += $this->db->query($sql)->rows;
            }
        }
        $this->cache->push($cacheKey, $output);
        return $output;
    }
}
