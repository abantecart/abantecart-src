<?php

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

        $this->loadModel('catalog/product');
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

        //reverse, so we show recent first
        $product_ids = array_reverse($product_ids);
        //set limit
        if ($this->config->get('viewed_products_limit')) {
            $product_ids = array_slice($product_ids, 0, $this->config->get('viewed_products_limit'));
        }



        $products_info = $this->model_catalog_product->getProductsAllInfo($product_ids);
        $products = $this->model_catalog_product->getProductsFromIDs($product_ids);
        $resource = new AResource('image');

        $width = $this->config->get('viewed_products_image_width');
        if (!has_value($width)) {
            $width = $this->config->get('config_image_product_width');
        }
        $height = $this->config->get('viewed_products_image_height');
        if (!has_value($height)) {
            $height = $this->config->get('config_image_product_height');
        }

        if (is_array($products)) {
            foreach ($products as $result) {
                $thumbnail = $resource->getMainThumb(
                    'products',
                    $result['product_id'],
                    $width,
                    $height,
                    true
                );

                $rating = $products_info[$result['product_id']]['rating'];

                $special = false;

                $discount = $products_info[$result['product_id']]['discount'];
                if ($discount) {
                    $price = $this->currency->format(
                        $this->tax->calculate(
                            $discount,
                            $result['tax_class_id'],
                            $this->config->get('config_tax')
                        )
                    );
                } else {
                    $price = $this->currency->format(
                        $this->tax->calculate(
                            $result['price'],
                            $result['tax_class_id'],
                            $this->config->get('config_tax')
                        )
                    );

                    $special = $products_info[$result['product_id']]['special'];

                    if ($special) {
                        $special = $this->currency->format(
                            $this->tax->calculate(
                                $special,
                                $result['tax_class_id'],
                                $this->config->get('config_tax')
                            )
                        );
                    }
                }

                $options = $products_info[$result['product_id']]['options'];
                if ($options) {
                    $add = $this->html->getSEOURL(
                        'product/product',
                        '&product_id='.$result['product_id'],
                        '&encode'
                    );
                } else {
                    if ($this->config->get('config_cart_ajax')) {
                        $add = '#';
                    } else {
                        $add = $this->html->getSecureURL(
                            'checkout/cart',
                            '&product_id='.$result['product_id'],
                            '&encode'
                        );
                    }
                }

                $this->data['products'][] = [
                    'product_id' => $result['product_id'],
                    'name' => $result['name'],
                    'model' => $result['model'],
                    'rating' => $rating,
                    'stars' => sprintf($this->language->get('text_stars'), $rating),
                    'price' => $price,
                    'call_to_order' => $result['call_to_order'],
                    'options' => $options,
                    'special' => $special,
                    'thumb' => $thumbnail,
                    'href' => $this->html->getSEOURL(
                        'product/product',
                        '&product_id='.$result['product_id'],
                        '&encode'
                    ),
                    'add' => $add,
                ];
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
<<<<<<< HEAD
}
=======
}
>>>>>>> 1.3.5
