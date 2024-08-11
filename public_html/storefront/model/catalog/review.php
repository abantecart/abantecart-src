<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2024 Belavier Commerce LLC

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

class ModelCatalogReview extends Model
{
    /**
     * @param int $product_id
     * @param array $data
     *
     * @return int
     * @throws AException
     */
    public function addReview($product_id, $data)
    {
        $verified_purchase = 0;
        if ($this->customer && $this->customer->getId()) {
            $customerId = $this->customer->getId();
            $orderProductsTable = $this->db->table('order_products');
            $ordersTable = $this->db->table('orders');

            $sql = 'SELECT product_id 
                    FROM '.$orderProductsTable.' 
                    INNER JOIN '.$ordersTable.' 
                        ON '.$ordersTable.'.order_id='.$orderProductsTable.'.order_id 
                            AND '.$ordersTable.'.customer_id='.$customerId.' 
                            AND '.$ordersTable.'.order_status_id>0 
                            AND '.$ordersTable.'.store_id='.$this->config->get('config_store_id').' 
                    WHERE '.$orderProductsTable.'.product_id='.$product_id.' 
                    LIMIT 1';
            $result = $this->db->query($sql);
            if ($result->num_rows > 0) {
                $verified_purchase = 1;
            }
        }

        $this->db->query(
            "INSERT INTO ".$this->db->table("reviews")." 
            SET author = '".$this->db->escape($data['name'])."',
                customer_id = '".(int) $this->customer->getId()."',
                product_id = '".(int) $product_id."',
                text = '".$this->db->escape(strip_tags($data['text']))."',
                rating = '".(int) $data['rating']."',
                verified_purchase = '".$verified_purchase."',
                date_added = NOW()"
        );

        $review_id = $this->db->getLastId();
        //notify administrator of pending review approval
        $language = new ALanguage($this->registry);
        $language->load($language->language_details['directory']);
        $language->load('product/product');

        $msg_text = sprintf($language->get('text_pending_review_approval'), $product_id, $review_id);
        $msg = new AMessage();
        $msg->saveNotice($language->get('text_new_review'), $msg_text);
        return $review_id;
    }

    /**
     * @param int $product_id
     * @param int $start
     * @param int $limit
     *
     * @return array
     * @throws AException
     */
    public function getReviewsByProductId($product_id, $start = 0, $limit = 20)
    {
        $language_id = (int)$this->config->get('storefront_language_id');
        $cacheKey = 'product.reviews.by.product.'.md5(var_export(func_get_args(), true));
        $output = $this->cache->pull($cacheKey);
        if ($output !== false) {
            return $output;
        }
        $query = $this->db->query(
            "SELECT r.review_id,
                  r.author,
                  r.rating,
                  r.text,
                  r.verified_purchase,
                  p.product_id,
                  pd.name,
                  p.price,
                  r.date_added
            FROM ".$this->db->table("reviews")." r
            LEFT JOIN ".$this->db->table("products")." p 
                ON (r.product_id = p.product_id)
            LEFT JOIN ".$this->db->table("product_descriptions")." pd 
                ON (p.product_id = pd.product_id)
            WHERE p.product_id = '".(int) $product_id."'
                    AND COALESCE(p.date_available, NOW()) <= NOW()
                    AND p.status = '1'
                    AND r.status = '1'
                    AND pd.language_id = '".$language_id."'
            ORDER BY r.date_added DESC
            LIMIT ".(int) $start.",".(int) $limit
        );
        $output = $query->rows;
        $this->cache->push($cacheKey,$output);
        return $output;
    }

    /**
     * @param int $product_id
     *
     * @return int
     * @throws AException
     */
    public function getAverageRating($product_id)
    {
        $cacheKey = 'product.rating.'.(int) $product_id;
        $cache = $this->cache->pull($cacheKey);
        if ($cache === false) {
            $query = $this->db->query(
                "SELECT FLOOR(AVG(rating)) AS total
                FROM ".$this->db->table("reviews")." 
                WHERE status = '1' 
                    AND product_id = '".(int) $product_id."'
                GROUP BY product_id"
            );
            $cache = (int) $query->row['total'];
            $this->cache->push($cacheKey, $cache);
        }
        return $cache;
    }

    /**
     * @return int
     */


    /**
     * @param int $product_id
     *
     * @return int
     * @throws AException
     */
    public function getTotalReviewsByProductId($product_id)
    {
        $language_id = (int) $this->config->get('storefront_language_id');
        $cache_key = 'product.reviews.totals.'.$product_id.'.lang_'.$language_id;
        $cache = $this->cache->pull($cache_key);
        if ($cache === false) {
            $query = $this->db->query(
                "SELECT COUNT(*) AS total
                FROM ".$this->db->table("reviews")." r
                LEFT JOIN ".$this->db->table("products")." p 
                    ON (r.product_id = p.product_id)
                LEFT JOIN ".$this->db->table("product_descriptions")." pd 
                    ON (p.product_id = pd.product_id)
                WHERE p.product_id = '".(int) $product_id."'
                    AND COALESCE(p.date_available, NOW()) <= NOW()
                    AND p.status = '1'
                    AND r.status = '1'
                    AND pd.language_id = '".$language_id."'"
            );

            $cache = (int) $query->row['total'];
            $this->cache->push($cache_key, $cache);
        }
        return $cache;
    }

