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

class ControllerPagesProductManufacturer extends AController
{
    public function __construct(Registry $registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        $this->prepareProductListingParameters();
    }

    public function main()
    {
        $get = $this->request->get;

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $httpQuery = $this->prepareProductSortingParameters();
        extract($httpQuery);

        $brands = $get['manufacturer_id'];
        if ($brands) {
            $httpQuery['manufacturer_id'] = $brands;
        }
        $ratings = $get['rating'];
        if ($ratings && is_array($ratings)) {
            $httpQuery['rating'] = $ratings;
        }

        if ($this->config->get('embed_mode')) {
            $cart_rt = 'r/checkout/cart/embed';

            //load special headers
            $this->addChild('responses/embed/head', 'head');
            $this->addChild('responses/embed/footer', 'footer');
        } else {
            $cart_rt = 'checkout/cart';
        }
        $this->data['cart_rt'] = $cart_rt;

        $this->loadLanguage('product/manufacturer');

        /** @var ModelCatalogManufacturer $mdl */
        $mdl = $this->loadModel('catalog/manufacturer');
        $this->loadModel('catalog/product');
        $this->loadModel('tool/seo_url');
        $this->loadModel('tool/image');

        $this->document->resetBreadcrumbs();
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getHomeURL(),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );
        $resource = new AResource('image');
        if (is_numeric($brands)) {
            $manufacturer_id = $brands;
            $manufacturer_info = $mdl->getManufacturer($manufacturer_id);

            $thumbnail = $resource->getMainThumb(
                'manufacturers',
                $manufacturer_id,
                $this->config->get('config_image_manufacturer_width'),
                $this->config->get('config_image_manufacturer_height'),
                false
            );
            $this->data['manufacturer_icon'] = $thumbnail;
        } elseif(is_array($brands)) {
            $manufacturer_id = filterIntegerIdList($brands);
            $extractFields = ['name'];
            $tmp = [];
            if($manufacturer_id) {
                foreach ($manufacturer_id as $brandId) {
                    $info = $mdl->getManufacturer($brandId);
                    foreach ($extractFields as $fName) {
                        $tmp[$fName][] = $info[$fName];
                    }
                }
                foreach ($extractFields as $fName) {
                    $tmp[$fName] = array_unique(array_filter(array_map('trim', $tmp[$fName])));
                    $manufacturer_info[$fName] = implode(', ', $tmp[$fName]);
                }
            }
            unset($tmp);
        }


        if ($manufacturer_info) {
            $this->document->addBreadcrumb(
                [
                    'href'      => $this->html->getSEOURL(
                        'product/manufacturer',
                        '&' . http_build_query($httpQuery),
                        '&encode'
                    ),
                    'text'      => $manufacturer_info['name'],
                    'separator' => $this->language->get('text_separator'),
                ]
            );

            $this->document->setTitle($manufacturer_info['name']);
            $this->view->assign('heading_title', $manufacturer_info['name']);
            $this->view->assign('text_sort', $this->language->get('text_sort'));

            $productFilter = [
                'sort'  => $sort,
                'order' => $order,
                'limit' => $limit,
                'start' => ($page - 1) * $limit,
            ];

            if (isset($this->request->get['category_id'])) {
                $productFilter['filter']['category_id'] = $this->request->get['category_id'];
            }
            if (isset($this->request->get['rating'])) {
                $productFilter['filter']['rating'] = $this->request->get['rating'];
            }

            $products_result = $this->model_catalog_product->getProductsByManufacturerId(
                $manufacturer_id,
                $productFilter
            );

            $product_total = $products_result
                ? ($products_result[0]['total_num_rows'] ?? $this->model_catalog_product->getTotalProductsByManufacturerId($manufacturer_id))
                : 0;

            if ($product_total) {
                $this->view->assign('button_add_to_cart', $this->language->get('button_add_to_cart'));
                $products = [];
                $product_ids = array_column($products_result, 'product_id');
                $products_info = $this->model_catalog_product->getProductsAllInfo($product_ids);
                $thumbnails = $product_ids
                    ? $resource->getMainThumbList(
                        'products',
                        $product_ids,
                        (int) $this->config->get('config_image_product_width'),
                        (int) $this->config->get('config_image_product_height')
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
                            '&product_id='.$result['product_id'],
                            '&encode'
                        );
                    } else {
                        if ($this->config->get('config_cart_ajax')) {
                            $add = '#';
                        } else {
                            $add = $this->html->getSecureURL($cart_rt, '&product_id='.$result['product_id'], '&encode');
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
                            'options'        => $products_info[$result['product_id']]['options'],
                            'special'        => $special,
                            'href'           => $this->html->getSEOURL(
                                'product/product',
                                '&product_id='.$result['product_id'],'&encode'
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
                $this->data['products'] = $products;

                if ($this->config->get('config_customer_price')) {
                    $display_price = true;
                } elseif ($this->customer->isLogged()) {
                    $display_price = true;
                } else {
                    $display_price = false;
                }
                $this->view->assign('display_price', $display_price);

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
                $pQuery['sort'] = $pQuery['sort'].'-'.$pQuery['order'];
                unset($pQuery['page'], $pQuery['order']);
                $pagination_url = $this->html->getSEOURL(
                    'product/manufacturer',
                    '&page={page}&'.http_build_query($pQuery,'',null,PHP_QUERY_RFC3986)
                );

                $rQuery = $httpQuery;
                unset($rQuery['sort']);
                $this->data['resort_url'] = $this->html->getSEOURL(
                    'product/manufacturer',
                    '&'.http_build_query($rQuery)
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
                $this->view->assign('sort', $sort);
                $this->view->assign('order', $order);
                $this->view->setTemplate('pages/product/manufacturer.tpl');
            } else {
                $this->document->setTitle($manufacturer_info['name']);
                $this->view->assign('heading_title', $manufacturer_info['name']);
                $this->view->assign('text_error', $this->language->get('text_empty'));
                $continue = $this->html->buildElement(
                    [
                        'type'  => 'button',
                        'name'  => 'continue_button',
                        'text'  => $this->language->get('button_continue'),
                        'style' => 'button',
                    ]
                );
                $this->view->assign('button_continue', $continue);
                $this->view->assign('continue', $this->html->getHomeURL());
                $this->view->setTemplate('pages/error/not_found.tpl');
            }
        } else {
            $this->document->addBreadcrumb(
                [
                    'href'      => $this->html->getSEOURL(
                        'product/manufacturer',
                        '&'.http_build_query($httpQuery),
                        '&encode'
                    ),
                    'text'      => $this->language->get('text_error'),
                    'separator' => $this->language->get('text_separator'),
                ]
            );

            $this->document->setTitle($this->language->get('text_error'));

            $this->view->assign('heading_title', $this->language->get('text_error'));
            $this->view->assign('text_error', $this->language->get('text_error'));
            $continue = $this->html->buildElement(
                [
                    'type'  => 'button',
                    'name'  => 'continue_button',
                    'text'  => $this->language->get('button_continue'),
                    'style' => 'button',
                ]
            );
            $this->view->assign('button_continue', $continue);
            $this->view->assign('continue', $this->html->getHomeURL());

            $this->view->setTemplate('pages/error/not_found.tpl');
        }

        $this->data['review_status'] = $this->config->get('display_reviews');

        $this->view->batchAssign($this->data);
        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}
