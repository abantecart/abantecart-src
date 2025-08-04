<?php /** @noinspection PhpMultipleClassDeclarationsInspection */
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */

/**
 * Class ModelToolImportProcess
 *
 * @property ModelCatalogProduct $model_catalog_product
 * @property ModelCatalogManufacturer $model_catalog_manufacturer
 * @property ModelLocalisationWeightClass $model_localisation_weight_class
 */
class ModelToolImportProcess extends Model
{
    public static $taskController = 'task/tool/import_process/processRows';
    public $errors = [];
    protected $eta = [];
    //numbers of rows per task step
    protected $rowDivider = 10;
    //processing time for one row in seconds
    protected $rowTimeLimit = 6;
    /**
     * @var ALog
     */
    protected $logger;

    /**
     * Array with configurations for import tables and fields
     *
     * @return array
     */
    public function importTableCols()
    {
        $this->data['import_column_definitions'] =
            [
                'products'      => [
                    'columns' => [
                        'products.status'                                  =>
                            [
                                'title' => 'Status (1 or 0)',
                                'alias' => 'status',
                            ],
                        'products.sku'                                     =>
                            [
                                'title'  => 'SKU (up to 64 chars)',
                                'update' => true,
                                'alias'  => 'sku',
                            ],
                        'products.model'                                   =>
                            [
                                'title'  => 'Model (up to 64 chars)',
                                'update' => true,
                                'alias'  => 'model',
                            ],
                        'product_descriptions.name'                        =>
                            [
                                'title'    => 'Name (up to 255 chars)',
                                'required' => true,
                                'update'   => true,
                                'alias'    => 'name',
                            ],
                        'product_descriptions.blurb'                       =>
                            [
                                'title' => 'Short Description',
                                'alias' => 'blurb',
                            ],
                        'product_descriptions.description'                 =>
                            [
                                'title' => 'Long Description',
                                'alias' => 'description',
                            ],
                        'product_descriptions.meta_keywords'               =>
                            [
                                'title' => 'Meta Keywords',
                                'alias' => 'meta keywords',
                            ],
                        'product_descriptions.meta_description'            =>
                            [
                                'title' => 'Meta Description',
                                'alias' => 'meta description',
                            ],
                        'products.keyword'                                 =>
                            [
                                'title' => 'SEO URL',
                                'alias' => 'seo url',
                            ],
                        'products.location'                                =>
                            [
                                'title' => 'Location (Text up to 128 chars)',
                                'alias' => 'warehouse',
                            ],
                        'products.quantity'                                =>
                            [
                                'title' => 'Quantity',
                                'alias' => 'stock',
                            ],
                        'products.minimum'                                 =>
                            [
                                'title' => 'Minimum Order Quantity',
                                'alias' => 'minimum quantity',
                            ],
                        'products.maximum'                                 =>
                            [
                                'title' => 'Maximum Order Quantity',
                                'alias' => 'maximum quantity',
                            ],
                        'products.price'                                   =>
                            [
                                'title' => 'Product price (In default currency)',
                                'alias' => 'price',
                            ],
                        'products.cost'                                    =>
                            [
                                'title' => 'Product Cost (In default currency)',
                                'alias' => 'cost',
                            ],
                        'products.shipping_price'                          =>
                            [
                                'title' => 'Fixed shipping price (In default currency)',
                                'alias' => 'shipping price',
                            ],
                        'products.tax_class_id'                            =>
                            [
                                'title' => 'Tax Class ID (Number, See tax settings)',
                                'alias' => 'tax id',
                            ],
                        'products.weight_class_id'                         =>
                            [
                                'title' => 'Weight Class ID (Number, See weight settings)',
                                'alias' => 'weight id',
                            ],
                        'products.length_class_id'                         =>
                            [
                                'title' => 'Length Class ID (Number, See length settings)',
                                'alias' => 'length id',
                            ],
                        'products.weight'                                  =>
                            [
                                'title' => 'Product Weight',
                                'alias' => 'weight',
                            ],
                        'products.length'                                  =>
                            [
                                'title' => 'Product Length',
                                'alias' => 'length',
                            ],
                        'products.width'                                   =>
                            [
                                'title' => 'Product Width',
                                'alias' => 'width',
                            ],
                        'products.height'                                  =>
                            [
                                'title' => 'Product Height',
                                'alias' => 'height',
                            ],
                        'products.subtract'                                =>
                            [
                                'title' => 'Track Stock Setting (1 or 0)',
                                'alias' => 'track stock',
                            ],
                        'products.free_shipping'                           =>
                            [
                                'title' => 'Free shipping (1 or 0)',
                                'alias' => 'free shipping',
                            ],
                        'products.shipping'                                =>
                            [
                                'title' => 'Enable Shipping (1 or 0)',
                                'alias' => 'requires shipping',
                            ],
                        'products.sort_order'                              =>
                            [
                                'title' => 'Sorting Order',
                                'alias' => 'sort order',
                            ],
                        'products.call_to_order'                           =>
                            [
                                'title' => 'Order only by calling (1 or 0)',
                                'alias' => 'call to order',
                            ],
                        'products.date_available'                          =>
                            [
                                'title' => 'Date Available (YYYY-MM-DD format)',
                                'alias' => 'date available',
                            ],
                        'categories.category'                              =>
                            [
                                'title'      => 'Category Name or Tree',
                                'split'      => 1,
                                'multivalue' => 1,
                                'alias'      => 'category',
                            ],
                        'manufacturers.manufacturer'                       =>
                            [
                                'title' => 'Manufacturer name',
                                'alias' => 'manufacturer name',
                            ],
                        'images.image'                                     =>
                            [
                                'title'      => "Image or List of URLs/Paths",
                                'split'      => 1,
                                'multivalue' => 1,
                                'alias'      => 'image url',
                            ],
                        'product_options.name'                             =>
                            [
                                'title'      => 'Option name (up to 255 chars)',
                                'multivalue' => 1,
                                'alias'      => 'option name',
                            ],
                        'product_options.sort_order'                       =>
                            [
                                'title'      => 'Option sorting order (numeric)',
                                'multivalue' => 1,
                                'alias'      => 'option sort order',
                            ],
                        'product_options.status'                           =>
                            [
                                'title'      => 'Option status (1 or 0)',
                                'multivalue' => 1,
                                'alias'      => 'option status',
                            ],
                        'product_options.required'                         =>
                            [
                                'title'      => 'Option Required (1 or 0)',
                                'multivalue' => 1,
                                'alias'      => 'option required',
                            ],
                        'product_options.product_option_values.name'       =>
                            [
                                'title'      => 'Option value name (up to 255 chars)',
                                'multivalue' => 1,
                                'alias'      => 'option value name',
                            ],
                        'product_options.product_option_values.sku'        =>
                            [
                                'title'      => 'Option value sku (up to 255 chars)',
                                'multivalue' => 1,
                                'alias'      => 'option value sku',
                            ],
                        'product_options.product_option_values.quantity'   =>
                            [
                                'title'      => 'Option value quantity',
                                'multivalue' => 1,
                                'alias'      => 'option value quantity',
                            ],
                        'product_options.product_option_values.price'      =>
                            [
                                'title'      => 'Option value price',
                                'multivalue' => 1,
                                'alias'      => 'option value price',
                            ],
                        'product_options.product_option_values.default'    =>
                            [
                                'title'      => 'Option value default selection (1 or 0)',
                                'multivalue' => 1,
                                'alias'      => 'option value default',
                            ],
                        'product_options.product_option_values.weight'     =>
                            [
                                'title'      => 'Option value weight (numeric)',
                                'multivalue' => 1,
                                'alias'      => 'option value weight',
                            ],
                        'product_options.product_option_values.sort_order' =>
                            [
                                'title'      => 'Option value sort order (1 or 0)',
                                'multivalue' => 1,
                                'alias'      => 'option value sort order',
                            ],
                        'product_options.product_option_values.image'      =>
                            [
                                'title'      => 'Option value image or List of URLs/Paths',
                                'split'      => 1,
                                'multivalue' => 1,
                                'alias'      => 'option value image',
                            ],
                    ],
                ],
                'categories'    => [
                    'columns' => [
                        'categories.status'                      =>
                            [
                                'title' => 'Status (0 or 1)',
                                'alias' => 'status',
                            ],
                        'categories.sort_order'                  =>
                            [
                                'title' => 'Sorting Order(Number)',
                                'alias' => 'sort order',
                            ],
                        'categories.keyword'                     =>
                            [
                                'title' => 'SEO URL',
                                'alias' => 'seo url',
                            ],
                        'category_descriptions.name'             =>
                            [
                                'title'      => 'Category Name or Tree',
                                'required'   => true,
                                'split'      => 1,
                                'multivalue' => 1,
                                'alias'      => 'name',
                            ],
                        'category_descriptions.description'      =>
                            [
                                'title' => 'Description',
                                'alias' => 'description',
                            ],
                        'category_descriptions.meta_keywords'    =>
                            [
                                'title' => 'Meta Keywords',
                                'alias' => 'meta keywords',
                            ],
                        'category_descriptions.meta_description' =>
                            [
                                'title' => 'Meta Description',
                                'alias' => 'meta description',
                            ],
                        'images.image'                           =>
                            [
                                'title'      => "Image or List of URLs/Paths",
                                'split'      => 1,
                                'multivalue' => 1,
                                'alias'      => 'image',
                            ],
                    ],
                ],
                'manufacturers' => [
                    'columns' => [
                        'manufacturers.sort_order' =>
                            [
                                'title'   => 'Sorting Order (Number)',
                                'default' => 0,
                                'alias'   => 'sort order',
                            ],
                        'manufacturers.keyword'    =>
                            [
                                'title' => 'SEO URL',
                                'alias' => 'seo url',
                            ],
                        'manufacturers.name'       =>
                            [
                                'title'    => 'Name (up to 64 chars)',
                                'required' => true,
                                'alias'    => 'name',
                            ],
                        'images.image'             =>
                            [
                                'title'      => "Image or List of URLs/Paths",
                                'split'      => 1,
                                'multivalue' => 1,
                                'alias'      => 'image',
                            ],
                    ],
                ],
            ];
        //allow hooks affect on it
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        return $this->data['import_column_definitions'];
    }

