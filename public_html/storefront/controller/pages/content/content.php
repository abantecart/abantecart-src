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
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerPagesContentContent extends AController
{
    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $request = $this->request->get;
        $this->loadModel('catalog/content');
        $this->document->resetBreadcrumbs();
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getHomeURL(),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );

        $content_id = (int)$request['content_id'];
        $page = (int)$request['page'] ?: 1;
        $sort = $request['sort'] ?? 'default';
        $limit = (int)$request['limit'] ?: 10;
        $selTag = (string)$request['tag'];

        $cntInfo = $this->model_catalog_content->getContent($content_id);
        if (!$cntInfo) {
            redirect($this->html->getSecureURL('error/not_found'));
        }

        $this->document->setTitle($cntInfo['title']);
        $this->document->setKeywords($cntInfo['meta_keywords']);
        $this->document->setDescription($cntInfo['meta_description']);

        $httpQuery = [ 'content_id' =>$content_id ];
        //add parent to breadcrumbs and content URL  for better SEO-URL
        if($cntInfo['parent_content_id']){
            $httpQuery['parent_id'] = $cntInfo['parent_content_id'];
            $parent = $this->model_catalog_content->getContent($cntInfo['parent_content_id']);
            $httpParentQuery = [ 'content_id' => $parent['content_id'] ];
            if($parent['parent_content_id']){
                $httpParentQuery['parent_id'] = $parent['parent_content_id'];
            }
            $this->document->addBreadcrumb(
                [
                    'href'      => $this->html->getSEOURL(
                        'content/content',
                        '&'.http_build_query($httpParentQuery),
                        true
                    ),
                    'text'      => $parent['title'],
                    'separator' => $this->language->get('text_separator'),
                ]
            );
        }
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSEOURL(
                    'content/content',
                    '&'.http_build_query($httpQuery),
                    true
                ),
                'text'      => $cntInfo['title'],
                'separator' => $this->language->get('text_separator'),
            ]
        );

        $this->data['content_info'] = $cntInfo;
        $this->data['publish_date'] = dateISO2Display($cntInfo['publish_date'], $this->language->get('date_format_long'));
        $this->data['heading_title'] = $cntInfo['title'];
        $this->data['description'] = html_entity_decode($cntInfo['description']);
        $this->data['content'] = html_entity_decode($cntInfo['content']);
        if ($cntInfo['icon_rl_id']) {
            $rl = new AResource('image');
            $resource = $rl->getResource($cntInfo['icon_rl_id']);
            if ($resource['resource_code']) {
                $this->data['icon_code'] = $resource['resource_code'];
            } else {
                $this->data['icon_url'] = $rl->getResourceThumb(
                    $cntInfo['icon_rl_id'],
                    (int)$this->config->get('config_image_cart_width'),
                    (int)$this->config->get('config_image_cart_height')
                );
            }
        }
        $tags = $this->model_catalog_content->getContentTags($content_id, $this->language->getLanguageID());
        $this->data['content_info']['tags'] = $this->prepTags($tags, 'content/content/list');

        $request['start'] = abs((int)($page - 1) * $limit);
        $request['filter'] = [
            'parent_id' => $content_id,
            'tag' => $selTag
        ];

        $this->data['contents'] = $this->prepContentData(
            $this->model_catalog_content->filterContents($request),
            'content/content',
            $content_id
        );

        if ($this->data['contents']) {
            $this->data['sorting'] = $this->getSortField((string)$sort);
            if ($selTag) {
                $this->data['selected_tag'] = $selTag;
                $this->data['remove_tag'] = $this->html->getSecureURL(
                    'content/content',
                    '&content_id=' . $content_id
                );
            }
            $params = [
                'content_id' => $content_id,
                'sort' => $sort
            ];
            if($selTag){
                $params['tag'] = $selTag;
            }
            $this->data['resort_url'] = $this->html->getSecureURL(
                'content/content',
                '&'.http_build_query($params)
            );
            $pagination_url = $this->html->getSecureURL(
                'content/content',
                '&'.http_build_query($params) . '&page={page}'
            );
            $this->data['pagination_bootstrap'] = $this->html->buildElement(
                [
                    'type'       => 'Pagination',
                    'name'       => 'pagination',
                    'text'       => $this->language->get('text_pagination'),
                    'text_limit' => $this->language->get('text_per_page'),
                    'total'      => $this->data['contents'][0]['total_num_rows'],
                    'page'       => $page,
                    'limit'      => $limit,
                    'url'        => $pagination_url,
                    'style'      => 'pagination',
                ]
            );
        }

        $this->view->batchAssign($this->data);
        $this->view->setTemplate('pages/content/content.tpl');
        $this->processTemplate();

        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function list()
    {
        $request = $this->request->get;
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadModel('catalog/content');
        $this->document->resetBreadcrumbs();
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getHomeURL(),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );
        $this->document->setTitle($this->language->get('heading_title'));

        $page = (int)$request['page'] ?: 1;
        $sort = $request['sort'] ?? 'default';
        $limit = (int)$request['limit'] ?: 10;
        $keyword = $request['keyword'] ?? '';
        $selTag = $request['tag'] ?? '';

        $this->data['sorting'] = $this->getSortField((string)$sort);

        $request['start'] = abs((int)($page - 1) * $limit);
        $request['filter'] = [
            'keyword' => $keyword,
            'tag' => $selTag
        ];
        $this->data['contents'] = $this->prepContentData(
            $this->model_catalog_content->filterContents($request),
            'content/content/list'
        );

        $params = [];
        if($keyword){
            $params['keyword'] = $keyword;
        }
        if ($selTag) {
            $this->data['selected_tag'] = $selTag;
            $this->data['remove_tag'] = $this->html->getSecureURL(
                'content/content/list',
                '&'.http_build_query($params)
            );

            $params['tag'] = $selTag;
        }

        $this->data['resort_url'] = $this->html->getSecureURL(
            'content/content/list',
            '&'.http_build_query($params)
        );

        $params['sort'] = $sort;
        $pagination_url = $this->html->getSecureURL(
            'content/content/list',
            '&'.http_build_query($params) . '&page={page}'
        );

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('content/content/list', '&'.http_build_query($params), true),
                'text'      => $this->language->get('heading_title'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        $this->data['pagination_bootstrap'] = $this->html->buildElement(
            [
                'type'       => 'Pagination',
                'name'       => 'pagination',
                'text'       => $this->language->get('text_pagination'),
                'text_limit' => $this->language->get('text_per_page'),
                'total'      => $this->data['contents'][0]['total_num_rows'],
                'page'       => $page,
                'limit'      => $limit,
                'url'        => $pagination_url,
                'style'      => 'pagination',
            ]
        );

        $this->data['mode'] = 'list';
        $this->view->batchAssign($this->data);
        $this->view->setTemplate('pages/content/content.tpl');
        $this->processTemplate();

        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function prepContentData($contArr, $rt, $parent_id = null)
    {
        foreach ($contArr as &$child) {
            $httpQuery = [ 'content_id' =>$child['content_id'] ];
            //add parent_id for better SEO-URL
            if($child['parent_content_id']){
                $httpQuery['parent_id'] = $child['parent_content_id'];
            }
            $child['url'] = $this->html->getSeoUrl('content/content','&'.http_build_query($httpQuery),true);
            if ($child['icon_rl_id']) {
                $rl = new AResource('image');
                $resource = $rl->getResource($child['icon_rl_id']);
                if ($resource['resource_code']) {
                    $child['icon_code'] = $resource['resource_code'];
                } else {
                    $child['icon_url'] = $rl->getResourceThumb(
                        $child['icon_rl_id'],
                        (int)$this->config->get('config_image_thumb_width'),
                        (int)$this->config->get('config_image_thumb_height')
                    );
                }
            }
            //Mark new for first 3 days
            if (time() - dateISO2Int($child['publish_date']) > 86400 * 3) {
                $child['new'] = true;
            }
            $tagsArr = explode(',', $child['tags']);
            $child['tags'] = $this->prepTags($tagsArr, $rt, $parent_id);
        }
        return $contArr;
    }

    protected function prepTags($tags, $rt, $parent_id = null)
    {
        //prepare tags
        $ret = [];
        foreach ($tags as $tag) {
            if ($tag) {
                $params = $parent_id ? '&content_id=' . $parent_id : '';
                $params .= '&tag=' . urlencode($tag);
                $ret[$tag] = $this->html->getSecureURL($rt, $params, true);
            }
        }
        return $ret;
    }

    /**
     * @param string $sort
     * @return HtmlElement
     * @throws AException
     */
    protected function getSortField(string $sort)
    {
        //handle children pages
        $sort_options = [
            'default'   => $this->language->get('text_default'),
            'name-ASC'  => $this->language->get('text_sorting_name_asc'),
            'name-DESC' => $this->language->get('text_sorting_name_desc'),
            'date-DESC' => $this->language->get('text_sorting_date_desc'),
            'date-ASC'  => $this->language->get('text_sorting_date_asc'),
        ];

        return $this->html->buildElement(
            [
                'type'    => 'selectbox',
                'name'    => 'sort',
                'options' => $sort_options,
                'value'   => $sort,
            ]
        );
    }
}
