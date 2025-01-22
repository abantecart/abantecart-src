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

class ControllerApiProductProduct extends AControllerAPI
{

    public function get()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $request = $this->rest->getRequestParams();

        $product_id = $request['product_id'];

        if (empty($product_id) || !is_numeric($product_id)) {
            $this->rest->setResponseData(['Error' => 'Missing or incorrect format product ID']);
            $this->rest->sendResponse(200);
            return null;
        }

        //Load all the data from the model
        $this->loadModel('catalog/product');
        $product_info = $this->model_catalog_product->getProduct($product_id);
        if (count($product_info) <= 0) {
            $this->rest->setResponseData(['Error' => 'No product found']);
            $this->rest->sendResponse(200);
            return null;
        }
        //Add and edit data based on the more details
        $this->loadModel('tool/seo_url');
        $keyword = $this->model_tool_seo_url->getSEOKeyword(
            'product',
            'product_id',
            (int)$product_id,
            (int)$this->config->get('storefront_language_id')
        );
        if ($keyword) {
            $url = defined('HTTP_SERVER') ? HTTP_SERVER : 'http://' . REAL_HOST . get_url_path($_SERVER['PHP_SELF']);
            $product_info['seo_url'] = $url . '/' . $keyword;
        }

        //load resource library
        $resource = new AResource('image');
        $thumbnail = $resource->getMainThumb('products',
            $product_id,
            $this->config->get('config_image_thumb_width'),
            $this->config->get('config_image_thumb_height'));
        $product_info['thumbnail'] = $thumbnail['thumb_url'];

        $promotion = new APromotion();
        if ($this->config->get('config_customer_price') || $this->customer->isLogged()) {
            $product_price = $product_info['price'];
            $discount = $promotion->getProductDiscount($product_id);
            if ($discount) {
                $product_price = $discount;
                $product_info['price'] = $this->currency->format($this->tax->calculate($discount,
                    $product_info['tax_class_id'],
                    $this->config->get('config_tax')));
                $product_info['special'] = false;
            } else {
                $product_info['price'] = $this->currency->format($this->tax->calculate($product_info['price'],
                    $product_info['tax_class_id'],
                    $this->config->get('config_tax')));

                $special = $promotion->getProductSpecial($product_id);

                if ($special) {
                    $product_price = $special;
                    $product_info['special'] = $this->currency->format($this->tax->calculate($special,
                        $product_info['tax_class_id'],
                        $this->config->get('config_tax')));
                } else {
                    $product_info['special'] = false;
                }
            }
            $product_discounts = $promotion->getProductDiscounts($product_id);
            $discounts = [];
            if ($product_discounts) {
                foreach ($product_discounts as $discount) {
                    $discounts[] = [
                        'quantity' => $discount['quantity'],
                        'price'    => $this->currency->format(
                            $this->tax->calculate(
                                $discount['price'],
                                $product_info['tax_class_id'],
                                $this->config->get('config_tax')
                            )
                        ),
                    ];
                }
            }
            $product_info['discounts'] = $discounts;
            $product_info['product_price'] = $product_price;
        } else {
            //Do not Show price if setting and not logged in
            $product_info['product_price'] = '';
            $product_info['price'] = '';
        }

        if ($product_info['quantity'] <= 0) {
            $product_info['stock'] = $product_info['stock_status'];
        } else {
            if ($this->config->get('config_stock_display')) {
                $product_info['stock'] = $product_info['quantity'];
            } else {
                $product_info['stock'] = $this->language->get('text_instock');
            }
        }
        //hide quantity
        unset($product_info['quantity']);

        if (!$product_info['minimum']) {
            $product_info['minimum'] = 1;
        }
        $product_info['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
        $product_info['options'] = $this->model_catalog_product->getProductOptions($product_id);

        $this->loadModel('catalog/review');
        if ($this->isReviewAllowed($product_id)) {
            $average = $this->model_catalog_review->getAverageRating($product_id);
            $product_info['text_stars'] = sprintf($this->language->get('text_stars'), $average);
            $product_info['stars'] = sprintf($this->language->get('text_stars'), $average);
            $product_info['average'] = $average;
        }

        $this->model_catalog_product->updateViewed($product_id);
        $tags = [];
        $results = $this->model_catalog_product->getProductTags($product_id);
        if ($results) {
            foreach ($results as $result) {
                if ($result['tag']) {
                    $tags[] = ['tag' => $result['tag']];
                }
            }
        }
        $product_info['tags'] = $tags;
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        $this->rest->setResponseData($product_info);
        $this->rest->sendResponse(200);
    }
}