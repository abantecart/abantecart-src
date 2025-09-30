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

class ControllerPagesSaleAvataxCustomerData extends AController
{
    public $error = [];

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadModel('sale/customer');
        $this->loadLanguage('sale/customer');
        $this->loadLanguage('avatax_integration/avatax_integration');
        $this->document->setTitle($this->language->get('avatax_integration_name'));

        $this->view->assign('error_warning', $this->session->data['warning']);
        if (isset($this->session->data['warning'])) {
            unset($this->session->data['warning']);
        }
        $this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $customer_id = (int)$this->request->get['customer_id'];
        if ($this->request->is_POST() && $this->validateForm()) {
            /** @var ModelExtensionAvataxIntegration $mdl */
            $mdl = $this->loadModel('extension/avatax_integration');
            $mdl->setCustomerSettings($customer_id, $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            redirect($this->html->getSecureURL('sale/avatax_customer_data', '&customer_id=' . $customer_id));
        }

        $this->getForm();
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function getForm()
    {
        $customer_id = (int)$this->request->get['customer_id'];
        $this->loadLanguage('avatax_integration/avatax_integration');
        $this->data['token'] = $this->session->data['token'];
        $this->data['error'] = $this->error;

        $this->document->initBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('sale/customer'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
            ]
        );

        //allow changing this list via hook
        $this->data['fields'] = array_merge(
            [
                'status'                => null,
                'exemption_number_name' => null,
                'entity_use_code_name'  => null,
            ],
            (array)$this->data['fields']);
        /** @var ModelSaleCustomer $cMdl */
        $cMdl = $this->loadModel('sale/customer');
        $customer_info = $cMdl->getCustomer($customer_id);

        $fields = array_keys($this->data['fields']);
        foreach ($fields as $f) {
            $this->data[$f] = $this->request->post[$f] ?? $customer_info[$f] ?? '';
        }

        $this->data['customer_id'] = $customer_id;
        $this->data['action'] = $this->html->getSecureURL('sale/avatax_customer_data', '&customer_id=' . $customer_id);
        $this->data['heading_title'] = $this->language->get('text_edit')
            . $this->language->get('text_customer')
            . ' - '
            . $customer_info['firstname'] . ' ' . $customer_info['lastname'];
        $form = new AForm('ST');

        $this->document->addBreadcrumb(
            [
                'href'      => $this->data['action'],
                'text'      => $this->data['heading_title'],
                'separator' => ' :: ',
                'current'   => true,
            ]
        );
        $this->data['tabs'][] = [
            'href' => $this->html->getSecureURL('sale/customer/update', '&customer_id=' . $customer_id),
            'text' => $this->language->get('tab_customer_details'),
        ];
        if (has_value($customer_id)) {
            $this->data['tabs'][] = [
                'href' => $this->html->getSecureURL('sale/customer_transaction', '&customer_id=' . $customer_id),
                'text' => $this->language->get('tab_transactions'),
            ];
            $this->data['tabs']['general'] = [
                'href'   => $this->html->getSecureURL('sale/avatax_customer_data', '&customer_id=' . $customer_id),
                'text'   => $this->language->get('avatax_integration_name'),
                'active' => true,
            ];
        }

        $form->setForm(
            [
                'form_name' => 'cgFrm',
                'update'    => $this->data['update'],
            ]
        );

        $this->data['form']['id'] = 'cgFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'cgFrm',
                'attr'   => 'data-confirm-exit="true" class="form-horizontal"',
                'action' => $this->data['action'],
            ]
        );
        $this->data['form']['submit'] = $form->getFieldHtml(
            [
                'type' => 'button',
                'name' => 'submit',
                'text' => $this->language->get('button_save'),
            ]
        );
        $this->data['form']['reset'] = $form->getFieldHtml(
            [
                'type' => 'button',
                'name' => 'reset',
                'text' => $this->language->get('button_reset'),
            ]
        );

        /** @var ModelExtensionAvataxIntegration $mdl */
        $mdl = $this->loadModel('extension/avatax_integration');
        $form_data = $mdl->getCustomerSettings($customer_id);
        $this->data['entry_status'] = $this->language->get('exemption_status');
        $this->data['form']['fields']['details']['status'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'status',
                'options' => [
                    '0' => $this->language->get('exemption_status_pending'),
                    '1' => $this->language->get('exemption_status_approved'),
                    '2' => $this->language->get('exemption_status_declined'),
                ],
                'value'   => $form_data['status'],
            ]
        );
        $this->data['entry_exemption_number'] = $this->language->get('exemption_number_name');
        $this->data['form']['fields']['details']['exemption_number'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'exemption_number',
                'value' => $form_data['exemption_number'],
            ]
        );
        $this->data['entry_entity_use_code'] = $this->language->get('entity_use_code_name');
        $this->data['form']['fields']['details']['entity_use_code'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'entity_use_code',
                'value'   => $form_data['entity_use_code'],
                'options' => [
                    ''  => $this->language->get('text_select'),
                    'A' => 'A. Federal government (United States)',
                    'B' => 'B. State government (United States)',
                    'C' => 'C. Tribe / Status Indian / Indian Band (United States & Canada)',
                    'D' => 'D. Foreign diplomat (United States & Canada)',
                    'E' => 'E. Charitable or benevolent org (United States & Canada)',
                    'F' => 'F. Religious organization (United States & Canada)',
                    'G' => 'G. Resale (United States & Canada)',
                    'H' => 'H. Commercial agricultural production (United States & Canada)',
                    'I' => 'I. Industrial production / manufacturer (United States & Canada)',
                    'J' => 'J. Direct pay permit (United States)',
                    'K' => 'K. Direct mail (United States)',
                    'L' => 'L. Other (United States & Canada)',
                    'M' => 'M. Educational Organization',
                    'N' => 'N. Local government (United States)',
                    //'O' => 'Not Used',
                    'P' => 'P. Commercial aquaculture (Canada)',
                    'Q' => 'Q. Commercial Fishery (Canada)',
                    'R' => 'R. Non-resident (Canada)',
                ],
            ]
        );

        $this->data['section'] = 'details';
        $this->data['tabs']['general']['active'] = true;
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/sale/avatax_customer_form.tpl');
    }

    protected function validateForm()
    {
        if (!$this->user->hasPermission('modify', 'sale/avatax_customer_data')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }
}