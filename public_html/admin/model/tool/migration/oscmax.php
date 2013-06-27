<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

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
require_once DIR_ROOT.'/admin/model/tool/migration/interface_migration.php';

class Migration_Oscmax implements Migration {

	private $data;
	private $config;
	private $db;
	private $error_msg;

	function __construct($migrate_data, $oc_config) {
		$this->config = $oc_config;
		$this->data = $migrate_data;
		$this->error_msg = "";
	}

	function __destruct() {
	}
	public function getName() {
		return 'OSCMax';
	}
    public function getVersion() {
        return '2.2RC2';
    }

	public function getCategories() {
		$this->error_msg = "";
		$this->db = mysql_connect($this->data[ 'db_host' ], $this->data[ 'db_user' ], $this->data[ 'db_password' ], true);
		mysql_select_db($this->data[ 'db_name' ], $this->db);

		// for now use default language
		$languages_id = 1;

		$categories_query = "SELECT	c.categories_id as category_id,
									cd.categories_name as name,
									'' as description,
									CONCAT('categories/',c.categories_image) as image,
									c.parent_id,
									c.sort_order
								FROM " . $this->data[ 'db_prefix' ] . "categories c, " . $this->data[ 'db_prefix' ] . "categories_description cd
								WHERE c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "'
								ORDER BY c.sort_order, cd.categories_name";
		$categories = mysql_query($categories_query, $this->db);
		if (!$categories) {
			$this->error_msg = 'Migration Error: ' . mysql_error() . '<br>File :' . __FILE__ . '<br>Line :' . __LINE__ . '<br>';
			return false;
		}

		$result = array();
		while ($item = mysql_fetch_assoc($categories)) {
			$result[ $item[ 'category_id' ] ] = $item;
		}

		mysql_free_result($categories);
		mysql_close($this->db);

		return $result;
	}

	public function getManufacturers() {
		$this->error_msg = "";
		$this->db = mysql_connect($this->data[ 'db_host' ], $this->data[ 'db_user' ], $this->data[ 'db_password' ], true);
		mysql_select_db($this->data[ 'db_name' ], $this->db);

		$sql_query = "SELECT manufacturers_id as manufacturer_id,
							manufacturers_name as name,
							CONCAT('manufacturers/',manufacturers_image) as image
                      FROM " . $this->data[ 'db_prefix' ] . "manufacturers
                      ORDER BY manufacturers_name";
		$items = mysql_query($sql_query, $this->db);
		if (!$items) {
			$this->error_msg = 'Migration Error: ' . mysql_error() . '<br>File :' . __FILE__ . '<br>Line :' . __LINE__ . '<br>';
			return false;
		}

		$result = array();
		while ($item = mysql_fetch_assoc($items)) {
			$result[ $item[ 'manufacturer_id' ] ] = $item;
		}

		mysql_free_result($items);
		mysql_close($this->db);

		return $result;
	}

