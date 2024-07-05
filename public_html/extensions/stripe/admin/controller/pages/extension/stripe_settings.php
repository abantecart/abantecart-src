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

class ControllerPagesExtensionStripeSettings extends AController
{
    protected $error = [];
    protected $errors = [
        'stripe_pk_live',
        'stripe_sk_live',
        'stripe_pk_test',
        'stripe_sk_test'
    ];

    protected $fields = [
        'stripe_access_token',
        'stripe_test_mode',
        'stripe_pk_live',
        'stripe_sk_live',
        'stripe_pk_test',
        'stripe_sk_test',
        'stripe_settlement'
    ];

    public function main()
    {
        $this->request->get['extension'] = 'stripe';
        $this->loadLanguage('stripe/stripe');
        $this->document->setTitle($this->language->get('stripe_name'));
        $this->load->model('setting/setting');

        $this->document->addStyle(
            [
                'href'  => $this->view->templateResource('/stylesheet/stripe.css'),
                'rel'   => 'stylesheet',
                'media' => 'screen',
            ]
        );

        //did we get code from stripe connect
        if ($this->request->get['access_token']) {
            //need to save stripe access_token and set live mode
            if ($this->request->get['livemode']) {
                $settings = [
                    'stripe_access_token' => $this->request->get['access_token'],
                    'stripe_sk_live'      => $this->request->get['access_token'],
                    'stripe_pk_live'      => $this->request->get['pub_key'],
                    'stripe_test_mode'    => 0,
                ];
            }else{
                $settings = [
                    'stripe_access_token' => $this->request->get['access_token'],
                    'stripe_sk_test'      => $this->request->get['access_token'],
                    'stripe_pk_test'      => $this->request->get['pub_key'],
                    'stripe_test_mode'    => 1,
                ];
            }
            $this->model_setting_setting->editSetting('stripe', $settings);
            $this->session->data['success'] = $this->language->get('text_connect_success');
            redirect($this->html->getSecureURL('extension/stripe_settings'));
        } else {
            if ($this->request->get['disconnect']) {
                $this->model_setting_setting->editSetting(
                    'stripe',
                    [
                        'stripe_access_token' => '',
                        'stripe_pk_live' => '',
                        'stripe_sk_live' => '',
                        'stripe_sk_test' => '',
                        'stripe_pk_test' => ''
                    ]
                );
                $this->session->data['success'] = $this->language->get('text_disconnect_success');
                redirect($this->html->getSecureURL('extension/stripe_settings'));
            }
        }

        if ($this->request->is_POST() && $this->_validate()) {
            $this->model_setting_setting->editSetting('stripe', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            redirect($this->html->getSecureURL('extension/stripe_settings'));
        }

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }
        $this->data['success'] = $this->session->data['success'];
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $this->data['error'] = [];
        foreach ($this->errors as $f) {
            if (isset ($this->error[$f])) {
                $this->data['error'][$f] = $this->error[$f];
            }
        }
        //error with stripe connect?
        if ($this->request->get['error']) {
            $this->data['error'][$this->request->get['error']] = $this->request->get['error_dec'];
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
            'href'      => $this->html->getSecureURL('extension/extensions/payment'),
            'text'      => $this->language->get('text_payment'),
            'separator' => ' :: ',
            ]
        );
        $this->document->addBreadcrumb(
            [
            'href'      => $this->html->getSecureURL('payment/stripe'),
            'text'      => $this->language->get('stripe_name'),
            'separator' => ' :: ',
            'current'   => true,
            ]
        );

        foreach ($this->fields as $f) {
            if (isset ($this->request->post [$f])) {
                $this->data[$f] = $this->request->post[$f];
            } else {
                $this->data[$f] = $this->config->get($f);
            }
        }

