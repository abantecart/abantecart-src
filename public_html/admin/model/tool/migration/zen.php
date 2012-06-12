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
require "interface_migration.php";

class Migration_Zen implements Migration {

	private $data;
	private $config;
	private $db;

	function __construct($migrate_data, $oc_config) {
		$this->config = $oc_config;
		$this->data = $migrate_data;
		//        $this->db = mysql_connect($this->data['db_host'], $this->data['db_user'], $this->data['db_password']);
		//		mysql_select_db($this->data['db_name'], $this->db);
	}

    public function getVersion() {
        return '1.3.9h';
    }


	public function getCategories() {
		$this->db = mysql_connect($this->data[ 'db_host' ], $this->data[ 'db_user' ], $this->data[ 'db_password' ], true);
		mysql_select_db($this->data[ 'db_name' ], $this->db);

		// for now use default language
		$languages_id = 1;

		$categories_query = "
            SELECT
                c.categories_id,
                cd.categories_name as name,
                '' as description,
                c.categories_image as image,
                c.parent_id,
                c.sort_order
            FROM
                " . $this->data[ 'db_prefix' ] . "categories c,
                " . $this->data[ 'db_prefix' ] . "categories_description cd
            WHERE
                c.categories_id = cd.categories_id
                and cd.language_id = '" . (int)$languages_id . "'
                order by c.sort_order, cd.categories_name";
		$categories = mysql_query($categories_query, $this->db);
		if (!$categories) die('Invalid query: ' . mysql_error());

		$result = array();
		while ($item = mysql_fetch_assoc($categories)) {
			$result[ $item[ 'categories_id' ] ] = $item;
		}

		mysql_close($this->db);

		return $result;
	}

	public function getManufacturers() {
		$this->db = mysql_connect($this->data[ 'db_host' ], $this->data[ 'db_user' ], $this->data[ 'db_password' ], true);
		mysql_select_db($this->data[ 'db_name' ], $this->db);

		$sql_query = "SELECT manufacturers_id,
							 manufacturers_name as name,
							 manufacturers_image as image
                      FROM " . $this->data[ 'db_prefix' ] . "manufacturers
                      ORDER by manufacturers_name";
		$items = mysql_query($sql_query, $this->db);
		if (!$items) die('Invalid query: ' . mysql_error());

		$result = array();
		while ($item = mysql_fetch_assoc($items)) {
			$result[ $item[ 'manufacturers_id' ] ] = $item;
		}

		mysql_close($this->db);

		return $result;
	}

	public function getProducts() {
		$this->db = mysql_connect($this->data[ 'db_host' ], $this->data[ 'db_user' ], $this->data[ 'db_password' ], true);
		mysql_select_db($this->data[ 'db_name' ], $this->db);

		// for now use default language
		$languages_id = 1;

		$products_query = "SELECT p.products_id,
									p.products_model as model,
									p.products_quantity as quantity,
									'7' as stock_status_id,
									p.products_image as image,
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
								FROM " . $this->data[ 'db_prefix' ] . "products p,
									 " . $this->data[ 'db_prefix' ] . "products_description pd
								WHERE pd.products_id = p.products_id
									  AND pd.language_id = '" . (int)$languages_id . "'";
		$items = mysql_query($products_query, $this->db);
		if (!$items) die('Invalid query: ' . mysql_error());

		$result = array();
		while ($item = mysql_fetch_assoc($items)) {
			$result[ $item[ 'products_id' ] ] = $item;
		}

		//add categories id
		$sql_query = "
            select categories_id, products_id
            from " . $this->data[ 'db_prefix' ] . "products_to_categories";
		$items = mysql_query($sql_query, $this->db);
		if (!$items) die('Invalid query: ' . mysql_error());

		while ($item = mysql_fetch_assoc($items)) {
			if (!empty($result[ $item[ 'products_id' ] ]))
				$result[ $item[ 'products_id' ] ][ 'product_category' ][ ] = $item[ 'categories_id' ];
		}

		//add product options
		$sql_query = " SELECT patrib.products_options_sort_order,
                              popt.products_options_name,
                              patrib.options_values_price,
                              patrib.price_prefix,
                              pov.products_options_values_name
						FROM
							" . $this->data[ 'db_prefix' ] . "products_options popt,
							" . $this->data[ 'db_prefix' ] . "products_attributes patrib,
							" . $this->data[ 'db_prefix' ] . "products_options_values pov
						WHERE patrib.products_id='#PID'
							AND patrib.options_id = popt.products_options_id
							AND patrib.options_values_id = pov.products_options_values_id
							AND popt.language_id = '" . (int)$languages_id . "'
						ORDER by patrib.products_attributes_id";

		foreach ($result as $id => $product) {
			$sql = str_replace('#PID', $id, $sql_query);
			$items = mysql_query($sql, $this->db);
			if (!mysql_num_rows($items)) continue;
			while ($item = mysql_fetch_assoc($items)) {
				$result[ $id ][ 'attributes' ][ ] = array(
															'sort_order' => $item[ 'products_options_sort_order' ],
															'name' => $item[ 'products_options_name' ],
															'price' => $item[ 'options_values_price' ],
															'prefix' => $item[ 'price_prefix' ],
															'option_name' => $item[ 'products_options_values_name' ],
				);
			}
		}
		mysql_close($this->db);

		return $result;
	}

	public function getCustomers() {
		$this->db = mysql_connect($this->data[ 'db_host' ], $this->data[ 'db_user' ], $this->data[ 'db_password' ], true);
		mysql_select_db($this->data[ 'db_name' ], $this->db);

		$customers_query = "
            SELECT
                c.customers_id,
                c.customers_firstname as firstname,
                c.customers_lastname lastname,
                c.customers_email_address as email,
                c.customers_telephone as telephone,
                c.customers_fax as fax,
                c.customers_password as password,
                c.customers_newsletter as newsletter                
            FROM
                " . $this->data[ 'db_prefix' ] . "customers c ";

		$customers = mysql_query($customers_query, $this->db);
		if (!$customers) die('Invalid query: ' . mysql_error());
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
		if (!$addresses) die('Invalid query: ' . mysql_error());
		while ($address = mysql_fetch_assoc($addresses)) {
			$result[ $address[ 'customers_id' ] ][ 'address' ][ ] = $address;
		}

		mysql_close($this->db);

		return $result;

	}

	public function getOrders() {
	}

	public function getErrors() {
		return $this->error_msg;
	}
}