	public function getProducts() {
		$this->error_msg = "";
		$this->db = mysql_connect($this->data[ 'db_host' ], $this->data[ 'db_user' ], $this->data[ 'db_password' ], true);
		mysql_select_db($this->data[ 'db_name' ], $this->db);

		// for now use default language
		$languages_id = 1;

		 $products_query = "SELECT   p.products_id as product_id,
									p.products_model as model,
									p.products_quantity as quantity,
									'7' as stock_status_id,
									CONCAT('products/',p.products_image) as image,
									p.manufacturers_id as manufacturer_id,
									'1' as shipping,
									p.products_price as price,
									pd.products_name as name,
									pd.products_description as description,
									'9' as tax_class_id,
									p.products_date_available as date_available,
									p.products_weight as weight,
									'5' as weight_class_id,
									p.products_status as status,
									p.products_date_added as date_added
							FROM	" . $this->data[ 'db_prefix' ] . "products p
							LEFT JOIN       " . $this->data[ 'db_prefix' ] . "products_description pd
							 	ON pd.products_id = p.products_id AND pd.language_id='" . (int)$languages_id . "'";

		$products = mysql_query($products_query, $this->db);
		if (!$products) {
			$this->error_msg = 'Migration Error: ' . mysql_error() . '<br>File :' . __FILE__ . '<br>Line :' . __LINE__ . '<br>';
			return false;
		}

		$result = array();
		while ($item = mysql_fetch_assoc($products)) {
			$result[ $item[ 'product_id' ] ] = $item;
		}

		//add categories id
		$sql_query = "SELECT categories_id, products_id
                      FROM " . $this->data[ 'db_prefix' ] . "products_to_categories";
		$categories = mysql_query($sql_query, $this->db);
		if (!$categories) {
			$this->error_msg = 'Migration Error: ' . mysql_error() . '<br>File :' . __FILE__ . '<br>Line :' . __LINE__ . '<br>';
			return false;
		}

		while ($item = mysql_fetch_assoc($categories)) {
			if (!empty($result[ $item[ 'products_id' ] ]))
				$result[ $item[ 'products_id' ] ][ 'product_category' ][ ] = $item[ 'categories_id' ];
		}


		//add reviews
		$sql_query = "SELECT products_id as product_id,
						r.customers_id as review_customer_id,
						r.customers_name as review_author,
						rd.reviews_text as review_text,
						r.reviews_rating as review_rating,
						r.approved as review_status,
						r.date_added as review_date_added,
						r.last_modified as review_date_modified

                      FROM " . $this->data[ 'db_prefix' ] . "reviews r
                      LEFT JOIN 	" . $this->data[ 'db_prefix' ] . "reviews_description rd
                      		ON r.reviews_id = rd.reviews_id AND rd.languages_id='" . (int)$languages_id . "'";
		$reviews = mysql_query($sql_query, $this->db);
		if (!$reviews) {
			$this->error_msg = 'Migration Error: ' . mysql_error() . '<br>File :' . __FILE__ . '<br>Line :' . __LINE__ . '<br>';
			return false;
		}

		while ($item = mysql_fetch_assoc($reviews)) {
			if (!empty($result[ $item[ 'product_id' ] ]))
				$result[ $item[ 'product_id' ] ][ 'reviews' ][ ] = $item;
		}

		mysql_close($this->db);

		return $result;
	}

	public function getCustomers() {
		$this->error_msg = "";
		$this->db = mysql_connect($this->data[ 'db_host' ], $this->data[ 'db_user' ], $this->data[ 'db_password' ], true);
		mysql_select_db($this->data[ 'db_name' ], $this->db);

		$customers_query = "SELECT  c.customers_id as customer_id,
									c.customers_firstname as firstname,
									c.customers_lastname lastname,
									c.customers_email_address as email,
									c.customers_telephone as telephone,
									c.customers_fax as fax,
									c.customers_password as password,
									c.customers_newsletter as newsletter
							FROM " . $this->data[ 'db_prefix' ] . "customers c ";

		$customers = mysql_query($customers_query, $this->db);
		if (!$customers) {
			$this->error_msg = 'Migration Error: ' . mysql_error() . '<br>File :' . __FILE__ . '<br>Line :' . __LINE__ . '<br>';
			return false;
		}

		$result = array();
		while ($customer = mysql_fetch_assoc($customers)) {
			$result[ $customer[ 'customers_id' ] ] = $customer;
		}

		// add customers addresses
		$address_query = "SELECT a.customers_id,
								a.entry_company as company,
								a.entry_firstname as firstname,
								a.entry_lastname as lastname,
								a.entry_street_address as address_1,
								a.entry_postcode as postcode,
								a.entry_city as city,
								a.entry_zone_id as zone_id,
								a.entry_country_id as country_id
						  FROM " . $this->data[ 'db_prefix' ] . "address_book a ";
		$addresses = mysql_query($address_query, $this->db);
		if (!$addresses) {
			$this->error_msg = 'Migration Error: ' . mysql_error() . '<br>File :' . __FILE__ . '<br>Line :' . __LINE__ . '<br>';
			return false;
		}

		while ($address = mysql_fetch_assoc($addresses)) {
			$result[ $address[ 'customers_id' ] ][ 'address' ][ ] = $address;
		}

		mysql_close($this->db);
		return $result;

	}

	public function getOrders() {

	}

