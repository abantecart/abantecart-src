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
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * Class ControllerResponsesCheckoutFastCheckoutSummary
 *
 * @property AWeight $weight
 *
 */
class ControllerResponsesCheckoutFastCheckoutSummary extends AController
{

    public function __construct($registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        $cart_key = $registry->get('session')->data['fc']['cart_key'];
        //if cart key must be present in the FC session data anyway!
        if (!$cart_key) {
            return;
        }

        //swap cart in the registry to replace default cart data with FC cart data
        $cart_class_name = get_class($this->cart);
        $registry->set('cart', new $cart_class_name($registry, $this->session->data['fc']));

        //set tax zone for tax class based on session data
        $guestSessionData = $this->session->data['fc']['guest'];
        if ($guestSessionData) {
            //when payment address was set as address for taxes
            if ($this->config->get('config_tax_customer')) {
                $tax_country_id = $guestSessionData['country_id'];
                $tax_zone_id = $guestSessionData['zone_id'];
            } else {
                $tax_country_id = $guestSessionData['shipping']['country_id'] ?? $guestSessionData['country_id'];
                $tax_zone_id = $guestSessionData['shipping']['zone_id'] ?? $guestSessionData['zone_id'];
            }
        } else {
            if ($this->config->get('config_tax_customer')) {
                $tax_country_id = $this->session->data['fc']['country_id'];
                $tax_zone_id = $this->session->data['fc']['zone_id'];
            } else {
                $tax_country_id =
                    $this->session->data['fc']['shipping']['country_id'] ?? $this->session->data['fc']['country_id'];
                $tax_zone_id =
                    $this->session->data['fc']['shipping']['zone_id'] ?? $this->session->data['fc']['zone_id'];
            }
        }

        if ($tax_country_id) {
            $this->tax->setZone($tax_country_id, $tax_zone_id);
        }
    }

    public function main()
    {
        //is this an embed mode
        $this->data['cart_rt'] = $this->config->get('embed_mode')
            ? 'r/checkout/cart/embed'
            : 'checkout/cart';

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('checkout/fast_checkout');

        $this->buildCartProductDetails();

        $this->view->batchAssign($this->data);
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        $this->response->setOutput($this->view->fetch('responses/checkout/fast_checkout_summary.tpl'));
    }

    protected function buildCartProductDetails()
    {
        $resource = new AResource('image');
        $products = $main_image = [];
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

        foreach ($this->cart->getProducts() as $result) {
            $optionData = [];
            foreach ((array) $result['option'] as $option) {
                if ($option['element_type'] == 'H') {
                    continue;
                } //skip hidden options
                $value = $option['value'];
                // hide binary value for checkbox
                if ($option['element_type'] == 'C' && in_array($value, [0, 1], true)) {
                    $value = '';
                }
                $title = '';
                // strip long textarea value
                if ($option['element_type'] == 'T') {
                    $title = strip_tags($value);
                    $title = str_replace('\r\n', "\n", $title);

                    $value = str_replace('\r\n', "\n", $value);
                    if (mb_strlen($value) > 64) {
                        $value = mb_substr($value, 0, 64) . '...';
                    }
                }

                $optionData[] = [
                    'option_value_id' => $option['option_value_id'],
                    'name'            => $option['name'],
                    'value'           => $value,
                    'title'           => $title,
                ];
            }

            //get main image
            $thumbnail = $resource->getMainImage(
                'products',
                $result['product_id'],
                $this->config->get('config_image_grid_width'),
                $this->config->get('config_image_grid_height')
            );

            $optValId = array_column($optionData, 'product_option_value_id')[0];
            if ($optValId) {
                $main_image = $resource->getResourceAllObjects(
                    'product_option_value',
                    (int) $optValId,
                    $mSizes,
                    1,
                    false
                );
            }

            if ($main_image) {
                $thumbnail['origin'] = $main_image['origin'];
                $thumbnail['title'] = $main_image['title'];
                $thumbnail['description'] = $main_image['description'];
                $thumbnail['thumb_html'] = $main_image['thumb_html'];
                $thumbnail['thumb_url'] = $main_image['thumb_url'];
                $thumbnail['main_url'] = $main_image['main_url'];
            }

            $products[] = array_merge(
                $result,
                [
                    'thumbnail' => $thumbnail,
                    'option'    => $optionData,
                    'price'     => $this->currency->format(
                        $this->tax->calculate(
                            $result['price'],
                            $result['tax_class_id'],
                            $this->config->get('config_tax')
                        )
                    ),
                    'href'      => $this->html->getSEOURL(
                        'product/product',
                        '&product_id=' . $result['product_id'],
                        true
                    ),
                ]
            );
        }

        //check for virtual product such as gift certificate, account credit, e.t.c.
        $virtual_products = $this->cart->getVirtualProducts();
        if ($virtual_products) {
            foreach ($virtual_products as $virtual) {
                $products[] = array_merge(
                    $virtual,
                    [
                        'name'      => ($virtual['name'] ? : 'Virtual Product'),
                        'price'     => $this->currency->format(
                            $virtual['amount'],
                            $this->currency->getCode()
                        ),
                        'quantity'  => ($virtual['quantity'] ? : 1),
                        'option'    => [],
                        'weight'    => (float) $virtual['weight'],
                        'thumbnail' => $virtual['thumb'] ? : $virtual['thumbnail'],
                    ]
                );
                $this->data['items_total'] += ($virtual['quantity'] ? : 1)
                    * $this->currency->format(
                        $virtual['amount'],
                        $this->currency->getCode(),
                        '',
                        false
                    );
            }
        }

        $this->data['products'] = $products;
        $displayTotals = $this->cart->buildTotalDisplay(true);

        $this->data['totals'] = $displayTotals['total_data'];
        $this->data['total'] = $displayTotals['total'];
        $this->data['total_string'] = $this->currency->format($displayTotals['total']);
        if ($this->config->get('config_cart_weight')) {
            $this->data['cart_weight'] = $this->weight->format(
                $this->cart->getWeight(),
                $this->config->get('config_weight_class')
            );
        }
        return ($this->data['totals']);
    }
}
