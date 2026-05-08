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
class ControllerPagesProductCollection extends AController
{
    public function __construct(Registry $registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        $this->loadLanguage('product/search');
        $this->prepareProductListingParameters();
    }

    public function main()
    {
        $request = $this->request->get;

        $this->data['cart_rt'] = $this->config->get('embed_mode')
            ? 'r/checkout/cart/embed'
            : 'checkout/cart';

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('product/category');
        $this->loadLanguage('product/collection');
        $this->document->resetBreadcrumbs();
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getHomeURL(),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );

        $this->loadModel('catalog/collection');
        $this->loadModel('tool/seo_url');

        $httpQuery = $this->prepareProductSortingParameters();
        extract($httpQuery);
        unset($httpQuery['raw_sort']);
        $httpQuery = array_merge($httpQuery, (array) $this->data['additional_filters']);

        $collectionId = (int) $request['collection_id'];
        $collectionInfo = $collectionId ? $this->model_catalog_collection->getById($collectionId) : [];
        if (!$collectionInfo) {
            $this->notFound();
            return;
        }

        $this->document->setTitle($collectionInfo['title']);
        $this->document->setKeywords($collectionInfo['meta_keywords']);
        $this->document->setDescription($collectionInfo['meta_description']);

        $httpQuery['collection_id'] = $collectionId;
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSEOURL('product/collection', '&' . http_build_query($httpQuery)),
                'text'      => $collectionInfo['title'],
                'separator' => $this->language->get('text_separator'),
            ]
        );

        $this->data['heading_title'] = $collectionInfo['title'];
        $this->data['text_sort'] = $this->language->get('text_sort');

        /** @var ModelCatalogProduct $pMdl */
        $pMdl = $this->loadModel('catalog/product');

        $start = ($page - 1) * $limit;
        $collectionProducts = [];
        if ($collectionInfo['conditions']) {
            $collectionProducts = $this->model_catalog_collection->getProducts(
                $collectionInfo['conditions'],
                $sort,
                $order,
                $start,
                $limit,
                $collectionId
            );
        }
        $resource = new AResource('image');

        if ($collectionProducts['items']) {
            $this->loadModel('catalog/review');
            $this->view->assign('button_add_to_cart', $this->language->get('button_add_to_cart'));
            $productIds = array_column((array) $collectionProducts['items'], 'product_id');
            $products = [];
            $productsInfo = $pMdl->getProductsAllInfo($productIds);
            $thumbnails = $productIds
                ? $resource->getMainThumbList(
                    'products',
                    $productIds,
                    $this->config->get('config_image_product_width'),
                    $this->config->get('config_image_product_height')
                )
                : [];
            $stockInfo = $pMdl->getProductsStockInfo($productIds);
            foreach ($collectionProducts['items'] as $result) {
                $productId = $result['product_id'];
                $thumbnail = $thumbnails[$productId];
                $rating = $productsInfo[$productId]['rating'];
                $special = false;
                $discount = $productsInfo[$productId]['discount'];

                if ($discount) {
                    $priceNum = $discount;
                } else {
                    $priceNum = $result['price'];
                    $special = $productsInfo[$productId]['special'];
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

                if ($productsInfo[$productId]['options']) {
                    $add = $this->html->getSEOURL(
                        'product/product',
                        '&product_id=' . $productId,
                        '&encode'
                    );
                } else {
                    $add = $this->config->get('config_cart_ajax')
                        ? '#'
                        : $this->html->getSecureURL($this->data['cart_rt'], '&product_id=' . $productId);
                }
                //check for stock status, availability and config
                $track_stock = false;
                $in_stock = false;
                $no_stock_text = $this->language->get('text_out_of_stock');
                $total_quantity = 0;
                $stock_checkout = $result['stock_checkout'] === ''
                    ? $this->config->get('config_stock_checkout')
                    : $result['stock_checkout'];
                if ($stockInfo[$productId]['subtract']) {
                    $track_stock = true;
                    $total_quantity = $pMdl->hasAnyStock($productId);
                    //we have stock or out-of-stock checkout is allowed
                    if ($total_quantity > 0 || $stock_checkout) {
                        $in_stock = true;
                    }
                }

                $productHttpQuery = [];
                if ($request['path']) {
                    $productHttpQuery['path'] = $request['path'];
                }
                $productHttpQuery['product_id'] = $productId;

                $products[] = array_merge(
                    $result,
                    [
                        'rating'         => $rating,
                        'stars'          => $this->language->getAndReplace('text_stars', replaces: $rating),
                        'thumb'          => $thumbnail,
                        'price'          => $price,
                        //todo: remove in the future as deprecated
                        'raw_price'      => $priceNum,
                        'price_num'      => $priceNum,
                        'options'        => $productsInfo[$productId]['options'],
                        'special'        => $special,
                        'special_num'    => $specialNum,
                        'href'           => $this->html->getSEOURL(
                            'product/product',
                            '&' . http_build_query($productHttpQuery),
                            true
                        ),
                        'add'            => $add,
                        'description'    => html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'),
                        'track_stock'    => $track_stock,
                        'in_stock'       => $in_stock,
                        'no_stock_text'  => $no_stock_text,
                        'total_quantity' => $total_quantity,
                    ]
                );
            }
            $this->data['products'] = $products;

            if ($this->config->get('config_customer_price') || $this->customer->isLogged()) {
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
            $this->data['url'] = $this->html->getURL('product/collection');

            $pQuery = $httpQuery;
            $pQuery['sort'] = $raw_sort . '-' . $pQuery['order'];
            unset($pQuery['page'], $pQuery['order']);

            $pagination_url = $this->html->getSEOURL(
                'product/collection',
                '&page=--page--&' . http_build_query($pQuery, '', null, PHP_QUERY_RFC3986)
            );
            $rQuery = $httpQuery;
            unset($rQuery['sort']);
            $this->data['resort_url'] = $this->html->getSecureSEOURL(
                'product/collection',
                '&' . http_build_query($rQuery)
            );

            $this->data['pagination_bootstrap'] = $this->html->buildElement(
                [
                    'type'       => 'Pagination',
                    'name'       => 'pagination',
                    'text'       => $this->language->get('text_pagination'),
                    'text_limit' => $this->language->get('text_per_page'),
                    'total'      => $collectionProducts['total'],
                    'page'       => $page,
                    'limit'      => $limit,
                    'url'        => $pagination_url,
                    'style'      => 'pagination',
                ]
            );

            $this->data['sort'] = $sort;
            $this->data['order'] = $order;
        } else {
            $this->document->setTitle($collectionInfo['title']);
            $this->document->setDescription($collectionInfo['meta_description']);
            $this->data['heading_title'] = $collectionInfo['title'];
            $this->data['text_error'] = $this->language->get('text_empty_collection');
            $this->data['button_continue'] = $this->language->get('button_continue');
            $this->data['continue'] = $this->html->getHomeURL();
            $this->data['categories'] = [];
            $this->data['products'] = [];
        }
        $this->view->setTemplate('pages/product/collection.tpl');

        $this->data['review_status'] = $this->config->get('display_reviews');
        $this->view->batchAssign($this->data);

        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function notFound()
    {
        $this->document->setTitle($this->language->get('text_empty_collection'));
        $this->view->assign('heading_title', $this->language->get('text_empty_collection'));
        $this->view->assign('text_error', $this->language->get('text_empty_collection'));
        $this->view->assign(
            'button_continue',
            $this->html->buildElement(
                [
                    'type'  => 'button',
                    'name'  => 'continue_button',
                    'text'  => $this->language->get('button_continue'),
                    'style' => 'button',
                ]
            )
        );
        $this->view->assign('continue', $this->html->getHomeURL());
        $this->view->setTemplate('pages/error/not_found.tpl');
        $this->processTemplate();
    }
}
