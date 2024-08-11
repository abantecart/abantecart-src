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

class ControllerPagesProductProduct extends AController
{
    protected $routes = [];

    protected function _init()
    {
        //is this an embed mode
        if ($this->config->get('embed_mode')) {
            $this->routes['cart_rt'] = 'r/checkout/cart/embed';
        } else {
            $this->routes['cart_rt'] = 'checkout/cart';
        }
        $this->loadLanguage('checkout/fast_checkout');
    }

    public function main()
    {
        $this->loadModel('catalog/product');
        $request = $this->request->get;
        $this->_init();

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        //key of product from cart
        $key = [];
        if (has_value($request['key'])) {
            $key = explode(':', $request['key']);
            $product_id = (int) $key[0];
        } elseif (has_value($request['product_id'])) {
            $product_id = (int) $request['product_id'];
        } else {
            $product_id = 0;
        }

        $product_info = $this->model_catalog_product->getProduct($product_id);
        //can not locate product? get out
        if (!$product_info) {
            $this->_product_not_found($product_id);
            return;
        }


        $this->document->resetBreadcrumbs();
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getHomeURL(),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );

        $this->loadModel('tool/seo_url');
        $this->loadModel('catalog/category');
        if(!isset($request['path'])){
            $prodCategories = $this->model_catalog_product->getProductCategories($product_id);
            if($prodCategories){
                $request['path'] = $this->model_catalog_category->buildPath(current($prodCategories));
            }

        }

        if (isset($request['path'])) {
            $path = '';
            foreach (explode('_', $request['path']) as $path_id) {
                $category_info = $this->model_catalog_category->getCategory($path_id);
                if (!$path) {
                    $path = $path_id;
                } else {
                    $path .= '_'.$path_id;
                }
                if ($category_info) {
                    $this->document->addBreadcrumb(
                        [
                            'href'      => $this->html->getSEOURL('product/category', '&path='.$path, '&encode'),
                            'text'      => $category_info['name'],
                            'separator' => $this->language->get('text_separator'),
                        ]
                    );
                }
            }
        }

        $this->loadModel('catalog/manufacturer');
        if (isset($request['manufacturer_id'])) {
            $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($request['manufacturer_id']);
            if ($manufacturer_info) {
                $this->document->addBreadcrumb(
                    [
                        'href'      => $this->html->getSEOURL(
                            'product/manufacturer',
                            '&manufacturer_id='.$request['manufacturer_id'], '&encode'
                        ),
                        'text'      => $manufacturer_info['name'],
                        'separator' => $this->language->get('text_separator'),
                    ]
                );
            }
        }

        if (isset($request['keyword'])) {
            $httpQuery = ['keyword' => $request['keyword']];
            if (isset($request['category_id'])) {
                $httpQuery['category_id'] = $request['category_id'];
            }
            if (isset($request['description'])) {
                $httpQuery['description'] = $request['description'];
            }
            $this->document->addBreadcrumb(
                [
                    'href'      => $this->html->getURL( 'product/search', '&'.http_build_query($httpQuery), '&encode'),
                    'text'      => $this->language->get('text_search'),
                    'separator' => $this->language->get('text_separator'),
                ]
            );
        }

        $urls = [
            'is_group_option' => $this->html->getURL(
                'r/product/product/is_group_option',
                '&product_id='.$product_id,
                '&encode'
            ),
        ];
        $this->view->assign('urls', $urls);

        $promotion = new APromotion();
        $url = $this->_build_url_params($this->request->get);

