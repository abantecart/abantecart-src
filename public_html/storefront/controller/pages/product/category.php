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
        $default_sorting = $this->config->get('config_product_default_sort_order');
        $sort_prefix = '';
        if (strpos($default_sorting, 'name-') === 0) {
            $sort_prefix = 'pd.';
        } elseif (strpos($default_sorting, 'price-') === 0) {
            $sort_prefix = 'p.';
        }
        $this->data['sorts'] = [
            $sort_prefix . $default_sorting => $this->language->get('text_default'),
            'pd.name-ASC'                   => $this->language->get('text_sorting_name_asc'),
            'pd.name-DESC'                  => $this->language->get('text_sorting_name_desc'),
            'p.price-ASC'                   => $this->language->get('text_sorting_price_asc'),
            'p.price-DESC'                  => $this->language->get('text_sorting_price_desc'),
            'rating-DESC'                   => $this->language->get('text_sorting_rating_desc'),
            'rating-ASC'                    => $this->language->get('text_sorting_rating_asc'),
            'date_modified-DESC'            => $this->language->get('text_sorting_date_desc'),
            'date_modified-ASC'             => $this->language->get('text_sorting_date_asc'),
        ];
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

        /** @var ModelCatalogCategory $mdl */
        $mdl = $this->loadModel('catalog/category');
        $this->loadModel('tool/seo_url');

        if (!isset($request['path']) && isset($request['category_id'])) {
            $request['path'] = $request['category_id'];
        }

        if (isset($request['path'])) {
            $this->data['path'] = $request['path'];
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
        } else {
            $category_id = 0;
        }

        $category_info = [];
        if ($category_id) {
            $category_info = $mdl->getCategory($category_id);
        } elseif ($this->config->get('embed_mode')) {
            $category_info['name'] = $this->language->get('text_top_category');
        }

        if ($category_info) {
            $this->document->setTitle($category_info['name']);
            $this->document->setKeywords($category_info['meta_keywords']);
            $this->document->setDescription($category_info['meta_description']);

            $this->data['heading_title'] = $category_info['name'];
            $this->data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
            $this->data['text_sort'] = $this->language->get('text_sort');

            $page = $request['page'] ?? 1;
          
            if (isset($this->request->get['limit'])) {
              $limit = (int) $this->request->get['limit'];
            } else { 
              $limit = $this->config->get('config_catalog_limit');
            }

            $sorting_href = $request['sort'];
            if (!$sorting_href || !isset($this->data['sorts'][$request['sort']])) {
                $sorting_href = $this->config->get('config_product_default_sort_order');
            }
            list($sort, $order) = explode("-", $sorting_href);
            if ($sort == 'name') {
                $sort = 'pd.' . $sort;
            } elseif (in_array($sort, ['sort_order', 'price'])) {
                $sort = 'p.' . $sort;
            }

            $this->loadModel('catalog/product');
            $category_total = $mdl->getTotalCategoriesByCategoryId($category_id);
            $products_result = $this->model_catalog_product->getProductsByCategoryId(
                $category_id,
                $sort,
                $order,
                ($page - 1) * $limit,
                $limit
            );

            $product_total = $products_result
                ? ( $products_result[0]['total_num_rows']
                    ?? $this->model_catalog_product->getTotalProductsByCategoryId($category_id)
                  )
                : 0;

            if ($category_total || $product_total) {
                $categories = [];

                $results = $mdl->getCategories($category_id);
                $category_ids = array_column($results, 'category_id');
                //get thumbnails by one pass
                $resource = new AResource('image');
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

                $sort_options = [];
                foreach ($this->data['sorts'] as $item => $text) {
                    $sort_options[$item] = $text;
                }
                $sorting = $this->html->buildElement(
                    [
                        'type'    => 'selectbox',
                        'name'    => 'sort',
                        'options' => $sort_options,
                        'value'   => $sort . '-' . $order,
                    ]
                );
                $this->data['sorting'] = $sorting;
                $this->data['url'] = $this->html->getSEOURL('product/category', '&path=' . $request['path']);

                $pagination_url = $this->html->getSEOURL(
                    'product/category',
                    '&path=' . $request['path'] . '&sort=' . $sorting_href . '&page={page}' . '&limit=' . $limit,
                    '&encode'
                );

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
            $this->view->batchAssign($this->data);
        } else {
            $url = '';
            if (isset($request['sort'])) {
                $url .= '&sort=' . $request['sort'];
            }

            if (isset($request['order'])) {
                $url .= '&order=' . $request['order'];
            }

            if (isset($request['page'])) {
                $url .= '&page=' . $request['page'];
            }

            if (isset($request['path'])) {
                $this->document->addBreadcrumb(
                    [
                        'href'      => $this->html->getSEOURL(
                            'product/category',
                            '&path=' . $request['path'] . $url,
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

        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}
