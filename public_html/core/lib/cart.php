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
/** @noinspection PhpUndefinedClassInspection */
/**
 * Class ACart
 * @property ModelCatalogProduct $model_catalog_product
 * @property ATax $tax
 * @property ASession $session
 * @property ADB $db
 * @property AWeight $weight
 * @property AConfig $config
 * @property ALoader $load
 * @property ALanguage $language
 * @property ModelCheckoutExtension $model_checkout_extension
 */
class ACart {
	/**
	 * @var Registry
	 */
  	private $registry;
	/**
	 * @var array
	 */
  	private $cart_data = array();
	/**
	 * @var array
	 */
  	private $cust_data = array();
	/**
	 * @var float
	 */
  	private $sub_total;
	/**
	 * @var array
	 */
  	private $taxes = array();
	/**
	 * @var float
	 */
  	private $total_value;
	/**
	 * @var array
	 */
  	private $final_total;
	/**
	 * @var array
	 */
  	private $total_data;
	/**
	 * @var ACustomer
	 */
  	private $customer;
	/**
	 * @var AAttribute
	 */
	private $attribute;
	/**
	 * @var APromotion
	 */
	private $promotion;

	/**
	 * @param $registry Registry
	  * @param $c_data array  - ref (Customer data array passed by ref)
	 *
	 */
	public function __construct($registry, &$c_data = null) {
  		$this->registry = $registry;
		$this->attribute = new AAttribute('product_option');
		$this->customer = $registry->get('customer');
		$this->session = $registry->get('session');
		
		//if nothing is passed (default) use session array. Customer session, can function on storefrnt only 
		if ($c_data == null) {
			$this->cust_data =& $this->session->data;  
		} else {
			$this->cust_data =& $c_data;  		
		}
		//can load promotion if customer_group_id is provided  
		$this->promotion = new APromotion($this->cust_data['customer_group_id']);

		if (!isset($this->cust_data['cart']) || !is_array($this->cust_data['cart'])) {
      		$this->cust_data['cart'] = array();
    	}
	}

