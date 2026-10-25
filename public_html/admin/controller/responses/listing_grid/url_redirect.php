<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2026 Belavier Commerce LLC
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

class ControllerResponsesListingGridURLRedirect extends AController
{
    public function main()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('tool/url_redirect');

        if (!$this->user->canAccess('tool/url_redirect')) {
            $response = new stdClass();
            $response->userdata = new stdClass();
            $response->userdata->error = $this->language->getAndReplace(
                'error_permission_access',
                replaces: 'tool/url_redirect'
            );
            $this->load->library('json');
            $this->response->setOutput(AJson::encode($response));
            return;
        }

        $gridFilterParams = array_merge(
            ['source', 'action'],
            (array)$this->data['grid_filter_params']
        );
        $filter = new AFilter([
            'method'             => 'post',
            'grid_filter_params' => $gridFilterParams,
        ]);
        $filterData = $filter->getFilterData();
        /** @var ModelToolURLRedirect $model */
        $model = $this->loadModel('tool/url_redirect');
        $total = $model->getTotalRedirects($filterData);
        $results = $model->getRedirects($filterData);

        $response = new stdClass();
        $response->page = $filter->getParam('page');
        $response->total = $filter->calcTotalPages($total);
        $response->records = $total;
        $response->userdata = new stdClass();

        $dateFormat = $this->language->get('date_format_short') . ' ' . $this->language->get('time_format');
        foreach ($results as $i => $result) {
            $source = ($result['source_host'] !== '' ? '[' . $result['source_host'] . '] ' : '') . $result['source'];
            $source = html2view($source);
            if ($result['comment'] !== null && trim($result['comment']) !== '') {
                $source = '<span class="tooltips" data-toggle="tooltip" data-placement="right"'
                    . ' data-original-title="' . html2view($result['comment']) . '">'
                    . '<i class="fa fa-comment-o"></i>&nbsp;' . $source . '</span>';
            }

            $response->rows[$i]['id'] = $result['id'];
            $response->rows[$i]['cell'] = [
                $result['id'],
                $source,
                $result['action'],
                $this->html->buildCheckbox([
                    'name'  => 'status[' . $result['id'] . ']',
                    'value' => $result['status'],
                    'style' => 'btn_switch',
                ]),
                $result['hits'],
                dateISO2Display((string)$result['last_hit'], $dateFormat),
            ];
        }

        $this->data['response'] = $response;
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->setOutput(AJson::encode($this->data['response']));
    }

    /**
     * Saves a single field from the edit form or the status switch from the grid.
     */
    public function update_field()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('tool/url_redirect');
        if (!$this->user->canModify('listing_grid/url_redirect')) {
            $this->jsonError('NO_PERMISSIONS_402', sprintf(
                $this->language->get('error_permission_modify'),
                'listing_grid/url_redirect'
            ));
            return;
        }

        /** @var ModelToolURLRedirect $model */
        $model = $this->loadModel('tool/url_redirect');
        $post = $this->request->post;
        // grid switch posts "status[id]=value"
        if (is_array($post['status'])) {
            foreach ($post['status'] as $id => $value) {
                $model->updateRedirect((int)$id, ['status' => (int)$value]);
            }
            $this->extensions->hk_UpdateData($this, __FUNCTION__);
            return;
        }

        $id = (int)$this->request->get['id'];
        $current = $model->getRedirect($id);
        if (!$current) {
            $this->jsonError('VALIDATION_ERROR_406', $this->language->get('error_not_found'));
            return;
        }
        $allowedFields = [
            'source', 'source_host', 'source_http_headers', 'match_type', 'target', 'action', 'keep_query', 'priority', 'status', 'comment',
        ];
        $data = array_merge($current, array_intersect_key($post, array_flip($allowedFields)));
        $errors = $model->validateRedirect($data, $id);
        if ($errors) {
            $this->jsonError('VALIDATION_ERROR_406', implode('<br>', $errors));
            return;
        }
        // whole record is saved because the source is normalized according to the match type
        $model->updateRedirect($id, $data);
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    /**
     * Grid "editurl": deletes rows or saves statuses of selected rows.
     */
    public function update()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('tool/url_redirect');
        if (!$this->user->canModify('listing_grid/url_redirect')) {
            $this->jsonError('NO_PERMISSIONS_402', sprintf(
                $this->language->get('error_permission_modify'),
                'listing_grid/url_redirect'
            ));
            return;
        }
        /** @var ModelToolURLRedirect $model */
        $model = $this->loadModel('tool/url_redirect');
        $post = $this->request->post;
        if ($post['oper'] === 'del') {
            $ids = filterIntegerIdList(explode(',', (string)$post['id']));
            foreach ($ids as $id) {
                $model->deleteRedirect((int)$id);
            }
        } elseif ($post['oper'] === 'save' && is_array($post['status'])) {
            foreach ($post['status'] as $id => $value) {
                $model->updateRedirect((int)$id, ['status' => (int)$value]);
            }
        }
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    private function jsonError(string $code, string $message): void
    {
        $error = new AError('');
        $error->toJSONResponse($code, ['error_text' => $message, 'reset_value' => true]);
    }
}
