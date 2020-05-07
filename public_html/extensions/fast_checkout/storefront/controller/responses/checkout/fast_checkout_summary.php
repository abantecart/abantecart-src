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
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerResponsesCheckoutFastCheckoutSummary extends AController
{

    public $data = array();

    public function __construct($registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        //set sign that checkout is fast. See usage in hooks
        $this->session->data['fast-checkout'] = true;

        $this->allow_guest = $this->config->get('config_guest_checkout');

        $this->cart_key = $this->session->data['cart_key'];

        if (!isset($this->session->data['fast_checkout'][$this->cart_key])
            || $this->session->data['fast_checkout'][$this->cart_key]['cart'] !== $this->session->data['cart']) {
            $this->session->data['fast_checkout'][$this->cart_key]['cart'] = $this->session->data['cart'];
        }

        $cart_class_name = get_class($this->cart);
        $this->registry->set('cart', new $cart_class_name($this->registry, $this->session->data['fast_checkout'][$this->cart_key]));
    }

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('fast_checkout/fast_checkout');

        $this->buildCartProductDetails();

        $this->view->batchAssign($this->data);
        $this->response->setOutput($this->view->fetch('responses/checkout/fast_checkout_summary.tpl'));
        return null;

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function buildCartProductDetails()
    {
        $qty = 0;
        $resource = new AResource('image');

        foreach ($this->cart->getProducts() as $result) {
            $option_data = [];

            foreach ($result['option'] as $option) {
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

            $products[] = [
                'key'       => $result['key'],
                'name'      => $result['name'],
                'thumbnail' => $thumbnail,
                'option'    => $option_data,
                'quantity'  => $result['quantity'],
                'stock'     => $result['stock'],
                'price'     => $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'],
                    $this->config->get('config_tax'))),
                'href'      => $this->html->getSEOURL('product/product', '&product_id='.$result['product_id'], true),
            ];
        }
        $this->data['products'] = $products;

        $display_totals = $this->cart->buildTotalDisplay(true);

        $this->data['totals'] = $display_totals['total_data'];
        $this->data['total'] = $display_totals['total'];
        $this->data['total_string'] = $this->currency->format($display_totals['total']);
        if ($this->data['totals']) {
            return true;
        } else {
            return false;
        }

    }
}
