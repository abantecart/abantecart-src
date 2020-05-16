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
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerPagesAccountForgotten extends AController
{
    private $error = array();
    public $data = array();

    public function main()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->password();
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function password()
    {

        $this->extensions->hk_InitData($this, __FUNCTION__);

        if ($this->customer->isLogged()) {
            redirect($this->html->getSecureURL('account/account'));
        }

        $this->document->setTitle($this->language->get('heading_title'));
        $this->loadModel('account/customer');
        $customer_details = array();
        if ($this->request->is_POST()) {

            if (!$this->csrftoken->isTokenValid()) {
                $this->error['message'] = $this->language->get('error_unknown');
                return false;
            }

            if ($this->_find_customer('password', $customer_details)) {
                //extra check that we have customer details
                if (!empty($customer_details['email'])) {
                    $this->loadLanguage('mail/account_forgotten');

                    $customer_id = $customer_details['customer_id'];
                    $code = genToken(32);
                    //save password reset code
                    $this->model_account_customer->updateOtherData($customer_id, array('password_reset' => $code));
                    //build reset link
                    $enc = new AEncryption($this->config->get('encryption_key'));
                    $rtoken = $enc->encrypt($customer_id.'::'.$code);

                    //do the trick for correct url
                    $embed_mode = $this->registry->get('config')->get('embed_mode');
                    $this->registry->get('config')->set('embed_mode', false);
                    $link = $this->html->getSecureURL('account/forgotten/reset', '&rtoken='.$rtoken);
                    $this->registry->get('config')->set('embed_mode', $embed_mode);

                    $subject = sprintf($this->language->get('text_subject'), $this->config->get('store_name'));
                    $message = sprintf($this->language->get('text_greeting'), $this->config->get('store_name'))."\n\n";
                    $message .= $this->language->get('text_password')."\n\n";
                    $message .= $link;

                    $mail = new AMail($this->config);
                    $mail->setTo($customer_details['email']);
                    $mail->setFrom($this->config->get('store_main_email'));
                    $mail->setSender($this->config->get('store_name'));
                    $mail->setSubject($subject);
                    $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
                    $mail->send();

                    $this->session->data['success'] = $this->language->get('text_success');
                    redirect($this->html->getSecureURL('account/login'));
                }
            }
        }

        $this->document->resetBreadcrumbs();

        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getHomeURL(),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
        ));

        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL('account/account'),
            'text'      => $this->language->get('text_account'),
            'separator' => $this->language->get('text_separator'),
        ));

        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL('account/forgotten/password'),
            'text'      => $this->language->get('text_forgotten'),
            'separator' => $this->language->get('text_separator'),
        ));

        $this->view->assign('error', $this->error['message']);
        $this->view->assign('action', $this->html->getSecureURL('account/forgotten'));
        $this->view->assign('back', $this->html->getSecureURL('account/account'));

        $form = new AForm();
        $form->setForm(array('form_name' => 'forgottenFrm'));
        $this->data['form']['form_open'] = $form->getFieldHtml(
            array(
                'type'   => 'form',
                'name'   => 'forgottenFrm',
                'action' => $this->html->getSecureURL('account/forgotten/password'),
                'csrf'   => true,
            )
        );

        //verify loginname if non email login used or data encryption is ON
        if ($this->config->get('prevent_email_as_login') || $this->dcrypt->active) {
            $this->data['form']['fields']['loginname'] = $form->getFieldHtml(array(
                'type'  => 'input',
                'name'  => 'loginname',
                'value' => $this->request->post['loginname'],
            ));
            $this->data['help_text'] = $this->language->get('text_loginname_email');
        } else {
            $this->data['help_text'] = $this->language->get('text_email');
        }

        $this->data['form']['fields']['email'] = $form->getFieldHtml(array(
            'type'  => 'input',
            'name'  => 'email',
            'value' => $this->request->post['email'],
        ));

        $this->data['form']['continue'] = $form->getFieldHtml(array(
            'type' => 'submit',
            'name' => $this->language->get('button_continue'),
        ));
        $this->data['form']['back'] = $form->getFieldHtml(array(
            'type'  => 'button',
            'name'  => 'back',
            'style' => 'button',
            'text'  => $this->language->get('button_back'),
        ));
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/account/forgotten.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

    }

    public function reset()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('mail/account_forgotten');

        if ($this->customer->isLogged()) {
            redirect($this->html->getSecureURL('account/account'));
        }
        $this->document->setTitle($this->language->get('heading_title'));
        $this->loadModel('account/customer');
        //validate token
        $rtoken = $this->request->get['rtoken'];
        $enc = new AEncryption($this->config->get('encryption_key'));
        list($customer_id, $code) = explode("::", $enc->decrypt($rtoken));
        $customer_details = $this->model_account_customer->getCustomer($customer_id);
        if (empty($customer_id) || empty($customer_details['data']['password_reset']) || $customer_details['data']['password_reset'] != $code) {
            $this->error['message'] = $this->language->get('error_reset_token');
            return $this->password();
        }

        if ($this->request->is_POST() && $this->_validatePassword()) {

            if (!$this->csrftoken->isTokenValid()) {
                $this->error['warning'] = $this->language->get('error_unknown');
                return false;
            }

            //extra check that we have customer details
            if (!empty($customer_details['email'])) {
                $this->loadLanguage('mail/account_forgotten');
                $this->model_account_customer->editPassword($customer_details['loginname'], $this->request->post['password']);
                $subject = sprintf($this->language->get('text_subject'), $this->config->get('store_name'));
                $message = sprintf($this->language->get('text_password_reset'), $this->config->get('store_name'))."\n\n";
                $mail = new AMail($this->config);
                $mail->setTo($customer_details['email']);
                $mail->setFrom($this->config->get('store_main_email'));
                $mail->setSender($this->config->get('store_name'));
                $mail->setSubject($subject);
                $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
                $mail->send();

                //update data and remove password_reset code
                unset($customer_details['data']['password_reset']);
                $this->model_account_customer->updateOtherData($customer_id, $customer_details['data']);

                $this->session->data['success'] = $this->language->get('text_success');
                redirect($this->html->getSecureURL('account/login'));
            }
        }

        $this->loadLanguage('account/password');
        $this->document->resetBreadcrumbs();
        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getHomeURL(),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
        ));

        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getURL('account/account'),
            'text'      => $this->language->get('text_account'),
            'separator' => $this->language->get('text_separator'),
        ));

        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getURL('account/forgotten/password'),
            'text'      => $this->language->get('text_forgotten'),
            'separator' => $this->language->get('text_separator'),
        ));

        $this->view->assign('error_warning', $this->error['warning']);
        $this->view->assign('error_password', $this->error['password']);
        $this->view->assign('error_confirm', $this->error['confirm']);

        $form = new AForm();
        $form->setForm(array('form_name' => 'PasswordFrm'));
        $form_open = $form->getFieldHtml(
            array(
                'type'   => 'form',
                'name'   => 'PasswordFrm',
                'action' => $this->html->getSecureURL('account/forgotten/reset', '&rtoken='.$rtoken),
                'csrf'   => true,
            )
        );
        $this->view->assign('form_open', $form_open);

        $password = $form->getFieldHtml(
            array(
                'type'     => 'password',
                'name'     => 'password',
                'value'    => '',
                'required' => true,
            ));
        $confirm = $form->getFieldHtml(
            array(
                'type'     => 'password',
                'name'     => 'confirm',
                'value'    => '',
                'required' => true,
            ));
        $submit = $form->getFieldHtml(
            array(
                'type' => 'submit',
                'name' => $this->language->get('button_continue'),
                'icon' => 'fa fa-check',
            ));

        $this->view->assign('password', $password);
        $this->view->assign('submit', $submit);
        $this->view->assign('confirm', $confirm);
        $this->view->assign('back', $this->html->getSecureURL('account/account'));

        $back = $this->html->buildElement(
            array(
                'type'  => 'button',
                'name'  => 'back',
                'text'  => $this->language->get('button_back'),
                'icon'  => 'fa fa-arrow-left',
                'style' => 'button',
            ));
        $this->view->assign('button_back', $back);
        $this->processTemplate('pages/account/password_reset.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

    }

    public function loginname()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);
        if ($this->customer->isLogged()) {
            redirect($this->html->getSecureURL('account/account'));
        }
        $this->document->setTitle($this->language->get('heading_title_loginname'));
        $this->loadModel('account/customer');
        $customer_details = array();
        if ($this->request->is_POST()) {
            if ($this->_find_customer('loginname', $customer_details)) {
                //extra check that we have customer details
                if (!empty($customer_details['email'])) {
                    $this->loadLanguage('mail/account_forgotten_login');
                    $subject = sprintf($this->language->get('text_subject'), $this->config->get('store_name'));
                    $message = sprintf($this->language->get('text_greeting'), $this->config->get('store_name'))."\n\n";
                    $message .= $this->language->get('text_your_loginname')."\n\n";
                    $message .= $customer_details['loginname'];

                    $mail = new AMail($this->config);
                    $mail->setTo($customer_details['email']);
                    $mail->setFrom($this->config->get('store_main_email'));
                    $mail->setSender($this->config->get('store_name'));
                    $mail->setSubject($subject);
                    $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
                    $mail->send();

                    $this->session->data['success'] = $this->language->get('text_success_loginname');
                    redirect($this->html->getSecureURL('account/login'));
                }
            }
        }

        $this->document->resetBreadcrumbs();
        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getHomeURL(),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
        ));

        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL('account/account'),
            'text'      => $this->language->get('text_account'),
            'separator' => $this->language->get('text_separator'),
        ));

        $this->document->addBreadcrumb(
            array(
                'href'      => $this->html->getSecureURL('account/forgotten/loginname'),
                'text'      => $this->language->get('text_forgotten_loginname'),
                'separator' => $this->language->get('text_separator'),
            )
        );

        $this->view->assign('error', $this->error['message']);
        $this->view->assign('action', $this->html->getSecureURL('account/forgotten'));
        $this->view->assign('back', $this->html->getSecureURL('account/account'));

        $form = new AForm();
        $form->setForm(array('form_name' => 'forgottenFrm'));
        $this->data['form']['form_open'] = $form->getFieldHtml(
            array(
                'type'   => 'form',
                'name'   => 'forgottenFrm',
                'action' => $this->html->getSecureURL('account/forgotten/loginname'),
                'csrf'   => true,
            )
        );

        $this->data['help_text'] = $this->language->get('text_lastname_email');
        $this->data['heading_title'] = $this->language->get('heading_title_loginname');

        $this->data['form']['fields']['lastname'] = $form->getFieldHtml(array(
            'type'  => 'input',
            'name'  => 'lastname',
            'value' => $this->request->post['lastname'],
        ));
        $this->data['form']['fields']['email'] = $form->getFieldHtml(array(
            'type'  => 'input',
            'name'  => 'email',
            'value' => $this->request->post['email'],
        ));

        $this->data['form']['continue'] = $form->getFieldHtml(array(
            'type' => 'submit',
            'name' => $this->language->get('button_continue'),
        ));
        $this->data['form']['back'] = $form->getFieldHtml(
            array(
                'type'  => 'button',
                'name'  => 'back',
                'style' => 'button',
                'text'  => $this->language->get('button_back'),
            ));
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/account/forgotten.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

    }

    /**
     * @param string $mode
     * @param array  $customer_details - reference
     *
     * @return bool
     */
    private function _find_customer($mode, &$customer_details)
    {
        $email = $this->request->post['email'];
        $loginname = $this->request->post['loginname'];
        $lastname = $this->request->post['lastname'];
        //email is always required 
        if (!isset($email) || empty($email)) {
            $this->error['message'] = $this->language->get('error_email');
            return false;
        }

        //locate customer based on login name
        if ($this->config->get('prevent_email_as_login') || $this->dcrypt->active) {
            if ($mode == 'password') {
                if (!empty($loginname)) {
                    $customer_details = $this->model_account_customer->getCustomerByLoginnameAndEmail($loginname, $email);
                } else {
                    $this->error['message'] = $this->language->get('error_loginname');
                    return false;
                }
            } else {
                if ($mode == 'loginname') {
                    if (!empty($lastname)) {
                        $customer_details = $this->model_account_customer->getCustomerByLastnameAndEmail($lastname, $email);
                    } else {
                        $this->error['message'] = $this->language->get('error_lastname');
                        return false;
                    }
                }
            }
        } else {
            //get customer by email
            $customer_details = $this->model_account_customer->getCustomerByEmail($email);
        }

        if (!count($customer_details)) {
            $this->error['message'] = $this->language->get('error_not_found');
            return false;
        } else {
            return true;
        }
    }

    private function _validatePassword()
    {
        $this->loadLanguage('account/password');
        $post = $this->request->post;

        //check password length considering html entities (special case for characters " > < & )
        $pass_len = mb_strlen(htmlspecialchars_decode($post['password']));
        if ($pass_len < 4 || $pass_len > 20) {
            $this->error['password'] = $this->language->get('error_password');
        }

        if ($post['confirm'] != $post['password']) {
            $this->error['confirm'] = $this->language->get('error_confirm');
        }

        if (!$this->error) {
            return true;
        } else {
            $this->error['warning'] = $this->language->get('gen_data_entry_error');
            return false;
        }
    }
}
