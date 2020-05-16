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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ControllerPagesIndexForgotPassword extends AController
{

    public $data = array();
    private $user_data;
    public $error = array();

    public function main()
    {
        if ($this->user->isLogged()) {
            $this->user->logout();
            unset($this->session->data['token']);
        }

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('common/forgot_password');
        $this->document->setTitle($this->language->get('heading_title'));

        if ($this->request->is_POST() && $this->_validate()) {

            //generate hash
            $hash = genToken(32);
            $enc = new AEncryption($this->config->get('encryption_key'));
            $rtoken = $enc->encrypt($this->request->post['username'].'::'.$hash);
            $link = $this->html->getSecureURL('index/forgot_password/validate', '&rtoken='.$rtoken);

            //create a scratch data for future use
            $password_reset = new ADataset ();
            $password_reset->createDataset('admin_pass_reset', $this->request->post['username']);
            $password_reset->setDatasetProperties(array(
                    'hash'  => $hash,
                    'email' => $this->request->post['email'],
                )
            );
            $mail = new AMail($this->config);
            $mail->setTo($this->request->post['email']);
            $mail->setFrom($this->config->get('store_main_email'));
            $mail->setSender($this->config->get('config_owner'));
            $mail->setTemplate('storefront_reset_password_link', [
                'store_name' => $this->config->get('store_name'),
                'reset_link' => $link
            ]);
            $mail->send();

            redirect($this->html->getSecureURL('index/forgot_password', '&mail=sent'));
        }

        $this->data['login'] = $this->html->getSecureURL('index/login');

        if (isset($this->request->get['mail']) && $this->request->get['mail'] == 'sent') {

            $this->data['show_instructions'] = true;

        } else {

            $this->data['error'] = $this->error;

            $fields = array('username', 'email', 'captcha');
            foreach ($fields as $f) {
                if (isset ($this->request->post [$f])) {
                    $this->data [$f] = $this->request->post [$f];
                } else {
                    $this->data[$f] = '';
                }
            }

            $this->data['action'] = $this->html->getSecureURL('index/forgot_password');
            $this->data['update'] = '';
            $form = new AForm('ST');

            $form->setForm(
                array(
                    'form_name' => 'forgotFrm',
                    'update'    => $this->data['update'],
                )
            );

            $this->data['form']['id'] = 'forgotFrm';
            $this->data['form']['form_open'] = $form->getFieldHtml(
                array(
                    'type'   => 'form',
                    'name'   => 'forgotFrm',
                    'action' => $this->data['action'],
                )
            );
            $this->data['form']['submit'] = $form->getFieldHtml(
                array(
                    'type'  => 'button',
                    'name'  => 'submit',
                    'text'  => $this->language->get('button_reset_password'),
                    'style' => 'button3',
                )
            );

            $this->data['form']['fields']['username'] = $form->getFieldHtml(
                array(
                    'type'        => 'input',
                    'name'        => 'username',
                    'value'       => $this->data['username'],
                    'required'    => true,
                    'placeholder' => $this->language->get('entry_username'),
                )
            );
            $this->data['form']['fields']['email'] = $form->getFieldHtml(
                array(
                    'type'        => 'input',
                    'name'        => 'email',
                    'value'       => $this->data['email'],
                    'required'    => true,
                    'placeholder' => $this->language->get('entry_email'),
                )
            );

            if ($this->config->get('config_recaptcha_site_key')) {
                $this->data['form']['fields']['captcha'] = $form->getFieldHtml(
                    array(
                        'type'               => 'recaptcha',
                        'name'               => 'captcha',
                        'recaptcha_site_key' => $this->config->get('config_recaptcha_site_key'),
                        'language_code'      => $this->language->getLanguageCode(),
                    )
                );
            } else {
                $this->data['form']['fields']['captcha'] = $form->getFieldHtml(
                    array(
                        'type'        => 'captcha',
                        'name'        => 'captcha',
                        'value'       => $this->data['captcha'],
                        'required'    => true,
                        'placeholder' => $this->language->get('entry_captcha'),
                    )
                );
            }
        }

        $this->view->batchAssign($this->data);

        $this->processTemplate('pages/index/forgot_password.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function validate()
    {
        if ($this->user->isLogged()) {
            $this->user->logout();
            unset($this->session->data['token']);
        }
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('common/forgot_password');
        $this->document->setTitle($this->language->get('heading_title'));

        //validate token
        $enc = new AEncryption($this->config->get('encryption_key'));
        list($username, $hash) = explode("::", $enc->decrypt($this->request->get['rtoken']));
        //get hash from dataset
        $dataset = new ADataset('admin_pass_reset', $username, 'silent');
        $reset_data = $dataset->getDatasetProperties();
        if ($this->_validateToken($reset_data, $hash) === false) {
            //not valid rtoken go back
            $this->main();
            return null;
        }

        $this->data['text_heading'] = $this->language->get('text_heading_reset');
        $this->data['login'] = $this->html->getSecureURL('index/login');

        if ($this->request->is_POST() && $this->_validatePassword()) {

            //generate password
            $password = $this->request->post['password'];
            $this->model_user_user->editUser($this->user_data['user_id'], array('password' => $password));

            $mail = new AMail($this->config);
            $mail->setTo($this->user_data['email']);
            $mail->setFrom($this->config->get('store_main_email'));
            $mail->setSender($this->config->get('config_owner'));
            $mail->setTemplate('storefront_reset_password_notify', ['store_name' => $this->config->get('store_name')]);
            $mail->send();

            //destroy scratch data
            $dataset->dropDataset();

            $this->data['show_instructions'] = true;
            $this->data['text_instructions'] = $this->language->get('text_instructions_reset');

            //all done and password is reset

        } else {

            $this->data['error'] = $this->error;
            $this->data['action'] = $this->html->getSecureURL('index/forgot_password/validate', '&rtoken='.$this->request->get['rtoken']);
            $this->data['update'] = '';
            $form = new AForm('ST');

            $form->setForm(
                array(
                    'form_name' => 'forgotFrm',
                    'update'    => $this->data['update'],
                )
            );

            $this->data['form']['id'] = 'forgotFrm';
            $this->data['form']['form_open'] = $form->getFieldHtml(
                array(
                    'type'   => 'form',
                    'name'   => 'forgotFrm',
                    'action' => $this->data['action'],
                )
            );
            $this->data['form']['submit'] = $form->getFieldHtml(
                array(
                    'type'  => 'button',
                    'name'  => 'submit',
                    'text'  => $this->language->get('text_please_confirm'),
                    'style' => 'button3',
                )
            );

            $this->data['form']['fields']['password'] = $form->getFieldHtml(
                array(
                    'type'  => 'passwordset',
                    'name'  => 'password',
                    'value' => $this->data['password'],
                )
            );

        }

        $this->view->batchAssign($this->data);

        $this->processTemplate('pages/index/forgot_password.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    private function _validate()
    {
        if ($this->config->get('config_recaptcha_secret_key')) {
            /** @noinspection PhpIncludeInspection */
            require_once DIR_VENDORS.'/google_recaptcha/autoload.php';
            $recaptcha = new \ReCaptcha\ReCaptcha($this->config->get('config_recaptcha_secret_key'));
            $resp = $recaptcha->verify($this->request->post['g-recaptcha-response'],
                $this->request->getRemoteIP());
            if (!$resp->isSuccess() && $resp->getErrorCodes()) {
                $this->error['captcha'] = $this->language->get('error_captcha');
                return false;
            }
        } else {
            if (!isset($this->session->data['captcha']) || ($this->session->data['captcha'] != $this->request->post['captcha'])) {
                $this->error['captcha'] = $this->language->get('error_captcha');
                return false;
            }
        }

        if (mb_strlen($this->request->post['username']) < 1) {
            $this->error['username'] = $this->language->get('error_username');
        }

        if (!preg_match(EMAIL_REGEX_PATTERN, $this->request->post['email'])) {
            $this->error['email'] = $this->language->get('error_email');
        }

        if (!$this->error && !$this->user->validate($this->request->post['username'], $this->request->post['email'])) {
            $this->error['warning'] = $this->language->get('error_match');
        }

        $this->extensions->hk_ValidateData($this);

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    private function _validateToken($reset_data, $check_hash)
    {
        $email = $reset_data['email'];
        $hash = $reset_data['hash'];
        if (empty($email) || empty($hash) || $hash != $check_hash) {
            $this->error['warning'] = $this->language->get('error_hash');
        } else {
            $this->loadModel('user/user');
            $users = $this->model_user_user->getUsers(array('subsql_filter' => "email = '".$this->db->escape($email)."'"));
            if (empty($users)) {
                $this->error['warning'] = $this->language->get('error_hash');
            } else {
                $this->user_data = $users[0];
            }
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    private function _validatePassword()
    {

        $this->loadLanguage('user/user');

        if (!empty($this->request->post['password'])) {
            if (mb_strlen($this->request->post['password']) < 4) {
                $this->error['password'] = $this->language->get('error_password');
            }

            if (!$this->error['password'] && $this->request->post['password'] != $this->request->post['password_confirm']) {
                $this->error['password'] = $this->language->get('error_confirm');
            }
        }

        $this->extensions->hk_ValidateData($this);

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