    /**
     * Function get positive review in percent
     * @param int $product_id
     * @return int $percentage
     * @throws AException
     */
    public function getPositiveReviewPercentage($product_id)
    {
        $cache_key = 'product.reviews.positive.percents.'.$product_id;
        $cache = $this->cache->pull($cache_key);
        if ($cache === false) {
            $totalReviews = $this->getTotalReviewsByProductId($product_id);
            if ($totalReviews === 0) {
                return 0;
            }
            $query = $this->db->query(
                "SELECT COUNT(*) AS positive
                FROM " . $this->db->table("reviews") . " 
                WHERE `product_id` = '" . $product_id . "'
                    AND `status` = '1'
                    AND `rating` >= '4'"
            );
            $positiveReviews = (int)$query->row['positive'];
            $percentage = ($positiveReviews / $totalReviews) * 100;
            $cache = round($percentage, 2);
            $this->cache->push($cache_key, $cache);
        }
        return $cache;
    }

    /**
     * @param array $categoryIds
     * @param array|null $data
     * @return array
     * @throws AException
     */
    public function getCategoriesAVGRatings(array $categoryIds, ?array $data = [])
    {
        $categoryIds = filterIntegerIdList($categoryIds);
        $data['filter']['manufacturer_id'] = filterIntegerIdList((array)$data['filter']['manufacturer_id']);
        $data['filter']['rating'] = filterIntegerIdList((array)$data['filter']['rating']);
        $storeId = (int)($data['store_id'] ?? $this->config->get('config_store_id'));
        $cacheKey = 'product.categories.avg.rating'.$storeId.md5(var_export(func_get_args(),true));
        $output = $this->cache->pull($cacheKey);
        if ($output !== false) {
            return $output;
        }

        $sql = "SELECT FLOOR(AVG(r.rating)) as rating, p.product_id
                FROM " . $this->db->table("reviews") . " r
                INNER JOIN " . $this->db->table("products") . " p
                    ON (r.product_id = p.product_id 
                        AND p.status = '1' AND COALESCE(p.date_available, NOW()) <= NOW() )
                INNER JOIN " . $this->db->table('products_to_stores') . " p2s 
                    ON (p2s.product_id = p.product_id AND p2s.store_id = ".$storeId.")";

        if($categoryIds) {
            $sql .= " RIGHT JOIN  " . $this->db->table("products_to_categories") . " p2c
                    ON ( p2c.product_id = r.product_id
             AND p2c.category_id IN (" . implode(',', $categoryIds) . " ))";
        }

        $sql .= " WHERE r.status = 1";

        if($data['filter']['manufacturer_id']){
            $sql .= " AND p.manufacturer_id IN (".implode(',',(array)$data['filter']['manufacturer_id']).")";
        }
        $sql .= " GROUP BY p.product_id";

        if($data['filter']['rating']){
            $sql .= " HAVING FLOOR(AVG(r.rating)) IN (" . implode(',', array_map('intval', (array)$data['filter']['rating'])) . ") ";
        }

        $query = $this->db->query( $sql );
        $output = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        foreach ($query->rows as $row) {
            $output[(int)$row['rating']]++;
        }
        $this->cache->push($cacheKey, $output);
        return $output;
    }

    public function getProductAVGRatings($productId)
    {
        $cache_key = 'product.avg.ratings.'.$productId;
        $cache = $this->cache->pull($cache_key);
        if ($cache === false) {
            $query = $this->db->query(
                "SELECT *
                FROM " . $this->db->table("reviews") . " r 
                WHERE r.status = 1 AND product_id = ".(int)$productId
            );
            $cache = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
            foreach ($query->rows as $row) {
                $cache[(int)$row['rating']]++;
            }
        }
        return $cache;
    }

    /**
     * @param array|null $manufacturerIds
     * @param array|null $data
     * @return array
     * @throws AException
     */
    public function getBrandsAVGRatings(?array $manufacturerIds = [], ?array $data = [])
    {
        $manufacturerIds = filterIntegerIdList($manufacturerIds);
        $data['filter']['rating'] = filterIntegerIdList((array)$data['filter']['rating']);
        $storeId = (int)($data['store_id'] ?? $this->config->get('config_store_id'));
        $cacheKey = 'product.brands.avg.rating'.$storeId.md5(var_export(func_get_args(),true));
        $output = $this->cache->pull($cacheKey);
        if ($output !== false) {
            return $output;
        }

        $sql = "SELECT FLOOR(AVG(r.rating)) as rating, p.product_id
                FROM " . $this->db->table("reviews") . " r 
                INNER JOIN  " . $this->db->table("products") . " p
                    ON ( p.product_id = r.product_id
                         AND p.status = '1' AND COALESCE(p.date_available, NOW()) <= NOW() 
                ";
        if($manufacturerIds) {
            $sql .= "AND p.manufacturer_id IN (" . implode(',', $manufacturerIds) . " ) ";
        }
        $sql .= ") 
            INNER JOIN " . $this->db->table('products_to_stores') . " p2s 
                ON (p2s.product_id = p.product_id AND p2s.store_id = ".$storeId.") ";

        if($data['filter']['category_id']){
            $sql .= " INNER JOIN ".$this->db->table('products_to_categories')." p2c
                ON (p2c.category_id IN (".implode(',',(array)$data['filter']['category_id']).") 
                    AND p.product_id = p2c.product_id) ";
        }
        $sql .= "WHERE r.status = 1";
        $sql .= " GROUP BY p.product_id";
        if($data['filter']['rating']){
            $sql .= " HAVING FLOOR(AVG(r.rating)) IN (" . implode(',', array_map('intval', (array)$data['filter']['rating'])) . ") ";
        }
        $query = $this->db->query( $sql );
        $output = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        foreach ($query->rows as $row) {
            $output[(int)$row['rating']]++;
        }
        $this->cache->push($cacheKey, $output);
        return $output;
    }

    public function getInitialsReviewUser($author)
    {
        $words = explode(" ", $author);

        $initials = "";
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        return $initials;
    }
}