<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2017 Belavier Commerce LLC

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

/**
 * Class ModelToolImportProcess
 * @property ModelToolImportProcess $model_tool_import_process
 */
class ModelToolImportProcess extends Model{
	public $errors = array ();
	private $eta = array ();
    private $imp_log = null;

	/**
	 * @param string $task_name
	 * @param array $data
	 * @return array|bool
	 */
	public function createTask($task_name, $data = array()) {

		if (!$task_name) {
			$this->errors[] = 'Can not to create task. Empty task name has been given.';
		}

        if (!$data['file'] && !$data['products_fields']) {
            $this->errors[] = 'Missing required data to build a task.';
        }
        //get file details
        $total_rows_count = 0;
        $handle = fopen($data['file'] , "r");
        if(is_resource($handle)) {
            while (!feof($handle)) {
                $line = fgets($handle);
                $total_rows_count++;
            }
            unset($line);
            fclose($handle);
        } else {
            $this->errors[] = 'No import feed file available!';
            return false;
        }

		//task controller processing task steps
		$task_controller = 'task/tool/import_process/processRows';

		//numbers of rows per task step
		$divider = 20;
		//timeout in seconds for one row
		$time_per_send = 4;

		$tm = new ATaskManager();
		//create new task
		$task_id = $tm->addTask(
				array (
						'name'               => $task_name,
						'starter'            => 1, //admin-side is starter
						'created_by'         => $this->user->getId(), //get starter id
						'status'             => $tm::STATUS_READY,
						'start_time'         => date('Y-m-d H:i:s',	mktime(0, 0, 0, date('m'), date('d') + 1, date('Y'))),
						'last_time_run'      => '0000-00-00 00:00:00',
						'progress'           => '0',
						'last_result'        => '1',
						'run_interval'       => '0',
						'max_execution_time' => ($total_rows_count * $time_per_send * 2)
				)
		);
		if (!$task_id) {
			$this->errors = array_merge($this->errors, $tm->errors);
			return false;
		}

		$tm->updateTaskDetails($task_id,
				array (
						'created_by' => $this->user->getId(),
						'settings'   => array (
                            'import_data'   => $data,
                            'total_rows_count' => $total_rows_count,
                            'processed'        => 0
						)
				)
		);

        $sort_order =1;
        $k = 0;
        while ($k < $total_rows_count){
            //create task step
            $step_id = $tm->addStep(array (
                'task_id'            => $task_id,
                'sort_order'         => $sort_order,
                'status'             => 1,
                'last_time_run'      => '0000-00-00 00:00:00',
                'last_result'        => '0',
                'max_execution_time' => ($divider * 2),
                'controller'         => $task_controller,
                'settings'           => array (
                    'start'     => $k,
                    'stop'      => $k + $divider
                )
            ));

            if (!$step_id) {
                $this->errors = array_merge($this->errors, $tm->errors);
                return false;
            } else {
                // get eta in seconds
                $this->eta[$step_id] = $divider * 2;
            }

            $sort_order++;
            $k += $divider;
        }

		$task_details = $tm->getTaskById($task_id);

		if ($task_details) {
			foreach ($this->eta as $step_id => $eta) {
				$task_details['steps'][$step_id]['eta'] = $eta;
				//remove settings from output json array. We will take it from database on execution.
				$task_details['steps'][$step_id]['settings'] = array ();
			}
			return $task_details;
		} else {
			$this->errors[] = 'Can not to get task details for execution';
			$this->errors = array_merge($this->errors, $tm->errors);
			return false;
		}
	}

    public function process_products_record($task_id, $data, $settings){
        $language_id = $this->session->data['content_language_id'];
        $store_id = $this->session->data['current_store_id'];

        $this->load->model('catalog/product');

        $this->imp_log = new ALog(DIR_LOGS . "products_import_{$task_id}.txt");

        return $this->addUpdateProduct($data, $settings, $language_id, $store_id);
    }