	public function getProductOptions(){
		$this->error_msg = "";
		$this->db = mysql_connect($this->data[ 'db_host' ], $this->data[ 'db_user' ], $this->data[ 'db_password' ], true);
		mysql_select_db($this->data[ 'db_name' ], $this->db);
		//options
		$sql = "SELECT DISTINCT pa.products_id as product_id, pa.options_id as product_option_id,
								`products_options_name` as product_option_name,
								`products_options_track_stock` as subtract,
								  CASE WHEN `products_options_type`=0 THEN 'S'
									WHEN `products_options_type`=4 THEN 'C'
								    WHEN `products_options_type`=3 THEN 'R' END as element_type,
								  `products_options_length`,
								  `products_options_comment`,
								  po.products_options_sort_order as sort_order,
								  0 as products_text_attributes_id
				FROM products_options po
				LEFT JOIN products_options_types pot ON pot.products_options_types_id = po.products_options_type AND pot.language_id=1
				RIGHT JOIN products_attributes pa	ON pa.options_id = po.products_options_id
				WHERE po.language_id=1
				UNION
				SELECT DISTINCT ptae.products_id,
					NULL as product_option_id,
					products_text_attributes_name as product_option_name,
					0 as subtract,
					'I' as element_type,
					'' as products_options_length,
					'' as products_options_comment,
					'-1' as sort_order,
					pta.products_text_attributes_id
				FROM products_text_attributes_enabled ptae
				LEFT JOIN products_text_attributes pta ON pta.products_text_attributes_id = ptae.products_text_attributes_id
				LEFT JOIN products p ON p.products_id = ptae.products_id
				WHERE ptae.products_id>0 AND products_text_attributes_name<>''
		order by product_id, product_option_id, sort_order";
		$items = mysql_query($sql, $this->db);
		if (!$items) {
			$this->error_msg = 'Migration Error: ' . mysql_error() . '<br>File :' . __FILE__ . '<br>Line :' . __LINE__ . '<br>';
			return false;
		}

		$result = array();
		while ($item = mysql_fetch_assoc($items)) {
			$result['product_options'][] = $item;
		}

		mysql_free_result($items);

		//option values
		$sql = "SELECT DISTINCT pa.price_prefix, pa.options_values_price as price, pa.products_id as product_id, povpo.products_options_id as product_option_id,
							povpo.products_options_values_id as product_option_value_id,
							pov.products_options_values_name as product_option_value_name,
		0 as products_text_attributes_id
						FROM products_options_values_to_products_options povpo
						LEFT JOIN products_options_values pov
							ON pov.products_options_values_id = povpo.products_options_values_id AND pov.language_id=1
						RIGHT JOIN products_attributes pa
							ON pa.options_values_id = povpo.products_options_values_id AND pa.options_id = povpo.products_options_id
		UNION
		SELECT DISTINCT '' as price_prefix, '' as price, ptae.products_id as product_id,
			NULL as product_option_id,
			NULL as product_option_value_id,
			'' as product_option_value_name, pta.products_text_attributes_id
		FROM products_text_attributes_enabled ptae
		LEFT JOIN products_text_attributes pta ON pta.products_text_attributes_id = ptae.products_text_attributes_id
		LEFT JOIN products p ON p.products_id = ptae.products_id
		WHERE ptae.products_id>0 AND products_text_attributes_name<>''
		order by product_id, product_option_id";
		$items = mysql_query($sql, $this->db);
		if (!$items) {
			$this->error_msg = 'Migration Error: ' . mysql_error() . '<br>File :' . __FILE__ . '<br>Line :' . __LINE__ . '<br>';
			return false;
		}


		while ($item = mysql_fetch_assoc($items)) {
			$result['product_option_values'][] = $item;
		}

		mysql_free_result($items);


		//products option values
		$sql = "SELECT *
				FROM products_attributes";
		$items = mysql_query($sql, $this->db);
		if (!$items) {
			$this->error_msg = 'Migration Error: ' . mysql_error() . '<br>File :' . __FILE__ . '<br>Line :' . __LINE__ . '<br>';
			return false;
		}


		while ($item = mysql_fetch_assoc($items)) {
			$result['product_attributes'][] = $item;
		}

		mysql_free_result($items);

		mysql_close($this->db);

		return $result;
	}

	public function getErrors() {
		return $this->error_msg;
	}


}