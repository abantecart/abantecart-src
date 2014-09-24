<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ModelCatalogReview extends Model {
	public function addReview($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "reviews
						  SET author = '" . $this->db->escape($data['author']) . "',
						      product_id = '" . $this->db->escape($data['product_id']) . "',
						      text = '" . $this->db->escape(strip_tags($data['text'])) . "',
						      rating = '" . (int)$data['rating'] . "',
						      status = '" . (int)$data['status'] . "',
						      date_added = NOW()");
        $this->cache->delete('product.reviews.totals');
		$this->cache->delete('product.all_info');
		return $this->db->getLastId();
	}
	
	public function editReview($review_id, $data) {

		$allowFields = array( 'product_id','customer_id','author','text','rating','status','date_added');
		$update_data = array(' date_modified = NOW() ');
		foreach ( $data as $key => $val ) {
			if(in_array($key,$allowFields)){
				$update_data[] = "`$key` = '" . $this->db->escape($val) . "' ";
			}
		}
		$this->db->query("UPDATE " . DB_PREFIX . "reviews
						  SET ".implode(',', $update_data)."
						  WHERE review_id = '" . (int)$review_id . "'");
        $this->cache->delete('product.rating.'.(int)$data['product_id']);
        $this->cache->delete('product.reviews.totals');
		$this->cache->delete('product.all_info');
	}
	
	public function deleteReview($review_id) {
        $review = $this->getReview($review_id);
		$this->db->query("DELETE FROM " . DB_PREFIX . "reviews WHERE review_id = '" . (int)$review_id . "'");
        $this->cache->delete('product.rating.'.(int)$review['product_id']);
        $this->cache->delete('product.reviews.totals');
		$this->cache->delete('product.all_info');
	}
	
	public function getReview($review_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "reviews WHERE review_id = '" . (int)$review_id . "'");
		
		return $query->row;
	}

	public function getReviews($data = array(), $mode = 'default') {
	
		if ($mode == 'total_only') {
			$total_sql = 'count(*) as total';
		}
		else {
			$total_sql = 'r.review_id, r.product_id, pd.name, r.author, r.rating, r.status, r.date_added';
		}
			
		$sql = "SELECT
		            $total_sql
		        FROM " . DB_PREFIX . "reviews r
		            LEFT JOIN " . DB_PREFIX . "product_descriptions pd ON (r.product_id = pd.product_id)
		            LEFT JOIN " . DB_PREFIX . "products p ON (r.product_id = p.product_id)
		        WHERE pd.language_id = '" . (int)$this->config->get('storefront_language_id') . "'";

		$filter = (isset($data['filter']) ? $data['filter'] : array());
		if (isset($filter['product_id']) && !is_null($filter['product_id'])) {
	    	$sql .= " AND r.product_id = '" . (int)$filter['product_id'] . "'";
	    }
	    if (isset($filter['status']) && !is_null($filter['status'])) {
	    	$sql .= " AND r.status = '" . (int)$filter['status'] . "'";
	    }

		if ( !empty($data['subsql_filter']) ) {
			$sql .= " AND ".$data['subsql_filter'];
		}

		//If for total, we done bulding the query
		if ($mode == 'total_only') {
		    $query = $this->db->query($sql);
		    return $query->row['total'];
		}
		
		$sort_data = array(
			'name' => 'pd.name',
			'author' => 'r.author',
			'rating' =>'r.rating',
			'status' => 'r.status',
			'date_added' => 'r.date_added'
		);	
			
		if (isset($data['sort']) && in_array($data['sort'], array_keys($sort_data)) ) {
			$sql .= " ORDER BY " . $data['sort'];	
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
			
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}																																							  
																																							  
		$query = $this->db->query($sql);																																				
		
		return $query->rows;	
	}
	
	public function getTotalReviews( $data = array() ) {
		return $this->getReviews($data, 'total_only');
	}
	
	public function getTotalReviewsAwaitingApproval() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "reviews WHERE status = '0'");
		
		return $query->row['total'];
	}

	public function getTotalToday() {
		$sql = "SELECT count(*) as total
	        	FROM `" . $this->db->table("reviews") . "` r 
	        	WHERE DATE_FORMAT(r.date_added,'%Y-%m-%d') = DATE_FORMAT(now(),'%Y-%m-%d') ";
		$query = $this->db->query($sql);
		return $query->row['total'];		
	}

	public function getReviewProducts() {
		$sql = "SELECT DISTINCT r.product_id, pd.name
		FROM " . DB_PREFIX . "reviews r LEFT JOIN " . DB_PREFIX . "product_descriptions pd ON (r.product_id = pd.product_id)
		WHERE pd.language_id = '" . (int)$this->config->get('storefront_language_id') . "'";
		$query = $this->db->query($sql);

		$result =  array();
		foreach ( $query->rows as $row ) {
			$result[ $row['product_id'] ] = $row['name'];
		}

		return $result;
	}
}
