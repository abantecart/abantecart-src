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

class ControllerResponsesListingGridSeoUrl extends AController
{
    public function main()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('tool/seo_url');

        if (!$this->user->canAccess('tool/seo_url')) {
            $response = new stdClass();
            $response->userdata = new stdClass();
            $response->userdata->error = $this->language->getAndReplace(
                'error_permission_access',
                replaces: 'tool/seo_url'
            );
            $this->load->library('json');
            $this->response->setOutput(AJson::encode($response));
            return;
        }

        $gridFilterParams = array_merge(
            ['seo_keyword', 'route', 'query'],
            (array)$this->data['grid_filter_params']
        );
        $filter = new AFilter([
            'method'             => 'post',
            'grid_filter_params' => $gridFilterParams,
        ]);
        $filterData = $filter->getFilterData();
        /** @var ModelToolSeoUrl $model */
        $model = $this->loadModel('tool/seo_url');
        $total = $model->getTotalSeoUrls($filterData);
        $results = $model->getSeoUrls($filterData);

        $response = new stdClass();
        $response->page = $filter->getParam('page');
        $response->total = $filter->calcTotalPages($total);
        $response->records = $total;
        $response->userdata = new stdClass();

        foreach ($results as $i => $result) {
            $response->rows[$i]['id'] = $result['url_alias_id'];
            $response->rows[$i]['cell'] = [
                $result['seo_keyword'],
                $result['route'],
                $result['query'],
            ];
        }

        $this->data['response'] = $response;
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->setOutput(AJson::encode($this->data['response']));
    }

    public function update_field()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('tool/seo_url');
        if (!$this->user->canModify('listing_grid/seo_url')) {
            $this->jsonError('NO_PERMISSIONS_402', sprintf(
                $this->language->get('error_permission_modify'),
                'listing_grid/seo_url'
            ));
            return;
        }

        /** @var ModelToolSeoUrl $model */
        $model = $this->loadModel('tool/seo_url');
        $urlAliasId = (int)$this->request->get['id'];
        $current = $model->getSeoUrl($urlAliasId);
        if (!$current) {
            $this->jsonError('VALIDATION_ERROR_406', $this->language->get('error_not_found'));
            return;
        }

        $allowedFields = ['seo_keyword', 'route', 'query', 'language_id'];
        $changes = array_intersect_key($this->request->post, array_flip($allowedFields));
        $data = array_merge($current, $changes);
        $errors = $model->validateSeoUrl($data, $urlAliasId);
        if ($errors) {
            $this->jsonError('VALIDATION_ERROR_406', implode('<br>', $errors));
            return;
        }
        $model->updateSeoUrl($urlAliasId, $changes);
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function delete()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('tool/seo_url');
        if (!$this->user->canModify('listing_grid/seo_url')) {
            $this->jsonError('NO_PERMISSIONS_402', sprintf(
                $this->language->get('error_permission_modify'),
                'listing_grid/seo_url'
            ));
            return;
        }
        /** @var ModelToolSeoUrl $model */
        $model = $this->loadModel('tool/seo_url');
        $ids = array_unique(array_filter(array_map(
            'intval',
            explode(',', (string)$this->request->post['id'])
        )));
        foreach ($ids as $urlAliasId) {
            $model->deleteSeoUrl($urlAliasId);
        }
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode(['status' => 'ok']));
    }

    public function validate()
    {
        $this->loadLanguage('tool/seo_url');
        /** @var ModelToolSeoUrl $model */
        $model = $this->loadModel('tool/seo_url');
        $urlAliasId = (int)$this->request->get['id'];
        $data = array_merge($model->getSeoUrl($urlAliasId), $this->request->post);
        $errors = $model->validateSeoUrl($data, $urlAliasId);
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode([
            'status' => $errors ? 'error' : 'ok',
            'errors' => $errors,
        ]));
    }

    private function jsonError(string $code, string $message): void
    {
        $error = new AError('');
        $error->toJSONResponse($code, ['error_text' => $message, 'reset_value' => true]);
    }
}