	/**
	 * @param $key
	 * @return mixed
	 */
	public function __get($key) {
		return $this->registry->get($key);
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

	/**
	 * Returns all products in the cart
	 * To force recalculate pass argument as TRUE
	 * @param bool $recalculate
	 * @return array
	 */
	public function getProducts( $recalculate = false ) {
		//check if cart data was built before
		if ( count($this->cart_data) && !$recalculate ) {
			return $this->cart_data;
		}

		$product_data = array();
		//process data in the cart session per each product in the cart
    	foreach ($this->cust_data['cart'] as $key => $data) {
			if ( $key == 'virtual' ) {
				continue;
			}
      		$array = explode(':', $key);
      		$product_id = $array[0];
      		$quantity =	 $data['qty'];
    		
      		if (isset($data['options'])) {
        		$options = (array)$data['options'];
      		} else {
        		$options = array();
      		}

			$product_result = $this->buildProductDetails($product_id, $quantity, $options);
			if ( count($product_result) ) {
				$product_data[$key] = $product_result;
				$product_data[$key]['key'] = $key;						

				//apply min and max for quantity once we have product details.
				if ($quantity < $product_result['minimum']) {
					$this->language->load('checkout/cart','silent');
					$this->cust_data['error'] = $this->language->get('error_quantity_minimum');
					$this->update($key, $product_result['minimum']);
				}
				if ($product_result['maximum'] > 0) {
					$this->language->load('checkout/cart','silent');
					if ($quantity > $product_result['maximum']) {
						$this->cust_data['error'] = $this->language->get('error_quantity_maximum');
						$this->update($key, $product_result['maximum']);
					}
				}
			} else {
				$this->remove($key);
			}
    	}
    	//save complete cart details in the class for future access
		$this->cart_data = $product_data;
		return $this->cart_data;
  	}

	/**
	 * @param string $key
	 * @param bool $recalculate
	 * @return array
	 */
	public function getProduct($key, $recalculate = false ) {
		if($recalculate){
			$this->getProducts(true);
		}
		return has_value($this->cust_data['cart'][$key]) ? $this->cust_data['cart'][$key] : array();
	}


	/**
	 * Collect product information for cart based on user selections
	 * Function can be used to get totals and other product information
	 * (based on user selection) as it is before getting into cart or after
	 * @param int $product_id
	 * @param int $quantity
	 * @param array $options
	 * @return array
	 */
	public function buildProductDetails( $product_id, $quantity = 0, $options = array()) {
		if (!has_value($product_id) || !is_numeric($product_id) || $quantity == 0) {
			return array();
		}	

		$stock = TRUE;
		/**
		 * @var $sf_product_mdl ModelCatalogProduct
		 */
		$sf_product_mdl = $this->load->model('catalog/product', 'storefront');
        $elements_with_options = HtmlElementFactory::getElementsWithOptions();

  	  	$product_query = $sf_product_mdl->getProductDataForCart($product_id);
  	  	if ( count($product_query) <= 0 || $product_query['call_to_order']) {
  	  		return array();
  	  	}
  		$option_price = 0;
  		$option_data = array();
    	$groups = array();

  		//Process each option and value	
  		foreach ($options as $product_option_id => $product_option_value_id) {
    	    //skip empty values
    	    if ($product_option_value_id == '' || (is_array($product_option_value_id) && !$product_option_value_id)) {
    	        continue;
    	    }

            $option_query = $sf_product_mdl->getProductOption($product_id, $product_option_id);
            $element_type = $option_query['element_type'];

    	    if (!in_array($element_type, $elements_with_options)) {
    	    	//This is single value element, get all values and expect only one	
    	    	$option_value_query = $sf_product_mdl->getProductOptionValues($product_id, $product_option_id);
    	    	$option_value_query = $option_value_query[0];
    	    	//Set value from input
    	    	$option_value_query['name'] = $this->db->escape($options[$product_option_id]);
    	    } else {
    	    	//is multivalue option type
    	    	if(is_array($product_option_value_id)){
    	    		$option_value_queries = array();
    	    		foreach($product_option_value_id as $val_id){
    	    			$option_value_queries[$val_id] = $sf_product_mdl->getProductOptionValue($product_id, $val_id);
    	    		}
    	    	}else{
    	    		$option_value_query = $sf_product_mdl->getProductOptionValue($product_id, (int)$product_option_value_id);
    	    	}
    	    }

    	    if( $option_value_query ){
            	//if group option load price from parent value
            	if ( $option_value_query['group_id'] && !in_array($option_value_query['group_id'], $groups) ) {
            		$group_value_query = $sf_product_mdl->getProductOptionValue($product_id, $option_value_query['group_id']);
            		$option_value_query['prefix'] = $group_value_query['prefix'];
            		$option_value_query['price'] = $group_value_query['price'];
            		$groups[] = $option_value_query['group_id'];
            	}
            	$option_data[] = array( 'product_option_value_id' => $option_value_query['product_option_value_id'],
			                            'product_option_id'       => $product_option_id,
	                                    'name'                    => $option_query['name'],
		                                'element_type'            => $element_type,
		                                'settings'                => $option_query['settings'],
            							'value'                   => $option_value_query['name'],
            							'prefix'                  => $option_value_query['prefix'],
            							'price'                   => $option_value_query['price'],
            							'sku'                     => $option_value_query['sku'],
            							'weight'                  => $option_value_query['weight'],
            							'weight_type'             => $option_value_query['weight_type']);

            	//check if need to track stock and we have it 
            	if ( $option_value_query['subtract'] && $option_value_query['quantity'] < $quantity ) {
            		$stock = FALSE;
            	}
            	$op_stock_trackable += $option_value_query['subtract'];
            	unset($option_value_query);
            	
            } else if( $option_value_queries ) {
            	foreach($option_value_queries as $item){
            		$option_data[] = array( 'product_option_value_id' => $item['product_option_value_id'],
            								'name'                    => $option_query['name'],
            								'value'                   => $item['name'],
            								'prefix'                  => $item['prefix'],
            								'price'                   => $item['price'],
            								'sku'                     => $item['sku'],
            								'weight'                  => $item['weight'],
            								'weight_type'             => $item['weight_type']);
            		//check if need to track stock and we have it 
            		if ( $item['subtract'] && $item['quantity'] < $quantity ) {
            			$stock = FALSE;
            		}
            		$op_stock_trackable += $option_value_query['subtract'];
            	}
            	unset($option_value_queries);
            }
  		} // end of options build
    	
		//needed for promotion
		$discount_quantity = 0; // this is used to calculate total QTY of 1 product in the cart

		// check is product is in cart and calculate quantity to define item price with product discount
    	foreach ($this->cust_data['cart'] as $k => $v) {
    	    $array2 = explode(':', $k);
    	    if ($array2[0] == $product_id) {
				$discount_quantity += $v['qty'];
    	    }
    	}
		if(!$discount_quantity){
			$discount_quantity = $quantity;
		}

    	//Apply group and quantity discount first and if non, reply product discount
    	$price = $this->promotion->getProductQtyDiscount($product_id, $discount_quantity);
    	if ( !$price ) {
    	    $price = $this->promotion->getProductSpecial($product_id );
    	}
    	//Still no special price, use regulr price
    	if ( !$price ) {
    	    $price = $product_query['price'];
    	} 
    	
    	//Need to round price after discounts and specials 
    	//round main price to currency decimal_place setting (most common 2, but still...)
		$currency = $this->registry->get('currency')->getCurrency();
		$decimal_place = (int)$currency['decimal_place'];
		$decimal_place = !$decimal_place ? 2 : $decimal_place;
    	$price = round($price, $decimal_place);
    	
    	foreach ( $option_data as $item ) {
    	    if ( $item['prefix'] == '%' ) {
    	    	$option_price += $price * $item['price'] / 100;
    	    } else {
    	    	$option_price += $item['price'];
    	    }
    	}
    	//round option price to currency decimal_place setting (most common 2, but still...)
    	$option_price = round($option_price, $decimal_place);

		// product downloads
    	$download_data = $this->download->getProductOrderDownloads($product_id);
    	
    	//check if we need to check main product stock. Do only if no stock trakable options selected
    	if ( !$op_stock_trackable && $product_query['subtract'] && $product_query['quantity'] < $quantity ) {
    	    $stock = FALSE;
    	}
    	
  		$result = array(
    	    'product_id'   => $product_query['product_id'],
    	    'name'         => $product_query['name'],
    	    'model'        => $product_query['model'],
    	    'shipping'     => $product_query['shipping'],
    	    'option'       => $option_data,
    	    'download'     => $download_data,
    	    'quantity'     => $quantity,
    	    'minimum'      => $product_query['minimum'],
    	    'maximum'      => $product_query['maximum'],
    	    'stock'        => $stock,
    	    'price'        => ($price + $option_price),
    	    'total'        => ($price + $option_price) * $quantity,
    	    'tax_class_id' => $product_query['tax_class_id'],
    	    'weight'       => $product_query['weight'],
    	    'weight_class' => $product_query['weight_class'],
    	    'length'       => $product_query['length'],
    	    'width'        => $product_query['width'],
    	    'height'       => $product_query['height'],
    	    'length_class' => $product_query['length_class'],					
    	    'ship_individually' => $product_query['ship_individually'],					
    	    'shipping_price' 	=> $product_query['shipping_price'],					
    	    'free_shipping'		=> $product_query['free_shipping'],
    	    'sku'			=> $product_query['sku']
  		);
 		return $result;	
	}

	/**
	 * @param int $product_id
	 * @param int $qty
	 * @param array $options
	 */
	public function add($product_id, $qty = 1, $options = array()) {
		$product_id = (int)$product_id;
    	if (!$options) {
      		$key = $product_id;
    	} else {
      		$key = $product_id . ':' . md5(serialize($options));
    	}
    	
		if ((int)$qty && ((int)$qty > 0)) {
    		if (!isset($this->cust_data['cart'][$key])) {
      			$this->cust_data['cart'][$key]['qty'] = (int)$qty;
    		} else {
      			$this->cust_data['cart'][$key]['qty'] += (int)$qty;
    		}
    		//TODO Add validation for correct options for the product and add error return or more stable behaviour
			$this->cust_data['cart'][$key]['options'] = $options;
		}

		//if logged in customer, save cart content
    	if ($this->customer && ($this->customer->isLogged() || $this->customer->isUnauthCustomer()) ) {
    		$this->customer->saveCustomerCart();
    	}
		
		#reload data for the cart
		$this->getProducts(TRUE);
  	}

	/**
	 * @param $key
	 * @param $data
	 * @return null
	 */
	public function addVirtual($key, $data) {

		if ( !has_value($data) ) {
			return null;
		}

		if ( !isset($this->cust_data['cart']['virtual']) || !is_array($this->cust_data['cart']['virtual']) ) {
			$this->cust_data['cart']['virtual'] = array();
		}

		$this->cust_data['cart']['virtual'][$key] = $data;
	}

	/**
	 * @return array
	 */
	public function getVirtualProducts() {
		return (array)$this->cust_data['cart']['virtual'];
	}

	/**
	 * @param $key
	 */
	public function removeVirtual($key) {
		if ( isset($this->cust_data['cart']['virtual'][$key]) ) {
			unset($this->cust_data['cart']['virtual'][$key]);
			if ( !has_value($this->cust_data['cart']['virtual']) ) {
				unset($this->cust_data['cart']['virtual']);
			}
		}
	}

	/**
	 * @param string $key
	 * @param int $qty
	 */
	public function update($key, $qty) {
    	if ((int)$qty && ((int)$qty > 0)) {
      		$this->cust_data['cart'][$key]['qty'] = (int)$qty;
    	} else {
	  		$this->remove($key);
		}
		
		//save if logged in customer
    	if ($this->customer && ($this->customer->isLogged() || $this->customer->isUnauthCustomer()) ) {
    		$this->customer->saveCustomerCart();
    	}

		#reload data for the cart
		$this->getProducts(TRUE);
  	}

	/**
	 * @param $key
	 */
	public function remove($key) {
		if (isset($this->cust_data['cart'][$key])) {
     		unset($this->cust_data['cart'][$key]);
			// remove balance credit from session when any products removed from cart
			unset($this->cust_data['used_balance']);

			//if logged in customer, save cart content
     		if ($this->customer && ($this->customer->isLogged() || $this->customer->isUnauthCustomer()) ) {
    			$this->customer->saveCustomerCart();
    		}

  		}
	}

  	public function clear() {
		$this->cust_data['cart'] = array();		
 	}

	/**
	 * Accumulative weight for all or requested products
	 * @param array $product_ids
	 * @return int
	 */
	public function getWeight( $product_ids = array() ) {
		$weight = 0;
		$products = $this->getProducts();
    	foreach ($products as $product) {
      		if (count($product_ids) > 0 && !in_array((string)$product['product_id'], $product_ids) ) {
      			continue;	
      		}

			if ($product['shipping']) {
				$product_weight = $product['weight'];
				// if product_option has weight value
				if($product['option']){
					$hard = false;
					foreach($product['option'] as $option){
						if($option['weight'] == 0) continue; // if weight not set - skip
						if($option['weight_type'] != '%'){
							//If weight was set by option hard and other option sets another weight hard - ignore it
							//skip negative weight. Negative allowed only for % based weight
							if ($hard || $option['weight'] < 0) {
								continue;
							}
						
							$hard = true;
							$product_weight = $this->weight->convert($option['weight'], $option['weight_type'], $product['weight_class']);
						}else{
							//We need product base weight for % calculation
							$temp = ($option['weight'] * $product['weight']/100) + $product['weight'];
							$product_weight = $this->weight->convert($temp, $option['weight_type'], $this->config->get('config_weight_class'));
						}
					}
				}
      			$weight += $this->weight->convert($product_weight * $product['quantity'], $product['weight_class'], $this->config->get('config_weight_class'));
			}
		}
		return $weight;
	}

	/**
	 * Products with no special settings for shippment
	 * @return array
	 */
	public function basicShippingProducts() {
		$basic_ship_products = array();
		$products = $this->getProducts();
    	foreach ($products as $product) {
			if ($product['shipping'] && !$product['ship_individually'] && !$product['free_shipping'] && $product['shipping_price'] == 0 ) {
				$basic_ship_products[] = $product;	
			}
		}
		return $basic_ship_products;
	}

	/**
	 * Products with special settings for shippment
	 * @return array
	 */
	public function specialShippingProducts() {
		$special_ship_products = array();
		$products = $this->getProducts();
    	foreach ($products as $product) {
			if ($product['shipping'] && ($product['ship_individually'] || $product['free_shipping'] || $product['shipping_price'] > 0 )) {
				$special_ship_products[] = $product;	
			}
		}
		return $special_ship_products;
	}

	/**
	 * Check if all products are free shipping 
	 * @return bool
	 */
	public function areAllFreeShipping() {
		$all_free_shipping = false;
		$products = $this->getProducts();
    	foreach ($products as $product) {
			if ( !$product['shipping'] || ($product['shipping'] && $product['free_shipping']) ) {
				$all_free_shipping = true;
			} else {
				return false;
			}
		}
		return $all_free_shipping;
	}
		
	/**
	 * Set mim quantity on whole cart
	 * @void
	 */
	public function setMinQty() {
		$products = $this->getProducts();
		foreach ($products as $product) {
			if ($product['quantity'] < $product['minimum']) {
				$this->cust_data['cart'][$product['key']]['qty'] = $product['minimum'];
			}
		}
  	}

	/**
	 * Set max quantity on whole cart
	 * @void
	 */
	public function setMaxQty() {
		# If set 0 there is no minimum
		$products = $this->getProducts();
		foreach ($products as $product) {
			if ($product['maximum'] > 0) {
				if ($product['quantity'] > $product['maximum']) {
					$this->cust_data['error'] = $this->language->get('error_quantity_maximum');
					$this->cust_data['cart'][$product['key']]['qty'] = $product['maximum'];
				}
			}
		}
  	}
	
	 /**
	 * Get Sub Total amount for current built order wihout any tax or any promotion
	 * To force recalculate pass argument as TRUE
	 * @param bool $recalculate
	 * @return float
	 */
	public function getSubTotal( $recalculate = false) {
		#check if value already set
		if ( has_value($this->sub_total) && !$recalculate) {
			return $this->sub_total;
		}
		
		$this->sub_total = 0.0;
		$products = $this->getProducts();
		foreach ($products as $product) {
			$this->sub_total += $product['total'];
		}

		return $this->sub_total;
  	}
	
	/**
	* candidate to be depricated
	* @return array
	*/
	public function getTaxes() {
		return $this->getAppliedTaxes();
	}
	
	 /**
	 * Returns all applied taxes on products in the cart
	 * To force recalculate pass argument as TRUE
	 * @param bool $recalculate
	 * @return array
	 */
	public function getAppliedTaxes( $recalculate = false ) {
		#check if value already set
		if ( has_value($this->taxes) && !$recalculate) {
			return $this->taxes;
		}
		$this->taxes = array();
		// taxes for products
		$products = $this->getProducts();
		foreach ($products as $product) {
			if ($product['tax_class_id']) {
				//save total for each tax class to build clear tax display later
				if (!isset($this->taxes[$product['tax_class_id']])) {
					$this->taxes[$product['tax_class_id']]['total'] = $product['total'];
					$this->taxes[$product['tax_class_id']]['tax'] = $this->tax->calcTotalTaxAmount($product['total'], $product['tax_class_id']);
				} else {
					$this->taxes[$product['tax_class_id']]['total'] += $product['total'];
					$this->taxes[$product['tax_class_id']]['tax'] += $this->tax->calcTotalTaxAmount($product['total'], $product['tax_class_id']);
				}
			}
		}
		//tax for shipping
		if($this->cust_data['shipping_method']['tax_class_id']){
			$tax_id = $this->cust_data['shipping_method']['tax_class_id'];
			$cost = $this->cust_data['shipping_method']['cost'];
			if (!isset($this->taxes[$tax_id])) {
				$this->taxes[$tax_id]['tax'] = $this->tax->calcTotalTaxAmount($cost, $tax_id);
			} else {
				$this->taxes[$tax_id]['tax'] += $this->tax->calcTotalTaxAmount($cost, $tax_id);
			}
		}
		return $this->taxes;
  	}


	 /**
	 * Get Total amount for current built order with applicable taxes ( order value )
	 * Can be used for total value in shipping insurace or to culculate total savings.
	 * To force recalculate pass argument as TRUE
	 * @param bool $recalculate
	 * @return float
	 */
	public function getTotal( $recalculate = false ) {
		#check if value already set
		if ( has_value($this->total_value) && !$recalculate) {
			return $this->total_value;
		}
		$this->total_value = 0.0;
		$products = $this->getProducts();
		foreach ($products as $product) {
			$this->total_value += $product['total'] + $this->tax->calcTotalTaxAmount($product['total'], $product['tax_class_id']);
		}

		return $this->total_value;
  	}


	/**
	 * Get Total amount for current built cart with all totals, taxes and applied promotions
	 * To force recalculate pass argument as TRUE
	 * @param bool $recalculate
	 * @return int
	 */
	public function getFinalTotal( $recalculate = false ) {
		#check if value already set
		if ( has_value($this->total_data) && has_value($this->final_total) && !$recalculate) {
			return $this->final_total;
		}
		$this->final_total = 0.0;
		$this->total_data = array();

		$total_data = array();
		$calc_order	= array();
		$total = 0;
		
		$taxes = $this->getAppliedTaxes( $recalculate );		 
		//force storefront load (if called from admin)
		/**
		 * @var $sf_checkout_mdl ModelCheckoutExtension
		 */
		$sf_checkout_mdl = $this->load->model('checkout/extension', 'storefront');

		$total_extns = $sf_checkout_mdl->getExtensions('total');

		foreach ($total_extns as $value) {
			$calc_order[$value['key']] = (int)$this->config->get($value['key'] . '_calculation_order');
		}
		array_multisort($calc_order, SORT_ASC, $total_extns);

		foreach ($total_extns as $extn) {
			$sf_total_mdl = $this->load->model('total/' . $extn['key'], 'storefront');
			/**
			 * parameters are references!!!
			 */
			$sf_total_mdl->getTotal($total_data, $total, $taxes, $this->cust_data);
			$sf_total_mdl = null;
		}

	  	$this->total_data = $total_data;	
  		$this->final_total = $total;  		
  		return $this->final_total;
  	}

	/**
	 * Get Total Data for current built cart with all totals, taxes and applied promotions
	 * To force recalculate pass argument as TRUE
	 * @param bool $recalculate
	 * @return mixed
	 */
	public function getFinalTotalData( $recalculate = false ) {
		#check if value already set
		if ( has_value($this->total_data) && has_value($this->final_total) && !$recalculate) {
			return $this->total_data;
		}
		$this->final_total = $this->getFinalTotal($recalculate);
		return $this->total_data;
	}

	/**
	 * Function to build total display based on enabled extensions/settings for total section
	 * To force recalculate pass argument as TRUE
	 * @param bool $recalculate
	 * @return array
	 */
	public function buildTotalDisplay( $recalculate = false ) {
	
		$taxes = $this->getAppliedTaxes( $recalculate );
		$total = $this->getFinalTotal( $recalculate );
		$total_data = $this->getFinalTotalData();

		#sort data for view			  
		$sort_order = array(); 
		foreach ($total_data as $key => $value) {
      		$sort_order[$key] = $value['sort_order'];
    	}
    	array_multisort($sort_order, SORT_ASC, $total_data);
    	//return result in array
    	return array('total' => $total, 'total_data' => $total_data, 'taxes' => $taxes); 	
	}

	/**
	 * Check if order/cart total has minimum amount setting met if it was set
	 * @return bool
	 */
	public function hasMinRequirement() {
		$cf_total_min = $this->config->get('total_order_minimum'); 
		if ( $cf_total_min && $cf_total_min > $this->getSubTotal() ) {
			return FALSE;
		}
		return TRUE;	 
	} 

	/**
	* Check if order/cart total has maximum amount setting met if it was set 
	* @return bool
	*/
 	public function hasMaxRequirement() {
		$cf_total_max = $this->config->get('total_order_maximum'); 
		if ( $cf_total_max && $cf_total_max < $this->getSubTotal() ) {
			return FALSE;
		}
		return TRUE; 
	} 
	
	/**
	 * Return count of products in the cart including quantity per product
	 * @return int
	 */
	public function countProducts() {
		$qty = 0;
		foreach ( $this->cust_data['cart'] as $product) {
			$qty += $product['qty'];
		}
		return $qty;
	}

	/**
	* Return 0/[count] for products in the cart (quantity is not counted)
	* @return int
	*/   		  
  	public function hasProducts() {
    	return count($this->cust_data['cart']);
  	}
  
	/**
	* Return TRUE if all products have stock 
	* @return bool
	*/   		    
  	public function hasStock() {
		$stock = TRUE;
		$products = $this->getProducts();
		foreach ($products as $product) {
			if (!$product['stock']) {
	    		$stock = FALSE;
			}
		}
		
    	return $stock;
  	}
  
	/**
	* Return FALSE if all products do NOT require shipping
	* @return bool
	*/   		    
  	public function hasShipping() {
		$shipping = FALSE;
	    $products = $this->getProducts();
		foreach ($products as $product) {
	  		if ($product['shipping']) {
	    		$shipping = TRUE;
				break;
	  		}		
		}
		
		return $shipping;
	}
	
	/**
	* Return FALSE if all products do NOT have download type
	* @return bool
	*/   		    
  	public function hasDownload() {
		$download = FALSE;
	    $products = $this->getProducts();
		foreach ($products as $product) {
	  		if ($product['download']) {
	    		$download = TRUE;
				break;
	  		}		
		}
		
		return $download;
	}	
}