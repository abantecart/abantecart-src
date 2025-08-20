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

class ControllerPagesAccountCreate extends AController
{
    const formTxtId = 'RegisterCustomerFrm';
    public $errors = [];

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        if ($this->customer->isLogged()) {
            redirect($this->html->getSecureURL('account/account'));
        }

        $this->document->setTitle($this->language->get('heading_title'));
        /** @var ModelAccountCustomer $mdl */
        $mdl = $this->loadModel('account/customer');
        $post = $this->request->post;
        if ($this->request->is_POST()) {
            if ($this->csrftoken->isTokenValid()) {
                // validation based on field settings
                $this->validateForm($post);
                $this->errors = array_merge( $this->errors, $mdl->validateRegistrationData($post) );
            } else {
                $this->errors['warning'] = $this->language->get('error_unknown');
            }
            if (!$this->errors) {
                //if allow login as email, need to set loginname = email
                if (!$this->config->get('prevent_email_as_login')) {
                    $post['loginname'] = $post['email'];
                }

                unset(
                    $post['csrftoken'],
                    $post['csrfinstance'],
                    $post['confirm'],
                    $post['agree'],
                    $post['captcha']
                );
                $this->data['customer_id'] = $mdl->addCustomer($post);
                $mdl->editCustomerNotifications($post, $this->data['customer_id']);
                unset($this->session->data['guest']);

                try {
                    if (!$this->config->get('config_customer_approval')) {
                        //add and send account activation link if required
                        if (!$this->config->get('config_customer_email_activation')) {
                            //send welcome email
                            $mdl->sendWelcomeEmail($post['email'], true);
                            //login customer after create account is approving and
                            // email activation are disabled in settings
                            $this->customer->login($post['loginname'], $post['password']);
                        } else {
                            //send activation email request and wait for confirmation
                            $mdl->emailActivateLink($this->data['customer_id']);
                        }
                    } else {
                        //send welcome email, but need manual approval
                        $mdl->sendWelcomeEmail($post['email'], false);
                    }
                }catch (Exception $e) {
                    $this->log->write(__CLASS__ . '::' . __FUNCTION__ . '() error: ' . $e->getMessage());
                    $this->messages->saveError("Mailer Critical Error!", $e->getMessage());
                }

                $this->extensions->hk_UpdateData($this, __FUNCTION__);

                //set success text for non-approved customers on login page after redirect
                if ($this->config->get('config_customer_approval')) {
                    $this->loadLanguage('account/success');
                    $this->session->data['success'] = $this->language->getAndReplace(
                        'text_approval',
                        'account/success',
                        [
                            $this->config->get('store_name'),
                            $this->html->getSecureURL('content/contact')
                        ]
                    );
                }

                if ($this->config->get('config_customer_email_activation') || !$this->session->data['redirect']) {
                    $redirect_url = $this->html->getSecureURL('account/success');
                } else {
                    $redirect_url = $this->session->data['redirect'];
                }
                redirect($redirect_url);
            } else {
                if (!$this->errors['warning']) {
                    $this->errors['warning'] = implode('<br>', $this->errors);
                }
            }
        }

        $this->document->initBreadcrumb(
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
                'href'      => $this->html->getSecureURL('account/create'),
                'text'      => $this->language->get('text_create'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        if ($this->config->get('prevent_email_as_login')) {
            $this->data['noemaillogin'] = true;
        }

        $form = new AForm();
        $form->setForm(['form_name' => self::formTxtId]);
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => self::formTxtId,
                'action' => $this->html->getSecureURL('account/create'),
                'csrf'   => true,
            ]
        );

        //default country
        $post['country_id'] = $post['country_id'] ?? $this->config->get('config_country_id');

        //address Form part based on database data
        $registerForm = new AForm();
        $registerForm->loadFromDb(self::formTxtId);
        $formFields = $registerForm->getFormElements(self::formTxtId);
        foreach ($formFields as $groupTxtId => $fields) {
            foreach ($fields as $name => $element) {
                //error messages
                $this->data['error_' . $name] = $this->errors[$name];
                $this->data['entry_' . $name] = $element->display_name ?: $this->language->get('entry_' . $name);
                if(isset($post[$name])){
                    $element->value = $post[$name];
                }
                if ($name == 'zone_id') {
                    $element->value = $post['country_id'] ?? $this->config->get('config_country_id');
                    $element->zone_value = $post['zone_id']?: $this->config->get('config_zone_id');
                    //set zone_id as value for select[option]
                    $element->submit_mode = 'id';
                    //show only zone selector
                    $element->zone_only = true;
                }
                $this->data['form']['fields'][$groupTxtId][$name] = $element;
            }
        }