        $this->data['disconnect'] = $this->html->getSecureURL(
            'extension/stripe_settings',
            '&extension=stripe&disconnect=true'
        );
        $this->data['heading_title'] = $this->language->get('text_edit').$this->language->get('stripe_name');
        $this->data['form_title'] = $this->language->get('heading_title');
        $this->data['update'] = $this->html->getSecureURL('r/extension/stripe/update');
        $url = base64_encode(
            $this->html->getSecureURL(
                'extension/stripe_settings',
                '&extension=stripe'
            )
        );
        $this->data['connect_url'] = base64_decode(
            'aHR0cHM6Ly9tYXJrZXRwbGFjZS5hYmFudGVjYXJ0LmNvbS9zdHJpcGVfY29ubmVjdC5waHA='
        );
        $this->data['connect_url'] .= '?clid=ca_5XtCjhqt1xB4wy8bMvr3QVlbtJg2coIs';
        $this->data['connect_url'] .= '&ret='.$url;

        //see if we are connected yet to stripe
        $stripe_code = $this->config->get('stripe_access_token');
        if ($stripe_code) {
            //validate the token
            $this->data['connected'] = true;
        }
        $form = new AForm('HT');
        $form->setForm(
            [
            'form_name' => 'editFrm',
            'update'    => $this->data ['update'],
            ]
        );

        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
            'type'   => 'form',
            'name'   => 'editFrm',
            'action' => $this->data ['action'],
            'attr'   => 'data-confirm-exit="true" class="aform form-horizontal"',
            ]
        );
        $this->data['form']['submit'] = $form->getFieldHtml(
            [
            'type' => 'button',
            'name' => 'submit',
            'text' => $this->language->get('button_save'),
            ]
        );


        $this->data['test_mode'] = $this->data['stripe_test_mode'];
        $this->data['form']['fields']['stripe_test_mode'] = $form->getFieldHtml(
            [
            'type'  => 'checkbox',
            'name'  => 'stripe_test_mode',
            'value' => $this->data['stripe_test_mode'],
            'style' => 'btn_switch',
            ]
        );

        $settlement = [
            'automatic'    => $this->language->get('stripe_settlement_auto'),
            'manual' => $this->language->get('stripe_settlement_delayed'),
        ];

        $this->data['form']['fields']['stripe_settlement'] = $form->getFieldHtml(
            [
            'type'    => 'selectbox',
            'name'    => 'stripe_settlement',
            'options' => $settlement,
            'value'   => $this->data['stripe_settlement'],
            ]
        );

        //load tabs controller
        $this->data['groups'][] = 'additional_settings';
        $this->data['link_additional_settings'] = $this->data['action'];
        $this->data['active_group'] = 'additional_settings';

        $tabs_obj = $this->dispatch('pages/extension/extension_tabs', [$this->data]);
        $this->data['tabs'] = $tabs_obj->dispatchGetOutput();
        unset($tabs_obj);

        $obj = $this->dispatch('pages/extension/extension_summary', [$this->data]);
        $this->data['extension_summary'] = $obj->dispatchGetOutput();
        unset($obj);

        $this->view->batchAssign($this->data);
        $this->view->batchAssign($this->language->getASet());
        $this->processTemplate('pages/extension/stripe_settings.tpl');
    }

    private function _validate()
    {
        if (!$this->user->canModify('stripe/stripe')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ($this->request->get['skip_connect']) {
            if($this->request->post['stripe_test_mode']){
                if (!$this->request->post['stripe_pk_test']) {
                    $this->error['stripe_pk_test'] = $this->language->get('error_stripe_pk_test');
                }
                if (!$this->request->post['stripe_sk_test']) {
                    $this->error['stripe_sk_test'] = $this->language->get('error_stripe_sk_test');
                }
            }else {
                if (!$this->request->post['stripe_sk_live']) {
                    $this->error['stripe_sk_live'] = $this->language->get('error_stripe_sk_live');
                }
                if (!$this->request->post['stripe_pk_live']) {
                    $this->error['stripe_pk_live'] = $this->language->get('error_stripe_pk_live');
                }
            }
        }

        return (!$this->error);
    }
}
