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
set_time_limit(0);
ini_set("memory_limit", "64M");
clearstatcache();

class ModelToolMigration extends Model {

	const CLASS_LOCATION = 'admin/model/tool/migration/';
	const CLASS_PREFIX = 'Migration_';

	protected $cart = null;
	protected $log = '';
	private $is_error = null;

	private $language_id;

	public function isCartSupported($cart) {
		$file = self::CLASS_LOCATION . $cart . '.php';
		if (!file_exists($file))
			return false;

		require_once($file);
		$name = self::CLASS_PREFIX . ucfirst($cart);
		if (!class_exists($name))
			return false;
		else
			return true;
	}

	public function saveStepData($vars) {
		foreach ($vars as $var) {
			$separator = "";
			if ($var == 'cart_url' && (substr($this->request->post[ $var ], -1) != "/")) {
				$separator = "/";
			}
			$this->session->data[ 'migration' ][ $var ] = !empty($this->request->post[ $var ])
					? $this->request->post[ $var ] . $separator : '';
		}
	}

	public function clearStepData() {
		$this->session->data[ 'migration' ] = array();
	}

	public function isStepData() {
		return !empty($this->session->data[ 'migration' ]);
	}

	/*
		* check cart URL, cart db info, writable directories
		* maybe check db or cart folder structure to confirm cart type
		* also  do  some php lib ( like curl, gd )
		*/
	protected function preCheck() {
		return '';
	}

	protected function addLog($msg,$type='error') {
		$class = $type=='error' ? 'warning' : 'success';
		$class = $type=='attention' ? 'attention' : $class;
		$this->log .= '<p class="'.$class.'">' . $msg . '</p>';
	}

	public function run() {
		$check = $this->preCheck();
		if (!empty($check)) return $check;


		if ($this->session->data[ 'migration' ][ 'erase_existing_data' ]) $this->clearData();

		$cart = $this->session->data[ 'migration' ][ 'cart_type' ];
		require_once self::CLASS_LOCATION . $cart . '.php';
		$name = self::CLASS_PREFIX . ucfirst($cart);
		$this->cart = new $name($this->session->data[ 'migration' ], $this->config);

		if ($this->session->data[ 'migration' ][ 'migrate_products' ]) {
			if (!$this->migrateProducts()) {
				return $this->log;
			}
		}
		if ($this->session->data[ 'migration' ][ 'migrate_customers' ]) {
			if (!$this->migrateCustomers()) {
				return $this->log;
			}
		}
		if ($this->session->data[ 'migration' ][ 'migrate_orders' ]) {
			if (!$this->migrateOrders()) {
				return $this->log;
			}
		}

		$this->clearStepData();
		return $this->log;
	}

	protected function import($sql) {
		foreach (explode(";\n", $sql) as $sql) {
			$sql = trim($sql);
			if ($sql) {
				$result = $this->db->query($sql,true);
				if($result===false){
					$this->addLog($this->db->error);
					return false;
				}
			}
		}
	}