        //get only active IM drivers
        $im_drivers = $this->im->getIMDriverObjects();
        if ($im_drivers) {
            foreach ($im_drivers as $protocol => $driver_obj) {
                /** @var AMailIM $driver_obj */
                if (!is_object($driver_obj) || $protocol == 'email') {
                    continue;
                }
                $fld = $driver_obj->getURIField($form, $this->request->post[$protocol]);
                $fld->display_name = $fld->label_text;
                $this->data['form']['fields']['details'][$protocol] = $fld;
            }
        }

        if (!$this->config->get('prevent_email_as_login')) { // require login name
            unset($this->data['form']['fields']['login']['loginname']);
        }

        $this->data['form']['fields']['login']['password'] = $form->getFieldHtml(
            [
                'type'     => 'password',
                'name'     => 'password',
                'value'    => $this->request->post['password'],
                'required' => true,
                'display_name' => $this->language->get('entry_password'),
            ]
        );
        $this->data['form']['fields']['login']['confirm'] = $form->getFieldHtml(
            [
                'type'     => 'password',
                'name'     => 'confirm',
                'value'    => $this->request->post['confirm'],
                'required' => true,
                'display_name' => $this->language->get('entry_confirm'),
            ]
        );

        if($this->request->get_or_post('newsletter')){
            $this->data['form']['fields']['newsletter']['newsletter']->checked = (bool)$this->request->get_or_post('newsletter');
        }

        //If captcha enabled, validate
        if ($this->config->get('config_account_create_captcha')) {
            if ($this->config->get('config_recaptcha_site_key')) {
                $captchaData = [
                    'type'               => 'recaptcha',
                    'name'               => 'g-recaptcha-response',
                    'recaptcha_site_key' => $this->config->get('config_recaptcha_site_key'),
                    'language_code'      => $this->language->getLanguageCode(),
                    'display_name'       => $this->language->get('entry_captcha','account/newsletter'),
                ];
            } else {
                $captchaData = [
                    'type' => 'captcha',
                    'name' => 'captcha',
                    'display_name'       => $this->language->get('entry_captcha','account/newsletter'),
                ];
            }
            $this->data['form']['fields']['newsletter']['captcha'] = $form->getFieldHtml( $captchaData);
        }

        $this->data['form']['agree'] = $form->getFieldHtml(
            [
                'type'    => 'checkbox',
                'name'    => 'agree',
                'value'   => 1,
                'checked' => $this->request->post['agree'] ?? false,
            ]
        );

        $this->data['form']['continue'] = $form->getFieldHtml(
            [
                'type' => 'submit',
                'name' => $this->language->get('button_continue'),
            ]
        );

        $this->data['error_password'] = $this->errors['password'];
        $this->data['error_confirm'] = $this->errors['confirm'];
        $this->data['error_captcha'] = $this->errors['captcha'];

        $this->data['action'] = $this->html->getSecureURL('account/create');
        $this->data['newsletter'] = $this->request->post['newsletter'];

        $contentId = (int)$this->config->get('config_account_id');
        $this->data['text_agree'] = '';
        if ($contentId) {
            /** @var ModelCatalogContent $mdl */
            $mdlC = $this->loadModel('catalog/content');
            $contentInfo = $mdlC->getContent($contentId);
            if ($contentInfo) {
                $this->data['text_agree'] = $this->language->get('text_agree');
                $this->data['text_agree_href'] = $this->html->getSecureURL(
                    'r/content/content/loadInfo',
                    '&content_id='.$contentId
                );
                $this->data['text_agree_href_text'] = $contentInfo['title'];
            }
        }

        $this->data['text_account_already'] = $this->language->getAndReplace(
            'text_account_already',
            replaces: $this->html->getSecureURL('account/login')
        );

        $this->data['error_warning'] = $this->errors['warning'];
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/account/create.tpl');
        unset($this->session->data['fc']);

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function resend()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        /** @var ModelAccountCustomer $mdl */
        $mdl = $this->loadModel('account/customer');
        $enc = new AEncryption($this->config->get('encryption_key'));
        list($customer_id, $activation_code) = explode("::", $enc->decrypt($this->request->get['rid']));
        if ($customer_id && $activation_code) {
            try {
                $mdl->emailActivateLink($customer_id);
            }catch (Exception $e) {
                $this->log->write(__CLASS__ . '::' . __FUNCTION__ . '() error: ' . $e->getMessage());
                $this->messages->saveError("Mailer Critical Error!", $e->getMessage());
            }
        }
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        redirect($this->html->getSecureURL('account/success'));
    }

    /**
     * @param array $data
     * @return bool
     * @throws AException
     */
    protected function validateForm(array $data)
    {
        if (!$this->csrftoken->isTokenValid()) {
            $this->errors['warning'] = $this->language->get('error_unknown');
        }

        $form = new AForm();
        $form->loadFromDb(self::formTxtId);
        $this->errors = $form->validateFormData($data);
        if (!$this->config->get('prevent_email_as_login')) {
            unset($this->errors['loginname']);
        }

        $this->extensions->hk_ValidateData($this, ['indata' => $data]);

        return (!$this->errors);
    }
}
