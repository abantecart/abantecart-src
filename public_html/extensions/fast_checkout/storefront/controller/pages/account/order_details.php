<?php
/** @noinspection PhpUndefinedClassInspection */

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

/**
 * @property ModelExtensionFastCheckout $model_extension_fast_checkout
 */
class ControllerPagesAccountOrderDetails extends AController
{
    public $error = [];

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $order_id = 0;
        $order_token = '';
        $this->data['guest'] = $guest = false;

        $this->loadModel('account/order');
        $this->loadModel('extension/fast_checkout');

        //validate input and re-route
        if ($this->customer->isLogged()) {
            //logged in customer, missing order ID?
            $order_id = (int) $this->request->get['order_id'];
            if (!$order_id) {
                redirect($this->html->getSecureURL('account/history'));
                return;
            }
            $order_info = $this->model_account_order->getOrder($order_id);
        } else {
            if (isset($this->request->get['ot']) && $this->config->get('config_guest_checkout')) {
                //try to decrypt order token
                $order_token = $this->request->get['ot'];
                list($order_id, $email) = $this->model_extension_fast_checkout->parseOrderToken($order_token);
                if ($order_id && $email) {
                    $this->data['order_token'] = $order_token;
                    $this->data['guest'] = $guest = true;
                    $order_info = $this->model_account_order->getOrder($order_id, '', 'view');
                } else {
                    redirect($this->html->getSecureURL('account/history'));
                    return;
                }
            } else {
                //redirect to login
                if (!$this->customer->isLogged() && $order_id) {
                    $this->session->data['redirect'] = $this->html->getSecureURL(
                        'account/order_details',
                        '&order_id='.$order_id
                    );
                    redirect($this->html->getSecureURL('account/login'));
                    return;
                } else {
                    redirect($this->html->getSecureURL('account/history'));
                    return;
                }
            }
        }

        $this->loadModel('account/customer');
        $this->loadLanguage('fast_checkout/fast_checkout');
        $this->loadLanguage('account/invoice');

        $this->view->assign('error', $this->error);

        $this->document->setTitle($this->language->get('heading_title'));

