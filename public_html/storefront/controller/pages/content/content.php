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
        $page = isset($request['page']) ? $request['page'] : 0;
        $sort = isset($request['sort']) ? $request['sort'] : 0;
        $limit = isset($request['limit']) ? $request['limit'] : 10;
        $selTag = isset($request['tag']) ? $request['tag'] : '';

        $content_info = $this->model_catalog_content->getContent($content_id);
        if (!$content_info) {
            redirect($this->html->getURL('error/not_found'));
        }

        $this->document->setTitle($content_info['title']);
        $this->document->setKeywords($content_info['meta_keywords']);
        $this->document->setDescription($content_info['meta_description']);

        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSEOURL('content/content', '&content_id='.$content_id, true),
            'text'      => $content_info['title'],
            'separator' => $this->language->get('text_separator'),
        ));

        $this->data['content_info'] = $content_info;
        $this->data['heading_title'] = $content_info['title'];
        $this->data['button_continue'] = $this->language->get('button_continue');
        $this->data['description'] = html_entity_decode($content_info['description']);
        $this->data['content'] = html_entity_decode($content_info['content']);

        $this->data['button_continue'] = HtmlElementFactory::create(array(
            'type'  => 'button',
            'name'  => 'continue_button',
            'text'  => $this->language->get('button_continue'),
            'style' => 'button',
        ));
        $this->data['continue'] = $this->html->getHomeURL();

        //handle children pages
        $sort_options = [
            'defailt'       => $this->language->get('text_default'),
            'name-ASC'      => $this->language->get('text_sorting_name_asc'),
            'name-DESC'     => $this->language->get('text_sorting_name_desc'),
            'date-DESC'     => $this->language->get('text_sorting_date_desc'),
            'date-ASC'      => $this->language->get('text_sorting_date_asc'),
        ];

        $this->data['sorting'] = $this->html->buildElement(
            [
                'type'    => 'selectbox',
                'name'    => 'sort',
                'options' => $sort_options,
                'value'   => $sort,
            ]
        );

        $children = $this->model_catalog_content->getChildren($content_id, $request);
        foreach ($children as &$child) {
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
            $tags = $this->model_catalog_content->getContentTags($child['content_id'], $this->language->getLanguageID());
            foreach ($tags as $tag) {
                $child['tags'][$tag] = $this->html->getSEOURL(
                        'content/content',
                        '&content_id='.$content_id . '&tag='.urlencode($tag),
                        true);
            }
        }
        $this->data['children'] = $children;
        if ($selTag) {
            $this->data['selected_tag'] =  $selTag;
            $this->data['remove_tag'] = $this->html->getSEOURL(
                'content/content',
                '&content_id='.$content_id
            );
        }

        $this->data['resort_url'] = $this->html->getSEOURL(
            'content/content',
            '&content_id='.$content_id.'&tag='.$selTag
        );

        $pagination_url = $this->html->getSEOURL(
            'content/content',
            '&content_id='.$content_id.'&tag='.$selTag.'&page={page}'
        );
        $this->data['pagination_bootstrap'] = $this->html->buildElement(
            [
                'type'       => 'Pagination',
                'name'       => 'pagination',
                'text'       => $this->language->get('text_pagination'),
                'text_limit' => $this->language->get('text_per_page'),
                'total'      => $children[0]['total_num_rows'],
                'page'       => $page,
                'limit'      => $limit,
                'url'        => $pagination_url,
                'style'      => 'pagination',
            ]
        );

        $this->view->batchAssign($this->data);
        $this->view->setTemplate('pages/content/content.tpl');

        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function search()
    {


        $this->view->batchAssign($this->data);
        $this->view->setTemplate('pages/content/content.tpl');
    }
}
