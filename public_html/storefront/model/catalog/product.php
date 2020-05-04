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

class ModelCatalogProduct extends Model
{
    /**
     * @param int $product_id
     *
     * @return array
     */
    public function getProduct($product_id)
    {
        if (!(int)$product_id) {
            return array();
        }
        $query = $this->db->query(
            "SELECT DISTINCT *,
                        pd.name AS name,
                        m.name AS manufacturer,
                        ss.name AS stock_status,
                        stock_checkout,
                        lcd.unit as length_class_name, ".
            $this->_sql_avg_rating_string().", ".
            $this->_sql_final_price_string()." ".
            $this->_sql_join_string().
            " LEFT JOIN ".$this->db->table("length_class_descriptions")." lcd
                                    ON (p.length_class_id = lcd.length_class_id
                                        AND lcd.language_id = '".(int)$this->config->get('storefront_language_id')."')
                WHERE p.product_id = '".(int)$product_id."'
                        AND p2s.store_id = '".(int)$this->config->get('config_store_id')."'
                        AND p.date_available <= NOW() AND p.status = '1'");
        return $query->row;
    }

    /**
     * Check if product or any option value require tracking stock subtract = 1
     *
     * @param int $product_id
     *
     * @return int
     */
    public function isStockTrackable($product_id)
    {
        if (!(int)$product_id) {
            return 0;
        }
        $track_status = 0;
        //check product option values
        $query = $this->db->query("SELECT pov.product_option_value_id, pov.subtract AS subtract
                                    FROM ".$this->db->table("product_options")." po
                                    LEFT JOIN ".$this->db->table("product_option_values")." pov
                                        ON (po.product_option_id = pov.product_option_id)
                                    WHERE po.product_id = '".(int)$product_id."'  AND po.status = 1");

        foreach ($query->rows as $row) {
            $track_status += (int)$row['subtract'];
        }
        //if no options - check whole product subtract
        if (!$track_status) {
            //check main product
            $query = $this->db->query("SELECT subtract
                                    FROM ".$this->db->table("products")." p
                                    WHERE p.product_id = '".(int)$product_id."'");

            $track_status = (int)$query->row['subtract'];
        }
        return $track_status;
    }

    /**
     * Returns array with stock information
     *
     * @since 1.2.7
     *
     * @param array $product_ids
     *
     * @return array
     */
    public function getProductsStockInfo($product_ids = array())
    {
        if (!$product_ids || !is_array($product_ids)) {
            return array();
        }

        $ids = array();
        foreach ($product_ids as $id) {
            $id = (int)$id;
            if (!$id) {
                continue;
            }
            $ids[] = $id;
        }

        if (!$ids) {
            return array();
        }

        $cache_key = 'product.stock_info.'.md5(implode('', $ids));
        $cache = $this->cache->pull($cache_key);
        if ($cache !== false) {
            return $cache;
        }

        $sql = "SELECT p.product_id,
                        p.subtract,
                        SUM(COALESCE(pov.subtract,0)) as option_subtract,
                        p.quantity,
                        SUM(COALESCE(pov.quantity,0)) as option_quantity
                FROM ".$this->db->table("products")." p
                LEFT JOIN ".$this->db->table("product_options")." po
                    ON (po.product_id = p.product_id AND po.status = 1)
                LEFT JOIN  ".$this->db->table("product_option_values")." pov
                    ON (po.product_option_id = pov.product_option_id)
                WHERE p.product_id IN (".implode(', ', $ids).")
                GROUP BY p.product_id";
        $query = $this->db->query($sql);
        $output = array();
        foreach ($query->rows as $row) {
            $output[$row['product_id']] = array(
                'subtract' => (((int)$row['subtract'] + (int)$row['option_subtract']) > 0 ? true : false),
                'quantity' => ((int)$row['quantity'] + (int)$row['option_quantity']),
            );
        }
        $this->cache->push($cache_key, $output);
        return $output;
    }

    /**
     *
     * Check if product or any option has any stock available
     *
     * @param int $product_id
     *
     * @return int
     */
    public function hasAnyStock($product_id)
    {
        if (!(int)$product_id) {
            return 0;
        }
        $trackable = false;
        $total_quantity = 0;
        //check product option values
        $query = $this->db->query("SELECT pov.quantity AS quantity, pov.subtract
                                    FROM ".$this->db->table("product_options")." po
                                    LEFT JOIN ".$this->db->table("product_option_values")." pov
                                        ON (po.product_option_id = pov.product_option_id)
                                    WHERE po.product_id = '".(int)$product_id."' AND po.status = 1");
        if ($query->num_rows) {
            foreach ($query->rows as $row) {
                //if tracking of stock disabled - set quantity as big
                if (!$row['subtract']) {
                    $total_quantity = true;
                    continue;
                } else {
                    $trackable = true;
                }
                //calculate only if have no options without tracking
                if ($total_quantity !== true) {
                    $total_quantity += $row['quantity'] < 0 ? 0 : $row['quantity'];
                }
            }
            //if some of option value have subtract NO - think product is available
            if ($total_quantity == 0 && !$trackable) {
                $total_quantity = true;
            }
        }

        if (!$trackable) {
            //get product quantity without options
            $query = $this->db->query("SELECT quantity, subtract
                                        FROM ".$this->db->table("products")." p
                                        WHERE p.product_id = '".(int)$product_id."'");
            if ($query->row['subtract']) {
                $total_quantity = (int)$query->row['quantity'];
            } else {
                $total_quantity = true;
            }
        }
        return $total_quantity;
    }

    public function getProductDataForCart($product_id)
    {
        if (!(int)$product_id) {
            return array();
        }
        $query = $this->db->query(
            "SELECT p.product_id,
                        pd.name,
                        pd.meta_keywords,
                        pd.meta_description,
                        pd.description,
                        pd.blurb,
                        p.model,
                        p.sku,
                        p.location,
                        p.quantity,
                        p.stock_checkout,
                        p.stock_status_id,
                        p.manufacturer_id,
                        p.shipping,
                        p.ship_individually,
                        p.free_shipping,
                        p.shipping_price,
                        p.price,
                        p.tax_class_id,
                        p.date_available,
                        p.weight,
                        p.weight_class_id,
                        p.length,
                        p.width,
                        p.height,
                        p.length_class_id,
                        p.status,
                        p.viewed,
                        p.sort_order,
                        p.subtract,
                        p.minimum,
                        p.maximum,
                        p.cost,
                        p.call_to_order,
                        p.date_added,
                        p.date_modified,
                        wcd.unit AS weight_class,
                        mcd.unit AS length_class
                FROM ".$this->db->table("products")." p
                LEFT JOIN ".$this->db->table("product_descriptions")." pd
                    ON (p.product_id = pd.product_id
                            AND pd.language_id = '".(int)$this->config->get('storefront_language_id')."')
                LEFT JOIN ".$this->db->table("weight_classes")." wc ON (p.weight_class_id = wc.weight_class_id)
                LEFT JOIN ".$this->db->table("weight_class_descriptions")." wcd
                    ON (wc.weight_class_id = wcd.weight_class_id
                            AND wcd.language_id = '".(int)$this->config->get('storefront_language_id')."' )
                LEFT JOIN ".$this->db->table("length_classes")." mc ON (p.length_class_id = mc.length_class_id)
                LEFT JOIN ".$this->db->table("length_class_descriptions")." mcd ON (mc.length_class_id = mcd.length_class_id)
                WHERE p.product_id = '".(int)$product_id."' AND p.date_available <= NOW() AND p.status = '1'");

        return $query->row;
    }

    /**
     * @param int $category_id
     * @param string $sort
     * @param string $order
     * @param int $start
     * @param int $limit
     *
     * @return array
     */
    public function getProductsByCategoryId(
        $category_id,
        $sort = 'p.sort_order',
        $order = 'ASC',
        $start = 0,
        $limit = 20
    ) {
        $start = abs((int)$start);
        $limit = abs((int)$limit);
        $store_id = (int)$this->config->get('config_store_id');
        $language_id = (int)$this->config->get('storefront_language_id');
        $cache_key =
            'product.listing.products_category.'.(int)$category_id.'.store_'.$store_id.'_sort_'.$sort.'_order_'.$order;
        $cache_key .= '_start_'.$start.'_limit_'.$limit.'_lang_'.$language_id;
        $cache = $this->cache->pull($cache_key);
        if ($cache === false) {

            $sql = "SELECT *,
                            p.product_id,
                            ".$this->_sql_final_price_string().",
                            pd.name AS name, 
                            pd.blurb,
                            m.name AS manufacturer,
                            ss.name AS stock,
                            ".$this->_sql_avg_rating_string().",
                            ".$this->_sql_review_count_string()."
                            ".$this->_sql_join_string()."
            LEFT JOIN ".$this->db->table("products_to_categories")." p2c
                ON (p.product_id = p2c.product_id)
            WHERE p.status = '1' AND p.date_available <= NOW()
                    AND p2s.store_id = '".$store_id."'
                    AND p2c.category_id = '".(int)$category_id."'";

            $sort_data = array(
                'pd.name'       => 'LCASE(pd.name)',
                'p.sort_order'  => 'p.sort_order',
                'p.price'       => 'final_price',
                'special'       => 'final_price',
                'rating'        => 'rating',
                'date_modified' => 'p.date_modified',
                'review'        => 'review',
            );

            if (isset($sort) && in_array($sort, array_keys($sort_data))) {
                $sql .= " ORDER BY ".$sort_data[$sort];
            } else {
                $sql .= " ORDER BY p.sort_order";
            }

            if ($order == 'DESC') {
                $sql .= " DESC";
            } else {
                $sql .= " ASC";
            }

            if ($start < 0) {
                $start = 0;
            }

            $sql .= " LIMIT ".(int)$start.",".(int)$limit;
            $query = $this->db->query($sql);

            $cache = $query->rows;
            $this->cache->push($cache_key, $cache);
        }

        return $cache;
    }

    /**
     * @param int $category_id
     *
     * @return int
     */
    public function getTotalProductsByCategoryId($category_id = 0)
    {
        $store_id = (int)$this->config->get('config_store_id');

        $cache_key = 'product.listing.products_by_category.'.(int)$category_id.'.store_'.$store_id;
        $cache = $this->cache->pull($cache_key);
        if ($cache === false) {
            $query = $this->db->query("SELECT COUNT(*) AS total
                                    FROM ".$this->db->table("products_to_categories")." p2c
                                    LEFT JOIN ".$this->db->table("products")." p 
                                        ON (p2c.product_id = p.product_id)
                                    LEFT JOIN ".$this->db->table("products_to_stores")." p2s 
                                        ON (p.product_id = p2s.product_id)
                                    WHERE 
                                        p2c.category_id = '".(int)$category_id."'
                                        AND p.status = '1'
                                        AND p.date_available <= NOW()
                                        AND p2s.store_id = '".$store_id."'"
            );

            $cache = $query->row['total'];
            $this->cache->push($cache_key, $cache);
        }

        return $cache;
    }

    /**
     * @param int $manufacturer_id
     * @param string $sort
     * @param string $order
     * @param int $start
     * @param int $limit
     *
     * @return array
     */
    public function getProductsByManufacturerId(
        $manufacturer_id,
        $sort = 'p.sort_order',
        $order = 'ASC',
        $start = 0,
        $limit = 20
    ) {
        $start = abs((int)$start);
        $limit = abs((int)$limit);
        if (!(int)$manufacturer_id) {
            return array();
        }
        $sql = "SELECT *, p.product_id,
                        ".$this->_sql_final_price_string().",
                        pd.name AS name, 
                        pd.blurb,
                        m.name AS manufacturer,
                        ss.name AS stock,
                        ".$this->_sql_avg_rating_string().",
                        ".$this->_sql_review_count_string()."
                        ".$this->_sql_join_string()."
        WHERE p.status = '1' AND p.date_available <= NOW()
            AND p2s.store_id = '".(int)$this->config->get('config_store_id')."'
            AND m.manufacturer_id = '".(int)$manufacturer_id."'";

        $sort_data = array(
            'pd.name'       => 'LCASE(pd.name)',
            'p.sort_order'  => 'p.sort_order',
            'p.price'       => 'final_price',
            'special'       => 'final_price',
            'rating'        => 'rating',
            'date_modified' => 'p.date_modified',
            'review'        => 'review',
        );

        if (isset($sort) && in_array($sort, array_keys($sort_data))) {
            $sql .= " ORDER BY ".$sort_data[$sort];
        } else {
            $sql .= " ORDER BY p.sort_order";
        }

        if ($order == 'DESC') {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if ($start < 0) {
            $start = 0;
        }

        $sql .= " LIMIT ".(int)$start.",".(int)$limit;
        $query = $this->db->query($sql);

        return $query->rows;
    }

    /**
     * @param int $manufacturer_id
     *
     * @return int
     */
    public function getTotalProductsByManufacturerId($manufacturer_id = 0)
    {
        $query = $this->db->query("SELECT COUNT(*) AS total
                                    FROM ".$this->db->table("products")."
                                    WHERE status = '1'
                                            AND date_available <= NOW()
                                            AND manufacturer_id = '".(int)$manufacturer_id."'");
        return (int)$query->row['total'];
    }

    /**
     * @param string $tag
     * @param int $category_id
     * @param string $sort
     * @param string $order
     * @param int $start
     * @param int $limit
     *
     * @return array
     */
    public function getProductsByTag(
        $tag,
        $category_id = 0,
        $sort = 'p.sort_order',
        $order = 'ASC',
        $start = 0,
        $limit = 20
    ) {
        $start = abs((int)$start);
        $limit = abs((int)$limit);
        if ($tag) {
            $sql = "SELECT *, p.product_id,
                            ".$this->_sql_final_price_string().",
                            pd.name AS name, 
                            m.name AS manufacturer,
                            ss.name AS stock,
                            ".$this->_sql_avg_rating_string().",
                            ".$this->_sql_review_count_string()."
                            ".$this->_sql_join_string()."
                    LEFT JOIN ".$this->db->table("product_tags")." pt 
                        ON (p.product_id = pt.product_id AND pt.language_id = '"
                .(int)$this->config->get('storefront_language_id')."')
                    WHERE p2s.store_id = '".(int)$this->config->get('config_store_id')."'
                        AND (LCASE(pt.tag) = '".$this->db->escape(mb_strtolower($tag))."'";

            $keywords = explode(" ", $tag);

            foreach ($keywords as $keyword) {
                $sql .= " OR LCASE(pt.tag) = '".$this->db->escape(mb_strtolower($keyword))."'";
            }

            $sql .= ")";

            if ($category_id) {
                $data = array();

                foreach (explode(',', $category_id) as $category_id) {
                    $data[] = "'".(int)$category_id."'";
                }

                $sql .= " AND p.product_id IN (SELECT product_id
                                                FROM ".$this->db->table("products_to_categories")."
                                                WHERE category_id IN (".implode(",", $data)."))";
            }

            $sql .= " AND p.status = '1' AND p.date_available <= NOW() GROUP BY p.product_id";

            $sort_data = array(
                'pd.name'       => 'LCASE(pd.name)',
                'p.sort_order'  => 'p.sort_order',
                'p.price'       => 'final_price',
                'special'       => 'final_price',
                'rating'        => 'rating',
                'date_modified' => 'p.date_modified',
                'review'        => 'review',
            );

            if (isset($sort) && in_array($sort, array_keys($sort_data))) {
                $sql .= " ORDER BY ".$sort_data[$sort];
            } else {
                $sql .= " ORDER BY p.sort_order";
            }

            if ($order == 'DESC') {
                $sql .= " DESC";
            } else {
                $sql .= " ASC";
            }

            if ($start < 0) {
                $start = 0;
            }

            $sql .= " LIMIT ".(int)$start.",".(int)$limit;

            $query = $this->db->query($sql);

            $products = array();

            foreach ($query->rows as $value) {
                $products[$value['product_id']] = $this->getProduct($value['product_id']);
            }

            return $products;
        }
        return array();
    }

    /**
     * @param string $keyword
     * @param int $category_id
     * @param bool $description
     * @param bool $model
     * @param string $sort
     * @param string $order
     * @param int $start
     * @param int $limit
     *
     * @return array
     * @throws AException
     */
    public function getProductsByKeyword(
        $keyword,
        $category_id = 0,
        $description = false,
        $model = false,
        $sort = 'p.sort_order',
        $order = 'ASC',
        $start = 0,
        $limit = 20
    ) {
        $start = abs((int)$start);
        $limit = abs((int)$limit);
        //trim keyword
        $keyword = trim($keyword);
        if ($keyword) {
            $sql = "SELECT  *,
                            p.product_id,  
                            ".$this->_sql_final_price_string().",
                            pd.name AS name, 
                            pd.blurb,
                            m.name AS manufacturer,
                            ss.name AS stock,
                            ".$this->_sql_avg_rating_string().",
                            ".$this->_sql_review_count_string()."
                            ".$this->_sql_join_string()."
            LEFT JOIN ".$this->db->table("product_tags")." pt 
                ON (p.product_id = pt.product_id)
            WHERE p2s.store_id = '".(int)$this->config->get('config_store_id')."' ";

            $tags = explode(' ', trim($keyword));
            $tags_str = array();
            if (sizeof($tags) > 1) {
                $tags_str[] = " LCASE(pt.tag) = '".$this->db->escape(trim($keyword))."' ";
            }
            foreach ($tags as $tag) {
                $tags_str[] = " LCASE(pt.tag) = '".$this->db->escape(mb_strtolower($tag))."' ";
            }

            if (!$description) {
                $sql .= " AND (LCASE(pd.name) LIKE '%".$this->db->escape(mb_strtolower($keyword), true)."%' OR "
                    .implode(' OR ', $tags_str);
            } else {
                $sql .= " AND (LCASE(pd.name) LIKE '%".$this->db->escape(mb_strtolower($keyword), true)."%'
                                OR ".implode(' OR ', $tags_str)."
                                OR LCASE(pd.description) LIKE '%".$this->db->escape(mb_strtolower($keyword), true)."%'";
            }

            if (!$model) {
                $sql .= ")";
            } else {
                $sql .= " OR LCASE(p.model) LIKE '%".$this->db->escape(mb_strtolower($keyword), true)."%')";
            }

            if ($category_id) {
                $data = array();

                $this->load->model('catalog/category');
                $string = rtrim($this->getPath($category_id), ',');
                $category_ids = explode(',', $string);

                foreach ($category_ids as $category_id) {
                    $data[] = "'".(int)$category_id."'";
                }

                $sql .= " AND p.product_id IN (SELECT product_id
                                                FROM ".$this->db->table("products_to_categories")."
                                                WHERE category_id IN (".implode(", ", $data)."))";
            }

            $sql .= " AND p.status = '1' AND p.date_available <= NOW()
                     GROUP BY p.product_id";

            $sort_data = array(
                'pd.name'       => 'LCASE(pd.name)',
                'p.sort_order'  => 'p.sort_order',
                'p.price'       => 'final_price',
                'special'       => 'final_price',
                'rating'        => 'rating',
                'date_modified' => 'p.date_modified',
                'review'        => 'review',
            );

            if (isset($sort) && in_array($sort, array_keys($sort_data))) {
                $sql .= " ORDER BY ".$sort_data[$sort];
            } else {
                $sql .= " ORDER BY p.sort_order";
            }

            if ($order == 'DESC') {
                $sql .= " DESC";
            } else {
                $sql .= " ASC";
            }

            if ($start < 0) {
                $start = 0;
            }

            $sql .= " LIMIT ".(int)$start.",".(int)$limit;
            $query = $this->db->query($sql);
            $products = array();
            if ($query->num_rows) {
                foreach ($query->rows as $value) {
                    $products[$value['product_id']] = $value;
                }
            }
            return $products;

        } else {
            return array();
        }
    }

    /**
     * @param string $keyword
     * @param int $category_id
     * @param bool $description
     * @param bool $model
     *
     * @return int
     * @throws AException
     */
    public function getTotalProductsByKeyword($keyword, $category_id = 0, $description = false, $model = false)
    {
        $keyword = trim($keyword);
        if ($keyword) {
            $sql = "SELECT COUNT( DISTINCT p.product_id ) AS total
                    FROM ".$this->db->table("products")." p
                    LEFT JOIN ".$this->db->table("product_descriptions")." pd
                                ON (p.product_id = pd.product_id AND pd.language_id = '"
                .(int)$this->config->get('storefront_language_id')."')
                    LEFT JOIN ".$this->db->table("products_to_stores")." p2s
                                ON (p.product_id = p2s.product_id)
                    LEFT JOIN ".$this->db->table("product_tags")." pt ON (p.product_id = pt.product_id)
                    WHERE p2s.store_id = '".(int)$this->config->get('config_store_id')."'";

            $tags = explode(' ', trim($keyword));
            $tags_str = array();
            if (sizeof($tags) > 1) {
                $tags_str[] = " LCASE(pt.tag) = '".$this->db->escape(trim($keyword))."' ";
            }
            foreach ($tags as $tag) {
                $tags_str[] = " LCASE(pt.tag) = '".$this->db->escape(mb_strtolower($tag))."' ";
            }

            if (!$description) {
                $sql .= " AND (LCASE(pd.name) LIKE '%".$this->db->escape(mb_strtolower($keyword), true)."%' OR "
                    .implode(' OR ', $tags_str);
            } else {
                $sql .= " AND (LCASE(pd.name) LIKE '%".$this->db->escape(mb_strtolower($keyword), true)."%' OR "
                    .implode(' OR ', $tags_str)." OR LCASE(pd.description) LIKE '%"
                    .$this->db->escape(mb_strtolower($keyword), true)."%'";
            }

            if (!$model) {
                $sql .= ")";
            } else {
                $sql .= " OR LCASE(p.model) LIKE '%".$this->db->escape(mb_strtolower($keyword), true)."%')";
            }

            if ($category_id) {
                $data = array();

                $this->load->model('catalog/category');

                $string = rtrim($this->getPath($category_id), ',');
                $category_ids = explode(',', $string);

                foreach ($category_ids as $category_id) {
                    $data[] = "category_id = '".(int)$category_id."'";
                }

                $sql .= " AND p.product_id IN (SELECT product_id 
                                                FROM ".$this->db->table("products_to_categories")." 
                                                WHERE ".implode(" OR ", $data).")";
            }

            $sql .= " AND p.status = '1' AND p.date_available <= NOW()";
            $query = $this->db->query($sql);
            if ($query->num_rows) {
                return $query->row['total'];
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    /**
     * @param string $tag
     * @param int $category_id
     *
     * @return int
     * @throws AException
     */
    public function getTotalProductsByTag($tag, $category_id = 0)
    {
        $tag = trim($tag);
        if ($tag) {
            $language_id = (int)$this->config->get('storefront_language_id');
            $sql = "SELECT COUNT(DISTINCT p.product_id) AS total
                    FROM ".$this->db->table("products")." p
                    LEFT JOIN ".$this->db->table("product_descriptions")." pd 
                        ON (p.product_id = pd.product_id AND pd.language_id = '".$language_id."')
                    LEFT JOIN ".$this->db->table("product_tags")." pt 
                        ON (p.product_id = pt.product_id AND pt.language_id = '".$language_id."')
                    LEFT JOIN ".$this->db->table("products_to_stores")." p2s 
                        ON (p.product_id = p2s.product_id)
                    LEFT JOIN ".$this->db->table("manufacturers")." m 
                        ON (p.manufacturer_id = m.manufacturer_id)
                    WHERE p2s.store_id = '".(int)$this->config->get('config_store_id')."'
                        AND (LCASE(pt.tag) = '".$this->db->escape(mb_strtolower($tag))."'";

            $keywords = explode(" ", $tag);

            foreach ($keywords as $keyword) {
                $sql .= " OR LCASE(pt.tag) = '".$this->db->escape(mb_strtolower($keyword))."'";
            }

            $sql .= ")";

            if ($category_id) {
                $data = array();

                $this->load->model('catalog/category');

                $string = rtrim($this->getPath($category_id), ',');
                $category_ids = explode(',', $string);

                foreach ($category_ids as $category_id) {
                    $data[] = "category_id = '".(int)$category_id."'";
                }
                $sql .= " AND p.product_id IN (SELECT product_id 
                                                FROM ".$this->db->table("products_to_categories")." 
                                                WHERE ".implode(" OR ", $data).")";
            }
            $sql .= " AND p.status = '1' AND p.date_available <= NOW()";
            $query = $this->db->query($sql);

            if ($query->num_rows) {
                return $query->row['total'];
            }
        }
        return 0;
    }

    /**
     * @param int $category_id
     *
     * @return string
     */
    public function getPath($category_id)
    {
        $string = $category_id.',';
        $results = $this->model_catalog_category->getCategories((int)$category_id);
        foreach ($results as $result) {
            $string .= $this->getPath($result['category_id']);
        }
        return $string;
    }

    /**
     * @param int $limit
     *
     * @return array
     */
    public function getLatestProducts($limit)
    {
        $limit = abs((int)$limit);
        $cache_key = 'product.latest.'
                    .$limit
                    .'.store_'.(int)$this->config->get('config_store_id')
                    .'_lang_'.$this->config->get('storefront_language_id');
        $cache = $this->cache->pull($cache_key);

        if ($cache === false) {
            $sql = "SELECT *,
                        pd.name AS name,
                        m.name AS manufacturer,
                        ss.name AS stock,
                        pd.blurb,
                        ".$this->_sql_final_price_string().",
                        ".$this->_sql_avg_rating_string().",
                        ".$this->_sql_review_count_string()."
                        ".$this->_sql_join_string()."
                    WHERE p.status = '1'
                            AND p.date_available <= NOW()
                            AND p2s.store_id = '".(int)$this->config->get('config_store_id')."'
                    ORDER BY p.date_added DESC";

            if ((int)$limit) {
                $sql .= " LIMIT ".(int)$limit;
            }

            $query = $this->db->query($sql);
            $cache = $query->rows;
            $this->cache->push($cache_key, $cache);
        }

        return $cache;
    }

    /**
     * @param int $limit
     *
     * @return array
     */
    public function getPopularProducts($limit = 0)
    {
        $limit = abs((int)$limit);
        $sql = "SELECT *,
                        pd.name AS name,
                        m.name AS manufacturer,
                        ss.name AS stock,
                        ".$this->_sql_avg_rating_string().",
                        ".$this->_sql_review_count_string()."
                        ".$this->_sql_join_string()."
                WHERE p.status = '1'
                        AND p.date_available <= NOW()
                        AND p2s.store_id = '".(int)$this->config->get('config_store_id')."'
                ORDER BY p.viewed DESC, p.date_added DESC";

        if ((int)$limit) {
            $sql .= " LIMIT ".(int)$limit;
        }
        $query = $this->db->query($sql);
        return $query->rows;
    }

    /**
     * @param $limit
     *
     * @return array
     */
    public function getFeaturedProducts($limit)
    {
        $limit = abs((int)$limit);
        $language_id = (int)$this->config->get('storefront_language_id');
        $store_id = (int)$this->config->get('config_store_id');
        $cache_key = 'product.featured.'.$limit.'.store_'.$store_id.'_lang_'.$language_id;
        $product_data = $this->cache->pull($cache_key);
        if ($product_data === false) {
            $sql = "SELECT f.*, pd.*, ss.name AS stock, p.*
                    FROM ".$this->db->table("products_featured")." f
                    LEFT JOIN ".$this->db->table("products")." p
                        ON (f.product_id = p.product_id)
                    LEFT JOIN ".$this->db->table("product_descriptions")." pd
                        ON (f.product_id = pd.product_id AND pd.language_id = '".$language_id."')
                    LEFT JOIN ".$this->db->table("products_to_stores")." p2s 
                        ON (p.product_id = p2s.product_id)
                    LEFT JOIN ".$this->db->table("stock_statuses")." ss 
                        ON (p.stock_status_id = ss.stock_status_id AND ss.language_id = '".$language_id."')
                    WHERE p2s.store_id = '".$store_id."'
                        AND p.status='1'
                        AND p.date_available <= NOW()
                    ORDER BY p.sort_order ASC, p.date_available DESC ";

            if ((int)$limit) {
                $sql .= " LIMIT ".(int)$limit;
            }

            $query = $this->db->query($sql);
            $product_data = $query->rows;
            $this->cache->push($cache_key, $product_data);
        }
        return $product_data;
    }

    /**
     * @param $limit
     *
     * @return array
     */
    public function getBestSellerProducts($limit)
    {
        $limit = abs((int)$limit);
        $language_id = (int)$this->config->get('storefront_language_id');
        $store_id = (int)$this->config->get('config_store_id');
        $cache_key = 'product.bestseller.'.$limit.'.store_'.$store_id.'_lang_'.$language_id;

        $product_data = $this->cache->pull($cache_key);
        if ($product_data === false) {
            $product_data = array();

            $sql = "SELECT op.product_id, SUM(op.quantity) AS total
                    FROM ".$this->db->table("order_products")." op
                    LEFT JOIN `".$this->db->table("orders")."` o 
                        ON (op.order_id = o.order_id)
                    LEFT JOIN ".$this->db->table("products")." p 
                        ON p.product_id = op.product_id
                    WHERE o.order_status_id > '0' AND p.status = '1' AND p.date_available <= NOW()
                    GROUP BY op.product_id
                    ORDER BY total DESC";
            if ((int)$limit) {
                $sql .= " LIMIT ".(int)$limit;
            }
            $query = $this->db->query($sql);

            if ($query->num_rows) {
                $products = array();
                foreach ($query->rows as $result) {
                    $products[] = (int)$result['product_id'];
                }

                if ($products) {
                    $sql = "SELECT pd.*, ss.name AS stock, p.*
                            FROM ".$this->db->table("products")." p
                            LEFT JOIN ".$this->db->table("product_descriptions")." pd
                                ON (p.product_id = pd.product_id AND pd.language_id = '".$language_id."')
                            LEFT JOIN ".$this->db->table("products_to_stores")." p2s
                                ON (p.product_id = p2s.product_id)
                            LEFT JOIN ".$this->db->table("stock_statuses")." ss
                                ON (p.stock_status_id = ss.stock_status_id AND ss.language_id = '".$language_id."')
                            WHERE p.product_id IN (".implode(', ', $products).")
                                AND p.status = '1' AND p.date_available <= NOW()
                                AND p2s.store_id = '".$store_id."'";
                    $product_query = $this->db->query($sql);

                    if ($product_query->num_rows) {
                        $data = array();
                        foreach ($product_query->rows as $result) {
                            $data[$result['product_id']] = $result;
                        }
                        // resort by totals
                        foreach ($products as $id) {
                            if (isset($data[$id])) {
                                $product_data[] = $data[$id];
                            }
                        }
                    }
                }
            }

            $this->cache->push($cache_key, $product_data);
        }

        return $product_data;
    }

    /**
     * Update view count. Do not update modification date. See lat
     *
     * @param int $product_id
     *
     * @return null
     */
    public function updateViewed($product_id)
    {
        if (empty($product_id)) {
            return false;
        }

        $this->db->query("UPDATE ".$this->db->table("products")."
                            SET viewed = viewed + 1,
                                date_modified = date_modified 
                            WHERE product_id = '".(int)$product_id."'");
        return true;
    }

    /**
     * @param int $product_id
     * @param int $status
     *
     * @return null
     */
    public function updateStatus($product_id, $status = 0)
    {
        if (empty($product_id)) {
            return false;
        }
        $this->db->query("UPDATE ".$this->db->table("products")."
                        SET status = ".(int)$status."
                        WHERE product_id = '".(int)$product_id."'");
        $this->cache->remove('product');
        return true;
    }

    /**
     * check if product option is group option
     * if yes, return array of possible groups for option_value_id
     *
     * @param $product_id
     * @param $option_id
     * @param $option_value_id
     *
     * @return array
     */
    public function getProductGroupOptions($product_id, $option_id, $option_value_id)
    {
        if (empty($product_id) || empty($option_id)) {
            return array();
        }
        $product_option = $this->db->query(
            "SELECT group_id 
                FROM ".$this->db->table("product_options")."
                WHERE status=1 AND product_id = '".(int)$product_id."'
                    AND product_option_id = '".(int)$option_id."' ");
        if (!$product_option->row['group_id']) {
            return array();
        }
        //get all option values of group
        $option_values = $this->db->query(
            "SELECT pov.*, povd.name
                FROM ".$this->db->table("product_options")." po
                LEFT JOIN ".$this->db->table("product_option_values")." pov 
                    ON (po.product_option_id = pov.product_option_id)
                LEFT JOIN  ".$this->db->table("product_option_value_descriptions")." povd
                    ON (pov.product_option_value_id = povd.product_option_value_id 
                            AND povd.language_id = '".(int)$this->config->get('storefront_language_id')."' )
                WHERE po.status = 1 AND po.group_id = '".(int)$product_option->row['group_id']."'
                ORDER BY pov.sort_order ");

        //find attribute_value_id of option_value
        //find all option values with attribute_value_id
        //for each option values find group id
        //add each group values to result array
        $result = array();
        $attribute_value_id = null;
        foreach ($option_values->rows as $row) {
            if ($row['product_option_value_id'] == $option_value_id) {
                $attribute_value_id = $row['attribute_value_id'];
                break;
            }
        }
        $groups = array();
        foreach ($option_values->rows as $row) {
            if ($row['attribute_value_id'] == $attribute_value_id) {
                $groups[] = $row['group_id'];
            }
        }
        $groups = array_unique($groups);
        foreach ($groups as $group_id) {
            foreach ($option_values->rows as $row) {
                if ($row['group_id'] == $group_id && $row['product_option_id'] != $option_id) {
                    $result[$row['product_option_id']][$row['product_option_value_id']] = array(
                        'name'   => $row['name'],
                        'price'  => $row['price'],
                        'prefix' => $row['prefix'],
                    );
                }
            }
        }

        return $result;
    }

    /**
     * Quick check if there are any options for the product
     *
     * @param int $product_id
     *
     * @return boolean
     */
    public function hasAnyOptions($product_id)
    {
        if (!(int)$product_id) {
            return null;
        }
        $query = $this->db->query(
            "SELECT count(*) as total 
            FROM ".$this->db->table("product_options")." 
            WHERE status = 1 AND product_id = '".(int)$product_id."'"
        );
        if ($query->row['total'] > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param int $product_id
     *
     * @return array
     */
    public function getProductOptions($product_id)
    {
        if (!(int)$product_id) {
            return array();
        }
        $language_id = (int)$this->config->get('storefront_language_id');
        $cache_key = 'product.options.'.$product_id.'.lang_'.$language_id;
        $product_option_data = $this->cache->pull($cache_key);
        $elements = HtmlElementFactory::getAvailableElements();
        if ($product_option_data === false) {
            $product_option_data = array();
            $product_option_query = $this->db->query(
                "SELECT po.*, pod.option_placeholder, pod.error_text
                    FROM ".$this->db->table("product_options")." po
                    LEFT JOIN ".$this->db->table("product_option_descriptions")." pod
                        ON pod.product_option_id = po.product_option_id AND pod.language_id =  '".$language_id."'
                    WHERE po.product_id = '".(int)$product_id."'
                        AND po.group_id = 0
                        AND po.status = 1
                    ORDER BY po.sort_order"
            );
            if ($product_option_query) {
                foreach ($product_option_query->rows as $product_option) {
                    $attribute_values = array();
                    $product_option_value_data = array();
                    $product_option_value_query = $this->db->query(
                        "SELECT *
                            FROM ".$this->db->table("product_option_values")."
                            WHERE product_option_id = '".(int)$product_option['product_option_id']."'
                            ORDER BY sort_order"
                    );
                    if ($product_option_value_query) {
                        foreach ($product_option_value_query->rows as $product_option_value) {
                            if ($product_option_value['attribute_value_id']) {
                                //skip duplicate attributes values if it is not grouped parent/child
                                if (in_array($product_option_value['attribute_value_id'], $attribute_values)) {
                                    continue;
                                }
                                $attribute_values[] = $product_option_value['attribute_value_id'];
                            }
                            $pd_opt_val_description_qr = $this->db->query(
                                "SELECT *
                                    FROM ".$this->db->table("product_option_value_descriptions")."
                                    WHERE product_option_value_id = '"
                                .(int)$product_option_value['product_option_value_id']."'
                                    AND language_id = '".(int)$language_id."'"
                            );

                            // ignore option value with 0 quantity and disabled subtract
                            if ((!$product_option_value['subtract'])
                                || (!$this->config->get('config_nostock_autodisable'))
                                || ($product_option_value['quantity'] && $product_option_value['subtract'])
                            ) {
                                $product_option_value_data[$product_option_value['product_option_value_id']] = array(
                                    'product_option_value_id' => $product_option_value['product_option_value_id'],
                                    'attribute_value_id'      => $product_option_value['attribute_value_id'],
                                    'grouped_attribute_data'  => $product_option_value['grouped_attribute_data'],
                                    'group_id'                => $product_option_value['group_id'],
                                    'name'                    => $pd_opt_val_description_qr->row['name'],
                                    'option_placeholder'      => $product_option['option_placeholder'],
                                    'regexp_pattern'          => $product_option['regexp_pattern'],
                                    'error_text'              => $product_option['error_text'],
                                    'settings'                => $product_option['settings'],
                                    'children_options_names'  => $pd_opt_val_description_qr->row['children_options_names'],
                                    'sku'                     => $product_option_value['sku'],
                                    'price'                   => $product_option_value['price'],
                                    'prefix'                  => $product_option_value['prefix'],
                                    'weight'                  => $product_option_value['weight'],
                                    'weight_type'             => $product_option_value['weight_type'],
                                    'quantity'                => $product_option_value['quantity'],
                                    'subtract'                => $product_option_value['subtract'],
                                    'default'                 => $product_option_value['default'],
                                );
                            }
                        }
                    }
                    $prd_opt_description_qr = $this->db->query(
                        "SELECT *
                            FROM ".$this->db->table("product_option_descriptions")."
                            WHERE product_option_id = '".(int)$product_option['product_option_id']."'
                                AND language_id = '".(int)$language_id."'"
                    );

                    $product_option_data[$product_option['product_option_id']] = array(
                        'product_option_id'  => $product_option['product_option_id'],
                        'attribute_id'       => $product_option['attribute_id'],
                        'group_id'           => $product_option['group_id'],
                        'name'               => $prd_opt_description_qr->row['name'],
                        'option_placeholder' => $product_option['option_placeholder'],
                        'option_value'       => $product_option_value_data,
                        'sort_order'         => $product_option['sort_order'],
                        'element_type'       => $product_option['element_type'],
                        'html_type'          => $elements[$product_option['element_type']]['type'],
                        'required'           => $product_option['required'],
                        'regexp_pattern'     => $product_option['regexp_pattern'],
                        'error_text'         => $product_option['error_text'],
                        'settings'           => $product_option['settings'],
                    );
                }
            }

            $this->cache->push($cache_key, $product_option_data);
        }
        return $product_option_data;
    }

    /**
     * @param int $product_id
     * @param int $product_option_id
     *
     * @return array
     */
    public function getProductOption($product_id, $product_option_id)
    {
        if (!(int)$product_id || !(int)$product_option_id) {
            return array();
        }

        $query = $this->db->query("SELECT *
                        FROM ".$this->db->table("product_options")." po
                        LEFT JOIN ".$this->db->table("product_option_descriptions")." pod 
                            ON (po.product_option_id = pod.product_option_id)
                        WHERE po.status=1 AND po.product_option_id = '".(int)$product_option_id."'
                            AND po.product_id = '".(int)$product_id."'
                            AND pod.language_id = '".(int)$this->config->get('storefront_language_id')."'
                        ORDER BY po.sort_order");
        return $query->row;
    }

    /**
     * @param $product_id
     * @param $product_option_id
     *
     * @return array
     */
    public function getProductOptionValues($product_id, $product_option_id)
    {
        if (!(int)$product_id || !(int)$product_option_id) {
            return array();
        }
        $query = $this->db->query(
            "SELECT *
                 FROM ".$this->db->table("product_option_values")." pov
                 WHERE pov.product_option_id = '".(int)$product_option_id."'
                    AND pov.product_id = '".(int)$product_id."'
                 ORDER BY pov.sort_order");
        return $query->rows;
    }

    /**
     * @param int $product_id
     * @param int $product_option_value_id
     *
     * @return array
     */
    public function getProductOptionValue($product_id, $product_option_value_id)
    {
        if (!(int)$product_id || !(int)$product_option_value_id) {
            return array();
        }

        $query = $this->db->query(
            "SELECT *,
                        COALESCE(povd.product_id,povd2.product_id) as product_id,
                        COALESCE(povd.language_id,povd2.language_id) as language_id,
                        COALESCE(povd.product_option_value_id,povd2.product_option_value_id) as product_option_value_id,
                        COALESCE(povd.name,povd2.name) as name
                FROM ".$this->db->table("product_option_values")." pov
                LEFT JOIN ".$this->db->table("product_option_value_descriptions")." povd
                        ON (pov.product_option_value_id = povd.product_option_value_id
                                AND povd.language_id = '".(int)$this->config->get('storefront_language_id')."' )
                LEFT JOIN ".$this->db->table("product_option_value_descriptions")." povd2
                        ON (pov.product_option_value_id = povd2.product_option_value_id
                                AND povd2.language_id = '1' )
                WHERE pov.product_option_value_id = '".(int)$product_option_value_id."'
                    AND pov.product_id = '".(int)$product_id."'
                ORDER BY pov.sort_order");
        return $query->row;
    }

    /**
     * Check if any of input options are required and provided
     *
     * @param int $product_id
     * @param array $input_options
     *
     * @return array
     */
    public function validateProductOptions($product_id, $input_options)
    {

        $errors = array();
        if (empty($product_id) && empty($input_options)) {
            return array();
        }
        $product_options = $this->getProductOptions($product_id);
        if (is_array($product_options) && $product_options) {
            $this->load->language('checkout/cart');
            foreach ($product_options as $option) {

                if ($option['required']) {
                    if (empty($input_options[$option['product_option_id']])) {
                        $errors[] = $option['name'].': '.$this->language->get('error_required_options');
                    }
                }

                if ($option['regexp_pattern']
                    && !preg_match($option['regexp_pattern'], (string)$input_options[$option['product_option_id']])) {
                    $errors[] = $option['name'].': '.$option['error_text'];
                }

            }
        }

        return $errors;
    }

    /**
     * @param int $product_id
     *
     * @return array
     */
    public function getProductTags($product_id)
    {
        if (!(int)$product_id) {
            return array();
        }
        $query = $this->db->query("SELECT *
                                    FROM ".$this->db->table("product_tags")."
                                    WHERE product_id = '".(int)$product_id."'
                                            AND language_id = '".(int)$this->config->get('storefront_language_id')."'");

        return $query->rows;
    }

    /**
     * @param int $product_id
     *
     * @return array
     */
    public function getProductDownloads($product_id)
    {
        if (!(int)$product_id) {
            return array();
        }

        $query = $this->db->query(
            "SELECT *
                     FROM ".$this->db->table("products_to_downloads")." p2d
                     LEFT JOIN ".$this->db->table("downloads")." d ON (p2d.download_id = d.download_id)
                     LEFT JOIN ".$this->db->table("download_descriptions")." dd
                        ON (d.download_id = dd.download_id
                                AND dd.language_id = '".(int)$this->config->get('storefront_language_id')."')
                     WHERE p2d.product_id = '".(int)$product_id."'");

        return $query->rows;
    }

    /**
     * @param int $product_id
     *
     * @return array
     */
    public function getProductRelated($product_id)
    {
        $product_data = array();
        if (!(int)$product_id) {
            return array();
        }

        $product_related_query = $this->db->query(
            "SELECT *
                 FROM ".$this->db->table("products_related")."
                 WHERE product_id = '".(int)$product_id."'");

        foreach ($product_related_query->rows as $result) {
            $product_query = $this->db->query(
                "SELECT DISTINCT *,
                            pd.name AS name,
                            m.name AS manufacturer,
                            ss.name AS stock,
                            ".$this->_sql_avg_rating_string().", ".
                $this->_sql_review_count_string().
                $this->_sql_join_string()."
                    WHERE p.product_id = '".(int)$result['related_id']."'
                        AND p2s.store_id = '".(int)$this->config->get('config_store_id')."'
                        AND p.date_available <= NOW() AND p.status = '1'");

            if ($product_query->num_rows) {
                $product_data[$result['related_id']] = $product_query->row;
            }
        }

        return $product_data;
    }

    /**
     * @param int $product_id
     *
     * @return array
     */
    public function getCategories($product_id)
    {
        if (!(int)$product_id) {
            return array();
        }
        $query = $this->db->query("SELECT *
                                    FROM ".$this->db->table("products_to_categories")."
                                    WHERE product_id = '".(int)$product_id."'");
        return $query->rows;
    }

    protected function _sql_avg_rating_string()
    {
        $sql = " ( SELECT AVG(r.rating)
                         FROM ".$this->db->table("reviews")." r
                         WHERE p.product_id = r.product_id AND status = 1
                         GROUP BY r.product_id 
                 ) AS rating ";
        return $sql;
    }

    protected function _sql_review_count_string()
    {
        $sql = " ( SELECT COUNT(rw.review_id)
                         FROM ".$this->db->table("reviews")." rw
                         WHERE p.product_id = rw.product_id AND status = 1
                         GROUP BY rw.product_id
                 ) AS review ";
        return $sql;
    }

    protected function _sql_final_price_string()
    {
        //special prices
        if (is_object($this->customer) && $this->customer->isLogged()) {
            $customer_group_id = (int)$this->customer->getCustomerGroupId();
        } else {
            $customer_group_id = (int)$this->config->get('config_customer_group_id');
        }

        $sql = " ( SELECT p2sp.price
                    FROM ".$this->db->table("product_specials")." p2sp
                    WHERE p2sp.product_id = p.product_id
                            AND p2sp.customer_group_id = '".$customer_group_id."'
                            AND ((p2sp.date_start = '0000-00-00' OR p2sp.date_start < NOW())
                            AND (p2sp.date_end = '0000-00-00' OR p2sp.date_end > NOW()))
                    ORDER BY p2sp.priority ASC, p2sp.price ASC LIMIT 1
                 ) ";
        $sql = "COALESCE( ".$sql.", p.price) as final_price";

        return $sql;
    }

    protected function _sql_join_string()
    {

        return "FROM ".$this->db->table("products")." p
                LEFT JOIN ".$this->db->table("product_descriptions")." pd
                    ON (p.product_id = pd.product_id
                            AND pd.language_id = '".(int)$this->config->get('storefront_language_id')."')
                LEFT JOIN ".$this->db->table("products_to_stores")." p2s ON (p.product_id = p2s.product_id)
                LEFT JOIN ".$this->db->table("manufacturers")." m ON (p.manufacturer_id = m.manufacturer_id)
                LEFT JOIN ".$this->db->table("stock_statuses")." ss
                        ON (p.stock_status_id = ss.stock_status_id
                            AND ss.language_id = '".(int)$this->config->get('storefront_language_id')."')";
    }

    public function getProductsAllInfo($products = array())
    {
        if (!$products) {
            return false;
        }
        foreach ($products as &$id) {
            $id = (int)$id;
        }

        //special prices
        if (is_object($this->customer) && $this->customer->isLogged()) {
            $customer_group_id = (int)$this->customer->getCustomerGroupId();
        } else {
            $customer_group_id = (int)$this->config->get('config_customer_group_id');
        }
        $language_id = (int)$this->config->get('storefront_language_id');
        $store_id = (int)$this->config->get('config_store_id');
        $cache_key = 'product.all_info.'.md5(implode('', $products)).'.'.$customer_group_id.'.store_'.$store_id.'_lang_'
            .$language_id;

        $output = $this->cache->pull($cache_key);
        // if no cache
        if ($output === false) {
            $sql = "SELECT product_id, price
                    FROM ".$this->db->table("product_specials")."
                    WHERE product_id IN (".implode(', ', $products).")
                            AND customer_group_id = '".$customer_group_id."'
                            AND ((date_start = '0000-00-00' OR date_start < NOW())
                            AND (date_end = '0000-00-00' OR date_end > NOW()))
                    ORDER BY product_id ASC, priority ASC, price ASC";
            $result = $this->db->query($sql);
            $temp = '';
            $specials = array();
            foreach ($result->rows as $row) {
                if ($row['product_id'] != $temp) {
                    $specials[$row['product_id']] = $row['price'];
                }
                $temp = $row['product_id'];
            }
            //avg-rating
            if ($this->config->get('display_reviews')) {
                $sql = "SELECT product_id, AVG(rating) AS total
                        FROM ".$this->db->table("reviews")."
                        WHERE status = '1' AND product_id IN (".implode(', ', $products).")
                        GROUP BY product_id";
                $result = $this->db->query($sql);
                $rating = array();
                foreach ($result->rows as $row) {
                    $rating[$row['product_id']] = (int)$row['total'];
                }
            } else {
                $rating = false;
            }

            // discounts
            $sql = "SELECT product_id, price
                    FROM ".$this->db->table("product_discounts")."
                    WHERE product_id IN (".implode(', ', $products).")
                        AND customer_group_id = '".(int)$customer_group_id."'
                        AND quantity = '1'
                        AND ((date_start = '0000-00-00' OR date_start < NOW())
                        AND (date_end = '0000-00-00' OR date_end > NOW()))
                    ORDER BY  product_id ASC, priority ASC, price ASC";
            $result = $this->db->query($sql);
            $temp = '';
            $discounts = array();
            foreach ($result->rows as $row) {
                if ($row['product_id'] != $temp) {
                    $discounts[$row['product_id']] = $row['price'];
                }
                $temp = $row['product_id'];
            }

            // options
            $sql = "SELECT po.product_id,
                            po.product_option_id,
                            po.regexp_pattern,
                            pov.product_option_value_id,
                            pov.sku,
                            pov.quantity,
                            pov.subtract,
                            pov.price,
                            pov.prefix,
                            pod.name as option_name,
                            pod.error_text as error_text,
                            povd.name as value_name,
                            po.sort_order
                        FROM ".$this->db->table("product_options")." po
                        LEFT JOIN ".$this->db->table("product_option_values")." pov
                            ON pov.product_option_id = po.product_option_id
                        LEFT JOIN ".$this->db->table("product_option_value_descriptions")." povd
                            ON (povd.product_option_value_id = pov.product_option_value_id
                                    AND povd.language_id='".$language_id."')
                        LEFT JOIN ".$this->db->table("product_option_descriptions")." pod
                            ON (pod.product_option_id = po.product_option_id
                                AND pod.language_id='".$language_id."') 
                        WHERE po.product_id in (".implode(', ', $products).")
                        ORDER BY pov.product_option_id, pov.product_id, po.sort_order, pov.sort_order";
            $result = $this->db->query($sql);
            $temp = $temp2 = '';
            $options = array();
            foreach ($result->rows as $row) {

                if ($row['product_id'] != $temp) {
                    $temp2 = '';
                }

                if ($row['product_option_id'] != $temp2) {
                    $options[$row['product_id']][$row['product_option_id']] = array(
                        'product_option_id' => $row['product_option_id'],
                        'name'              => $row['option_name'],
                        'sort_order'        => $row['sort_order'],
                    );
                }
                $options[$row['product_id']][$row['product_option_id']]['option_value'][] = array(
                    'product_option_value_id' => $row['product_option_value_id'],
                    'name'                    => $row['value_name'],
                    'sku'                     => $row['sku'],
                    'price'                   => $row['price'],
                    'prefix'                  => $row['prefix'],
                );
                $temp = $row['product_id'];
                $temp2 = $row['product_option_id'];
            }

            foreach ($products as $product) {
                $output[$product]['special'] = $specials[$product];
                $output[$product]['discount'] = $discounts[$product];
                $output[$product]['options'] = $options[$product];
                $output[$product]['rating'] = $rating !== false ? (int)$rating[$product] : false;
            }
            $this->cache->push($cache_key, $output);
        }
        return $output;
    }

    /**
     * @param array $data
     * @param string $mode
     *
     * @return false|mixed
     */
    public function getProducts($data = array(), $mode = 'default')
    {

        if (!empty($data['content_language_id'])) {
            $language_id = ( int )$data['content_language_id'];
        } else {
            $language_id = (int)$this->config->get('storefront_language_id');
        }

        if ($data || $mode == 'total_only') {

            $filter = (isset($data['filter']) ? $data['filter'] : array());

            if ($mode == 'total_only') {
                $sql = "SELECT COUNT(DISTINCT p.product_id) as total
                        FROM ".$this->db->table("products")." p
                        LEFT JOIN ".$this->db->table("product_descriptions")." pd
                            ON (p.product_id = pd.product_id)";
            } else {
                $sql = "SELECT *,
                                p.product_id,
                                ".$this->_sql_final_price_string().",
                                pd.name AS name,
                                m.name AS manufacturer,
                                ss.name AS stock,
                                ".$this->_sql_avg_rating_string().",
                                ".$this->_sql_review_count_string()."
                                ".$this->_sql_join_string();
            }

            if (isset($filter['category_id']) && !is_null($filter['category_id'])) {
                $sql .= " LEFT JOIN ".$this->db->table("products_to_categories")
                    ." p2c ON (p.product_id = p2c.product_id)";
            }
            $sql .= " WHERE pd.language_id = '".$language_id."' AND p.date_available <= NOW() AND p.status = '1' ";

            if (!empty($data['subsql_filter'])) {
                $sql .= " AND ".$data['subsql_filter'];
            }

            if (isset($filter['match']) && !is_null($filter['match'])) {
                $match = $filter['match'];
            } else {
                $match = 'exact';
            }

            if (isset($filter['keyword']) && !is_null($filter['keyword'])) {
                $keywords = explode(' ', $filter['keyword']);

                if ($match == 'any') {
                    $sql .= " AND (";
                    foreach ($keywords as $k => $keyword) {
                        $kw = $this->db->escape(strtolower($keyword), true);
                        $sql .= $k > 0 ? " OR" : "";
                        $sql .= " (LCASE(pd.name) LIKE '%".$kw."%'";
                        $sql .= " OR LCASE(p.model) LIKE '%".$kw."%'";
                        $sql .= " OR LCASE(p.sku) LIKE '%".$kw."%')";
                    }
                    $sql .= " )";
                } else {
                    if ($match == 'all') {
                        $sql .= " AND (";
                        foreach ($keywords as $k => $keyword) {
                            $kw = $this->db->escape(strtolower($keyword), true);
                            $sql .= $k > 0 ? " AND" : "";
                            $sql .= " (LCASE(pd.name) LIKE '%".$kw."%'";
                            $sql .= " OR LCASE(p.model) LIKE '%".$kw."%'";
                            $sql .= " OR LCASE(p.sku) LIKE '%".$kw."%')";
                        }
                        $sql .= " )";
                    } else {
                        if ($match == 'exact') {
                            $kw = $this->db->escape(strtolower($filter['keyword']), true);
                            $sql .= " AND (LCASE(pd.name) LIKE '%".$kw."%'";
                            $sql .= " OR LCASE(p.model) LIKE '%".$kw."%'";
                            $sql .= " OR LCASE(p.sku) LIKE '%".$kw."%')";
                        }
                    }
                }
            }

            if (isset($filter['pfrom']) && !is_null($filter['pfrom'])) {
                $sql .= " AND p.price >= '".(float)$filter['pfrom']."'";
            }
            if (isset($filter['pto']) && !is_null($filter['pto'])) {
                $sql .= " AND p.price <= '".(float)$filter['pto']."'";
            }
            if (isset($filter['category_id']) && !is_null($filter['category_id'])) {
                $sql .= " AND p2c.category_id = '".(int)$filter['category_id']."'";
            }
            if (isset($filter['manufacturer_id']) && !is_null($filter['manufacturer_id'])) {
                $sql .= " AND p.manufacturer_id = '".(int)$filter['manufacturer_id']."'";
            }

            if (isset($filter['status']) && !is_null($filter['status'])) {
                $sql .= " AND p.status = '".(int)$filter['status']."'";
            }

            //If for total, we done building the query
            if ($mode == 'total_only') {
                $query = $this->db->query($sql);
                return $query->row['total'];
            }

            $sort_data = array(
                'name'          => 'pd.name',
                'model'         => 'p.model',
                'quantity'      => 'p.quantity',
                'price'         => 'p.price',
                'status'        => 'p.status',
                'sort_order'    => 'p.sort_order',
                'date_modified' => 'p.date_modified',
                'review'        => 'review',
                'rating'        => 'rating',
            );

            if (isset($data['sort']) && in_array($data['sort'], array_keys($sort_data))) {
                $sql .= " ORDER BY ".$sort_data[$data['sort']];
            } else {
                $sql .= " ORDER BY pd.name";
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

            return $query->rows;
        } else {
            $cache_key = 'product.lang_'.$language_id;
            $product_data = $this->cache->pull($cache_key);

            if ($product_data === false) {
                $query = $this->db->query("SELECT *
                                            FROM ".$this->db->table("products")." p
                                            LEFT JOIN ".$this->db->table("product_descriptions")." pd ON (p.product_id = pd.product_id)
                                            WHERE pd.language_id = '".$language_id."' AND p.date_available <= NOW() AND p.status = '1'
                                            ORDER BY pd.name ASC");
                $product_data = $query->rows;
                $this->cache->push($cache_key, $product_data);
            }

            return $product_data;
        }
    }

    /**
     * @param array $data
     *
     * @return array|null
     */
    public function getTotalProducts($data = array())
    {
        return $this->getProducts($data, 'total_only');
    }

    /**
     * @param string $sort
     * @param string $order
     * @param int $start
     * @param int $limit
     *
     * @return array
     */
    public function getProductSpecials($sort = 'p.sort_order', $order = 'ASC', $start = 0, $limit = 0)
    {
        $start = abs((int)$start);
        $limit = abs((int)$limit);
        $promotion = new APromotion();
        $results = $promotion->getProductSpecials($sort, $order, $start, $limit);

        return $results;
    }

    /**
     * @param int $product_id
     *
     * @return bool
     */
    public function hasTrackOptions($product_id)
    {
        $sql = "SELECT *
                FROM ".$this->db->table('product_option_values')." pov
                INNER JOIN ".$this->db->table('product_options')." po
                    ON (pov.product_option_id = po.product_option_id AND po.status = 1) 
                WHERE pov.product_id=".(int)$product_id." AND pov.subtract = 1";
        $result = $this->db->query($sql);
        return ($result->num_rows ? true : false);
    }
}
