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
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerPagesCheckoutGuestStep3 extends AController
{
    private $error = [];

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        //is this an embed mode
        $cart_rt = 'checkout/cart';
        if ($this->config->get('embed_mode') == true) {
            $cart_rt = 'r/checkout/cart/embed';
        }

        if (!$this->cart->hasProducts() || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            redirect($this->html->getSecureURL($cart_rt));
        }

        //validate if order min/max are met
        if (!$this->cart->hasMinRequirement() || !$this->cart->hasMaxRequirement()) {
            redirect($this->html->getSecureURL($cart_rt));
        }

        if ($this->customer->isLogged()) {
            redirect($this->html->getSecureURL('checkout/shipping'));
        }

        if (!isset($this->session->data['guest'])) {
            redirect($this->html->getSecureURL('checkout/guest_step_1'));
        }

        if ($this->cart->hasShipping()) {
            if (!isset($this->session->data['shipping_method'])) {
                redirect($this->html->getSecureURL('checkout/guest_step_2'));
            }
        } else {
            unset(
                $this->session->data['shipping_method'],
                $this->session->data['shipping_methods']
            );
            $this->tax->setZone($this->session->data['country_id'], $this->session->data['zone_id']);
        }

        if (!isset($this->session->data['payment_method'])) {
            redirect($this->html->getSecureURL('checkout/guest_step_2'));
        }

        $this->loadLanguage('checkout/confirm');

        $this->document->setTitle($this->language->get('heading_title'));

        //build and save order
        $this->data = [];
        $order = new AOrder($this->registry);
        $this->data = $order->buildOrderData($this->session->data);

        $this->session->data['order_id'] = $order->saveOrder();

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
                'href'      => $this->html->getSecureURL($cart_rt),
                'text'      => $this->language->get('text_basket'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('checkout/guest_step_1'),
                'text'      => $this->language->get('text_guest_step_1'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('checkout/guest_step_2'),
                'text'      => $this->language->get('text_guest_step_2'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('checkout/guest_step_3'),
                'text'      => $this->language->get('text_confirm'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        $this->view->assign('error_warning', $this->error['warning']);
        $this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        if ($this->cart->hasShipping()) {
            if (isset($this->session->data['guest']['shipping'])) {
                $shipping_address = $this->session->data['guest']['shipping'];
            } else {
                $shipping_address = $this->session->data['guest'];
            }

            $this->data['shipping_address'] = $this->customer->getFormattedAddress(
                $shipping_address,
                $shipping_address['address_format']
            );
        } else {
            $this->data['shipping_address'] = '';
        }

        if (isset($this->session->data['shipping_method']['title'])) {
            $this->data['shipping_method'] = $this->session->data['shipping_method']['title'];
        } else {
            $this->data['shipping_method'] = '';
        }

        $this->data['checkout_shipping'] = $this->html->getSecureURL('checkout/guest_step_2');
        $this->data['checkout_shipping_edit'] = $this->html->getSecureURL('checkout/guest_step_2', '&mode=edit', true);

        $this->data['checkout_shipping_address'] = $this->html->getSecureURL('checkout/guest_step_1');

        $payment_address = $this->session->data['guest'];

        if ($payment_address) {
            $this->data['payment_address'] = $this->customer->getFormattedAddress(
                $payment_address,
                $payment_address['address_format']
            );
        } else {
            $this->data['payment_address'] = '';
        }

        if ($this->session->data['payment_method']['id'] != 'no_payment_required') {
            $this->data['payment_method'] = $this->session->data['payment_method']['title'];
        } else {
            $this->data['payment_method'] = '';
        }

        $this->data['checkout_payment'] = $this->html->getSecureURL('checkout/guest_step_2');
        $this->data['checkout_payment_edit'] = $this->html->getSecureURL('checkout/guest_step_2', '&mode=edit', true);
        $this->data['cart'] = $this->html->getSecureURL($cart_rt);
        $this->data['checkout_payment_address'] = $this->html->getSecureURL('checkout/guest_step_1');

        $this->loadModel('tool/seo_url');

        $product_ids = array_column($this->data['products'], 'product_id');

        //Format product data specific for confirmation page
        $resource = new AResource('image');
        $thumbnails = $product_ids
            ? $resource->getMainThumbList(
                'products',
                $product_ids,
                $this->config->get('config_image_cart_width'),
                $this->config->get('config_image_cart_height')
            )
            : [];

        $mSizes = [
            'main'  =>
                [
                    'width'  => $this->config->get('config_image_cart_width'),
                    'height' => $this->config->get('config_image_cart_height'),
                ],
            'thumb' => [
                'width'  => $this->config->get('config_image_cart_width'),
                'height' => $this->config->get('config_image_cart_height'),
            ],
        ];

        foreach ($this->data['products'] as $product) {
            if (isset($product['option']) && !empty($product['option'])) {
                foreach ($product['option'] as $option) {
                    $main_image = $resource->getResourceAllObjects(
                            'product_option_value',
                            $option['product_option_value_id'],
                            $mSizes,
                            1,
                            false
                        );
                    if (!empty($main_image)) {
                        $thumbnails[$product['key']] = $main_image;
                    }
                }
            }
        }

        $virtualProducts = $this->cart->getVirtualProducts();
        for ($i = 0; $i < sizeof($this->data['products']); $i++) {
            $product_id = $this->data['products'][$i]['product_id'];
            $thumbnail = null;
            if ($thumbnails[$this->data['products'][$i]['key']]) {
                $thumbnail = $thumbnails[$this->data['products'][$i]['key']];
            } elseif($thumbnails[$product_id]) {
                $thumbnail = $thumbnails[$product_id];
            }else{
                $thumbnail = $virtualProducts[$this->data['products'][$i]['key']]['thumb'];
            }
            $tax = $this->tax->calcTotalTaxAmount(
                $this->data['products'][$i]['total'],
                $this->data['products'][$i]['tax_class_id']
            );
            $price = $this->data['products'][$i]['price'];
            $quantity = $this->data['products'][$i]['quantity'];
            $this->data['products'][$i] = array_merge(
                $this->data['products'][$i],
                [
                    'thumb' => $thumbnail,
                    'tax'   => $this->currency->format($tax),
                    'price' => $this->currency->format($price),
                    'total' => $this->currency->format_total($price, $quantity),
                    'href'  => $this->html->getSEOURL('product/product', '&product_id='.$product_id, true),
                ]
            );
        }

        if ($this->config->get('config_checkout_id')) {
            $this->loadModel('catalog/content');
            $content_info = $this->model_catalog_content->getContent($this->config->get('config_checkout_id'));
            if ($content_info) {
                $this->data['text_accept_agree'] = $this->language->get('text_accept_agree');
                $this->data['text_accept_agree_href'] = $this->html->getURL(
                    'r/content/content/loadInfo',
                    '&content_id='.$this->config->get('config_checkout_id'),
                    true
                );
                $this->data['text_accept_agree_href_link'] = $content_info['title'];
            } else {
                $this->data['text_accept_agree'] = '';
            }
        } else {
            $this->data['text_accept_agree'] = '';
        }

        if ($this->config->get('coupon_status')) {
            $this->data['coupon_status'] = $this->config->get('coupon_status');
        }

        if ($this->session->data['payment_method']['id'] != 'no_payment_required') {
            $this->addChild('responses/extension/'.$this->session->data['payment_method']['id'], 'payment');
        } else {
            $this->addChild('responses/checkout/no_payment', 'payment');
        }

        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/checkout/confirm.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}