    public function process_categories_record($task_id, $data, $settings){
        $language_id = $this->session->data['content_language_id'];
        $store_id = $this->session->data['current_store_id'];

        $this->load->model('catalog/category');

        $this->imp_log = new ALog(DIR_LOGS . "categories_import_{$task_id}.txt");

        return $this->addUpdateCategory($data, $settings, $language_id, $store_id);
    }

    public function process_manufacturers_record($task_id, $data, $settings){
        $language_id = $this->session->data['content_language_id'];
        $store_id = $this->session->data['current_store_id'];

        $this->load->model('catalog/manufacturer');

        $this->imp_log = new ALog(DIR_LOGS . "manufacturers_import_{$task_id}.txt");

        return $this->addUpdateManufacture($data, $settings, $language_id, $store_id);
    }


    protected function addUpdateProduct($record, $settings, $language_id, $store_id){
        $status = false;
        $record = array_map('trim', $record);

        //data mapping
        $data = $this->buildDataMap($record, $settings['import_col'], $settings['products_fields'], $settings['split_col']);
        if(empty($data)){
            return $this->toLog("Error: Unable to build products import data map.");
        }
        $product = $this->_filter_array($data['products']);
        $product_desc = $this->_filter_array($data['product_descriptions']);
        $manuf = $this->_filter_array($data['manufacturers']);

        //check if row is complete and uniform
        if (!$product_desc['name'] && !$product['sku'] && !$product['model']) {
            return $this->toLog('Error: Record is not complete or missing required data. Skipping!');
        }

        $this->toLog("Processing record for product {$product_desc['name']} {$product['sku']} {$product['model']}.");

        //detect if we update or create new product based on update settings
        $new_product = true;
        $product_id = 0;
        if($settings['update_col']){
            $unique_field_index = key($settings['update_col']);
            if ($unique_field_index) {
                $unique_field = $settings['products_fields'][$unique_field_index];
                $lookup_value = $this->getValuefromDataMap($unique_field, $record, $settings['products_fields'], $settings['import_col']);
                $product_id = $this->getProductByField($unique_field, $lookup_value, $language_id, $store_id);
                if ($product_id) {
                    //we have product, update
                    $new_product = false;
                }
            }
        }

        // import category if needed
        $categories = array();
        if ($data['categories'] && $data['categories']['category']) {
            $categories = $this->_process_categories($data['categories'], $language_id, $store_id);
        }

        // import brand if needed
        $manufacturer_id = 0;
        if ($manuf['manufacturer']) {
            $manufacturer_id = $this->_process_manufacturer($manuf['manufacturer'], 0, $store_id);
        }

        // import or update product
        $product_data = array_merge(
            $product,
            array (
            'manufacturer_id'     => $manufacturer_id,
            'product_description' => array (
                    $language_id => $product_desc
                ),
            )
        );

        $this->load->model('catalog/product');
        if ($new_product) {
            //apply default settings for new products only
            $default_arr = array(
                'status' => 1,
                'subtract' => 1,
                'free_shipping' => 0,
                'shipping' => 1,
                'call_to_order' => 0,
                'sort_order' => 0,
                'weight_class_id' => 6,
                'length_class_id' => 3,
            );
            foreach ($default_arr as $key => $val ) {
                $product_data[$key] = isset($product_data[$key]) ? $product_data[$key] : $val;
            }

            $product_id = $this->model_catalog_product->addProduct($product_data);
            if ($product_id) {
                $this->toLog("Created product '{$product_desc['name']}' with ID {$product_id}.");
                $status = true;
            } else {
                $this->toLog("Error: Failed to create product '{$product_desc['name']}'.");
            }

        } else {
            $this->model_catalog_product->updateProduct($product_id, $product_data);
            $this->toLog("Updated product '{$product_desc['name']}' with ID {$product_id}.");
            $status = true;
        }

        if (count($categories)) {
            $this->model_catalog_product->updateProductLinks($product_id, array (
                'product_store'    => array ($store_id),
                'product_category' => array_column($categories, 'category_id')
            ));
        }

        //process images
        $this->_migrateImages($data['images'], 'products', $product_id, $language_id);
        //process options
        $this->_addUpdateOptions($product_id, $data['product_options'], $language_id, $store_id);
        //process SEO URL

        return $status;
    }

