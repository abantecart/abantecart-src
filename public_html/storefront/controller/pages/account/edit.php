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
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerPagesAccountEdit extends AController
{
    public $error = [];
    public static $formTxtId = 'CustomerFrm';

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->html->getSecureURL('account/edit');
            redirect($this->html->getSecureURL('account/login'));
        }

        $this->document->setTitle($this->language->get('heading_title'));
        /** @var ModelAccountCustomer $mdl */
        $mdl = $this->loadModel('account/customer');

        $post = $this->request->post;
        if ($this->request->is_POST()) {
            if ($this->csrftoken->isTokenValid()) {
                // validation based on field settings
                $this->validateForm($post);
                if (!isset($post['loginname'])) {
                    unset($this->error['loginname']);
                }

                //validation of IM-setting
                $this->error = array_merge($this->error, $mdl->validateEditData($post));
                //if no update for loginname do not allow edit of username/loginname
                if (!$this->customer->isLoginnameAsEmail()) {
                    $post['loginname'] = null;
                } else {
                    //if allow login as email, need to set loginname = email in case email changed
                    if (!$this->config->get('prevent_email_as_login')) {
                        $post['loginname'] = $post['email'];
                    }
                }
            } else {
                $this->error['warning'] = $this->language->get('error_unknown');
            }

            if (!$this->error) {
                unset(
                    $post['confirm'],
                    $post['agree'],
                    $post['csrftoken'],
                    $post['csrfinstance']
                );
                $mdl->editCustomer($post);
                $mdl->editCustomerNotifications($post);
                $this->session->data['success'] = $this->language->get('text_success');
                $this->extensions->hk_ProcessData($this);
                redirect($this->html->getSecureURL('account/account'));
            }
        }

        //check if existing customer has loginname = email. Redirect if not allowed
        $reset_loginname = false;
        if ($this->config->get('prevent_email_as_login') && $this->customer->isLoginnameAsEmail()) {
            $this->error['warning'] = $this->language->get('loginname_update_required');
            $reset_loginname = true;
        }

        $this->document->resetBreadcrumbs();

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getHomeURL(),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('account/account'),
                'text'      => $this->language->get('text_account'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('account/edit'),
                'text'      => $this->language->get('text_edit'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        $customerInfo = $this->model_account_customer->getCustomer($this->customer->getId());


        $form = new AForm();
        $form->setForm(['form_name' => static::$formTxtId]);
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => static::$formTxtId,
                'action' => $this->html->getSecureURL('account/edit'),
                'csrf'   => true,
            ]
        );

        $this->data['reset_loginname'] = $reset_loginname;

        $form->loadFromDb(static::$formTxtId);
        $formElements = $form->getFormElements()['general'];
        $this->data['error_warning'] = $this->error['warning'];
        foreach ($formElements as $name => $element) {
            //error messages
            $this->data['error_' . $name] = $this->error[$name];
            $this->data['entry_' . $name] = $element->display_name ?: $this->language->get('entry_' . $name);

            if ($name == 'country_id') {
                $element->value = $this->request->post['country_id']
                    ?? $customerInfo['country_id']
                    ?? $this->config->get('config_country_id');
            } elseif ($name == 'zone_id') {
                $element->zone_value = $this->data['zone_id'];
                //set zone_id as value for select[option]
                $element->submit_mode = 'id';
                //show only zone selector
                $element->zone_only = true;
            } else {
                $element->value = $this->request->post[$name]
                    ?: $customerInfo[$name]
                        //take extended fields value
                        ?: $customerInfo['ext_fields'][$name];
            }

            $this->data['form']['fields'][$name] = $element;
        }

        if (!$reset_loginname) {
            $this->data['form']['fields']['loginname'] = $this->data['form']['fields']['loginname']->value;
        }

        //get only active IM drivers
        $im_drivers = $this->im->getIMDriverObjects();
        if ($im_drivers) {
            foreach ($im_drivers as $protocol => $driver_obj) {
                if (!is_object($driver_obj) || $protocol == 'email') {
                    continue;
                }
                $value = $post[$protocol] ?? $customerInfo[$protocol];
                $fld = $driver_obj->getURIField($form, $value);
                $this->data['form']['fields'][$protocol] = $fld;
                $this->data['entry_' . $protocol] = $fld->label_text;
                $this->data['error_' . $protocol] = $this->error[$protocol];
            }
        }

        $this->data['form']['continue'] = // backward compatibility. Todo: Remove in the 1.5
        $this->data['form']['submit'] = $form->getFieldHtml(
            [
                'id'   => 'submit_button',
                'type' => 'submit',
                'name' => $this->language->get('button_continue'),
            ]
        );
        $this->data['form']['back'] = $form->getFieldHtml(
            [
                'type' => 'button',
                'name' => 'back',
                'text' => $this->language->get('button_back'),
                'href' => $this->html->getSecureURL('account/account')
            ]
        );

        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/account/edit.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function validateForm(array $data)
    {
        if (!$this->csrftoken->isTokenValid()) {
            $this->error['warning'] = $this->language->get('error_unknown');
        }

        $form = new AForm();
        $form->loadFromDb(static::$formTxtId);
        $this->error = $form->validateFormData($data);

        $this->extensions->hk_ValidateData($this, ['indata' => $data]);

        return (!$this->error);
    }
}