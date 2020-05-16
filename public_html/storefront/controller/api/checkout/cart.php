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

class ControllerApiCheckoutCart extends AControllerAPI
{
    public $data = array();
    public $error = array();

    public function post()
    {
        $request = $this->rest->getRequestParams();
        if (!$this->customer->isLoggedWithToken($request['token'])) {
            $this->rest->setResponseData(array('error' => 'Not logged in or Login attempt failed!'));
            $this->rest->sendResponse(401);
            return null;
        }

        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadModel('catalog/product');
        $product_id = $request['product_id'];

        //check if we add single or multiple products to cart
        if (isset($request['quantity']) || is_array($request['products'])) {
            if (isset($request['product_id']) && !is_array($request['quantity'])) {
                //add single product
                $this->_add_to_cart($request);
            } else {
                if (isset($request['product_id']) && is_array($request['quantity'])) {
                    //update quantities for products
                    foreach ($request['quantity'] as $key => $value) {
                        $this->cart->update($key, $value);
                    }
                } else {
                    if (is_array($request['products'])) {
                        //add bulk products
                        foreach ($request['products'] as $i => $product) {
                            $this->_add_to_cart($product);
                        }
                    }
                }
            }

            unset($this->session->data['shipping_methods']);
            unset($this->session->data['shipping_method']);
            unset($this->session->data['payment_methods']);
            unset($this->session->data['payment_method']);
        }

        //request to remove
        if (isset($request['remove']) && is_array($request['remove'])) {
            foreach (array_keys($request['remove']) as $key) {
                if ($key) {
                    $this->cart->remove($key);
                }
            }
        } else {
            if ($request['remove_all']) {
                $this->cart->clear();
            }
        }

        $this->_prepare_cart_data();

        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->rest->setResponseData($this->data);
        $this->rest->sendResponse(200);
    }

    public function delete()
    {
        $request = $this->rest->getRequestParams();
        $count = 0;
        if (isset($request['remove']) && is_array($request['remove'])) {
            foreach (array_keys($request['remove']) as $key) {
                if ($key) {
                    $this->cart->remove($key);
                    $count++;
                }
            }
        } else {
            if ($request['remove_all']) {
                $this->cart->clear();
            }
        }
        $this->rest->setResponseData(array('success' => "$count removed"));
        $this->rest->sendResponse(200);
        return null;
    }

    public function put()
    {
        return $this->post();
    }

    private function _add_to_cart($product)
    {
        if (isset($product['option'])) {
            $options = $product['option'];
        } else {
            $options = array();
        }
        if ($errors = $this->model_catalog_product->validateProductOptions($product['product_id'], $options)) {
            $this->rest->setResponseData(array('error' => implode(' ', $errors)));
            $this->rest->sendResponse(206);
        }
        $this->cart->add($product['product_id'], $product['quantity'], $options);
    }

    private function _prepare_cart_data()
    {
        if ($this->cart->hasProducts()) {
            $this->loadModel('tool/image');
            $products = array();
            $cart_products = $this->cart->getProducts();

            $product_ids = array();
            foreach ($cart_products as $result) {
                $product_ids[] = (int)$result['product_id'];
            }

            $resource = new AResource('image');
            $thumbnails = $resource->getMainThumbList(
                'products',
                $product_ids,
                $this->config->get('config_image_cart_width'),
                $this->config->get('config_image_cart_height')
            );

            if (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout')) {
                $this->data['error_warning'] = $this->language->get('error_stock');
            }

            foreach ($cart_products as $result) {
                $option_data = array();
                $thumbnail = $thumbnails[$result['product_id']];
                foreach ($result['option'] as $option) {
                    $option_data[] = array(
                        'name'  => $option['name'],
                        'value' => $option['value'],
                    );
                    // product image by option value
                    $mSizes = array(
                        'main'  =>
                            array(
                                'width' => $this->config->get('config_image_cart_width'),
                                'height' => $this->config->get('config_image_cart_height')
                            ),
                        'thumb' => array(
                            'width' =>  $this->config->get('config_image_cart_width'),
                            'height' => $this->config->get('config_image_cart_height')
                        ),
                    );
                    $main_image =
                        $resource->getResourceAllObjects('product_option_value', $option['product_option_value_id'], $mSizes, 1, false);
                    if (!empty($main_image)) {
                        $thumbnail['origin'] = $main_image['origin'];
                        $thumbnail['title'] = $main_image['title'];
                        $thumbnail['description'] = $main_image['description'];
                        $thumbnail['thumb_html'] = $main_image['thumb_html'];
                        $thumbnail['thumb_url'] = $main_image['thumb_url'];
                    }
                }

                $price_with_tax = $this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax'));
                $products[] = array(
                    'key'      => $result['key'],
                    'name'     => $result['name'],
                    'model'    => $result['model'],
                    'thumb'    => $thumbnail['thumb_url'],
                    'option'   => $option_data,
                    'quantity' => $result['quantity'],
                    'stock'    => $result['stock'],
                    'price'    => $this->currency->format($price_with_tax),
                    'total'    => $this->currency->format_total($price_with_tax, $result['quantity']),
                );
            }
            $this->data['products'] = $products;
            if ($this->config->get('config_cart_weight')) {
                $this->data['weight'] = $this->weight->format($this->cart->getWeight(), $this->config->get('config_weight_class'));
            } else {
                $this->data['weight'] = false;
            }
            $display_totals = $this->cart->buildTotalDisplay();
            $this->data['totals'] = $display_totals['total_data'];
        } else {
            //empty cart content
            $this->data['products'] = array();
            $this->data['totals'] = 0;
        }
    }
}