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

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerBlocksViewedProducts extends AController
{
    public function main()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->language->load('blocks/viewed');
        $this->view->assign('heading_title', $this->language->get('text_recently_viewed'));

        /** @var ModelCatalogProduct $pMdl */
        $pMdl = $this->loadModel('catalog/product');
        $this->loadModel('catalog/review');
        $this->loadModel('tool/image');

        $this->data['button_add_to_cart'] = $this->language->get('button_add_to_cart');

        $this->data['products'] = [];
        $product_ids = [];
        if (is_array($this->session->data['viewed_products']) && has_value($this->session->data['viewed_products'])) {
            $product_ids = array_values(array_unique($this->session->data['viewed_products']));
        }

        foreach ($product_ids as $index => $result) {
            //skip current product
            if ($result == $this->request->get['product_id'] || empty($result) || !is_numeric($result)) {
                unset($product_ids[$index]);
            }
        }

        //reverse, so we show a recent first
        $product_ids = array_reverse($product_ids);
        //set limit
        if ($this->config->get('viewed_products_limit')) {
            $product_ids = array_slice($product_ids, 0, $this->config->get('viewed_products_limit'));
        }

        $products_info = $pMdl->getProductsAllInfo($product_ids);
        $products = $pMdl->getProductsFromIDs($product_ids);
        $resource = new AResource('image');

        $width = $this->config->get('viewed_products_image_width') ? : $this->config->get('config_image_product_width');
        $height =
            $this->config->get('viewed_products_image_height') ? : $this->config->get('config_image_product_height');

        $stock_info = $pMdl->getProductsStockInfo($product_ids);
        if (is_array($products)) {
            foreach ($products as $result) {
                $productId = $result['product_id'];
                $thumbnail = $resource->getMainThumb(
                    'products',
                    $productId,
                    $width,
                    $height,
                    true
                );

                $rating = $products_info[$productId]['rating'];
                $special = $specialNum = false;

                $discount = $products_info[$productId]['discount'];

                if ($discount) {
                    $priceNum = $discount;
                } else {
                    $priceNum = $result['price'];
                    $special = $products_info[$productId]['special'];
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

                $options = $products_info[$productId]['options'];
                if ($options) {
                    $add = $this->html->getSEOURL(
                        'product/product',
                        '&product_id=' . $productId,
                        '&encode'
                    );
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

                $this->data['products'][] =
                    array_merge(
                        $result,
                        [
                            'rating'         => $rating,
                            'stars'          => $this->language->getAndReplace('text_stars', replaces: $rating),
                            'price'          => $price,
                            'price_num'      => $priceNum,
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
                            'track_stock'    => $track_stock,
                            'in_stock'       => $in_stock,
                            'no_stock_text'  => $no_stock_text,
                            'total_quantity' => $total_quantity,
                        ]
                    );
            }
        }

        if ($this->config->get('config_customer_price')) {
            $this->data['display_price'] = true;
        } elseif ($this->customer->isLogged()) {
            $this->data['display_price'] = true;
        } else {
            $this->data['display_price'] = false;
        }
        $this->data['review_status'] = $this->config->get('enable_reviews');
        $this->data['imgW'] = $width;
        $this->data['imgH'] = $height;
        $this->view->batchAssign($this->data);
        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}