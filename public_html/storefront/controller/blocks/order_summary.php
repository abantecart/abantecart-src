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

class ControllerBlocksOrderSummary extends AController
{
    public function main()
    {

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

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadModel('tool/seo_url');
        $this->view->assign('heading_title', $this->language->get('heading_title', 'blocks/order_summary'));

        $this->view->assign('view', $this->html->getSecureURL('checkout/cart'));

        $rt = $this->request->get['rt'];
        if($rt == 'checkout/success') {
            //do now show any info on success page
            return;
        }elseif (strpos($rt, 'checkout') !== false && $rt != 'checkout/cart') {
            $this->view->assign('checkout', '');
        } else {
            if ($this->cart->hasMinRequirement() && $this->cart->hasMaxRequirement()) {
                $this->view->assign('checkout', $this->html->getSecureURL('checkout/shipping'));
            }
        }

        $products = [];

        $qty = 0;
        $resource = new AResource('image');

        foreach ($this->cart->getProducts() + $this->cart->getVirtualProducts()  as $result) {
            $option_data = [];

            foreach ($result['option'] as $option) {
                $value = $option['value'];
                // hide binary value for checkbox
                if ($option['element_type'] == 'C' && in_array($value, [0, 1])) {
                    $value = '';
                }
                $title = '';
                // strip long textarea value
                if ($option['element_type'] == 'T') {
                    $title = strip_tags($value);
                    $title = str_replace('\r\n', "\n", $title);

                    $value = str_replace('\r\n', "\n", $value);
                    if (mb_strlen($value) > 64) {
                        $value = mb_substr($value, 0, 64).'...';
                    }
                }

                $option_data[] = [
                    'name'  => $option['name'],
                    'value' => $value,
                    'title' => $title,
                ];
            }

            $qty += $result['quantity'];

            //get main image
            $thumbnail = $resource->getMainImage(
                'products',
                $result['product_id'],
                $this->config->get('config_image_grid_width'),
                $this->config->get('config_image_grid_height')
            );

            $main_image = $resource->getResourceAllObjects(
                'product_option_value',
                $option['product_option_value_id'],
                $mSizes,
                1,
                false
            );

            if (!empty($main_image)) {
                $thumbnail['origin'] = $main_image['origin'];
                $thumbnail['title'] = $main_image['title'];
                $thumbnail['description'] = $main_image['description'];
                $thumbnail['thumb_html'] = $main_image['thumb_html'];
                $thumbnail['thumb_url'] = $main_image['thumb_url'];
                $thumbnail['main_url'] = $main_image['main_url'];
            }

            $products[] = [
                'key'       => $result['key'],
                'name'      => $result['name'],
                'option'    => $option_data,
                'thumbnail' => $thumbnail,
                'quantity'  => $result['quantity'],
                'stock'     => $result['stock'],
                'price'     => $this->currency->format(
                    $this->tax->calculate(
                        $result['price'] ?: $result['amount'],
                        $result['tax_class_id'],
                        $this->config->get('config_tax')
                    )
                ),
                'href'      => $result['product_id']
                        ? $this->html->getSEOURL(
                            'product/product',
                            '&product_id='.$result['product_id'],
                            true
                        )
                        : null,
            ];
        }

        $this->view->assign('products', $products);

        $this->view->assign('total_qty', $qty);

        $display_totals = $this->cart->buildTotalDisplay();

        $this->data['totals'] = $display_totals['total_data'];

        $this->view->batchAssign($this->data);
        $this->processTemplate();
        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

}