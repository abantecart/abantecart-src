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

class ControllerBlocksNewContent extends AController
{
    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->data['heading_title'] = $this->language->get('heading_title_new', 'blocks/content');
        $this->loadLanguage('common/header');

        //build dynamic content (pages) links
        $this->loadModel('catalog/content');

        $this->data['contents'] = $this->model_catalog_content->getContents(
           [
               'new' => true,
               'limit' => 10
           ]
        );
        foreach ($this->data['contents'] as &$child) {
            $child['url']  = $this->html->getSEOURL('content/content', '&content_id='.$child['content_id'], true);
            if ($child['icon_rl_id']) {
                $rl = new AResource('image');
                $resource = $rl->getResource($child['icon_rl_id']);
                if ($resource['resource_code']) {
                    $child['icon_code'] = $resource['resource_code'];
                } else {
                    $child['icon_url'] = $rl->getResourceThumb(
                        $child['icon_rl_id'],
                        (int) $this->config->get('config_image_cart_width'),
                        (int) $this->config->get('config_image_cart_height')
                    );
                }
            }
        }

        $this->view->batchAssign($this->data);
        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

    }

    /**
     * Recursive function for building tree of content.
     * Note that same content can have two parents!
     *
     * @param     $all_contents array with all contents. Contains element with key
     *                          parent_content_id that is array  - all parent ids
     * @param int $parent_id
     * @param int $level
     *
     * @return array
     * @throws AException
     */
    protected function _buildTree($all_contents, $parent_id = 0, $level = 0)
    {
        $output = [];
        $k = 0;
        foreach ($all_contents as $content) {
            if ($content['parent_content_id'] == $parent_id) {
                $output[$k] = [
                    'id' => $content['parent_content_id'].'_'.$content['content_id'],
                    'title' => str_repeat('&nbsp;&nbsp;', $level).$content['title'],
                    'text'  => $content['title'],
                    'href' => $this->html->getSEOURL(
                        'content/content',
                        '&content_id='.$content['content_id'],
                        '&encode'
                    ),
                    'level' => $level,
                    'children' => $this->_buildTree(
                                    $all_contents,
                                    $content['content_id'],
                                    $level + 1
                                )
                ];
                $k++;
            }
        }
        return $output;
    }
}
