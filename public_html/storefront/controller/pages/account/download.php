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

class ControllerPagesAccountDownload extends AController
{
    public $data = array();

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        //if disabled downloads redirect to
        if (!$this->config->get('config_download')) {
            redirect($this->html->getSecureURL('account/account'));
        }
        // when guest checkout downloads
        $this->loadModel('account/customer');
        $guest = false;
        $order_token = $this->request->get['ot'];
        if ($order_token) {
            list($order_id, $email) = $this->model_account_customer->parseOrderToken($order_token);
            if ($order_id && $email) {
                $guest = true;
            }
        }

        if (!$this->customer->isLogged() && !$guest) {
            $this->session->data['redirect'] = $this->html->getSecureURL('account/download');
            redirect($this->html->getSecureURL('account/login'));
        }

        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->resetBreadcrumbs();
        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getHomeURL(),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
        ));
        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL('account/account'),
            'text'      => $this->language->get('text_account'),
            'separator' => $this->language->get('text_separator'),
        ));

        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL('account/download'),
            'text'      => $this->language->get('text_downloads'),
            'separator' => $this->language->get('text_separator'),
        ));

        if (isset($this->request->get['limit'])) {
            $limit = (int)$this->request->get['limit'];
            $limit = $limit > 50 ? 50 : $limit;
        } else {
            $limit = $this->config->get('config_catalog_limit');
        }

        if ($this->config->get('config_download')) {
            if (isset($this->request->get['page'])) {
                $page = $this->request->get['page'];
            } else {
                $page = 1;
            }
            $downloads = array();
            //get only enabled, not expired, which have remaining count > 0 and available
            if ($guest) {
                $customer_downloads = $this->download->getCustomerOrderDownloads($order_id, 0);
            } else {
                $customer_downloads = $this->download->getCustomerDownloads(($page - 1) * $limit, $limit);
            }
            $product_ids = array();
            foreach ($customer_downloads as $result) {
                $product_ids[] = (int)$result['product_id'];
            }
            $resource = new AResource('image');
            $thumbnails = $resource->getMainThumbList(
                'products',
                $product_ids,
                $this->config->get('config_image_cart_width'),
                $this->config->get('config_image_cart_height'),
                false);
            $suffix = array(
                'B',
                'KB',
                'MB',
                'GB',
                'TB',
                'PB',
                'EB',
                'ZB',
                'YB',
            );

            foreach ($customer_downloads as $download_info) {
                $text_status = $this->download->getTextStatusForOrderDownload($download_info);
                $size = filesize(DIR_RESOURCE.$download_info['filename']);
                $i = 0;
                while (($size / 1024) > 1) {
                    $size = $size / 1024;
                    $i++;
                }

                $download_text = $download_button = '';
                if (!$text_status) {
                    $download_button = $this->html->buildElement(
                        array(
                            'type'  => 'button',
                            'name'  => 'download_button_'.$download_info['order_download_id'],
                            'title' => $this->language->get('text_download'),
                            'text'  => $this->language->get('text_download'),
                            'style' => 'button',
                            'href'  => $this->html->getSecureURL(
                                'account/download/startdownload',
                                '&order_download_id='.$download_info['order_download_id']
                                .($guest ? '&ot='.$order_token : '')),
                            'icon'  => 'fa fa-download-alt',
                        )
                    );
                } else {
                    $download_text = $text_status;
                }

                $thumbnail = $thumbnails[$download_info['product_id']];
                $attributes = $this->download->getDownloadAttributesValuesForCustomer($download_info['download_id']);

                $downloads[] = array(
                    'thumbnail'   => $thumbnail,
                    'attributes'  => $attributes,
                    'order_id'    => $download_info['order_id'],
                    'date_added'  => dateISO2Display($download_info['date_added'], $this->language->get('date_format_short')),
                    'name'        => $download_info['name'],
                    'remaining'   => $download_info['remaining_count'],
                    'size'        => round(substr($size, 0, strpos($size, '.') + 4), 2).$suffix[$i],
                    'button'      => $download_button,
                    'text'        => $download_text,
                    'expire_date' => dateISO2Display($download_info['expire_date'], $this->language->get('date_format_short').' '.$this->language->get('time_format_short')),
                );

            }

            $this->data['downloads'] = $downloads;

            $this->data['pagination_bootstrap'] = $this->html->buildElement(
                array(
                    'type'       => 'Pagination',
                    'name'       => 'pagination',
                    'text'       => $this->language->get('text_pagination'),
                    'text_limit' => $this->language->get('text_per_page'),
                    'total'      => $this->download->getTotalDownloads(),
                    'page'       => $page,
                    'limit'      => $limit,
                    'url'        => $this->html->getURL('account/download&limit='.$limit.'&page={page}', '&encode'),
                    'style'      => 'pagination',
                )
            );

            if ($downloads) {
                $template = 'pages/account/download.tpl';
            } else {
                $template = 'pages/error/not_found.tpl';
            }
        } else {
            $template = 'pages/error/not_found.tpl';
        }

        $continue = HtmlElementFactory::create(array(
            'type'  => 'button',
            'name'  => 'continue_button',
            'text'  => $this->language->get('button_continue'),
            'style' => 'button',
            'icon'  => 'fa fa-arrow-right',
            'href'  => $this->html->getSecureURL('account/account'),
        ));
        $this->data['button_continue'] = $continue;

        if ($this->session->data['warning']) {
            $this->data['error_warning'] = $this->session->data['warning'];
            unset($this->session->data['warning']);
        }
        $this->view->batchAssign($this->data);
        $this->processTemplate($template);

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function startdownload()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $download_id = (int)$this->request->get['download_id'];
        $order_download_id = (int)$this->request->get['order_download_id'];

        if (!$this->config->get('config_download')) {
            redirect($this->html->getSecureURL('account/account'));
        }

        $can_access = false;
        $download_info = array();

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
            elseif (!$this->customer->isLogged() && isset($this->request->get['ot']) && $this->config->get('config_guest_checkout')) {
                //try to decrypt order token
                $order_token = $this->request->get['ot'];
                if ($order_token) {
                    $this->load->model('account/customer');
                    list($order_id, $email) = $this->model_account_customer->parseOrderToken($order_token);
                    if ($order_id && $email) {
                        $order_downloads = $this->download->getCustomerOrderDownloads($order_id, 0);
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

        //if can access and info presents - retrieve file and output 
        if ($can_access && $download_info && is_array($download_info)) {
            //if it's ok - send file and exit, otherwise do nothing
            $this->download->sendDownload($download_info);
        }

        $this->session->data['warning'] = $this->language->get('error_download_not_exists');
        redirect($this->html->getSecureURL('account/download'));
    }

}