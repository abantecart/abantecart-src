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

class ControllerResponsesEmbedJS extends AController
{

    public $data = array();

    /**
     * NOTE: main() is bootup method
     */
    public function main()
    {
        // if embedding disabled or enabled maintenance mode - return empty
        if (!$this->config->get('config_embed_status') || $this->config->get('config_maintenance')) {
            return null;
        }

        $this->extensions->hk_InitData($this, __FUNCTION__);

        //check is third-party cookie allowed
        if (!isset($this->request->cookie[SESSION_ID])) {
            $this->data['test_cookie'] = true;
        }

        if (HTTPS === true) {
            $this->view->assign('base', HTTPS_SERVER);
        } else {
            $this->view->assign('base', HTTP_SERVER);
        }

        $this->view->assign('store_name', $this->config->get('store_name'));

        $icon_rl = $this->config->get('config_icon');
        //see if we have a resource ID or path
        if (is_numeric($icon_rl)) {
            $resource = new AResource('image');
            $image_data = $resource->getResource($icon_rl);
            if (is_file(DIR_RESOURCE.$image_data['image'])) {
                $icon_rl = 'resources/'.$image_data['image'];
            } else {
                $icon_rl = $image_data['resource_code'];
            }
        } else {
            if (!is_file(DIR_RESOURCE.$icon_rl)) {
                $icon_rl = '';
            }
        }
        $this->view->assign('icon', $icon_rl);

        $this->data['logo'] = $this->config->get('config_icon');
        //see if we have a resource ID
        if (is_numeric($this->data['logo'])) {
            $resource = new AResource('image');
            $image_data = $resource->getResource($this->data['logo']);
            if (is_file(DIR_RESOURCE.$image_data['image'])) {
                $this->data['logo'] = 'resources/'.$image_data['image'];
            } else {
                $this->data['logo'] = $image_data['resource_code'];
            }
        }

        $this->data['homepage'] = HTTPS_SERVER;
        $this->data['abc_embed_test_cookie_url'] = $this->html->getURL('r/embed/js/testcookie', '&timestamp='.time());

        $this->loadLanguage('common/header');
        $this->data['account'] = $this->html->getSecureURL('r/account/account');
        $this->data['logged'] = $this->customer->isLogged();
        $this->data['login'] = $this->html->getSecureURL('r/account/login');
        $this->data['logout'] = $this->html->getURL('r/account/logout');
        $this->data['cart'] = $this->html->getURL('r/checkout/cart/embed');
        $this->data['checkout'] = $this->html->getSecureURL('r/checkout/shipping');

        $this->data['embed_click_action'] = $this->config->get('config_embed_click_action');

        $this->view->setTemplate('embed/js.tpl');
        $this->view->batchAssign($this->data);
        $this->setJsHttpHeaders();
        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    /**
     * Method fill data into embedded block with single product
     */
    public function product()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $product_id = (int)$this->request->get['product_id'];
        if (!$product_id) {
            return null;
        }

        $this->data['target'] = $this->request->get['target'];
        if (!$this->data['target']) {
            return null;
        }

        $this->loadModel('catalog/product');
        $this->loadLanguage('product/product');
        $product_info = $this->model_catalog_product->getProduct($product_id);

        //can not locate product? get out
        if (!$product_info) {
            return null;
        }
        //deal with quotes in name
        $product_info['name'] =
            htmlentities(html_entity_decode($product_info['name'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');

        $resource = new AResource('image');
        $product_info['thumbnail'] = $resource->getMainThumb(
            'products',
            $product_id,
            (int)$this->config->get('config_image_product_width'),
            (int)$this->config->get('config_image_product_height')
        );

        if ($product_info['final_price'] && $product_info['final_price'] != $product_info['price']) {
            $product_price = $this->tax->calculate(
                $product_info['final_price'],
                $product_info['tax_class_id'],
                (bool)$this->config->get('config_tax')
            );
            $product_info['special'] = $this->currency->format($product_price);
        }

        $product_price = $this->tax->calculate(
            $product_info['price'],
            $product_info['tax_class_id'],
            (bool)$this->config->get('config_tax')
        );
        $product_info['price'] = $this->currency->format($product_price);

        if ($this->config->get('config_customer_price')) {
            $display_price = true;
        } elseif ($this->customer->isLogged()) {
            $display_price = true;
        } else {
            $display_price = false;
        }
        $this->data['display_price'] = $display_price;

        $rt = $this->config->get('config_embed_click_action') == 'modal' ? 'r/product/product' : 'product/product';
        $this->data['product_details_url'] = $this->html->getURL($rt, '&product_id='.$product_id);

        //handle stock messages
        // if track stock is off. no messages needed.
        if ($this->model_catalog_product->isStockTrackable($product_id)) {
            $total_quantity = $this->model_catalog_product->hasAnyStock($product_id);
            $product_info['track_stock'] = true;
            //out of stock if no quantity and no stick checkout is disabled
            if ($total_quantity <= 0 && !$this->config->get('config_stock_checkout')) {
                $product_info['in_stock'] = false;
                //show out of stock message
                $product_info['stock'] = $product_info['stock_status'];
            } else {
                $product_info['in_stock'] = true;
                if ($this->config->get('config_stock_display')) {
                    $product_info['stock'] = $product_info['quantity'];
                } else {
                    $product_info['stock'] = $this->language->get('text_instock');
                }
            }

            //check if we need to disable product for no stock
            if ($this->config->get('config_nostock_autodisable') && $total_quantity <= 0) {
                return null;
            }
        }

        $product_options = $this->model_catalog_product->getProductOptions($product_id);

        if (!$product_options) {
            $product_info['button_addtocart'] = $this->html->buildElement(
                array(
                    'type' => 'button',
                    'name' => 'addtocart'.$product_id,
                    'text' => $this->language->get('button_add_to_cart'),

                    'attr' => 'data-product-id="'.$product_id.'" data-href = "'
                        .$this->html->getURL('r/embed/js/addtocart', '&product_id='.$product_id).'"',
                )
            );
        } else {
            $product_info['button_addtocart'] = $this->html->buildElement(
                array(
                    'type' => 'button',
                    'name' => 'addtocart'.$product_id,
                    'text' => $this->language->get('button_add_to_cart'),
                    'attr' => ' data-href="'.$this->data['product_details_url'].'"  data-id="'.$product_id
                        .'" data-html="true" data-target="#abc_embed_modal" data-toggle="abcmodal" ',
                )
            );
            $product_info['options'] = $product_options;
        }

        $product_info['quantity'] = $this->html->buildElement(
            array(
                'type'  => 'input',
                'name'  => 'quantity',
                'value' => $product_info['minimum'],
                'style' => 'short',
            )
        );

        if (!$this->config->get('display_reviews') && isset($product_info['rating'])) {
            unset($product_info['rating']);
        }

        $this->data['product'] = $product_info;

        $this->view->setTemplate('embed/js_product.tpl');

        $this->view->batchAssign($this->language->getASet('product/product'));
        $this->view->batchAssign($this->data);
        $this->setJsHttpHeaders();
        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    /**
     * Method fill data into embedded block with category or few categories
     */
    public function categories()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $category_id = (array)$this->request->get['category_id'];

        if (!$category_id) {
            return null;
        }

        $this->data['targets'] = (array)$this->request->get['target_id'];
        if (!$this->data['targets']) {
            return null;
        }

        $this->loadModel('catalog/category');
        $categories = $this->model_catalog_category->getCategoriesData(array(
            'filter_ids'    => $category_id,
            'subsql_filter' => ' c.status=1',
        ));

        //can not locate categories? get out
        if (!$categories) {
            return null;
        }

        $ids = array();
        foreach ($categories as $result) {
            $ids[] = (int)$result['category_id'];
        }

        //get thumbnails by one pass
        $resource = new AResource('image');
        $thumbnails = $resource->getMainThumbList(
            'categories',
            $ids,
            $this->config->get('config_image_category_width'),
            $this->config->get('config_image_category_height')
        );

        foreach ($categories as &$category) {
            //deal with quotes
            $category['name'] =
                htmlentities(html_entity_decode($category['name'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
            $category['thumbnail'] = $thumbnails[$category['category_id']];
            $rt =
                $this->config->get('config_embed_click_action') == 'modal' ? 'r/product/category' : 'product/category';
            $category['details_url'] = $this->html->getURL($rt, '&category_id='.$category['category_id']);

        }

        $this->data['categories'] = $categories;

        $this->view->setTemplate('embed/js_categories.tpl');

        $this->view->batchAssign($this->language->getASet('product/category'));
        $this->view->batchAssign($this->data);
        $this->setJsHttpHeaders();
        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    /**
     * Method fill data into embedded block with manufacturer or few manufacturers
     */
    public function manufacturers()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $manufacturer_id = (array)$this->request->get['manufacturer_id'];

        if (!$manufacturer_id) {
            return null;
        } else {
            foreach ($manufacturer_id as &$id) {
                $id = (int)$id;
            }
            unset($id);
        }

        $this->data['targets'] = (array)$this->request->get['target_id'];
        if (!$this->data['targets']) {
            return null;
        }

        $this->loadModel('catalog/manufacturer');
        $manufacturers = $this->model_catalog_manufacturer->getManufacturersData(array(
            'subsql_filter' => ' m.manufacturer_id IN ('.implode(',', $manufacturer_id).')',
        ));

        //can not locate manufacturers? get out
        if (!$manufacturers) {
            return null;
        }

        $ids = array();
        foreach ($manufacturers as $result) {
            $ids[] = (int)$result['manufacturer_id'];
        }

        //get thumbnails by one pass
        $resource = new AResource('image');
        $thumbnails = $resource->getMainThumbList(
            'manufacturers',
            $ids,
            $this->config->get('config_image_category_width'),
            $this->config->get('config_image_category_height')
        );

        foreach ($manufacturers as &$manufacturer) {
            //deal with quotes
            $manufacturer['name'] =
                htmlentities(html_entity_decode($manufacturer['name'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
            $manufacturer['thumbnail'] = $thumbnails[$manufacturer['manufacturer_id']];
            $rt = $this->config->get('config_embed_click_action')
            == 'modal' ? 'r/product/manufacturer' : 'product/manufacturer';
            $manufacturer['details_url'] =
                $this->html->getURL($rt, '&manufacturer_id='.$manufacturer['manufacturer_id']);

        }

        $this->data['manufacturers'] = $manufacturers;

        $this->view->setTemplate('embed/js_manufacturers.tpl');

        $this->view->batchAssign($this->language->getASet('product/manufacturer'));
        $this->view->batchAssign($this->data);
        $this->setJsHttpHeaders();
        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function testCookie()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->data['allowed'] = $this->request->cookie[SESSION_ID] ? true : false;
        $this->data['abc_token'] = session_id();

        $this->view->setTemplate('embed/js_cookie_check.tpl');
        $this->setJsHttpHeaders();
        $this->view->batchAssign($this->data);
        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function cart()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('blocks/cart');

        $this->data['cart_count'] = $this->cart->countProducts();
        if ($this->config->get('config_embed_click_action') != 'modal') {
            $this->data['cart_url'] = $this->html->getSecureURL('checkout/cart');
        } else {
            $this->data['cart_url'] = $this->html->getSecureURL('r/checkout/cart/embed');
        }

        $this->view->setTemplate('embed/js_cart.tpl');
        $this->setJsHttpHeaders();
        $this->view->batchAssign($this->data);
        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function addtocart()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadModel('catalog/product');
        $product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
        if ($product_info) {

            $qnt = (int)$this->request->get['quantity'];
            if ($qnt < $product_info['minimum']) {
                $qnt = (int)$product_info['minimum'];
            }
            $qnt = $qnt == 0 ? 1 : $qnt;
            $this->cart->add($this->request->get['product_id'], $qnt);
        }
        $this->setJsHttpHeaders();
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function setJsHttpHeaders()
    {
        $this->response->addHeader('Content-Type: text/javascript; charset=UTF-8');
    }

    public function collection()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $collection_id = (int)$this->request->get['collection_id'];

        if (!$collection_id) {
            return null;
        }

        $this->data['target'] = $this->request->get['target_id'];
        if (!$this->data['target']) {
            return null;
        }

        $this->loadModel('catalog/collection');
        $collection = $this->model_catalog_collection->getById($collection_id);

        //can not locate collection? get out
        if (!$collection) {
            return null;
        }

        $this->data['ajax_url'] = $this->html->getCatalogURL('r/product/collection', '&collection_id='.$collection_id);

        $collectionProducts=[];
        if ($collection['conditions']) {
            $sortOrder = $this->config->get('config_product_default_sort_order');
            list ($sort, $order) = explode('-', $sortOrder);
            $collectionProducts = $this->model_catalog_collection->getProducts($collection['conditions'], $sort ?: 'date_modified', $order ?: 'DESC', 0, 10000, $collection_id);
        }
        $resource = new AResource('image');

        if (!empty($collectionProducts['items'])) {
            $this->loadModel('catalog/review');
            $this->loadModel('catalog/product');

            $productIds = $products = [];

            foreach ($collectionProducts['items'] as $result) {
                $productIds[] = (int)$result['product_id'];
            }
            $productsInfo = $this->model_catalog_product->getProductsAllInfo($productIds);

            $thumbnails = $resource->getMainThumbList(
                'products',
                $productIds,
                $this->config->get('config_image_category_width'),
                $this->config->get('config_image_category_height')
            );
            $stockInfo = $this->model_catalog_product->getProductsStockInfo($productIds);
            foreach ($collectionProducts['items'] as $result) {
                $thumbnail = $thumbnails[$result['product_id']];
                $rating = $productsInfo[$result['product_id']]['rating'];
                $special = false;
                $discount = $productsInfo[$result['product_id']]['discount'];
                if ($discount) {
                    $price = $this->currency->format(
                        $this->tax->calculate(
                            $discount,
                            $result['tax_class_id'],
                            $this->config->get('config_tax'))
                    );
                } else {
                    $price = $this->currency->format(
                        $this->tax->calculate(
                            $result['price'],
                            $result['tax_class_id'],
                            $this->config->get('config_tax')
                        )
                    );
                    $special = $productsInfo[$result['product_id']]['special'];
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

                //check for stock status, availability and config
                $track_stock = false;
                $in_stock = false;
                $no_stock_text = $this->language->get('text_out_of_stock');
                $total_quantity = 0;
                $stock_checkout = $result['stock_checkout'] === ''
                    ? $this->config->get('config_stock_checkout')
                    : $result['stock_checkout'];
                if ($stockInfo[$result['product_id']]['subtract']) {
                    $track_stock = true;
                    $total_quantity = $this->model_catalog_product->hasAnyStock($result['product_id']);
                    //we have stock or out of stock checkout is allowed
                    if ($total_quantity > 0 || $stock_checkout) {
                        $in_stock = true;
                    }
                }

                $rt = $this->config->get('config_embed_click_action') == 'modal' ? 'r/product/product' : 'product/product';
                $product = array(
                    'product_id'          => $result['product_id'],
                    'name'                => $result['name'],
                    'blurb'               => $result['blurb'],
                    'model'               => $result['model'],
                    'thumb'               => $thumbnail,
                    'price'               => $price,
                    'raw_price'           => $result['price'],
                    'call_to_order'       => $result['call_to_order'],
                    'options'             => $productsInfo[$result['product_id']]['options'],
                    'special'             => $special,
                    'product_details_url' => $this->html->getURL($rt, '&product_id='.$result['product_id']),
                    'description'         => html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'),
                    'track_stock'         => $track_stock,
                    'in_stock'            => $in_stock,
                    'no_stock_text'       => $no_stock_text,
                    'total_quantity'      => $total_quantity,
                    'tax_class_id'        => $result['tax_class_id'],
                );
                if ($this->config->get('display_reviews')) {
                    $product['rating'] = $rating;
                    $product['stars'] = sprintf($this->language->get('text_stars'), $rating);
                }
                $products[] = $product;
            }
            $this->data['products'] = $products;

            if ($this->config->get('config_customer_price')) {
                $display_price = true;
            } elseif ($this->customer->isLogged()) {
                $display_price = true;
            } else {
                $display_price = false;
            }
            $this->view->assign('display_price', $display_price);
        }

        $this->view->setTemplate('embed/js_collection.tpl');

        $this->view->batchAssign($this->language->getASet('product/collection'));
        $this->view->batchAssign($this->data);
        $this->setJsHttpHeaders();
        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

}
