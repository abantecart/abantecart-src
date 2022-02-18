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

class ControllerPagesTotalAvataxIntegrationTotal extends AController
{
    public $data = array();
    private $error = array();
    private $fields = array(
        'avatax_integration_total_status',
        'avatax_integration_total_sort_order',
        'avatax_integration_total_calculation_order',
        'avatax_integration_total_total_type',
    );

    public function main()
    {

        $this->loadModel('setting/setting');
        $this->loadLanguage('extension/total');
        $this->loadLanguage('avatax_integration/avatax_integration');

        if ($this->request->is_POST() && $this->validate()) {
            $this->model_setting_setting->editSetting('avatax_integration_total', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            redirect($this->html->getSecureURL('total/avatax_integration_total'));
        }

        $this->document->setTitle($this->language->get('total_name'));

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }
        $this->data['success'] = $this->session->data['success'];
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $this->document->initBreadcrumb(array(
            'href'      => $this->html->getSecureURL('index/home'),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
        ));
        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL('extension/total'),
            'text'      => $this->language->get('heading_title'),
            'separator' => ' :: ',
        ));
        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL('total/avatax_integration_total'),
            'text'      => $this->language->get('total_name'),
            'separator' => ' :: ',
        ));

        foreach ($this->fields as $f) {
            if (isset ($this->request->post [$f])) {
                $this->data [$f] = $this->request->post [$f];
            } else {
                $this->data [$f] = $this->config->get($f);
            }
        }

        $this->data ['action'] = $this->html->getSecureURL('total/avatax_integration_total');
        $this->data['cancel'] = $this->html->getSecureURL('extension/total');
        $this->data ['heading_title'] = $this->language->get('text_edit').' '.$this->language->get('total_name');
        $this->data ['form_title'] = $this->language->get('total_name');
        $this->data ['update'] =
            $this->html->getSecureURL('listing_grid/total/update_field', '&id=avatax_integration_total');

        $form = new AForm ('HS');
        $form->setForm(array('form_name' => 'editFrm', 'update' => $this->data ['update']));

        $this->data['form']['form_open'] =
            $form->getFieldHtml(array('type' => 'form', 'name' => 'editFrm', 'action' => $this->data ['action']));
        $this->data['form']['submit'] = $form->getFieldHtml(array(
            'type'  => 'button',
            'name'  => 'submit',
            'text'  => $this->language->get('button_save'),
            'style' => 'button1',
        ));
        $this->data['form']['cancel'] = $form->getFieldHtml(array(
            'type'  => 'button',
            'name'  => 'cancel',
            'text'  => $this->language->get('button_cancel'),
            'style' => 'button2',
        ));

        $this->data['form']['fields']['status'] = $form->getFieldHtml(array(
            'type'  => 'checkbox',
            'name'  => 'avatax_integration_total_status',
            'value' => $this->data['avatax_integration_total_status'],
            'style' => 'btn_switch',
        ));

        $this->data['form']['fields']['total_type'] = $form->getFieldHtml(array(
            'type'  => 'hidden',
            'name'  => 'avatax_integration_total_total_type',
            'value' => 'avatax_integration',
        ));
        $this->data['form']['fields']['sort_order'] = $form->getFieldHtml(array(
            'type'  => 'input',
            'name'  => 'avatax_integration_total_sort_order',
            'value' => $this->data['avatax_integration_total_sort_order'],
        ));
        $this->data['form']['fields']['calculation_order'] = $form->getFieldHtml(array(
            'type'  => 'input',
            'name'  => 'avatax_integration_total_calculation_order',
            'value' => $this->data['avatax_integration_total_calculation_order'],
        ));

        $this->view->assign('help_url', $this->gen_help_url('edit_avatax_integration_total'));
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/total/form.tpl');

    }

    private function validate()
    {
        if (!$this->user->canModify('total/avatax_integration_total')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
