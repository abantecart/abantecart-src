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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ControllerResponsesListingGridUser extends AController
{
    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('user/user');
        $this->loadModel('user/user');

        $this->loadModel('user/user_group');
        $user_groups = ['' => $this->language->get('text_select_group'),];
        $results = $this->model_user_user_group->getUserGroups();
        foreach ($results as $r) {
            $user_groups[$r['user_group_id']] = $r['name'];
        }

        //Prepare filter config
        $filter_params = array_merge(['status', 'user_group_id'], (array)$this->data['filter_params']);
        $grid_filter_params = array_merge(['username'], (array)$this->data['grid_filter_params']);

        //Build query string based on GET params first 
        $filter_form = new AFilter(['method' => 'get', 'filter_params' => $filter_params]);
        //Build final filter
        $filter_grid = new AFilter([
            'method'                   => 'post',
            'grid_filter_params'       => $grid_filter_params,
            'additional_filter_string' => $filter_form->getFilterString(),
        ]);
        $total = $this->model_user_user->getTotalUsers($filter_grid->getFilterData());
        $response = new stdClass();
        $response->page = $filter_grid->getParam('page');
        $response->total = $filter_grid->calcTotalPages($total);
        $response->records = $total;
        $results = $this->model_user_user->getUsers($filter_grid->getFilterData());

        $i = 0;
        foreach ($results as $result) {

            $response->rows[$i]['id'] = $result['user_id'];
            $response->rows[$i]['cell'] = [
                $result['username'],
                $user_groups[$result['user_group_id']],
                $this->html->buildCheckbox([
                    'name'  => 'status[' . $result['user_id'] . ']',
                    'value' => $result['status'],
                    'style' => 'btn_switch',
                ]),
                dateISO2Display($result['date_added'], $this->language->get('date_format_short')),
            ];
            $i++;
        }
        $this->data['response'] = $response;
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        $this->load->library('json');
        $this->response->setOutput(AJson::encode($this->data['response']));
    }

    public function update()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        if (!$this->user->canModify('listing_grid/user')) {
            $error = new AError('');
            $error->toJSONResponse('NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf($this->language->get('error_permission_modify'), 'listing_grid/user'),
                    'reset_value' => true,
                ]
            );
            return;
        }

        $this->loadModel('user/user');
        $this->loadLanguage('user/user');

        $ids = array_unique(
            array_map(
                'intval',
                explode(',', $this->request->post['id'])
            )
        );
        if ($ids) {
            switch ($this->request->post['oper']) {
                case 'del':
                    foreach ($ids as $id) {
                        $errorText = '';
                        if ($this->user->getId() == $id) {
                            $errorText = $this->language->get('error_account');
                        }

                        $this->extensions->hk_ProcessData(
                            $this,
                            __FUNCTION__,
                            ['user_id' => $id, 'error_text' => $errorText]
                        );

                        if ($errorText) {
                            $error = new AError($errorText);
                            $error->toJSONResponse(
                                'VALIDATION_ERROR_406',
                                [
                                    'error_text' => $errorText,
                                ]
                            );
                            return;
                        }
                        $this->model_user_user->deleteUser($id);
                    }
                    break;
                case 'save':
                    foreach ($ids as $id) {
                        $this->model_user_user->editUser(
                            $id,
                            [
                                'status' => (int)$this->request->post['status'][$id]
                            ]
                        );
                    }
                    break;
                default:
            }
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    /**
     * update only one field
     *
     * @return void
     * @throws AException
     */
    public function update_field()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        if (!$this->user->canModify('listing_grid/user')) {
            $error = new AError('');
            $error->toJSONResponse('NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf($this->language->get('error_permission_modify'), 'listing_grid/user'),
                    'reset_value' => true,
                ]
            );
            return;
        }

        $this->loadLanguage('user/user');
        $this->loadModel('user/user');
        $user_id = (int)$this->request->get['id'];
        if ($user_id) {
            $user_info = $this->model_user_user->getUser($user_id);
            //request sent from edit form. ID in url
            foreach ($this->request->post as $key => $value) {
                if ($key == 'password_confirm') {
                    continue;
                } elseif ($key == 'username') {
                    $exists = $this->model_user_user->getUsers(
                        [
                            'subsql_filter' => " `username` = '" . $this->db->escape($value) . "' AND user_id <> " . $user_id
                        ],
                        'total_only'
                    );
                    if ($exists) {
                        $error = new AError('');
                        $error->toJSONResponse(
                            'VALIDATION_ERROR_406',
                            [
                                'error_text'  => $this->language->get('error_username'),
                                'reset_value' => true,
                            ]
                        );
                        return;
                    }
                } elseif ($key == 'user_group_id') {
                    if ($user_info['user_group_id'] != $value) {
                        if ( //cannot to change group for yourself
                            $user_id == $this->user->getId()
                            //or current user is not admin
                            || $this->user->getUserGroupId() != 1
                        ) {
                            $error = new AError('');
                            $error->toJSONResponse(
                                'VALIDATION_ERROR_406',
                                [
                                    'error_text'  => $this->language->get('error_user_group'),
                                    'reset_value' => true,
                                ]
                            );
                            return;
                        }
                    }
                }

                $data = [$key => $value];
                $this->model_user_user->editUser($this->request->get['id'], $data);
            }

            if ($this->request->post['password'] && $this->request->post['password_confirm']) {
                //logout when password was changed
                $salt_key = $user_info['salt'];
                if ($this->user->getId() == $user_id
                    && $user_info['password']
                    && $user_info['password'] != sha1($salt_key . sha1($salt_key . sha1($this->request->post['password'])))
                ) {
                    $this->user->logout();
                }
            }
            return;
        }

        //request sent from jGrid. ID is key of array
        foreach ($this->request->post as $field => $value) {
            foreach ($value as $k => $v) {
                $this->model_user_user->editUser($k, [$field => $v]);
            }
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}