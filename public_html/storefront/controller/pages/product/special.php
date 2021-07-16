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

/**
 * Class ControllerPagesProductSpecial
 */
class ControllerPagesProductSpecial extends AController
{
    public function __construct(Registry $registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        $default_sorting = $this->config->get('config_product_default_sort_order');
        $sort_prefix = '';
        if (strpos($default_sorting, 'name-') === 0) {
            $sort_prefix = 'pd.';
        } elseif (strpos($default_sorting, 'price-') === 0) {
            $sort_prefix = 'p.';
        }
        $this->data['sorts'] = [
            $sort_prefix.$default_sorting => $this->language->get('text_default'),
            'pd.name-ASC'                 => $this->language->get('text_sorting_name_asc'),
            'pd.name-DESC'                => $this->language->get('text_sorting_name_desc'),
            'p.price-ASC'                 => $this->language->get('text_sorting_price_asc'),
            'p.price-DESC'                => $this->language->get('text_sorting_price_desc'),
            'rating-DESC'                 => $this->language->get('text_sorting_rating_desc'),
            'rating-ASC'                  => $this->language->get('text_sorting_rating_asc'),
            'date_modified-DESC'          => $this->language->get('text_sorting_date_desc'),
            'date_modified-ASC'           => $this->language->get('text_sorting_date_asc'),
        ];
    }

    /**
     * Check if HTML Cache is enabled for the method
     *
     * @return array - array of data keys to be used for cache key building
     */
    public static function main_cache_keys()
    {
        return ['page', 'limit', 'sort', 'order'];
    }

    public function main()
    {
        $request = $this->request->get;

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('product/special');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->resetBreadcrumbs();
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getHomeURL(),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );

