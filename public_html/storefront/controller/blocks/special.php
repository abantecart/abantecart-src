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
/** @noinspection PhpMultipleClassDeclarationsInspection */
/** @noinspection PhpUnused */

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerBlocksSpecial extends AController
{
    public function __construct($registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        $this->data['empty_render_text'] =
            'To view content of block you should be logged in and prices must be without taxes';
    }

    public function main()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('blocks/special');
        $this->data['heading_title'] = $this->language->get('heading_title', 'blocks/special');

        /** @var ModelCatalogProduct $pMdl */
        $pMdl = $this->loadModel('catalog/product');
        $this->loadModel('catalog/review');
        $this->loadModel('tool/seo_url');
        $this->loadModel('tool/image');
        $promotion = new APromotion();
        $this->data['button_add_to_cart'] = $this->language->get('button_add_to_cart');
        $this->data['products'] = [];
        $results = $pMdl->getSpecialProducts(
            [
                'sort'       => 'pd.name',
                'order'      => 'ASC',
                'start'      => 0,
                'limit'      => $this->config->get('config_special_limit'),
                'avg_rating' => $this->config->get('display_reviews'),
            ]
        );
        $product_ids = array_column($results, 'product_id');

        //get thumbnails by one pass
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
            $special = $specialNum = false;
            $discount = $result['discount_price'];

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
                $add = $this->html->getSEOURL('product/product', '&product_id='.$productId, '&encode');
            } else {
                if ($this->config->get('config_cart_ajax')) {
                    $add = '#';
                } else {
                    $add = $this->html->getSecureURL('checkout/cart', '&product_id='.$productId, '&encode');
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
            $rating = (int) $result['rating'];
            $this->data['products'][] =
                array_merge(
                    $result,
                    [
                        'rating'         => $rating,
                        'stars'          => $this->language->getAndReplace( 'text_stars', replaces: $rating ),
                        'price'          => $price,
                        'price_num'      => $priceNum,
                        'options'        => $options,
                        'special'        => $special,
                        'special_num'    => $specialNum,
                        'thumb'          => $thumbnail,
                        'href'           => $this->html->getSEOURL(
                            'product/product',
                            '&product_id='.$productId,
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

        if ($this->config->get('config_customer_price')) {
            $this->data['display_price'] = true;
        } elseif ($this->customer->isLogged()) {
            $this->data['display_price'] = true;
        } else {
            $this->data['display_price'] = false;
        }
        $this->data['review_status'] = $this->config->get('display_reviews');
        // framed needs to show frames for generic block.
        //If tpl used by listing block framed was set by listing block settings
        $this->data['block_framed'] = true;
        $this->view->batchAssign($this->data);
        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}
