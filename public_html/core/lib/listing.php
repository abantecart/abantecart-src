<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
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
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * Class AListing
 *
 * @property ACache $cache
 * @property ADB $db
 */
class AListing
{
    /** @var Registry */
    protected $registry;
    /** @var int */
    public $errors = 0;
    /** @var int */
    protected $custom_block_id;
    /** @var array */
    public $data_sources = [];

    //default list. Use public property $data_sources if you wish to change it inside class instance
    //or see methods
    /** @see addListingDataSource()
     * @see deleteListingDataSource()
     */
    const DATA_SOURCES  =  [
        'catalog_product_getRelatedProducts' => [
            'text'                 => 'text_products_related',
            'rl_object_name'       => 'products',
            'data_type'            => 'product_id',
            'storefront_model'     => 'catalog/product',
            /** @see ModelCatalogProduct::getProductRelated() */
            'storefront_method'    => 'getProductRelated',
            'storefront_view_path' => 'product/product',
        ],
        'catalog_product_getPopularProducts' => [
            'text'                 => 'text_products_popular',
            'rl_object_name'       => 'products',
            'data_type'            => 'product_id',
            'storefront_model'     => 'catalog/product',
            /** @see ModelCatalogProduct::getPopularProducts() */
            'storefront_method'    => 'getPopularProducts',
            'storefront_view_path' => 'product/product',
        ],
        'catalog_product_getSpecialProducts' => [
            'text'                 => 'text_products_special',
            'rl_object_name'       => 'products',
            'data_type'            => 'product_id',
            'storefront_model'     => 'catalog/product',
            /** @see ModelCatalogProduct::getProductSpecials() */
            'storefront_method'    => 'getProductSpecials',
            'storefront_view_path' => 'product/product',
        ],
        'catalog_category_getcategories'     => [
            'text'                 => 'text_categories',
            'rl_object_name'       => 'categories',
            'data_type'            => 'category_id',
            'storefront_model'     => 'catalog/category',
            /** @see ModelCatalogCategory::getCategories() */
            'storefront_method'    => 'getCategories',
            'storefront_view_path' => 'product/category',
        ],

        'catalog_category_getmanufacturers'  => [
            'text'                 => 'text_manufacturers',
            'rl_object_name'       => 'manufacturers',
            'data_type'            => 'manufacturer_id',
            'storefront_model'     => 'catalog/manufacturer',
            /** @see ModelCatalogManufacturer::getManufacturers() */
            'storefront_method'    => 'getManufacturers',
            'storefront_view_path' => 'product/manufacturer',
        ],
        'catalog_product_getfeatured'        => [
            'text'                 => 'text_featured',
            'rl_object_name'       => 'products',
            'data_type'            => 'product_id',
            'storefront_model'     => 'catalog/product',
            /** @see ModelCatalogProduct::getFeaturedProducts() */
            'storefront_method'    => 'getFeaturedProducts',
            'storefront_view_path' => 'product/product',
        ],
        'catalog_product_getlatest'          => [
            'text'                 => 'text_latest',
            'rl_object_name'       => 'products',
            'data_type'            => 'product_id',
            'storefront_model'     => 'catalog/product',
            /** @see ModelCatalogProduct::getLatestProducts() */
            'storefront_method'    => 'getLatestProducts',
            'storefront_view_path' => 'product/product',
        ],
        'catalog_product_getbestsellers'     => [
            'text'                 => 'text_bestsellers',
            'rl_object_name'       => 'products',
            'data_type'            => 'product_id',
            'storefront_model'     => 'catalog/product',
            /** @see ModelCatalogProduct::getBestSellerProducts() */
            'storefront_method'    => 'getBestsellerProducts',
            'storefront_view_path' => 'product/product',
        ],
        'media'                              => ['text' => 'text_media'],
        'custom_products'                    => [
            'language'             => 'catalog/product',
            'data_type'            => 'product_id',
            'view_path'            => 'catalog/product/update',
            'rl_object_name'       => 'products',
            'text'                 => 'text_custom_products',
            'storefront_model'     => 'catalog/product',
            /** @see ModelCatalogProduct::getProduct() */
            'storefront_method'    => 'getProduct',
            'storefront_view_path' => 'product/product',
            'items_list_url'       => 'product/product/related',
        ],
        'custom_categories'                  => [
            'language'             => 'catalog/category',
            'data_type'            => 'category_id',
            'view_path'            => 'catalog/category/update',
            'rl_object_name'       => 'categories',
            'text'                 => 'text_custom_categories',
            'storefront_model'     => 'catalog/category',
            'storefront_method'    => 'getCategory',
            'storefront_view_path' => 'product/category',
            'items_list_url'       => 'product/product/product_categories',
        ],
        'custom_manufacturers'               => [
            'language'             => 'catalog/manufacturer',
            'data_type'            => 'manufacturer_id',
            'view_path'            => 'catalog/category/update',
            'rl_object_name'       => 'manufacturers',
            'text'                 => 'text_custom_manufacturers',
            'storefront_model'     => 'catalog/manufacturer',
            'storefront_method'    => 'getManufacturer',
            'storefront_view_path' => 'product/manufacturer',
            'items_list_url'       => 'catalog/manufacturer_listing/getManufacturers',
        ],
        'collection'                         => [
            'model'             => 'catalog/collection',
            /** @see ModelCatalogCollection::getCollections() */
            'method'            => 'getCollections',
            'language'          => 'catalog/collections',
            'text'              => 'text_collection',
            'view_path'         => 'catalog/product/update',
            'rl_object_name'    => 'products',
            'data_type'         => 'product_id',
            'storefront_model'  => 'catalog/collection',
            'storefront_method' => 'getListingBlockProducts',
        ],
        'selected_content'                  => [
            'language'             => 'design/content',
            'data_type'            => 'content_id',
            'view_path'            => 'design/content/update',
            'text'                 => 'text_selected_content',
            'storefront_model'     => 'catalog/content',
            'storefront_method'    => 'getListingContent',
            'storefront_view_path' => 'content/content',
            'items_list_url'       => 'content/content',
        ],
    ];

