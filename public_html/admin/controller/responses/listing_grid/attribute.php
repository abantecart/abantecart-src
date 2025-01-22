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

class ControllerResponsesListingGridAttribute extends AController
{

    private $attribute_manager;

    public function __construct($registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        $this->attribute_manager = new AAttribute_Manager();
        $this->loadLanguage('catalog/attribute');
    }

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $limit = $this->request->post['rows'];

        //get all leave attributes 
        $new_level = 0;
        $attr_parent_id = null;
        $leafNodes = $this->attribute_manager->getLeafAttributes();
        $to_show_tree = $this->config->get('config_show_tree_data');
        if ($to_show_tree) {
            if ($this->request->post['nodeid']) {
                $attr_parent_id = (int) $this->request->post['nodeid'];
                $new_level = (int) $this->request->post["n_level"] + 1;
            } else {
                //if filter presents - seek by all list. Otherwise, show only parent
                $attr_parent_id = $this->request->post['_search'] == 'true' ? null : 0;
            }
        }


        $total = $this->attribute_manager->getTotalAttributes([], '', $attr_parent_id);
        if ($total > 0) {
            $total_pages = ceil($total / $limit);
        } else {
            $total_pages = 0;
        }

        $response = new stdClass();
        $response->page = $this->request->post['page'];
        $response->total = $total_pages;
        $response->records = $total;

        $results = $this->attribute_manager->getAttributes([], '', $attr_parent_id);
        $i = 0;
        foreach ($results as $result) {
            //tree grid structure
            if ($to_show_tree) {
                $last_leaf = ($result['attribute_id'] == $leafNodes[$result['attribute_id']]);
            } else {
                $last_leaf = true;
            }

            $response->rows[$i]['id'] = $result['attribute_id'];
            $response->rows[$i]['cell'] = [
                $result['name'],
                $result['type_name'],
                $this->html->buildInput([
                                            'name'  => 'sort_order['.$result['attribute_id'].']',
                                            'value' => $result['sort_order'],
                                            'style' => 'small-field',
                                        ]),
                $this->html->buildCheckbox([
                                               'name'  => 'status['.$result['attribute_id'].']',
                                               'value' => $result['status'],
                                               'style' => 'btn_switch',
                                           ]),
                'action',
                $new_level,
                ($attr_parent_id ? : null),
                $last_leaf,
                false,
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

        if (!$this->user->canModify('listing_grid/attribute')) {
            $error = new AError('');
            $error->toJSONResponse(
                'NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf(
                        $this->language->get('error_permission_modify'),
                        'listing_grid/attribute'
                    ),
                    'reset_value' => true,
                ]
            );
            return;
        }

        $ids = array_unique(
            array_map(
                'intval',
                explode(',', $this->request->post['id'])
            )
        );
        if($ids) {
            switch ($this->request->post['oper']) {
                case 'del':
                    foreach ($ids as $id) {
                        $err = $this->validateDelete($id);
                        if ($err) {
                            $error = new AError('');
                            $error->toJSONResponse(
                                'VALIDATION_ERROR_406',
                                [
                                    'error_text' => $err,
                                ]
                            );
                            return;
                        }
                        $this->attribute_manager->deleteAttribute($id);
                    }
                    break;
                case 'save':
                    $allowedFields = array_merge(
                        ['name', 'attribute_type_id', 'sort_order', 'status'],
                        (array)$this->data['allowed_fields']
                    );

                    if ($this->request->post['resort'] == 'yes') {
                        //get only ids we need
                        $array = [];
                        foreach ($ids as $id) {
                            $array[$id] = $this->request->post['sort_order'][$id];
                        }
                        $new_sort = build_sort_order(
                            $ids,
                            min($array),
                            max($array),
                            $this->request->post['sort_direction']
                        );
                        $this->request->post['sort_order'] = $new_sort;
                    }

                    foreach ($ids as $id) {
                        foreach ($allowedFields as $f) {
                            if (isset($this->request->post[$f][$id])) {
                                $err = $this->_validateField($f, $this->request->post[$f][$id]);
                                if ($err) {
                                    $error = new AError('');
                                    $error->toJSONResponse(
                                        'VALIDATION_ERROR_406',
                                        [
                                            'error_text' => $err,
                                        ]
                                    );
                                    return;
                                }
                                $this->attribute_manager->updateAttribute($id, [$f => $this->request->post[$f][$id]]);
                            }
                        }
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

        if (!$this->user->canModify('listing_grid/attribute')) {
            $error = new AError('');
            $error->toJSONResponse(
                'NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf(
                        $this->language->get('error_permission_modify'),
                        'listing_grid/attribute'
                    ),
                    'reset_value' => true,
                ]
            );
            return;
        }

        if (isset($this->request->get['id'])) {
            //request sent from edit form. ID in url
            foreach ($this->request->post as $key => $value) {
                $err = $this->_validateField($key, $value);
                if (!empty($err)) {
                    $error = new AError('');
                    $error->toJSONResponse(
                        'VALIDATION_ERROR_406',
                        [
                            'error_text' => $err,
                        ]
                    );
                    return;
                }
                $data = [$key => $value];
                $this->attribute_manager->updateAttribute($this->request->get['id'], $data);
            }
            return null;
        }

        //request sent from jGrid. ID is key of array
        $fields = ['sort_order', 'status'];
        foreach ($fields as $f) {
            if (isset($this->request->post[$f])) {
                foreach ($this->request->post[$f] as $k => $v) {
                    $err = $this->_validateField($f, $v);
                    if (!empty($err)) {
                        $error = new AError('');
                        $error->toJSONResponse(
                            'VALIDATION_ERROR_406',
                            [
                                'error_text' => $err,
                            ]
                        );
                        return;
                    }
                    $this->attribute_manager->updateAttribute($k, [$f => $v]);
                }
            }
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function _validateField($field, $value)
    {
        $err = '';
        switch ($field) {
            case 'name' :
                if (((mb_strlen($value)) < 2) || ((mb_strlen($value)) > 32)) {
                    $err = $this->language->get('error_name');
                }
                break;
            case 'attribute_type_id' :
                if (empty($value)) {
                    $err = $this->language->get('error_required');
                }
                break;
        }
        $this->extensions->hk_ValidateData($this, [__FUNCTION__]);
        return $err;
    }

    public function validateDelete($id)
    {
        $this->data['error'] = '';
        $this->extensions->hk_ValidateData($this, [__FUNCTION__, $id]);
        return $this->data['error'];
    }

}