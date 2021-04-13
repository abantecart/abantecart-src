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

class ControllerPagesSaleOrder extends AController
{
    public $error = [];

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->initBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('sale/order'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        if (isset($this->session->data['error'])) {
            $this->data['error_warning'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } elseif (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }

        //set content language to main language.
        if ($this->language->getContentLanguageID() != $this->language->getLanguageID()) {
            //reset content language
            $this->language->setCurrentContentLanguage($this->language->getLanguageID());
        }

        //outer parameters to filter the result 
        $extra_params = '';
        $extra_params .= isset($this->request->get['customer_id'])
            ? '&customer_id='.$this->request->get['customer_id']
            : '';
        $extra_params .= isset($this->request->get['product_id'])
            ? '&product_id='.$this->request->get['product_id']
            : '';

        $grid_settings = [
            //id of grid
            'table_id'     => 'order_grid',
            // url to load data from
            'url'          => $this->html->getSecureURL('listing_grid/order', $extra_params),
            'editurl'      => $this->html->getSecureURL('listing_grid/order/update'),
            'update_field' => $this->html->getSecureURL('listing_grid/order/update_field'),
            'sortname'     => 'order_id',
            'sortorder'    => 'desc',
            'multiselect'  => 'true',
            // actions
            'actions'      => [
                'edit'     => [
                    'text' => $this->language->get('tab_order_details'),
                    'href' => $this->html->getSecureURL(
                        'sale/order/details',
                        '&order_id=%ID%'
                    ),
                ],
                'save'     => [
                    'text' => $this->language->get('button_save'),
                ],
                'delete'   => [
                    'text' => $this->language->get('button_delete'),
                ],
                'print'    => [
                    'text'   => $this->language->get('button_invoice'),
                    'href'   => $this->html->getSecureURL('sale/invoice', '&order_id=%ID%'),
                    'target' => '_invoice',
                ],
                'dropdown' => [
                    'text'     => $this->language->get('text_choose_action'),
                    'href'     => $this->html->getSecureURL('sale/order/update', '&order_id=%ID%'),
                    'children' => array_merge(
                        [
                            'quickview' => [
                                'text'  => $this->language->get('text_quick_view'),
                                'href'  => $this->html->getSecureURL(
                                    'sale/order/details',
                                    '&order_id=%ID%'
                                ),
                                //quick view port URL
                                'vhref' => $this->html->getSecureURL(
                                    'r/common/viewport/modal',
                                    '&viewport_rt=sale/order/details&order_id=%ID%'
                                ),
                            ],
                            'details'   => [
                                'text' => $this->language->get('tab_order_details'),
                                'href' => $this->html->getSecureURL(
                                    'sale/order/details',
                                    '&order_id=%ID%'
                                ),
                            ],
                            'address'   => [
                                'text' => $this->language->get('tab_address'),
                                'href' => $this->html->getSecureURL(
                                    'sale/order/address',
                                    '&order_id=%ID%'
                                ),
                            ],
                            'files'     => [
                                'text' => $this->language->get('tab_files'),
                                'href' => $this->html->getSecureURL(
                                    'sale/order/files',
                                    '&order_id=%ID%'
                                ),
                            ],
                            'history'   => [
                                'text' => $this->language->get('tab_history'),
                                'href' => $this->html->getSecureURL(
                                    'sale/order/history',
                                    '&order_id=%ID%'
                                ),
                            ],

                        ], (array) $this->data['grid_edit_expand']
                    ),
                ],
            ],
        ];

        $grid_settings['colNames'] = [
            $this->language->get('column_order'),
            $this->language->get('column_name'),
            $this->language->get('column_status'),
            $this->language->get('column_date_added'),
            $this->language->get('column_total'),
        ];
        $grid_settings['colModel'] = [
            [
                'name'  => 'order_id',
                'index' => 'order_id',
                'width' => 60,
                'align' => 'center',
            ],
            [
                'name'  => 'name',
                'index' => 'name',
                'width' => 140,
                'align' => 'center',
            ],
            [
                'name'   => 'status',
                'index'  => 'status',
                'width'  => 140,
                'align'  => 'center',
                'search' => false,
            ],
            [
                'name'   => 'date_added',
                'index'  => 'date_added',
                'width'  => 90,
                'align'  => 'center',
                'search' => false,
            ],
            [
                'name'  => 'total',
                'index' => 'total',
                'width' => 90,
                'align' => 'center',
            ],
        ];

        $this->loadModel('localisation/order_status');
        $results = $this->model_localisation_order_status->getOrderStatuses();
        $statuses = [
            ''    => $this->language->get('text_select_status'),
            'all' => $this->language->get('text_all_orders'),
        ];
        foreach ($results as $item) {
            $statuses[$item['order_status_id']] = $item['name'];
        }

        $form = new AForm();
        $form->setForm(
            [
                'form_name' => 'order_grid_search',
            ]
        );

        //get search filter from cookie if required
        $search_params = [];
        if ($this->request->get['saved_list']) {
            $grid_search_form = json_decode(html_entity_decode($this->request->cookie['grid_search_form']));
            if ($grid_search_form->table_id == $grid_settings['table_id']) {
                parse_str($grid_search_form->params, $search_params);
            }
        }

        $grid_search_form = [];
        $grid_search_form['id'] = 'order_grid_search';
        $grid_search_form['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'order_grid_search',
                'action' => '',
            ]
        );
        $grid_search_form['submit'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'submit',
                'text'  => $this->language->get('button_go'),
                'style' => 'button1',
            ]
        );
        $grid_search_form['reset'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'reset',
                'text'  => $this->language->get('button_reset'),
                'style' => 'button2',
            ]
        );
        $grid_search_form['fields']['status'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'status',
                'options' => $statuses,
                'value'   => $search_params['status'],
            ]
        );
        $grid_settings['search_form'] = true;

        $grid = $this->dispatch('common/listing_grid', [$grid_settings]);
        $this->view->assign('listing_grid', $grid->dispatchGetOutput());
        $this->view->assign('search_form', $grid_search_form);
        $this->view->assign('help_url', $this->gen_help_url('order_listing'));
        $this->view->assign('form_store_switch', $this->html->getStoreSwitcher());

        $this->document->setTitle($this->language->get('heading_title'));

        $this->processTemplate('pages/sale/order_list.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function update()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->document->setTitle($this->language->get('heading_title'));

        if ($this->request->is_POST() && $this->_validateForm()) {
            if (has_value($this->request->post['order_product_id'])) { //if present - saving form modal
                $this->model_sale_order->editOrderProduct($this->request->get['order_id'], $this->request->post);
            } else {
                $this->model_sale_order->editOrder($this->request->get['order_id'], $this->request->post);
            }

            //recalc totals and update
            $this->session->data['success'] = $this->language->get('text_success');
            $this->session->data['attention'] = $this->language->get('attention_check_total');
            redirect(
                $this->html->getSecureURL(
                    'sale/order/recalc',
                    '&order_id='.$this->request->get['order_id']
                )
            );
        }

        redirect($this->html->getSecureURL('sale/order'));

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function details(...$args)
    {
        $viewport_mode = isset($args[0]['viewport_mode']) ? $args[0]['viewport_mode'] : '';

        $this->data = [];
        $fields = [
            'email',
            'telephone',
            'fax',
            'shipping_method',
            'payment_method',
        ];

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->document->setTitle($this->language->get('heading_title'));

        if (has_value($this->session->data['error'])) {
            $this->data['error']['warning'] = $this->session->data['error'];
            unset($this->session->data['error']);
        }

        $order_id = (int) $this->request->get['order_id'];
        if ($this->request->is_POST() && $this->_validateForm()) {
            $this->model_sale_order->editOrder($order_id, $this->request->post);
            if (has_value($this->request->post['downloads'])) {
                $data = $this->request->post['downloads'];
                $this->loadModel('catalog/download');
                foreach ($data as $order_download_id => $item) {
                    if ($item['expire_date']) {
                        $item['expire_date'] = dateDisplay2ISO(
                            $item['expire_date'],
                            $this->language->get('date_format_short')
                        );
                    } else {
                        $item['expire_date'] = '';
                    }
                    $this->model_catalog_download->editOrderDownload($order_download_id, $item);
                }
            } else {
                //NOTE: Totals will be recalculated if forced so skip array is not needed.
                if ($this->request->post['force_recalc']) {
                    $this->session->data['attention'] = $this->language->get('attention_check_total');
                    redirect($this->html->getSecureURL('sale/order/recalc', '&order_id='.$order_id));
                } else {
                    if ($this->request->post['force_recalc_single']) {
                        //recalc single only
                        $skip_recalc = [];
                        foreach ($this->request->post['totals'] as $key => $value) {
                            if (has_value($value)) {
                                $skip_recalc[] = $key;
                            }
                        }

                        $enc = new AEncryption($this->config->get('encryption_key'));
                        redirect(
                            $this->html->getSecureURL(
                                'sale/order/recalc',
                                '&order_id='.$order_id.'&skip_recalc='.$enc->encrypt(serialize($skip_recalc))
                            )
                        );
                    }
                }
            }
        }

        $order_info = $this->model_sale_order->getOrder($order_id);

        $this->data['order_info'] = $order_info;

        //set content language to order language ID.
        if ($this->language->getContentLanguageID() != $order_info['language_id']) {
            //reset content language
            $this->language->setCurrentContentLanguage($order_info['language_id']);
        }

        if (empty($order_info)) {
            $this->session->data['error'] = $this->language->get('error_order_load');
            redirect($this->html->getSecureURL('sale/order'));
        }

        $this->document->initBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('sale/order'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('sale/order/details', '&order_id='.$order_id),
                'text'      => $this->language->get('heading_title').' #'.$order_id,
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        if (isset($this->session->data['attention'])) {
            $this->data['attention'] = $this->session->data['attention'];
            unset($this->session->data['attention']);
        } else {
            $this->data['attention'] = '';
        }
        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }

        $this->data['heading_title'] = $this->language->get('heading_title').' #'.$order_id;
        $this->data['token'] = $this->session->data['token'];
        $this->data['invoice_url'] = $this->html->getSecureURL('sale/invoice', '&order_id='.$order_id);
        $this->data['button_invoice'] = $this->html->buildElement(
            [
                'type' => 'button',
                'name' => 'generate_invoice',
                'text' => $this->language->get('button_generate'),
            ]
        );
        $this->data['invoice_generate'] = $this->html->getSecureURL('sale/invoice/generate');
        $this->data['category_products'] = $this->html->getSecureURL('product/product/category');
        $this->data['product_update'] = $this->html->getSecureURL('catalog/product/update');
        $this->data['order_id'] = $order_id;
        $this->data['action'] = $this->html->getSecureURL('sale/order/details', '&order_id='.$order_id);
        $this->data['cancel'] = $this->html->getSecureURL('sale/order');

        if ($viewport_mode != 'modal') {
            $this->_initTabs('order_details');
        }

        // These only change for insert, not edit. To be added later
        $this->data['ip'] = $order_info['ip'];
        $this->data['history'] = $this->html->getSecureURL('sale/order/history', '&order_id='.$order_id);
        $this->data['store_name'] = $order_info['store_name'];
        $this->data['store_url'] = $order_info['store_url'];
        $this->data['comment'] = nl2br($order_info['comment']);
        $this->data['firstname'] = $order_info['firstname'];
        $this->data['lastname'] = $order_info['lastname'];
        $this->data['lastname'] = $order_info['lastname'];
        $this->data['total'] = $this->currency->format(
            $order_info['total'],
            $order_info['currency'],
            $order_info['value']
        );
        $this->data['date_added'] = dateISO2Display(
            $order_info['date_added'],
            $this->language->get('date_format_short').' '.$this->language->get('time_format')
        );
        if ($order_info['customer_id']) {
            $this->data['customer_href'] = $this->html->getSecureURL(
                'sale/customer/update',
                '&customer_id='.$order_info['customer_id']
            );
            $this->data['customer_vhref'] = $this->html->getSecureURL(
                'r/common/viewport/modal',
                '&viewport_rt=sale/customer/update&customer_id='.$order_info['customer_id']
            );
        }

        $this->loadModel('localisation/order_status');
        $status = $this->model_localisation_order_status->getOrderStatus($order_info['order_status_id']);
        if ($status) {
            $this->data['order_status'] = $status['name'];
        } else {
            $this->data['order_status'] = '';
        }

        $this->loadModel('sale/customer_group');
        $customer_group_info = $this->model_sale_customer_group->getCustomerGroup($order_info['customer_group_id']);
        if ($customer_group_info) {
            $this->data['customer_group'] = $customer_group_info['name'];
        } else {
            $this->data['customer_group'] = '';
        }

        if ($order_info['invoice_id']) {
            $this->data['invoice_id'] = $order_info['invoice_prefix'].$order_info['invoice_id'];
        } else {
            $this->data['invoice_id'] = '';
        }

        foreach ($fields as $f) {
            if (isset ($this->request->post [$f])) {
                $this->data [$f] = $this->request->post [$f];
            } elseif (isset($order_info[$f])) {
                $this->data[$f] = $order_info[$f];
            } else {
                $this->data[$f] = '';
            }
        }

        $this->data['email'] = $this->html->buildInput(
            [
                'name'  => 'email',
                'value' => $order_info['email'],
            ]
        );
        $this->data['telephone'] = $this->html->buildInput(
            [
                'name'  => 'telephone',
                'value' => $order_info['telephone'],
            ]
        );

        $this->data['fax'] = $this->html->buildInput(
            [
                'name'  => 'fax',
                'value' => $order_info['fax'],
            ]
        );

        if (isset($order_info['im'])) {
            foreach ($order_info['im'] as $protocol => $setting) {
                $this->data['im'][$protocol] = $setting['uri'];
            }
        }

        $this->loadModel('catalog/product');
        $this->loadModel('catalog/category');
        $this->data['categories'] = $this->model_catalog_category->getCategories(ROOT_CATEGORY_ID);

        $this->data['order_products'] = [];
        $order_products = $this->model_sale_order->getOrderProducts($order_id);

        foreach ($order_products as $order_product) {
            $option_data = [];
            $options = $this->model_sale_order->getOrderOptions($order_id, $order_product['order_product_id']);
            foreach ($options as $option) {
                $value = $option['value'];
                //generate link to download uploaded files
                if ($option['element_type'] == 'U') {
                    $file_settings = unserialize($option['settings']);
                    $filename = $value;
                    if (has_value($file_settings['directory'])) {
                        $file = DIR_APP_SECTION.'system/uploads/'.$file_settings['directory'].'/'.$filename;
                    } else {
                        $file = DIR_APP_SECTION.'system/uploads/'.$filename;
                    }

                    if (is_file($file)) {
                        $value = '<a href="'
                            .$this->html->getSecureURL(
                                'tool/files/download',
                                '&filename='.urlencode($filename).'&order_option_id='.(int) $option['order_option_id']
                            )
                            .'" title=" to download file" target="_blank">'.$value.'</a>';
                    } else {
                        $value = '<span title="file '.$file.' is unavailable">'.$value.'</span>';
                    }
                } elseif ($option['element_type'] == 'C' && $value == 1) {
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
                    'value' => nl2br($value),
                    'title' => $title,
                ];
            }

            //check if this product product is still available, so we can use recalculation against the cart
            $product = $this->model_catalog_product->getProduct($order_product['product_id']);
            if (empty($product) || !$product['status'] || $product['call_to_order']) {
                $this->data['no_recalc_allowed'] = true;
                $product['status'] = 0;
            } else {
                if (dateISO2Int($product['date_available']) > time()) {
                    $this->data['no_recalc_allowed'] = true;
                    $product['status'] = 0;
                }
            }

            //append stock locations from order
            $stock_located = $this->model_catalog_product->getOrderProductStockLocations(
                $order_product['order_product_id']
            );
            $stock_quantities = [];

            if ($stock_located) {
                $stock_quantities = [];
                foreach ($stock_located as $row) {
                    $stock_quantities[] = [
                        'location_id' => $row['location_id'],
                        'name'        => $row['location_name'],
                        'quantity'    => $row['quantity'],
                        'available'   => $row['available_quantity'],
                    ];
                }
            }

            $this->data['order_products'][$order_product['order_product_id']] = [
                'order_product_id' => $order_product['order_product_id'],
                'product_id'       => $order_product['product_id'],
                'product_status'   => $product['status'],
                'name'             => $order_product['name'],
                'model'            => $order_product['model'],
                'option'           => $option_data,
                'quantity'         => $order_product['quantity'],
                'stock_quantities' => $stock_quantities,
                'price'            => $this->currency->format(
                    $order_product['price'],
                    $order_info['currency'],
                    $order_info['value']
                ),
                'total'            => $this->currency->format_total(
                    $order_product['price'],
                    $order_product['quantity'],
                    $order_info['currency'],
                    $order_info['value']
                ),
                'href'             => $this->html->getSecureURL(
                    'catalog/product/update',
                    '&product_id='.$order_product['product_id']
                ),
            ];
        }

        $this->data['currency'] = $this->currency->getCurrency($order_info['currency']);

        $this->data['totals'] = $this->model_sale_order->getOrderTotals($order_id);
        //add enabled but not present totals such as discount and fee.
        $add_missing = ['low_order_fee', 'handling', 'coupon', 'shipping', 'tax'];
        $this->loadModel('setting/extension');
        $new_totals = [];
        $total_ext = $this->extensions->getExtensionsList(['filter' => 'total']);
        if ($total_ext->rows) {
            foreach ($total_ext->rows as $row) {
                $match = false;
                if (!$row['status'] || !in_array($row['key'], $add_missing)) {
                    continue;
                }
                foreach ($this->data['totals'] as $total) {
                    if ($row['key'] == $total['key']) {
                        $match = true;
                        break;
                    }
                }
                if (!$match) {
                    $new_totals[$row['key']] = $row['key'];
                    $this->data['totals_add'][] = [
                        'key'        => $row['key'],
                        'type'       => $this->config->get($row['key'].'_total_type'),
                        'order_id'   => $order_id,
                        'title'      => $row['key'],
                        'text'       => '',
                        'value'      => '',
                        'sort_order' => $this->config->get($row['key'].'_sort_order'),
                    ];
                }
            }
        }
        //check which totals we allow to edit (disable edit for missing and disabled totals. 
        foreach ($this->data['totals'] as &$ototal) {
            $ototal['unavailable'] = true;
            //is order prior to 1.2.2 upgrade? do not allow recalc
            if (empty($ototal['key'])) {
                $this->data['no_recalc_allowed'] = true;
                continue;
            }

            if ($total_ext->rows) {
                foreach ($total_ext->rows as $extn) {
                    if (!$extn['status']) {
                        //is total in this order missing? do not allow recalculate
                        if (str_replace('_', '', $ototal['key']) == str_replace('_', '', $extn['key'])) {
                            $this->data['no_recalc_allowed'] = true;
                        }
                        continue;
                    }
                    if (str_replace('_', '', $ototal['key']) == str_replace('_', '', $extn['key'])) {
                        //all good, total is available 
                        $ototal['unavailable'] = false;
                    }
                }
            }
        }
        //count duplicate keys to prevent delete od duplicate (such as tax)
        //issue with recalc of deleted items if duplicate keys 
        $this->data['total_key_count'] = [];
        foreach ($this->data['totals'] as $t_old) {
            $this->data['total_key_count'][$t_old['key']]++;
        }

        $this->data['form_title'] = $this->language->get('edit_title_details');
        $this->data['update'] = $this->html->getSecureURL('listing_grid/order/update_field', '&id='.$order_id);
        $form = new AForm('HS');

        $form->setForm(
            [
                'form_name' => 'orderFrm',
                'update'    => $this->data['update'],
            ]
        );

        $this->data['form']['id'] = 'orderFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'orderFrm',
                'attr'   => 'data-confirm-exit="true" class="aform form-horizontal"',
                'action' => $this->data['action'],
            ]
        );
        $this->data['form']['submit'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'submit',
                'text'  => $this->language->get('button_save'),
                'style' => 'button1',
            ]
        );
        $this->data['form']['cancel'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'cancel',
                'text'  => $this->language->get('button_cancel'),
                'style' => 'button2',
            ]
        );

        $this->data['new_total'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'new_total',
                'value'   => $this->data['new_total'],
                'options' => $new_totals,
            ]
        );

        //if virtual product (no shipment);
        if (!$this->data['shipping_method']) {
            $this->data['form']['fields']['shipping_method'] = $this->language->get('text_not_applicable');
        } else {
            $this->data['form']['fields']['shipping_method'] = $this->data['shipping_method'];
        }
        // no payment 
        if (!$this->data['payment_method']) {
            $this->data['form']['fields']['payment_method'] = $this->language->get('text_not_applicable');
        } else {
            $this->data['form']['fields']['payment_method'] = $this->data['payment_method'];
        }

        $this->data['add_product'] = $this->html->buildElement(
            [
                'type'          => 'multiselectbox',
                'name'          => 'add_product',
                'value'         => '',
                'options'       => [],
                'style'         => 'aform_noaction chosen',
                'ajax_url'      => $this->html->getSecureURL(
                    'r/product/product/products',
                    '&currency_code='.$this->data['currency']['code']
                ),
                'placeholder'   => $this->language->get('text_select_from_lookup'),
                'option_attr'   => ['price'],
                'filter_params' => 'enabled_only'
                // list of json-item properties that becomes html5 attributes of option tag. Ex. price will be data-price="00.000"
            ]
        );

        $this->data['add_product_url'] = $this->html->getSecureURL(
            'r/product/product/orderProductForm',
            '&order_id='.$order_id
        );
        $this->data['edit_order_total'] = $this->html->getSecureURL('sale/order/recalc', '&order_id='.$order_id);
        $this->data['delete_order_total'] = $this->html->getSecureURL(
            'sale/order/delete_total',
            '&order_id='.$order_id
        );

        $saved_list_data = json_decode(html_entity_decode($this->request->cookie['grid_params']));
        if ($saved_list_data->table_id == 'order_grid') {
            $this->data['list_url'] = $this->html->getSecureURL('sale/order', '&saved_list=order_grid');
        }

        $this->view->batchAssign($this->data);
        $this->view->assign('help_url', $this->gen_help_url('order_details'));

        if ($viewport_mode == 'modal') {
            $tpl = 'responses/viewport/modal/sale/order_details.tpl';
        } else {
            $this->addChild('pages/sale/order_summary', 'summary_form', 'pages/sale/order_summary.tpl');
            $tpl = 'pages/sale/order_details.tpl';
        }

        $this->processTemplate($tpl);

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function payment()
    {
        redirect(
            $this->html->getSecureURL(
                'sale/order/address',
                '&order_id='.$this->request->get['order_id']
            )
        );
    }

    public function shipping()
    {
        redirect(
            $this->html->getSecureURL(
                'sale/order/address',
                '&order_id='.$this->request->get['order_id']
            )
        );
    }

    public function address()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->document->setTitle($this->language->get('heading_title'));

        if ($this->request->is_POST() && $this->_validateForm()) {
            $this->model_sale_order->editOrder($this->request->get['order_id'], $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            redirect(
                $this->html->getSecureURL(
                    'sale/order/address',
                    '&order_id='.$this->request->get['order_id']
                )
            );
        }

        if (isset($this->request->get['order_id'])) {
            $order_id = (int) $this->request->get['order_id'];
        } else {
            $order_id = 0;
        }

        $order_info = $this->model_sale_order->getOrder($order_id);

        if (empty($order_info)) {
            $this->session->data['error'] = $this->language->get('error_order_load');
            redirect($this->html->getSecureURL('sale/order'));
        }

        //set content language to order language ID.
        if ($this->language->getContentLanguageID() != $order_info['language_id']) {
            //reset content language
            $this->language->setCurrentContentLanguage($order_info['language_id']);
        }

        $this->document->initBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('sale/order'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('sale/order/details', '&order_id='.$order_id),
                'text'      => $this->language->get('heading_title').' #'.$order_id,
                'separator' => ' :: ',
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('sale/order/address', '&order_id='.$order_id),
                'text'      => $this->language->get('tab_address'),
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }

        $this->data['order_id'] = $order_id;
        $this->data['invoice_url'] = $this->html->getSecureURL('sale/invoice', '&order_id='.$order_id);
        $this->data['button_invoice'] = $this->html->buildButton(
            [
                'name'  => 'invoice',
                'text'  => $this->language->get('text_invoice'),
                'style' => 'button3',
            ]
        );
        $this->data['action'] = $this->html->getSecureURL('sale/order/address', '&order_id='.$order_id);
        $this->data['cancel'] = $this->html->getSecureURL('sale/order');
        $this->data['common_zone'] = $this->html->getSecureURL('common/zone');

        $this->_initTabs('address');

        $this->data['form_title'] = $this->language->get('edit_title_shipping');
        $this->data['update'] = $this->html->getSecureURL('listing_grid/order/update_field', '&id='.$order_id);
        $form = new AForm('HS');

        $form->setForm(
            [
                'form_name' => 'orderFrm',
                'update'    => $this->data['update'],
            ]
        );

        $this->data['form']['id'] = 'orderFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'orderFrm',
                'attr'   => 'data-confirm-exit="true" class="aform form-horizontal"',
                'action' => $this->data['action'],
            ]
        );
        $this->data['form']['submit'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'submit',
                'text'  => $this->language->get('button_save'),
                'style' => 'button1',
            ]
        );
        $this->data['form']['cancel'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'cancel',
                'text'  => $this->language->get('button_cancel'),
                'style' => 'button2',
            ]
        );

        $this->loadModel('localisation/country');
        $this->data['countries'] = $this->model_localisation_country->getCountries();
        $this->data['countries'] = array_merge(
            [
                0 => [
                    'country_id'   => 0,
                    'country_name' => $this->language->get('text_select_country'),
                ],
            ], $this->data['countries']
        );

        //preparing shipping fields
        $this->shippingFields($order_info, $form);
        //preparing payment fields
        $this->paymentFields($order_info, $form);

        $this->data['google_api_key'] = $this->config->get('config_google_api_key');

        $this->addChild('pages/sale/order_summary', 'summary_form', 'pages/sale/order_summary.tpl');
        $this->view->assign('help_url', $this->gen_help_url('order_shipping'));
        $this->view->batchAssign($this->data);

        $this->processTemplate('pages/sale/order_address.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    private function shippingFields($order_info, $form)
    {
        $fields = [
            'shipping_firstname',
            'shipping_lastname',
            'shipping_company',
            'shipping_address_1',
            'shipping_address_2',
            'shipping_city',
            'shipping_postcode',
            'fax',
            'telephone',
            'shipping_zone',
            'shipping_zone_id',
            'shipping_country',
            'shipping_country_id',
        ];

        foreach ($fields as $f) {
            if (isset ($this->request->post [$f])) {
                $this->data [$f] = $this->request->post [$f];
            } elseif (isset($order_info[$f])) {
                $this->data[$f] = $order_info[$f];
            }
        }

        foreach ($fields as $f) {
            if ($f == 'shipping_zone') {
                break;
            }
            $name = str_replace('shipping_', '', $f);
            $this->data['form']['shipping_fields'][$name] = $form->getFieldHtml(
                [
                    'type'  => 'input',
                    'name'  => $f,
                    'value' => $this->data[$f],
                ]
            );
        }

        $this->data['form']['shipping_fields']['telephone'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'telephone',
                'value' => $this->data['telephone'],
            ]
        );
        $this->data['form']['shipping_fields']['fax'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'fax',
                'value' => $this->data['fax'],
            ]
        );

        $countries = [];
        foreach ($this->data['countries'] as $country) {
            $countries[$country['country_id']] = $country['name'];
        }
        if (!$this->data['shipping_country_id']) {
            $this->data['shipping_country_id'] = $this->config->get('config_country_id');
        }

        $this->data['form']['shipping_fields']['country'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'shipping_country_id',
                'value'   => $this->data['shipping_country_id'],
                'options' => $countries,
            ]
        );

        $this->data['form']['shipping_fields']['zone'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'shipping_zone_id',
                'value'   => '',
                'options' => [],
            ]
        );

        $this->data['full_shipping_address'] = addslashes(
            $this->data['shipping_address_1']
            .' '.$this->data['shipping_address_2']
            .', '.$this->data['shipping_city']
            .', '.$this->data['shipping_zone']
            .', '.$this->data['shipping_postcode']
            .', '.$this->data['shipping_country']
        );
    }

    private function paymentFields($order_info, $form)
    {
        $fields = [
            'payment_firstname',
            'payment_lastname',
            'payment_company',
            'payment_address_1',
            'payment_address_2',
            'payment_city',
            'payment_postcode',
            'payment_zone',
            'payment_zone_id',
            'payment_country',
            'payment_country_id',
        ];

        foreach ($fields as $f) {
            if (isset ($this->request->post [$f])) {
                $this->data [$f] = $this->request->post [$f];
            } elseif (isset($order_info[$f])) {
                $this->data[$f] = $order_info[$f];
            }
        }

        foreach ($fields as $f) {
            if ($f == 'payment_zone') {
                break;
            }
            $name = str_replace('payment_', '', $f);
            $this->data['form']['payment_fields'][$name] = $form->getFieldHtml(
                [
                    'type'  => 'input',
                    'name'  => $f,
                    'value' => $this->data[$f],
                ]
            );
        }

        $countries = [];
        foreach ($this->data['countries'] as $country) {
            $countries[$country['country_id']] = $country['name'];
        }
        if (!$this->data['payment_country_id']) {
            $this->data['payment_country_id'] = $this->config->get('config_country_id');
        }

        $this->data['form']['payment_fields']['country'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'payment_country_id',
                'value'   => $this->data['payment_country_id'],
                'options' => $countries,
                'style'   => 'no-save',
            ]
        );

        $this->data['form']['payment_fields']['zone'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'payment_zone_id',
                'value'   => '',
                'options' => [],
                'style'   => 'no-save',
            ]
        );

        $this->data['full_payment_address'] = addslashes(
            $this->data['payment_address_1']
            .' '.$this->data['payment_address_2']
            .', '.$this->data['payment_city']
            .', '.$this->data['payment_zone']
            .', '.$this->data['payment_postcode']
            .', '.$this->data['payment_country']
        );
    }

    public function history()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->data = [];
        $this->document->setTitle($this->language->get('heading_title'));

        if ($this->request->is_POST() && $this->_validateForm()) {
            $this->model_sale_order->addOrderHistory($this->request->get['order_id'], $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            redirect(
                $this->html->getSecureURL(
                    'sale/order/history',
                    '&order_id='.$this->request->get['order_id']
                )
            );
        }

        if (isset($this->request->get['order_id'])) {
            $order_id = (int) $this->request->get['order_id'];
        } else {
            $order_id = 0;
        }

        $order_info = $this->model_sale_order->getOrder($order_id);

        if (empty($order_info)) {
            $this->session->data['error'] = $this->language->get('error_order_load');
            redirect($this->html->getSecureURL('sale/order'));
        }

        //set content language to order language ID.
        if ($this->language->getContentLanguageID() != $order_info['language_id']) {
            //reset content language
            $this->language->setCurrentContentLanguage($order_info['language_id']);
        }

        $this->document->initBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('sale/order'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('sale/order/details', '&order_id='.$order_id),
                'text'      => $this->language->get('heading_title').' #'.$order_id,
                'separator' => ' :: ',
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('sale/order/history', '&order_id='.$order_id),
                'text'      => $this->language->get('tab_history'),
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }

        $this->loadModel('localisation/order_status');
        $results = $this->model_localisation_order_status->getOrderStatuses();
        $statuses = ['' => $this->language->get('text_select_status'),];
        foreach ($results as $item) {
            $statuses[$item['order_status_id']] = $item['name'];
        }

        $this->data['order_id'] = $order_id;
        $this->data['invoice_url'] = $this->html->getSecureURL('sale/invoice', '&order_id='.$order_id);
        $this->data['button_invoice'] = $this->html->buildButton(
            [
                'name'  => 'invoice',
                'text'  => $this->language->get('text_invoice'),
                'style' => 'button3',
            ]
        );
        $this->data['order_history'] = $this->html->getSecureURL('sale/order_history');
        $this->data['cancel'] = $this->html->getSecureURL('sale/order');

        $this->_initTabs('history');

        $this->data['action'] = $this->html->getSecureURL('sale/order/history', '&order_id='.$order_id);
        $this->data['form_title'] = $this->language->get('text_edit').' '.$this->language->get('tab_history');
        $form = new AForm('ST');

        $form->setForm(
            [
                'form_name' => 'orderFrm',
                'update'    => $this->data['update'],
            ]
        );

        $this->data['form']['id'] = 'orderFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'orderFrm',
                'attr'   => 'data-confirm-exit="true" class="aform form-horizontal"',
                'action' => $this->data['action'],
            ]
        );
        $this->data['form']['submit'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'submit',
                'text'  => $this->language->get('button_add_history'),
                'style' => 'button1',
            ]
        );
        $this->data['form']['cancel'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'cancel',
                'text'  => $this->language->get('button_cancel'),
                'style' => 'button2',
            ]
        );

        $this->data['form']['fields']['order_status'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'order_status_id',
                'value'   => $order_info['order_status_id'],
                'options' => $statuses,
            ]
        );
        $this->data['form']['fields']['notify'] = $form->getFieldHtml(
            [
                'type'    => 'checkbox',
                'name'    => 'notify',
                'value'   => 1,
                'checked' => false,
                'style'   => 'btn_switch',
            ]
        );
        $this->data['form']['fields']['append'] = $form->getFieldHtml(
            [
                'type'  => 'checkbox',
                'name'  => 'append',
                'value' => 1,
                'style' => 'btn_switch',
            ]
        );
        $this->data['form']['fields']['comment'] = $form->getFieldHtml(
            [
                'type'  => 'textarea',
                'name'  => 'comment',
                'style' => 'large-field',
            ]
        );

        $this->data['histories'] = [];
        $results = $this->model_sale_order->getOrderHistory($this->request->get['order_id']);
        foreach ($results as $result) {
            $this->data['histories'][] = [
                'date_added' => dateISO2Display(
                    $result['date_added'],
                    $this->language->get('date_format_short').' '.$this->language->get('time_format')
                ),
                'status'     => $result['status'],
                'comment'    => nl2br($result['comment']),
                'notify'     => $result['notify'] ? $this->language->get('text_yes') : $this->language->get('text_no'),
            ];
        }

        $this->addChild('pages/sale/order_summary', 'summary_form', 'pages/sale/order_summary.tpl');

        $this->view->assign('help_url', $this->gen_help_url('order_history'));
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/sale/order_history.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function payment_details()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('sale/order');

        $this->data = [];

        $this->document->setTitle($this->language->get('title_payment_details'));

        $order_id = (int) $this->request->get['order_id'];
        $this->data['order_id'] = $order_id;

        $order_info = $this->model_sale_order->getOrder($order_id);
        $this->data['order_info'] = $order_info;

        if (empty($order_info)) {
            $this->session->data['error'] = $this->language->get('error_order_load');
            redirect($this->html->getSecureURL('sale/order'));
        }

        //set content language to order language ID.
        if ($this->language->getContentLanguageID() != $order_info['language_id']) {
            //reset content language
            $this->language->setCurrentContentLanguage($order_info['language_id']);
        }

        $this->document->initBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('sale/order'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('sale/order/payment_details', '&order_id='.$order_id),
                'text'      => $this->language->get('title_payment_details').' #'.$order_info['order_id'],
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        $this->data['invoice_url'] = $this->html->getSecureURL('sale/invoice', '&order_id='.$order_id);
        $this->_initTabs('payment_details');

        //NOTE: This is an empty controller to be hooked from extensions

        if ($this->session->data['error']) {
            $this->data['error_warning'] = $this->session->data['error'];
            unset($this->session->data['error']);
        }
        $this->view->batchAssign($this->data);

        $this->addChild('pages/sale/order_summary', 'summary_form', 'pages/sale/order_summary.tpl');
        $this->view->assign('help_url', $this->gen_help_url('order_history'));
        $this->processTemplate('pages/sale/order_payment_details.tpl');

        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    private function _validateForm()
    {
        if (!$this->user->canModify('sale/order')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $this->extensions->hk_ValidateData($this);

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    private function _initTabs($active)
    {
        $this->data['active'] = $active;
        //load tabs controller
        $tabs_obj = $this->dispatch('pages/sale/order_tabs', [$this->data]);
        $this->data['order_tabs'] = $tabs_obj->dispatchGetOutput();
    }

    public function files()
    {
        $this->data = [];
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->document->setTitle($this->language->get('heading_title'));

        if (has_value($this->session->data['error'])) {
            $this->data['error']['warning'] = $this->session->data['error'];
            unset($this->session->data['error']);
        }

        if (isset($this->request->get['order_id'])) {
            $order_id = $this->request->get['order_id'];
        } else {
            $order_id = 0;
        }

        if ($this->request->is_POST() && $this->_validateForm()) {
            if (has_value($this->request->post['downloads'])) {
                $data = $this->request->post['downloads'];
                $this->loadModel('catalog/download');
                foreach ($data as $order_download_id => $item) {
                    if (isset($item['expire_date'])) {
                        $item['expire_date'] = $item['expire_date']
                            ? dateDisplay2ISO(
                                $item['expire_date'],
                                $this->language->get('date_format_short')
                            )
                            : '';
                    }
                    $this->model_catalog_download->editOrderDownload($order_download_id, $item);
                }
            }
            //add download to order
            if (has_value($this->request->post['push'])) {
                $this->load->library('json');
                foreach ($this->request->post['push'] as $order_product_id => $download_id) {
                    if ($download_id) {
                        $download_info = $this->download->getDownloadInfo($download_id);
                        $download_info['attributes_data'] = serialize(
                            $this->download->getDownloadAttributesValues(
                                $download_id
                            )
                        );
                        $this->download->addProductDownloadToOrder($order_product_id, $order_id, $download_info);
                    }
                }
            }

            $this->session->data['success'] = $this->language->get('text_success');
            redirect(
                $this->html->getSecureURL(
                    'sale/order/files',
                    '&order_id='.$this->request->get['order_id']
                )
            );
        }

        $order_info = $this->model_sale_order->getOrder($order_id);
        $this->data['order_info'] = $order_info;

        //set content language to order language ID.
        if ($this->language->getContentLanguageID() != $order_info['language_id']) {
            //reset content language
            $this->language->setCurrentContentLanguage($order_info['language_id']);
        }

        if (empty($order_info)) {
            $this->session->data['error'] = $this->language->get('error_order_load');
            redirect($this->html->getSecureURL('sale/order'));
        }

        $this->document->initBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('sale/order'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL(
                    'sale/order/files',
                    '&order_id='.$this->request->get['order_id']
                ),
                'text'      => $this->language->get('heading_title').' #'.$order_info['order_id'],
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }

        $this->data['heading_title'] = $this->language->get('heading_title').' #'.$order_info['order_id'];
        $this->data['token'] = $this->session->data['token'];
        $this->data['invoice_url'] = $this->html->getSecureURL(
            'sale/invoice',
            '&order_id='.(int) $this->request->get['order_id']
        );
        $this->data['button_invoice'] = $this->html->buildButton(
            [
                'name' => 'btn_invoice',
                'text' => $this->language->get('text_invoice'),
            ]
        );
        $this->data['invoice_generate'] = $this->html->getSecureURL('sale/invoice/generate');
        $this->data['category_products'] = $this->html->getSecureURL('product/product/category');
        $this->data['product_update'] = $this->html->getSecureURL('catalog/product/update');
        $this->data['order_id'] = $this->request->get['order_id'];
        $this->data['action'] = $this->html->getSecureURL(
            'sale/order/files',
            '&order_id='.$this->request->get['order_id']
        );
        $this->data['cancel'] = $this->html->getSecureURL('sale/order');

        $this->_initTabs('files');

        $this->loadModel('localisation/order_status');
        $status = $this->model_localisation_order_status->getOrderStatus($order_info['order_status_id']);
        if ($status) {
            $this->data['order_status'] = $status['name'];
        } else {
            $this->data['order_status'] = '';
        }

        $this->loadModel('sale/customer_group');
        $customer_group_info = $this->model_sale_customer_group->getCustomerGroup($order_info['customer_group_id']);
        if ($customer_group_info) {
            $this->data['customer_group'] = $customer_group_info['name'];
        } else {
            $this->data['customer_group'] = '';
        }

        $this->data['form_title'] = $this->language->get('edit_title_files');
        $this->data['update'] = $this->html->getSecureURL(
            'listing_grid/order/update_field',
            '&id='.$this->request->get['order_id']
        );
        $form = new AForm('HS');

        $form->setForm(
            [
                'form_name' => 'orderFrm',
                'update'    => $this->data['update'],
            ]
        );

        $this->data['form']['id'] = 'orderFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'orderFrm',
                'attr'   => 'data-confirm-exit="true" class="aform form-horizontal"',
                'action' => $this->data['action'],
            ]
        );
        $this->data['form']['submit'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'submit',
                'text'  => $this->language->get('button_save'),
                'style' => 'button1',
            ]
        );
        $this->data['form']['cancel'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'cancel',
                'text'  => $this->language->get('button_cancel'),
                'style' => 'button2',
            ]
        );

        $this->loadModel('catalog/download');
        $all_downloads = $this->model_catalog_download->getDownloads();

        $options = ['' => $this->language->get('text_push_download')];
        foreach ($all_downloads as $d) {
            $options[$d['download_id']] = $d['name'].' ('.$d['mask'].')';
        }

        $this->addChild('pages/sale/order_summary', 'summary_form', 'pages/sale/order_summary.tpl');

        /** ORDER DOWNLOADS */
        $this->data['downloads'] = [];
        $order_downloads = $this->model_sale_order->getOrderDownloads($this->request->get['order_id']);

        if ($order_downloads) {
            //get thumbnails by one pass
            $resource = new AResource('image');
            $thumbnails = $resource->getMainThumbList(
                'products',
                array_keys($order_downloads),
                $this->config->get('config_image_grid_width'),
                $this->config->get('config_image_grid_height')
            );

            $this->loadModel('catalog/download');
            foreach ($order_downloads as $product_id => $order_download) {
                $downloads = (array) $order_download['downloads'];
                $this->data['order_downloads'][$product_id]['product_name'] = $order_download['product_name'];
                $this->data['order_downloads'][$product_id]['product_thumbnail'] = $thumbnails[$product_id];

                foreach ($downloads as $download_info) {
                    $download_info['order_status_id'] = $order_info['order_status_id'];
                    $attributes = $this->download->getDownloadAttributesValuesForDisplay($download_info['download_id']);
                    $is_file = $this->download->isFileAvailable($download_info['filename']);
                    foreach ($download_info['download_history'] as &$h) {
                        $h['time'] = dateISO2Display(
                            $h['time'],
                            $this->language->get('date_format_short').' '.$this->language->get('time_format')
                        );
                    }
                    unset($h);

                    $status_text = $this->model_catalog_download->getTextStatusForOrderDownload($download_info);

                    if ($status_text) {
                        $status = $status_text;
                    } else {
                        $status = $form->getFieldHtml(
                            [
                                'type'  => 'checkbox',
                                'name'  => 'downloads['.(int) $download_info['order_download_id'].'][status]',
                                'value' => $download_info['status'],
                                'style' => 'btn_switch',
                            ]
                        );
                    }

                    $this->data['order_downloads'][$product_id]['downloads'][] = [
                        'name'             => $download_info['name'],
                        'attributes'       => $attributes,
                        'href'             => $this->html->getSecureURL(
                            'catalog/product_files',
                            '&product_id='.$product_id.'&download_id='.$download_info['download_id']
                        ),
                        'resource'         => $download_info['filename'],
                        'is_file'          => $is_file,
                        'mask'             => $download_info['mask'],
                        'status'           => $status,
                        'remaining'        => $form->getFieldHtml(
                            [
                                'type'        => 'input',
                                'name'        =>
                                    'downloads['.(int) $download_info['order_download_id'].'][remaining_count]',
                                'value'       => $download_info['remaining_count'],
                                'placeholder' => '-',
                                'style'       => 'small-field',
                            ]
                        ),
                        'expire_date'      => $form->getFieldHtml(
                            [
                                'type'       => 'date',
                                'name'       => 'downloads['.(int) $download_info['order_download_id'].'][expire_date]',
                                'value'      =>
                                    ($download_info['expire_date']
                                        ? dateISO2Display($download_info['expire_date'])
                                        : ''),
                                'default'    => '',
                                'dateformat' => format4Datepicker($this->language->get('date_format_short')),
                                'highlight'  => 'future',
                                'style'      => 'medium-field',
                            ]
                        ),
                        'download_history' => $download_info['download_history'],
                    ];
                    $this->data['order_downloads'][$product_id]['push_download'] = $form->getFieldHtml(
                        [
                            'type'        => 'selectbox',
                            'name'        => 'push['.(int) $download_info['order_download_id'].']',
                            'value'       => '',
                            'options'     => $options,
                            'style'       => 'chosen no-save',
                            'placeholder' => $this->language->get('text_push_download'),
                        ]
                    );
                }
            }
        } else {
            redirect(
                $this->html->getSecureURL(
                    'sale/order/details',
                    '&order_id='.$this->request->get['order_id']
                )
            );
        }

        $this->view->batchAssign($this->data);
        $this->view->assign('help_url', $this->gen_help_url('order_files'));

        $this->processTemplate('pages/sale/order_files.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    /**
     * Response controller to recalculate an order in admin
     * IMPORTANT: To prevent conflict of models, call independently
     *
     * @void
     */
    public function recalc()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $order_id = $this->request->get['order_id'];
        $skip_recalc = [];
        $new_totals = [];

        if (!$this->user->canModify('sale/order')) {
            $this->session->data['error'] = $this->language->get('error_permission');
            return 0;
        } else {
            if (!has_value($order_id)) {
                $this->session->data['error'] = "Missing required details";
                return 0;
            }
        }

        //do we have to skip recalc for some totals?
        if ($this->request->get['skip_recalc']) {
            $enc = new AEncryption($this->config->get('encryption_key'));
            $skip_recalc = unserialize($enc->decrypt($this->request->get['skip_recalc']));
        }
        //do we have total values passed?
        if ($this->request->post['totals']) {
            $new_totals = $this->request->post['totals'];
        }

        //do we need to add new total record?
        /**
         * @var $adm_order_mdl ModelSaleOrder
         */
        $adm_order_mdl = $this->load->model('sale/order');
        if ($this->request->post['key'] && $order_id) {
            $new_total = $this->request->post;
            $order_total_id = $adm_order_mdl->addOrderTotal($order_id, $new_total);
            $skip_recalc[] = $order_total_id;
        }

        $order = new AOrderManager($order_id);
        //Recalc. If total has changed from original, update and create a log to order history
        $t_ret = $order->recalcTotals($skip_recalc, $new_totals);
        if (!$t_ret || $t_ret['error']) {
            $this->session->data['error'] = "Error recalculating totals! ".$t_ret['error'];
        } else {
            $this->session->data['success'] = $this->language->get('text_success');
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        redirect($this->html->getSecureURL('sale/order/details', '&order_id='.$order_id));
    }

    public function delete_total()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $order_id = $this->request->get['order_id'];
        $order_total_id = $this->request->get['order_total_id'];

        if (has_value($order_id) && has_value($order_total_id)) {
            /**
             * @var $adm_order_mdl ModelSaleOrder
             */
            $adm_order_mdl = $this->load->model('sale/order');
            $original_totals = $adm_order_mdl->getOrderTotals($order_id);
            $tobe_deleted = [];
            foreach ($original_totals as $total) {
                if ($order_total_id == $total['order_total_id']) {
                    $tobe_deleted = $total;
                    break;
                }
            }
            if (empty($tobe_deleted)) {
                $this->session->data['error'] = "Error deleting total!";
            } else {
                $adm_order_mdl->deleteOrderTotal($order_id, $order_total_id);
                //recalculate order total
                $order = new AOrderManager($order_id);
                $t_ret = $order->recalcTotals();
                if (!$t_ret || $t_ret['error']) {
                    $this->session->data['error'] = "Error recalculating totals! ".$t_ret['error'];
                } else {
                    $this->session->data['success'] = $this->language->get('text_success');
                    $this->session->data['attention'] = $this->language->get('attention_check_total');
                }
            }
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        redirect($this->html->getSecureURL('sale/order/details', '&order_id='.$order_id));
    }
}