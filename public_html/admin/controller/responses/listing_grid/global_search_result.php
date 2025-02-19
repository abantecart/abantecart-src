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

class ControllerResponsesListingGridGlobalSearchResult extends AController
{
    public $error = [];

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        /** @var ModelToolGlobalSearch $mdl */
        $mdl = $this->loadModel('tool/global_search');
        $this->loadLanguage('tool/global_search');

        $page = (int)$this->request->post['page']; // get the requested page
        $limit = $this->request->post['rows']; // get how many rows we want to have into the grid

        $results = $mdl->getResult($this->request->get['search_category'], $this->request->get['keyword']);
        $total = (int)$results['result'][0]['total_num_rows'];
        $total_pages = $total ? (int)ceil($total / $limit) : 0;

        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $total;
        $response->userdata = new stdClass();
        $i = 0;
        foreach ($results['result'] as $result) {
            $response->rows[$i]['id'] = $i + 1;
            $response->userdata->type[$i + 1] = $result['type'];
            $response->rows[$i]['cell'] = [
                $i + 1,
                $result['text'],
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

    /**
     * function check access rights to search results
     *
     * @return boolean
     * @throws AException
     */
    protected function validate()
    {
        // check access to global search
        if (!$this->user->canAccess('tool/global_search')) {
            $this->error ['warning'] = $this->language->get('error_permission');
        }
        $this->extensions->hk_ValidateData($this);
        return !$this->error;
    }

    public function suggest()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        /** @var ModelToolGlobalSearch $mdl */
        $mdl = $this->loadModel('tool/global_search');
        $this->loadLanguage('tool/global_search');

        $search_categories = $mdl->getSearchSources('all');
        $result_controllers = $mdl->results_controllers;
        $results['response'] = [];

        foreach ($search_categories as $id => $name) {
            $r = $mdl->getResult($id, $this->request->get['term'], 'suggest');
            foreach ($r['result'] as $item) {
                if (!$item) {
                    continue;
                }
                $tmp = [];
                // exception for extension settings
                if ($id == 'settings' && !empty($item['extension'])) {
                    $tmp_id = 'extensions';
                    if ($item['type'] == 'total') {
                        $page_rt = sprintf($result_controllers[$tmp_id]['page2'], $item['extension']);
                    } else {
                        $page_rt = $result_controllers[$tmp_id]['page'];
                    }
                } else {
                    $tmp_id = $id;
                    $page_rt = $result_controllers[$tmp_id]['page'];
                }

                if (!is_array($result_controllers[$tmp_id]['id'])) {
                    $tmp[$result_controllers[$tmp_id]['id']] = $item[$result_controllers[$tmp_id]['id']];
                } else {
                    foreach ($result_controllers[$tmp_id]['id'] as $j) {
                        // if some id have alias - build link with it
                        $tmp[$j] = $item[$j];
                    }
                }

                if ($item['controller'] == 'setting/setting') {
                    $a = explode('-', $item['active']);
                    if ($a[0] == 'appearance' || $a[0] == 'im') {
                        unset($result_controllers[$tmp_id]['response']);
                    }
                }

                if ($id == 'commands') {
                    $item['page'] = $item['url'];
                    unset($item['url']);
                } else {
                    $item['controller'] = $result_controllers[$tmp_id]['response']
                        ? $this->html->getSecureURL(
                            $result_controllers[$tmp_id]['response'],
                            '&' . http_build_query($tmp)
                        )
                        : '';
                    $item['page'] = $this->html->getSecureURL($page_rt, '&' . http_build_query($tmp));
                }

                $item['category'] = $id;
                $item['category_name'] = $this->language->get('text_' . $id);
                $item['label'] = mb_strlen($item['title']) > 40
                    ? mb_substr($item['title'], 0, 40) . '...'
                    : $item['title'];
                $item['text'] = htmlentities($item['text'], ENT_QUOTES, 'utf-8', false);
                $item['text'] = $item['text'] ?: $item['title'];

                $results['response'][] = $item;
            }
        }

        $this->data['response'] = $results;
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode($this->data['response']));
    }
}
