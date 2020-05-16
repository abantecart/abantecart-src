<?php

class ControllerPagesProductCollection extends AController
{
    public $data = [];

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
        $this->data['sorts'] = array(
            $sort_prefix.$default_sorting => $this->language->get('text_default'),
            'pd.name-ASC'                 => $this->language->get('text_sorting_name_asc'),
            'pd.name-DESC'                => $this->language->get('text_sorting_name_desc'),
            'p.price-ASC'                 => $this->language->get('text_sorting_price_asc'),
            'p.price-DESC'                => $this->language->get('text_sorting_price_desc'),
            'rating-DESC'                 => $this->language->get('text_sorting_rating_desc'),
            'rating-ASC'                  => $this->language->get('text_sorting_rating_asc'),
            'date_modified-DESC'          => $this->language->get('text_sorting_date_desc'),
            'date_modified-ASC'           => $this->language->get('text_sorting_date_asc'),
        );
    }

    /**
     * Check if HTML Cache is enabled for the method
     *
     * @return array - array of data keys to be used for cache key building
     */
    public static function main_cache_keys()
    {
        return array('path', 'collection_id', 'page', 'limit', 'sort', 'order');
    }

    public function main()
    {
        $request = $this->request->get;

        //is this an embed mode
        if ($this->config->get('embed_mode') == true) {
            $cart_rt = 'r/checkout/cart/embed';
        } else {
            $cart_rt = 'checkout/cart';
        }

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('product/category');
        $this->loadLanguage('product/collection');
        $this->document->resetBreadcrumbs();
        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getHomeURL(),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
        ));

        $this->loadModel('catalog/collection');
        $this->loadModel('tool/seo_url');

        $collectionId = (int)$request['collection_id'];

        $collectionInfo = [];
        if ($collectionId) {
            $collectionInfo = $this->model_catalog_collection->getById($collectionId);
        }

        if ($collectionInfo) {
            $this->document->setTitle($collectionInfo['title']);
            $this->document->setKeywords($collectionInfo['meta_keywords']);
            $this->document->setDescription($collectionInfo['meta_description']);

            $this->document->addBreadcrumb(
                array(
                    'href'      => $this->html->getSEOURL(
                        'product/collection',
                        '&collection_id='.$request['collection_id'],
                        '&encode'
                    ),
                    'text'      => $collectionInfo['title'],
                    'separator' => $this->language->get('text_separator'),
                )
            );

            $this->view->assign('heading_title', $collectionInfo['title']);
            $this->view->assign('text_sort', $this->language->get('text_sort'));

            if (isset($request['page'])) {
                $page = $request['page'];
            } else {
                $page = 1;
            }
            if (isset($request['limit'])) {
                $limit = (int)$request['limit'];
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
            } elseif (in_array($sort, array('sort_order', 'price'))) {
                $sort = 'p.'.$sort;
            }

            $this->loadModel('catalog/product');

            $start = ($page - 1) * $limit;
            $collectionProducts = [];
            if ($collectionInfo['conditions']) {
                $collectionProducts = $this->model_catalog_collection->getProducts($collectionInfo['conditions'], $sort, $order, $start, $limit, $collectionId);
            }
            $resource = new AResource('image');

            if (!empty($collectionProducts['items'])) {
                $this->loadModel('catalog/review');
                $this->view->assign('button_add_to_cart', $this->language->get('button_add_to_cart'));
                $product_ids = $products = [];

                foreach ($collectionProducts['items'] as $result) {
                    $productIds[] = (int)$result['product_id'];
                }
                $productsInfo = $this->model_catalog_product->getProductsAllInfo($productIds);

                $thumbnails = $resource->getMainThumbList(
                    'products',
                    $productIds,
                    $this->config->get('config_image_product_width'),
                    $this->config->get('config_image_product_height')
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

                    if ($productsInfo[$result['product_id']]['options']) {
                        $add = $this->html->getSEOURL(
                            'product/product',
                            '&product_id='.$result['product_id'],
                            '&encode'
                        );
                    } else {
                        if ($this->config->get('config_cart_ajax')) {
                            $add = '#';
                        } else {
                            $add = $this->html->getSecureURL($cart_rt, '&product_id='.$result['product_id'], '&encode');
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

                    $products[] = array(
                        'product_id'     => $result['product_id'],
                        'name'           => $result['name'],
                        'blurb'          => $result['blurb'],
                        'model'          => $result['model'],
                        'rating'         => $rating,
                        'stars'          => sprintf($this->language->get('text_stars'), $rating),
                        'thumb'          => $thumbnail,
                        'price'          => $price,
                        'raw_price'      => $result['price'],
                        'call_to_order'  => $result['call_to_order'],
                        'options'        => $productsInfo[$result['product_id']]['options'],
                        'special'        => $special,
                        'href'           => $this->html->getSEOURL(
                            'product/product',
                            '&path='.$request['path'].'&product_id='.$result['product_id'],
                            '&encode'
                        ),
                        'add'            => $add,
                        'description'    => html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'),
                        'track_stock'    => $track_stock,
                        'in_stock'       => $in_stock,
                        'no_stock_text'  => $no_stock_text,
                        'total_quantity' => $total_quantity,
                        'tax_class_id'   => $result['tax_class_id'],
                    );
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

                $sort_options = array();
                foreach ($this->data['sorts'] as $item => $text) {
                    $sort_options[$item] = $text;
                }
                $sorting = $this->html->buildSelectbox(array(
                    'name'    => 'sort',
                    'options' => $sort_options,
                    'value'   => $sort.'-'.$order,
                ));
                $this->view->assign('sorting', $sorting);
                $this->view->assign('url', $this->html->getSEOURL('product/collection', '&collection_id='.$request['collection_id']));

                $pagination_url = $this->html->getSEOURL(
                    'product/collection',
                    '&collection_id='.$request['collection_id'].'&sort='.$sorting_href.'&page={page}'.'&limit='.$limit,
                    '&encode'
                );

                $this->view->assign(
                    'pagination_bootstrap',
                    $this->html->buildElement(array(
                        'type'       => 'Pagination',
                        'name'       => 'pagination',
                        'text'       => $this->language->get('text_pagination'),
                        'text_limit' => $this->language->get('text_per_page'),
                        'total'      => $collectionProducts['total'],
                        'page'       => $page,
                        'limit'      => $limit,
                        'url'        => $pagination_url,
                        'style'      => 'pagination',
                    ))
                );

                $this->view->assign('sort', $sort);
                $this->view->assign('order', $order);
                $this->view->setTemplate('pages/product/collection.tpl');
            } else {
                $this->document->setTitle($collectionInfo['title']);
                $this->document->setDescription($collectionInfo['meta_description']);
                $this->view->assign('heading_title', $collectionInfo['title']);
                $this->view->assign('text_error', $this->language->get('text_empty_collection'));
                $this->view->assign('button_continue', $this->language->get('button_continue'));
                $this->view->assign('continue', $this->html->getHomeURL());
                $this->view->assign('categories', array());
                $this->data['products'] = array();

                $this->view->setTemplate('pages/product/collection.tpl');
            }

            $this->data['review_status'] = $this->config->get('display_reviews');
            $this->view->batchAssign($this->data);
        } else {
            $url = '';
            if (isset($request['sort'])) {
                $url .= '&sort='.$request['sort'];
            }

            if (isset($request['order'])) {
                $url .= '&order='.$request['order'];
            }


            $this->document->addBreadcrumb(
                array(
                    'href'      => $this->html->getSEOURL(
                        'product/collection',
                        '&collection_id='.$collectionId.$url,
                        '&encode'
                    ),
                    'text'      => $this->language->get('text_error'),
                    'separator' => $this->language->get('text_separator'),
                )
            );

            $this->document->setTitle($this->language->get('text_error'));
            $this->view->assign('heading_title', $this->language->get('text_error'));
            $this->view->assign('text_error', $this->language->get('text_error'));
            $this->view->assign(
                'button_continue',
                $this->html->buildElement(
                    array(
                        'type'  => 'button',
                        'name'  => 'continue_button',
                        'text'  => $this->language->get('button_continue'),
                        'style' => 'button',
                    )
                )
            );
            $this->view->assign('continue', $this->html->getHomeURL());
            $this->view->setTemplate('pages/error/not_found.tpl');
        }

        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}
