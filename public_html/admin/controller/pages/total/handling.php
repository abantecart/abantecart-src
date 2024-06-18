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

class ControllerPagesTotalHandling extends AController
{
    public $error = [];
    private $fields = [
        'handling_total',
        'handling_prefix',
        'handling_fee',
        'handling_tax_class_id',
        'handling_status',
        'handling_fee_total_type',
        'handling_sort_order',
        'handling_calculation_order',
        'handling_per_payment',
    ];

    public function main()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->document->setTitle($this->language->get('heading_title'));
        $this->loadModel('setting/setting');

        if ($this->request->is_POST() && $this->_validate($this->request->post)) {
            $settings = $this->request->post;
            foreach ($settings['handling_payment'] as $i => $payment) {
                if (!trim($payment)) {
                    unset($settings['handling_payment'][$i],
                        $settings['handling_payment_subtotal'][$i],
                        $settings['handling_payment_prefix'][$i],
                        $settings['handling_payment_fee'][$i]);
                }
            }

            $settings['handling_per_payment'] = serialize(
                [
                    'handling_payment'          => $settings['handling_payment'],
                    'handling_payment_subtotal' => $settings['handling_payment_subtotal'],
                    'handling_payment_prefix'   => $settings['handling_payment_prefix'],
                    'handling_payment_fee'      => $settings['handling_payment_fee'],
                ]
            );

            unset($settings['handling_payment'],
                $settings['handling_payment_subtotal'],
                $settings['handling_payment_prefix'],
                $settings['handling_payment_fee']);

            $this->model_setting_setting->editSetting('handling', $settings);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->extensions->hk_ProcessData($this);
            redirect($this->html->getSecureURL('total/handling'));
        }
        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }
        $this->data['error'] = $this->error;
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
            'href'      => $this->html->getSecureURL('extension/total'),
            'text'      => $this->language->get('text_total'),
            'separator' => ' :: ',
        ]);
        $this->document->addBreadcrumb([
            'href'      => $this->html->getSecureURL('total/handling'),
            'text'      => $this->language->get('heading_title'),
            'separator' => ' :: ',
            'current'   => true,
        ]);

        $this->loadModel('localisation/tax_class');
        $_tax_classes = $this->model_localisation_tax_class->getTaxClasses();
        $tax_classes = [0 => $this->language->get('text_none')]
            + array_column($_tax_classes, 'title', 'tax_class_id');


        foreach ($this->fields as $f) {
            $this->data[$f] = $this->request->post [$f] ?? $this->config->get($f);
            if ($f == 'handling_per_payment') {
                $this->data[$f] = unserialize($this->data[$f]);
            }
        }

        $this->data ['action'] = $this->html->getSecureURL('total/handling');
        $this->data ['cancel'] = $this->html->getSecureURL('extension/total');
        $this->data ['heading_title'] = $this->language->get('text_edit') . $this->language->get('text_total');
        $this->data ['form_title'] = $this->language->get('heading_title');
        $this->data ['update'] = $this->html->getSecureURL('listing_grid/total/update_field', '&id=handling');

        $form = new AForm ('HT');
        $form->setForm([
            'form_name' => 'editFrm',
            'update'    => $this->data ['update'],
        ]);

        $this->data['form']['form_open'] = $form->getFieldHtml([
            'type'   => 'form',
            'name'   => 'editFrm',
            'action' => $this->data ['action'],
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

        $currency_symbol = $this->currency->getCurrency($this->config->get('config_currency'));
        $currency_symbol = $currency_symbol['symbol_left'] . $currency_symbol['symbol_right'];

        $this->data['form']['fields']['status'] = $form->getFieldHtml([
            'type'  => 'checkbox',
            'name'  => 'handling_status',
            'value' => $this->data['handling_status'],
            'style' => 'btn_switch status_switch',
        ]);
        $this->data['form']['fields']['total'] = $form->getFieldHtml([
            'type'        => 'input',
            'name'        => 'handling_total',
            'value'       => $this->data['handling_total'],
            'placeholder' => $this->language->get('entry_total_placeholder'),
            'required'    => true,
        ]);
        $this->data['form']['fields']['fee'] = [
            $form->getFieldHtml([
                'type'    => 'selectbox',
                'name'    => 'handling_prefix',
                'value'   => $this->data['handling_prefix'],
                'options' => [
                    '$' => $currency_symbol,
                    '%' => '%',
                ],
                'style'   => 'small-field',
            ]),
            $form->getFieldHtml([
                'type'     => 'input',
                'name'     => 'handling_fee',
                'value'    => $this->data['handling_fee'],
                'required' => true,
            ]),
        ];

        $payments = $this->extensions->getExtensionsList(['filter' => 'payment', 'sort_order' => ['name']]);
        $options[] = $this->language->get('text_none');
        foreach ($payments->rows as $row) {
            if ($row['status']) {
                $options[$row['key']] = $row['name'];
            }
        }
        if (!$this->data['handling_per_payment']['handling_payment']) {
            $this->data['handling_per_payment'] = [
                'handling_payment'          => [0 => ''],
                'handling_payment_subtotal' => [0 => ''],
                'handling_payment_fee'      => [0 => ''],
            ];
        }

        foreach ($this->data['handling_per_payment']['handling_payment'] as $i => $payment) {
            $this->data['form']['fields']['payment_fee' . $i] = [
                $this->language->get('entry_payment'),
                $form->getFieldHtml([
                    'type'    => 'selectbox',
                    'name'    => 'handling_payment[]',
                    'options' => $options,
                    'value'   => $payment,
                ]),
                $this->language->get('entry_order_subtotal'),
                $form->getFieldHtml([
                    'type'  => 'input',
                    'name'  => 'handling_payment_subtotal[]',
                    'value' => $this->data['handling_per_payment']['handling_payment_subtotal'][$i],
                    'style' => 'small-field',
                ]),
                $this->language->get('entry_payment_fee'),
                $form->getFieldHtml([
                    'type'    => 'selectbox',
                    'name'    => 'handling_payment_prefix[' . $i . ']',
                    'value'   => $this->data['handling_per_payment']['handling_payment_prefix'][$i],
                    'options' => [
                        '$' => $currency_symbol,
                        '%' => '%',
                    ],
                    'style'   => 'small-field',
                ]),
                $form->getFieldHtml([
                    'type'  => 'input',
                    'name'  => 'handling_payment_fee[]',
                    'value' => $this->data['handling_per_payment']['handling_payment_fee'][$i],
                    'style' => 'small-field',
                ]),
            ];
        }

        $this->data['form']['fields']['tax'] = $form->getFieldHtml([
            'type'    => 'selectbox',
            'name'    => 'handling_tax_class_id',
            'options' => $tax_classes,
            'value'   => $this->data['handling_tax_class_id'],
        ]);
        $this->loadLanguage('extension/extensions');
        $options = [
            'fee'      => $this->language->get('text_fee'),
            'discount' => $this->language->get('text_discount'),
            'total'    => $this->language->get('text_total'),
            'subtotal' => $this->language->get('text_subtotal'),
            'tax'      => $this->language->get('text_tax'),
            'shipping' => $this->language->get('text_shipping'),
        ];
        $this->data['form']['fields']['total_type'] = $form->getFieldHtml([
            'type'    => 'selectbox',
            'name'    => 'handling_fee_total_type',
            'options' => $options,
            'value'   => $this->data['handling_fee_total_type'],
        ]);

        $this->data['form']['fields']['sort_order'] = $form->getFieldHtml([
            'type'  => 'number',
            'name'  => 'handling_sort_order',
            'value' => $this->data['handling_sort_order']
        ]);
        $defaultCalcOrder = (int)$this->config->get('subtotal_calculation_order') + 1;
        $value = (int)($this->data['handling_calculation_order'] ?? $defaultCalcOrder);
        $value = max($value, $defaultCalcOrder);

        $this->data['form']['fields']['calculation_order'] = $form->getFieldHtml([
            'type'  => 'number',
            'name'  => 'handling_calculation_order',
            'value' => $value,
            'attr'  => 'min="' . $defaultCalcOrder . '" '
        ]);
        $this->view->assign('help_url', $this->gen_help_url('edit_handling'));
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/total/handling.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function _validate($inData)
    {
        if (!$this->user->canModify('total/handling')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        foreach ($inData['handling_payment'] as $i => $payment_id) {
            if ($payment_id) {
                if (!(int)$inData['handling_payment_subtotal'][$i]) {
                    $this->error['warning'] = $this->language->get('error_number');
                }
                if (!(float)$inData['handling_payment_fee'][$i]) {
                    $this->error['warning'] = $this->language->get('error_number');
                }
            }
        }
        $total = (float)$inData['handling_total'];
        if (!$total) {
            $this->error['total'] = $this->language->get('error_number');
        }
        $fee = (float)$inData['handling_fee'];
        if (!$fee) {
            $this->error['fee'] = $this->language->get('error_number');
        }

        $this->extensions->hk_ValidateData($this);
        return (!$this->error);
    }
}