        $this->document->resetBreadcrumbs();

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getHomeURL(),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('account/account'),
                'text'      => $this->language->get('text_account'),
                'separator' => $this->language->get('text_separator'),
            ]
        );
        if (!$guest) {
            $this->document->addBreadcrumb(
                [
                    'href'      => $this->html->getSecureURL('account/history'),
                    'text'      => $this->language->get('text_history'),
                    'separator' => $this->language->get('text_separator'),
                ]
            );
        }

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('account/invoice', '&order_id='.$order_id),
                'text'      => $this->language->get('text_invoice'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        $this->data['success'] = '';
        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        if ($order_info) {
            $this->data['order_id'] = $order_id;
            $this->data['invoice_id'] = $order_info['invoice_id']
                ? $order_info['invoice_prefix'].$order_info['invoice_id']
                : '';

            $this->data['email'] = $order_info['email'];
            $this->data['telephone'] = $order_info['telephone'];
            $this->data['mobile_phone'] = $this->im->getCustomerURI('sms', (int) $order_info['customer_id'], $order_id);
            $this->data['fax'] = $order_info['fax'];
            $this->data['status'] = $this->model_account_order->getOrderStatus($order_id);

            $shipping_data = [
                'firstname' => $order_info['shipping_firstname'],
                'lastname'  => $order_info['shipping_lastname'],
                'company'   => $order_info['shipping_company'],
                'address_1' => $order_info['shipping_address_1'],
                'address_2' => $order_info['shipping_address_2'],
                'city'      => $order_info['shipping_city'],
                'postcode'  => $order_info['shipping_postcode'],
                'zone'      => $order_info['shipping_zone'],
                'zone_code' => $order_info['shipping_zone_code'],
                'country'   => $order_info['shipping_country'],
            ];

            $this->data['shipping_address'] = $this->customer->getFormattedAddress(
                $shipping_data,
                $order_info['shipping_address_format']
            );
            $this->data['shipping_method'] = $order_info['shipping_method'];

            $payment_data = [
                'firstname' => $order_info['payment_firstname'],
                'lastname'  => $order_info['payment_lastname'],
                'company'   => $order_info['payment_company'],
                'address_1' => $order_info['payment_address_1'],
                'address_2' => $order_info['payment_address_2'],
                'city'      => $order_info['payment_city'],
                'postcode'  => $order_info['payment_postcode'],
                'zone'      => $order_info['payment_zone'],
                'zone_code' => $order_info['payment_zone_code'],
                'country'   => $order_info['payment_country'],
            ];

            $this->data['payment_address'] = $this->customer->getFormattedAddress(
                $payment_data,
                $order_info['payment_address_format']
            );
            $this->data['payment_method'] = $order_info['payment_method'];

            $products = [];
            $order_products = $this->model_account_order->getOrderProducts($order_id);
            $product_ids = array_column($order_products, 'product_id');

            //get thumbnails by one pass
            $resource = new AResource('image');
            $thumbnails = $product_ids
                ? $resource->getMainThumbList(
                    'products',
                    $product_ids,
                    $this->config->get('config_image_cart_width'),
                    $this->config->get('config_image_cart_width'),
                    false
                )
            : [];

            foreach ($order_products as $product) {
                $options = $this->model_account_order->getOrderOptions($order_id, $product['order_product_id']);
                $thumbnail = $thumbnails[$product['product_id']];

                $option_data = $option = [];
                foreach ($options as $option) {
                    if ($option['element_type'] == 'H') {
                        continue;
                    } //hide hidden options

                    $value = $option['value'];
                    $title = '';
                    // hide binary value for checkbox
                    if ($option['element_type'] == 'C' && in_array($value, [0, 1])) {
                        $value = '';
                    }
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
                    'id'               => (int) $product['product_id'],
                    'order_product_id' => (int) $product['order_product_id'],
                    'thumbnail'        => $thumbnail,
                    'name'             => $product['name'],
                    'model'            => $product['model'],
                    'option'           => $option_data,
                    'quantity'         => $product['quantity'],
                    'price'            => $this->currency->format(
                                                                    $product['price'],
                                                                    $order_info['currency'],
                                                                    $order_info['value']
                                                                ),
                    'total'            => $this->currency->format(
                                                                    $product['total'],
                                                                    $order_info['currency'],
                                                                    $order_info['value']
                                                                ),
                    'url'              => $this->html->getSEOURL('product/product', '&product_id='.$product['product_id'])
                ];
            }
            $this->data['products'] = $products;
            $this->data['totals'] = $this->model_account_order->getOrderTotals($order_id);
            $this->data['comment'] = $order_info['comment'];

            $histories = [];
            $results = $this->model_account_order->getOrderHistories($order_id);
            foreach ($results as $result) {
                $histories[] = [
                    'date_added' => dateISO2Display(
                        $result['date_added'],
                        $this->language->get('date_format_short').' '.$this->language->get('time_format')
                    ),
                    'status'     => $result['status'],
                    'comment'    => nl2br($result['comment']),
                ];
            }
            $this->data['histories'] = $histories;

            if ($guest) {
                $this->data['continue'] = $this->html->getHomeURL();
            } else {
                $this->data['continue'] = $this->html->getSecureURL('account/history');
            }

            $this->data['button_print'] = $this->html->buildElement(
                [
                    'type'  => 'button',
                    'name'  => 'print_button',
                    'text'  => $this->language->get('button_print'),
                    'icon'  => 'fa fa-print',
                    'style' => 'button',
                ]
            );

            //button for order cancellation
            if ($this->config->get('config_customer_cancelation_order_status_id')) {
                $order_cancel_ids = unserialize($this->config->get('config_customer_cancelation_order_status_id'));
                if (in_array($order_info['order_status_id'], $order_cancel_ids)) {
                    $this->data['button_order_cancel'] = $this->html->buildElement(
                        [
                            'type'  => 'button',
                            'name'  => 'button_order_cancelation',
                            'text'  => $this->language->get('text_order_cancelation'),
                            'icon'  => 'fa fa-ban',
                            'style' => 'button',
                        ]
                    );
                    if (!$guest) {
                        $this->data['order_cancelation_url'] = $this->html->getSecureURL(
                            'account/invoice/CancelOrder',
                            '&order_id='.$order_id
                        );
                    } else {
                        $this->data['order_cancelation_url'] = $this->html->getSecureURL(
                            'account/invoice/CancelOrder',
                            '&ot='.$order_token
                        );
                    }
                }
            }

            //get downloads if we have them?
            $this->_build_download_list($order_id);

            if ($this->config->get('embed_mode') == true) {
                //load special headers
                $this->addChild('responses/embed/head', 'head');
                $this->addChild('responses/embed/footer', 'footer');
                $this->view->setTemplate('embed/account/order_details.tpl');
            } else {
                $this->view->setTemplate('pages/account/order_details.tpl');
            }
        } else {
            if ($guest) {
                $this->data['continue'] = $this->html->getHomeURL();
            } else {
                $this->data['continue'] = $this->html->getSecureURL('account/account');
            }
            $this->view->setTemplate('pages/error/not_found.tpl');
        }

        $this->data['button_continue'] = $this->html->buildElement(
            [
                'type'  => 'button',
                'name'  => 'continue_button',
                'text'  => $this->language->get('button_continue'),
                'icon'  => 'fa fa-arrow-right',
                'style' => 'button',
            ]
        );

        $this->view->batchAssign($this->data);
        $this->processTemplate();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    /**
     * @param int $order_id
     *
     * @throws AException
     */
    protected function _build_download_list($order_id)
    {
        if (!$this->config->get('config_download')) {
            return;
        }

        $downloads = [];
        //get only enabled, not expired, which have remaining count > 0 and available
        if ($this->data['guest']) {
            $customer_downloads = $this->model_extension_fast_checkout->getCustomerOrderDownloads($order_id, 0);
        } else {
            $customer_downloads = $this->model_extension_fast_checkout->getCustomerOrderDownloads(
                $order_id,
                $this->customer->getId()
            );
        }

        if (!$customer_downloads) {
            return;
        }

        $this->data['text_name'] = $this->language->get('text_name', 'account/download');
        $this->data['fast_checkout_text_order_downloads'] = $this->language->get('fast_checkout_text_order_downloads');
        $this->data['text_date_added'] = $this->language->get('text_date_added', 'account/download');

        $suffix = [
            'B',
            'KB',
            'MB',
            'GB',
            'TB',
            'PB',
            'EB',
            'ZB',
            'YB',
        ];

        foreach ($customer_downloads as $download_info) {
            $text_status = $this->download->getTextStatusForOrderDownload($download_info);
            if (is_numeric($download_info['filename'])) {
                $rl = new AResource('download');
                $resource = $rl->getResource($download_info['filename']);
                $download_info['filename'] = $rl->getTypeDir().$resource['resource_path'];
            }
            $size = filesize(DIR_RESOURCE.$download_info['filename']);
            $i = 0;

            while (($size / 1024) > 1) {
                $size = $size / 1024;
                $i++;
            }

            $download_text = $download_button = '';

            if (!$text_status) {
                $download_button = $this->html->buildElement(
                    [
                        'type'  => 'button',
                        'name'  => 'download_button_'.$download_info['order_download_id'],
                        'title' => $this->language->get('text_download'),
                        'text'  => $this->language->get('text_download'),
                        'style' => 'button',
                        'href'  => $this->html->getSecureURL(
                            'account/order_details/startdownload',
                            '&order_download_id='.$download_info['order_download_id']
                            .($this->data['guest'] ? '&ot='.$this->data['order_token'] : '')
                        ),
                        'icon'  => 'fa fa-download-alt',
                    ]
                );
            } else {
                $download_text = $text_status;
            }

            $attributes = $this->download->getDownloadAttributesValuesForCustomer($download_info['download_id']);
            $downloads[] = [
                'attributes'  => $attributes,
                'order_id'    => $download_info['order_id'],
                'date_added'  => dateISO2Display(
                    $download_info['date_added'],
                    $this->language->get('date_format_short')
                ),
                'name'        => $download_info['name'],
                'remaining'   => $download_info['remaining_count'],
                'size'        => round(substr($size, 0, strpos($size, '.') + 4), 2).$suffix[$i],
                'button'      => $download_button,
                'text'        => $download_text,
                'expire_date' => dateISO2Display(
                    $download_info['expire_date'],
                    $this->language->get('date_format_short').' '.$this->language->get('time_format_short')
                ),
            ];
        }
        $this->data['downloads'] = $downloads;
        $this->data['text_remaining'] = $this->language->get('text_remaining');
        $this->data['text_expire_date'] = $this->language->get('text_expire_date');
    }

    public function startdownload()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $download_id = (int) $this->request->get['download_id'];
        $order_download_id = (int) $this->request->get['order_download_id'];

        if (!$this->config->get('config_download')) {
            redirect($this->html->getSecureURL('account/account'));
        }

        $this->loadModel('extension/fast_checkout');

        $can_access = false;
        $download_info = [];

        if ($download_id) {
            //downloads before order
            $download_info = $this->download->getDownloadinfo($download_id);
            //allow $download_id based downloads only for 'before_order' type download
            if ($download_info && $download_info['activate'] == 'before_order') {
                $can_access = true;
            }
        } else {
            if ($order_download_id && $this->customer->isLogged()) {
                //verify purchased downloads only for logged customers
                $download_info = $this->download->getOrderDownloadInfo($order_download_id);
                if ($download_info) {
                    //check is customer has requested download in his order
                    $customer_downloads = $this->download->getCustomerDownloads();
                    if (in_array($order_download_id, array_keys($customer_downloads))) {
                        $can_access = true;
                    }
                }
            } //allow download for guest customer
            else {
                if (!$this->customer->isLogged() && isset($this->request->get['ot'])
                    && $this->config->get('config_guest_checkout')) {
                    //try to decrypt order token
                    $order_token = $this->request->get['ot'];
                    if ($order_token) {
                        $this->load->model('account/customer');
                        list($order_id, $email) = $this->model_extension_fast_checkout->parseOrderToken($order_token);
                        if ($order_id && $email) {
                            $order_downloads = $this->model_extension_fast_checkout->getCustomerOrderDownloads(
                                $order_id,
                                0
                            );
                            if ($order_downloads) {
                                //check is customer has requested download in his order
                                if (in_array($order_download_id, array_keys($order_downloads))) {
                                    $can_access = true;
                                    $download_info = $order_downloads[$order_download_id];
                                }
                            }
                        }
                    }
                }
            }
        }

        //if can access and info presents - retrieve file and output
        if ($can_access && $download_info && is_array($download_info)) {
            //if it's ok - send file and exit, otherwise do nothing
            $this->download->sendDownload($download_info);
        }

        $this->session->data['warning'] = $this->language->get('error_download_not_exists');
        redirect($this->html->getSecureURL('account/download'));
    }

    public function CancelOrder()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        //Run a few checks on passed data and
        $order_id = (int) $this->request->get['order_id'];
        $customer_id = $this->customer->getId();

        $this->loadModel('account/order');

        $guest = false;
        if (isset($this->request->get['ot']) && $this->config->get('config_guest_checkout')) {
            //try to decrypt order token
            $enc = new AEncryption($this->config->get('encryption_key'));
            $decrypted = $enc->decrypt($this->request->get['ot']);
            list($order_id, $email) = explode('::', $decrypted);

            $order_id = (int) $order_id;
            if (!$decrypted || !$order_id || !$email) {
                if ($order_id) {
                    $this->session->data['redirect'] = $this->html->getSecureURL(
                        'account/invoice',
                        '&order_id='.$order_id
                    );
                }
                redirect($this->html->getSecureURL('account/login'));
            }
            $order_info = $this->model_account_order->getOrder($order_id, '', 'view');
            //compare emails
            if ($order_info['email'] != $email) {
                redirect($this->html->getSecureURL('account/login'));
            }
            $guest = true;
        } else {
            $order_info = $this->model_account_order->getOrder($order_id);
        }

        if (!$order_id && !$guest) {
            redirect($this->html->getSecureURL('account/invoice'));
        }

        if (!$customer_id && !$guest) {
            redirect($this->html->getSecureURL('account/login'));
        }

        if (!$order_info) {
            redirect($this->html->getSecureURL('account/invoice'));
        }
        //is cancellation enabled at all
        $order_cancel_ids = [];
        if ($this->config->get('config_customer_cancelation_order_status_id')) {
            $order_cancel_ids = unserialize($this->config->get('config_customer_cancelation_order_status_id'));
        }
        //is cancellation allowed for current order status
        if (!$order_cancel_ids || !in_array($order_info['order_status_id'], $order_cancel_ids)) {
            redirect($this->html->getSecureURL('account/invoice'));
        }

        //now do the changes
        $new_order_status_id = $this->order_status->getStatusByTextId('canceled_by_customer');
        if ($new_order_status_id) {
            $this->loadModel('checkout/order');
            $this->model_checkout_order->update(
                $order_id, $new_order_status_id,
                'Request an Order cancellation from Customer', true
            );
            $this->session->data['success'] = $this->language->get('text_order_cancelation_success');
            $this->messages->saveNotice(
                sprintf(
                    $this->language->get('text_order_cancelation_message_title'),
                    $order_id
                ),
                sprintf(
                    $this->language->get('text_order_cancelation_message_body'),
                    $order_info['firstname'].' '.$order_info['lastname'],
                    $order_id,
                    '#admin#rt=sale/order/details&order_id='.$order_id
                )
            );
        } else {
            //when new order status id is null by some unexpected reason - just redirect on the same page
            $this->log->write(
                'Error: Unknown cancellation order status id. '
                .'Probably integrity code problem. '
                .'Check is file /core/lib/order_status.php exists.'
            );
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        if (!$guest) {
            $url = $this->html->getSecureURL('account/invoice', '&order_id='.$order_id);
        } else {
            $url = $this->html->getSecureURL('account/invoice', '&ot='.$this->request->get['ot']);
        }

        redirect($url);
    }
}