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

class ModelCatalogReview extends Model
{
    /**
     * @param array $data
     *
     * @return int
     * @throws AException
     */
    public function addReview($data)
    {
        $this->db->query("INSERT INTO ".$this->db->table("reviews")." 
						  SET author = '".$this->db->escape($data['author'])."',
							  product_id = '".$this->db->escape($data['product_id'])."',
							  text = '".$this->db->escape(strip_tags($data['text']))."',
							  rating = '".(int)$data['rating']."',
							  verified_purchase = '".(int)$data['verified_purchase']."',
							  status = '".(int)$data['status']."',
							  date_added = NOW()");
        $this->cache->remove(['product', 'collection', 'category']);
        return $this->db->getLastId();
    }

    /**
     * @param int $review_id
     * @param array $data
     * @throws AException
     */
    public function editReview($review_id, $data)
    {

        $allowFields = ['product_id', 'customer_id', 'author', 'text', 'rating', 'status', 'date_added', 'verified_purchase'];
        $update_data = [' date_modified = NOW() '];
        foreach ($data as $key => $val) {
            if (in_array($key, $allowFields)) {
                $update_data[] = "`$key` = '".$this->db->escape($val)."' ";
            }
        }

        $this->db->query("UPDATE ".$this->db->table("reviews")." 
						  SET ".implode(',', $update_data)."
						  WHERE review_id = '".(int)$review_id."'");
        $this->cache->remove(['product', 'collection', 'category']);
    }

    /**
     * @avoid
     *
     * @param int $review_id
     * @throws AException
     */
    public function deleteReview($review_id)
    {

        $this->db->query("DELETE FROM ".$this->db->table("reviews")." WHERE review_id = '".(int)$review_id."'");
        $this->cache->remove(['product', 'collection', 'category']);
    }

    /**
     * @param int $review_id
     *
     * @return array
     * @throws AException
     */
    public function getReview($review_id)
    {
        $query = $this->db->query(
            "SELECT DISTINCT *
				 FROM ".$this->db->table("reviews")."
				 WHERE review_id = '".(int)$review_id."'");
        return $query->row;
    }

    /**
     * @param array $data
     * @param string $mode
     *
     * @return array|int
     * @throws AException
     */
    public function getReviews($data = [], $mode = 'default')
    {

        if ($mode == 'total_only') {
            $total_sql = 'COUNT(*) as total';
        } else {
            $total_sql = 'r.review_id, r.product_id, pd.name, r.author, r.rating, r.verified_purchase, r.status, r.date_added';
        }
        $filter = $data['filter'] ?? [];
        $join = '';
        if (isset($filter['store_id'])) {
            $join = " INNER JOIN ".$this->db->table("products_to_stores")." p2s 
			ON (p2s.product_id = r.product_id AND p2s.store_id = '".(int)$filter['store_id']."')";
        }
        $sql = "SELECT ".$total_sql."
				FROM ".$this->db->table("reviews")." r
				LEFT JOIN ".$this->db->table("product_descriptions")." pd
					ON (r.product_id = pd.product_id AND pd.language_id = '".(int)$this->language->getContentLanguageID()."')
				LEFT JOIN ".$this->db->table("products")." p ON (r.product_id = p.product_id)
				".$join."
				WHERE 1=1 ";

        if (isset($filter['product_id'])) {
            $sql .= " AND r.product_id = '".(int)$filter['product_id']."'";
        }

        if (isset($filter['status'])) {
            $sql .= " AND r.status = '".(int)$filter['status']."'";
        }

        if (!empty($data['subsql_filter'])) {
            $sql .= " AND ".$data['subsql_filter'];
        }

        //If for total, we're done building the query
        if ($mode == 'total_only') {
            $query = $this->db->query($sql);
            return $query->row['total'];
        }

        $sort_data = [
            'name'       => 'pd.name',
            'author'     => 'r.author',
            'rating'     => 'r.rating',
            'status'     => 'r.status',
            'date_added' => 'r.date_added',
            'verified_purchase' => 'r.verified_purchase',
        ];

        if (isset($data['sort']) && in_array($data['sort'], array_keys($sort_data))) {
            $sql .= " ORDER BY ".$data['sort'];
        } else {
            $sql .= " ORDER BY r.date_added";
        }

        if (isset($data['order']) && (strtoupper($data['order']) == 'DESC')) {
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
    }

    /**
     * @param array $data
     *
     * @return int
     * @throws AException
     */
    public function getTotalReviews($data = [])
    {
        return $this->getReviews($data, 'total_only');
    }

    /**
     * @return int
     * @throws AException
     */
    public function getTotalReviewsAwaitingApproval()
    {
        $query = $this->db->query(
            "SELECT COUNT(*) AS total
				 FROM ".$this->db->table("reviews")."
				 WHERE status = '0'");

        return (int)$query->row['total'];
    }

    /**
     * @return int
     * @throws AException
     */
    public function getTotalToday()
    {
        $sql = "SELECT count(*) as total
				FROM `".$this->db->table("reviews")."` r
				WHERE DATE_FORMAT(r.date_added,'%Y-%m-%d') = DATE_FORMAT(now(),'%Y-%m-%d') ";
        $query = $this->db->query($sql);
        return (int)$query->row['total'];
    }

    /**
     * @return array
     * @throws AException
     */
    public function getReviewProducts()
    {
        $sql = "SELECT DISTINCT r.product_id, pd.name
				FROM ".$this->db->table("reviews")." r
				LEFT JOIN ".$this->db->table("product_descriptions")." pd
					ON (r.product_id = pd.product_id AND pd.language_id = '".(int)$this->language->getContentLanguageID()."')";
        $query = $this->db->query($sql);
        return array_column($query->rows, 'name', 'product_id');
    }
}
