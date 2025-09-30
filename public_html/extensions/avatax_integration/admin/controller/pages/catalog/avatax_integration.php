<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
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

class ControllerPagesCatalogAvataxIntegration extends AController
{
    public $error = [];
    /** @var AAttribute_Manager */
    protected $attribute_manager;

    public function main()
    {
        $productId = (int)$this->request->get['product_id'];
        $this->loadLanguage('avatax_integration/avatax_integration');
        $this->loadLanguage('catalog/product');
        /** @var ModelExtensionAvataxIntegration $avMdl */
        $avMdl = $this->loadModel('extension/avatax_integration');
        $this->loadModel('catalog/product');
        $this->attribute_manager = new AAttribute_Manager();

        if ($this->request->is_POST() && $this->validateForm()) {
            $avMdl->setProductTaxCode($productId, (string)$this->request->post['name']);
            $this->session->data['success'] = $this->language->get('text_success');
            redirect($this->html->getSecureURL('catalog/avatax_integration', '&product_id=' . $productId));
        }

        $this->document->setTitle($this->language->get('heading_title'));
        $this->data['language_id'] = $this->language->getContentLanguageID();
        $this->data['product_description'] = $this->model_catalog_product->getProductDescriptions(
            $productId,
            $this->data['language_id']
        );
        $this->data['heading_title'] = $this->language->get('text_edit') . '&nbsp;' . $this->language->get('text_product');

        $this->view->assign('error', $this->error);
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
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('catalog/product/update', '&product_id=' . $productId),
                'text'      => $this->language->get('text_edit')
                    . '&nbsp;'
                    . $this->language->get('text_product')
                    . ' - '
                    . $this->data['product_description']['name'],
                'separator' => ' :: ',
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('catalog/avatax_integration', '&product_id=' . $productId),
                'text'      => $this->language->get('avatax_integration_name'),
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        //load tabs controller
        $this->data['active'] = 'avatax_integration';
        $tabs_obj = $this->dispatch('pages/catalog/product_tabs', [$this->data]);
        $this->data['product_tabs'] = $tabs_obj->dispatchGetOutput();
        unset($tabs_obj);

        $this->data['avatax_integration'] = $avMdl->getProductTaxCode($productId);

        $form = new AForm('HT');
        $form->setForm(
            [
                'form_name' => 'new_avatax_integration_form',
                'update'    => '',
            ]
        );
        $this->data['tax_code_name'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'name',
                'required' => false,
                'value'    => $this->data['avatax_integration'],
            ]
        );

        $this->data['action'] = $this->html->getSecureURL('catalog/avatax_integration', '&product_id=' . $productId);
        $this->data['form_title'] = $this->language->get('text_edit') . '&nbsp;' . $this->language->get('text_product');
        $this->data['update'] = '';
        $form = new AForm('HT');

        $form->setForm(
            [
                'form_name' => 'avatax_integration_form',
                'update'    => $this->data['update'],
            ]
        );

        $this->data['form']['id'] = 'avatax_integration_form';
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'avatax_integration_form',
                'action' => $this->data['action'],
                'attr'   => 'data-confirm-exit="true" class="aform form-horizontal"',
            ]
        );
        $this->data['form']['submit'] = $form->getFieldHtml(
            [
                'type' => 'button',
                'name' => 'submit',
                'text' => $this->language->get('button_add_tax_code'),
            ]
        );
        $this->data['form']['delete'] = $form->getFieldHtml(
            [
                'type' => 'button',
                'name' => 'delete',
                'text' => $this->language->get('button_delete_tax_code'),
            ]
        );
        $this->data['form']['cancel'] = $form->getFieldHtml(
            [
                'type' => 'button',
                'name' => 'cancel',
                'text' => $this->language->get('button_cancel'),
            ]
        );

        //prepend button to generate keyword
        $this->data['getcode_button'] = $form->getFieldHtml([
            'type'   => 'button',
            'name'   => 'lookup_code',
            'text'   => $this->language->get('avatax_integration_button_find_taxcode'),
            'href'   => 'https://taxcode.avatax.avalara.com/search?q='.urlencode($this->data['product_description']['name']),
            //set button not to submit a form
            'attr'   => 'type="button"',
            'style'  => 'btn btn-info',
            'icon'   => 'fa fa-search',
            'target' => '_blank',
        ]);

        $this->data['entry_tax_code_name'] = $this->language->get('avatax_integration_taxcode_name');
        $this->addChild('pages/catalog/product_summary', 'summary_form', 'pages/catalog/product_summary.tpl');

        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/avatax_integration/avatax_integration_form.tpl');
    }

    protected function validateForm()
    {
        if (!$this->user->hasPermission('modify', 'catalog/avatax_integration')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}