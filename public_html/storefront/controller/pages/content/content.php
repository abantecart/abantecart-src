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
    public function __construct($registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        //New for first 3 days. can be changed from hooks in InitData
        $this->data['date_threshold'] = 86400 * 3;
    }

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
        $httpQuery = $this->prepareSortingParameters();
        extract($httpQuery);
        $httpQuery['sort'] = $sort . '-' . $order;
        unset($httpQuery['order']);
        $httpQuery['content_id'] = $content_id;


        $selTag = (string)$request['tag'];

        $cntInfo = $this->model_catalog_content->getContent($content_id);
        if (!$cntInfo) {
            redirect($this->html->getSecureURL('error/not_found'));
        }

        $this->document->setTitle($cntInfo['title']);
        $this->document->setKeywords($cntInfo['meta_keywords']);
        $this->document->setDescription($cntInfo['meta_description']);

        //add parent to breadcrumbs and content URL  for better SEO-URL
        if ($cntInfo['parent_content_id']) {
            $httpQuery['parent_id'] = $cntInfo['parent_content_id'];
            $parent = $this->model_catalog_content->getContent(
                $cntInfo['parent_content_id'],
                $this->config->get('config_store_id'),
                $this->language->getLanguageID()
            );
            $httpParentQuery = ['content_id' => $parent['content_id']];
            if ($parent['parent_content_id']) {
                $httpParentQuery['parent_id'] = $parent['parent_content_id'];
            }
            $this->document->addBreadcrumb(
                [
                    'href'      => $this->html->getSEOURL(
                        'content/content',
                        '&' . http_build_query($httpParentQuery),
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
                    '&' . http_build_query($httpQuery),
                    true
                ),
                'text'      => $cntInfo['title'],
                'separator' => $this->language->get('text_separator'),
            ]
        );

        $this->data['content_info'] = $cntInfo;
        $this->data['publish_date'] = dateISO2Display(
            $cntInfo['publish_date'],
            $this->language->get('date_format_long')
        );
        $this->data['heading_title'] = $cntInfo['title'];
        $this->data['description'] = html_entity_decode($cntInfo['description']);
        $this->data['content'] = html_entity_decode($cntInfo['content']);
        if ($cntInfo['icon_rl_id']) {
            $rl = new AResource('image');
            $resource = $rl->getResource($cntInfo['icon_rl_id']);
            if ($resource['resource_code']) {
                $this->data['icon_code'] = $resource['resource_code'];
            } else {
                $this->data['icon'] = $rl->getResource($cntInfo['icon_rl_id']);
                $this->data['icon_url'] = $rl->getResizedImageURL($this->data['icon']);
            }
        }
        $tags = $this->model_catalog_content->getContentTags($content_id, $this->language->getLanguageID());
        $this->data['content_info']['tags'] = $this->prepTags($tags, 'content/content/list', null, $httpQuery);

        $request['start'] = abs((int)($page - 1) * $limit);
        $request['limit'] = $limit;
        $request['filter'] = [
            'parent_id' => $content_id,
            'tag'       => $selTag
        ];
        $request['sort'] = $request['sort'] ?: $httpQuery['sort'];

        $this->data['contents'] = $this->prepContentData(
            $this->model_catalog_content->filterContents($request),
            'content/content',
            $content_id,
            $httpQuery
        );

        if ($this->data['contents']) {
            $this->data['sorting'] = $this->getSortField($httpQuery['sort']);
            if ($selTag) {
                $this->data['selected_tag'] = $selTag;
                $this->data['remove_tag'] = $this->html->getSecureURL(
                    'content/content',
                    '&' . http_build_query(
                        [
                            'content_id' => $content_id,
                            'sort'       => $httpQuery['sort'],
                            'limit'      => $limit,
                            'page'       => 1
                        ]
                    )
                );
            }

            if ($selTag) {
                $httpQuery['tag'] = $selTag;
            }
            $sQuery = $httpQuery;
            unset($sQuery['limit'], $sQuery['sort']);
            $this->data['resort_url'] = $this->html->getSecureSEOURL(
                'content/content',
                '&' . http_build_query($sQuery)
            );

            $pagination_url = $this->html->getSecureSEOURL(
                'content/content',
                $this->getPaginationParams($httpQuery),
                true
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

        $httpQuery = $this->prepareSortingParameters();
        extract($httpQuery);
        $httpQuery['sort'] = $sort . '-' . $order;
        unset($httpQuery['order']);

        $selTag = (string)$request['tag'] ?? '';

        $this->data['sorting'] = $this->getSortField($httpQuery['sort']);

        $request['start'] = abs((int)($page - 1) * $limit);
        $request['limit'] = $limit;
        $request['filter'] = [
            'keyword' => $keyword,
            'tag'     => $selTag
        ];
        $this->data['contents'] = $this->prepContentData(
            $this->model_catalog_content->filterContents($request),
            'content/content/list'
        );

        $params = [];
        if ($keyword) {
            $params['keyword'] = $keyword;
        }
        if ($selTag) {
            $this->data['selected_tag'] = $selTag;
            $this->data['remove_tag'] = $this->html->getSecureURL(
                'content/content/list',
                '&' . http_build_query($params)
            );

            $httpQuery['tag'] = $selTag;
        }

        $sQuery = $httpQuery;
        unset($sQuery['limit'], $sQuery['sort']);
        $this->data['resort_url'] = $this->html->getSecureURL(
            'content/content/list',
            '&' . http_build_query($sQuery)
        );

        $pagination_url = $this->html->getSecureSEOURL(
            'content/content/list',
            $this->getPaginationParams($httpQuery),
            true
        );

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL(
                    'content/content/list',
                    '&' . http_build_query($httpQuery)
                ),
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

    /**
     * @param array $httpQuery
     * @return string
     */
    protected function getPaginationParams(array $httpQuery)
    {
        $pageQuery = $httpQuery;
        $pageQuery['page'] = '--page--';
        $pageQuery['limit'] = '--limit--';
        return '&' . http_build_query($pageQuery);
    }

    /**
     * @param array $contArr
     * @param string $rt
     * @param int|null $parent_id
     * @param array|null $httpQuery
     * @return array
     * @throws AException
     */
    protected function prepContentData(array $contArr, string $rt, ?int $parent_id = null, ?array $httpQuery = [])
    {
        foreach ($contArr as &$child) {
            $httpQuery['content_id'] = $child['content_id'];
            //add parent_id for better SEO-URL
            if ($child['parent_content_id']) {
                $httpQuery['parent_id'] = $child['parent_content_id'];
            }
            $child['url'] = $this->html->getSeoUrl('content/content', '&' . http_build_query($httpQuery));
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
            //Mark new content
            $pubDate = $child['publish_date'] ?: $child['date_added'];
            if (time() - dateISO2Int($pubDate) < $this->data['date_threshold']) {
                $child['new'] = true;
            }
            $tagsArr = explode(',', $child['tags']);
            $child['tags'] = $this->prepTags($tagsArr, $rt, $parent_id, $httpQuery);
        }
        return $contArr;
    }

    /**
     * @param array $tags
     * @param string $rt
     * @param int|null $parent_id
     * @param array|null $httpQuery
     * @return array
     * @throws AException
     */
    protected function prepTags(array $tags, string $rt, ?int $parent_id = null, ?array $httpQuery = [])
    {
        if ($parent_id) {
            $httpQuery['content_id'] = $parent_id;
        }
        //prepare tags
        $ret = [];
        foreach ($tags as $tag) {
            if ($tag) {
                $httpQuery['tag'] = $tag;
                $ret[$tag] = $this->html->getSecureURL($rt, '&' . http_build_query($httpQuery));
            }
        }
        return $ret;
    }

    /**
     * @param string $sorting
     * @return HtmlElement
     * @throws AException
     */
    protected function getSortField(string $sorting)
    {
        //handle children pages
        $sort_options = [
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
                'value'   => $sorting,
            ]
        );
    }

    /**
     * @return array
     * @throws AException
     */
    protected function prepareSortingParameters()
    {
        $request = $this->request->get;
        $keyword = $request['keyword'] ?: null;
        $page = $request['page'] ?? 1;
        $limit = (int)$request['limit'] ?: $this->config->get('config_catalog_limit');
        $sorting_href = $request['sort'] ?? 'date-DESC';
        list($sort, $order) = explode("-", $sorting_href);
        return [
            'keyword' => $keyword,
            'sort'    => $sort,
            'order'   => $order,
            'page'    => $page,
            'limit'   => $limit
        ];
    }
}
