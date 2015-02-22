<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
class ModelCatalogReview extends Model {		
	public function addReview($product_id, $data) {
		$this->db->query("INSERT INTO " . $this->db->table("reviews") . " 
						  SET author = '" . $this->db->escape($data['name']) . "',
						      customer_id = '" . (int)$this->customer->getId() . "',
						      product_id = '" . (int)$product_id . "',
						      text = '" . $this->db->escape(strip_tags($data['text'])) . "',
						      rating = '" . (int)$data['rating'] . "',
						      date_added = NOW()");
		
		$review_id = $this->db->getLastId();
		//notify administrator of pending review approval
		$language = new ALanguage($this->registry);
		$language->load('product/product');

		$msg_text = sprintf($language->get('text_pending_review_approval'), $product_id, $review_id);
		$msg = new AMessage();
		$msg->saveNotice($language->get('text_new_review'), $msg_text);
				
		$this->cache->delete('product.rating.'.(int)$product_id);
		$this->cache->delete('product.reviews.totals');
		$this->cache->delete('product.reviews.totals.'.$product_id);

		return '';
	}
		
	public function getReviewsByProductId($product_id, $start = 0, $limit = 20) {
		$query = $this->db->query("SELECT r.review_id,
										  r.author,
										  r.rating,
										  r.text,
										  p.product_id,
										  pd.name,
										  p.price,
										  r.date_added
							        FROM " . $this->db->table("reviews") . " r
							        LEFT JOIN " . $this->db->table("products") . " p ON (r.product_id = p.product_id)
							        LEFT JOIN " . $this->db->table("product_descriptions") . " pd ON (p.product_id = pd.product_id)
							        WHERE p.product_id = '" . (int)$product_id . "'
							                AND p.date_available <= NOW()
							                AND p.status = '1'
							                AND r.status = '1'
							                AND pd.language_id = '" . (int)$this->config->get('storefront_language_id') . "'
							        ORDER BY r.date_added DESC
							        LIMIT " . (int)$start . "," . (int)$limit);
		
		return $query->rows;
	}
	
	public function getAverageRating($product_id) {
		$cache = $this->cache->get('product.rating.'.(int)$product_id);
		if(is_null($cache)){
			$query = $this->db->query( "SELECT AVG(rating) AS total
										FROM " . $this->db->table("reviews") . " 
										WHERE status = '1' AND product_id = '" . (int)$product_id . "'
										GROUP BY product_id");
			$cache  = (int)$query->row['total'];
			$this->cache->set('product.rating.'.(int)$product_id,$cache);
		}
		return $cache;
	}	
	
	public function getTotalReviews() {
		$cache = $this->cache->get('product.reviews.totals');
		if(is_null($cache)){
		$query = $this->db->query( "SELECT COUNT(*) AS total
									FROM " . $this->db->table("reviews") . " r
									LEFT JOIN " . $this->db->table("products") . " p ON (r.product_id = p.product_id)
									WHERE p.date_available <= NOW()
										AND p.status = '1'
										AND r.status = '1'");
			$cache = $query->row['total'];
			$this->cache->set('product.reviews.totals', $cache);
		}
		return $cache;
	}

	public function getTotalReviewsByProductId($product_id) {
		$cache = $this->cache->get('product.reviews.totals.'.$product_id, (int)$this->config->get('storefront_language_id'));
		if(is_null($cache)){
			$query = $this->db->query( "SELECT COUNT(*) AS total
										FROM " . $this->db->table("reviews") . " r
										LEFT JOIN " . $this->db->table("products") . " p ON (r.product_id = p.product_id)
										LEFT JOIN " . $this->db->table("product_descriptions") . " pd ON (p.product_id = pd.product_id)
										WHERE p.product_id = '" . (int)$product_id . "'
											AND p.date_available <= NOW()
											AND p.status = '1'
											AND r.status = '1'
											AND pd.language_id = '" . (int)$this->config->get('storefront_language_id') . "'");

			$cache = $query->row['total'];
			$this->cache->set('product.reviews.totals.'.$product_id, $cache, (int)$this->config->get('storefront_language_id'));
		}
		return $cache;
	}
}
?>