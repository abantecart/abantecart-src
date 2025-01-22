<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ControllerPagesCatalogProductFiles extends AController
{
    public $error = [];

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('catalog/files');
        /** @var ModelCatalogProduct $pMdl */
        $pMdl = $this->loadModel('catalog/product');
        /** @var ModelCatalogDownload $mdl */
        $dMdl = $this->loadModel('catalog/download');
        $productId = (int)$this->request->get['product_id'];

        if (!$productId) {
            redirect($this->html->getSecureURL('catalog/product'));
        }
        $productInfo = $pMdl->getProduct($productId);
        if (!$productInfo) {
            $this->session->data['warning'] = $this->language->get('error_product_not_found');
            redirect($this->html->getSecureURL('catalog/product'));
        }

        //Downloads disabled. Warn user
        if (!$this->config->get('config_download')) {
            $this->error['warning'] = $this->html->convertLinks($this->language->get('error_downloads_disabled'));
        }

        if ($this->request->is_POST() && $this->_validateForm()) {
            foreach ($this->request->post['selected'] as $id) {
                $dMdl->mapDownload($id, $productId);
            }

            $this->session->data['success'] = $this->language->get('text_map_success');
            redirect($this->html->getSecureURL('catalog/product_files', '&product_id=' . $productId));
        }

        $this->view->assign('error_warning', $this->error['warning']);
        $this->view->assign('success', $this->session->data['success']);

        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
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
                'href'      => $this->html->getSecureURL('catalog/product'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
            ]
        );
        $title = $this->language->get('text_edit') . '&nbsp;'
            . $this->language->get('text_product') . ' - '
            . $productInfo['name'];
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('catalog/product/update', '&product_id=' . $productId),
                'text'      => $title,
                'separator' => ' :: ',
            ]
        );
        $this->document->setTitle($title);
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('catalog/product_files', '&product_id=' . $productId),
                'text'      => $this->language->get('tab_files'),
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        $this->data['active'] = 'files';
        //load tabs controller
        $tabs_obj = $this->dispatch('pages/catalog/product_tabs', [$this->data]);
        $this->data['product_tabs'] = $tabs_obj->dispatchGetOutput();
        unset($tabs_obj);

        $this->loadModel('catalog/download');
        $this->data['downloads'] = [];

        $this->data['product_files'] = $dMdl->getProductDownloadsDetails($productId);

        $rl = new AResource('download');
        $rl_dir = $rl->getTypeDir();
        foreach ($this->data['product_files'] as &$file) {
            $resource_id = is_numeric($file['filename'])
                ? $file['filename']
                : $rl->getIdFromHexPath(str_replace($rl_dir, '', $file['filename']));

            $resource_info = $rl->getResource($resource_id);
            $thumbnail = $rl->getResourceThumb(
                $resource_id, $this->config->get('config_image_grid_width'),
                $this->config->get('config_image_grid_height')
            );
            if ($resource_info['resource_path']) {
                $file['icon'] = $this->html->buildResourceImage(
                    [
                        'url'    => $thumbnail,
                        'width'  => $this->config->get('config_image_grid_width'),
                        'height' => $this->config->get('config_image_grid_height'),
                        'attr'   => 'alt="' . $resource_info['title'] . '"',
                    ]
                );
            } else {
                $file['icon'] = $resource_info['resource_code'];
            }

            $file['status'] = $file['status']
                ? $this->language->get('text_enabled')
                : $this->language->get('text_disabled');

            $file['button_edit'] = $this->html->buildElement(
                [
                    'type' => 'button',
                    'text' => $this->language->get('button_edit'),
                    'href' => $this->html->getSecureURL(
                        'r/product/product/buildDownloadForm',
                        '&product_id=' . $productId . '&download_id=' . $file['download_id']
                    ),
                ]
            );

            $mapList = $dMdl->getDownloadMapList($file['download_id']);
            if ((sizeof($mapList) == 1 && key($mapList) == $productId) || $file['shared'] != 1) {
                $text = $this->language->get('button_delete');
                $icon = 'fa-trash-o';
            } else {
                $text = $this->language->get('button_unmap');
                $icon = 'fa-chain-broken';
            }

            $file['button_delete'] = $this->html->buildElement(
                [
                    'type' => 'button',
                    'text' => $text,
                    'href' => $this->html->getSecureURL(
                        'catalog/product_files/delete',
                        '&product_id=' . $productId . '&download_id=' . $file['download_id']
                    ),
                    'icon' => $icon,
                ]
            );

            $orders_count = $dMdl->getTotalOrdersWithProduct($productId);
            if ($orders_count) {
                $file['push_to_customers'] = $this->html->buildElement(
                    [
                        'type'  => 'button',
                        'name'  => 'push_to_customers',
                        'text'  => sprintf($this->language->get('text_push_to_orders'), $orders_count),
                        'title' => $this->language->get('text_push'),
                        'icon'  => 'fa-share-alt-square',
                        'href'  => $this->html->getSecureURL(
                            'catalog/product_files/pushToCustomers',
                            '&product_id=' . $productId . '&download_id=' . $file['download_id']
                        ),
                        'attr'  => 'data-orders-count="' . $orders_count . '"',
                    ]
                );
            }

            if ($file['map_list']) {
                foreach ($file['map_list'] as $k => &$item) {
                    $new = [
                        'product_id' => $k,
                        'name'       => $item,
                        'url'        => $this->html->getSecureURL('catalog/product_files', '&product_id=' . $k),
                    ];
                    $item = $new;
                }
            }
        }
        unset($file);

        $this->data['button_add_file'] = $this->html->buildElement(
            [
                'type' => 'button',
                'text' => $this->language->get('text_add_file'),
                'href' => $this->html->getSecureURL(
                    'r/product/product/buildDownloadForm',
                    '&product_id=' . $productId
                )
            ]
        );
        if ($this->config->get('config_embed_status')) {
            $this->data['embed_url'] = $this->html->getSecureURL(
                'common/do_embed/product',
                '&product_id=' . $productId
            );
        }

        $this->addChild('pages/catalog/product_summary', 'summary_form', 'pages/catalog/product_summary.tpl');
        $this->view->assign('help_url', $this->gen_help_url('product_files'));
        $this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/catalog/product_files.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function _validateForm()
    {
        if (!$this->user->canModify('catalog/product_files')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['selected']) {
            $this->error['warning'] = $this->language->get('error_selected_downloads');
        }

        $this->extensions->hk_ValidateData($this);
        return !$this->error;
    }

    public function delete()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $downloadId = (int)$this->request->get['download_id'];
        $productId = (int)$this->request->get['product_id'];
        // remove
        if ($downloadId && $productId) {
            $this->loadLanguage('catalog/files');
            /** @var ModelCatalogDownload $mdl */
            $dMdl = $this->loadModel('catalog/download');

            $downloadInfo = $dMdl->getDownload($downloadId);
            $mapList = $dMdl->getDownloadMapList($downloadId);

            if ((sizeof($mapList) == 1 && key($mapList) == $productId)
                || $downloadInfo['shared'] != 1
            ) {
                $dMdl->deleteDownload($downloadId);
            } else {
                $dMdl->unmapDownload($downloadId, $productId);
            }
            $this->session->data['success'] = $this->language->get('text_success_remove');
        }
        redirect($this->html->getSecureURL('catalog/product_files', '&product_id=' . $productId));
        $this->extensions->hk_InitData($this, __FUNCTION__);
    }

    public function pushToCustomers()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $downloadId = (int)$this->request->get['download_id'];
        $productId = (int)$this->request->get['product_id'];

        $downloadInfo = $this->download->getDownloadInfo($downloadId);

        if (!$downloadInfo || !$productId) {
            redirect($this->html->getSecureURL('catalog/product_files', '&product_id=' . $productId));
        }

        $downloadInfo['attributes_data'] = serialize($this->download->getDownloadAttributesValues($downloadId));
        $this->loadModel('catalog/download');
        $orders4push = $this->model_catalog_download->getOrdersWithProduct($productId);
        $updArray = [];
        if ($orders4push) {
            foreach ($orders4push as $row) {
                $updArray = array_merge(
                    $updArray,
                    $this->download->addUpdateOrderDownload(
                        (int)$row['order_product_id'],
                        (int)$row['order_id'],
                        $downloadInfo
                    )
                );
            }

            $this->loadLanguage('catalog/files');
            $this->session->data['success'] = sprintf(
                $this->language->get('success_push_to_orders'),
                count($updArray)
            );
        }
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        redirect($this->html->getSecureURL('catalog/product_files', '&product_id=' . $productId));
    }
}