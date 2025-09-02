<?php /** @noinspection PhpMultipleClassDeclarationsInspection */
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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ControllerResponsesListingGridLanguage extends AController
{
    public $errors = [];
    /** @var ModelLocalisationLanguage  */
    protected $mdl;

    public function __construct($registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        $this->loadLanguage('localisation/language');
        $this->mdl = $this->loadModel('localisation/language');
    }
    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        //Prepare filter config
        $filter_params = array_merge(
            ['name', 'status'],
            //take from hooks
            (array)$this->data['filter_params']
        );
        $grid_filter_params = array_merge(
            ['name', 'code', 'sort_order'],
            //take from hooks
            (array)$this->data['grid_filter_params']
        );

        $filter_form = new AFilter(['method' => 'get', 'filter_params' => $filter_params]);
        $filter_grid = new AFilter(['method' => 'post', 'grid_filter_params' => $grid_filter_params]);

        $results = $this->mdl->getLanguages(
            array_merge(
                $filter_form->getFilterData(),
                $filter_grid->getFilterData()
            )
        );
        $total = $results[0]['total_num_rows'];

        $response = new stdClass();
        $response->page = $filter_grid->getParam('page');
        $response->total = $filter_grid->calcTotalPages($total);
        $response->records = $total;
        $response->userdata = new stdClass();

        $i = 0;
        foreach ($results as $result) {
            if($result['code'] == 'en'){
               $response->userdata->classes[$result['language_id']] = 'disable-delete';
            }
            $response->rows[$i]['id'] = $result['language_id'];
            $response->rows[$i]['cell'] = [
                $this->html->buildInput(
                    [
                        'name'  => 'name[' . $result['language_id'] . ']',
                        'value' => $result['name'],
                    ]
                ),
                $this->html->buildInput(
                    [
                        'name'  => 'code[' . $result['language_id'] . ']',
                        'value' => $result['code'],
                    ]
                ),
                $this->html->buildInput(
                    [
                        'name'  => 'sort_order[' . $result['language_id'] . ']',
                        'value' => $result['sort_order'],
                    ]
                ),
                $this->html->buildCheckbox(
                    [
                        'name'  => 'status[' . $result['language_id'] . ']',
                        'value' => $result['status'],
                        'style' => 'btn_switch',
                    ]
                ),
            ];
            $i++;
        }
        $this->data['response'] = $response;
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode($this->data['response']));
    }

    public function update()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        if (!$this->user->canModify('listing_grid/language')) {
            $error = new AError('');
            $error->toJSONResponse(
                'NO_PERMISSIONS_402',
                [
                    'error_text'  => $this->language->getAndReplace('error_permission_modify', replaces: 'listing_grid/language'),
                    'reset_value' => true,
                ]
            );
            return;
        }

        $this->loadLanguage('localisation/language');
        $this->loadModel('localisation/language');
        $this->loadModel('setting/store');
        $this->loadModel('sale/order');
        $ids = filterIntegerIdList( explode(',', $this->request->post['id']) );
        if ($ids) {
            switch ($this->request->post['oper']) {
                case 'del':
                    foreach ($ids as $id) {
                        $errorText = '';
                        $language_info = $this->mdl->getLanguage($id);

                        if ($language_info) {
                            if ($this->config->get('config_storefront_language') == $language_info['code']) {
                                $errorText = $this->language->get('error_default');
                            }
                            if ($this->config->get('admin_language') == $language_info['code']) {
                                $errorText .= $this->language->get('error_admin');
                            }
                            $store_total = $this->model_setting_store->getTotalStoresByLanguage($language_info['code']);
                            if ($store_total) {
                                $errorText .= $this->language->getAndReplace('error_store', replaces: $store_total);
                            }
                        }

                        $order_total = $this->model_sale_order->getTotalOrdersByLanguageId($id);
                        if ($order_total) {
                            $errorText .= $this->language->getAndReplace('error_order', replaces: $order_total);
                        }
                        $this->extensions->hk_ProcessData(
                            $this,
                            __FUNCTION__,
                            ['language_id' => $id, 'error_text' => $errorText]
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
                        $this->mdl->deleteLanguage($id);
                    }
                    break;
                case 'save':
                    if ($this->request->post['resort'] == 'yes') {
                        $array = [];
                        //get only ids we need
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
                        $data = [
                            'name'       => $this->request->post['name'][$id],
                            'code'       => $this->request->post['code'][$id],
                            'sort_order' => $this->request->post['sort_order'][$id],
                            'status'     => $this->request->post['status'][$id],
                        ];
                        $this->mdl->editLanguage($id, $data);
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

        if (!$this->user->canModify('listing_grid/language')) {
            $error = new AError('');
            $error->toJSONResponse(
                'NO_PERMISSIONS_402',
                [
                    'error_text'  => $this->language->getAndReplace(
                        'error_permission_modify',
                        replaces: 'listing_grid/language'
                    ),
                    'reset_value' => true,
                ]
            );
            return;
        }

        $this->loadLanguage('localisation/language');
        $this->loadModel('localisation/language');
        $allowedFields = array_merge(
            [
                'name',
                'code',
                'sort_order',
                'status',
                'locale',
                'directory'
            ],
            (array)$this->data['allowed_fields']
        );
        $languageId = (int)$this->request->get['id'];
        if ($languageId) {
            $upd = [];
            foreach ($this->request->post as $key => $value) {
                if (!in_array($key, $allowedFields)
                    || !$this->validateField($languageId, $key, $value)
                ) {
                    $error = new AError('');
                    $error->toJSONResponse(
                        'VALIDATION_ERROR_406',
                        [
                            'error_text'  => implode(PHP_EOL, $this->errors),
                            'reset_value' => true,
                        ]
                    );
                    return;
                }
                $upd[$key] = $value;
            }
            if ($upd) {
                $this->mdl->editLanguage($languageId, $upd);
            }
            return;
        }

        //request sent from jGrid. ID is key of array
        foreach ($this->request->post as $key => $value) {
            if (!in_array($key, $allowedFields)) {
                continue;
            }
            foreach ($value as $k => $v) {
                $k = (int)$k;
                if ($this->validateField($k, $key, $v)) {
                    $data = [$key => $v];
                    $this->mdl->editLanguage($k, $data);
                } else {
                    $error = new AError('');
                    $error->toJSONResponse(
                        'VALIDATION_ERROR_406',
                        [
                            'error_text'  => implode(PHP_EOL, $this->errors),
                            'reset_value' => true,
                        ]
                    );
                    return;
                }
            }
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function validateField(int $id, string $name, $value)
    {
        if ($name == 'status' && !$value) {
            $sql = "SELECT *
                     FROM " . $this->db->table("languages") . "
                      WHERE language_id <> " . (int)$id . " AND status = 1";
            $query = $this->db->query($sql);
            if (!$query->num_rows) {
                $this->errors[] = $this->language->get('error_all_disabled');
            }
        }
        $this->extensions->hk_ValidateData(
            $this,
            [
                __FUNCTION__,
                'language_id' => $id,
                'column_name' => $name,
                'value'       => $value
            ]
        );
        return !($this->errors);
    }
}