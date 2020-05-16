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
/** @noinspection PhpUndefinedClassInspection */

/**
 * Class ModelCatalogCategory
 *
 * @property ModelCatalogProduct      $model_catalog_product
 * @property ModelCatalogManufacturer $model_catalog_manufacturer
 */
class ModelCatalogCategory extends Model
{
    /**
     * @param int $category_id
     *
     * @return array
     */
    public function getCategory($category_id)
    {
        $store_id = (int)$this->config->get('config_store_id');
        $language_id = (int)$this->config->get('storefront_language_id');

        $cache_key = 'product.listing.category.'.(int)$category_id.'.store_'.$store_id.'_lang_'.$language_id;
        $cache = $this->cache->pull($cache_key);
        if ($cache === false) {
            $query = $this->db->query("SELECT DISTINCT *,
										(SELECT COUNT(p2c.product_id) as cnt
										 FROM ".$this->db->table('products_to_categories')." p2c
										 INNER JOIN ".$this->db->table('products')." p ON p.product_id = p2c.product_id AND p.status = '1'
										 WHERE  p2c.category_id = c.category_id) as products_count
									FROM ".$this->db->table("categories")." c
									LEFT JOIN ".$this->db->table("category_descriptions")." cd ON (c.category_id = cd.category_id AND cd.language_id = '".$language_id."')
									LEFT JOIN ".$this->db->table("categories_to_stores")." c2s ON (c.category_id = c2s.category_id)
									WHERE c.category_id = '".(int)$category_id."'
										AND c2s.store_id = '".$store_id."'
										AND c.status = '1'");
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
     */
    public function getCategories($parent_id = 0, $limit = 0)
    {
        $language_id = (int)$this->config->get('storefront_language_id');
        $store_id = (int)$this->config->get('config_store_id');
        $cache_key = 'category.list.'.$parent_id.'.store_'.$store_id.'_limit_'.$limit.'_lang_'.$language_id;
        $cache = $this->cache->pull($cache_key);

        if ($cache === false) {
            $query = $this->db->query("SELECT *
										FROM ".$this->db->table("categories")." c
										LEFT JOIN ".$this->db->table("category_descriptions")." cd ON (c.category_id = cd.category_id AND cd.language_id = '".$language_id."')
										LEFT JOIN ".$this->db->table("categories_to_stores")." c2s ON (c.category_id = c2s.category_id)
										WHERE ".($parent_id < 0 ? "" : "c.parent_id = '".(int)$parent_id."' AND ")."
										     c2s.store_id = '".$store_id."' AND c.status = '1'
										ORDER BY c.sort_order, LCASE(cd.name)
										".((int)$limit ? "LIMIT ".(int)$limit : '')." ");
            $cache = $query->rows;
            //optimized selection of product counts.
            $category_ids = array();
            foreach ($cache as $row) {
                $category_ids[] = $row['category_id'];
            }
            if (count($category_ids)) {
                $query = $this->db->query("SELECT p2c.category_id as category_id, count(*) as product_count
						  			    FROM ".$this->db->table('products_to_categories')." p2c
						  				INNER JOIN ".$this->db->table('products')." p ON p.product_id = p2c.product_id
						  				WHERE p2c.category_id in (".implode(",", $category_ids).") AND p.status = '1' GROUP BY p2c.category_id
						  			  ");

                foreach ($query->rows as $row) {
                    foreach ($cache as $i => $categ) {
                        if ($row['category_id'] == $categ['category_id']) {
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
     * @param array  $data
     * @param string $mode
     *
     * @return array|int
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

        if ($mode == 'total_only') {
            $total_sql = 'count(*) as total';
        } else {
            $total_sql = "*,
						  c.category_id,
						  (SELECT count(*) as cnt
						  	FROM ".$this->db->table('products_to_categories')." p2c
						  	INNER JOIN ".$this->db->table('products')." p ON p.product_id = p2c.product_id
						  	WHERE p2c.category_id = c.category_id AND p.status = '1') as products_count ";
        }
        $where = (isset($data['parent_id']) ? " c.parent_id = '".(int)$data['parent_id']."'" : '');
        //filter result by given ids array
        if ($data['filter_ids']) {
            $ids = array();
            foreach ($data['filter_ids'] as $id) {
                $id = (int)$id;
                if ($id) {
                    $ids[] = $id;
                }
            }
            $where = " c.category_id IN (".implode(', ', $ids).")";
        }

        $where = $where ? 'WHERE '.$where : '';

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

        //If for total, we done building the query
        if ($mode == 'total_only') {
            $query = $this->db->query($sql);
            return $query->row['total'];
        }

        $sort_data = array(
            'name'       => 'cd.name',
            'status'     => 'c.status',
            'sort_order' => 'c.sort_order',
        );

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

            $sql .= " LIMIT ".(int)$data['start'].",".(int)$data['limit'];
        }

        $query = $this->db->query($sql);
        $category_data = array();
        foreach ($query->rows as $result) {
            $category_data[] = array(
                'category_id'    => $result['category_id'],
                'name'           => $result['name'],
                'status'         => $result['status'],
                'sort_order'     => $result['sort_order'],
                'products_count' => $result['products_count'],

            );
        }
        return $category_data;
    }

    /**
     * @return array
     */
    public function getAllCategories()
    {
        return $this->getCategories(-1);
    }

    /**
     * @param int $parent_id
     *
     * @return int
     */
    public function getTotalCategoriesByCategoryId($parent_id = 0)
    {
        $query = $this->db->query("SELECT COUNT(*) AS total
									FROM ".$this->db->table("categories")." c
									LEFT JOIN ".$this->db->table("categories_to_stores")." c2s ON (c.category_id = c2s.category_id)
									WHERE c.parent_id = '".(int)$parent_id."'
										AND c2s.store_id = '".(int)$this->config->get('config_store_id')."'
										AND c.status = '1'");

        return $query->row['total'];
    }

    /**
     * @deprecated since 1.1.7
     *
     * @param int    $parent_id
     * @param string $path
     *
     * @return array
     */
    public function getCategoriesDetails($parent_id = 0, $path = '')
    {
        $language_id = (int)$this->config->get('storefront_language_id');
        $store_id = (int)$this->config->get('config_store_id');

        $resource = new AResource('image');
        $cache_key = 'category.details.'.$parent_id.'.store_'.$store_id.'_lang_'.$language_id;
        $categories = $this->cache->pull($cache_key);
        if ($categories !== false) {
            return $categories;
        }

        $this->load->model('catalog/product');
        $this->load->model('catalog/manufacturer');

        $results = $this->getCategories($parent_id);

        foreach ($results as $result) {
            if (!$path) {
                $new_path = $result['category_id'];
            } else {
                $new_path = $path.'_'.$result['category_id'];
            }

            $prods = $brands = array();

            if ($parent_id == 0) {

                $data['filter'] = array();
                $data['filter']['category_id'] = $result['category_id'];
                $data['filter']['status'] = 1;

                $prods = $this->model_catalog_product->getProducts($data);

                foreach ($prods as $prod) {
                    if ($prod['manufacturer_id']) {
                        $brand = $this->model_catalog_manufacturer->getManufacturer($prod['manufacturer_id']);
                        $brands[$prod['manufacturer_id']] = array(
                            'name' => $brand['name'],
                            'href' => $this->html->getSEOURL('product/manufacturer', '&manufacturer_id='.$brand['manufacturer_id'], '&encode'),
                        );
                    }
                }
            }

            $thumbnail = $resource->getMainThumb('categories',
                $result['category_id'],
                $this->config->get('config_image_category_width'),
                $this->config->get('config_image_category_height'));

            $categories[] = array(
                'category_id'   => $result['category_id'],
                'name'          => $result['name'],
                'children'      => $this->getCategoriesDetails($result['category_id'], $new_path),
                'href'          => $this->html->getSEOURL('product/category', '&path='.$new_path, '&encode'),
                'brands'        => $brands,
                'product_count' => count($prods),
                'thumb'         => $thumbnail['thumb_url'],
            );
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
     */
    public function getCategoriesProductsCount($categories = array())
    {
        $categories = (array)$categories;
        foreach ($categories as &$val) {
            $val = (int)$val;
        }
        unset($val);
        $categories = array_unique($categories);
        if ($categories) {
            $query = $this->db->query("SELECT COUNT(DISTINCT p2c.product_id) AS total
										FROM ".$this->db->table("products_to_categories")." p2c
										INNER JOIN ".$this->db->table('products')." p
											ON p.product_id = p2c.product_id
										WHERE p.status = '1' AND p2c.category_id IN (".implode(', ', $categories).");");
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
     */
    public function getCategoriesBrands($categories = array())
    {
        $categories = (array)$categories;
        foreach ($categories as &$val) {
            $val = (int)$val;
        }
        unset($val);

        $categories = array_unique($categories);
        if ($categories) {
            $sql = "SELECT DISTINCT p.manufacturer_id, m.name
				FROM ".$this->db->table('products')." p
				LEFT JOIN ".$this->db->table('manufacturers')." m ON p.manufacturer_id = m.manufacturer_id
				WHERE p.product_id IN (SELECT DISTINCT p2c.product_id
									   FROM ".$this->db->table('products_to_categories')." p2c
									   INNER JOIN ".$this->db->table('products')." p ON p.product_id = p2c.product_id AND p.status = '1'
									   WHERE p2c.category_id IN (".implode(', ', $categories)."));";
            $query = $this->db->query($sql);
            $output = $query->rows;
        } else {
            $output = array();
        }
        return $output;
    }

    /**
     * @param int $category_id
     *
     * @return string
     */
    public function buildPath($category_id)
    {
        $query = $this->db->query("SELECT c.category_id, c.parent_id
		                            FROM ".$this->db->table("categories")." c
		                            WHERE c.category_id = '".(int)$category_id."'
		                            ORDER BY c.sort_order");

        $category_info = $query->row;
        if ($category_info['parent_id']) {
            return $this->buildPath($category_info['parent_id'])."_".$category_info['category_id'];
        } else {
            return $category_info['category_id'];
        }
    }
}