    protected function addUpdateCategory($record, $settings, $language_id, $store_id) {
        $this->load->model('catalog/category');
        $status = false;
        $record = array_map('trim', $record);
        //data mapping
        $data = $this->buildDataMap($record, $settings['import_col'], $settings['categories_fields'], $settings['split_col']);
        if(empty($data)){
            return $this->toLog("Error: Unable to build categories import data map.");
        }

        $category = $this->_filter_array($data['categories']);
        //check if we have split tree or an array
        $categ_desc = $this->_filter_array($data['category_descriptions']);
        $categ_tree = $categ_desc['name'];
        if(count($data['category_descriptions']['name']) > 1) {
            $categ_tree = $data['category_descriptions']['name'];
        }
        //Get actual category name
        $categ_desc['name'] = end($categ_tree);

        $stree = implode(' -> ', $categ_tree);
        $this->toLog("Processing record for category { $stree } .");
        //process all categories
        $categories = $this->_process_categories(array('category' => array($categ_tree)), $language_id, $store_id);
        //we will have always one category
        $category_id = $categories[0]['category_id'];
        $pcategory_id = $categories[0]['parent_id'];

        if ($category_id) {
            //update category
            $this->model_catalog_category->editCategory(
                $category_id,
                array_merge(
                    $category,
                    array('parent_id'   => $pcategory_id),
                    array('category_description' => array($language_id => $categ_desc)),
                    array('category_store'  => array ($store_id))
                )
            );
            $this->toLog("Updated category '{$categ_desc['name']}' with ID {$category_id}.");
            $status = true;
        } else {
            $default_arr = array(
                'status' => 1,
                'sort_order' => 0,
            );
            foreach ($default_arr as $key => $val ) {
                $category[$key] = isset($category[$key]) ? $category[$key] : $val;
            }

            $category_id = $this->model_catalog_category->addCategory(
                array_merge(
                    $category,
                    array('parent_id'   => $pcategory_id),
                    array('category_description' => array($language_id => $categ_desc)),
                    array('category_store'  => array ($store_id))
                )
            );
            if ($category_id) {
                $this->toLog("Created category '{$categ_desc['name']}' with ID {$category_id}.");
                $status = true;
            } else {
                $this->toLog("Error: Failed to create category '{$categ_desc['name']}'.");
                return false;
            }
        }

        //process images
        $this->_migrateImages($data['images'], 'categories', $category_id, $language_id);

        return $status;
    }

    protected function addUpdateManufacture($record, $settings, $language_id, $store_id) {
        $this->load->model('catalog/category');
        $status = false;
        $record = array_map('trim', $record);
        //data mapping
        $data = $this->buildDataMap($record, $settings['import_col'], $settings['manufacturers_fields'], $settings['split_col']);
        if(empty($data)){
            return $this->toLog("Error: Unable to build manufacturers import data map.");
        }

        $manufacturer = $this->_filter_array($data['manufacturers']);

        echo_array($settings);
        echo_array($manufacturer);

        $manufacturer_id = $this->_process_manufacturer($manufacturer['name'], $manufacturer['sort_order'], $store_id);
        if ($manufacturer_id) {
            $status = true;
            //process images
            $this->_migrateImages($data['images'], 'manufacturers', $manufacturer_id, $language_id);
        }

        return $status;
    }