        $url = '';
        if (isset($request['page'])) {
            $url .= '&page='.$request['page'];
        }

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getNonSecureURL('product/special', $url),
                'text'      => $this->language->get('heading_title'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        if (isset($request['page'])) {
            $page = $request['page'];
        } else {
            $page = 1;
        }

        if (isset($request['limit'])) {
            $limit = (int) $request['limit'];
            $limit = $limit > 50 ? 50 : $limit;
        } else {
            $limit = $this->config->get('config_catalog_limit');
        }

        $sorting_href = $request['sort'];
        if (!$sorting_href || !isset($this->data['sorts'][$request['sort']])) {
            $sorting_href = $this->config->get('config_product_default_sort_order');
        }

        list($sort, $order) = explode("-", $sorting_href);
        if ($sort == 'name') {
            $sort = 'pd.'.$sort;
        } elseif (in_array($sort, ['sort_order'])) {
            $sort = 'p.'.$sort;
        } elseif (in_array($sort, ['price'])) {
            $sort = 'ps.'.$sort;
        } elseif (in_array($sort, ['p.price'])) {
            $sort = 'ps.price';
        }

        $this->loadModel('catalog/product');
        $promotion = new APromotion();

        $product_total = $promotion->getTotalProductSpecials();

        if ($product_total) {
            $this->loadModel('catalog/review');
            $this->loadModel('tool/seo_url');
            $this->loadModel('tool/image');

            $this->data['button_add_to_cart'] = $this->language->get('button_add_to_cart');
            $results = $promotion->getProductSpecials(
                $sort,
                $order,
                ($page - 1) * $limit,
                $limit
            );

            $product_ids = array_column($results, 'product_id');

            //Format product data specific for confirmation page
            $resource = new AResource('image');
            $thumbnails = $product_ids
                ? $resource->getMainThumbList(
                    'products',
                    $product_ids,
                    $this->config->get('config_image_product_width'),
                    $this->config->get('config_image_product_height')
                )
                : [];

            $stock_info = $this->model_catalog_product->getProductsStockInfo($product_ids);
            foreach ($results as $result) {
                $thumbnail = $thumbnails[$result['product_id']];
                if ($this->config->get('display_reviews')) {
                    $rating = $this->model_catalog_review->getAverageRating($result['product_id']);
                } else {
                    $rating = false;
                }

                $special = false;
                $discount = $promotion->getProductDiscount($result['product_id']);
                if ($discount) {
                    $price = $this->currency->format(
                        $this->tax->calculate($discount, $result['tax_class_id'], $this->config->get('config_tax'))
                    );
                } else {
                    $price = $this->currency->format(
                        $this->tax->calculate(
                            $result['price'],
                            $result['tax_class_id'],
                            $this->config->get('config_tax')
                        )
                    );
                    $special = $promotion->getProductSpecial($result['product_id']);
                    if ($special) {
                        $special = $this->currency->format(
                            $this->tax->calculate($special, $result['tax_class_id'], $this->config->get('config_tax'))
                        );
                    }
                }

                $options = $this->model_catalog_product->getProductOptions($result['product_id']);
                if ($options) {
                    $add = $this->html->getSEOURL('product/product', '&product_id='.$result['product_id'], '&encode');
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

                //check for stock status, availability and config
                $track_stock = false;
                $in_stock = false;
                $no_stock_text = $this->language->get('text_out_of_stock');
                $total_quantity = 0;
                $stock_checkout = $result['stock_checkout'] === ''
                    ? $this->config->get('config_stock_checkout')
                    : $result['stock_checkout'];
                if ($stock_info[$result['product_id']]['subtract']) {
                    $track_stock = true;
                    $total_quantity = $this->model_catalog_product->hasAnyStock($result['product_id']);
                    //we have stock or out of stock checkout is allowed
                    if ($total_quantity > 0 || $stock_checkout) {
                        $in_stock = true;
                    }
                }

                $this->data['products'][] = [
                    'product_id'     => $result['product_id'],
                    'name'           => $result['name'],
                    'model'          => $result['model'],
                    'rating'         => $rating,
                    'stars'          => sprintf($this->language->get('text_stars'), $rating),
                    'price'          => $price,
                    'raw_price'      => $result['price'],
                    'call_to_order'  => $result['call_to_order'],
                    'options'        => $options,
                    'special'        => $special,
                    'thumb'          => $thumbnail,
                    'href'           => $this->html->getSEOURL(
                        'product/product',
                        '&product_id='.$result['product_id'],
                        '&encode'
                    ),
                    'add'            => $add,
                    'description'    => html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'),
                    'blurb'          => $result['blurb'],
                    'track_stock'    => $track_stock,
                    'in_stock'       => $in_stock,
                    'no_stock_text'  => $no_stock_text,
                    'total_quantity' => $total_quantity,
                    'tax_class_id'   => $result['tax_class_id'],
                ];
            }

            if ($this->config->get('config_customer_price')) {
                $display_price = true;
            } elseif ($this->customer->isLogged()) {
                $display_price = true;
            } else {
                $display_price = false;
            }
            $this->data['display_price'] = $display_price;

            $sort_options = [];
            foreach ($this->data['sorts'] as $item => $text) {
                $sort_options[$item] = $text;
            }
            $sorting = $this->html->buildElement(
                [
                    'type'    => 'selectbox',
                    'name'    => 'sort',
                    'options' => $sort_options,
                    'value'   => $sorting_href,
                ]
            );

            $this->view->assign('sorting', $sorting);
            $this->view->assign('url', $this->html->getURL('product/special'));

            $pagination_url = $this->html->getURL(
                'product/special',
                '&sort='.$sorting_href.'&page={page}'.'&limit='.$limit,
                '&encode'
            );

            $this->data['pagination_bootstrap'] = $this->html->buildElement(
                [
                    'type'       => 'Pagination',
                    'name'       => 'pagination',
                    'text'       => $this->language->get('text_pagination'),
                    'text_limit' => $this->language->get('text_per_page'),
                    'total'      => $product_total,
                    'page'       => $page,
                    'limit'      => $limit,
                    'url'        => $pagination_url,
                    'style'      => 'pagination',
                ]
            );

            $this->data['sort'] = $sort;
            $this->data['order'] = $order;
            $this->data['review_status'] = $this->config->get('display_reviews');
            $this->view->batchAssign($this->data);
            $this->view->setTemplate('pages/product/special.tpl');
        } else {
            $this->view->assign('text_error', $this->language->get('text_empty'));
            $continue = $this->html->buildElement(
                [
                    'type'  => 'button',
                    'name'  => 'continue_button',
                    'text'  => $this->language->get('button_continue'),
                    'style' => 'button',
                ]
            );
            $this->view->assign('button_continue', $continue);
            $this->view->assign('continue', $this->html->getHomeURL());
            $this->view->setTemplate('pages/error/not_found.tpl');
        }
        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}
