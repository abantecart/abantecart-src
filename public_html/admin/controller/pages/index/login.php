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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ControllerPagesIndexLogin extends AController
{
    public $error = [];

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('common/login');
        $this->document->setTitle($this->language->get('heading_title'));

        $this->document->addBreadcrumb(
            [
                'href'      => '',
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'     => $this->html->getSecureURL('index/login'),
                'text'     => $this->language->get('heading_title'),
                'current'  => true,
                'sub_text' => '',
                'icon'     => ''
            ]
        );

        if ($this->request->is_POST() && $this->_validate()) {
            $this->session->data['token'] = genToken(32);
            $this->session->data['checkupdates'] = true;
            $this->user->setActiveToken($this->session->data['token']);
            // sign to run ajax-request to check for updates. see common/head for details
            //login is successfully redirected to the originally requested page
            if (isset($this->request->post['redirect'])
                && !preg_match("/rt=index\/login/i", $this->request->post['redirect'])
            ) {
                $redirect = $this->html->filterQueryParams($this->request->post['redirect'], ['token']);
                $redirect .= "&token=" . $this->session->data['token'];
                redirect($redirect);
            } else {
                redirect($this->html->getSecureURL('index/home'));
            }
        }

        if (
            (isset($this->session->data['token']) && !isset($this->request->get['token']))
            || ((isset($this->request->get['token'])
                && (isset($this->session->data['token'])
                    && ($this->request->get['token'] != $this->session->data['token']))))
        ) {
            $this->error['warning'] = $this->language->get('error_token');
        }

        $this->data['action'] = $this->html->getSecureURL('index/login');
        $this->data['update'] = '';
        $form = new AForm('ST');

        $form->setForm(
            [
                'form_name' => 'loginFrm',
                'update'    => $this->data['update'],
            ]
        );

        $this->data['form']['id'] = 'loginFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'loginFrm',
                'action' => $this->data['action'],
            ]
        );
        $this->data['form']['submit'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'submit',
                'text'  => $this->language->get('button_login'),
                'style' => 'button3',
            ]
        );

        $fields = ['username', 'password'];
        foreach ($fields as $f) {
            $this->data['form']['fields'][$f] = $form->getFieldHtml(
                [
                    'type'        => ($f == 'password' ? 'password' : 'input'),
                    'name'        => $f,
                    'value'       => $this->data[$f],
                    'placeholder' => $this->language->get('entry_' . $f),
                ]
            );
        }

        //run critical system check
        $check_result = run_critical_system_check($this->registry);

        if ($check_result) {
            $this->error['warning'] = '';
            foreach ($check_result as $log) {
                $this->error['warning'] .= $log['body'] . "\n";
            }
        }

        //non-secure check
        if (HTTPS !== true
            && $this->config->get('config_ssl_url')
            && str_starts_with($this->config->get('config_ssl_url'), 'https://')
        ) {
            $this->error['warning'] .= $this->language->getAndReplace(
                          'error_login_secure',
                replaces: 'https://' . REAL_HOST . HTTP_DIR_NAME . '/?s=' . ADMIN_PATH
            );
        }

        $this->view->assign('error_warning', $this->error['warning']);
        $this->view->assign('forgot_password', $this->html->getSecureURL('index/forgot_password'));

        if (isset($this->request->get['rt'])) {
            $route = $this->request->get['rt'];
            unset($this->request->get['rt']);
            if (isset($this->request->get['token'])) {
                unset($this->request->get['token']);
            }
            $url = '';
            if ($this->request->get) {
                $url = '&' . http_build_query($this->request->get);
            }
            if ($this->request->is_POST()) {
                // if a login attempt failed - save a path for redirect
                $this->view->assign('redirect', $this->request->post['redirect']);
            } else {
                $this->view->assign('redirect', $this->html->getSecureURL($route, $url));
            }
        } else {
            $this->view->assign('redirect', '');
        }

        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/index/login.tpl');
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    private function _validate()
    {
        if (isset($this->request->post['username']) && isset($this->request->post['password'])
            && !$this->user->login($this->request->post['username'], $this->request->post['password'])
        ) {
            $this->error['warning'] = $this->language->get('error_login');
        }
        if (!$this->error) {
            return true;
        } else {
            $this->messages->saveNotice(
                $this->language->get('error_login_message') . $this->request->getRemoteIP(),
                $this->language->get('error_login_message_text') . $this->request->post['username']
            );
            return false;
        }
    }
}  