	protected function clearData() {
		// find the default language id
		$languageId = $this->getDefaultLanguageId();

		// start transaction, remove products
		$sql = "START TRANSACTION;\n";

		if ($this->session->data[ 'migration' ][ 'migrate_products' ]) {
			//categories
			$sql .= "DELETE FROM `" . DB_PREFIX . "categories`;\n";
			$sql .= "DELETE FROM `" . DB_PREFIX . "category_descriptions` WHERE language_id='".$languageId."';\n";
			$sql .= "DELETE FROM `" . DB_PREFIX . "categories_to_stores`;\n";
			$sql .= "DELETE FROM `" . DB_PREFIX . "url_aliases` WHERE query LIKE 'category_id=%';\n";
			$sql .= "DELETE FROM `" . DB_PREFIX . "resource_map` WHERE object_name = 'categories';\n";
			//products
			$sql .= "DELETE FROM `" . DB_PREFIX . "products`;\n";
			$sql .= "DELETE FROM `" . DB_PREFIX . "product_descriptions` WHERE language_id='".$languageId."';\n";
			$sql .= "DELETE FROM `" . DB_PREFIX . "products_to_categories`;\n";
			$sql .= "DELETE FROM `" . DB_PREFIX . "products_to_stores`;\n";
			$sql .= "DELETE FROM `" . DB_PREFIX . "manufacturers`;\n";
			$sql .= "DELETE FROM `" . DB_PREFIX . "manufacturers_to_stores`;\n";
			$sql .= "DELETE FROM `" . DB_PREFIX . "url_aliases` WHERE `query` LIKE 'product_id=%';\n";
			$sql .= "DELETE FROM `" . DB_PREFIX . "products_related`;\n";
			$sql .= "DELETE FROM `" . DB_PREFIX . "resource_map` WHERE object_name = 'products';\n";
		}

		if ($this->session->data[ 'migration' ][ 'migrate_customers' ]) {
			$sql .= "DELETE FROM `" . DB_PREFIX . "customers`;\n";
			$sql .= "DELETE FROM `" . DB_PREFIX . "addresses`;\n";
		}

		if ($this->session->data[ 'migration' ][ 'migrate_orders' ]) {
			$sql .= "DELETE FROM `" . DB_PREFIX . "orders`;\n";
			$sql .= "DELETE FROM `" . DB_PREFIX . "order_downloads`;\n";
			$sql .= "DELETE FROM `" . DB_PREFIX . "order_history`;\n";
			$sql .= "DELETE FROM `" . DB_PREFIX . "order_options`;\n";
			$sql .= "DELETE FROM `" . DB_PREFIX . "order_products`;\n";
			$sql .= "DELETE FROM `" . DB_PREFIX . "order_statuses`;\n";
			$sql .= "DELETE FROM `" . DB_PREFIX . "order_totals`;\n";
		}

		$this->import($sql);
		// final commit
		$this->db->query("COMMIT;");
		$this->clearCache();
		$this->addLog('Existing data erased','success');
		return TRUE;
	}

	function clearCache() {
		$this->cache->delete('category');
		$this->cache->delete('category_description');
		$this->cache->delete('manufacturer');
		$this->cache->delete('product');
		$this->cache->delete('product_image');
		$this->cache->delete('product_option');
		$this->cache->delete('product_option_description');
		$this->cache->delete('product_option_value');
		$this->cache->delete('product_option_value_description');
		$this->cache->delete('product_to_category');
		$this->cache->delete('url_alias');
		$this->cache->delete('product_special');
		$this->cache->delete('product_discount');
	}

	protected function getDefaultLanguageId() {
		if (isset($this->language_id)) return $this->language_id;

		$code = $this->config->get('admin_language');
		$sql = "SELECT language_id
				FROM `" . DB_PREFIX . "languages`
				WHERE code = '".$code."'";
		$result = $this->db->query($sql,true);
		$languageId = 1;
		if ($result->rows) {
			foreach ($result->rows as $row) {
				$languageId = $row[ 'language_id' ];
				break;
			}
		}
		$this->language_id = $languageId;
		return $this->language_id;
	}


