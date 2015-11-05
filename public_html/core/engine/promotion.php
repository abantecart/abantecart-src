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
/**
 * Class APromotion
 * @property ACustomer $customer
 * @property ACart $cart
 */
class APromotion {
	/**
	 * @var Registry
	 */
	protected $registry;
	/**
	 * @var int
	 */
	protected $customer_group_id;
	/**
	 * @var array
	 */
    public $condition_objects = array();
	/**
	 * @var array
	 */
    public $bonus_objects = array();

	/**
	 * @param null/int $customer_group_id
	 */
	public function __construct( $customer_group_id = null ) {
		$this->registry = Registry::getInstance();

		if ( $customer_group_id ){
			$this->customer_group_id = $customer_group_id;
		} else if ( !is_null($this->customer) ) {
			//set customer group
			if ($this->customer->isLogged()) {
				$this->customer_group_id = $this->customer->getCustomerGroupId();
			} else {
				$this->customer_group_id = $this->config->get('config_customer_group_id');

			}
		}

        $this->condition_objects = array(
                                        'product_price',
                                        'categories',
                                        'brands',
                                        'products',
                                        'customers',
                                        'customer_groups',
                                        'customer_country',
                                        'customer_postcode',
                                        'order_subtotal',
                                        'order_product_count',
                                        'order_product_weight',
                                        'payment_method',
                                        'shipping_method',
                                        'coupon_code'
        );
        $this->bonus_objects = array(
                                        'order_discount',
                                        'free_shipping',
	                                    'discount_products',
	                                    'free_products',

        );
	}

    public function __get($key) {
		return $this->registry->get($key);
	}

	/**
	 * @param $key
	 * @param $value
	 */
	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

	/**
	 * @return array
	 */
    public function getConditionObjects(){
        return $this->condition_objects;
    }

	/**
	 * @return array
	 */
    public function getBonusObjects(){
       return $this->bonus_objects;
    }

