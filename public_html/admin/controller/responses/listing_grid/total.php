<?php

/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2021 Belavier Commerce LLC

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

class ControllerResponsesListingGridTotal extends AController
{
    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('extension/total');

        $page = $this->request->post['page']; // get the requested page
        if ((int) $page < 0) {
            $page = 0;
        }
        $limit = $this->request->post['rows']; // get how many rows we want to have into the grid
        $sidx = $this->request->post['sidx']; // get index row - i.e. user click to sort
        $sord = $this->request->post['sord']; // get the direction

        $this->loadModel('setting/extension');
        $ext = $this->extensions->getExtensionsList(['filter' => 'total']);
        $extensions = [];
        if ($ext->rows) {
            foreach ($ext->rows as $row) {
                $language_rt = $config_controller = '';
                // for total-extensions inside engine
                if (is_file(DIR_APP_SECTION.'controller/pages/total/'.$row['key'].'.php')) {
                    $config_controller = $language_rt = 'total/'.$row['key'];
                } else {
                    // looking for config controller into parent extension.
                    //That Controller must to have filename equal child extension text id
                    $parents = $this->extension_manager->getParentsExtensionTextId($row['key']);
                    if ($parents) {
                        foreach ($parents as $parent) {
                            if (!$parent['status']) {
                                continue;
                            }
                            if (is_file(DIR_EXT.$parent['key'].'/admin/controller/pages/total/'.$row['key'].'.php')) {
                                $config_controller = 'total/'.$row['key'];
                                $language_rt = $parent['key'].'/'.$parent['key'];
                                break;
                            }
                        }
                    }
                }
                if ($config_controller) {
                    $extensions[$row['key']] = [
                        'extension_txt_id'  => $row['key'],
                        'config_controller' => $config_controller,
                        'language_rt'       => $language_rt,
                    ];
                }
            }
        }

        //looking for uninstalled engine's total-extensions
        $files = glob(DIR_APP_SECTION.'controller/pages/total/*.php');
        if ($files) {
            foreach ($files as $file) {
                $id = basename($file, '.php');
                if (!array_key_exists($id, $extensions)) {
                    $extensions[$id] = [
                        'extension_txt_id'  => $id,
                        'config_controller' => 'total/'.$id,
                        'language_rt'       => 'total/'.$id,
                    ];
                }
            }
        }

        $items = [];
        if ($extensions) {
            $readOnly = [
                'balance' => 999,
                'total' => 1000
            ];
            foreach ($extensions as $extension) {
                $this->loadLanguage($extension['language_rt']);
                if( in_array($extension['extension_txt_id'], array_keys($readOnly))){
                    $sort_order = $calc_order = $readOnly[$extension['extension_txt_id']];
                    $readonly = true;
                    if((int) $this->config->get($extension['extension_txt_id'].'_sort_order') != $sort_order){
                        $this->loadModel('setting/setting');
                        $this->model_setting_setting->editSetting(
                            $extension['extension_txt_id'],
                            [
                                $extension['extension_txt_id'].'_sort_order' => $sort_order,
                                $extension['extension_txt_id'].'_calculation_order' => $sort_order
                            ]
                        );
                    }
                } else {
                    $sort_order = (int) $this->config->get($extension['extension_txt_id'].'_sort_order');
                    $calc_order = (int) $this->config->get($extension['extension_txt_id'].'_calculation_order');
                    $readonly = false;
                }

                $items[] = [
                    'id'                => $extension['extension_txt_id'],
                    'name'              => $this->language->get('total_name'),
                    'status'            => $this->config->get($extension['extension_txt_id'].'_status'),
                    'sort_order'        => $sort_order,
                    'calculation_order' => $calc_order,
                    'action'            => $this->html->getSecureURL($extension['config_controller']),
                    'readonly'          => $readonly,
                ];
            }
        }

        //sort
        $allowedSort = [
            'name',
            'status',
            'sort_order',
            'calculation_order'
        ];

        $allowedDirection = [
            SORT_ASC => 'asc',
            SORT_DESC => 'desc'
        ];

        if (!in_array($sidx, $allowedSort)) {
            $sidx = $allowedSort[0];
        }
        if (!in_array($sord, $allowedDirection)) {
            $sord = SORT_ASC;
        } else {
            $sord = array_search($sord, $allowedDirection);
        }

        $sort = [];
        foreach ($items as $item) {
            $sort[] = $item[$sidx];
        }

        array_multisort($sort, $sord, $items);

        $total = count($items);
        if ($total > 0) {
            $total_pages = ceil($total / $limit);
        } else {
            $total_pages = 0;
        }

        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $total;

        $response->userdata = new stdClass();
        $response->userdata->rt = [];
        $response->userdata->classes = [];

        $results = array_slice($items, ($page - 1) * -$limit, $limit);

        $i = 0;
        foreach ($results as $result) {
            $id = $result['id'];
            $response->userdata->rt[$id] = $result['action'];
            if(in_array($id,['sub_total', 'total'])){
                $status = '';
            }else {
                $status = $this->html->buildCheckbox(
                    [
                        'name' => $id.'['.$id.'_status]',
                        'value' => $result['status'],
                        'style' => 'btn_switch',
                    ]
                );
            }
            $sort = $this->html->buildInput(
                [
                    'name'  => $id.'['.$id.'_sort_order]',
                    'value' => $result['sort_order'],
                    'attr'  => $result['readonly'] ? 'readonly' : '',
                ]
            );

            $calc = $this->html->buildInput(
                [
                    'name'  => $id.'['.$id.'_calculation_order]',
                    'value' => $result['calculation_order'],
                    'attr'  => $result['readonly'] ? 'readonly' : '',
                ]
            );

            $response->rows[$i]['id'] = $id;
            $response->rows[$i]['cell'] = [
                $result['name'],
                $status,
                ($result['status'] ? $sort : ''),
                ($result['status'] ? $calc : ''),
            ];
            $i++;
        }
        $this->data['response'] = $response;

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        $this->load->library('json');
        $this->response->setOutput(AJson::encode($this->data['response']));
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

        $this->loadLanguage('extension/total');
        $ids = [];
        if (isset($this->request->get['id'])) {
            $ids[] = $this->request->get['id'];
        } else {
            $ids = array_keys($this->request->post);
        }

        if (!$this->user->canModify('listing_grid/total')) {
            $error = new AError('');
            $error->toJSONResponse(
                'NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf($this->language->get('error_permission_modify'), 'listing_grid/total'),
                    'reset_value' => true,
                ]
            );
            return;
        }
        foreach ($ids as $id) {
            if (!$this->user->canModify('total/'.$id)) {
                $error = new AError('');
                $error->toJSONResponse(
                    'NO_PERMISSIONS_402',
                    [
                        'error_text'  => sprintf($this->language->get('error_permission_modify'), 'total/'.$id),
                        'reset_value' => true,
                    ]
                );
                return;
            }
        }

        $this->loadModel('setting/setting');

        if (isset($this->request->get['id'])) {
            //request sent from edit form. ID in url
            $this->model_setting_setting->editSetting($this->request->get['id'], $this->request->post);
        } else {
            //request sent from jGrid. ID is key of array
            foreach ($this->request->post as $group => $values) {
                $this->model_setting_setting->editSetting($group, $values);
            }
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

}