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

class ControllerPagesProductCategory extends AController
{
    public function __construct(Registry $registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        $this->prepareProductListingParameters();
        $this->data['thumb_no_image'] = [
            'category' => true,
            'product'  => true
        ];
    }

    public function main()
    {
        $request = $this->request->get;

        //is this an embed mode
        $this->data['cart_rt'] = $this->config->get('embed_mode')
            ? 'r/checkout/cart/embed'
            : 'checkout/cart';

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('product/category');
        $this->document->resetBreadcrumbs();
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getHomeURL(),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );

        $category_info = [];
        $httpQuery = $this->prepareProductFilterParameters();
        extract($httpQuery);

        $brands = $request['manufacturer_id'];
        if ($brands && is_array($brands)) {
            $httpQuery['manufacturer_id'] = $brands;
        }
        $ratings = $request['rating'];
        if ($ratings && is_array($ratings)) {
            $httpQuery['rating'] = $ratings;
        }

        /** @var ModelCatalogCategory $mdl */
        $mdl = $this->loadModel('catalog/category');
        $this->loadModel('tool/seo_url');

        if (!isset($request['path']) && isset($request['category_id']) && is_numeric($request['category_id'])) {
            $request['path'] = $request['category_id'];
        }


        if (isset($request['path'])) {
            $httpQuery['path'] = $this->data['path'] = $request['path'];
            $path = '';
            $parts = explode('_', $request['path']);
            if (count($parts) == 1) {
                //see if this is a category ID to sub category, need to build full path
                $parts = explode('_', $mdl->buildPath($request['path']));
            }
            foreach ($parts as $path_id) {
                $category_info = $mdl->getCategory($path_id);
                if ($category_info) {
                    if (!$path) {
                        $path = $path_id;
                    } else {
                        $path .= '_' . $path_id;
                    }
                    $this->document->addBreadcrumb(
                        [
                            'href'      => $this->html->getSEOURL('product/category', '&path=' . $path, '&encode'),
                            'text'      => $category_info['name'],
                            'separator' => $this->language->get('text_separator'),
                        ]
                    );
                }
            }
            $category_id = array_pop($parts);
            $category_info = $mdl->getCategory($category_id);
        } elseif (is_array($request['category_id'])) {
            $category_id = filterIntegerIdList($request['category_id']);
            if($category_id){
                $httpQuery['category_id'] = $category_id;
            }
            if ($category_id) {
                $extractFields = ['name', 'meta_keywords', 'meta_description', 'description'];
                $tmp = [];
                foreach ($category_id as $catId) {
                    $info = $mdl->getCategory($catId);
                    foreach ($extractFields as $fName) {
                        $tmp[$fName][] = $info[$fName];
                    }
                }
                foreach ($extractFields as $fName) {
                    $tmp[$fName] = array_unique(array_filter(array_map('trim',$tmp[$fName])));
                    $category_info[$fName] = implode(', ', $tmp[$fName]);
                }
                unset($tmp);

                $this->document->addBreadcrumb(
                    [
                        'href'      => $this->html->getSEOURL(
                            'product/category',
                            '&' . http_build_query($httpQuery)),
                        'text'      => $category_info['name'],
                        'separator' => $this->language->get('text_separator')
                    ]
                );
                $this->data['filter_url'] = $this->html->getSEOURL( 'product/category', '&' . http_build_query($httpQuery) );
            }
        } else {
            $category_id = 0;
        }

        //if category not set but brands are selected
        if(!$category_id && $httpQuery['manufacturer_id']){
          redirect($this->html->getSecureURL('product/manufacturer', '&' . http_build_query($httpQuery)));
        }