    /**
     * @param string $logFileName
     * @return void
     * @throws AException
     */
    public function initLogger(string $logFileName)
    {
        $this->logger = new ALog($logFileName);
    }

    /**
     * @param int $rowTimeLimit
     * @return void
     */
    public function setRowTimeLimit(int $rowTimeLimit)
    {
        $this->rowTimeLimit = $rowTimeLimit;
    }

    /**
     * @param int $rowDivider
     * @return void
     */
    public function setRowDivider(int $rowDivider)
    {
        $this->rowDivider = $rowDivider;
    }

    /**
     * @param string $task_name
     * @param array $data
     *
     * @return array|bool
     * @throws AException
     */
    public function createTask(string $task_name, array $data)
    {
        if (!$task_name) {
            throw new AException(AC_ERR_USER_ERROR, 'Can not to create task. Empty task name has been given.');
        }

        if (!$data['file'] && !$data['products_fields']) {
            throw new AException(AC_ERR_USER_ERROR, 'Missing required data to build a task (file, product_fields etc).');
        }
        //get file details
        $totalRowsCount = -1;
        $handle = fopen($data['file'], "r");
        if (is_resource($handle)) {
            while (fgetcsv($handle, 0, $data['delimiter']) !== false) {
                $totalRowsCount++;
            }
            unset($line);
            fclose($handle);
        } else {
            $this->errors[] = 'No import feed file available!';
            return false;
        }

        $tm = new ATaskManager();
        //create new task
        $task_id = $tm->addTask(
            [
                'name'               => $task_name,
                'starter'            => 1, //admin-side is starter
                'created_by'         => $this->user->getId(), //get starter id
                'status'             => $tm::STATUS_READY,
                'start_time'         => date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), (int)date('d') + 1, date('Y'))),
                'last_time_run'      => null,
                'progress'           => '0',
                'last_result'        => '1',
                'run_interval'       => '0',
                'max_execution_time' => ($totalRowsCount * $this->rowTimeLimit * 2),
            ]
        );
        if (!$task_id) {
            $this->errors = array_merge($this->errors, $tm->errors);
            return false;
        }

        $tm->updateTaskDetails(
            $task_id,
            [
                'created_by' => $this->user->getId(),
                'settings'   => [
                    'import_data'      => $data,
                    'total_rows_count' => $totalRowsCount,
                    'success_count'    => 0,
                    'failed_count'     => 0,
                ],
            ]
        );

        $sort_order = 1;
        $k = 0;
        while ($k < $totalRowsCount) {
            //create task step
            $stop = $k + $this->rowDivider;
            $stop = min($stop, $totalRowsCount);
            $step_id = $tm->addStep(
                [
                    'task_id'            => $task_id,
                    'sort_order'         => $sort_order,
                    'status'             => 1,
                    'last_time_run'      => null,
                    'last_result'        => 0,
                    'max_execution_time' => ($this->rowDivider * $this->rowTimeLimit * 2),
                    'controller'         => static::$taskController,
                    'settings'           => [
                        'start' => $k,
                        'stop'  => $stop,
                    ],
                ]
            );

            if (!$step_id) {
                $this->errors = array_merge($this->errors, $tm->errors);
                return false;
            } else {
                // get eta in seconds
                $this->eta[$step_id] = ($this->rowDivider * $this->rowTimeLimit * 2);
            }

            $sort_order++;
            $k += $this->rowDivider + 1;
        }

        $task_details = $tm->getTaskById($task_id);

        if ($task_details) {
            foreach ($this->eta as $step_id => $eta) {
                $task_details['steps'][$step_id]['eta'] = $eta;
                //remove settings from output json array. We will take it from database on execution.
                $task_details['steps'][$step_id]['settings'] = [];
            }
            return $task_details;
        } else {
            $this->errors[] = 'Can not to get task details for execution';
            $this->errors = array_merge($this->errors, $tm->errors);
            return false;
        }
    }

    /**
     * @param int|string $task_id
     * @param array $data
     * @param array $settings
     *
     * @return bool|null
     * @throws AException
     */
    public function process_products_record(int|string $task_id, array $data, array $settings)
    {
        $language_id = (int)$settings['language_id'] ?: $this->language->getContentLanguageID();
        $store_id = $settings['store_id'] ?? (int)$this->config->get('current_store_id');
        $this->load->model('catalog/product');
        $this->initLogger(DIR_LOGS . "products_import_" . $task_id . ".txt");
        return $this->addUpdateProduct($data, $settings, (int)$language_id, (int)$store_id);
    }

    /**
     * @param int|string $task_id
     * @param array $data
     * @param array $settings
     *
     * @return bool|null
     * @throws AException
     */
    public function process_categories_record(int|string $task_id, array $data, array $settings)
    {
        $language_id = (int)$settings['language_id'] ?: $this->language->getContentLanguageID();
        $store_id = $settings['store_id'] ?: $this->session->data['current_store_id'];
        $this->load->model('catalog/category');
        $this->initLogger(DIR_LOGS . "categories_import_" . $task_id . ".txt");
        return $this->addUpdateCategory($data, $settings, (int)$language_id, (int)$store_id);
    }

    /**
     * @param int|string $task_id
     * @param array $data
     * @param array $settings
     *
     * @return bool|null
     * @throws AException
     */
    public function process_manufacturers_record(int|string $task_id, $data, $settings)
    {
        $language_id = $settings['language_id'] ?: $this->language->getContentLanguageID();
        $store_id = $settings['store_id'] ?: $this->session->data['current_store_id'];
        $this->load->model('catalog/manufacturer');
        $this->initLogger(DIR_LOGS . "manufacturers_import_" . $task_id . ".txt");
        return $this->addUpdateBrand($data, $settings, $language_id, $store_id);
    }

    /**
     * @param array $record
     * @param array $settings
     * @param int $language_id
     * @param int $store_id
     *
     * @return bool|null
     * @throws AException
     */
    protected function addUpdateProduct(array $record, array $settings, int $language_id, int $store_id)
    {
        $prodMdl = $this->model_catalog_product;
        $status = false;
        $record = array_map('trim', $record);

        //process columns that needs to be concatenated
        if (isset($settings['concat']) && is_array($settings['concat'])) {
            $this->updateConcatenatedColumns($record, $settings);
        }
        //data mapping
        $data = $this->buildDataMap(
            $record,
            $settings['import_col'],
            $settings['products_fields'],
            $settings['split_col']
        );
        if (!$data) {
            $this->toLog("Error: Unable to build products import data map.");
            return false;
        }
        $action = $data['action'][0] ?? 'update_or_insert';
        $product = $this->filterArray((array)$data['products']);
        $product_desc = $this->filterArray((array)$data['product_descriptions']);
        $manufacturers = $this->filterArray((array)$data['manufacturers']);
        //check if row is complete and uniform
        if (
            (!$product_desc['name'] && !$product['sku'] && !$product['model'])
            || ($data['product_options'] && !$this->validateOptions((array)$data['product_options']))
        ) {
            $this->toLog('Error: Record is not complete or missing required data. Skipping!');
            return false;
        }
        $this->toLog("Processing record for product " . $product_desc['name'] . ".");

        //detect if we update or create new product based on update settings
        $new_product = true;
        $product_id = 0;
        $lookup_value = $unique_field = null;
        if ($settings['update_col']) {
            $unique_field_index = key($settings['update_col']);
            if (is_numeric($unique_field_index)) {
                $unique_field = $settings['products_fields'][$unique_field_index];
                $lookup_value = $this->getValueFromDataMap(
                    $unique_field,
                    $record,
                    $settings['products_fields'],
                    $settings['import_col']
                );
                $product_id = $this->getProductByField($unique_field, $lookup_value, $language_id, $store_id);
                if ($product_id) {
                    //we have product, update
                    $new_product = false;
                }
            }
        }
        //validate actions
        if ($action == 'update' && $new_product) {
            $this->toLog('Error: Update only action is set, but product not found. Skipping!');
            return false;
        } elseif ($action == 'insert' && !$new_product) {
            $this->toLog('Error: Insert only action is set, but product already exists. Skipping!');
            return false;
        } elseif ($action == 'delete' && ($new_product || !$product_id)) {
            $this->toLog(
                'Warn: Delete action is set, but product does not exists (' . $unique_field . ': ' . $lookup_value . '). Skipping!'
            );
            return true;
        }

        if ($action == 'delete' && $product_id) {
            $prodMdl->deleteProduct($product_id);
            $this->toLog("Deleted product '" . $product_desc['name'] . "' with ID " . $product_id);
            return true;
        }

        // import category if needed
        $categories = [];
        if ($data['categories'] && $data['categories']['category']) {
            $categories = $this->_process_categories($data['categories'], $language_id, $store_id);
        }

        $product_data = $product;
        // import brand if needed
        if ($manufacturers['manufacturer']) {
            $manufacturer_id = $this->_process_manufacturer($manufacturers['manufacturer'], 0, $store_id);
            $product_data['manufacturer_id'] = $manufacturer_id;
        }

        $this->load->model('catalog/product');
        if ($new_product) {
            $product_data['product_description'] = [
                $language_id => $product_desc,
            ];

            //apply default settings for new products only
            $default_arr = [
                'status'          => 1,
                'subtract'        => 1,
                'free_shipping'   => 0,
                'shipping'        => 1,
                'call_to_order'   => 0,
                'sort_order'      => 0,
                'weight_class_id' => 5,
                'length_class_id' => 3,
            ];
            foreach ($default_arr as $key => $val) {
                $product_data[$key] = $product_data[$key] ?? $val;
            }
            try {
                $product_id = $prodMdl->addProduct($product_data);
                $this->toLog("Created product '" . $product_desc['name'] . "' with ID " . $product_id);
                $status = true;
            } catch (Exception $e) {
                $this->toLog("Error: Failed to create product " . $product_desc['name'] . ". " . $e->getTraceAsString());
            }
        } else {
            //flat array for description (specific for update)
            $product_data['product_description'] = $product_desc;
            $prodMdl->updateProduct($product_id, $product_data);
            $this->toLog("Updated product " . $product_desc['name'] . " with ID " . $product_id);
            $status = true;
        }

        $product_links = [
            'product_store' => [$store_id],
        ];
        if (count($categories)) {
            $product_links['product_category'] = array_column($categories, 'category_id');
        }
        $prodMdl->updateProductLinks($product_id, $product_links);

        if (isset($data['images'])) {
            //process images
            $this->migrateImages(
                (array)$data['images'],
                'products',
                $product_id,
                (string)$product_desc['name'],
                $language_id
            );
        }

        if (isset($data['product_options'])) {
            //process options
            $this->_addUpdateOptions(
                $product_id,
                $data['product_options'],
                $product_data['weight_class_id']
            );
        }

        return $status;
    }

    protected function validateOptions(array $options)
    {
        foreach ($options as $option) {
            $arr = (array)$option;
            $flattened = [];
            array_walk_recursive($arr, function ($value) use (&$flattened) {
                $flattened[] = (string)$value;
            });

            $cc = implode('', $flattened);
            //if option array contains only empty values - skip option check
            if ($cc === '') {
                continue;
            }
            if (!is_array($option) || !array_filter($option) || !$option['name']) {
                $this->toLog('Error: Option name must be a non-empty string.');
                return false;
            }
        }
        return true;
    }

    /**
     * @param array $record
     * @param array $settings
     * @param int $language_id
     * @param int $store_id
     *
     * @return bool|null
     * @throws AException
     */
    protected function addUpdateCategory(array $record, array $settings, int $language_id, int $store_id)
    {
        $record = array_map('trim', (array)$record);

        //process columns that needs to be concatenated
        if (isset($settings['concat']) && is_array($settings['concat'])) {
            $this->updateConcatenatedColumns($record, $settings);
        }

        //data mapping
        $data = $this->buildDataMap(
            $record,
            $settings['import_col'],
            $settings['categories_fields'],
            $settings['split_col']
        );
        if (empty($data)) {
            $this->toLog("Error: Unable to build categories import data map.");
            return false;
        }

        $action = $data['action'][0] ?: 'update_or_insert';
        $category = $this->filterArray((array)$data['categories']);
        //check if we have split tree or an array
        $category_desc = $this->filterArray((array)$data['category_descriptions']);
        $category_tree = [];
        if (is_array($category_desc['name'])) {
            // we have category array tree
            $category_tree = $category_desc['name'];
            $category_desc['name'] = end($category_tree);
            $s_tree = implode(' -> ', $category_tree);
            $this->toLog("Processing record for category " . $s_tree);
        } else {
            $category_tree[] = $category_desc['name'];
            $this->toLog("Processing record for category " . $category_desc['name']);
        }
        //process all categories
        $categories = $this->_process_categories(
            [
                'category' => [
                    $category_tree
                ]
            ],
            $language_id,
            $store_id
        );
        //we will have always one category
        $category_id = $categories[0]['category_id'];
        $parent_category_id = $categories[0]['parent_id'];

        //validate actions
        if ($action == 'update' && !$category_id) {
            $this->toLog('Error: Update only action is set, but category not found. Skipping!');
            return false;
        } elseif ($action == 'insert' && $category_id) {
            $this->toLog('Error: Insert only action is set, but category already exists. Skipping!');
            return false;
        } elseif ($action == 'delete' && !$category_id) {
            $this->toLog('Error: Delete action is set, but category does not exists. Skipping!');
            return false;
        }

        if ($action == 'delete') {
            $this->model_catalog_category->deleteCategory($category_id);
            $this->toLog("Deleted category '" . $category_desc['name'] . "' with ID " . $category_id);
            return true;
        }

        if ($category_id) {
            //update category
            $this->model_catalog_category->editCategory(
                $category_id,
                array_merge(
                    $category,
                    ['parent_id' => $parent_category_id],
                    ['category_description' => [$language_id => $category_desc]],
                    ['category_store' => [$store_id]]
                )
            );
            $this->toLog("Updated category '" . $category_desc['name'] . "' with ID " . $category_id);
        } else {
            $default_arr = [
                'status'     => 1,
                'sort_order' => 0,
            ];
            foreach ($default_arr as $key => $val) {
                $category[$key] = $category[$key] ?? $val;
            }

            $category_id = $this->model_catalog_category->addCategory(
                array_merge(
                    $category,
                    ['parent_id' => $parent_category_id],
                    ['category_description' => [$language_id => $category_desc]],
                    ['category_store' => [$store_id]]
                )
            );
            if ($category_id) {
                $this->toLog("Created category " . $category_desc['name'] . " with ID " . $category_id);
            } else {
                $this->toLog("Error: Failed to create category '" . $category_desc['name'] . "'");
                return false;
            }
        }

        //process images
        $this->migrateImages(
            (array)$data['images'],
            'categories',
            $category_id,
            (string)$category_desc['name'],
            $language_id
        );

        return true;
    }

    /**
     * @param array $record
     * @param array $settings
     * @param int $language_id
     * @param int $store_id
     *
     * @return bool|null
     * @throws AException
     */
    protected function addUpdateBrand(array $record, array $settings, int $language_id, int $store_id)
    {
        $status = false;
        $record = array_map('trim', (array)$record);

        //process columns that needs to be concatenated
        if (isset($settings['concat']) && is_array($settings['concat'])) {
            $this->updateConcatenatedColumns($record, $settings);
        }

        //data mapping
        $data = $this->buildDataMap(
            $record,
            $settings['import_col'],
            $settings['manufacturers_fields'],
            $settings['split_col']
        );
        if (empty($data)) {
            $this->toLog("Error: Unable to build manufacturers import data map.");
            return false;
        }

        $action = $data['action'][0] ?: 'update_or_insert';
        $manufacturer = $this->filterArray((array)$data['manufacturers']);
        $manufacturer_id = $this->_process_manufacturer($manufacturer['name'], $manufacturer['sort_order'], $store_id);

        //validate actions
        if ($action == 'update' && !$manufacturer_id) {
            $this->toLog('Error: Update only action is set, but brand/manufacturer not found. Skipping!');
            return false;
        } elseif ($action == 'insert' && $manufacturer_id) {
            $this->toLog('Error: Insert only action is set, but brand/manufacturer already exists. Skipping!');
            return false;
        } elseif ($action == 'delete' && !$manufacturer_id) {
            $this->toLog('Error: Delete action is set, but brand/manufacturer does not exists. Skipping!');
            return false;
        }

        if ($action == 'delete') {
            $this->model_catalog_manufacturer->deleteManufacturer($manufacturer_id);
            $this->toLog("Deleted manufacturer '" . $manufacturer['name'] . "' with ID " . $manufacturer_id);
            return true;
        }

        if ($manufacturer_id) {
            $status = true;
            //process images
            $this->migrateImages(
                (array)$data['images'],
                'manufacturers',
                $manufacturer_id,
                (string)$manufacturer['name'],
                $language_id
            );
        }

        return $status;
    }

    /**
     * @param int $product_id
     * @param array $data
     * @param int $weight_class_id
     *
     * @return bool
     * @throws AException
     */
    protected function _addUpdateOptions($product_id, $data, $weight_class_id)
    {
        if (!is_array($data) || empty($data)) {
            //no option details
            return false;
        }

        $this->toLog("Creating product option for product ID " . $product_id);
        //get existing options and values.
        /** @var ModelCatalogProduct $prodMdl */
        $prodMdl = $this->load->model('catalog/product');
        $options = $prodMdl->getProductOptions($product_id);
        if ($options) {
            //delete all options if exist
            foreach ($options as $option) {
                $prodMdl->deleteProductOption($product_id, $option['product_option_id']);
            }
        }

        //add new options for each option
        for ($i = 0; $i < count($data); $i++) {
            //skip empty arrays
            if (!is_array($data[$i]) || !array_filter($data[$i]) || !$data[$i]['name']) {
                continue;
            }

            $opt_data = [
                'option_name'        => $data[$i]['name'],
                'element_type'       => 'S',
                'regexp_pattern'     => "",
                'error_text'         => '',
                'option_placeholder' => '',
            ];
            $opt_data['required'] = $data[$i]['required'] ?? 0;
            $opt_data['sort_order'] = $data[$i]['sort_order'] ?? 0;
            $opt_data['status'] = $data[$i]['status'] ?? 1;

            $p_option_id = $prodMdl->addProductOption($product_id, $opt_data);
            if ($p_option_id) {
                $this->toLog("Created product option '" . $data[$i]['name'] . "' with ID " . $p_option_id);
            } else {
                $this->toLog("Error: Failed to create product option '" . $data[$i]['name'] . "'.");
                return false;
            }

            //now load values. Pick longest data array
            $option_vals = $data[$i]['product_option_values'];

            //find largest key by count
            $cc = function ($value) {
                return is_array($value) ? count($value) : 1;
            };
            $counts = array_map($cc, (array)$option_vals);

            if ($counts) {
                if (max($counts) == 1) {
                    //single option value case
                    $this->_save_option_value($product_id, $weight_class_id, $p_option_id, $option_vals);
                } else {
                    for ($j = 0; $j < max($counts); $j++) {
                        //build flat associative array options value
                        $opt_val_data = [];
                        foreach (array_keys((array)$option_vals) as $key) {
                            $opt_val_data[$key] = $option_vals[$key][$j];
                        }
                        $this->_save_option_value($product_id, $weight_class_id, $p_option_id, $opt_val_data);
                    }
                }
            }
        }
        return true;
    }

    /**
     * @param int $productId
     * @param int $weight_class_id
     * @param int $optionId
     * @param array $data
     *
     * @return false|int|null
     * @throws AException
     */
    protected function _save_option_value(int $productId, int $weight_class_id, int $optionId, array $data)
    {
        $data = array_filter($data);
        if (!$data) {
            return false;
        }

        $valueData = [];
        $opt_keys = [
            'name'          => '',
            'sku'           => '',
            'txt_id'        => '',
            'quantity'      => 0,
            'sort_order'    => 0,
            'subtract'      => 0,
            'prefix'        => '$',
            'weight'        => 0,
            'weight_type'   => 'lbs',
            'default'       => 0,
            'price'         => 0,
            'cost'          => 0,
            'supplier_code' => '',
            'supplier_id'   => '',
        ];
        foreach ($opt_keys as $k => $v) {
            $valueData[$k] = $v;
            if (isset($data[$k])) {
                $valueData[$k] = $data[$k];
            }
        }
        //enable stock taking if quantity specified
        if ($valueData['quantity'] > 0) {
            $valueData['subtract'] = 1;
        }

        $this->load->model('localisation/weight_class');
        $weightClass = $this->model_localisation_weight_class->getWeightClass($weight_class_id);
        if ($weightClass['unit']) {
            $valueData['weight_type'] = $weightClass['unit'];
        }

        $optionValueId = $this->model_catalog_product->addProductOptionValueAndDescription(
            $productId,
            $optionId,
            $valueData
        );

        if ($optionValueId && $data['image']) {
            //process images
            $this->migrateImages(
                (array)$data,
                'product_option_value',
                $optionValueId,
                (string)$data['name'],
                $this->language->getContentLanguageID()
            );
        }
        return $optionValueId;
    }

    //add from URL download

    /**
     * @param array $data
     * @param string|null $object_txt_id
     * @param int|null $object_id
     * @param string|null $title
     * @param int|null $language_id
     *
     * @return bool
     * @throws AException
     */
    public function migrateImages(
        array   $data,
        ?string $object_txt_id = '',
        ?int    $object_id = 0,
        ?string $title = '',
        ?int    $language_id = 0
    )
    {
        $objects = [
            'products'             => 'Product',
            'categories'           => 'Category',
            'manufacturers'        => 'Brand',
            'product_option_value' => 'ProductOptionValue',
        ];

        if (!in_array($object_txt_id, array_keys($objects)) || !$data) {
            $this->toLog("Warning: Missing images for " . $object_txt_id . ".");
            return true;
        }

        $language_list = $this->language->getAvailableLanguages();
        $rm = new AResourceManager();
        $rm->setType('image');
        //delete existing resources
        $rm->unmapAndDeleteResources($object_txt_id, $object_id, 'image');

        //IMAGE PROCESSING
        $data['image'] = (array)$data['image'];
        foreach ($data['image'] as $srcUrl) {
            if (!$srcUrl) {
                continue;
            } else {
                if (is_array($srcUrl)) {
                    //we have an array from list of values. Run again
                    $this->migrateImages(['image' => $srcUrl], $object_txt_id, $object_id, $title, $language_id);
                    continue;
                }
            }
            //check if image is absolute path or remote URL
            $host = parse_url($srcUrl, PHP_URL_HOST);
            $imageBasename = basename(parse_url($srcUrl, PHP_URL_PATH));
            $dstFileName = DIR_RESOURCE . $rm->getTypeDir() . $imageBasename;
            if (!is_dir(DIR_RESOURCE . $rm->getTypeDir())) {
                @mkdir(DIR_RESOURCE . $rm->getTypeDir(), 0777);
            }

            if ($host === null) {
                //this is a path to file
                if (!copy($srcUrl, $dstFileName)) {
                    $this->toLog("Error: Unable to copy file " . $srcUrl . " to " . $dstFileName);
                    continue;
                }
            } else {
                //this is URL to image. Download first
                $fl = new AFile();
                if (($file = $fl->downloadFile($srcUrl)) === false) {
                    $this->toLog("Error: Unable to download file from " . $srcUrl);
                    continue;
                }
                //check if destination filename does not have an extension
                if (!pathinfo($dstFileName, PATHINFO_EXTENSION)) {
                    $fExt = getFileExtensionByMimeType($file->content_type);
                    $dstFileName .= '.' . $fExt;
                    $imageBasename .= '.' . $fExt;
                }

                if (!$fl->writeDownloadToFile($file, $dstFileName)) {
                    $this->toLog("Error: Unable to save downloaded file to " . $dstFileName);
                    continue;
                }
            }

            //save resource
            $resource = [
                'language_id'   => $language_id,
                'name'          => [],
                'title'         => [],
                'description'   => '',
                'resource_path' => $imageBasename,
                'resource_code' => '',
            ];
            foreach ($language_list as $lang) {
                $resource['name'][$lang['language_id']] = $title;
                $resource['title'][$lang['language_id']] = $title;
            }
            $resource_id = $rm->addResource($resource);
            if ($resource_id) {
                $this->toLog("Map image resource : " . $imageBasename . " " . $resource_id);
                $rm->mapResource($object_txt_id, $object_id, $resource_id);
            } else {
                $this->toLog("Error: Image resource can not be created. " . $this->db->error);
            }
            //set micro-delay in 50ms
            usleep(50000);
        }

        return true;
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param int $language_id
     * @param int $store_id
     *
     * @return mixed|null
     * @throws AException
     */
    public function getProductByField($field, $value, $language_id, $store_id)
    {
        if ($field == 'products.sku') {
            return $this->getProductIDBySku($value, $store_id);
        } else {
            if ($field == 'products.model') {
                return $this->getProductIDByModel($value, $store_id);
            } else {
                if ($field == 'product_descriptions.name') {
                    return $this->getProductIDByName($value, $language_id, $store_id);
                }
            }
        }
        return null;
    }

    /**
     * @param string $name
     * @param int $language_id
     * @param int $store_id
     *
     * @return mixed|null
     * @throws AException
     */
    public function getProductIDByName($name, $language_id, $store_id)
    {
        if ($name) {
            $query = $this->db->query(
                "SELECT p.product_id as product_id
                FROM " . $this->db->table("products") . " p
                LEFT JOIN " . $this->db->table("product_descriptions") . " pd 
                    ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$language_id . "')
                LEFT JOIN " . $this->db->table("products_to_stores") . " p2s 
                    ON (p.product_id = p2s.product_id)
                WHERE LCASE(pd.name) = '" . $this->db->escape(mb_strtolower($name)) . "' 
                        AND p2s.store_id = '" . (int)$store_id . "' 
                LIMIT 1"
            );
            return $query->row['product_id'];
        } else {
            return null;
        }
    }

    /**
     * @param string $model
     * @param int $store_id
     *
     * @return mixed|null
     * @throws AException
     */
    public function getProductIDByModel($model, $store_id)
    {
        if ($model) {
            $query = $this->db->query(
                "SELECT p.product_id as product_id
                FROM " . $this->db->table("products") . " p
                LEFT JOIN " . $this->db->table("products_to_stores") . " p2s 
                    ON (p.product_id = p2s.product_id)
                WHERE LCASE(p.model) = '" . $this->db->escape(mb_strtolower($model)) . "' 
                    AND p2s.store_id = '" . (int)$store_id . "' 
                LIMIT 1"
            );
            return $query->row['product_id'];
        } else {
            return null;
        }
    }

    /**
     * @param string $sku
     * @param int $store_id
     *
     * @return mixed|null
     * @throws AException
     */
    public function getProductIDBySku($sku, $store_id)
    {
        if ($sku) {
            $query = $this->db->query(
                "SELECT p.product_id as product_id
                FROM " . $this->db->table("products") . " p
                LEFT JOIN " . $this->db->table("products_to_stores") . " p2s 
                    ON (p.product_id = p2s.product_id)
                WHERE LCASE(p.sku) = '" . $this->db->escape(mb_strtolower($sku)) . "' 
                    AND p2s.store_id = " . (int)$store_id . " 
                LIMIT 1"
            );
            return $query->row['product_id'];
        } else {
            return null;
        }
    }

    /**
     * @param string $manufacturer_name
     * @param int $sort_order
     * @param int $store_id
     *
     * @return int|mixed
     * @throws AException
     */
    protected function _process_manufacturer($manufacturer_name, $sort_order, $store_id)
    {
        $sql = $this->db->query(
            "SELECT manufacturer_id 
             FROM " . $this->db->table("manufacturers") . " 
             WHERE LCASE(name) = '" . $this->db->escape(mb_strtolower($manufacturer_name)) . "' 
             LIMIT 1"
        );
        $manufacturer_id = $sql->row['manufacturer_id'];
        if (!$manufacturer_id) {
            //create category
            $this->load->model('catalog/manufacturer');
            $manufacturer_id = $this->model_catalog_manufacturer->addManufacturer(
                [
                    'sort_order'         => $sort_order,
                    'name'               => $manufacturer_name,
                    'manufacturer_store' => [$store_id],
                ]
            );
            if ($manufacturer_id) {
                $this->toLog("Created manufacturer '" . $manufacturer_name . "' with ID " . $manufacturer_id);
            } else {
                $this->toLog("Error: Failed to create manufacturer '" . $manufacturer_name);
            }
        }
        return $manufacturer_id;
    }

    /**
     * @param array $data
     * @param int $language_id
     * @param int $store_id
     *
     * @return array
     * @throws AException
     */
    protected function _process_categories($data, $language_id, $store_id)
    {
        if (!is_array($data['category'])) {
            return [];
        }
        $this->load->model('catalog/category');

        $ret = [];
        for ($i = 0; $i < count($data['category']); $i++) {
            //check if we have a tree in a form of array or just a category
            $categories = [];
            if (is_array($data['category'][$i])) {
                $categories = $data['category'][$i];
            } else {
                $categories[] = str_replace(',', '', $data['category'][$i]);
            }

            $last_parent_id = 0;
            foreach ($categories as $index => $c_name) {
                //is parent?
                $is_parent = !(($index + 1 == count($categories)));
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
                        $ret[] = ['category_id' => $cid, 'parent_id' => $last_parent_id];
                    }
                    break;
                }
            }
        }
        return $ret;
    }

    /**
     * @param string $category_name
     * @param int $language_id
     * @param int $store_id
     * @param int $parent_id
     *
     * @return mixed
     * @throws AException
     */
    protected function _get_category($category_name, $language_id, $store_id, $parent_id)
    {
        $sql = "SELECT cd.category_id 
                FROM " . $this->db->table("category_descriptions") . " cd
                INNER JOIN " . $this->db->table("categories_to_stores") . " c2s 
                    ON (cd.category_id = c2s.category_id)
                WHERE language_id = " . (int)$language_id . " 
                    AND c2s.store_id = " . (int)$store_id . "
                    AND LCASE(name) = '" . $this->db->escape(mb_strtolower($category_name)) . "'";
        $res = $this->db->query($sql);
        if ($res->num_rows == 1) {
            return $res->row['category_id'];
        } else {
            if ($res->num_rows > 1) {
                //we have categories with same names, locate based on parent.
                $cIds = array_column($res->rows, 'category_id');
                $sql2 = "SELECT category_id 
                        FROM " . $this->db->table("categories") . "
                        WHERE category_id IN (" . implode(', ', $cIds) . ") 
                            AND parent_id = " . (int)$parent_id . " 
                        ORDER BY parent_id DESC ";
                $res2 = $this->db->query($sql2);
                return $res2->row['category_id'];
            }
        }
        return false;
    }

    /**
     * @param string $category_name
     * @param int $language_id
     * @param int $store_id
     * @param int $pid
     *
     * @return int
     * @throws AException
     */
    protected function _save_category($category_name, $language_id, $store_id, $pid = 0)
    {
        $category_id = $this->model_catalog_category->addCategory(
            [
                'parent_id'            => $pid,
                'sort_order'           => 0,
                'status'               => 1,
                'category_description' => [
                    $language_id => ['name' => $category_name],
                ],
                'category_store'       => [$store_id],
            ]
        );
        if ($category_id) {
            $this->toLog("Created category '" . $category_name . "' with ID " . $category_id);
        } else {
            $this->toLog("Error: Failed to create category '" . $category_name . "'.");
        }
        return $category_id;
    }

    /**
     * Map data from record based on the settings
     *
     * @param array $record
     * @param       $import_col
     * @param       $fields
     * @param       $split_col
     *
     * @return array
     */
    protected function buildDataMap($record, $import_col, $fields, $split_col)
    {
        $output = [];
        $op_index = -1;
        $op_array = [];
        if (!is_array($import_col) || !is_array($fields)) {
            return $output;
        }

        //decode html encoded symbols such as &gt;
        if (isset($split_col) && is_array($split_col)) {
            $split_col = array_map('html_entity_decode', (array)$split_col);
        }

        foreach ($fields as $index => $field) {
            if (empty($field)) {
                continue;
            }
            $arr = [];
            $field_val = $record[$import_col[$index]] ?? '';
            $keys = array_reverse(explode('.', $field));
            if (end($keys) == 'product_options' && isset($field_val)) {
                //map options special way
                //check if this is still same option or it is new name
                if (count($keys) == 2) {
                    if ($keys[0] == 'name') {
                        $op_array[++$op_index]['name'] = $field_val;
                    } else {
                        $tmp_index = ($op_index >= 0) ? $op_index : 0;
                        $op_array[$tmp_index][$keys[0]] = $field_val;
                    }
                } elseif ($keys[0] == 'image') {
                    //leaf element
                    //check if we need to split the record data from list of values
                    if (isset($split_col) && !empty($split_col[$index])) {
                        $field_val = explode($split_col[$index], $field_val);
                        $field_val = array_map('trim', (array)$field_val);
                    }
                    if (!is_array($field_val)) {
                        $field_val = [$field_val];
                    }
                    $arr['product_option_values']['image'][] = $field_val;
                    $tmp_index = ($op_index >= 0) ? $op_index : 0;
                    $op_array[$tmp_index] = array_merge_recursive((array)$op_array[$tmp_index], $arr);
                } else {
                    for ($i = 0; $i < count($keys) - 1; $i++) {
                        if ($i == 0) {
                            $arr = [$keys[$i] => $field_val];
                        } else {
                            $arr = [$keys[$i] => $arr];
                        }
                    }
                    $tmp_index = ($op_index >= 0) ? $op_index : 0;
                    $op_array[$tmp_index] = array_merge_recursive((array)$op_array[$tmp_index], $arr);
                }
            } else {
                foreach ($keys as $key) {
                    if ($key === reset($keys)) {
                        //leaf element
                        //check if we need to split the record data from list of values
                        if (isset($split_col) && !empty($split_col[$index])) {
                            $field_val = explode($split_col[$index], $field_val);
                            $field_val = array_map('trim', (array)$field_val);
                        }
                        //skip empty values
                        if (isset($field_val[0])) {
                            $arr[$key][] = $field_val;
                        }
                    } else {
                        $arr = [$key => $arr];
                    }
                }

                $output = array_merge_recursive($output, $arr);
            }
        }

        if ($op_array) {
            $output = array_merge_recursive($output, ['product_options' => $op_array]);
        }
        return $output;
    }

    /**
     * @param $vals
     * @param $map
     *
     * @return void
     */
    protected function updateConcatenatedColumns(&$vals, &$map)
    {
        $concatMap = [];
        foreach ($map['concat'] as $index => $col) {
            $concatMap[$col['new_name']][$col['position']]['index'] = $index;
            $concatMap[$col['new_name']][$col['position']]['concat_by'] = $col['concat_by'];
        }
        //process each concatenated column
        foreach ($concatMap as $newName => $index) {
            $vals[$newName] = '';
            //add new column to import list
            $map['import_col'][] = $newName;
            $map[$map['table'] . '_fields'][] = $map[$map['table'] . '_fields'][$index[1]['index']];
            foreach ($index as $colDetails) {
                $colIndex = $colDetails['index'];
                $vals[$newName] .= $colDetails['concat_by'] . $vals[$map['import_col'][$colIndex]];
                //remove concatenated column from values and map
                unset(
                    $vals[$map['import_col'][$colIndex]],
                    $map['import_col'][$colIndex],
                    $map['products_fields'][$colIndex]
                );
            }
        }
        unset($map['concat']);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function filterArray(array $data = [])
    {
        $output = [];
        if (!$data) {
            return $output;
        }

        foreach ($data as $key => $val) {
            //get only first element of data array
            $output[$key] = reset($val);
        }
        return $output;
    }

    /**
     * Get a value from the record based on the setting key
     *
     * @param string $key
     * @param array $record
     * @param        $fields
     * @param        $columns
     *
     * @return mixed
     */
    protected function getValueFromDataMap($key, $record, $fields, $columns)
    {
        $index = array_search($key, (array)$fields);
        if ($index !== false) {
            return $record[$columns[$index]];
        }
        return null;
    }

    /**
     * @param string $message
     *
     * @return null
     */
    protected function toLog(string $message)
    {
        if (!$message) {
            return null;
        }
        $log = $this->logger ?? Registry::getInstance()->get('log');
        $log?->write($message);
        return true;
    }

}