<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2021 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/

/**
 * Class ControllerPagesProductSearch
 */
class ControllerPagesProductSearch extends AController
{
    protected $category;
    protected $path;

    public function __construct(Registry $registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        $this->prepareProductListingParameters();
     }

    public function main()
    {

        $get = $this->request->get;
        //is this an embed mode
        $this->data['cart_rt'] = $this->config->get('embed_mode')
            ? 'r/checkout/cart/embed'
            : 'checkout/cart';

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->resetBreadcrumbs();
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getHomeURL(),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );

        $httpQuery = $this->prepareProductSortingParameters();
        extract($httpQuery);
        if (isset($get['keyword'])) {
            $httpQuery['keyword'] = (string)$get['keyword'];
        }
        if (isset($get['description'])) {
            $httpQuery['description'] = (int)$get['description'];
        }
        if (isset($get['model'])) {
            $httpQuery['model'] = (int)$get['model'];
        }
        if (isset($get['category_id'])) {
            $httpQuery['category_id'] = (array)$get['category_id'];
        }
        if (isset($get['sort'])) {
            $httpQuery['sort'] = $get['sort'];
        }
        if (isset($get['page'])) {
            $httpQuery['page'] = (int)$get['page'];
        }
        if (isset($get['limit'])) {
            $httpQuery['limit'] = (int)$get['limit'];
        }

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('product/search', '&'.http_build_query($httpQuery)),
                'text'      => $this->language->get('heading_title'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        $this->data['keyword'] = $this->html->buildElement(
            [
                'type'  => 'input',
                'name'  => 'keyword',
                'value' => $get['keyword'],
            ]
        );

        $this->loadModel('catalog/category');
        $categories = $this->getCategories(ROOT_CATEGORY_ID);
        $options = ['0' => $this->language->get('text_category')]
            + array_column($categories,'name', 'category_id')
        ;

        $this->data['category'] = $this->html->buildElement(
            [
                'type'    => 'selectbox',
                'name'    => 'category_id[]',
                'options' => $options,
                'value'   => $get['category_id'],
            ]
        );

        $this->data['description'] = $this->html->buildElement(
            [
                'type'       => 'checkbox',
                'id'         => 'description',
                'name'       => 'description',
                'checked'    => (bool) $get['description'],
                'value'      => 1,
                'label_text' => $this->language->get('entry_description'),
            ]
        );

        $this->data['model'] = $this->html->buildElement(
            [
                'type'       => 'checkbox',
                'id'         => 'model',
                'name'       => 'model',
                'checked'    => (bool) $get['model'],
                'value'      => 1,
                'label_text' => $this->language->get('entry_model'),
            ]
        );

        $this->data['submit'] = $this->html->buildElement(
            [
                'type'  => 'button',
                'name'  => 'search_button',
                'text'  => $this->language->get('button_search'),
                'icon'  => 'fa fa-search',
                'style' => 'btn-default',
            ]
        );

        if (isset($get['keyword'])) {
            $this->loadModel('catalog/product');
            $promotion = new APromotion();

            $this->data['raw_data'] = $products_result = $this->model_catalog_product->getProductsByKeyword(
                [
                    'filter' => [
                        'keyword'         => $get['keyword'],
                        'category_id'     => $get['category_id'],
                        'description'     => $get['description'],
                        'model'           => $get['model'],
                        'manufacturer_id' => $get['manufacturer_id'],
                        'rating'          => $get['rating']
                    ],
                    'sort'  => $sort,
                    'order' => $order,
                    'start' => ($page - 1) * $limit,
                    'limit' => $limit
                ]
            );

            //if single result, redirect to the product
            if (count($products_result) == 1) {
                redirect(
                    $this->html->getSEOURL(
                        'product/product',
                        '&product_id='.key($products_result),
                        '&encode'
                    )
                );
            }

            $product_total = current($products_result)['total_num_rows'];

            if ($product_total) {
                $url = '';
                if (isset($get['category_id'])) {
                    $url .= '&category_id='.$get['category_id'];
                }

                if (isset($get['description'])) {
                    $url .= '&description='.$get['description'];
                }

                if (isset($get['model'])) {
                    $url .= '&model='.$get['model'];
                }

                $this->loadModel('catalog/review');
                $this->loadModel('tool/seo_url');
                $products = [];



                if ($products_result) {
                    $product_ids = array_column($products_result, 'product_id');
                    //Format product data specific for confirmation page
                    $resource = new AResource('image');
                    $thumbnails = $product_ids
                        ? $resource->getMainThumbList(
                            'products',
                            $product_ids,
                            $this->config->get('config_image_product_width'),
                            $this->config->get('config_image_product_height')
                        )
                        : [];
                    $stock_info = $this->model_catalog_product->getProductsStockInfo($product_ids);

                    foreach ($products_result as $result) {
                        $thumbnail = $thumbnails[$result['product_id']];

                        $rating = $this->config->get('display_reviews')
                                ? $this->model_catalog_review->getAverageRating($result['product_id'])
                                : false;
                        $special = false;
                        $discount = $promotion->getProductDiscount($result['product_id']);

                        if ($discount) {
                            $priceNum = $discount;
                        } else {
                            $priceNum = $result['price'];
                            $special = $promotion->getProductSpecial($result['product_id']);
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

                        $price = $this->currency->format(
                            $this->tax->calculate(
                                $priceNum,
                                $result['tax_class_id'],
                                $this->config->get('config_tax')
                            )
                        );

                        $options = $this->model_catalog_product->getProductOptions($result['product_id']);
                        if ($options) {
                            $add = $this->html->getSEOURL(
                                'product/product',
                                '&product_id='.$result['product_id'],
                                '&encode'
                            );
                        } else {
                            if ($this->config->get('config_cart_ajax')) {
                                $add = '#';
                            } else {
                                $add = $this->html->getSecureURL(
                                    $this->data['cart_rt'],
                                    '&product_id='.$result['product_id'],
                                    '&encode'
                                );
                            }
                        }

                        //check for stock status, availability and config
                        $track_stock = false;
                        $in_stock = false;
                        $no_stock_text = $this->language->get('text_out_of_stock');
                        $stock_checkout = $result['stock_checkout'] === ''
                            ? $this->config->get('config_stock_checkout')
                            : $result['stock_checkout'];
                        $total_quantity = 0;
                        if ($stock_info[$result['product_id']]['subtract']) {
                            $track_stock = true;
                            $total_quantity = $this->model_catalog_product->hasAnyStock($result['product_id']);
                            //we have stock or out of stock checkout is allowed
                            if ($total_quantity > 0 || $stock_checkout) {
                                $in_stock = true;
                            }
                        }

                        $products[] = array_merge(
                            $result,
                            [
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
                                'options'        => $options,
                                'special'        => $special,
                                'href'           =>
                                    $this->html->getSEOURL(
                                        'product/product',
                                        '&keyword='.$get['keyword'].$url.'&product_id='.$result['product_id'],
                                        '&encode'
                                    ),
                                'add'            => $add,
                                'description'    => html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'),
                                'track_stock'    => $track_stock,
                                'in_stock'       => $in_stock,
                                'no_stock_text'  => $no_stock_text,
                                'total_quantity' => $total_quantity,
                                'tax_class_id'   => $result['tax_class_id'],
                            ]
                        );
                    }
                }

                $this->data['products'] = $products;

                if ($this->config->get('config_customer_price') || $this->customer->isLogged())
                {
                    $display_price = true;
                } else {
                    $display_price = false;
                }
                $this->data['display_price'] = $display_price;
                $this->data['url'] = $this->html->getSEOURL('product/manufacturer', '&'.http_build_query($httpQuery));

                $sort_options = $this->data['sorts'];
                $sorting = $this->html->buildElement(
                    [
                        'type'    => 'selectbox',
                        'name'    => 'sort',
                        'options' => $sort_options,
                        'value'   => $sort.'-'.$order,
                    ]
                );
                $this->view->assign('sorting', $sorting);
                $pQuery = $httpQuery;
                unset($pQuery['page']);
                $pQuery['sort'] = $sorting_href;
                $pagination_url = $this->html->getSEOURL(
                    'product/search',
                    '&page={page}&'.http_build_query($pQuery,'',null,PHP_QUERY_RFC3986)
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
                $this->data['limit'] = $limit;
            }
        }
        $this->data['review_status'] = $this->config->get('display_reviews');

        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/product/search.tpl');
        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function getCategories($parent_id, $level = 0)
    {
        $level++;
        $data = [];
        $cat_id = explode(',', $parent_id);
        end($cat_id);
        $results = $this->model_catalog_category->getCategories((int)current($cat_id));

        foreach ($results as $result) {
            if (in_array($result['category_id'], (array)$this->path)) {
                $this->category = $result['category_id'];
            } else {
                $this->category = 0;
            }

            $data[] = [
                'category_id' => $result['category_id'],
                'name'        => str_repeat('&nbsp;&nbsp;&nbsp;', $level).$result['name'],
            ];
            $children = [];
            if ($this->category) {
                $children = $this->getCategories($parent_id.','.$result['category_id'], $level);
            }

            if ($children) {
                $data = array_merge($data, $children);
            }
            unset($children);
        }

        return $data;
    }
}