	protected function migrateProducts() {
		$this->load->model('tool/image');
		$rm = new AResourceManager();
		$rm->setType('image');

		$language_id = $this->getDefaultLanguageId();
		$store_id = $this->config->get('config_store_id');
		$category_id_map = array();
		$manufacturers_id_map = array();

		$language_list = $this->language->getAvailableLanguages();
		//////////////////////////////////////////////////////

		$products = $this->cart->getProducts();
		if (!$products) {
			$errors = $this->cart->getErrors();
			$class = 'error';
			if(!$errors){
				$errors =  $this->language->get('text_no_products');
				$class = 'attention';
			}
			$this->addLog($errors,$class);
			return false;
		}
		$categories = $this->cart->getCategories();
		if (!$categories) {
			$errors = $this->cart->getErrors();
			$class = 'error';
			if(!$errors){
				$errors =  $this->language->get('text_no_categories');
				$class = 'attention';
			}
			$this->addLog($errors,$class);
			return false;
		}
		$manufacturers = $this->cart->getManufacturers();
		if (!$manufacturers) {
			$errors = $this->cart->getErrors();
			$class = 'error';
			if(!$errors){
				$errors =  $this->language->get('text_no_brands');
				$class = 'attention';
			}
			$this->addLog($errors,$class);
			return false;
		}

		// import categories
		//   $categories = $this->cart->getCategories();
		$pics = 0;
		foreach ($categories as $data) {
			$data[ 'name' ] = strip_tags($data[ 'name' ]);
			$result = $this->db->query("INSERT INTO " . DB_PREFIX . "categories
                                        SET parent_id = '" . (int)$data[ 'parent_id' ] . "',
                                            sort_order = '" . (int)$data[ 'sort_order' ] . "',
                                            status = '1',
                                            date_modified = NOW(),
                                            date_added = NOW()",
			                           true);
			if($result===false){
				$this->addLog($this->db->error);
				return;
			}
			$category_id = $this->db->getLastId();
			$category_id_map[ $data[ 'category_id' ] ] = $category_id;

			if (!empty($data[ 'image' ])) {
				$source = $this->session->data[ 'migration' ][ 'cart_url' ] . 'image/' . $data[ 'image' ];
				$src_exists = @getimagesize($source);
				if(!$src_exists){
					$source = $this->session->data[ 'migration' ][ 'cart_url' ] . 'images/' . $data[ 'image' ];
				    $src_exists = @getimagesize($source);
				}
				if ( $src_exists ) {
					$data['image'] = 'data/'. pathinfo($data['image'], PATHINFO_BASENAME);
					$target = DIR_RESOURCE.'image/'. pathinfo($data['image'], PATHINFO_BASENAME);
					if (($file = $this->downloadFile($source)) === false) {
						$this->is_error = true;
						$this->addLog(" Category {$data['name']} File  " . $source . " couldn't be uploaded.");
					}

					if (!$this->is_error){
						if(!file_exists(DIR_RESOURCE.'image/')){
							mkdir(DIR_RESOURCE.'image/',0777);
						}
						if (!$this->writeToFile($file, $target)) {
							$this->is_error = true;
							$this->addLog("Cannot create Category {$data['name']} ({$source})  file " . $target . " in resource/image folder ");
						}else{
							// increase picture counter
							$pics++;
						}
						$resource = array( 'language_id' => $this->config->get('storefront_language_id'),
										   'name' => array(),
										   'title' => '',
										   'description' => '',
										   'resource_path' => pathinfo($data['image'], PATHINFO_BASENAME),
										   'resource_code' => '');
						$filename = pathinfo($data['image'], PATHINFO_BASENAME);
						foreach($language_list as $lang){
							$resource['name'][$lang['language_id']] = $filename;
						}
						$resource_id = $rm->addResource($resource);
						if ( $resource_id ) {
							$rm->mapResource('categories', $category_id, $resource_id);
						} else {
							$this->addLog($this->db->error);
							return;
						}
					}
				}
			}
			$result = $this->db->query( "INSERT INTO " . DB_PREFIX . "category_descriptions
					 				     SET category_id = '" . (int)$category_id . "',
										     language_id = '" . (int)$language_id . "',
										     name = '" . $this->db->escape($data[ 'name' ]) . "',
										     description = '" . $this->db->escape($data[ 'description' ]) . "'",
			                            true);
			if($result===false){
				$this->addLog($this->db->error);
				return;
			}

			$result = $this->db->query( "INSERT INTO " . DB_PREFIX . "categories_to_stores
										   (category_id,store_id)
										 VALUES ('" . (int)$category_id . "','" . (int)$store_id . "')", true);
			if($result === false){
				$this->addLog($this->db->error);
				return;
			}
		}

		//update parent id according to new map
		$query = $this->db->query("SELECT category_id, parent_id FROM " . DB_PREFIX . "categories ");
		foreach ($query->rows as $result) {
			if (empty($category_id_map[ $result[ 'parent_id' ] ])) continue;

				$result = $this->db->query("UPDATE " . DB_PREFIX . "categories
										    SET parent_id = '" . $category_id_map[ $result[ 'parent_id' ] ] . "'
										    WHERE category_id = '" . (int)$result[ 'category_id' ] . "'", true);
				if($result === false){
					$this->addLog($this->db->error);
					return;
				}
		}
		$this->addLog(count($categories) . ' categories imported ('. $pics . ' pictures)','success');

		//////////////////////////////////////////////////////
		// import manufacturers
		$pics = 0;
		foreach ($manufacturers as $data) {

			$result = $this->db->query("INSERT INTO " . DB_PREFIX . "manufacturers
                                        SET name = '" . $this->db->escape($data[ 'name' ]) . "'", true);
			if($result === false){
				$this->addLog($this->db->error);
				return;
			}

			$manufacturer_id = $this->db->getLastId();
			$manufacturers_id_map[ $data[ 'manufacturers_id' ] ] = $manufacturer_id;

			if (!empty($data[ 'image' ])) {
				$source = $this->session->data[ 'migration' ][ 'cart_url' ] . 'image/' . $data[ 'image' ];
				$src_exists = @getimagesize($source);
				if(!$src_exists){
					$source = $this->session->data[ 'migration' ][ 'cart_url' ] . 'images/' . $data[ 'image' ];
				    $src_exists = @getimagesize($source);
				}
				if ( $src_exists ) {
					$data['image'] = 'data/'. pathinfo($data['image'], PATHINFO_BASENAME);
					$target = DIR_RESOURCE.'image/'. pathinfo($data['image'], PATHINFO_BASENAME);
					if (($file = $this->downloadFile($source)) === false) {
						$this->is_error = true;
						$this->addLog(" Brand {$data['name']} File " . $source . " couldn't be uploaded.");
					}

					if (!$this->is_error){
						if(!file_exists(DIR_RESOURCE.'image/')){
							mkdir(DIR_RESOURCE.'image/',0777);
						}

						if (!$this->writeToFile($file, $target)) {
							$this->is_error = true;
							$this->addLog("Couldn't create Manufacturer {$data['name']} ({$source}) file " . $target . " in image folder ");
						}else{
							$pics++;
						}
					}

					if (!$this->is_error){
						$resource = array( 'language_id' => $this->config->get('storefront_language_id'),
										   'name' => array(),
										   'title' => '',
										   'description' => '',
										   'resource_path' => pathinfo($data['image'], PATHINFO_BASENAME),
										   'resource_code' => '');
						$filename = pathinfo($data['image'], PATHINFO_BASENAME);
						foreach($language_list as $lang){
							$resource['name'][$lang['language_id']] = $filename;
						}
						$resource_id = $rm->addResource($resource);
						if ( $resource_id ) {
							$rm->mapResource('manufacturers', $manufacturer_id, $resource_id);
						} else {
							$this->addLog($this->db->error);
							return;
						}
					}
				}
			}
			$result = $this->db->query("INSERT INTO " . DB_PREFIX . "manufacturers_to_stores
                                        SET manufacturer_id = '" . (int)$manufacturer_id . "', store_id = '" . (int)$store_id . "'", true);
			if($result === false){
				$this->addLog($this->db->error);
				return;
			}
		}

		$this->addLog(count($manufacturers) . ' brands imported ('. $pics . ' pictures)','success');

		//////////////////////////////////////////////////////
		// import products

		$pics = 0;
		foreach ($products as $data) {

			$data[ 'manufacturer_id' ] = empty($manufacturers_id_map[ $data[ 'manufacturer_id' ] ]) ? '' : $manufacturers_id_map[ $data[ 'manufacturer_id' ] ];

			$result = $this->db->query("INSERT INTO " . DB_PREFIX . "products
										SET model = '" . $this->db->escape($data[ 'model' ]) . "',
											quantity = '" . (int)$data[ 'quantity' ] . "',
											stock_status_id = '" . (int)$data[ 'stock_status_id' ] . "',
											date_available = '" . $this->db->escape($data[ 'date_available' ]) . "',
											manufacturer_id = '" . (int)$data[ 'manufacturer_id' ] . "',
											shipping = '" . (int)$data[ 'shipping' ] . "',
											price = '" . (float)$data[ 'price' ] . "',
											weight = '" . (float)$data[ 'weight' ] . "',
											weight_class_id = '" . (int)$data[ 'weight_class_id' ] . "',
											status = '" . (int)$data[ 'status' ] . "',
											tax_class_id = '" . (int)$data[ 'tax_class_id' ] . "',
											date_added = NOW()",
			                           true);

			if($result === false){
				$this->addLog($this->db->error);
				return;
			}

			$product_id = $this->db->getLastId();

			if (!empty($data[ 'image' ])) {

				//TODO: create directories automatically
				//for now remove directories from image path
				$source = $this->session->data[ 'migration' ][ 'cart_url' ] . 'image/' . $data[ 'image' ];
				$src_exists = @getimagesize($source);
				if(!$src_exists){
					$source = $this->session->data[ 'migration' ][ 'cart_url' ] . 'images/' . $data[ 'image' ];
				    $src_exists = @getimagesize($source);
				}
				if ( $src_exists ) {
					$data['image'] = 'data/'. pathinfo($data['image'], PATHINFO_BASENAME);
					$target = DIR_RESOURCE.'image/'. pathinfo($data['image'], PATHINFO_BASENAME);
					if (($file = $this->downloadFile($source)) === false) {
						$this->is_error = true;
						$this->addLog(" Product {$data['name']} File " . $source . " couldn't be uploaded.");
					}

					if (!$this->is_error){
						if (!$this->writeToFile($file, $target)) {
							$this->is_error = true;
							$this->addLog("Couln't create product {$data['name']} ({$source}) file " . $target . " in image folder ");
						}else{
							$pics++;
						}
					}
					if (!$this->is_error){
						$resource = array( 'language_id' => $this->config->get('storefront_language_id'),
										   'name' => array(),
										   'title' => '',
										   'description' => '',
										   'resource_path' => pathinfo($data['image'], PATHINFO_BASENAME),
										   'resource_code' => '');

						$filename = pathinfo($data['image'], PATHINFO_BASENAME);
						foreach($language_list as $lang){
							$resource['name'][$lang['language_id']] = $filename;
						}

						$resource_id = $rm->addResource($resource);
						
						if ( $resource_id ) {

							$rm->mapResource('products', $product_id, $resource_id);
						} else {
							$this->addLog($this->db->error);
							return;
						}
					}

				} else {
					$this->addLog(" Product {$data['name']} File " . $source . " couldn't be accessed.");
				}
			}

			$result = $this->db->query ( "INSERT INTO " . DB_PREFIX . "product_descriptions
										  SET product_id = '" . (int)$product_id . "',
										  	  language_id = '" . (int)$language_id . "',
											  name = '" . $this->db->escape($data[ 'name' ]) . "',
											  description = '" . $this->db->escape($data[ 'description' ]) . "'", true);
			if($result === false){
				$this->addLog($this->db->error);
				return;
			}

			$result = $this->db->query ( "INSERT INTO " . DB_PREFIX . "products_to_stores
			                              SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'", true);
			if($result === false){
				$this->addLog($this->db->error);
				return;
			}

			if (isset($data[ 'product_category' ])) {
				foreach ($data[ 'product_category' ] as $category_id) {
					if(!(int)$category_id_map[ $category_id ]) continue;

					$result = $this->db->query("INSERT INTO " . DB_PREFIX . "products_to_categories
                                                    (product_id,category_id)
                                                VALUES ('" . (int)$product_id . "', '" . (int)$category_id_map[ $category_id ] . "')", true);
					if($result === false){
						$this->addLog($this->db->error);
						return;
					}
				}
			}

		}

		$this->addLog(count($products) . ' products imported ('. $pics . ' pictures)','success');
		return true;

	}

	protected function migrateCustomers() {
		$customers = $this->cart->getCustomers();
		if (!$customers) {
			$errors = $this->cart->getErrors();
			$class = '';
			if(!$errors){
				$errors =  $this->language->get('text_no_customers');
				$class = 'attention';
			}
			$this->addLog($errors,$class);
			return false;
		}

		foreach ($customers as $data) {
			$result = $this->db->query ( "INSERT INTO " . DB_PREFIX . "customers
										SET store_id = '" . (int)$this->config->get('config_store_id') . "',
											firstname = '" . $this->db->escape($data[ 'firstname' ]) . "',
											lastname = '" . $this->db->escape($data[ 'lastname' ]) . "',
											email = '" . $this->db->escape($data[ 'email' ]) . "',
											telephone = '" . $this->db->escape($data[ 'telephone' ]) . "',
											fax = '" . $this->db->escape($data[ 'fax' ]) . "',
											password = '" . $this->db->escape(AEncryption::getHash($data[ 'password' ])) . "',
											newsletter = '" . $this->db->escape($data[ 'newsletter' ]) . "',
											customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "',
											status = '1',
											approved = '1',
											date_added = NOW()",
									   true);
			if($result === false){
				$this->addLog($this->db->error);
				return;
			}

			$customer_id = $this->db->getLastId();

			foreach ($data[ 'address' ] as $address) {
				$result = $this->db->query ( "INSERT INTO " . DB_PREFIX . "addresses
											  SET customer_id = '" . (int)$customer_id . "',
											   	  firstname = '" . $this->db->escape($address[ 'firstname' ]) . "',
													lastname = '" . $this->db->escape($address[ 'lastname' ]) . "',
													company = '" . $this->db->escape($address[ 'company' ]) . "',
													address_1 = '" . $this->db->escape($address[ 'address_1' ]) . "',
													city = '" . $this->db->escape($address[ 'city' ]) . "',
													postcode = '" . $this->db->escape($address[ 'postcode' ]) . "',
													country_id = '" . (int)$address[ 'country_id' ] . "',
													zone_id = '" . (int)$address[ 'zone_id' ] . "'", true);
				if($result === false){
					$this->addLog($this->db->error);
					return;
				}
				$address_id = $this->db->getLastId();
			}

			$result = $this->db->query("UPDATE " . DB_PREFIX . "customers
									    SET address_id = '" . (int)$address_id . "'
								        WHERE customer_id = '" . (int)$customer_id . "'",
			                           true);
			if($result === false){
				$this->addLog($this->db->error);
				return;
			}

		}
		$this->addLog(count($customers) . ' customers imported','success');
		return true;
	}

	protected function migrateOrders() {
		$orders = $this->cart->getOrders();
		if (!$orders) {
			$errors = $this->cart->getErrors();
			$class = '';
			if(!$errors){
				$errors =  $this->language->get('text_no_orders');
				$class = 'attention';
			}
			$this->addLog($errors,$class);
			$this->addLog($errors);
			return false;
		}
		return true;
	}

	private function _get($uri) {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $uri);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$response = new stdClass();

		$response->body = curl_exec($ch);
		$response->http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response->content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
		$response->content_length = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

		curl_close($ch);

		return $response;
	}

	private function downloadFile($path) {
		$file = $this->_get($path);
		if ($file->http_code == 200) {
			return $file;
		}
		return false;
	}

	function writeToFile($data, $file) {
		if (is_dir($file)) return;
		if (function_exists("file_put_contents")) {
			$bytes = @file_put_contents($file, $data->body);
			return $bytes == $data->content_length;
		}

		$handle = @fopen($file, 'w+');
		$bytes = fwrite($handle, $data->body);
		@fclose($handle);

		return $bytes == $data->content_length;
	}
}

?>