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
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerPagesContentContent extends AController
{
    public function main()
    {
        $request = $this->request->get;
        $this->data = [];
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadModel('catalog/content');
        $this->document->resetBreadcrumbs();
        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getHomeURL(),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
        ));

        $content_id = isset($request['content_id']) ? $request['content_id'] : 0;
        $page = isset($request['page']) ? $request['page'] : 1;
        $sort = isset($request['sort']) ? $request['sort'] : 'default';
        $limit = isset($request['limit']) ? $request['limit'] : 10;
        $selTag = isset($request['tag']) ? $request['tag'] : '';

        $cntInfo = $this->model_catalog_content->getContent($content_id);
        if (!$cntInfo) {
            redirect($this->html->getURL('error/not_found'));
        }

        $this->document->setTitle($cntInfo['title']);
        $this->document->setKeywords($cntInfo['meta_keywords']);
        $this->document->setDescription($cntInfo['meta_description']);

        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSEOURL('content/content', '&content_id='.$content_id, true),
            'text'      => $cntInfo['title'],
            'separator' => $this->language->get('text_separator'),
        ));

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
                    (int) $this->config->get('config_image_cart_width'),
                    (int) $this->config->get('config_image_cart_height')
                );
            }
        }
        $tags = $this->model_catalog_content->getContentTags($content_id, $this->language->getLanguageID());
        $this->data['content_info']['tags'] = $this->prepTags($tags, 'content/content/list');

        $request['start'] = abs((int)($page - 1) * $limit);
        $request['filter'] = [];
        $request['filter']['parent_id'] = $content_id;
        $request['filter']['tag'] = $selTag;
        $this->data['contents'] = $this->prepContentData(
            $this->model_catalog_content->filterContents($request),
            'content/content',
            $content_id
        );

        if ($this->data['contents'] ) {
            $this->data['sorting'] = $this->getSortField($sort);
            if ($selTag) {
                $this->data['selected_tag'] =  $selTag;
                $this->data['remove_tag'] = $this->html->getSEOURL(
                    'content/content',
                    '&content_id='.$content_id
                );
            }
            $params = '&content_id='.$content_id.'&tag='.$selTag;
            $this->data['resort_url'] = $this->html->getSEOURL(
                'content/content',
                $params
            );
            $pagination_url = $this->html->getSEOURL(
                'content/content',
                $params.'&sort='.$sort.'&page={page}'
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
        $this->data = [];
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadModel('catalog/content');
        $this->document->resetBreadcrumbs();
        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getHomeURL(),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
        ));
        $this->document->setTitle($this->language->get('heading_title'));

        $page = isset($request['page']) ? $request['page'] : 1;
        $sort = isset($request['sort']) ? $request['sort'] : 'default';
        $limit = isset($request['limit']) ? $request['limit'] : 10;
        $keyword = isset($request['keyword']) ? $request['keyword'] : '';
        $selTag = isset($request['tag']) ? $request['tag'] : '';

        $this->data['sorting'] = $this->getSortField($sort);

        $request['start'] = abs((int)($page - 1) * $limit);
        $request['filter'] = [];
        $request['filter']['keyword'] = $keyword;
        $request['filter']['tag'] = $selTag;
        $this->data['contents'] = $this->prepContentData(
            $this->model_catalog_content->filterContents($request),
            'content/content/list'
        );

        if ($selTag) {
            $this->data['selected_tag'] =  $selTag;
            $this->data['remove_tag'] = $this->html->getSEOURL(
                'content/content/list',
                '&keyword='.$keyword
            );
        }

        $params = '&keyword='.$keyword.'&tag='.$selTag;
        $this->data['resort_url'] = $this->html->getSEOURL(
            'content/content/list',
            $params
        );

        $pagination_url = $this->html->getSEOURL(
            'content/content/list',
            $params.'&sort='.$sort.'&page={page}'
        );

        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSEOURL('content/content/list', $params, true),
            'text'      => $this->language->get('heading_title'),
            'separator' => $this->language->get('text_separator'),
        ));

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

    private function prepContentData($contArr, $rt, $parent_id = null) {
        foreach ($contArr as &$child) {
            $child['url']  = $this->html->getSEOURL('content/content', '&content_id='.$child['content_id'], true);
            if ($child['icon_rl_id']) {
                $rl = new AResource('image');
                $resource = $rl->getResource($child['icon_rl_id']);
                if ($resource['resource_code']) {
                    $child['icon_code'] = $resource['resource_code'];
                } else {
                    $child['icon_url'] = $rl->getResourceThumb(
                        $child['icon_rl_id'],
                        (int) $this->config->get('config_image_thumb_width'),
                        (int) $this->config->get('config_image_thumb_height')
                    );
                }
            }
            //Mark new for first 3 days
            if(time() - dateISO2Int($child['publish_date']) > 86400 * 3) {
                $child['new'] = true;
            }
            $tagsArr = explode(',', $child['tags']);
            $child['tags'] = $this->prepTags($tagsArr, $rt, $parent_id);
        }
        return $contArr;
    }

    private function prepTags($tags, $rt, $parent_id = null)
    {
        //prepare tags
        $ret = [];
        foreach ($tags as $tag) {
            if ($tag) {
                $params = $parent_id ? '&content_id='.$parent_id : '';
                $params .= '&tag='.urlencode($tag);
                $ret[$tag] = $this->html->getSEOURL($rt, $params, true);
            }
        }
        return $ret;
    }

    private function getSortField($sort) {
        //handle children pages
        $sort_options = [
            'default'       => $this->language->get('text_default'),
            'name-ASC'      => $this->language->get('text_sorting_name_asc'),
            'name-DESC'     => $this->language->get('text_sorting_name_desc'),
            'date-DESC'     => $this->language->get('text_sorting_date_desc'),
            'date-ASC'      => $this->language->get('text_sorting_date_asc'),
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
