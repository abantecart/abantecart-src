<?php
/**
 * $Id$
 *
 * AbanteCart, Ideal OpenSource Ecommerce Solution
 * http://www.AbanteCart.com
 *
 * Copyright © 2011-2026 Belavier Commerce LLC
 *
 * This source file is subject to Open Software License (OSL 3.0)
 * License details are bundled with this package in the file LICENSE.txt.
 * It is also available at this URL:
 * <http://www.opensource.org/licenses/OSL-3.0>
 *
 * UPGRADE NOTE:
 *   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *   versions in the future. If you wish to customize AbanteCart for your
 *   needs, please refer to http://www.AbanteCart.com for more information.
 */
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerPagesExtensionUsps extends AController
{
    public $error = [];
    public $fields = [
        'usps_manifest_order_status_id',
        'usps_postcode',
        'usps_domestic_1',
        'usps_domestic_2',
        'usps_domestic_3',
        'usps_domestic_4',
        'usps_domestic_5',
        'usps_domestic_6',
        'usps_domestic_7',
        'usps_international_1',
        'usps_international_2',
        'usps_international_3',
        'usps_international_4',
        'usps_length',
        'usps_width',
        'usps_height',
        'usps_display_weight',
        'usps_tax_class_id',
        'usps_location_id',
        'usps_status',
        'usps_sort_order',
    ];

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->request->get['extension'] = 'usps';
        $this->loadLanguage('extension/extensions');
        $this->loadLanguage('usps/usps');
        $this->document->setTitle($this->language->get('usps_name'));
        $this->load->model('setting/setting');

        if ($this->request->is_POST() && $this->_validate()) {
            $this->model_setting_setting->editSetting('usps', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_usps_success');
            redirect($this->html->getSecureURL('extension/usps'));
        }

        $this->data['error_warning'] = $this->error['warning'] ?? '';

        if (isset($this->error['postcode'])) {
            $this->data['error']['postcode'] = $this->error['postcode'];
        }
        $this->data['success'] = $this->session->data['success'];
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $this->document->initBreadcrumb([
            'href'      => $this->html->getSecureURL('index/home'),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
        ]);
        $this->document->addBreadcrumb([
            'href'      => $this->html->getSecureURL('extension/extensions/shipping'),
            'text'      => $this->language->get('text_shipping'),
            'separator' => ' :: ',
        ]);
        $this->document->addBreadcrumb([
            'href'      => $this->html->getSecureURL('extension/usps'),
            'text'      => $this->language->get('usps_name'),
            'separator' => ' :: ',
            'current'   => true,
        ]);

        /** @var ModelLocalisationTaxClass $mdl */
        $mdl = $this->load->model('localisation/tax_class');
        $results = $mdl->getTaxClasses();
        $tax_classes =
            [0 => $this->language->get('text_none')]
            +
            array_column($results, 'title', 'tax_class_id');

        /** @var ModelLocalisationLocation $mdl */
        $mdl = $this->load->model('localisation/location');
        $results = $mdl->getLocations();
        $locations = array_column($results, 'name', 'location_id');
        $allLocationsText = $this->language->get('usps_location_id_0');
        $locations = [0 => $allLocationsText] + $locations;

        /** @var ModelLocalisationOrderStatus $mdl */
        $mdl = $this->load->model('localisation/order_status');
        $results = $mdl->getOrderStatuses();
        $order_statuses = array_column($results, 'name', 'order_status_id');

        foreach ($this->fields as $f) {
            $this->data[$f] = $this->request->post[$f] ?? $this->config->get($f);
        }
        if (!(int)$this->data['usps_manifest_order_status_id']) {
            reset($order_statuses);
            $this->data['usps_manifest_order_status_id'] = (int)key($order_statuses);
        }

        $this->view->assign('form_store_switch', $this->html->getStoreSwitcher());
        $this->data['action'] = $this->html->getSecureURL('extension/usps', '&extension=usps');
        $this->data['cancel'] = $this->html->getSecureURL('extension/shipping');
        $this->data['heading_title'] = $this->language->get('usps_name');
        $this->data['form_title'] = $this->language->get('usps_name');
        $this->data['update'] = $this->html->getSecureURL('r/extension/usps_save/update');

        $form = new AForm ('HS');
        $form->setForm(['form_name' => 'editFrm', 'update' => $this->data['update']]);

        $this->data['form']['form_open'] = $form->getFieldHtml([
            'type'   => 'form',
            'name'   => 'editFrm',
            'action' => $this->data['action'],
            'attr'   => 'data-confirm-exit="true" class="aform form-horizontal"',
        ]);
        $this->data['form']['submit'] = $form->getFieldHtml([
            'type' => 'button',
            'name' => 'submit',
            'text' => $this->language->get('button_save'),
        ]);
        $this->data['form']['cancel'] = $form->getFieldHtml([
            'type' => 'button',
            'name' => 'cancel',
            'text' => $this->language->get('button_cancel'),
        ]);

        $this->data['form']['fields']['postcode'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'usps_postcode',
                'value'    => $this->data['usps_postcode'],
                'required' => true,
            ]
        );
        $this->data['form']['fields']['domestic'] = [];

        $options = [];
        foreach (USPS_CLASSES['domestic'] as $i => $title) {
            $name = 'usps_domestic_' . $i;
            $this->data['form']['fields']['domestic'][$name] = $form->getFieldHtml(
                [
                    'type'  => 'checkbox',
                    'name'  => $name,
                    'style' => 'btn_switch',
                    'value' => $this->data[$name],
                ]
            );
            $options[$name] = $this->data['entry_' . $name] = $title;
        }

        $this->data['form']['fields']['international'] = [];
        $options = [];
        foreach (USPS_CLASSES['international'] as $i => $title) {

            $name = 'usps_international_' . $i;
            $this->data['form']['fields']['international'][$name] = $form->getFieldHtml(
                [
                    'type'  => 'checkbox',
                    'name'  => $name,
                    'style' => 'btn_switch',
                    'value' => $this->data[$name],
                ]
            );
            $options[$name] = $this->data['entry_' . $name] = $title;
        }

        $this->data['form']['fields']['length'] = $form->getFieldHtml([
            'type'  => 'input',
            'name'  => 'usps_length',
            'value' => $this->data['usps_length'],
        ]);
        $this->data['form']['fields']['width'] = $form->getFieldHtml([
            'type'  => 'input',
            'name'  => 'usps_width',
            'value' => $this->data['usps_width'],
        ]);
        $this->data['form']['fields']['height'] = $form->getFieldHtml([
            'type'  => 'input',
            'name'  => 'usps_height',
            'value' => $this->data['usps_height'],
        ]);
        $this->data['form']['fields']['display_weight'] = $form->getFieldHtml([
            'type'    => 'selectbox',
            'name'    => 'usps_display_weight',
            'value'   => $this->data['usps_display_weight'],
            'options' => [
                1 => $this->language->get('text_yes'),
                0 => $this->language->get('text_no'),
            ],
        ]);

        $this->data['form']['fields']['tax'] = $form->getFieldHtml([
            'type'    => 'selectbox',
            'name'    => 'usps_tax_class_id',
            'options' => $tax_classes,
            'value'   => $this->data['usps_tax_class_id'],
        ]);
        $this->data['form']['fields']['location'] = $form->getFieldHtml([
            'type'    => 'selectbox',
            'name'    => 'usps_location_id',
            'options' => $locations,
            'value'   => $this->data['usps_location_id'],
        ]);
        $this->data['form']['fields']['manifest_order_status_id'] = $form->getFieldHtml([
            'type'    => 'selectbox',
            'name'    => 'usps_manifest_order_status_id',
            'options' => $order_statuses,
            'value'   => $this->data['usps_manifest_order_status_id'],
        ]);
        $this->data['form']['fields']['sort_order'] = $form->getFieldHtml([
            'type'  => 'input',
            'name'  => 'usps_sort_order',
            'value' => $this->data['usps_sort_order'],
        ]);

        //load tabs controller

        $this->data['active_group'] = 'general';

        $tabs_obj = $this->dispatch('pages/extension/extension_tabs', [$this->data]);
        $this->data['tabs'] = $tabs_obj->dispatchGetOutput();
        unset($tabs_obj);

        $obj = $this->dispatch('pages/extension/extension_summary', [$this->data]);
        $this->data['extension_summary'] = $obj->dispatchGetOutput();
        unset($obj);

        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/extension/usps.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function _validate()
    {
        if (!$this->user->canModify('extension/usps')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['usps_postcode']) {
            $this->error['postcode'] = $this->language->get('error_postcode');
        }

        return (!$this->error);
    }
}
