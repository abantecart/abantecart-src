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
if (!defined('DIR_CORE') || !IS_ADMIN) {
	header('Location: static_pages/');
}

require_once DIR_ROOT . '/admin/model/tool/migration/interface_migration.php';

class Migration_OC implements Migration {

	private $data;
	private $config;
	private $src_db;
	private $error_msg;
	private $language_id_src;


	function __construct($migrate_data, $oc_config) {
		$this->config = $oc_config;
		$this->data = $migrate_data;
		$this->error_msg = "";
		/**
		 * @var ADB
		 */
		if ($migrate_data) {
			require_once DIR_DATABASE . 'mysql.php';
			$this->src_db = new Mysql($this->data['db_host'], $this->data['db_user'], $this->data['db_password'], $this->data['db_name'], true);
		}
	}

	public function getName() {
		return 'OpenCart';
	}

	public function getVersion() {
		return '1.4.9';
	}

	private function getSourceLanguageId(){
		if(!$this->language_id_src){
			$result = $this->src_db->query("SELECT language_id
											FROM " . $this->data['db_prefix'] . "language
											WHERE `code` = (SELECT `value`
															FROM " . $this->data['db_prefix'] . "setting
															WHERE `key`='config_admin_language');");
			$this->language_id_src = $result->row['language_id'];
		}
		return $this->language_id_src;
	}

	public function getCategories() {
		// for now use default language
		$language_id = $this->getSourceLanguageId();

		$categories_query = "SELECT c.category_id,
									cd.name,
									cd.description,
									c.image,
									c.parent_id,
									c.sort_order
                            FROM " . $this->data['db_prefix'] . "category c, " . $this->data['db_prefix'] . "category_description cd
                            WHERE c.category_id = cd.category_id AND cd.language_id = '" . (int)$language_id . "'
                            ORDER BY c.sort_order, cd.name";
		$categories = $this->src_db->query( $categories_query, true);
		if (!$categories) {
			$this->error_msg = 'Migration Error: ' . $this->src_db->error . '<br>';
			return false;
		}

		$result = array();
		foreach ($categories->rows as $item) {
			$result[$item['category_id']] = $item;
			$result[$item['category_id']]['image']= array();
			if($item['image']){
				$img_uri = $this->data['cart_url'];
				if(substr($img_uri,-1)!='/'){
					$img_uri .= '/';
				}
				$img_uri .= 'image/';
				$result[$item['category_id']]['image']['db'] = str_replace(' ', '%20', $img_uri. $item['image']);
			}
		}

		return $result;
	}

	public function getManufacturers() {
		$sql_query = "
            SELECT manufacturer_id, name, image
            FROM " . $this->data['db_prefix'] . "manufacturer
            ORDER BY name";
		$items = $this->src_db->query( $sql_query, true);
		if (!$items) {
			$this->error_msg = 'Migration Error: ' . $this->src_db->error . '<br>';
			return false;
		}

		$result = array();
		foreach ($items->rows as $item) {
			$result[$item['manufacturer_id']] = $item;
			$result[$item['manufacturer_id']]['image']= array();
			if($item['image']){
				$img_uri = $this->data['cart_url'];
				if(substr($img_uri,-1)!='/'){
					$img_uri .= '/';
				}
				$img_uri .= 'image/';
				$result[$item['manufacturer_id']]['image']['db'] = str_replace(' ', '%20', $img_uri. $item['image']);
			}
		}

		return $result;
	}

	public function getProducts() {
		$this->error_msg = "";
		// for now use default language
		$language_id = $this->getSourceLanguageId();

		$products_query = "
            select
                p.product_id,
                p.model,
                p.quantity,
                p.stock_status_id,
                p.image,
                p.manufacturer_id,
                p.shipping,
                p.price,
                pd.name,
                pd.description,
                p.tax_class_id,
                p.date_available,
                p.weight as weight,
                p.weight_class_id,
                p.status,
                p.date_added
            from
                " . $this->data['db_prefix'] . "product p,
                " . $this->data['db_prefix'] . "product_description pd
            where
                pd.product_id = p.product_id
                and pd.language_id = '" . (int)$language_id . "'";
		$items = $this->src_db->query( $products_query, true);
		if (!$items) {
			$this->error_msg = 'Migration Error: ' . $this->src_db->error . '<br>';
			return false;
		}

		$result = array();
		foreach ($items->rows as $item) {
			$result[$item['product_id']] = $item;
			$item['image'] = trim($item['image']);
			$result[$item['product_id']]['image'] = array();
			if ($item['image']) {
				$img_uri = $this->data['cart_url'];
				if (substr($img_uri, -1) != '/') {
					$img_uri .= '/';
				}
				$img_uri .= 'image/';
				$result[$item['product_id']]['image']['db'] = str_replace(' ', '%20', $img_uri . $item['image']);
				//additional images
				$imgs = $this->src_db->query("SELECT * FROM " . $this->data['db_prefix'] . "product_image WHERE product_id = '".$item['product_id']."' ORDER BY product_id");
				foreach ($imgs->rows as $img) {
					$uri = str_replace(' ', '%20', $img_uri . $img['image']);
					if (!in_array($uri, $result[$img['product_id']]['image'])) {
						$result[$img['product_id']]['image'][] = $uri;
					}
				}
			}
		}

		//add categories id
		$sql_query = "
            select category_id, product_id
            from " . $this->data['db_prefix'] . "product_to_category";
		$items = $this->src_db->query( $sql_query, true);
		if (!$items) {
			$this->error_msg = 'Migration Error: ' . $this->src_db->error . '<br>';
			return false;
		}

		foreach ($items->rows as $item) {
			if (!empty($result[$item['product_id']]))
				$result[$item['product_id']]['product_category'][] = $item['category_id'];
		}

		return $result;
	}

	public function getCustomers() {
		$customers_query = "
            select
                c.customer_id,
                c.firstname,
                c.lastname,
                c.email,
                c.telephone,
                c.fax,
                c.password,
                c.newsletter                
            from
                " . $this->data['db_prefix'] . "customer c ";

		$customers = $this->src_db->query( $customers_query, true);
		if (!$customers) {
			$this->error_msg = 'Migration Error: ' . $this->src_db->error . '<br>';
			return false;
		}
		$result = array();
		foreach ($customers->rows as $customer) {
			$result[$customer['customer_id']] = $customer;
		}

		// add customers addresses
		$address_query = "
            select a.customer_id,
                a.company,
                a.firstname,
                a.lastname,
                a.address_1,
                a.address_2,
                a.postcode,
                a.city,
                a.zone_id,
                a.country_id
            from
                " . $this->data['db_prefix'] . "address a ";
		$addresses = $this->src_db->query( $address_query, true);
		if (!$addresses) {
			$this->error_msg = 'Migration Error: ' . $this->src_db->error . '<br>';
			return false;
		}

		foreach ($addresses->rows as $address) {
			$result[$address['customer_id']]['address'][] = $address;
		}


		return $result;

	}

	public function getOrders() {
		return array();
	}

	public function getErrors() {
		return $this->error_msg;
	}

	public function getCounts() {
		$products = $this->src_db->query("SELECT COUNT(*) as cnt FROM ".$this->data['db_prefix']."product", true);
		$categories = $this->src_db->query("SELECT COUNT(*) as cnt FROM ".$this->data['db_prefix']."category", true);
		$manufacturers = $this->src_db->query("SELECT COUNT(*) as cnt FROM ".$this->data['db_prefix']."manufacturer", true);
		$customers = $this->src_db->query("SELECT COUNT(*) as cnt FROM ".$this->data['db_prefix']."customer", true);

		return array(
			'products' => (int)$products->row['cnt'],
			'categories' => (int)$categories->row['cnt'],
			'manufacturers' => (int)$manufacturers->row['cnt'],
			'customers' => (int)$customers->row['cnt']
		);
	}
}