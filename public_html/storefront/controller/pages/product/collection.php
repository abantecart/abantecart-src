<?php

class ControllerPagesProductCollection extends AController
{
    public $data = [];

    public function __construct(Registry $registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        $this->loadLanguage('product/search');
        $this->prepareProductListingParameters();
    }

    public function main()
    {
        $request = $this->request->get;

        //is this an embed mode
        $this->data['cart_rt'] = $this->config->get('embed_mode')
            ? 'r/checkout/cart/embed'
            : 'checkout/cart';

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('product/category');
        $this->loadLanguage('product/collection');
        $this->document->resetBreadcrumbs();
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getHomeURL(),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );

        $this->loadModel('catalog/collection');
        $this->loadModel('tool/seo_url');

        $httpQuery = $this->prepareProductSortingParameters();
        extract($httpQuery);
        unset($httpQuery['raw_sort']);

        $collectionId = (int)$request['collection_id'];

        $collectionInfo = [];
        if ($collectionId) {
            $collectionInfo = $this->model_catalog_collection->getById($collectionId);
        }

        if ($collectionInfo) {
            $this->document->setTitle($collectionInfo['title']);
            $this->document->setKeywords($collectionInfo['meta_keywords']);
            $this->document->setDescription($collectionInfo['meta_description']);

            $httpQuery['collection_id'] = $collectionId;
            $this->document->addBreadcrumb(
                [
                    'href'      => $this->html->getSEOURL('product/collection', '&' . http_build_query($httpQuery)),
                    'text'      => $collectionInfo['title'],
                    'separator' => $this->language->get('text_separator'),
                ]
            );

            $this->view->assign('heading_title', $collectionInfo['title']);
            $this->view->assign('text_sort', $this->language->get('text_sort'));

            $this->loadModel('catalog/product');

            $start = ($page - 1) * $limit;
            $collectionProducts = [];
            if ($collectionInfo['conditions']) {
                $collectionProducts = $this->model_catalog_collection->getProducts(
                    $collectionInfo['conditions'],
                    $sort,
                    $order,
                    $start,
                    $limit,
                    $collectionId
                );
            }
            $resource = new AResource('image');

            if (!empty($collectionProducts['items'])) {
                $this->loadModel('catalog/review');
                $this->view->assign('button_add_to_cart', $this->language->get('button_add_to_cart'));
                $productIds = array_column((array)$collectionProducts['items'], 'product_id');
                $products = [];
                $productsInfo = $this->model_catalog_product->getProductsAllInfo($productIds);
                $thumbnails = $productIds
                    ? $resource->getMainThumbList(
                        'products',
                        $productIds,
                        $this->config->get('config_image_product_width'),
                        $this->config->get('config_image_product_height')
                    )
                    : [];
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
                            '&product_id=' . $result['product_id'],
                            '&encode'
                        );
                    } else {
                        if ($this->config->get('config_cart_ajax')) {
                            $add = '#';
                        } else {
                            $add = $this->html->getSecureURL($this->data['cart_rt'], '&product_id=' . $result['product_id'], '&encode');
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

                    $productHttpQuery = [];
                    if ($request['path']) {
                        $productHttpQuery['path'] = $request['path'];
                    }
                    $productHttpQuery['product_id'] = $result['product_id'];

                    $products[] = [
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
                            '&' . http_build_query($productHttpQuery),
                            true
                        ),
                        'add'            => $add,
                        'description'    => html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'),
                        'track_stock'    => $track_stock,
                        'in_stock'       => $in_stock,
                        'no_stock_text'  => $no_stock_text,
                        'total_quantity' => $total_quantity,
                        'tax_class_id'   => $result['tax_class_id'],
                    ];
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

                $sort_options = $this->data['sorts'];
                $sorting = $this->html->buildElement(
                    [
                        'type'    => 'selectbox',
                        'name'    => 'sort',
                        'options' => $sort_options,
                        'value'   => $raw_sort . '-' . $order,
                    ]
                );
                $this->data['sorting'] = $sorting;
                $this->data['url'] = $this->html->getURL('product/collection');

                $pQuery = $httpQuery;
                $pQuery['sort'] = $pQuery['sort'] . '-' . $pQuery['order'];
                unset($pQuery['page'], $pQuery['order']);

                $pagination_url = $this->html->getURL(
                    'product/collection',
                    '&page={page}&' . http_build_query($pQuery, '', null, PHP_QUERY_RFC3986)
                );
                $rQuery = $httpQuery;
                unset($rQuery['sort']);
                $this->data['resort_url'] = $this->html->getSEOURL(
                    'product/collection',
                    '&' . http_build_query($rQuery)
                );

                $this->view->assign(
                    'pagination_bootstrap',
                    $this->html->buildElement(
                        [
                            'type'       => 'Pagination',
                            'name'       => 'pagination',
                            'text'       => $this->language->get('text_pagination'),
                            'text_limit' => $this->language->get('text_per_page'),
                            'total'      => $collectionProducts['total'],
                            'page'       => $page,
                            'limit'      => $limit,
                            'url'        => $pagination_url,
                            'style'      => 'pagination',
                        ]
                    )
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
                $this->view->assign('categories', []);
                $this->data['products'] = [];

                $this->view->setTemplate('pages/product/collection.tpl');
            }

            $this->data['review_status'] = $this->config->get('display_reviews');
            $this->view->batchAssign($this->data);
        } else {
            $url = '';
            if (isset($request['sort'])) {
                $url .= '&sort=' . $request['sort'];
            }

            if (isset($request['order'])) {
                $url .= '&order=' . $request['order'];
            }

            $this->document->addBreadcrumb(
                [
                    'href'      => $this->html->getSEOURL(
                        'product/collection',
                        '&collection_id=' . $collectionId . $url,
                        '&encode'
                    ),
                    'text'      => $this->language->get('text_error'),
                    'separator' => $this->language->get('text_separator'),
                ]
            );

            $this->document->setTitle($this->language->get('text_error'));
            $this->view->assign('heading_title', $this->language->get('text_error'));
            $this->view->assign('text_error', $this->language->get('text_error'));
            $this->view->assign(
                'button_continue',
                $this->html->buildElement(
                    [
                        'type'  => 'button',
                        'name'  => 'continue_button',
                        'text'  => $this->language->get('button_continue'),
                        'style' => 'button',
                    ]
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
