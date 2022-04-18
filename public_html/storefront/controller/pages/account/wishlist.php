<?php

/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2022 Belavier Commerce LLC

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

class ControllerPagesAccountWishlist extends AController
{

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->html->getSecureURL('account/wishlist');
            redirect($this->html->getSecureURL('account/login'));
        }

        $this->document->setTitle($this->language->get('heading_title'));

        $this->getWishList();

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        unset($this->session->data['success']);
    }

    private function getWishList()
    {
        $cart_rt = 'checkout/cart';
        //is this an embed mode	
        if ($this->config->get('embed_mode') == true) {
            $cart_rt = 'r/checkout/cart/embed';
        }

        $this->document->setTitle($this->language->get('heading_title'));

        $this->document->resetBreadcrumbs();

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getHomeURL(),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('account/account'),
                'text'      => $this->language->get('text_account'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('account/wishlist'),
                'text'      => $this->language->get('heading_title'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        $wishList = $this->customer->getWishList();
        if ($wishList) {
            $this->loadModel('tool/seo_url');
            $this->loadModel('catalog/product');

            //get thumbnails by one pass
            $resource = new AResource('image');
            $thumbnails = $resource->getMainThumbList(
                'products',
                array_keys($wishList),
                $this->config->get('config_image_cart_width'),
                $this->config->get('config_image_cart_width')
            );
            $promotion = new APromotion();
            $products = [];
            $stock_info = $this->model_catalog_product->getProductsStockInfo(array_keys($wishList));
            foreach ($wishList as $product_id => $timestamp) {
                $product_info = $this->model_catalog_product->getProduct($product_id);
                if (!$product_info) {
                    continue;
                }
                $thumbnail = $thumbnails[$product_id];
                $options = $this->model_catalog_product->getProductOptions($product_id);
                if ($options) {
                    $add = $this->html->getSEOURL('product/product', '&product_id='.$product_id, '&encode');
                } else {
                    $add = $this->html->getSecureURL($cart_rt, '&product_id='.$product_id, '&encode');
                }

                $special = false;
                $discount = $promotion->getProductDiscount($product_id);
                if ($discount) {
                    $price = $this->currency->format(
                        $this->tax->calculate(
                            $discount,
                            $product_info['tax_class_id'],
                            $this->config->get('config_tax')
                        )
                    );
                } else {
                    $price = $this->currency->format(
                        $this->tax->calculate(
                            $product_info['price'],
                            $product_info['tax_class_id'],
                            $this->config->get('config_tax')
                        )
                    );
                    $special = $promotion->getProductSpecial($product_info['product_id']);
                    if ($special) {
                        $special = $this->currency->format(
                            $this->tax->calculate(
                                $special,
                                $product_info['tax_class_id'],
                                $this->config->get('config_tax')
                            )
                        );
                    }
                }

                //check for stock status, availability and config
                $track_stock = false;
                $in_stock = false;
                $no_stock_text = $this->language->get('text_out_of_stock');
                $stock_checkout = $product_info['stock_checkout'] === ''
                    ? $this->config->get('config_stock_checkout')
                    : $product_info['stock_checkout'];
                if ($stock_info[$product_id]['subtract']) {
                    $track_stock = true;
                    $total_quantity = $this->model_catalog_product->hasAnyStock($product_id);
                    //we have stock or out of stock checkout is allowed
                    if ($total_quantity > 0 || $stock_checkout) {
                        $in_stock = true;
                    }
                }

                $products[] = array_merge(
                    $product_info,
                    [
                        'product_id'    => $product_id,
                        'name'          => $product_info['name'],
                        'model'         => $product_info['model'],
                        'thumb'         => $thumbnail,
                        'added'         => dateInt2Display($timestamp),
                        'price'         => $price,
                        'special'       => $special,
                        'href'          => $this->html->getSEOURL('product/product', '&product_id='.$product_id, true),
                        'call_to_order' => $product_info['call_to_order'],
                        'add'           => $add,
                        'in_stock'      => $in_stock,
                        'no_stock_text' => $no_stock_text,
                        'track_stock'   => $track_stock,
                    ]
                );
            }
            $this->data['products'] = $products;

            if (isset($this->session->data['redirect'])) {
                $this->data['continue'] = str_replace('&amp;', '&', $this->session->data['redirect']);
                unset($this->session->data['redirect']);
            } else {
                $this->data['continue'] = $this->html->getHomeURL();
            }

            $this->data['button_continue'] = HtmlElementFactory::create(
                [
                    'name'  => 'continue',
                    'type'  => 'button',
                    'text'  => $this->language->get('button_continue'),
                    'href'  => $this->data['continue'],
                ]
            );

            $this->view->assign('error', '');
            if ($this->session->data['error']) {
                $this->view->assign('error', $this->session->data['error']);
                unset($this->session->data['error']);
            }

            if ($this->config->get('config_customer_price')) {
                $display_price = true;
            } elseif ($this->customer->isLogged()) {
                $display_price = true;
            } else {
                $display_price = false;
            }
            $this->data['display_price'] = $display_price;

            $this->view->setTemplate('pages/account/wishlist.tpl');
        } else {
            $this->data['heading_title'] = $this->language->get('heading_title');
            $this->data['text_error'] = $this->language->get('text_empty_wishlist');

            $this->data['button_continue'] = HtmlElementFactory::create(
                [
                    'name'  => 'continue',
                    'type'  => 'button',
                    'text'  => $this->language->get('button_continue'),
                    'href'  => $this->html->getHomeURL(),
                    'style' => 'button',
                ]
            );

            $this->view->setTemplate('pages/error/not_found.tpl');
        }

        $this->data['cart'] = $this->html->getSecureURL($cart_rt);

        $this->data['button_cart'] = HtmlElementFactory::create(
            [
                'name'  => 'cart',
                'type'  => 'button',
                'text'  => $this->language->get('button_cart'),
                'href'  => $this->data['cart'],
                'style' => 'button',
            ]
        );

        $this->view->batchAssign($this->data);
        $this->processTemplate();
    }

}