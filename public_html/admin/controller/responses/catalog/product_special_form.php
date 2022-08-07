<?php

/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2022 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ControllerResponsesCatalogProductSpecialForm extends AController
{
    public $error = [];

    public function insert()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('catalog/product');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->loadModel('catalog/product');
        $this->_getForm();
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function update()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('catalog/product');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->loadModel('catalog/product');

        $this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    private function _getForm()
    {
        $view = new AView($this->registry, 0);
        $productId = (int) $this->request->get['product_id'];
        $specialId = (int) $this->request->get['product_special_id'];

        $view->batchAssign($this->language->getASet('catalog/product'));

        $view->assign('error_warning', $this->error['warning']);
        $view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $this->data['error'] = $this->error;
        $this->data['cancel'] = $this->html->getSecureURL(
            'catalog/product_promotions',
            '&product_id='.$productId
        );

        $this->data['active'] = 'promotions';

        $this->data['product_description'] = $this->model_catalog_product->getProductDescriptions(
            $productId
        );
        $this->data['heading_title'] =
            $this->language->get('text_edit')
            .'&nbsp;'
            .$this->language->get('text_product')
            .' - '
            .$this->data['product_description'][$this->language->getContentLanguageID()]['name'];

        if ($specialId) {
            $special_info = $this->model_catalog_product->getProductSpecial($specialId);
            if ($special_info['date_start'] == '0000-00-00') {
                $special_info['date_start'] = '';
            }
            if ($special_info['date_end'] == '0000-00-00') {
                $special_info['date_end'] = '';
            }
        }

        $this->loadModel('sale/customer_group');
        $results = $this->model_sale_customer_group->getCustomerGroups();
        $this->data['customer_groups'] = array_column($results,'name', 'customer_group_id');

        $fields = ['customer_group_id', 'quantity', 'priority', 'price_prefix', 'price', 'date_start', 'date_end'];
        foreach ($fields as $f) {
            if (isset ($this->request->post [$f])) {
                $this->data [$f] = $this->request->post [$f];
                if (in_array($f, ['date_start', 'date_end'])) {
                    $this->data [$f] = dateDisplay2ISO(
                        $this->data [$f],
                        $this->language->get('date_format_short')
                    );
                }
            } elseif (isset($special_info)) {
                $this->data[$f] = $special_info[$f];
            } else {
                $this->data[$f] = '';
            }
        }

        if (!$specialId) {
            $this->data['action'] = $this->html->getSecureURL(
                'catalog/product_promotions',
                '&product_id='.$productId
            );
            $this->data['form_title'] = $this->language->get('text_insert')
                .'&nbsp;'
                .$this->language->get('entry_special');
            $this->data['update'] = '';
            $form = new AForm('ST');
        } else {
            $this->data['action'] = $this->html->getSecureURL(
                'catalog/product_promotions',
                '&product_id='.$productId
                .'&product_special_id='.$specialId
            );
            $this->data['form_title'] = $this->language->get('text_edit')
                .'&nbsp;'
                .$this->language->get('entry_special');
            $this->data['update'] = $this->html->getSecureURL(
                'listing_grid/product/update_special_field',
                '&id='.$specialId
            );
            $form = new AForm('HS');
        }

        $this->document->addBreadcrumb(
            [
                'href'      => $this->data['action'],
                'text'      => $this->data['form_title'],
                'separator' => ' :: ',
            ]
        );

        $form->setForm(
            [
                'form_name' => 'productFrm',
                'update'    => $this->data['update'],
            ]
        );

        $this->data['form']['id'] = 'productFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'productFrm',
                'action' => $this->data['action'],
                'attr'   => 'data-confirm-exit="true"  class="aform form-horizontal"',
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

        $this->data['form']['fields']['promotion_type'] = $form->getFieldHtml(
            [
                'type'  => 'hidden',
                'name'  => 'promotion_type',
                'value' => 'special',
            ]
        );
        $this->data['form']['fields']['customer_group'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'customer_group_id',
                'value'   => $this->data['customer_group_id'],
                'options' => $this->data['customer_groups'],
            ]
        );

        $this->data['form']['fields']['priority'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'priority',
                'value' => $this->data['priority'],
                'style' => 'small-field',
            ]
        );

        $this->data['form']['fields']['price_prefix'] = $form->getFieldHtml(
            [
                'type'  => 'selectbox',
                'name'  => 'price_prefix',
                'value' => $this->data['price_prefix'],
                'options' => [
                    '$' => '$',
                    '%' => '%'
                ]
            ]
        );

        $this->data['form']['fields']['price'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'price',
                'value' => moneyDisplayFormat($this->data['price']),
                'style' => 'tiny-field',
            ]
        );

        $this->data['js_date_format'] = format4Datepicker($this->language->get('date_format_short'));
        $this->data['form']['fields']['date_start'] = $form->getFieldHtml(
            [
                'type'       => 'date',
                'name'       => 'date_start',
                'value'      => dateISO2Display(
                    $this->data['date_start'],
                    $this->language->get('date_format_short')
                ),
                'default'    => '',
                'dateformat' => format4Datepicker(
                    $this->language->get('date_format_short')
                ),
                'highlight'  => 'future',
                'style'      => 'small-field',
            ]
        );
        $this->data['form']['fields']['date_end'] = $form->getFieldHtml(
            [
                'type'       => 'date',
                'name'       => 'date_end',
                'value'      => dateISO2Display(
                    $this->data['date_end'],
                    $this->language->get('date_format_short')
                ),
                'default'    => '',
                'dateformat' => format4Datepicker(
                    $this->language->get('date_format_short')
                ),
                'highlight'  => 'future',
                'style'      => 'small-field',
            ]
        );

        $view->assign('help_url', $this->gen_help_url('product_special_edit'));
        $view->batchAssign($this->data);
        $this->data['response'] = $view->fetch('responses/catalog/product_promotion_form.tpl');
        $this->response->setOutput($this->data['response']);
    }
}