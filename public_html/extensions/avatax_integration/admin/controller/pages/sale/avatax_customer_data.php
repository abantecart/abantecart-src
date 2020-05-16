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
 * @property ModelProductFeaturesProductFeatures $model_avatax_integration_avatax_integration
 * @property ModelExtensionAvataxIntegration     $model_extension_avatax_integration
 */
class ControllerPagesSaleAvataxCustomerData extends AController
{
    public $data = array();
    public $error = array();

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

        $customer_id = $this->request->get['customer_id'];
        if ($this->request->is_POST() && $this->validateForm()) {
            $this->loadModel('extension/avatax_integration');
            $this->model_extension_avatax_integration->setCustomerSettings(
                $this->request->get['customer_id'],
                $this->request->post
            );
            $redirect_url = $this->html->getSecureURL('sale/avatax_customer_data', '&customer_id='.$customer_id);

            $this->session->data['success'] = $this->language->get('text_success');
            $this->redirect($redirect_url);
        }

        $this->getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

    }

    protected function getForm()
    {

        $customer_id = $this->request->get['customer_id'];
        $this->loadLanguage('avatax_integration/avatax_integration');
        $this->data['token'] = $this->session->data['token'];
        $this->data['error'] = $this->error;

        $this->document->initBreadcrumb(array(
            'href'      => $this->html->getSecureURL('index/home'),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
        ));
        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL('sale/customer'),
            'text'      => $this->language->get('heading_title'),
            'separator' => ' :: ',
        ));

        //allow to change this list via hook
        $this->data['fields'] = array_merge(array(
            'status'                => null,
            'exemption_number_name' => null,
            'entity_use_code_name'  => null,
        ),
            (array)$this->data['fields']);
        $this->loadModel('sale/customer');
        $customer_info = $this->model_sale_customer->getCustomer($customer_id);

        $fields = array_keys($this->data['fields']);
        foreach ($fields as $f) {
            if (isset ($this->request->post [$f])) {
                $this->data [$f] = $this->request->post [$f];
            } elseif (isset($customer_info)) {
                $this->data[$f] = $customer_info[$f];
            } else {
                $this->data[$f] = '';
            }
        }

        $this->data['customer_id'] = $customer_id;
        $this->data['action'] = $this->html->getSecureURL('sale/avatax_customer_data', '&customer_id='.$customer_id);
        $this->data['heading_title'] = $this->language->get('text_edit')
            .$this->language->get('text_customer')
            .' - '
            .$customer_info['firstname']
            .' '.$customer_info['lastname'];
        $form = new AForm('ST');

        $this->document->addBreadcrumb(array(
            'href'      => $this->data['action'],
            'text'      => $this->data['heading_title'],
            'separator' => ' :: ',
            'current'   => true,
        ));
        $this->data['tabs'][] = array(
            'href' => $this->html->getSecureURL('sale/customer/update', '&customer_id='.$customer_id),
            'text' => $this->language->get('tab_customer_details'),
        );
        if (has_value($customer_id)) {
            $this->data['tabs'][] = array(
                'href' => $this->html->getSecureURL('sale/customer_transaction', '&customer_id='.$customer_id),
                'text' => $this->language->get('tab_transactions'),
            );
            $this->data['tabs']['general'] = array(
                'href'   => $this->html->getSecureURL('sale/avatax_customer_data', '&customer_id='.$customer_id),
                'text'   => $this->language->get('avatax_integration_name'),
                'active' => true,
            );
        }

        $form->setForm(array(
            'form_name' => 'cgFrm',
            'update'    => $this->data['update'],
        ));

        $this->data['form']['id'] = 'cgFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
            'type'   => 'form',
            'name'   => 'cgFrm',
            'attr'   => 'data-confirm-exit="true" class="form-horizontal"',
            'action' => $this->data['action'],
        ));
        $this->data['form']['submit'] = $form->getFieldHtml(array(
            'type' => 'button',
            'name' => 'submit',
            'text' => $this->language->get('button_save'),
        ));
        $this->data['form']['reset'] = $form->getFieldHtml(array(
            'type' => 'button',
            'name' => 'reset',
            'text' => $this->language->get('button_reset'),
        ));

        $this->loadModel('extension/avatax_integration');
        $form_data = $this->model_extension_avatax_integration->getCustomerSettings($customer_id);
        $this->data['entry_status'] = $this->language->get('exemption_status');
        $this->data['form']['fields']['details']['status'] = $form->getFieldHtml(array(
            'type'    => 'selectbox',
            'name'    => 'status',
            'options' => array(
                '0' => $this->language->get('exemption_status_pending'),
                '1' => $this->language->get('exemption_status_approved'),
                '2' => $this->language->get('exemption_status_declined'),
            ),
            'value'   => $form_data['status'],
        ));
        $this->data['entry_exemption_number'] = $this->language->get('exemption_number_name');
        $this->data['form']['fields']['details']['exemption_number'] = $form->getFieldHtml(array(
            'type'  => 'input',
            'name'  => 'exemption_number',
            'value' => $form_data['exemption_number'],
        ));
        $this->data['entry_entity_use_code'] = $this->language->get('entity_use_code_name');
        $this->data['form']['fields']['details']['entity_use_code'] = $form->getFieldHtml(array(
            'type'    => 'selectbox',
            'name'    => 'entity_use_code',
            'value'   => $form_data['entity_use_code'],
            'options' => array(
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
            ),
        ));

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

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}