        if ($category_info) {
            if ($this->config->get('embed_mode')) {
                $category_info['name'] = $this->language->get('text_top_category');
            }
            $this->document->setTitle($category_info['name']);
            $this->document->setKeywords($category_info['meta_keywords']);
            $this->document->setDescription($category_info['meta_description']);

            $this->data['heading_title'] = $category_info['name'];
            if(!is_array($category_id)) {
                $this->data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
            }
            $this->data['text_sort'] = $this->language->get('text_sort');

            $this->loadModel('catalog/product');
            $category_total = !is_array($category_id) ? $mdl->getTotalCategoriesByCategoryId($category_id) : 0;
            $productFilter = [
                'sort'  => $sort,
                'order' => $order,
                'limit' => $limit,
                'start' => ($page - 1) * $limit,
            ];

            if (isset($this->request->get['manufacturer_id'])) {
                $productFilter['filter']['manufacturer_id'] = $this->request->get['manufacturer_id'];
            }
            if (isset($this->request->get['rating'])) {
                $productFilter['filter']['rating'] = $this->request->get['rating'];
            }

            $products_result = $this->model_catalog_product->getProductsByCategoryId(
                $category_id,
                $productFilter
            );

            $product_total = $products_result
                ? ($products_result[0]['total_num_rows'] ?? $this->model_catalog_product->getTotalProductsByCategoryId($category_id))
                : 0;
            //if requested page does not exist
            if($product_total < $page*$limit && $page>1){
                $httpQuery['page'] = 1;
                redirect($this->html->getSEOURL('product/category', '&'.http_build_query($httpQuery)));
            }

            if ($category_total || $product_total) {
                $categories = [];
                $resource = new AResource('image');

                if(!is_array($category_id)){
                    $results = $mdl->getCategories($category_id);
                    $category_ids = array_column($results, 'category_id');
                    //get thumbnails by one pass
                    $thumbnails = $category_ids
                        ? $resource->getMainThumbList(
                            'categories',
                            $category_ids,
                            $this->config->get('config_image_category_width'),
                            $this->config->get('config_image_category_height'),
                            $this->data['thumb_no_image']['category']
                        )
                        : [];

                    foreach ($results as $result) {
                        $thumbnail = $thumbnails[$result['category_id']];
                        $categories[] = [
                            'name'  => $result['name'],
                            'href'  => $this->html->getSEOURL(
                                'product/category',
                                '&path=' . $request['path'] . '_' . $result['category_id'],
                                '&encode'
                            ),
                            'thumb' => $thumbnail,
                        ];
                    }
                    $this->data['categories'] = $categories;
                }
                $this->data['button_add_to_cart'] = $this->language->get('button_add_to_cart');

                $product_ids = array_column($products_result, 'product_id');
                $products = [];

                $products_info = $this->model_catalog_product->getProductsAllInfo($product_ids);
                $thumbnails = $product_ids
                    ? $resource->getMainThumbList(
                        'products',
                        $product_ids,
                        $this->config->get('config_image_product_width'),
                        $this->config->get('config_image_product_height'),
                        $this->data['thumb_no_image']['product']
                    )
                    : [];
                $stock_info = $this->model_catalog_product->getProductsStockInfo($product_ids);
                foreach ($products_result as $result) {
                    $thumbnail = $thumbnails[$result['product_id']];
                    $rating = $products_info[$result['product_id']]['rating'];
                    $special = false;
                    $discount = $products_info[$result['product_id']]['discount'];
                    if ($discount) {
                        $price = $this->currency->format(
                            $this->tax->calculate(
                                $discount,
                                $result['tax_class_id'],
                                $this->config->get('config_tax')
                            )
                        );
                    } else {
                        $price = $this->currency->format(
                            $this->tax->calculate(
                                $result['price'],
                                $result['tax_class_id'],
                                $this->config->get('config_tax')
                            )
                        );
                        $special = $products_info[$result['product_id']]['special'];
                        if ($special) {
                            $special = $this->currency->format(
                                $this->tax->calculate(
                                    $special,
                                    $result['tax_class_id'],
                                    $this->config->get('config_tax')
                                )
                            );
                        }
                    }

                    if ($products_info[$result['product_id']]['options']) {
                        $add = $this->html->getSEOURL(
                            'product/product',
                            '&product_id=' . $result['product_id'],
                            '&encode'
                        );
                    } else {
                        $add = $this->config->get('config_cart_ajax')
                            ? '#'
                            : $this->html->getSecureURL(
                                $this->data['cart_rt'],
                                '&product_id=' . $result['product_id'],
                                '&encode'
                            );
                    }
                    //check for stock status, availability and config
                    $track_stock = false;
                    $in_stock = false;
                    $no_stock_text = $this->language->get('text_out_of_stock');
                    $total_quantity = 0;
                    $stock_checkout = $result['stock_checkout'] === ''
                        ? $this->config->get('config_stock_checkout')
                        : $result['stock_checkout'];
                    if ($stock_info[$result['product_id']]['subtract']) {
                        $track_stock = true;
                        $total_quantity = $this->model_catalog_product->hasAnyStock($result['product_id']);
                        //we have stock or out of stock checkout is allowed
                        if ($total_quantity > 0 || $stock_checkout) {
                            $in_stock = true;
                        }
                    }

                    $products[] = [
                        'product_id'     => $result['product_id'],
                        'name'           => $result['name'],
                        'blurb'          => $result['blurb'],
                        'model'          => $result['model'],
                        'rating'         => $rating,
                        'stars'          => sprintf($this->language->get('text_stars'), $rating),
                        'thumb'          => $thumbnail,
                        'price'          => $price,
                        'raw_price'      => $result['price'],
                        'call_to_order'  => $result['call_to_order'],
                        'options'        => $products_info[$result['product_id']]['options'],
                        'special'        => $special,
                        'href'           => $this->html->getSEOURL(
                            'product/product',
                            '&path=' . $request['path'] . '&product_id=' . $result['product_id'],
                            '&encode'
                        ),
                        'add'            => $add,
                        'description'    => html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'),
                        'track_stock'    => $track_stock,
                        'in_stock'       => $in_stock,
                        'no_stock_text'  => $no_stock_text,
                        'total_quantity' => $total_quantity,
                        'tax_class_id'   => $result['tax_class_id'],
                    ];
                }
                $this->data['products'] = $products;

                if ($this->config->get('config_customer_price')) {
                    $display_price = true;
                } elseif ($this->customer->isLogged()) {
                    $display_price = true;
                } else {
                    $display_price = false;
                }
                $this->data['display_price'] = $display_price;

                $sort_options = $this->data['sorts'];
                $sorting = $this->html->buildElement(
                    [
                        'type'    => 'selectbox',
                        'name'    => 'sort',
                        'options' => $sort_options,
                        'value'   => $sort . '-' . $order,
                    ]
                );
                $this->data['sorting'] = $sorting;
                $pQuery = $httpQuery;
                unset($pQuery['page']);
                $pQuery['sort'] = $sorting_href;
                $pagination_url = $this->html->getSEOURL(
                    'product/category',
                    '&page={page}&'.http_build_query($pQuery,'',null,PHP_QUERY_RFC3986)
                );

                $this->data['url'] = $this->html->getSEOURL('product/category', '&'.http_build_query($httpQuery));

                $this->data['pagination_bootstrap'] = $this->html->buildElement(
                    [
                        'type'       => 'Pagination',
                        'name'       => 'pagination',
                        'text'       => $this->language->get('text_pagination'),
                        'text_limit' => $this->language->get('text_per_page'),
                        'total'      => $product_total,
                        'page'       => $page,
                        'limit'      => $limit,
                        'url'        => $pagination_url,
                        'style'      => 'pagination',
                    ]
                );

                $this->data['sort'] = $sort;
                $this->data['order'] = $order;
                $this->view->setTemplate('pages/product/category.tpl');
            } else {
                $this->document->setTitle($category_info['name']);
                $this->document->setDescription($category_info['meta_description']);
                $this->data['heading_title'] = $category_info['name'];
                $this->data['text_error'] = $this->language->get('text_empty');
                $this->data['button_continue'] = $this->language->get('button_continue');
                $this->data['continue'] = $this->html->getHomeURL();
                $this->data['categories'] = [];
                $this->data['products'] = [];
                $this->view->setTemplate('pages/product/category.tpl');
            }
            $this->data['review_status'] = $this->config->get('display_reviews');
        } else {
            if (isset($request['path'])) {
                $this->document->addBreadcrumb(
                    [
                        'href'      => $this->html->getSEOURL(
                            'product/category',
                            '&path=' . $request['path'] . http_build_query($httpQuery),
                            '&encode'
                        ),
                        'text'      => $this->language->get('text_error'),
                        'separator' => $this->language->get('text_separator'),
                    ]
                );
            }

            $this->document->setTitle($this->language->get('text_error'));
            $this->data['heading_title'] = $this->language->get('text_error');
            $this->data['text_error'] = $this->language->get('text_error');
            $this->data['button_continue'] = $this->html->buildElement(
                [
                    'type'  => 'button',
                    'name'  => 'continue_button',
                    'text'  => $this->language->get('button_continue'),
                    'style' => 'button',
                ]
            );
            $this->data['continue'] = $this->html->getHomeURL();
            $this->view->setTemplate('pages/error/not_found.tpl');
        }
        $this->view->batchAssign($this->data);
        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}