    /**
     * @param int $custom_block_id
     */
    public function __construct($custom_block_id)
    {
        $this->registry = Registry::getInstance();
        $this->custom_block_id = (int) $custom_block_id;
        $this->data_sources = self::DATA_SOURCES;
    }

    public function __get($key)
    {
        return $this->registry->get($key);
    }

    public function __set($key, $value)
    {
        $this->registry->set($key, $value);
    }

    /**
     * @param int $store_id
     *
     * @return array
     * @throws AException
     */
    public function getCustomList($store_id = 0)
    {
        $store_id = (int) $store_id;
        if (!(int) $this->custom_block_id) {
            return [];
        }

        $custom_block_id = (int) $this->custom_block_id;
        $cache_key = 'blocks.custom.'.$custom_block_id.$store_id;
        $output = $this->cache->pull($cache_key);

        if ($output !== false) {
            return $output;
        }

        $result = $this->db->query(
            "SELECT *
            FROM `".$this->db->table('custom_lists')."`
            WHERE custom_block_id = '".$custom_block_id."'
             AND store_id = '".$store_id."'
            ORDER BY sort_order"
        );
        $output = $result->rows;
        $this->cache->push($cache_key, $output);
        return $output;
    }

    /**
     * @return array
     */
    public function getListingDataSources()
    {
        return $this->data_sources;
    }

    /**
     * @param string $key
     * @param array $data
     */
    public function addListingDataSource($key, $data)
    {
        $this->data_sources[$key] = $data;
    }

    /**
     * @param string $key
     */
    public function deleteListingDataSource($key)
    {
        unset($this->data_sources[$key]);
    }

    //method returns argument for call_user_func function usage when call storefront model to get list

    /**
     * @param string $model
     * @param string $method
     * @param array $args
     *
     * @return array
     * @throws AException
     */
    public function getListingArguments($model, $method, $args = [])
    {
        if (!$method || !$model || !$args) {
            return [];
        }
        if(isset($args['limit'])){
            $args['limit'] = (int)$args['limit'] ?: $this->registry->get('config')->get('config_catalog_limit');
        }

        $output = [];
        /** @see ModelCatalogCategory::getCategories() */
        if ($model == 'catalog/category' && $method == 'getCategories') {
            $args['parent_id'] = is_null($args['parent_id']) ? 0 : $args['parent_id'];
            $output = [
                'parent_id' => $args['parent_id'],
                'data' => [
                    'limit' => $args['limit']
                ],
            ];
        }
        /** @see ModelCatalogManufacturer::getManufacturers() */
        elseif ($model == 'catalog/manufacturer' && $method == 'getManufacturers') {
            $output = [
                'data' => [
                    'limit' => $args['limit'],
                ],
            ];
        }
        /** @see ModelCatalogProduct::getPopularProducts() */
        elseif ($model == 'catalog/product' && $method == 'getPopularProducts') {
            $output = [
                'limit' => $args['limit'],
            ];
        }
        /** @see ModelCatalogProduct::getProductRelated() */
        elseif ($model == 'catalog/product' && $method == 'getProductRelated') {
            $output = [
                'product_id' => $args['product_id'],
                'limit'      => $args['limit'],
            ];
        }
        /** @see ModelCatalogProduct::getProductSpecials() */
        elseif ($model == 'catalog/product' && $method == 'getProductSpecials') {
            $output = [
                'sort'  => 'p.sort_order',
                'order' => 'ASC',
                'start' => 0,
                'limit' => $args['limit'],
            ];
        }
        /** @see ModelCatalogProduct::getBestsellerProducts() */
        elseif ($model == 'catalog/product' && $method == 'getBestsellerProducts') {
            $output = [
                'limit' => $args['limit'],
            ];
        }
        /** @see ModelCatalogProduct::getFeaturedProducts() */
        elseif ($model == 'catalog/product' && $method == 'getFeaturedProducts') {
            $output = [
                'limit' => $args['limit'],
            ];
        }
        /** @see ModelCatalogProduct::getLatestProducts() */
        elseif ($model == 'catalog/product' && $method == 'getLatestProducts') {
            $output = [
                'limit' => $args['limit'],
            ];
        }
        /** @see ModelCatalogCollection::getListingBlockProducts() */
        elseif ($model == 'catalog/collection' && $method == 'getListingBlockProducts') {
            $output = [
                'collectionId' => $args['collection_id'],
                'limit'         => $args['limit'],
            ];
        }
        /** @see ModelCatalogContent::getListingContent() */
        elseif ($model == 'catalog/content' && $method == 'getListingContent') {
            $output = [
                'content_ids' => (array)$args['content_ids'],
                'limit'         => $args['limit']
            ];
        }

        return $output;
    }
}
