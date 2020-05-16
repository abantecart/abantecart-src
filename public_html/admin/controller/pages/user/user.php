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

class ControllerPagesUserUser extends AController
{
    public $data = array();
    public $error = array();
    protected $fields = array('username', 'firstname', 'lastname', 'email', 'user_group_id', 'status');

    public function main()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->document->setTitle($this->language->get('heading_title'));

        $this->view->assign('error_warning', $this->error['warning']);
        $this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $this->document->initBreadcrumb(array(
            'href'      => $this->html->getSecureURL('index/home'),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
        ));
        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL('user/user'),
            'text'      => $this->language->get('heading_title'),
            'separator' => ' :: ',
            'current'   => true,
        ));

        $grid_settings = array(
            'table_id'     => 'user_grid',
            'url'          => $this->html->getSecureURL('listing_grid/user'),
            'editurl'      => $this->html->getSecureURL('listing_grid/user/update'),
            'update_field' => $this->html->getSecureURL('listing_grid/user/update_field'),
            'sortname'     => 'username',
            'sortorder'    => 'asc',
            'actions'      => array(
                'edit'   => array(
                    'text' => $this->language->get('text_edit'),
                    'href' => $this->html->getSecureURL('user/user/update', '&user_id=%ID%'),
                ),
                'save'   => array(
                    'text' => $this->language->get('button_save'),
                ),
                'delete' => array(
                    'text' => $this->language->get('button_delete'),
                ),
            ),
        );

        $grid_settings['colNames'] = array(
            $this->language->get('column_username'),
            $this->language->get('column_group'),
            $this->language->get('column_status'),
            $this->language->get('column_date_added'),
        );
        $grid_settings['colModel'] = array(
            array(
                'name'  => 'username',
                'index' => 'username',
                'width' => 300,
                'align' => 'left',
            ),
            array(
                'name'   => 'user_group_id',
                'index'  => 'user_group_id',
                'width'  => 120,
                'align'  => 'left',
                'search' => false,
            ),
            array(
                'name'   => 'status',
                'index'  => 'status',
                'width'  => 130,
                'align'  => 'center',
                'search' => false,
            ),
            array(
                'name'   => 'date_added',
                'index'  => 'date_added',
                'width'  => 100,
                'align'  => 'center',
                'search' => false,
            ),
        );

        $statuses = array(
            '' => $this->language->get('text_select_status'),
            1  => $this->language->get('text_enabled'),
            0  => $this->language->get('text_disabled'),
        );

        $this->loadModel('user/user_group');
        $user_groups = array('' => $this->language->get('text_select_group'),);
        $results = $this->model_user_user_group->getUserGroups();
        foreach ($results as $r) {
            $user_groups[$r['user_group_id']] = $r['name'];
        }

        $form = new AForm();
        $form->setForm(array(
            'form_name' => 'user_grid_search',
        ));

        $grid_search_form = array();
        $grid_search_form['id'] = 'user_grid_search';
        $grid_search_form['form_open'] = $form->getFieldHtml(array(
            'type'   => 'form',
            'name'   => 'user_grid_search',
            'action' => '',
        ));
        $grid_search_form['submit'] = $form->getFieldHtml(array(
            'type'  => 'button',
            'name'  => 'submit',
            'text'  => $this->language->get('button_go'),
            'style' => 'button1',
        ));
        $grid_search_form['reset'] = $form->getFieldHtml(array(
            'type'  => 'button',
            'name'  => 'reset',
            'text'  => $this->language->get('button_reset'),
            'style' => 'button2',
        ));
        $grid_search_form['fields']['status'] = $form->getFieldHtml(array(
            'type'    => 'selectbox',
            'name'    => 'status',
            'options' => $statuses,
        ));
        $grid_search_form['fields']['group'] = $form->getFieldHtml(array(
            'type'    => 'selectbox',
            'name'    => 'user_group_id',
            'options' => $user_groups,
        ));

        $grid_settings['search_form'] = true;

        $grid = $this->dispatch('common/listing_grid', array($grid_settings));
        $this->view->assign('listing_grid', $grid->dispatchGetOutput());
        $this->view->assign('search_form', $grid_search_form);

        $this->view->assign('insert', $this->html->getSecureURL('user/user/insert'));
        $this->view->assign('help_url', $this->gen_help_url('user_listing'));

        $this->processTemplate('pages/user/user_list.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function insert()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('user/user');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->loadModel('user/user');
        if ($this->request->is_POST() && $this->validateForm()) {
            $user_id = $this->model_user_user->addUser($this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            redirect($this->html->getSecureURL('user/user/update', '&user_id='.$user_id));
        }
        $this->getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function update()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('user/user');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->loadModel('user/user');

        $this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        if ($this->request->is_POST() && $this->validateForm()) {
            $this->model_user_user->editUser($this->request->get['user_id'], $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            redirect($this->html->getSecureURL('user/user/update', '&user_id='.$this->request->get['user_id']));
        }
        $this->getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function getForm()
    {

        $this->data = array();
        $this->data['error'] = $this->error;

        $this->document->initBreadcrumb(array(
            'href'      => $this->html->getSecureURL('index/home'),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
        ));
        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL('user/user'),
            'text'      => $this->language->get('heading_title'),
            'separator' => ' :: ',
        ));

        $this->data['cancel'] = $this->html->getSecureURL('user/user');

        if (isset($this->request->get['user_id'])) {
            $user_id = $this->request->get['user_id'];
            $this->data['user_id'] = $user_id;
            $user_info = $this->model_user_user->getUser($user_id);
        } else {
            $user_id = 0;
        }

        foreach ($this->fields as $f) {
            if (isset($user_info)) {
                $this->data[$f] = $user_info[$f];
            } elseif (isset($this->request->post[$f])) {
                $this->data[$f] = $this->request->post[$f];
            } else {
                $this->data[$f] = '';
            }
        }

        if (!$user_id) {
            $this->data['action'] = $this->html->getSecureURL('user/user/insert');
            $this->data['heading_title'] = $this->language->get('text_insert')
                .'&nbsp;'
                .$this->language->get('text_user');
            $this->data['update'] = '';
            $form = new AForm('ST');
        } else {
            $this->data['action'] = $this->html->getSecureURL('user/user/update', '&user_id='.$user_id);
            $this->data['heading_title'] =
                $this->language->get('text_edit')
                .'&nbsp;'
                .$this->language->get('text_user')
                .' - '
                .$this->data['username'];
            $this->data['update'] = $this->html->getSecureURL('listing_grid/user/update_field', '&id='.$user_id);
            $form = new AForm('HS');
            $this->data['edit_im_url'] = $this->html->getSecureURL('user/user/im', '&user_id='.$user_id);

        }

        $this->document->addBreadcrumb(array(
            'href'      => $this->data['action'],
            'text'      => $this->data['heading_title'],
            'separator' => ' :: ',
            'current'   => true,
        ));

        $form->setForm(array(
            'form_name' => 'cgFrm',
            'update'    => $this->data['update'],
        ));

        $this->data['form']['id'] = 'cgFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
            'type'   => 'form',
            'name'   => 'cgFrm',
            'action' => $this->data['action'],
            'attr'   => 'data-confirm-exit="true" class="aform form-horizontal"',
        ));
        $this->data['form']['submit'] = $form->getFieldHtml(array(
            'type'  => 'button',
            'name'  => 'submit',
            'text'  => $this->language->get('button_save'),
            'style' => 'button1',
        ));
        $this->data['form']['cancel'] = $form->getFieldHtml(array(
            'type'  => 'button',
            'name'  => 'cancel',
            'text'  => $this->language->get('button_cancel'),
            'style' => 'button2',
        ));

        $this->data['form']['fields']['status'] = $form->getFieldHtml(array(
            'type'  => 'checkbox',
            'name'  => 'status',
            'value' => $this->data['status'],
            'style' => 'btn_switch',
        ));

        $input = array('username', 'firstname', 'lastname', 'password');
        foreach ($input as $f) {
            $this->data['form']['fields'][$f] = $form->getFieldHtml(array(
                'type'     => ($f == 'password' ? 'passwordset' : 'input'),
                'name'     => $f,
                'value'    => $this->data[$f],
                'required' => true,
                'attr'     => (in_array($f, array('password', 'password_confirm')) ? 'class="no-save"' : ''),
                'style'    => ($f == 'password' ? 'medium-field' : ''),
            ));
        }

        //forbid to downgrade permissions
        // if user admin and only one
        $attr = '';
        //user cannot to change group for himself
        if ($user_id == $this->user->getId()) {
            $attr = ' disabled="disabled" ';
        }
        //non-admin cannot too
        if ($user_id && $this->user->getUserGroupId() != 1) {
            $attr = ' disabled="disabled" ';
        }

        $this->loadModel('user/user_group');
        $user_groups = array('' => $this->language->get('text_select_group'),);
        $results = $this->model_user_user_group->getUserGroups();

        foreach ($results as $r) {
            //do not show top-admin-group for non-admin for new user form
            if (!$user_id && $this->user->getUserGroupId() != 1 && $r['user_group_id'] == 1) {
                continue;
            }
            $user_groups[$r['user_group_id']] = $r['name'];
        }

        $this->data['form']['fields']['user_group'] = $form->getFieldHtml(array(
            'type'    => 'selectbox',
            'name'    => 'user_group_id',
            'value'   => $this->data['user_group_id'],
            'options' => $user_groups,
            'attr'    => $attr,
        ));

        $this->data['form']['fields']['email'] = $form->getFieldHtml(array(
            'type'     => 'input',
            'name'     => 'email',
            'value'    => $this->data['email'],
            'required' => true,
        ));

        $this->view->assign('help_url', $this->gen_help_url('user_edit'));
        $this->view->batchAssign($this->data);

        $this->processTemplate('/pages/user/user_form.tpl');
    }

    public function im()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        if (isset($this->request->get['user_id'])) {
            $user_id = $this->request->get['user_id'];
        } else {
            $user_id = 0;
        }
        if (!$user_id) {
            redirect($this->html->getSecureURL('user/user'));
        }

        $this->loadLanguage('common/im');
        $this->loadLanguage('user/user');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->loadModel('user/user');
        $user_info = $this->model_user_user->getUser($user_id);

        $this->view->assign('form_store_switch', $this->html->getStoreSwitcher());
        $this->data['edit_profile_url'] = $this->html->getSecureURL('user/user/update', '&user_id='.$user_id);

        $this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $this->document->initBreadcrumb(array(
            'href'      => $this->html->getSecureURL('index/home'),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
        ));
        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL('user/user'),
            'text'      => $this->language->get('heading_title'),
            'separator' => ' :: ',
        ));

        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL('user/user/update', '&user='.$user_id),
            'text'      => sprintf($this->language->get('text_notification_for', 'common/im'), $user_info['username']),
            'separator' => ' :: ',
            'current'   => true,
        ));

        $this->data['cancel'] = $this->html->getSecureURL('user/user');

        $this->loadLanguage('common/im');
        $protocols = $this->im->getProtocols();

        $sendpoints = array_merge(array_keys($this->im->sendpoints), array_keys($this->im->admin_sendpoints));

        foreach ($sendpoints as $sendpoint) {
            $ims = $this->im->getUserIMs($user_id, $this->session->data['current_store_id']);
            $imsettings = array_merge((array)$ims['storefront'][$sendpoint], (array)$ims['admin'][$sendpoint]);
            $values = array();

            foreach ($imsettings as $row) {
                if ($row['uri'] && in_array($row['protocol'], $protocols)) {
                    $values[$row['protocol']] = $row['protocol'];
                }
            }
            //send notification id present for admin => 1
            if (!empty($this->im->sendpoints[$sendpoint][1]) || !empty($this->im->admin_sendpoints[$sendpoint][1])) {
                $this->data['sendpoints'][$sendpoint] = array(
                    'id'     => $sendpoint,
                    'text'   => $this->language->get('im_sendpoint_name_'.preformatTextID($sendpoint)),
                    'values' => $values,
                );
            }
        }

        $this->data['im_settings_url'] = $this->html->getSecureURL('user/user_ims/settings', '&user_id='.$user_id);
        $this->data['text_change_im_addresses'] = $this->language->get('text_change_im_addresses');

        $this->view->assign('help_url', $this->gen_help_url('user_edit'));
        $this->view->batchAssign($this->data);

        $this->processTemplate('/pages/user/user_im.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function validateForm()
    {
        if (!$this->user->canModify('user/user')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (mb_strlen($this->request->post['username']) < 2 || mb_strlen($this->request->post['username']) > 20) {
            $this->error['username'] = $this->language->get('error_username');
        }else{
            $inc = $this->request->get['user_id'] ? ' AND user_id <> '.(int)$this->request->get['user_id'] : '';
            $exists = $this->model_user_user->getUsers(
                array(
                    'subsql_filter' => " `username` = '".$this->db->escape($this->request->post['username'])."'".$inc
                ),
                'total_only'
            );
            if($exists ){
                $this->error['username'] = $this->language->get('error_username');
            }
        }

        if (mb_strlen($this->request->post['firstname']) < 2 || mb_strlen($this->request->post['firstname']) > 32) {
            $this->error['firstname'] = $this->language->get('error_firstname');
        }

        if (mb_strlen($this->request->post['lastname']) < 2 || mb_strlen($this->request->post['lastname']) > 32) {
            $this->error['lastname'] = $this->language->get('error_lastname');
        }

        if (mb_strlen($this->request->post['email']) > 96
            || !preg_match(EMAIL_REGEX_PATTERN, $this->request->post['email'])
        ) {
            $this->error['email'] = $this->language->get('error_email');
        }

        if (($this->request->post['password']) || (!isset($this->request->get['user_id']))) {
            if (mb_strlen($this->request->post['password']) < 4) {
                $this->error['password'] = $this->language->get('error_password');
            }

            if ($this->request->post['password'] != $this->request->post['password_confirm']) {
                $this->error['password'] = $this->language->get('error_confirm');
            }
        }

        if ($this->request->post['user_group_id']) {
            $user_info = $this->model_user_user->getUser($this->request->get['user_id']);
            if ($user_info['user_group_id'] != $this->request->post['user_group_id']) {
                if ( //cannot to change group for yourself
                    $this->request->get['id'] == $this->user->getId()
                    //or current user is not admin
                    || $this->user->getUserGroupId() != 1
                ) {
                    $this->error['user_group'] = $this->language->get('error_user_group');
                }
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
