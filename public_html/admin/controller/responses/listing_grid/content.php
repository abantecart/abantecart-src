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

class ControllerResponsesListingGridContent extends AController
{
    /**
     * @var AContentManager
     */
    protected $acm;

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('design/content');
        $this->acm = new AContentManager();

        //Prepare filter config
        $grid_filter_params = array_merge(
            ['sort_order', 'id.title', 'status', 'nodeid'],
            (array)$this->data['grid_filter_params']
        );
        //Build advanced filter
        $filter_data = [
            'method'             => 'post',
            'grid_filter_params' => $grid_filter_params,
        ];
        $filter_grid = new AFilter($filter_data);
        $filterData = $filter_grid->getFilterData();
        $filterData['parent_id'] = $this->request->get['parent_id'] ?? 0;
        if ($filterData['subsql_filter']) {
            $filterData['parent_id'] =
                ($filterData['parent_id'] == 'null' || $filterData['parent_id'] < 1)
                    ? null
                    : $filterData['parent_id'];
        }
        if ($filterData['parent_id'] === null || $filterData['parent_id'] === 'null') {
            unset($filterData['parent_id']);
        }

        $filterData['store_id'] = $this->config->get('current_store_id');

        if ($this->request->post['nodeid']) {
            $parent_id = $this->request->post['nodeid'];
            $filterData['parent_id'] = $parent_id;
            if ($filterData['subsql_filter']) {
                $filterData['subsql_filter'] .= " AND i.parent_content_id='" . (int)$filterData['parent_id'] . "' ";
            } else {
                $filterData['subsql_filter'] = " i.parent_content_id='" . (int)$filterData['parent_id'] . "' ";
            }
            $new_level = (int)$this->request->post["n_level"] + 1;
        } else {
            //Add custom params
            $filterData['parent_id'] = $new_level = 0;
            //sign to search by title in all levels of contents
            $need_filter = false;
            if (has_value($this->request->post['filters'])) {
                $this->load->library('json');
                $searchData = AJson::decode(htmlspecialchars_decode($this->request->post['filters']), true);
                if ($searchData['rules']) {
                    $need_filter = true;
                }
            }

            if ($this->config->get('config_show_tree_data') && !$need_filter) {
                if ($filterData['subsql_filter']) {
                    $filterData['subsql_filter'] .= " AND i.parent_content_id='0' ";
                } else {
                    $filterData['subsql_filter'] = " i.parent_content_id='0' ";
                }
            }
        }

        $leaf_nodes = $this->config->get('config_show_tree_data') ? $this->acm->getLeafContents() : [];

        $total = $this->acm->getTotalContents($filterData);
        $response = new stdClass();
        $response->page = $filter_grid->getParam('page');
        $response->total = $filter_grid->calcTotalPages($total);
        $response->records = $total;
        $response->userdata = new stdClass();
        $results = $this->acm->getContents($filterData);
        $results = !$results ? [] : $results;
        $i = 0;

        foreach ($results as $result) {
            if ($this->config->get('config_show_tree_data')) {
                $title_label = '<label style="white-space: nowrap;">' . $result['title'] . '</label>';
            } else {
                $title_label = $result['title'];
            }
            $response->rows[$i]['id'] = $result['content_id'];
            $response->rows[$i]['cell'] = [
                $title_label,
                $result['parent_name'],
                $this->html->buildCheckbox(
                    [
                        'name'  => 'status[' . $result['content_id'] . ']',
                        'value' => $result['status'],
                        'style' => 'btn_switch',
                    ]
                ),
                dateISO2Display($result['publish_date'], $this->language->get('date_format_short')),
                $this->html->buildInput(
                    [
                        'name'  => 'sort_order[' . $result['content_id'] . ']',
                        'value' => $result['sort_order'],
                    ]
                ),
                'action',
                $new_level,
                ($this->request->post['nodeid'] ?: null),
                $result['content_id'] == $leaf_nodes[$result['content_id']],
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
        $this->loadLanguage('design/content');
        $this->acm = new AContentManager();
        if (!$this->user->canModify('listing_grid/content')) {
            $error = new AError('');
            $error->toJSONResponse(
                'NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf(
                        $this->language->get('error_permission_modify'),
                        'listing_grid/content'
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
        if ($ids) {
            switch ($this->request->post['oper']) {
                case 'del':
                    foreach ($ids as $content_id) {
                        $errorText = '';
                        if ($this->config->get('config_account_id') == $content_id) {
                            $errorText = $this->language->get('error_account');
                        }
                        if ($this->config->get('config_checkout_id') == $content_id) {
                            $errorText = $this->language->get('error_checkout');
                        }
                        if ($this->acm->isParent($content_id)) {
                            $errorText = $this->language->get('error_delete_parent');
                        }
                        $this->extensions->hk_ProcessData(
                            $this,
                            __FUNCTION__,
                            ['content_id' => $content_id, 'error_text' => $errorText]
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

                        $this->acm->deleteContent($content_id);
                    }
                    break;
                case 'save':
                    $allowedFields = array_merge(['sort_order', 'status'], (array)$this->data['allowed_fields']);
                    if ($this->request->post['resort'] == 'yes') {
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
                        foreach ($allowedFields as $field) {
                            $this->acm->editContentField(
                                $id,
                                $field,
                                $this->request->post[$field][$id]
                            );
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
        $this->loadLanguage('design/content');
        $this->acm = new AContentManager();
        if (!$this->user->canModify('listing_grid/content')) {
            $error = new AError('');
            $error->toJSONResponse(
                'NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf(
                        $this->language->get('error_permission_modify'),
                        'listing_grid/content'
                    ),
                    'reset_value' => true,
                ]
            );
            return;
        }
        $allowedFields = array_merge(
            [
                'title',
                'description',
                'keyword',
                'meta_description',
                'meta_keywords',
                'store_id',
                'sort_order',
                'status',
                'parent_content_id',
                'tags',
                'author',
                'publish_date',
                'expire_date'
            ],
            (array)$this->data['allowed_fields']
        );
        $contentId = (int)$this->request->get['id'];
        if ($contentId) {
            //request sent from edit form. ID in url
            foreach ($this->request->post as $field => $value) {
                if (!in_array($field, $allowedFields)) {
                    continue;
                }
                $errorText = '';
                if ($field == 'keyword') {
                    $errorText = $this->html->isSEOkeywordExists('content_id=' . $contentId, $value);
                }
                if ($field == 'title') {
                    if (isHtml(html_entity_decode($value))) {
                        $errorText .= $this->language->get('error_title_html');
                    }
                }

                $this->extensions->hk_ProcessData($this, __FUNCTION__, ['content_id' => $contentId, 'error_text' => $errorText]);
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
                $this->acm->editContentField($contentId, $field, $value);
            }
            return;
        }

        //request sent from jGrid. ID is key of array
        foreach ($this->request->post as $field => $value) {
            if (!in_array($field, $allowedFields)) {
                continue;
            }
            // NOTE: grid quicksave ids are not the same as id from form quick save request!
            $content_id = key($value);
            $this->acm->editContentField($content_id, $field, current($value));
        }
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}