        $this->view->assign('error', '');
        if (isset($this->session->data['error'])) {
            $this->view->assign('error', $this->session->data['error']);
            unset($this->session->data['error']);
        }

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSEOURL('product/product', $url.'&product_id='.$product_id, '&encode'),
                'text'      => $product_info['name'],
                'separator' => $this->language->get('text_separator'),
            ]
        );
        $this->document->setTitle($product_info['name']);
        $this->document->setKeywords($product_info['meta_keywords']);
        $this->document->setDescription($product_info['meta_description']);

        $this->data['product_url'] = $this->html->getSEOURL('product/product', $url.'&product_id='.$product_id );
        $this->data['heading_title'] = $product_info['name'];
        $this->data['blurb'] = $product_info['blurb'];
        $this->data['minimum'] = $product_info['minimum'];
        $this->data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
        $this->data['maximum'] = $product_info['maximum'];
        $this->data['text_maximum'] = sprintf($this->language->get('text_maximum'), $product_info['maximum']);
        $this->data['option_resources_url'] = $this->html->getURL('r/product/product/get_option_resources');
        $this->data['calc_total_url'] = $this->html->getURL('r/product/product/calculateTotal');
        $this->data['product_review_url'] = $this->html->getURL('product/review/review', '&product_id='.$product_id);
        $this->data['product_review_write_url'] = $this->html->getURL(
                'product/review/write',
                '&product_id='.$product_id
        );
        $this->data['product_wishlist_add_url'] = $this->html->getURL(
            'product/wishlist/add',
            '&product_id='.$product_id
        );
        $this->data['product_wishlist_remove_url'] = $this->html->getURL(
            'product/wishlist/remove',
            '&product_id='.$product_id
        );
        $this->data['captcha_url'] = $this->html->getURL('common/captcha');
        $this->data['update_view_count_url'] = $this->html->getURL(
            'common/view_count/product',
            '&product_id='.$product_id
        );

        $this->loadModel('catalog/review');
        $this->data['display_reviews'] = $this->config->get('display_reviews');
        if ($this->data['display_reviews']) {
            $this->data['tab_review'] = sprintf(
                $this->language->get('tab_review'),
                $this->model_catalog_review->getTotalReviewsByProductId($product_id)
            );
        } else {
            $this->data['tab_review'] = $this->language->get('tab_review_empty');
        }
        $this->data['total_reviews']= $this->model_catalog_review->getTotalReviewsByProductId($product_id);
        $this->data['review_percentage_translate'] = $this->language->get('percentage_review');
        $this->data['feedback_customer_title']=$this->language->get('feedback_customer_title');
        $this->data['review_title']=$this->language->get('review_title');
        $this->data['write_review_title']=$this->language->get('write_review_title');
        $this->data['product_rate_title']=$this->language->get('product_rate_title');
        $this->data['review_form_status'] = $this->isReviewAllowed($product_id);
        $average = $this->data['display_reviews']
            ? $this->model_catalog_review->getAverageRating($product_id)
            : false;
        $this->data['review_percentage'] = $this->model_catalog_review->getPositiveReviewPercentage($product_id);
        if ($this->data['review_form_status']) {
            $this->data['rating_element'] = HtmlElementFactory::create(
                [
                    'type'    => 'rating',
                    'name'    => 'rating',
                    'value'   => '',
                    'options' => [1 => 1, 2, 3, 4, 5],
                    'pack'    => true,
                ]
            );
        }

        $this->data['text_stars'] = sprintf($this->language->get('text_stars'), $average);
        $this->data['review_name'] = HtmlElementFactory::create(
            [
                'type' => 'input',
                'name' => 'name',
            ]
        );
        $this->data['review_text'] = HtmlElementFactory::create(
            [
                'type' => 'textarea',
                'name' => 'text',
                'attr' => ' rows="8" cols="50" ',
            ]
        );

        //show page of approval reviews
        if ($this->data['display_reviews'] || $this->data['review_form_status']) {
            $this->addChild('responses/product/review/review', 'current_reviews');
        }


        if ($this->config->get('config_recaptcha_site_key')) {
            $this->data['recaptcha_site_key'] = $this->config->get('config_recaptcha_site_key');
            $this->data['review_recaptcha'] = HtmlElementFactory::create(
                [
                    'type'               => 'recaptcha',
                    'name'               => 'g-recaptcha-response',
                    'recaptcha_site_key' => $this->data['recaptcha_site_key'],
                    'language_code'      => $this->language->getLanguageCode(),
                ]
            );
        } else {
            $this->data['review_captcha'] = HtmlElementFactory::create(
                [
                    'type' => 'input',
                    'name' => 'captcha',
                    'attr' => '',
                ]
            );
        }
        $this->data['review_button'] = HtmlElementFactory::create(
            [
                'type'  => 'button',
                'name'  => 'review_submit',
                'text'  => $this->language->get('button_submit'),
                'style' => 'btn-primary lock-on-click',
                'icon'  => 'fa fa-comment',
            ]
        );

        $this->data['product_info'] = $product_info;

        $form = new AForm();
        $form->setForm(['form_name' => 'product']);
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'product',
                'action' => $this->html->getSecureURL($this->routes['cart_rt']),
            ]
        );

        $discount = $promotion->getProductDiscount($product_id);

        //Need to round price after discounts and specials
        //round main price to currency decimal_place setting (most common 2, but still...)
        $currency = $this->currency->getCurrency();
        $decimal_place = (int) $currency['decimal_place'];
        $decimal_place = !$decimal_place ? 2 : $decimal_place;

        if ($discount) {
            $product_price = round((float)$discount, $decimal_place);
            $this->data['price_num'] = round(
                $this->tax->calculate(
                    $discount,
                    $product_info['tax_class_id'],
                    (bool) $this->config->get('config_tax')
                ),
                $decimal_place
            );
            $this->data['special'] = false;
        } else {
            $product_price = round((float)$product_info['price'], $decimal_place);
            $this->data['price_num'] = round(
                $this->tax->calculate(
                    $product_info['price'],
                    $product_info['tax_class_id'],
                    (bool) $this->config->get('config_tax')
                ),
                $decimal_place
            );

            $special = $promotion->getProductSpecial($product_id);
            if ($special) {
                $product_price = round($special, $decimal_place);
                $this->data['special_num'] = round(
                    $this->tax->calculate(
                        $special,
                        $product_info['tax_class_id'],
                        (bool) $this->config->get('config_tax')
                    ),
                    $decimal_place
                );
            } else {
                $this->data['special'] = false;
            }
        }

        $this->data['price'] = $this->currency->format($this->data['price_num']);

        if (isset($this->data['special_num'])) {
            $this->data['special'] = $this->currency->format($this->data['special_num']);
        }

        $product_discounts = $promotion->getProductDiscounts($product_id);
        $discounts = [];
        foreach ($product_discounts as $discount) {
            $discounts[] = [
                'quantity' => $discount['quantity'],
                'price'    => $this->currency->format(
                    $this->tax->calculate(
                        $discount['price'],
                        $product_info['tax_class_id'],
                        (bool) $this->config->get('config_tax')
                    )
                )
            ];
        }
        $this->data['discounts'] = $discounts;
        $this->data['product_price'] = $product_price;
        $this->data['tax_class_id'] = $product_info['tax_class_id'];

        if (!$product_info['call_to_order']) {
            $qnt = (int) $request['quantity'];
            if (!$qnt) {
                $qnt = (int)$product_info['minimum'] ? : 1;
            }

            $qnt = (int) $product_info['minimum'] && $product_info['minimum'] > $qnt
                ? (int) $product_info['minimum']
                : $qnt;

            $this->data['form']['minimum'] = $form->getFieldHtml(
                [
                    'type'  => 'number',
                    'name'  => 'quantity',
                    'value' => $qnt,
                    'style' => 'short',
                    'attr'  => ' size="3" '
                        .( (int)$product_info['maximum'] ? 'max="'.(int)$product_info['maximum'].'"' : '')
                        .( ' min="'.(int)$product_info['minimum'].'"')
                    ,
                ]
            );

            $this->data['form']['add_to_cart'] = $form->getFieldHtml(
                [
                    'type'  => 'button',
                    'name'  => 'add_to_cart',
                    'text'  => $this->language->get('button_add_to_cart'),
                    'style' => 'button1',
                ]
            );
        }

        $this->data['form']['product_id'] = $form->getFieldHtml(
            [
                'type'  => 'hidden',
                'name'  => 'product_id',
                'value' => $product_id,
            ]
        );
        $this->data['form']['redirect'] = $form->getFieldHtml(
            [
                'type'  => 'hidden',
                'name'  => 'redirect',
                'value' => $this->html->getURL('product/product', $url.'&product_id='.$product_id, '&encode'),
            ]
        );

        $this->data['model'] = $product_info['model'];
        $this->data['sku'] = $product_info['sku'];
        $this->data['manufacturer'] = $product_info['manufacturer'];
        $this->data['manufacturers'] = $this->html->getSEOURL(
            'product/manufacturer',
            '&manufacturer_id='.$product_info['manufacturer_id'],
            '&encode'
        );
        $this->data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
        $this->data['product_id'] = $product_id;
        $this->data['average'] = number_format($product_info['rating'],1,'.');

        if (!has_value($product_info['stock_checkout'])) {
            $product_info['stock_checkout'] = $this->config->get('config_stock_checkout');
        }

        $resource = new AResource('image');
        $thumbnail = $resource->getMainThumb(
            'manufacturers',
            $product_info['manufacturer_id'],
            (int) $this->config->get('config_image_grid_width'),
            (int) $this->config->get('config_image_grid_height')
        );

        if (!str_contains($thumbnail['thumb_url'], 'no_image')) {
            $this->data['manufacturer_icon'] = $thumbnail['thumb_url'];
        }

        // Prepare options and values for display
        $elements_with_options = HtmlElementFactory::getElementsWithOptions();
        $options = [];
        $option_images = $txtIds = [];
        $product_options = $this->model_catalog_product->getProductOptions($product_id);

        //get info from cart if key presents
        $cart_product_info = [];
        if ($key) {
            $cart_product_info = $this->cart->getProduct($request['key']);
        }
        foreach ($product_options as $option) {
            $values = [];
            $disabled_values = [];
            $name = $price = '';
            $default_value = $cart_product_info['options'][$option['product_option_id']];
            if ($option['element_type'] == 'R') {
                $default_value = is_array($default_value) ? current($default_value) : (string) $default_value;
            }
            $preset_value = $default_value;
            $opt_stock_message = '';
            foreach ($option['option_value'] as $option_value) {
                $default_value = $option_value['default'] && !$default_value
                    ? $option_value['product_option_value_id']
                    : $default_value;
                // for case when trying to add to cart without required options. we get option-array back inside _GET
                if (has_value($request['option'][$option['product_option_id']])) {
                    $default_value = $request['option'][$option['product_option_id']];
                }

                $name = $option_value['name'];
                //check if we disable option based on stock settings
                if ($option_value['subtract'] && $this->config->get('config_nostock_autodisable')
                    && $option_value['quantity'] <= 0) {
                    continue;
                }

                if($option_value['txt_id']) {
                    $txtIds[$option_value['product_option_value_id']] = $option_value['txt_id'];
                }

                //Stock and status
                $opt_stock_message = '';
                //if options has stock tracking and not allowed to be purchased out of stock
                if ($option_value['subtract'] && !$product_info['stock_checkout']) {
                    if ($option_value['quantity'] <= 0) {
                        //show out of stock message
                        $opt_stock_message = $this->language->get('text_out_of_stock');
                        $disabled_values[] = $option_value['product_option_value_id'];
                    } elseif ($this->config->get('config_stock_display')) {
                        $opt_stock_message = $option_value['quantity']." ".$this->language->get('text_instock');
                        $opt_stock_message = "(".$opt_stock_message.")";
                    }
                } else {
                    if ($option_value['subtract'] && $product_info['stock_checkout']) {
                        if ($option_value['quantity'] <= 0) {
                            $opt_stock_message =
                                $product_info['stock_status'] ? "({$product_info['stock_status']})" : '';
                        }
                    }
                }

                //Apply option price modifier
                if ($option_value['prefix'] == '%') {
                    $price = $this->tax->calculate(
                        ($product_price * $option_value['price'] / 100),
                        $product_info['tax_class_id'],
                        (bool) $this->config->get('config_tax')
                    );
                } else {
                    $price = $this->tax->calculate(
                        $option_value['price'],
                        $product_info['tax_class_id'],
                        (bool) $this->config->get('config_tax')
                    );
                }
                $price = $price != 0 ? $this->currency->format($price) : '';

                $values[$option_value['product_option_value_id']] = $option_value['name']
                    .' '
                    .$price
                    .' '
                    .$opt_stock_message;
                //disable stock tracking for product if one of option have "subtract"
                if ($option_value['subtract']) {
                    $product_info['subtract'] = false;
                }

                if ($option['element_type'] == 'B') {
                    $name = $default_value = preg_replace("/\r|\n/", " ", $option_value['name']);
                    if ($price) {
                        $default_value .= '</br>';
                        $name .= ' ';
                    }
                    if ($price) {
                        $default_value .= $price.' ';
                        $name .= $price;
                    }
                    $option['required'] = false;
                }
                // if at least one option is file - change enctype for form
                if ($option['element_type'] == 'U') {
                    $this->data['form']['form_open']->enctype = "multipart/form-data";
                }
            }

            $option_data = [];

            //if not values are build, nothing to show
            if (count($values)) {
                $value = $data_attr = '';
                //add price to option name if it is not element with options
                if (!in_array($option['element_type'], $elements_with_options) && $option['element_type'] != 'B') {
                    $option['name'] .= ' <small>'.$price.'</small>';
                    if ($opt_stock_message) {
                        $option['name'] .= '<br />'.$opt_stock_message;
                    }
                    $value = $default_value ? : $name;
                } else {
                    if ($option['element_type'] == 'B') {
                        $value = $name;
                    }
                }

                //set default selection is nothing selected
                if (!has_value($value)) {
                    if (has_value($default_value)) {
                        $value = $default_value;
                    }
                }

                //for checkbox with empty value
                if ($option['element_type'] == 'C') {
                    $value = $value ?: 1;
                    $data_attr .= ' data-attribute-value-id="'.key($option['option_value']).'"';
                }

                if(in_array($option['element_type'], HtmlElementFactory::getElementsWithOptions())){
                    $data_attr .= ' data-txt-ids="'.base64_encode(json_encode($txtIds)).'"';
                }else{
                    $data_attr .= ' data-txt-ids="'.html2view(current($txtIds)).'"';
                }

                $option_data = [
                    'type'             => $option['html_type'],
                    'name'             =>
                        !in_array($option['element_type'], HtmlElementFactory::getMultivalueElements())
                            ? 'option['.$option['product_option_id'].']'
                            : 'option['.$option['product_option_id'].'][]',
                    'attr'             => $data_attr,
                    'extra'            => count($txtIds)==1 ? ['txt_id' => current($txtIds)] : ['txt_id' => $txtIds],
                    'value'            => $value,
                    'options'          => $values,
                    'disabled_options' => $disabled_values,
                    'required'         => $option['required'],
                    'placeholder'      => $option['option_placeholder'],
                    'regexp_pattern'   => $option['regexp_pattern'],
                    'error_text'       => $option['error_text'],
                ];

                if ($option['element_type'] == 'C') {
                    if (!in_array($value, ['0', '1'])) {
                        $option_data['label_text'] = $value;
                    }
                    $option_data['checked'] = (bool) $preset_value;
                }

                $options[] = [
                    'name' => $option['name'],
                    'html' => $this->html->buildElement($option_data),  // not a string!!! it's object!
                ];
            }
            // main product image
            $mSizes = [
                'main'  => [
                    'width'  => $this->config->get('config_image_popup_width'),
                    'height' => $this->config->get('config_image_popup_height'),
                ],
                'thumb' => [
                    'width'  => $this->config->get('config_image_thumb_width'),
                    'height' => $this->config->get('config_image_thumb_height'),
                ],
            ];
            $object_id = (is_array($option_data['value']) ? current($option_data['value']) : $option_data['value']);
            if ($object_id) {
                $option_images['main'] = $resource->getResourceAllObjects(
                    'product_option_value',
                    $object_id,
                    $mSizes,
                    1,
                    false
                );
            }

            if (!$option_images['main']) {
                unset($option_images['main']);
            }
            // additional images
            $oSizes = [
                'main'   =>
                    [
                        'width'  => $this->config->get('config_image_popup_width'),
                        'height' => $this->config->get('config_image_popup_height'),
                    ],
                'thumb'  =>
                    [
                        'width'  => $this->config->get('config_image_additional_width'),
                        'height' => $this->config->get('config_image_additional_height'),
                    ],
                //product image zoom related thumbnail
                'thumb2' =>
                    [
                        'width'  => $this->config->get('config_image_thumb_width'),
                        'height' => $this->config->get('config_image_thumb_height'),
                    ],
            ];
            if ($object_id) {
                $option_images['images'] = $resource->getResourceAllObjects(
                    'product_option_value',
                    $object_id,
                    $oSizes,
                    0,
                    false
                );
            }
            if (!$option_images['images']) {
                unset($option_images['images']);
            }
        }

        $this->data['options'] = $options;

        //handle stock messages
        // if track stock is off. no messages needed.
        if ($this->model_catalog_product->isStockTrackable($product_id)) {
            //NOTE: total quantity can be integer and true(in case stock-track is off)
            $total_quantity = $this->model_catalog_product->hasAnyStock($product_id);
            $this->data['track_stock'] = true;
            $this->data['can_buy'] = true;
            //out of stock if no quantity and no stock checkout is disabled
            if ($total_quantity <= 0 && !$product_info['stock_checkout']) {
                $this->data['can_buy'] = false;
                $this->data['in_stock'] = false;
                //show out of stock message
                $this->data['stock'] = $this->language->get('text_out_of_stock');
            } else {
                $this->data['can_buy'] = true;
                $this->data['in_stock'] = true;
                $this->data['stock'] = '';
                if ($this->config->get('config_stock_display') && $total_quantity > 0) {
                    //if not tracked - show nothing
                    $this->data['stock'] = $total_quantity !== true ? $total_quantity.' ' : '';
                }
                if ($total_quantity <= 0) {
                    $this->data['stock'] = $product_info['stock_status'];
                } else {
                    $this->data['stock'] .= $this->language->get('text_instock');
                }
            }

            //check if we need to disable product for no stock
            if ($this->config->get('config_nostock_autodisable') && $total_quantity <= 0) {
                //set available data
                $pd_identifiers = "ID: ".$product_id;
                $pd_identifiers .= (empty($product_info['model']) ? '' : " Model: ".$product_info['model']);
                $pd_identifiers .= (empty($product_info['sku']) ? '' : " SKU: ".$product_info['sku']);
                $message_ttl = sprintf($this->language->get('notice_out_of_stock_ttl'), $product_info['name']);
                $product_url = '#admin#rt=catalog/product/update&product_id='.$product_id;
                $message_txt = sprintf(
                    $this->language->get('notice_out_of_stock_body'),
                    $product_info['name'],
                    $pd_identifiers,
                    $product_url
                );
                //record to message box
                $msg = new AMessage();
                $msg->saveNotice($message_ttl, $message_txt);
                $this->model_catalog_product->updateStatus($product_id, 0);
                redirect(
                    $this->html->getSEOURL('product/product', '&product_id='.$product_info['product_id'], '&encode')
                );
            }
        } else {
            $this->data['can_buy'] = true;
            if ($product_info['quantity'] <= 0) {
                $this->data['stock'] = $product_info['stock_status'];
            }
        }

        // main product image
        $sizes = [
            'main'  => [
                'width'  => $this->config->get('config_image_popup_width'),
                'height' => $this->config->get('config_image_popup_height'),
            ],
            'thumb' => [
                'width'  => $this->config->get('config_image_thumb_width'),
                'height' => $this->config->get('config_image_thumb_height'),
            ],
        ];
        if (!$option_images['main']) {
            $this->data['image_main'] = $resource->getResourceAllObjects(
                'products',
                $product_id,
                $sizes,
                1,
                true);
            if ($this->data['image_main']) {
                $this->data['image_main']['sizes'] = $sizes;
            }
        } else {
            $this->data['image_main'] = $option_images['main'];
            if ($this->data['image_main']) {
                $this->data['image_main']['sizes'] = $sizes;
            }
            unset($option_images['main']);
        }

        // additional images
        $sizes = [
            'main'   => [
                'width'  => $this->config->get('config_image_popup_width'),
                'height' => $this->config->get('config_image_popup_height'),
            ],
            'thumb'  => [
                'width'  => $this->config->get('config_image_additional_width'),
                'height' => $this->config->get('config_image_additional_height'),
            ],
            'thumb2' => [
                'width'  => $this->config->get('config_image_thumb_width'),
                'height' => $this->config->get('config_image_thumb_height'),
            ],
        ];
        if (!$option_images['images']) {
            $this->data['images'] = $resource->getResourceAllObjects('products', $product_id, $sizes, 0, false);
        } else {
            $this->data['images'] = $option_images['images'];
        }

        $products = [];
        $results = $this->model_catalog_product->getProductRelated($product_id);
        foreach ($results as $result) {
            // related product image
            $sizes = [
                'main'  => [
                    'width'  => $this->config->get('config_image_related_width'),
                    'height' => $this->config->get('config_image_related_height'),
                ],
                'thumb' => [
                    'width'  => $this->config->get('config_image_related_width'),
                    'height' => $this->config->get('config_image_related_height'),
                ],
            ];
            $image = $resource->getResourceAllObjects('products', $result['product_id'], $sizes, 1);

            if ($this->config->get('display_reviews')) {
                $rating = $this->model_catalog_review->getAverageRating($result['product_id']);
            } else {
                $rating = false;
            }

            $special = false;
            $discount = $promotion->getProductDiscount($result['product_id']);
            if ($discount) {
                $price = $this->currency->format(
                    $this->tax->calculate(
                        $discount,
                        $result['tax_class_id'],
                        (bool) $this->config->get('config_tax')
                    )
                );
            } else {
                $price = $this->currency->format(
                    $this->tax->calculate(
                        $result['price'],
                        $result['tax_class_id'],
                        (bool) $this->config->get('config_tax')
                    )
                );
                $special = $promotion->getProductSpecial($result['product_id']);
                if ($special) {
                    $special = $this->currency->format(
                        $this->tax->calculate(
                            $special, $result['tax_class_id'], (bool) $this->config->get('config_tax')
                        )
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
                        $this->routes['cart_rt'],
                        '&product_id='.$result['product_id'],
                        '&encode'
                    );
                }
            }

            $products[] = [
                'product_id'    => $result['product_id'],
                'name'          => $result['name'],
                'model'         => $result['model'],
                'rating'        => $rating,
                'stars'         => sprintf($this->language->get('text_stars'), $rating),
                'price'         => $price,
                'call_to_order' => $result['call_to_order'],
                'options'       => $options,
                'special'       => $special,
                'image'         => $image,
                'href'          => $this->html->getSEOURL(
                                                            'product/product',
                                                            '&product_id='.$result['product_id'],
                                                            '&encode'
                                                        ),
                'add'           => $add,
                'tax_class_id'  => $result['tax_class_id'],
            ];
        }

        $this->data['related_products'] = $products;
        if ($this->config->get('config_customer_price')) {
            $display_price = true;
        } elseif ($this->customer->isLogged()) {
            $display_price = true;
        } else {
            $display_price = false;
        }
        $this->data['display_price'] = $display_price;

        $tags = [];
        $results = $this->model_catalog_product->getProductTags($product_id);
        foreach ($results as $result) {
            if ($result['tag']) {
                $tags[] = [
                    'tag'  => $result['tag'],
                    'href' => $this->html->getURL('product/search', '&keyword='.$result['tag'], '&encode'),
                ];
            }
        }
        $this->data['tags'] = $tags;

        //downloads before order if allowed
        if ($this->config->get('config_download')) {
            $download_list = $this->download->getDownloadsBeforeOrder($product_id);
            if ($download_list) {
                $downloads = [];

                foreach ($download_list as $download) {
                    $href = $this->html->getURL(
                        'account/download/startdownload',
                        '&download_id='.$download['download_id']
                    );
                    $download['attributes'] = $this->download->getDownloadAttributesValuesForCustomer(
                        $download['download_id']
                    );

                    $download['button'] = $form->getFieldHtml(
                        [
                            'type'  => 'button',
                            'id'    => 'download_'.$download['download_id'],
                            'href'  => $href,
                            'title' => $this->language->get('text_start_download'),
                            'text'  => $this->language->get('text_start_download'),
                        ]
                    );

                    $downloads[] = $download;
                }

                $this->data['downloads'] = $downloads;
            }
        }

        #check if product is in a wishlist
        $this->data['is_customer'] = false;
        if ($this->customer->isLogged() || $this->customer->isUnauthCustomer()) {
            $this->data['is_customer'] = true;
            $whishlist = $this->customer->getWishList();
            if ($whishlist[$product_id]) {
                $this->data['in_wishlist'] = true;
            }
        }

        if($this->config->get('fast_checkout_buy_now_status')) {

            $data = [];
            $data['button_add_to_cart'] = $this->language->get('button_add_to_cart');
            $data['text_buynow'] = $this->language->get('fast_checkout_buy_now');
            $data['buynow_url'] = $this->html->getSecureURL('checkout/fast_checkout', '&single_checkout=1');
            $data['add_to_cart'] = $this->language->get('button_add_to_cart');

            /** @var AView $view */
            $viewClass = get_class($this->view);
            $view = new $viewClass(Registry::getInstance(), 0);
            $view->batchAssign($data);
            $this->view->addHookVar(
                'product_add_to_cart_html',
                $view->fetch('pages/product/add_to_cart_buttons.tpl')
            );
        }

        $this->view->setTemplate('pages/product/product.tpl');
        $this->view->batchAssign($this->data);
        $this->session->data['viewed_products'][] = $this->request->get['product_id'];
        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function _product_not_found($product_id)
    {
        $this->_init();
        $url = $this->_build_url_params($this->request->get);
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSEOURL(
                    'product/product',
                    $url.'&product_id='.$product_id,
                    '&encode'
                ),
                'text'      => $this->language->get('text_error'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        $this->document->setTitle($this->language->get('text_error'));

        $this->data['heading_title'] = $this->language->get('text_error');
        $this->data['text_error'] = $this->language->get('text_error');
        $continue = HtmlElementFactory::create(
            [
                'type'  => 'button',
                'name'  => 'continue_button',
                'text'  => $this->language->get('button_continue'),
                'style' => 'button',
            ]
        );
        $this->view->assign('button_continue', $continue);
        $this->data['continue'] = $this->html->getHomeURL();

        $this->view->setTemplate('pages/error/not_found.tpl');

        $this->view->batchAssign($this->data);
        $this->processTemplate();
    }

    /**
     * @param $request
     *
     * @return string
     */
    protected function _build_url_params($request)
    {
        $httpQuery = [];
        $params = [
            'path',
            'manufacturer_id',
            'keyword',
            'category_id',
            'description'
        ];
        foreach($params as $key){
            if (isset($request[$key])) {
                $httpQuery[$key] = $request[$key];
            }
        }

        return $httpQuery ? '&'.http_build_query($httpQuery) : '';
    }
}
