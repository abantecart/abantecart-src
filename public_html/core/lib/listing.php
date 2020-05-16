<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * Class AListing
 *
 * @property ACache $cache
 * @property ADB    $db
 */
class AListing
{
    /**
     * @var Registry
     */
    protected $registry;
    /**
     * @var int
     */
    public $errors = 0;
    /**
     * @var int
     */
    protected $custom_block_id;
    /**
     * @var array
     */
    public $data_sources = array();

    /**
     * @param int $custom_block_id
     */
    public function __construct($custom_block_id)
    {
        $this->registry = Registry::getInstance();
        $this->custom_block_id = (int)$custom_block_id;
        // datasources
        $this->data_sources = Array(
            'catalog_product_getPopularProducts' => array(
                'text'                 => 'text_products_popular',
                'rl_object_name'       => 'products',
                'data_type'            => 'product_id',
                'storefront_model'     => 'catalog/product',
                'storefront_method'    => 'getPopularProducts',
                'storefront_view_path' => 'product/product',
            ),
            'catalog_product_getSpecialProducts' => array(
                'text'                 => 'text_products_special',
                'rl_object_name'       => 'products',
                'data_type'            => 'product_id',
                'storefront_model'     => 'catalog/product',
                'storefront_method'    => 'getProductSpecials',
                'storefront_view_path' => 'product/product',
            ),
            'catalog_category_getcategories'     => array(
                'text'                 => 'text_categories',
                'rl_object_name'       => 'categories',
                'data_type'            => 'category_id',
                'storefront_model'     => 'catalog/category',
                'storefront_method'    => 'getCategories',
                'storefront_view_path' => 'product/category',
            ),
            'catalog_category_getmanufacturers'  => array(
                'text'                 => 'text_manufacturers',
                'rl_object_name'       => 'manufacturers',
                'data_type'            => 'manufacturer_id',
                'storefront_model'     => 'catalog/manufacturer',
                'storefront_method'    => 'getManufacturers',
                'storefront_view_path' => 'product/manufacturer',
            ),
            'catalog_product_getfeatured'        => array(
                'text'                 => 'text_featured',
                'rl_object_name'       => 'products',
                'data_type'            => 'product_id',
                'storefront_model'     => 'catalog/product',
                'storefront_method'    => 'getFeaturedProducts',
                'storefront_view_path' => 'product/product',
            ),
            'catalog_product_getlatest'          => array(
                'text'                 => 'text_latest',
                'rl_object_name'       => 'products',
                'data_type'            => 'product_id',
                'storefront_model'     => 'catalog/product',
                'storefront_method'    => 'getLatestProducts',
                'storefront_view_path' => 'product/product',
            ),
            'catalog_product_getbestsellers'     => array(
                'text'                 => 'text_bestsellers',
                'rl_object_name'       => 'products',
                'data_type'            => 'product_id',
                'storefront_model'     => 'catalog/product',
                'storefront_method'    => 'getBestsellerProducts',
                'storefront_view_path' => 'product/product',
            ),
            'media'                              => array('text' => 'text_media'),
            'custom_products'                    => array(

                'model'                => 'catalog/product',
                'total_method'         => 'getTotalProducts',
                'method'               => 'getProducts',
                'language'             => 'catalog/product',
                'data_type'            => 'product_id',
                'view_path'            => 'catalog/product/update',
                'rl_object_name'       => 'products',
                'text'                 => 'text_custom_products',
                'storefront_model'     => 'catalog/product',
                'storefront_method'    => 'getProduct',
                'storefront_view_path' => 'product/product',
                'items_list_url'       => 'product/product/related',
            ),
            'custom_categories'                  => array(
                'model'                => 'catalog/category',
                'total_method'         => 'getTotalCategories',
                'method'               => 'getCategoriesData',
                'language'             => 'catalog/category',
                'data_type'            => 'category_id',
                'view_path'            => 'catalog/category/update',
                'rl_object_name'       => 'categories',
                'text'                 => 'text_custom_categories',
                'storefront_model'     => 'catalog/category',
                'storefront_method'    => 'getCategory',
                'storefront_view_path' => 'product/category',
                'items_list_url'       => 'product/product/product_categories',
            ),
            'custom_manufacturers'               => array(
                'model'                => 'catalog/manufacturer',
                'total_method'         => 'getTotalManufacturers',
                'method'               => 'getManufacturers',
                'language'             => 'catalog/manufacturer',
                'data_type'            => 'manufacturer_id',
                'view_path'            => 'catalog/category/update',
                'rl_object_name'       => 'manufacturers',
                'text'                 => 'text_custom_manufacturers',
                'storefront_model'     => 'catalog/manufacturer',
                'storefront_method'    => 'getManufacturer',
                'storefront_view_path' => 'product/manufacturer',
                'items_list_url'       => 'catalog/manufacturer_listing/getManufacturers',
            ),
            'collection'  => array(
                'model'                => 'catalog/collection',
                'method'               => 'getCollections',
                'language'             => 'catalog/collections',
                'text'                 => 'text_collection',
                'view_path'            => 'catalog/product/update',
                'rl_object_name'       => 'products',
                'data_type'            => 'product_id',
                'storefront_model'     => 'catalog/collection',
                'storefront_method'    => 'getListingBlockProducts',
            ),
        );
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
     */
    public function getCustomList($store_id=0)
    {
        $store_id = (int)$store_id;
        if (!(int)$this->custom_block_id) {
            return array();
        }

        $custom_block_id = (int)$this->custom_block_id;
        $cache_key = 'blocks.custom.'.$custom_block_id.$store_id;
        $output = $this->cache->pull($cache_key);

        if ($output !== false) {
            return $output;
        }

        $result = $this->db->query("SELECT *
									FROM `".$this->db->table('custom_lists')."`
									WHERE custom_block_id = '".$custom_block_id."'
									 AND store_id = '".$store_id."'
									ORDER BY sort_order");
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
     * @param array  $data
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
     * @param array  $args
     *
     * @return array
     */
    public function getListingArguments($model, $method, $args = array())
    {
        if (!$method || !$model || !$args) {
            return false;
        }
        $output = array();
        if ($model == 'catalog/category' && $method == 'getCategories') {
            $args['parent_id'] = is_null($args['parent_id']) ? 0 : $args['parent_id'];
            $output = array($args['parent_id'], $args['limit']);
        } elseif ($model == 'catalog/manufacturer' && $method == 'getManufacturers') {
            $output = array(array('limit' => $args['limit']));
        } elseif ($model == 'catalog/product' && $method == 'getPopularProducts') {
            $output = array('limit' => $args['limit']);
        } elseif ($model == 'catalog/product' && $method == 'getProductSpecials') {
            $output = array('p.sort_order', 'ASC', 0, 'limit' => $args['limit']);
        } elseif ($model == 'catalog/product' && $method == 'getBestsellerProducts') {
            $output = array($args['limit']);
        } elseif ($model == 'catalog/product' && $method == 'getFeaturedProducts') {
            $output = array($args['limit']);
        } elseif ($model == 'catalog/product' && $method == 'getLatestProducts') {
            $output = array($args['limit']);
        } elseif ($model == 'catalog/collection' && $method == 'getListingBlockProducts') {
            $output = array($args['collection_id'], $args['limit']);
        }

        return $output;
    }
}