    private function _addUpdateOptions($product_id, $data = array (), $language_id, $store_id){
        if (!is_array($data) || empty($data)) {
            //no option details
            return;
        }

        $this->toLog("Creating product option for product ID {$product_id}.");
        //get existing options and values.
        $this->load->model('catalog/product');
        $options = $this->model_catalog_product->getProductOptions($product_id);
        if ($options) {
            //delete all options if exists
            foreach ($options as $option) {
                $this->model_catalog_product->deleteProductOption($product_id, $option['product_option_id']);
            }
        }

        //add new options for each option
        for ($i = 0; $i < count($data); $i++) {
            //create new option
            if(!$data[$i]['name'] && !is_array($data['product_option_values'])) {
                $this->toLog("Error: Missing option name or values for product ID '{$product_id}'.");
                continue;
            }

            $opt_data = array (
                'option_name'        => $data[$i]['name'],
                'element_type'       => 'S',
                'regexp_pattern'     => "",
                'error_text'         => '',
                'option_placeholder' => '',
            );
            $opt_data['required'] = $data[$i]['required'] ? $data[$i]['required'] : 0;
            $opt_data['sort_order'] = $data[$i]['sort_order'] ? $data[$i]['sort_order'] : 0;
            $opt_data['status'] = $data[$i]['status'] ? $data[$i]['status'] : 1;

            $p_option_id = $this->model_catalog_product->addProductOption($product_id, $opt_data);
            if ($p_option_id) {
                $this->toLog("Created product option '{$data[$i]['name']}' with ID {$p_option_id}.");
            } else {
                $this->toLog("Error: Failed to create product option '{$data[$i]['name']}'.");
                return;
            }

            //now load values. Pick longest data array
            $option_vals = $data[$i]['product_option_values'];
            $counts = array_map('count', $option_vals);
            $key = array_flip($counts)[max($counts)];
            for ($j = 0; $j < count($option_vals[$key]); $j++) {
                //add options value
                $opt_val_data = array();
                $opt_keys = array(  'name' => '',
                                    'sku' => '',
                                    'quantity' => 0,
                                    'sort_order' => 0,
                                    'subtract' => 0,
                                    'prefix' => '$',
                                    'weight' => 0,
                                    'weight_type' => 'lbs',
                                    'default' => 0,
                                    'price' => 0
                );
                foreach ($opt_keys as $k => $v) {
                    $opt_val_data[$k] = $v;
                    if(isset($option_vals[$key][$j])){
                        $opt_val_data[$k] = $option_vals[$key][$j];
                    }
                }

                $this->model_catalog_product->addProductOptionValueAndDescription($product_id, $p_option_id, $opt_val_data);
            }
        }

        return true;
    }

    //add from URL download
    private function _migrateImages($data = array (), $object_txt_id = '', $object_id = 0, $language_id){
        $objects = array (
            'products'      => 'Product',
            'categories'    => 'Category',
            'manufacturers' => 'Brand'
        );

        if (!in_array($object_txt_id, array_keys($objects)) || !$data || !is_array($data)) {
            $this->toLog("Error: Missing images data array for object {$object_txt_id}.");
            return false;
        }

        $language_list = $this->language->getAvailableLanguages();

        $rm = new AResourceManager();
        $rm->setType('image');

        //IMAGE PROCESSING
        $data['image'] = (array)$data['image'];
        foreach ($data['image'] as $source) {

            if (empty($source)) {
                continue;
            } else if (is_array($source)) {
                //we have an array from list of values. Run again
                $this->_migrateImages(array ('image' => $source), $object_txt_id, $object_id, $language_id);
                continue;
            }
            // check is image exists
            $src_exists = @getimagesize($source);
            if ($src_exists) {
                $image_basename = trim(basename($source));
                $target = DIR_RESOURCE . 'image/' . $image_basename;

                if (($file = $this->downloadFile($source)) === false) {
                    $this->toLog("Error: Image " . $source . " cannot be downloaded.");
                    continue;
                }

                if (!is_dir(DIR_RESOURCE . 'image/')) {
                    mkdir(DIR_RESOURCE . 'image/', 0777);
                }
                if (!$this->writeToFile($file, $target)) {
                    $this->toLog("Error: Cannot create " . $objects[$object_txt_id] . " " . $data['title'] . " ( " . $source . " )  file " . $target . " in resource/image folder ");
                    continue;
                }
                $resource = array (
                    'language_id'   => $language_id,
                    'name'          => $image_basename,
                    'title'         => '',
                    'description'   => '',
                    'resource_path' => $image_basename,
                    'resource_code' => ''
                );
                foreach ($language_list as $lang) {
                    $resource['name'][$lang['language_id']] = $data['title'];
                }
                $resource_id = $rm->addResource($resource);
                if ($resource_id) {
                    $rm->mapResource($object_txt_id, $object_id, $resource_id);
                } else {
                    $this->toLog("Error: Image resource can not be created. " . $this->db->error);
                    continue;
                }
            } else {
                $this->toLog("Error: Image {$source} does not exists for {$object_txt_id} with ID {$object_id}. ");
            }
        }

        return true;
    }