	/**
	 * @param int $product_id
	 * @param int $discount_quantity
	 * @return float
	 */
	public function getProductQtyDiscount ( $product_id, $discount_quantity ) {
		if ( empty($product_id) && empty($discount_quantity) ) {
			return 0.00;
		}

		$sql = "SELECT price
				FROM " . $this->db->table("product_discounts") . "
				WHERE product_id = '" . (int)$product_id . "'
						AND customer_group_id = '" . (int)$this->customer_group_id . "'
						AND quantity <= '" . (int)$discount_quantity . "'
						AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW()))
				ORDER BY quantity DESC, priority ASC, price ASC
				LIMIT 1";

		$product_discount_query = $this->db->query( $sql );
				
		if ($product_discount_query->num_rows) {
			return $product_discount_query->row['price'];
		}
		return 0.00;
	}

	/**
	 * @param int $product_id
	 * @return bool|float
	 */
	public function getProductDiscount($product_id) {
		$cache = $this->cache->get('product.discount.'.$product_id.'.'.$this->customer_group_id);
		if(is_null($cache)){
			$query = $this->db->query( "SELECT price
										FROM " . $this->db->table("product_discounts") . "
										WHERE product_id = '" . (int)$product_id . "'
											AND customer_group_id = '" . (int)$this->customer_group_id . "'
											AND quantity = '1'
											AND ((date_start = '0000-00-00' OR date_start < NOW())
											AND (date_end = '0000-00-00' OR date_end > NOW()))
										ORDER BY priority ASC, price ASC
										LIMIT 1");
			if ($query->num_rows) {
				$cache = $query->row['price'];
			}else{
				$cache = false;
			}
			$this->cache->set('product.discount.'.$product_id.'.'.$this->customer_group_id, $cache);
		}
		return $cache;
	}

	/**
	 * @param int $product_id
	 * @return array
	 */
	public function getProductDiscounts($product_id) {
		$cache = $this->cache->get('product.discounts.'.$product_id.'.'.$this->customer_group_id);
		if(is_null($cache)){
		$query = $this->db->query("	SELECT *
									FROM " . $this->db->table("product_discounts") . "
									WHERE product_id = '" . (int)$product_id . "'
										AND customer_group_id = '" . (int)$this->customer_group_id . "'
										AND quantity > 1
										AND ((date_start = '0000-00-00' OR date_start < NOW())
										AND (date_end = '0000-00-00' OR date_end > NOW()))
									ORDER BY quantity ASC, priority ASC, price ASC");
			$cache = $query->rows;
			$this->cache->set('product.discounts.'.$product_id.'.'.$this->customer_group_id, $cache);
		}
		return $cache;
	}

	/**
	 * @param int $product_id
	 * @return bool|float
	 */
	public function getProductSpecial($product_id) {
		$cache = $this->cache->get('product.special.'.$product_id.'.'.$this->customer_group_id);
		if(is_null($cache)){
			$query = $this->db->query( "SELECT price
										FROM " . $this->db->table("product_specials") . "
										WHERE product_id = '" . (int)$product_id . "'
											AND customer_group_id = '" . $this->customer_group_id . "'
											AND ((date_start = '0000-00-00' OR date_start < NOW())
											AND (date_end = '0000-00-00' OR date_end > NOW()))
										ORDER BY priority ASC, price ASC LIMIT 1");
			if ($query->num_rows) {
				$cache =  $query->row['price'];
			}else{
				$cache = false;
			}
			$this->cache->set('product.special.'.$product_id.'.'.$this->customer_group_id, $cache);
		}
		return $cache;
	}

	/**
	 * @param string $sort
	 * @param string $order
	 * @param int $start
	 * @param int $limit
	 * @return array
	 */
	public function getProductSpecials($sort = 'p.sort_order', $order = 'ASC', $start = 0, $limit = 20) {

		$sql = "SELECT DISTINCT ps.product_id, p.*, pd.name, pd.description, pd.blurb, ss.name AS stock,
                    (SELECT AVG(rating)
                    FROM " . $this->db->table("reviews") . " r1
                    WHERE r1.product_id = ps.product_id
                        AND r1.status = '1'
                    GROUP BY r1.product_id) AS rating
                FROM " . $this->db->table("product_specials") . " ps
                LEFT JOIN " . $this->db->table("products") . " p ON (ps.product_id = p.product_id)
                LEFT JOIN " . $this->db->table("product_descriptions") . " pd ON (p.product_id = pd.product_id AND language_id=".(int)$this->config->get('storefront_language_id').")
                LEFT JOIN " . $this->db->table("products_to_stores") . " p2s ON (p.product_id = p2s.product_id)
				LEFT JOIN " . $this->db->table("stock_statuses") . " ss ON (p.stock_status_id = ss.stock_status_id AND ss.language_id = '" . (int)$this->config->get('storefront_language_id') . "')
                WHERE p.status = '1'
                    AND p.date_available <= NOW()
                    AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
                    AND ps.customer_group_id = '" . (int)$this->customer_group_id . "'
                    AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW())
                    AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))
                GROUP BY ps.product_id";

		$sort_data = array(
			'pd.name',
			'p.sort_order',
			'ps.price',
			'rating',
			'date_modified'
		);
			
		if (in_array($sort, $sort_data)) {
			if ($sort == 'pd.name') {
				$sql .= " ORDER BY LCASE(" . $sort . ")";
			} else {
				$sql .= " ORDER BY " . $this->db->escape($sort);
			}
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
		if((int)$limit){
			$sql .= " LIMIT " . (int)$start . "," . (int)$limit;
		}

		$query = $this->db->query($sql);
		
		return $query->rows;
	}

	/**
	 * @return int
	 */
    public function getTotalProductSpecials() {

   		$query = $this->db->query( "SELECT COUNT(DISTINCT ps.product_id) AS total
									FROM " . $this->db->table("product_specials") . " ps
										LEFT JOIN " . $this->db->table("products") . " p ON (ps.product_id = p.product_id)
										LEFT JOIN " . $this->db->table("products_to_stores") . " p2s ON (p.product_id = p2s.product_id)
									WHERE p.status = '1'
										AND p.date_available <= NOW()
										AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
										AND ps.customer_group_id = '" . (int)$this->customer_group_id . "'
										AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW())
										AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))");

   		if (isset($query->row['total'])) {
   			return $query->row['total'];
   		} else {
   			return 0;
   		}
   	}

	/**
	 * @param string $coupon_code
	 * @return array
	 */
	public function getCouponData($coupon_code) {
		if ( empty ($coupon_code) ) {
			return array();
		}
	
		$status = TRUE;
		$coupon_query = $this->db->query("SELECT *
										  FROM " . $this->db->table("coupons") . " c
										  LEFT JOIN " . $this->db->table("coupon_descriptions") . " cd
										        ON (c.coupon_id = cd.coupon_id AND cd.language_id = '" . (int)$this->config->get('storefront_language_id') . "' )
										  WHERE c.code = '" . $this->db->escape($coupon_code) . "'
										        AND ((date_start = '0000-00-00' OR date_start < NOW())
										        AND (date_end = '0000-00-00' OR date_end > NOW()))
										        AND c.status = '1'");
		$coupon_product_data = array();
		if ($coupon_query->num_rows) {
			if ($coupon_query->row['total'] >= $this->cart->getSubTotal()) {
				$status = FALSE;
			}
			$coupon_redeem_query = $this->db->query("SELECT COUNT(*) AS total
													 FROM `" . $this->db->table("orders") . "`
													 WHERE order_status_id > '0' AND coupon_id = '" . (int)$coupon_query->row['coupon_id'] . "'");

			if ($coupon_redeem_query->row['total'] >= $coupon_query->row['uses_total'] && $coupon_query->row['uses_total'] > 0) {
				$status = FALSE;
			}
			if ($coupon_query->row['logged'] && !is_null($this->customer) && !$this->customer->getId()) {
				$status = FALSE;
			}
			
			if (!is_null($this->customer) && $this->customer->getId()) {
				$coupon_redeem_query = $this->db->query("SELECT COUNT(*) AS total
														 FROM `" . $this->db->table("orders") . "`
														 WHERE order_status_id > '0'
														        AND coupon_id = '" . (int)$coupon_query->row['coupon_id'] . "'
														        AND customer_id = '" . (int)$this->customer->getId() . "'");
				
				if ($coupon_redeem_query->row['total'] >= $coupon_query->row['uses_customer'] && $coupon_query->row['uses_customer'] > 0) {
					$status = FALSE;
				}
			}

			$coupon_product_query = $this->db->query( "SELECT *
													   FROM " . $this->db->table("coupons_products") . "
													   WHERE coupon_id = '" . (int)$coupon_query->row['coupon_id'] . "'");

			foreach ($coupon_product_query->rows as $result) {
				$coupon_product_data[] = $result['product_id'];
			}
				
			if ($coupon_product_data) {
				$coupon_product = FALSE;
					
				foreach ($this->cart->getProducts() as $product) {
					if (in_array($product['product_id'], $coupon_product_data)) {
						$coupon_product = TRUE;
							
						break;
					}
				}
					
				if (!$coupon_product) {
					$status = FALSE;
				}
			}
		} else {
			$status = FALSE;
		}
		
		if ($status) {
			$coupon_data = array(
								'coupon_id'     => $coupon_query->row['coupon_id'],
								'code'          => $coupon_query->row['code'],
								'name'          => $coupon_query->row['name'],
								'type'          => $coupon_query->row['type'],
								'discount'      => $coupon_query->row['discount'],
								'shipping'      => $coupon_query->row['shipping'],
								'total'         => $coupon_query->row['total'],
								'product'       => $coupon_product_data,
								'date_start'    => $coupon_query->row['date_start'],
								'date_end'      => $coupon_query->row['date_end'],
								'uses_total'    => $coupon_query->row['uses_total'],
								'uses_customer' => $coupon_query->row['uses_customer'],
								'status'        => $coupon_query->row['status'],
								'date_added'    => $coupon_query->row['date_added']
			);
			
			return $coupon_data;
		}
		return array();
	}


	/**
	 * @param array $total_data
	 * @param array $total
	 * @return array
	 */
	public function apply_promotions($total_data, $total){
		$registry = Registry::getInstance();
		if ( $registry->has('extensions') ) {
			$result = $registry->get('extensions')->hk_apply_promotions($this,$total_data,$total);
		} else {
			$result = $this->_apply_promotions($total_data,$total);
		}
		return $result;
	}

	//adding native promotions
	/**
	 * @param array $total_data
	 * @param array $total
	 * @return array
	 */
	public function _apply_promotions($total_data,$total){
		return array();
	}

}