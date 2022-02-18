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

/**
 * Class ControllerPagesCatalogProductFeatures
 *
 * @property ModelExtensionAvataxIntegration $model_extension_avatax_integration
 * @property AAttribute_Manager              $attribute_manager
 */
class ControllerPagesCatalogAvataxIntegration extends AController
{
    public $error = array();
    public $data = array();

    public function main()
    {

        $this->loadLanguage('avatax_integration/avatax_integration');
        $this->loadLanguage('catalog/product');
        $this->loadModel('extension/avatax_integration');
        $this->loadModel('catalog/product');
        $this->attribute_manager = new AAttribute_Manager();

        if ($this->request->is_POST() && $this->validateForm()) {
            $this->model_extension_avatax_integration->setProductTaxCode(
                $this->request->get['product_id'],
                $this->request->post['name']
            );
            $this->session->data['success'] = $this->language->get('text_success');
            redirect(
                $this->html->getSecureURL(
                    'catalog/avatax_integration',
                    '&product_id='.$this->request->get['product_id']
                )
            );
        }

        $this->document->setTitle($this->language->get('heading_title'));
        $this->data['product_description'] = $this->model_catalog_product->getProductDescriptions(
            $this->request->get['product_id']
        );
        $this->data['heading_title'] = $this->language->get('text_edit').'&nbsp;'.$this->language->get('text_product');

        $this->view->assign('error', $this->error);
        $this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $this->document->initBreadcrumb(array(
            'href'      => $this->html->getSecureURL('index/home'),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
        ));
        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL('catalog/product'),
            'text'      => $this->language->get('heading_title'),
            'separator' => ' :: ',
        ));
        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL('catalog/product/update',
                '&product_id='.$this->request->get['product_id']),
            'text'      => $this->language->get('text_edit')
                .'&nbsp;'
                .$this->language->get('text_product')
                .' - '
                .$this->data['product_description'][$this->session->data['content_language_id']]['name'],
            'separator' => ' :: ',
        ));
        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL(
                'catalog/avatax_integration',
                '&product_id='.$this->request->get['product_id']
            ),
            'text'      => $this->language->get('avatax_integration_name'),
            'separator' => ' :: ',
            'current'   => true,
        ));

        $this->data['language_id'] = $this->session->data['content_language_id'];

        //load tabs controller
        $this->data['active'] = 'avatax_integration';
        $tabs_obj = $this->dispatch('pages/catalog/product_tabs', array($this->data));
        $this->data['product_tabs'] = $tabs_obj->dispatchGetOutput();
        unset($tabs_obj);

        $this->data['avatax_integration'] = $this->model_extension_avatax_integration->getProductTaxCode(
            $this->request->get['product_id']
        );

        $form = new AForm('HT');
        $form->setForm(array(
            'form_name' => 'new_avatax_integration_form',
            'update'    => '',
        ));
        $this->data['tax_code_name'] = $form->getFieldHtml(array(
            'type'     => 'input',
            'name'     => 'name',
            'required' => false,
            'value'    => $this->data['avatax_integration'],
        ));

        $this->data['action'] =
            $this->html->getSecureURL('catalog/avatax_integration', '&product_id='.$this->request->get['product_id']);
        $this->data['form_title'] = $this->language->get('text_edit').'&nbsp;'.$this->language->get('text_product');
        $this->data['update'] = '';
        $form = new AForm('HT');

        $form->setForm(array(
            'form_name' => 'avatax_integration_form',
            'update'    => $this->data['update'],
        ));

        $this->data['form']['id'] = 'avatax_integration_form';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
            'type'   => 'form',
            'name'   => 'avatax_integration_form',
            'action' => $this->data['action'],
            'attr'   => 'data-confirm-exit="true" class="aform form-horizontal"',
        ));
        $this->data['form']['submit'] = $form->getFieldHtml(array(
            'type'  => 'button',
            'name'  => 'submit',
            'text'  => $this->language->get('button_add_tax_code'),
            'style' => 'button1',
        ));
        $this->data['form']['delete'] = $form->getFieldHtml(array(
            'type'  => 'button',
            'name'  => 'delete',
            'text'  => $this->language->get('button_delete_tax_code'),
            'style' => 'button3',
        ));
        $this->data['form']['cancel'] = $form->getFieldHtml(array(
            'type'  => 'button',
            'name'  => 'cancel',
            'text'  => $this->language->get('button_cancel'),
            'style' => 'button2',
        ));

        //prepend button to generate keyword
        $this->data['getcode_button'] = $form->getFieldHtml(array(
            'type'   => 'button',
            'name'   => 'lookup_code',
            'text'   => $this->language->get('avatax_integration_button_find_taxcode'),
            'href'   => 'https://taxcode.avatax.avalara.com/',
            //set button not to submit a form
            'attr'   => 'type="button"',
            'style'  => 'btn btn-info',
            'icon'   => 'fa fa-search',
            'target' => '_blank',
        ));

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

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}