<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2026 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */

/** @noinspection PhpUnused */

/** @noinspection PhpMultipleClassDeclarationsInspection */
class ControllerPagesProductSpecial extends AController
{
    public function __construct(Registry $registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        $this->prepareProductListingParameters();
    }

    public function main()
    {
        $this->data['cart_rt'] = $this->config->get('embed_mode')
            ? 'r/checkout/cart/embed'
            : 'checkout/cart';

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('product/special');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->resetBreadcrumbs();
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getHomeURL(),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );

        $httpQuery = $this->prepareProductSortingParameters(['special' => true]);
        extract($httpQuery);
        unset($httpQuery['raw_sort']);
        $httpQuery = array_merge($httpQuery, (array) $this->data['additional_filters']);

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getNonSecureURL('product/special', '&' . http_build_query($httpQuery)),
                'text'      => $this->language->get('heading_title'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        /** @var ModelCatalogProduct $pMdl */
        $pMdl = $this->loadModel('catalog/product');
        $promotion = new APromotion();

        $product_total = $promotion->getTotalProductSpecials();

        if ($product_total) {
            $this->loadModel('catalog/review');
            $this->loadModel('tool/seo_url');
            $this->loadModel('tool/image');

            $this->data['button_add_to_cart'] = $this->language->get('button_add_to_cart');
            $results = $pMdl->getSpecialProducts(
                [
                    'sort'  => $sort,
                    'order' => $order,
                    'start' => ($page - 1) * $limit,
                    'limit' => $limit,
                ]
            );

            $product_ids = array_column($results, 'product_id');

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

            $stock_info = $pMdl->getProductsStockInfo($product_ids);
            foreach ($results as $result) {
                $productId = $result['product_id'];
                $thumbnail = $thumbnails[$productId];
                if ($this->config->get('display_reviews')) {
                    $rating = $this->model_catalog_review->getAverageRating($productId);
                } else {
                    $rating = false;
                }

                $special = false;
                $discount = $promotion->getProductDiscount($productId);
                if ($discount) {
                    $priceNum = $discount;
                } else {
                    $priceNum = $result['price'];
                    $special = $promotion->getProductSpecial($productId);
                    if ($special) {
                        $specialNum = $this->tax->calculate(
                            $special,
                            $result['tax_class_id'],
                            $this->config->get('config_tax')
                        );
                        $special = $this->currency->format($specialNum);
                    }
                }
                $priceNum = $this->tax->calculate($priceNum, $result['tax_class_id'], $this->config->get('config_tax'));
                $price = $this->currency->format($priceNum);

                $options = $pMdl->getProductOptions($productId);
                if ($options) {
                    $add = $this->html->getSEOURL('product/product', '&product_id=' . $productId, '&encode');
                } else {
                    if ($this->config->get('config_cart_ajax')) {
                        $add = '#';
                    } else {
                        $add = $this->html->getSecureURL(
                            'checkout/cart',
                            '&product_id=' . $productId,
                            '&encode'
                        );
                    }
                }

                //check for stock status, availability and config
                $track_stock = false;
                $in_stock = false;
                $no_stock_text = $this->language->get('text_out_of_stock');
                $total_quantity = 0;
                $stock_checkout = $result['stock_checkout'] === ''
                    ? $this->config->get('config_stock_checkout')
                    : $result['stock_checkout'];
                if ($stock_info[$productId]['subtract']) {
                    $track_stock = true;
                    $total_quantity = $pMdl->hasAnyStock($productId);
                    //we have stock or out-of-stock checkout is allowed
                    if ($total_quantity > 0 || $stock_checkout) {
                        $in_stock = true;
                    }
                }

                $this->data['products'][] = array_merge(
                    $result,
                    [
                        'product_id'     => $productId,
                        'name'           => $result['name'],
                        'model'          => $result['model'],
                        'rating'         => $rating,
                        'stars'          => $this->language->getAndReplace('text_stars', replaces: $rating),
                        'price'          => $price,
                        //todo: remove in the future as deprecated
                        'raw_price'      => $priceNum,
                        'price_num'      => $priceNum,
                        'call_to_order'  => $result['call_to_order'],
                        'options'        => $options,
                        'special'        => $special,
                        'special_num'    => $specialNum,
                        'thumb'          => $thumbnail,
                        'href'           => $this->html->getSEOURL(
                            'product/product',
                            '&product_id=' . $productId,
                            '&encode'
                        ),
                        'add'            => $add,
                        'description'    => html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'),
                        'blurb'          => $result['blurb'],
                        'track_stock'    => $track_stock,
                        'in_stock'       => $in_stock,
                        'no_stock_text'  => $no_stock_text,
                        'total_quantity' => $total_quantity,
                        'tax_class_id'   => $result['tax_class_id'],
                    ]
                );
            }

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
                    'value'   => $raw_sort . '-' . $order,
                ]
            );
            $this->data['sorting'] = $sorting;
            $this->data['url'] = $this->html->getURL('product/special');

            $pQuery = $httpQuery;
            $pQuery['sort'] = $raw_sort . '-' . $order;
            unset($pQuery['page'], $pQuery['order']);

            $pagination_url = $this->html->getURL(
                'product/special',
                '&page=--page--&' . http_build_query($pQuery, '', null, PHP_QUERY_RFC3986)
            );
            $rQuery = $httpQuery;
            unset($rQuery['sort']);
            $this->data['resort_url'] = $this->html->getSecureSEOURL(
                'product/special',
                '&' . http_build_query($rQuery)
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
            $this->data['review_status'] = $this->config->get('display_reviews');
            $this->view->batchAssign($this->data);
            $this->view->setTemplate('pages/product/special.tpl');
        } else {
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
        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}