    private function _get($uri){
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

    private function downloadFile($path){
        $file = $this->_get($path);
        if ($file->http_code == 200) {
            return $file;
        }
        return false;

    }

    private function writeToFile($data, $file){
        if (is_dir($file)) {
            return null;
        }
        if (function_exists("file_put_contents")) {
            $bytes = @file_put_contents($file, $data->body);
            return $bytes == $data->content_length;
        }

        $handle = @fopen($file, 'w+');
        $bytes = fwrite($handle, $data->body);
        @fclose($handle);

        return $bytes == $data->content_length;
    }

    public  function getProductByField($field, $value, $language_id, $store_id) {
        if($field == 'products.sku') {
            return $this->getProductIDBySku($value, $store_id);
        } else if($field == 'products.model') {
            return $this->getProductIDByModel($value, $store_id);
        } else if($field == 'product_descriptions.name') {
            return $this->getProductIDByName($value, $language_id, $store_id);
        }
        return null;
    }

    public function getProductIDByName($name, $language_id, $store_id) {
        if ($name) {
            $query = $this->db->query(
                "SELECT p.product_id as product_id
				FROM " . $this->db->table("products") . " p
				LEFT JOIN " . $this->db->table("product_descriptions") . " pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$language_id . "')
                LEFT JOIN " . $this->db->table("products_to_stores") . " p2s ON (p.product_id = p2s.product_id)
				WHERE LCASE(pd.name) = '" . $this->db->escape(mb_strtolower($name)) . "' AND p2s.store_id = '" . (int)$store_id . "' limit 1");
            return $query->row['product_id'];
        } else {
            return null;
        }
    }

    public function getProductIDByModel($model, $store_id) {
        if ($model) {
            $query = $this->db->query(
                "SELECT p.product_id as product_id
				     FROM " . $this->db->table("products") . " p
                LEFT JOIN " . $this->db->table("products_to_stores") . " p2s ON (p.product_id = p2s.product_id)
				WHERE LCASE(p.model) = '" . $this->db->escape(mb_strtolower($model)) . "' AND p2s.store_id = '" . (int)$store_id . "' limit 1");
            return $query->row['product_id'];
        } else {
            return null;
        }
    }

    public function getProductIDBySku($sku, $store_id) {
        if ($sku) {
            $query = $this->db->query(
                "SELECT p.product_id as product_id
				FROM " . $this->db->table("products") . " p
                LEFT JOIN " . $this->db->table("products_to_stores") . " p2s ON (p.product_id = p2s.product_id)
				WHERE LCASE(p.sku) = '" . $this->db->escape(mb_strtolower($sku)) . "' AND p2s.store_id = " . (int)$store_id . " limit 1");
            return $query->row['product_id'];
        } else {
            return null;
        }
    }

    private function _process_manufacturer($mname, $sort_order, $store_id) {
        $manufacturer_id = null;
        $sql = $this->db->query("SELECT manufacturer_id from " . $this->db->table("manufacturers")
            . " WHERE LCASE(name) = '" . $this->db->escape(mb_strtolower($mname)) . "' limit 1");
        $manufacturer_id = $sql->row['manufacturer_id'];
        if (!$manufacturer_id) {
            //create category
            $this->load->model('catalog/manufacturer');
            $manufacturer_id = $this->model_catalog_manufacturer->addManufacturer(
                array (
                    'sort_order'         => $sort_order,
                    'name'               => $mname,
                    'manufacturer_store' => array ($store_id)
                )
            );
            if ($manufacturer_id) {
                $this->toLog("Created manufacturer '{$mname}' with ID {$manufacturer_id}.");
            } else {
                $this->toLog("Error: Failed to create manufacturer '{$mname}'.");
            }
        }
        return $manufacturer_id;
    }

    private function _process_categories($data, $language_id, $store_id) {
        if(!is_array($data['category'])) {
            return array();
        }
        $this->load->model('catalog/category');

        $ret = array();
        for ($i = 0; $i < count($data['category']); $i++) {
            //check if we have a tree in a form of array or just a category
            $categories = array();
            if (is_array($data['category'][$i])) {
                $categories = $data['category'][$i];
            } else {
                $categories[] = str_replace(',', '', $data['category'][$i]);
            }

            $last_parent_id = 0;
            foreach ($categories as $index => $c_name) {
                //is parent?
                $is_parent = ($index + 1 == count($categories)) ? false : true;
                //check if category exists with this name
                $cid = $this->_get_category($c_name, $language_id, $store_id, $last_parent_id);
                if ($is_parent) {
                    if (!$cid) {
                        $last_parent_id = $this->_save_category($c_name, $language_id, $store_id, $last_parent_id);
                        if (!$last_parent_id) {
                            break;
                        }
                    } else {
                        $last_parent_id = $cid;
                    }
                } else {
                    //last node, leave category
                    if (!$cid) {
                        $cid = $this->_save_category($c_name, $language_id, $store_id, $last_parent_id);
                    }
                    if ($cid) {
                        $ret[] = array('category_id' => $cid, 'parent_id' => $last_parent_id);
                    }
                    break;
                }
            }
        }
        return $ret;
    }

    private function _get_category($cname, $language_id, $store_id, $parent_id){
        $sql = "SELECT cd.category_id from " . $this->db->table("category_descriptions") . " cd
              INNER JOIN " . $this->db->table("categories_to_stores") . " c2s ON (cd.category_id = c2s.category_id) 
              WHERE language_id = " . (int)$language_id . " AND  c2s.store_id = " . (int)$store_id . "
                    AND LCASE(name) = '" . $this->db->escape(mb_strtolower($cname)) . "'";
        $res = $this->db->query($sql);
        if ($res->num_rows == 1) {
            return $res->row['category_id'];
        } else if($res->num_rows > 1) {
            //we have categories with same names, locate based on parent.
            $cids = array_column($res->rows, 'category_id');
            $sql2 = "SELECT category_id from " . $this->db->table("categories") . "
                WHERE category_id in(".implode(', ', $cids).") AND parent_id = $parent_id ORDER BY parent_id DESC ";
            $res2 = $this->db->query( $sql2 );
            return $res2->row['category_id'];
        }
    }

    private function _save_category($cname, $language_id, $store_id, $pid = 0) {
        $category_id = $this->model_catalog_category->addCategory(
            array (
                'parent_id'            => $pid,
                'sort_order'           => 0,
                'status'               => 1,
                'category_description' => array (
                    $language_id => array ('name' => $cname)
                ),
                'category_store'       => array ($store_id)
            )
        );
        if ($category_id) {
            $this->toLog("Created category '{$cname}' with ID {$category_id}.");
        } else {
            $this->toLog("Error: Failed to create category '{$cname}'.");
        }
        return $category_id;
    }

    /**
     * Map data from record based on the settings
     * @param array $record
     * @param array $settings
     * @return int
     */
    protected function buildDataMap($record, $import_col, $fields, $split_col){
        $ret = array();
        $op_index = -1;
        $op_array = array();
        if (!is_array($import_col) || !is_array($fields))  {
            return $ret;
        }

        foreach ($fields as $index => $field) {
            if(empty($field)){
                continue;
            }
            $arr = array();
            $field_val = $record[$import_col[$index]];
            $keys = array_reverse(explode('.', $field));
            if (end($keys) == 'product_options') {
                //map options special way
                //check if this is still same option or it is new name
                if (count($keys) == 2) {
                    if ($keys[0] == 'name') {
                        $op_array[++$op_index]['name'] = $field_val;
                    } else {
                        $tmpindex = ($op_index >= 0) ? $op_index : 0;
                        $op_array[$tmpindex][$keys[0]] = $field_val;
                    }
                } else {
                    for($i = 0; $i < count($keys)-1; $i++) {
                        if ($i == 0) {
                            $arr = array($keys[$i] => $field_val);
                        } else {
                            $arr = array($keys[$i] => $arr);
                        }
                    }
                    $tmpindex = ($op_index >= 0) ? $op_index : 0;
                    $op_array[$tmpindex] = array_merge_recursive($op_array[$tmpindex], $arr);
                }
            } else {
                foreach ($keys as $key) {
                    if ($key === reset($keys)) {
                        //leaf element
                        //check if we need to split the record data from list of values
                        if(isset($split_col) && !empty($split_col[$index])) {
                            $field_val = explode($split_col[$index], $field_val);
                        }
                        $arr[$key][] = $field_val;
                    } else {
                        $arr = array($key => $arr);
                    }
                }

                $ret = array_merge_recursive($ret, $arr);
            }
        }

        if ($op_array) {
            $ret = array_merge_recursive($ret, array('product_options' => $op_array));
        }
        return $ret;
    }

    private function _filter_array($arr = array()) {
        $ret = array();
        foreach ($arr as $key => $val) {
            //get only first element of data array
            $ret[$key] = reset($val);
        }
        return $ret;
    }

    /**
     * Get a value from the record based on the setting key
     * @param string $key
     * @param array $record
     * @param array $settings
     * @return int
     */
    protected function getValuefromDataMap($key, $record, $fields, $columns){
        $index = array_search($key, $fields);
        if($index !== false) {
            return $record[$columns[$index]];
        }
    }

    /**
     * @param $message
     * @return null
     */
    protected function toLog($message){
        if(!$message){
            return null;
        }
        $this->imp_log->write($message);
    }

    /**
     * Array wth configurations for import tables and fields
     * @param none
     * @return array()
     */
    public function importTableCols() {
        return array(
            'products' => array(
                'columns' => array(
                    'products.status' => array(
                        'title' => 'Status (1 or 0)',
                    ),
                    'products.sku' => array(
                        'title' => 'SKU (up to 64 chars)',
                        'update' => true,
                    ),
                    'products.model' => array(
                        'title' => 'Model (up to 64 chars)',
                        'update' => true,
                    ),
                    'product_descriptions.name' => array(
                        'title' => 'Name (up to 255 chars)',
                        'required' => true,
                        'update' => true,
                    ),
                    'product_descriptions.blurb' => array(
                        'title' => 'Short Description',
                    ),
                    'product_descriptions.description' => array(
                        'title' => 'Long Description',
                    ),
                    'product_descriptions.meta_keywords' => array(
                        'title' => 'Meta Kewords',
                    ),
                    'product_descriptions.meta_description' => array(
                        'title' => 'Meta Description',
                    ),
                    'products.keyword' => array(
                        'title' => 'SEO URL',
                    ),
                    'products.location' => array(
                        'title' => 'Location (Text up to 128 chars)',
                    ),
                    'products.quantity' => array(
                        'title' => 'Quantity',
                    ),
                    'products.minimum' => array(
                        'title' => 'Minimum Order Quantity',
                    ),
                    'products.maximum' => array(
                        'title' => 'Maximum Order Quantity',
                    ),
                    'products.price' => array(
                        'title' => 'Product price (In default currency)',
                    ),
                    'products.cost' => array(
                        'title' => 'Product Cost (In default currency)',
                    ),
                    'products.shipping_price' => array(
                        'title' => 'Fixed shipping price (In default currency)',
                    ),
                    'products.weight' => array(
                        'title' => 'Product Weight',
                    ),
                    'products.length' => array(
                        'title' => 'Product Length',
                    ),
                    'products.width' => array(
                        'title' => 'Product Width',
                    ),
                    'products.height' => array(
                        'title' => 'Product Height',
                    ),
                    'products.subtract' => array(
                        'title' => 'Track Stock Setting (1 or 0)',
                    ),
                    'products.free_shipping' => array(
                        'title' => 'Free shipping (1 or 0)',
                    ),
                    'products.shipping' => array(
                        'title' => 'Enable Shipping (1 or 0)',
                    ),
                    'products.sort_order' => array(
                        'title' => 'Sorting Order',
                    ),
                    'products.call_to_order' => array(
                        'title' => 'Order only by calling (1 or 0)',
                    ),
                    'categories.category' => array(
                        'title' => 'Category Name or Tree',
                        'split' => 1,
                    ),
                    'manufacturers.manufacturer' => array(
                        'title' => 'Manufacturer name',
                    ),
                    'images.image' =>  array(
                        'title' => "Image URL or List of URLs",
                        'split' => 1,
                    ),
                    'product_options.sort_order' => array(
                        'title' => 'Option sorting order (numeric)',
                    ),
                    'product_options.status' => array(
                        'title' => 'Option status (1 or 0)',
                    ),
                    'product_options.required' => array(
                        'title' => 'Option Required (1 or 0)',
                    ),
                    'product_options.name' => array(
                        'title' => 'Option name (up to 255 chars)',
                    ),
                    'product_options.product_option_values.name' => array(
                        'title' => 'Option value name (up to 255 chars)',
                    ),
                    'product_options.product_option_values.sku' => array(
                        'title' => 'Option value sku (up to 255 chars)',
                    ),
                    'product_options.product_option_values.quantity' => array(
                        'title' => 'Option value quantity',
                    ),
                    'product_options.product_option_values.price' => array(
                        'title' => 'Option value price',
                    ),
                    'product_options.product_option_values.default' => array(
                        'title' => 'Option value default selection (1 or 0)',
                    ),
                    'product_options.product_option_values.weight' => array(
                        'title' => 'Option value weight (numeric)',
                    ),
                    'product_options.product_option_values.sort_order' => array(
                        'title' => 'Option value sort otrder (1 or 0)',
                    ),
                )
            ),
            'categories' => array(
                'columns' => array(
                    'categories.status' => array(
                        'title' => 'Status (0 or 1)',
                    ),
                    'categories.sort_order' => array(
                        'title' => 'Sorting Order(Number)',
                    ),
                    'category_descriptions.name' => array(
                        'title' => 'Category Name or Tree',
                        'required' => true,
                        'split' => 1,
                    ),
                    'category_descriptions.description' => array(
                        'title' => 'Description',
                    ),
                    'category_descriptions.meta_keywords' => array(
                        'title' => 'Meta Keywords',
                    ),
                    'category_descriptions.meta_description' => array(
                        'title' => 'Medta Description',
                    ),
                    'images.image' =>  array(
                        'title' => "Image URL or List of URLs",
                        'split' => 1,
                    ),
                ),
            ),
            'manufacturers' => array(
                'columns' => array(
                    'manufacturers.sort_order' => array(
                        'title' => 'Sorting Order (Number)',
                        'default' => 0
                    ),
                    'manufacturers.name' => array(
                        'title' => 'Name (up to 64 chars)',
                        'required' => true,
                    ),
                    'images.image' =>  array(
                        'title' => "Image URL or List of URLs",
                        'split' => 1,
                    ),
                ),
            ),
        );
    }
}
