<?php
/** @noinspection SqlDialectInspection */

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

        $this->cache->remove('product');

        return $review_id;
    }

    /**
     * @param int $product_id
     * @param int $start
     * @param int $limit
     *
     * @return array
     */
    public function getReviewsByProductId($product_id, $start = 0, $limit = 20)
    {
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
                    AND pd.language_id = '".(int) $this->config->get('storefront_language_id')."'
            ORDER BY r.date_added DESC
            LIMIT ".(int) $start.",".(int) $limit
        );

        return $query->rows;
    }

    /**
     * @param int $product_id
     *
     * @return int
     */
    public function getAverageRating($product_id)
    {
        $cacheKey = 'product.rating.'.(int) $product_id;
        $cache = $this->cache->pull($cacheKey);
        if ($cache === false) {
            $query = $this->db->query(
                "SELECT AVG(rating) AS total
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
     * @return array
     * @throws AException
     */
    public function getCategoriesAVGRatings($categoryIds)
    {
        $cache_key = 'product.categories.avg.rating'.md5(implode(',',$categoryIds));
        $cache = $this->cache->pull($cache_key);
        if ($cache === false) {
            $ids = array_unique(array_map('intval', $categoryIds));
            $query = $this->db->query(
                "SELECT *
                FROM " . $this->db->table("reviews") . " r 
                RIGHT JOIN  " . $this->db->table("products_to_categories") . " p2c
                    ON ( p2c.category_id IN (" . implode(',', $ids) . " )
                        AND p2c.product_id = r.product_id)
                WHERE r.status = 1"
            );
            $cache = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
            foreach ($query->rows as $row) {
                $cache[(int)$row['rating']]++;
            }
        }
        return $cache;
    }

    public function getProductAVGRatings($productId)
    {
        $cache_key = 'product.avg.ratings'.$productId;
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

}
