<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2026 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */
/** @noinspection PhpMultipleClassDeclarationsInspection */
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ControllerPagesCatalogProductPromotions extends AController
{
    public $error = [];

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $productId = (int) $this->request->get['product_id'];
        $this->loadLanguage('catalog/product');
        /** @var ModelCatalogProduct $mdl */
        $mdl = $this->loadModel('catalog/product');

        if ($productId && $this->request->is_GET()) {
            $product_info = $this->model_catalog_product->getProduct($productId);
            if (!$product_info) {
                $this->session->data['warning'] = $this->language->get('error_product_not_found');
                redirect($this->html->getSecureURL('catalog/product'));
            }
        }

        if ($this->request->is_POST() && $this->_validateForm()) {
            $post = $this->request->post;
            foreach (['date_start', 'date_end'] as $datetime) {
                if ($post[$datetime]) {
                    $post[$datetime] = dateDisplay2ISO($post[$datetime], $this->language->get('date_format_short'));
                }
            }

            if ($post['promotion_type'] == 'discount') {
                $post['quantity'] = max((int) $post['quantity'], 2);
                //update
                if (has_value($this->request->get['product_discount_id'])) {
                    $mdl->updateProductDiscount((int) $this->request->get['product_discount_id'], $post);
                } //insert
                else {
                    $this->data['product_discount_id'] = $mdl->addProductDiscount($productId, $post);
                }
            } elseif ($post['promotion_type'] == 'special') {
                //update
                if (has_value($this->request->get['product_special_id'])) {
                    $mdl->updateProductSpecial((int) $this->request->get['product_special_id'], $post);
                } //insert
                else {
                    $this->data['product_special_id'] = $mdl->addProductSpecial($productId, $post);
                }
            }
            $this->session->data['success'] = $this->language->get('text_success');
            $this->extensions->hk_ProcessData($this, __FUNCTION__);
            redirect(
                $this->html->getSecureURL(
                    'catalog/product_promotions',
                    '&product_id=' . $productId
                )
            );
        }

        $this->data['product_description'] = $mdl->getProductDescriptions(
            $productId,
            $this->language->getContentLanguageID()
        );

        $this->view->assign('error_warning', $this->error['warning'] = implode('<br>', $this->error));
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
            . $this->data['product_description']['name'];
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL(
                    'catalog/product/update',
                    '&product_id=' . $productId
                ),
                'text'      => $title,
                'separator' => ' :: ',
            ]
        );
        $this->document->setTitle($title);
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL(
                    'catalog/product_promotions',
                    '&product_id=' . $productId
                ),
                'text'      => $this->language->get('tab_promotions'),
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        $this->loadModel('sale/customer_group');
        $results = $this->model_sale_customer_group->getCustomerGroups();
        $this->data['customer_groups'] = array_column($results, 'name', 'customer_group_id');

        $this->data['form_title'] = $this->language->get('text_edit')
            . '&nbsp;'
            . $this->language->get('text_product');
        $this->data['product_discounts'] = $mdl->getProductDiscounts(
            $productId
        );
        $this->data['delete_discount'] = $this->html->getSecureURL(
            'catalog/product_promotions/delete',
            '&product_id=' . $productId . '&product_discount_id=%ID%'
        );
        $this->data['update_discount'] = $this->html->getSecureURL(
            'catalog/product_discount_form/update',
            '&product_id=' . $productId . '&product_discount_id=%ID%'
        );

        $this->data['product_specials'] = $mdl->getProductSpecials($productId);
        $this->data['delete_special'] = $this->html->getSecureURL(
            'catalog/product_promotions/delete',
            '&product_id=' . $productId . '&product_special_id=%ID%'
        );
        $this->data['update_special'] = $this->html->getSecureURL(
            'catalog/product_special_form/update',
            '&product_id=' . $productId . '&product_special_id=%ID%'
        );

        foreach (['product_discounts', 'product_specials'] as $section) {
            foreach ($this->data[$section] as $i => $item) {
                foreach (['date_start', 'date_end'] as $dateField) {
                    $date = $item[$dateField];
                    $date = $date == '0000-00-00' ? null : $date;
                    $this->data[$section][$i][$dateField] = $date ? dateISO2Display($date) : '';
                }
            }
        }

        $this->data['button_remove'] = $this->html->buildElement(
            [
                'type' => 'button',
                'text' => $this->language->get('button_remove'),
            ]
        );
        $this->data['button_edit'] = $this->html->buildElement(
            [
                'type' => 'button',
                'text' => $this->language->get('button_edit'),
            ]
        );
        $this->data['button_add_discount'] = $this->html->buildElement(
            [
                'type' => 'button',
                'text' => $this->language->get('button_add_discount'),
                'href' => $this->html->getSecureURL(
                    'catalog/product_discount_form/insert',
                    '&product_id=' . $productId
                ),
            ]
        );
        $this->data['button_add_special'] = $this->html->buildElement(
            [
                'type' => 'button',
                'text' => $this->language->get('button_add_special'),
                'href' => $this->html->getSecureURL(
                    'catalog/product_special_form/insert',
                    '&product_id=' . $productId
                ),
            ]
        );

        $this->data['active'] = 'promotions';
        //load tabs controller
        $tabs_obj = $this->dispatch('pages/catalog/product_tabs', [$this->data]);
        $this->data['product_tabs'] = $tabs_obj->dispatchGetOutput();
        unset($tabs_obj);

        $this->addChild('pages/catalog/product_summary', 'summary_form', 'pages/catalog/product_summary.tpl');

        $this->view->assign('help_url', $this->gen_help_url('product_promotions'));
        if ($this->config->get('config_embed_status')) {
            $this->data['product_store'] = $mdl->getProductStores($productId);
            $btnData = getEmbedButtonsData(
                'common/do_embed/product',
                ['product_id' => $productId],
                $this->data['product_store']
            );
            $this->data['embed_url'] = $btnData['embed_url'];
            $this->data['embed_stores'] = $btnData['embed_stores'];
        }
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/catalog/product_promotions.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function delete()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('catalog/product');
        /** @var ModelCatalogProduct $mdl */
        $mdl = $this->loadModel('catalog/product');
        if (has_value($this->request->get['product_discount_id'])) {
            $mdl->deleteProductDiscount((int) $this->request->get['product_discount_id']);
        } elseif (has_value($this->request->get['product_special_id'])) {
            $mdl->deleteProductSpecial((int) $this->request->get['product_special_id']);
        }
        $this->session->data['success'] = $this->language->get('text_success');
        redirect(
            $this->html->getSecureURL(
                'catalog/product_promotions',
                '&product_id=' . (int) $this->request->get['product_id']
            )
        );

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function _validateForm()
    {
        if (!$this->user->canModify('catalog/product_promotions')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (has_value($this->request->post['promotion_type'])) {
            $start = (string) $this->request->post['date_start'];
            $end = (string) $this->request->post['date_end'];

            if ($start != ''
                && $end != ''
                && dateFromFormat($start, $this->language->get('date_format_short'))
                > dateFromFormat($end, $this->language->get('date_format_short'))
            ) {
                $this->error['date_end'] = $this->language->get('error_date');
            }
        }

        $this->extensions->hk_ValidateData($this, [__FUNCTION__]);
        return (!$this->error);
    }
}
